/******************************************************************************
*       SOFA, Simulation Open-Framework Architecture, version 1.0 RC 1        *
*                (c) 2006-2011 INRIA, USTL, UJF, CNRS, MGH                    *
*                                                                             *
* This program is free software; you can redistribute it and/or modify it     *
* under the terms of the GNU General Public License as published by the Free  *
* Software Foundation; either version 2 of the License, or (at your option)   *
* any later version.                                                          *
*                                                                             *
* This program is distributed in the hope that it will be useful, but WITHOUT *
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or       *
* FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for    *
* more details.                                                               *
*                                                                             *
* You should have received a copy of the GNU General Public License along     *
* with this program; if not, write to the Free Software Foundation, Inc., 51  *
* Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.                   *
*******************************************************************************
*                            SOFA :: Applications                             *
*                                                                             *
* Authors: The SOFA Team and external contributors (see Authors.txt)          *
*                                                                             *
* Contact information: contact@sofa-framework.org                             *
******************************************************************************/
#include "viewer/qt/QtViewer.h"
#include <sofa/helper/system/FileRepository.h>
#include <sofa/helper/system/thread/CTime.h>
#include <sofa/simulation/common/Simulation.h>
#include <sofa/core/objectmodel/KeypressedEvent.h>
#include <sofa/core/objectmodel/KeyreleasedEvent.h>
#include <sofa/core/ObjectFactory.h>
//#include <sofa/helper/system/SetDirectory.h>
#include <math.h>
#include <iostream>
#include <fstream>
#include <string.h>
#include <math.h>

#include <qevent.h>

//#ifdef __APPLE__
//#include <OpenGL.h>
//#endif

#include "GenGraphForm.h"


#include <sofa/helper/system/glut.h>
#include <sofa/helper/gl/glfont.h>
#include <sofa/helper/gl/RAII.h>
#ifdef SOFA_HAVE_GLEW
#include <sofa/helper/gl/GLSLShader.h>
#endif
#include <sofa/helper/io/ImageBMP.h>

#include <sofa/defaulttype/RigidTypes.h>
#include <sofa/gui/ColourPickingVisitor.h>
#include <qextserialport.h>
//#include <sofa/component/collision/ExercisePipeline.h>
//#include <sofa/component/collision/GraspingPipeline.h>
#include <sofa/component/projectiveconstraintset/FixedConstraint.h>
#include <sofa/component/visualmodel/Light.h>
#include <QThread>


#include <sofa/component/misc/TopologicalChangeProcessor.h>
#include <sofa/component/engine/BoxROI.h>
// define this if you want video and OBJ capture to be only done once per N iteration
//#define CAPTURE_PERIOD 5

namespace sofa
{

namespace gui
{

namespace qt
{

namespace viewer
{

namespace qt
{



using std::cout;
using std::endl;
using namespace sofa::defaulttype;
using namespace sofa::helper::gl;

using sofa::simulation::getSimulation;

helper::SofaViewerCreator< QtViewer> QtViewer_class("qt",false);
SOFA_DECL_CLASS ( QTGUI )

//Q:Why would the QtViewer write its .view file with the qglviewer (a GPL library) extension?
//A:The new QtViewer has the same parameters as QGLViewer.
//  Consequently, the old .view file is now totally incorrect.

///TODO: standardize .view file parameters
//const std::string QtViewer::VIEW_FILE_EXTENSION = "qglviewer.view";
const std::string QtViewer::VIEW_FILE_EXTENSION = "view";
// Mouse Interactor
bool QtViewer::_mouseTrans = false;
bool QtViewer::_mouseRotate = false;
Quaternion QtViewer::_mouseInteractorNewQuat;


QGLFormat QtViewer::setupGLFormat()
{
  QGLFormat f = QGLFormat::defaultFormat();
#if defined(QT_VERSION) && QT_VERSION >= 0x040200
  std::cout << "QtViewer: disabling vertical refresh sync" << std::endl;
  f.setSwapInterval(0); // disable vertical refresh sync
#endif
  //f.setOption(QGL::SampleBuffers);
  return f;
}

// ---------------------------------------------------------
// --- Constructor
// ---------------------------------------------------------
QtViewer::QtViewer(QWidget* parent, const char* name)
: QGLWidget(setupGLFormat(), parent, name)
{

  startExercise = false;

  instrumentMenuFlag = false;
  instrumentMenuIndex = 0;
  firstCharReceivedFlag = false;
  firstX = false;
  previousX = false;

  pCount = 0;
  qCount = 0;

  graspCloseLeft = false;
  grasperActivatedLeft = false;

  graspLeft = NULL;
  graspRight = NULL;

  graspCloseRight = false;
  grasperActivatedRight = false;

  graspLeft2 = NULL;
  graspRight2 = NULL;

  rollPositiveAngle = 0.0;
  rollNegativeAngle = 0.0;

  rollPositiveAngle2 = 0.0;
  rollNegativeAngle2 = 0.0;

  transPaused = 0.0;
  transPaused2 = 0.0;

  localDetachedFlag = false;

  cameraLap = NULL;
  sceneCameraCheck = false;

  breakYaw = 0.0;
  breakPitch = 0.0;
  breakTrans = 0.0;

  breakYaw2 = 0.0;
  breakPitch2 = 0.0;
  breakTrans2 = 0.0;

  gripped = false;
  gripped2 = false;

  gripPos=0.5;
  gripPos1=0.5;
  gripPos2=0.5;
  totalPitch = 0.0;
  totalYaw = 0.0;

  scissorsPos = 0.5;

  scissorsActivated = false;
  scissorsClose = false;
  scissors = NULL;

  LHook = NULL;
  l_HookActivated = false;
  kPressed = false;

  LHookRight = NULL;
  l_HookActivated2 = false;

  registerInst = false;

  /*clipperTop = NULL;
  clipperBottom = NULL;
  clipperClose = false;
  clipperFullClose = false;
  clipperActivated = false;*/

 // clipperPos = -0.05;
 // clipperPosBottom = 0.3;

  groot = NULL;
  initTexturesDone = false;
  backgroundColour[0] = 1.0f;
  backgroundColour[1] = 1.0f;
  backgroundColour[2] = 1.0f;

 // instrument = NULL;
 // instrument1 = NULL;

  // setup OpenGL mode for the window
  //Fl_Gl_Window::mode(FL_RGB | FL_DOUBLE | FL_DEPTH | FL_ALPHA);
  timerAnimate = new QTimer(this);
  //connect( timerAnimate, SIGNAL(timeout()), this, SLOT(animate()) );

  _video = false;
  _axis = false;
  _background = 0;
  _numOBJmodels = 0;
  _materialMode = 0;
  _facetNormal = GL_FALSE;
  _renderingMode = GL_RENDER;
  _waitForRender = false;

  ////////////////
  // Interactor //
  ////////////////
  m_isControlPressed = false;
  _mouseInteractorMoving = false;
  _mouseInteractorTranslationMode = false;
  _mouseInteractorRotationMode = false;
  _mouseInteractorSavedPosX = 0;
  _mouseInteractorSavedPosY = 0;
#ifdef TRACKING
  savedX = 0;
  savedY = 0;
  firstTime = true;
  tracking = false;
#endif // TRACKING
  _mouseInteractorTrackball.ComputeQuaternion(0.0, 0.0, 0.0, 0.0);
  _mouseInteractorNewQuat = _mouseInteractorTrackball.GetQuaternion();

  thread1 = new QThread(this);
  thread2 = new QThread(this);

  connect( &captureTimer, SIGNAL(timeout()), this, SLOT(captureEvent()) );
  connect (port, SIGNAL(readyRead()), this, SLOT(onDataAvailable()));
  connect (port2, SIGNAL(readyRead()), this, SLOT(onDataAvailable2()));

 // connect(thread1, SIGNAL(started()), this, SLOT(onDataAvailable()));
//  connect(thread2, SIGNAL(started()), this, SLOT(onDataAvailable2()));
 	
}

void QtViewer::moveFirst(){

	 
  
	////port->moveToThread(mainThread);
	//if(thread1->isRunning())
	//	onDataAvailable();
	//else{
	//	thread1 = new QThread(this);
	//    connect(thread1, SIGNAL(started()), this, SLOT(onDataAvailable()));
 // 		thread1->start();
	//}
	//return;
 
}

void QtViewer::moveSecond(){
	/*
	if(thread2->isRunning())
		onDataAvailable2();
	else{
		thread2 = new QThread(this);
		connect(thread2, SIGNAL(started()), this, SLOT(onDataAvailable2()));
		thread2->start();
	}
	return;*/
 
}

void QtViewer::onDataAvailable2() {
        int avail = port2->bytesAvailable();
		const char data[]= "b";
		int i;

		double encoderStep = 0.001;
		double rollEncoderStep = 0.0025;
		double depthEncoderStep = 0.005;
		double graspEncoderStep=0.005;

		//sofa::component::collision::ExercisePipeline* exP = NULL;
		//exP = groot->getTreeObject<sofa::component::collision::ExercisePipeline>();

		std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > Vec1Objects1;
		std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > instruments;
		sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* instrument1 = NULL;
		getScene()->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > >(&instruments);
		if (!instruments.empty()){
			if(instruments.size() > 1)
				instrument1 = instruments[1];
			
			if(instrument1 != NULL){
				std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*> Vec3Objects1;
				
				sofa::simulation::Node* inst1 = NULL;

				inst1 = dynamic_cast<simulation::Node*>(instrument1->getContext());
				if(inst1 != NULL){
					inst1->getTreeObjects<sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>, std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > >(&Vec1Objects1);
					inst1->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > >(&Vec3Objects1);

					if(!Vec3Objects1.empty()){
						for(std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*>::const_iterator it = Vec3Objects1.begin(); it!=Vec3Objects1.end(); it++){
							sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* temp = *it;
							if(temp->getName() == "CollisionLR" || temp->getName() == "CollisionLL")
								graspLeft2 = temp;
							if(temp->getName() == "CollisionRR" || temp->getName() == "CollisionRL")
								graspRight2 = temp;
							if(temp->getName() == "Burn")
								LHookRight = temp;
						}				 
					}
				}
			}
		}
		

		sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>* fC = NULL;
		std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*> fixedConstObjs;
		groot->getTreeObjects<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>,std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*>>(&fixedConstObjs);

		if(!fixedConstObjs.empty()){
			for(std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*>::const_iterator it = fixedConstObjs.begin(); it!=fixedConstObjs.end(); it++){
				sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>* temp = *it;
			if(temp->getName() == "RemovableConstraint")
				fC=temp;
			}
		 }
		
		/*if(registerInst == false){
			registerInstrument();
			registerInst = true;
		}*/

		if(cameraLap != NULL){
			translationStart2 = true;
			pitchStart2 = true;
			yawStart2 = true;
		}

		if( avail > 0 ) {
            QByteArray usbdata;
            usbdata.resize(avail);
            int read = port2->read(usbdata.data(), usbdata.size());
			std::cerr<<"reading 2: "<<usbdata.data()<<std::endl;	
			if (instrument1 != NULL)
			{
							
                helper::WriteAccessor<Data<sofa::defaulttype::LaparoscopicRigidTypes::VecCoord> > instrumentX = *instrument1->write(core::VecCoordId::position());

			//if(*usbdata.data()=='a')
			//{
			//	int i = port->write(data, sizeof(data));
			//	std::cerr<<"Handshaking is done"<<std::endl;
				
				int bytelength = usbdata.size();
				for(int anot=0; anot<bytelength; anot++){
				switch(usbdata[anot])
				{
				case 'a':
					i = port2->write(data, sizeof(data));
					std::cerr<<"Handshaking is done"<<std::endl;
					break;

				case '1':
					{
						if(!translationStart2)
							translationStart2 = true;
						if(!getScene()->getContext()->getAnimate()){
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								getScene()->getContext()->setAnimate(true);
								timerAnimate->start(0);
								startExercise = true;
							}
						}

					}
					break;

					case '2':
					{
						if(!pitchStart2)
							pitchStart2 = true;
						if(!getScene()->getContext()->getAnimate()){
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								getScene()->getContext()->setAnimate(true);
								timerAnimate->start(0);
								startExercise = true;
							}
						}
					}
					break;

					case '3':
					{
						if(!yawStart2)
							yawStart2 = true;
						if(!getScene()->getContext()->getAnimate()){
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								getScene()->getContext()->setAnimate(true);
								timerAnimate->start(0);
								startExercise = true;
							}
						}
					}
					break;

				case 'p':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						std::cerr<<"P pressed"<<std::endl;
						for(int hm=0; hm<=20; hm++){
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle2);
									
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(1,0,0),encoderStep);
							//totalPitch += encoderStep;
						
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle2);
						
							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
							getQWidget()->update();

							if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();
						
							 if(gripped2 == true){
								 breakPitch2 += encoderStep;
								 gripped2 = false;
							 }
							 if(breakPitch2 >=0.09 && fC!=NULL){
								 fC->clearConstraints();
								 breakPitch2 = 0.0;
							 }

							 if(LHookRight != NULL && l_HookActivated2 == true && kPressed == true){
								 Vec3d position, direction;
									helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHookRight->read(core::VecCoordId::position());
									int itCount = 0;
									for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
										BurnFunction* temp = *it;
										position = burnTipX[burnTipIndexR[itCount]];
										if(itCount < 4)
											direction = Vec3d(0,1,0);
										else
											direction = Vec3d(0,0,1);
										temp->updateRay(position,direction);
										itCount++;
									}
							}
												
						}
					
						}
					}
						break;
					
					case 'q':
						{

							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								std::cerr<<"q pressed"<<std::endl;
						for(int hm=0; hm<=20; hm++){
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle2);
									
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(-1,0,0),encoderStep);
							//totalPitch += encoderStep;
						
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle2);
						
							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
							getQWidget()->update();

							if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();
							 
							if(gripped2 == true){
								breakPitch2 -= encoderStep;
								gripped2 = false;
							 }
							 if(breakPitch2 <= -0.09 && fC!=NULL){
								 fC->clearConstraints();
								 breakPitch2 = 0.0;
							 }

							 if(LHookRight != NULL && l_HookActivated2 == true && kPressed == true){
								 Vec3d position, direction;
									helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHookRight->read(core::VecCoordId::position());
									int itCount = 0;
									for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
										BurnFunction* temp = *it;
										position = burnTipX[burnTipIndexR[itCount]];
										if(itCount < 4)
											direction = Vec3d(0,1,0);
										else
											direction = Vec3d(0,0,1);
										temp->updateRay(position,direction);
										itCount++;
									}
							}
						}
						}
						}
								
						break;

					case 'y':
						{
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								std::cerr<<"y pressed"<<std::endl;
						for(int hm=0; hm<=20; hm++){
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle2);
									
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,1,0),encoderStep);
							//totalPitch += encoderStep;
						
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle2);
						
							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
							getQWidget()->update();

							if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();

							if(gripped2 == true){
								breakYaw2 += encoderStep;
								gripped2 = false;
							 }
							 if(breakYaw2 >=0.09 && fC!=NULL){
								 fC->clearConstraints();
								 breakYaw2 = 0.0;
							 }

							 if(LHookRight != NULL && l_HookActivated2 == true && kPressed == true){
									Vec3d position, direction;
									helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHookRight->read(core::VecCoordId::position());
									int itCount = 0;
									for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
										BurnFunction* temp = *it;
										position = burnTipX[burnTipIndexR[itCount]];
										if(itCount < 4)
											direction = Vec3d(0,1,0);
										else
											direction = Vec3d(0,0,1);
										temp->updateRay(position,direction);
										itCount++;
									}
							}
						}
						}
						}
						break;
					
					case 'z':
						{
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								std::cerr<<"z pressed"<<std::endl;
						for(int hm=0; hm<=20; hm++){
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle2);
									
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,-1,0),encoderStep);
							//totalPitch += encoderStep;
						
							if(rollPositiveAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle2);
							else if(rollNegativeAngle2 > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle2);
						
							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
							getQWidget()->update();

							if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();

							 if(gripped2 == true){
								breakYaw2 -= encoderStep;
								gripped2 = false;
							  }
							  if(breakYaw2 <= -0.09 && fC!=NULL){
								 fC->clearConstraints();
								 breakYaw2 = 0.0;
							  }

							   if(LHookRight != NULL && l_HookActivated2 == true && kPressed == true){
								 Vec3d position, direction;
									helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHookRight->read(core::VecCoordId::position());
									int itCount = 0;
									for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
										BurnFunction* temp = *it;
										position = burnTipX[burnTipIndexR[itCount]];
										if(itCount < 4)
											direction = Vec3d(0,1,0);
										else
											direction = Vec3d(0,0,1);
										temp->updateRay(position,direction);
										itCount++;
									}
							 }
						}
						}
						}
						break;

					case 'r':
						{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
							std::cerr<<"r pressed"<<std::endl;
						for(int hm=0; hm<=20;hm++){
						instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollEncoderStep);
					//	std::cerr<<"s pressed"<<std::endl;
						rollNegativeAngle2 += rollEncoderStep;
						rollPositiveAngle2 -= rollEncoderStep;
					
				
						   sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
						  sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
						  getQWidget()->update();

						  if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();

						  if(LHookRight != NULL && l_HookActivated2 == true && kPressed == true){
									Vec3d position, direction;
									helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHookRight->read(core::VecCoordId::position());
									int itCount = 0;
									for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
										BurnFunction* temp = *it;
										position = burnTipX[burnTipIndexR[itCount]];
										if(itCount < 4){
											direction = Vec3d(0,1,0);
											direction = Quat(Vector3(0,0,-1),rollEncoderStep).rotate(direction);
										}
										else{
											direction = Vec3d(0,0,1);
											direction = Quat(Vector3(0,0,-1),rollEncoderStep).rotate(direction);
										}
										temp->updateRay(position,direction);
										itCount++;
									}
							  }
						}
						}
						}
						break;
					
					case 's':
						{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
							std::cerr<<"s pressed"<<std::endl;
						for(int hm=0; hm<=20;hm++){
						
							instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollEncoderStep);
							rollPositiveAngle2 += rollEncoderStep;
							rollNegativeAngle2 -= rollEncoderStep;
						
							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
							getQWidget()->update();

							if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();

							if(LHookRight != NULL && l_HookActivated2 == true && kPressed == true){
								 Vec3d position, direction;
									helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHookRight->read(core::VecCoordId::position());
									int itCount = 0;
									for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
										BurnFunction* temp = *it;
										position = burnTipX[burnTipIndexR[itCount]];
										if(itCount < 4){
											direction = Vec3d(0,1,0);
											direction = Quat(Vector3(0,0,1),rollEncoderStep).rotate(direction);
										}
										else{
											direction = Vec3d(0,0,1);
											direction = Quat(Vector3(0,0,1),rollEncoderStep).rotate(direction);
										}
										temp->updateRay(position,direction);
										itCount++;
									}
							  }
						}
						}
						}
						break;

					case 'd':
						{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
							std::cerr<<"d pressed"<<std::endl;
						for(int hm=0; hm<=3; hm++){
							/*if(exP != NULL){
								if(exP->blockSecondInstrument == false){
									instrumentX[0].getTranslation() -= depthEncoderStep;
								}
								else
									transPaused2 += depthEncoderStep;
							}
							else*/
								instrumentX[0].getTranslation() -= depthEncoderStep;

						
						//	instrumentX[0].getTranslation() -= depthEncoderStep;
				
					    sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
						getQWidget()->update();

						if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();

						if(gripped2 == true){
							breakTrans2 -= encoderStep;
							gripped2 = false;
						}
						if(breakTrans2 <= -0.09 && fC!=NULL){
							fC->clearConstraints();
							breakTrans2 = 0.0;
						}

					
						}
						}
						}
						break;
					
					case 'e':
						{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
							std::cerr<<"e pressed"<<std::endl;
						for(int hm=0; hm<=3; hm++){
							if(transPaused2 <= (0.0 * depthEncoderStep)) 
								instrumentX[0].getTranslation() += depthEncoderStep;
							else 
								transPaused2 -= depthEncoderStep;
						
						//   instrumentX[0].getTranslation() += depthEncoderStep;
						
						 
						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
						getQWidget()->update();	

						if(grasperActivatedRight == true && graspCloseRight == true)
								updateSecondGrasper();

						if(gripped2 == true){
							breakTrans2 += encoderStep;
							gripped2 = false;
						 }
						 if(breakTrans2 >=0.09 && fC!=NULL){
							 fC->clearConstraints();
							 breakTrans2 = 0.0;
						 }
						}
						 
						}
						}
						break;

					case 'h':
						{
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								std::cerr<<"h pressed"<<std::endl;
					for(int hm=0; hm<=3; hm++){
						if(graspRight2 != NULL && graspLeft2 != NULL){
						if (!Vec1Objects1.empty()){
							for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects1.begin(); it!=Vec1Objects1.end();it++){
								sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
								sofa::helper::vector<double> k;
								if(gripPos2>=0.0)
								{
									graspCloseRight = true;
									gripPos2-=graspEncoderStep;
									k.push_back(-gripPos2);
					
									k.push_back(0.0);
									k.push_back(0.0);
									temp->forcePointPosition(0,k);
									k[0] = gripPos2;
				
									temp->forcePointPosition(1,k);
								
									if(grasperActivatedRight == true /*&& graspCloseRight == false*/){
										//	gFunc->reactivateRay(getScene());
										for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecLeftJaw2.begin(); it != gLFuncVecLeftJaw2.end(); it++){
											GraspFunction* temp = *it;
											temp->reactivateRay(getScene());
										}
										for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecRightJaw2.begin(); it != gLFuncVecRightJaw2.end(); it++){
											GraspFunction* temp = *it;
											temp->reactivateRay(getScene());
										}
									}
								

								}
						
							}
						}
						int gInd = 0;
						int rayCount = 1;
						if(gripPos2<=0.25){
						if(graspLeft2 != NULL && graspRight2 != NULL && grasperActivatedRight == false){
							for(int iter=0;iter<16;iter=iter+2){
								GraspFunction* tempGFuncR = new GraspFunction();
								tempGFuncR->init(getScene(),rayCount);
								gLFuncVecRightJaw2.push_back(tempGFuncR);
								
								rayCount++;
								GraspFunction* tempGFuncL = new GraspFunction();
								tempGFuncL->init(getScene(),rayCount);
								gLFuncVecLeftJaw2.push_back(tempGFuncL);
								rayCount++;

								graspInsideIndexR[gInd] = (iter+32);
								gInd++;

						
							}	
							graspInsideIndexR[gInd] = 26;
							gInd++;
							graspInsideIndexR[gInd] = 29;
					
							grasperActivatedRight = true;
						}
						}
						}

						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
						getQWidget()->update();
							}
						}
						}
						break;
					
					case 'g':
						{
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								std::cerr<<"g pressed"<<std::endl;
							//	sofa::component::collision::GraspingPipeline* gPipe = NULL;
							//	gPipe = getScene()->get<component::collision::GraspingPipeline>();
							/*	if(gPipe != NULL){
									if(gPipe->waitForRelease)
										gPipe->released = true;
								}*/
						for(int hm=0; hm<=3; hm++){
								if(graspRight2 != NULL && graspLeft2 != NULL){
									if (!Vec1Objects1.empty()){
										for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects1.begin(); it!=Vec1Objects1.end();it++){
											sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
											sofa::helper::vector<double> k;
											if (gripPos2<=0.5)
											{
												gripPos2+=graspEncoderStep;
												graspCloseRight = false;
												k.push_back(-gripPos2);
					
												k.push_back(0.0);
												k.push_back(0.0);
												temp->forcePointPosition(0,k);
												k[0] = gripPos2;
				
												temp->forcePointPosition(1,k);

												if(grasperActivatedRight == true ){
													//gFunc->deactivateRay();
													for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecLeftJaw2.begin(); it != gLFuncVecLeftJaw2.end(); it++){
														GraspFunction* temp = *it;
														temp->deactivateRay();
													}
													for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecRightJaw2.begin(); it != gLFuncVecRightJaw2.end(); it++){
														GraspFunction* temp = *it;
														temp->deactivateRay();
													}
												}
											}
						
										}
									}
								}
						
				
							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument1->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument1->getContext());
							getQWidget()->update();
						}

						//std::cerr<<"g pressed"<<std::endl;
						}
						}
						break;
					default:
						break;
				}
			
			}
		}
		}
		else
			std::cerr<<"not reading"<<std::endl;
}

void QtViewer::onDataAvailable() {
        int avail = port->bytesAvailable();
		int i;
		const char data[]= "b";
		/*std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > instruments;
		groot->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > >(&instruments);*/
        
		double encoderStep = 0.001;
		double rollEncoderStep = 0.0025;
		double depthEncoderStep = 0.005;
		double graspEncoderStep=0.005;

	//	sofa::component::collision::ExercisePipeline* exP = NULL;
	//	exP = groot->getTreeObject<sofa::component::collision::ExercisePipeline>();

		/*if(registerInst == false){
			registerInstrument();
			registerInst = true;
		}*/

		std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > Vec1Objects;

		sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* instrument = NULL;
		std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > instruments;
		getScene()->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > >(&instruments);
		if (!instruments.empty()){
			instrument = instruments[0];

			std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*> Vec3Objects;
			sofa::simulation::Node* leftInstrumentNode = NULL;

			leftInstrumentNode = dynamic_cast<simulation::Node*>(instrument->getContext());
			if(leftInstrumentNode != NULL){
				leftInstrumentNode->getTreeObjects<sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>, std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > >(&Vec1Objects);
				leftInstrumentNode->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > >(&Vec3Objects);

				if(!Vec3Objects.empty()){
					for(std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*>::const_iterator it = Vec3Objects.begin(); it!=Vec3Objects.end(); it++){
						sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* temp = *it;
						if(temp->getName() == "Camera")
							cameraLap = temp;
						if(temp->getName() == "CollisionLL" || temp->getName() == "CollisionLR")
							graspLeft = temp;
						if(temp->getName() == "CollisionRL" || temp->getName() == "CollisionRR")
							graspRight = temp;
						if(temp->getName() == "Cut")
							scissors = temp;
						if(temp->getName() == "Burn")
							LHook = temp;
					//	if(temp->getName() == "ClipTop")
					//		clipperTop = temp;
					//	if(temp->getName() == "ClipBottom")
					//		clipperBottom = temp;
					}			 
				}
			}
		}

		
		sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>* fC = NULL;
		std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*> fixedConstObjs;
		groot->getTreeObjects<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>,std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*>>(&fixedConstObjs);

		if(!fixedConstObjs.empty()){
			for(std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*>::const_iterator it = fixedConstObjs.begin(); it!=fixedConstObjs.end(); it++){
				sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>* temp = *it;
			if(temp->getName() == "RemovableConstraint")
				fC=temp;
			}
		 }
			
		if( avail > 0 ){
            QByteArray usbdata;
            usbdata.resize(avail);
            int read = port->read(usbdata.data(), usbdata.size());
		//	std::cerr<<"reading: "<<usbdata.data()<<std::endl;
            //if( read > 0 ) {
              //  processNewData(usbdata);
            //}
			
			if (instrument != NULL)
			{
				helper::WriteAccessor<Data<sofa::defaulttype::LaparoscopicRigidTypes::VecCoord> > instrumentX = *instrument->write(core::VecCoordId::position());
				//std::cerr<<"check\n"<<std::endl;
				
				if(cameraLap != NULL && sceneCameraCheck == false){
					currentCamera -> p_position = Vector3(0,0,0);
					currentCamera -> p_orientation = instrumentX[0].getOrientation();
					sofa::component::visualmodel::SpotLight* spLight = NULL;
					spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
					if(spLight != NULL)
						spLight->direction.setValue(instrumentX[0].getOrientation().rotate(spLight->direction.getValue()));
							
					sceneCameraCheck = true;
				}

				if(scissors!=NULL && scissorsActivated == false){
					iFunc = new InciseFunction();
					iFunc->init(getScene());
					scissorsActivated = true;
				}

				/*if(LHook != NULL){
					if(l_HookActivated == false){
						for(int iter = 0; iter <=9; iter++){
							BurnFunction* tempbFunc = new BurnFunction();
							tempbFunc->init(getScene());
							bFuncVec.push_back(tempbFunc);
						}
						burnTipIndex[0] = 2;
						burnTipIndex[1] = 43;
						burnTipIndex[2] = 64;
						burnTipIndex[3] = 85;
						burnTipIndex[4] = 137;
						burnTipIndex[5] = 127;
						burnTipIndex[6] = 148;
						burnTipIndex[7] = 169;
						burnTipIndex[8] = 190;
						burnTipIndex[9] = 211;
						l_HookActivated = true;
					}
				}*/

			// Finding all the Vec1d Objects in the graph
		//	std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > Vec1Objects;
			//groot->getTreeObjects<sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>, std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > >(&Vec1Objects);

		/*	sofa::simulation::Node* inst1 = NULL;
			inst1 = groot->getChild("InstrumentGraspL");
			if(inst1 != NULL)*/
		//	if(leftInstrumentNode != NULL)
		//		leftInstrumentNode->getTreeObjects<sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>, std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > >(&Vec1Objects);

				int bytelength = usbdata.size();
				for(int anot=0; anot<bytelength; anot++){
				switch(usbdata[anot])
				{
					case 'a':
					i = port->write(data, sizeof(data));
					std::cerr<<"Handshaking is done"<<std::endl;
					break;

					case 'x':
					{
						if(firstX == false){
							if(!getScene()->getContext()->getAnimate()){
								getScene()->getContext()->setAnimate(true);
								timerAnimate->start(0);
							}
							else{
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							firstX = true;
							}
						}
						else
							previousX = true;
						
					}
					break;

					case 'i':
					{
						if(instrumentMenuFlag == true)
							firstCharReceivedFlag = true;
					}
					break;

					case 'j':
					{
						if(firstCharReceivedFlag == true){
							selectNextInstrument();
							firstCharReceivedFlag = false;
						}
					}
					break;

					case '1':
					{
						if(!translationStart)
							translationStart = true;
						if(!getScene()->getContext()->getAnimate()){
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								getScene()->getContext()->setAnimate(true);
								timerAnimate->start(0);
								startExercise = true;
							}
						}


					}
					break;

					case '2':
					{
						if(!pitchStart)
							pitchStart = true;
						if(!getScene()->getContext()->getAnimate()){
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								getScene()->getContext()->setAnimate(true);
								timerAnimate->start(0);
								startExercise = true;
							}
						}
					}
					break;

					case '3':
					{
						if(!yawStart)
							yawStart = true;
						if(!getScene()->getContext()->getAnimate()){
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
								getScene()->getContext()->setAnimate(true);
								timerAnimate->start(0);
								startExercise = true;
							}
						}
					}
					break;

					case 'p':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						pCount++;
						
					//	std::cout<<pCount<<std::endl;
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
					//	std::cerr<<"P pressed"<<std::endl;

						for(int hm=0; hm<=20; hm++){
							if(rollPositiveAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle);
							else if(rollNegativeAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle);
									
						//if(exP != NULL){
						//	if(1/*exP->blockInstrument == false*/){
						//		if(cameraLap != NULL){
						//				currentCamera -> rotateWorldAroundPoint(Quat(Vector3(1,0,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
						//				instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(1,0,0),encoderStep);
						//				sofa::component::visualmodel::SpotLight* spLight = NULL;
						//				spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
						//				if(spLight != NULL)
						//					spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
						//				/*if(spLight != NULL){
						//					Vector3 axRot = Vector3(1,0,0);
						//					if(rollPositiveAngle > 0)
						//						axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
						//					else if(rollNegativeAngle > 0)
						//						axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
						//					spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
						//				}*/
						//			}
						//			else
						//				instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(1,0,0),encoderStep);
						//		totalPitch += encoderStep;
						//			
						//		
						//	}
						//}
						//else{
							
								if(cameraLap != NULL){
									currentCamera -> rotateWorldAroundPoint(Quat(Vector3(1,0,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
									instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(1,0,0),encoderStep);
									sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL)
										spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
									/*sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL){
										Vector3 axRot = Vector3(1,0,0);
										if(rollPositiveAngle > 0)
											axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
										else if(rollNegativeAngle > 0)
											axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
										spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
									}*/
							
								}
								else
									instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(1,0,0),encoderStep);
								totalPitch += encoderStep;
								 
							
						//}

						 if(rollPositiveAngle > 0)
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle);
						else if(rollNegativeAngle > 0)
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle);
						
						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						getQWidget()->update();
											
						if(grasperActivatedLeft == true && graspCloseLeft == true)
							updateGrasper();

							
						 if(gripped == true){
							 breakPitch += encoderStep;
							 gripped = false;
						 }
						 if(breakPitch >=0.09 && fC!=NULL){
							 fC->clearConstraints();
							 breakPitch = 0.0;
						 }
						
						}
						if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4)
										direction = Vec3d(0,1,0);
									else
										direction = Vec3d(0,0,1);
									temp->updateRay(position,direction);
									itCount++;
								}
						}
					}
						//instrumentX[0].getTranslation() += 0.1;
					}
						break;
					
					case 'q':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						qCount++;
				//		std::cout<<qCount<<std::endl;
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
						for(int hm=0; hm<=20;hm++){
							if(rollPositiveAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle);
							else if(rollNegativeAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle);
									
						//if(exP != NULL){
						//	if(1/*exP->blockInstrument == false*/){
						//		if(cameraLap != NULL){
						//				currentCamera -> rotateWorldAroundPoint(Quat(Vector3(-1,0,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
						//				instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(-1,0,0),encoderStep) ;
						//				sofa::component::visualmodel::SpotLight* spLight = NULL;
						//				spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
						//				if(spLight != NULL)
						//					spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
						//				/*if(spLight != NULL){
						//					Vector3 axRot = Vector3(-1,0,0);
						//					if(rollPositiveAngle > 0)
						//						axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
						//					else if(rollNegativeAngle > 0)
						//						axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
						//					spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
						//				}*/
						//			}
						//			else
						//				instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(-1,0,0),encoderStep) ;
						//			totalPitch -= encoderStep;
						//			
						//		
						//	}
						//}
						//else
						//{
							
								if(cameraLap != NULL){
									currentCamera -> rotateWorldAroundPoint(Quat(Vector3(-1,0,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
									instrumentX[0].getOrientation() =  instrumentX[0].getOrientation()*Quat(Vector3(-1,0,0),encoderStep) ;
									sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL)
										spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
									/*sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL){
										Vector3 axRot = Vector3(-1,0,0);
										if(rollPositiveAngle > 0)
											axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
										else if(rollNegativeAngle > 0)
											axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
										spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
									}*/
								}
								else
									instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(-1,0,0),encoderStep) ;
									totalPitch -= encoderStep;
									
								
					//	}
						
					//	std::cerr<<"Q pressed"<<std::endl;

						 
						  if(rollPositiveAngle > 0)
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle);
						  else if(rollNegativeAngle > 0)
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle);
						  sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						getQWidget()->update();
						
						if(grasperActivatedLeft == true && graspCloseLeft == true )
							updateGrasper();

						 if(gripped == true){
							breakPitch -= encoderStep;
							gripped = false;
						}
						 if(breakPitch <= -0.09 && fC!=NULL){
							 fC->clearConstraints();
							 breakPitch = 0.0;
						 }
						
						}
						if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4)
										direction = Vec3d(0,1,0);
									else
										direction = Vec3d(0,0,1);
									temp->updateRay(position,direction);
									itCount++;
								}
						}
					}
					}
						break;

					case 'y':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
					//	std::cerr<<"y pressed"<<std::endl;
						for(int hm=0; hm<=20; hm++){
							if(rollPositiveAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle);
							else if(rollNegativeAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle);
									
						//if(exP != NULL){
						//	if(1/*exP->blockInstrument == false*/){
						//		if(cameraLap != NULL){
						//				currentCamera -> rotateWorldAroundPoint(Quat(Vector3(0,1,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
						//				instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,1,0),encoderStep)  ;
						//				sofa::component::visualmodel::SpotLight* spLight = NULL;
						//				spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
						//				if(spLight != NULL)
						//					spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
						//				/*if(spLight != NULL){
						//					Vector3 axRot = Vector3(0,1,0);
						//					if(rollPositiveAngle > 0)
						//						axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
						//					else if(rollNegativeAngle > 0)
						//						axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
						//					spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
						//				}*/
						//			}
						//			else
						//				instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,1,0),encoderStep)  ;
						//			totalYaw += encoderStep;
						//			
						//		
						//	}
						//}
						//else{
							
								if(cameraLap != NULL){
									currentCamera -> rotateWorldAroundPoint(Quat(Vector3(0,1,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
									instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,1,0),encoderStep);
									sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL)
										spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
									/*sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL){
										Vector3 axRot = Vector3(0,1,0);
										if(rollPositiveAngle > 0)
											axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
										else if(rollNegativeAngle > 0)
											axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
										spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
									}*/
								}
								else
									instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,1,0),encoderStep);
									totalYaw += encoderStep;
								
							
						//}
						
					
						//instrumentX[0].getTranslation() += 0.1;

						  if(rollPositiveAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle);
						  else if(rollNegativeAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle);
						  sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						 sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						getQWidget()->update();

							if(grasperActivatedLeft == true && graspCloseLeft == true)
								updateGrasper();

						if(gripped == true){
							breakYaw += encoderStep;
							gripped = false;
						 }
						 if(breakYaw >=0.09 && fC!=NULL){
							 fC->clearConstraints();
							 breakYaw = 0.0;
						 }
						
						}
						if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4)
										direction = Vec3d(0,1,0);
									else
										direction = Vec3d(0,0,1);
									temp->updateRay(position,direction);
									itCount++;
								}
						}
					}
					}
						break;
					
					case 'z':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
						for(int hm=0;hm<=20;hm++){
							if(rollPositiveAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollPositiveAngle);
							else if(rollNegativeAngle > 0)
								instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollNegativeAngle);
								
						//if(exP != NULL){
						//	if(1/*exP->blockInstrument == false*/){
						//			if(cameraLap != NULL){
						//				currentCamera -> rotateWorldAroundPoint(Quat(Vector3(0,-1,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
						//				instrumentX[0].getOrientation() = instrumentX[0].getOrientation() *Quat(Vector3(0,-1,0),encoderStep) ;
						//				sofa::component::visualmodel::SpotLight* spLight = NULL;
						//				spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
						//				if(spLight != NULL)
						//					spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
						//				/*if(spLight != NULL){
						//					Vector3 axRot = Vector3(0,-1,0);
						//					if(rollPositiveAngle > 0)
						//						axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
						//					else if(rollNegativeAngle > 0)
						//						axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
						//					spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
						//				}*/
						//			}
						//			else
						//				instrumentX[0].getOrientation() = instrumentX[0].getOrientation() *Quat(Vector3(0,-1,0),encoderStep) ;
						//			totalYaw -= encoderStep;
						//			
						//		
						//	}
						//}
						//else
						//{
							
								if(cameraLap != NULL){
									currentCamera -> rotateWorldAroundPoint(Quat(Vector3(0,-1,0),encoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
									instrumentX[0].getOrientation() = instrumentX[0].getOrientation() *Quat(Vector3(0,-1,0),encoderStep) ;
									sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL)
										spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
									/*sofa::component::visualmodel::SpotLight* spLight = NULL;
									spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
									if(spLight != NULL){
										Vector3 axRot = Vector3(0,-1,0);
										if(rollPositiveAngle > 0)
											axRot = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(axRot);
										else if(rollNegativeAngle > 0)
											axRot = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(axRot);
										spLight->direction.setValue(Quat(axRot,encoderStep).rotate(spLight->direction.getValue()));
									}*/
								}
								else
									instrumentX[0].getOrientation() = instrumentX[0].getOrientation() *Quat(Vector3(0,-1,0),encoderStep) ;
								totalYaw -= encoderStep;
								
							
						//}
					//	std::cerr<<"z pressed"<<std::endl;
						
						  if(rollPositiveAngle > 0)
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollPositiveAngle);
						  else if(rollNegativeAngle > 0)
							instrumentX[0].getOrientation() =   instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollNegativeAngle);
						  sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						  sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						  getQWidget()->update();

						  if(grasperActivatedLeft == true && graspCloseLeft == true )
							updateGrasper();

						  if(gripped == true){
							breakYaw -= encoderStep;
							gripped = false;
						  }
						  if(breakYaw <= -0.09 && fC!=NULL){
							 fC->clearConstraints();
							 breakYaw = 0.0;
						  }
						
						}
						 if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4)
										direction = Vec3d(0,1,0);
									else
										direction = Vec3d(0,0,1);
									temp->updateRay(position,direction);
									itCount++;
								}
						 }
					}
						}
						break;

					case 's':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
					//	std::cerr<<"r pressed"<<std::endl;
						for(int hm=0; hm<=20;hm++){
						instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,0,1),rollEncoderStep);
						rollPositiveAngle += rollEncoderStep;
						rollNegativeAngle -= rollEncoderStep;
						
						
						 if(cameraLap != NULL){
							 currentCamera -> rotateWorldAroundPoint(Quat(Vector3(0,0,1),rollEncoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
							 instrumentX[0].getOrientation() = instrumentX[0].getOrientation() * Quat(Vector3(0,0,1),rollEncoderStep);
							 sofa::component::visualmodel::SpotLight* spLight = NULL;
							 spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
							if(spLight != NULL)
								spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
						//	 sofa::component::visualmodel::SpotLight* spLight = NULL;
						//	 spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
						//	 if(spLight != NULL)
						//		spLight->direction.setValue(Quat(Vector3(0,0,1),rollEncoderStep).rotate(spLight->direction.getValue()));
								
						 }
						  sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						  sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						  getQWidget()->update();

						  if(grasperActivatedLeft == true && graspCloseLeft == true)
							updateGrasper();
						
						}
						  if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4){
										direction = Vec3d(0,1,0);
										direction = Quat(Vector3(0,0,1),rollEncoderStep).rotate(direction);
									}
									else{
										direction = Vec3d(0,0,1);
										direction = Quat(Vector3(0,0,1),rollEncoderStep).rotate(direction);
									}
									temp->updateRay(position,direction);
									itCount++;
								}
						  }
					//	//instrumentX[0].getTranslation() += 0.1;
					}
					}
						break;
					
					case 'r':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
						for(int hm=0; hm<=20;hm++){
						instrumentX[0].getOrientation() = instrumentX[0].getOrientation()*Quat(Vector3(0,0,-1),rollEncoderStep);
					//	std::cerr<<"s pressed"<<std::endl;
						rollNegativeAngle += rollEncoderStep;
						rollPositiveAngle -= rollEncoderStep;
						
						if(cameraLap != NULL){
							 currentCamera -> rotateWorldAroundPoint(Quat(Vector3(0,0,-1),rollEncoderStep),Vector3(0,0,0),currentCamera->p_orientation.getValue());
							 instrumentX[0].getOrientation() = instrumentX[0].getOrientation() * Quat(Vector3(0,0,-1),rollEncoderStep);
							 sofa::component::visualmodel::SpotLight* spLight = NULL;
							 spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
							 if(spLight != NULL)
								spLight->direction.setValue(instrumentX[0].getOrientation().rotate(Vector3(0,0,-1)));
					//		 sofa::component::visualmodel::SpotLight* spLight = NULL;
					//		 spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
					//		 if(spLight != NULL)
					//			spLight->direction.setValue(Quat(Vector3(0,0,-1),rollEncoderStep).rotate(spLight->direction.getValue()));
						 }
						   sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						  sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						  getQWidget()->update();

						  if(grasperActivatedLeft == true && graspCloseLeft == true)
							updateGrasper();
						  
						}

						  if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4){
										direction = Vec3d(0,1,0);
										direction = Quat(Vector3(0,0,-1),rollEncoderStep).rotate(direction);
									}
									else{
										direction = Vec3d(0,0,1);
										direction = Quat(Vector3(0,0,-1),rollEncoderStep).rotate(direction);
									}
									temp->updateRay(position,direction);
									itCount++;
								}
						  }
					}
					}
						break;

					case 'd':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
					//	std::cerr<<"d pressed"<<std::endl;
						for(int hm=0; hm<=3; hm++){
						/*if(exP != NULL){
							if(exP->blockInstrument == false){
								instrumentX[0].getTranslation() -= depthEncoderStep;
							}
							else
								transPaused += depthEncoderStep;
						}
						else*/
							instrumentX[0].getTranslation() -= depthEncoderStep;

						
						
						if(cameraLap != NULL){
							
							double zoomDistance = -depthEncoderStep;
            
							Vector3 trans(0.0, 0.0, zoomDistance);
							trans = currentCamera->cameraToWorldTransform(trans);	// rotate the z-pointing vector to camera orientation
							currentCamera->translate(trans);	// move camera along new direction
						//	sofa::component::visualmodel::SpotLight* spLight = NULL;
						//	spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
						//	if(spLight != NULL)
						//		spLight->position.setValue(Vector3(0,0,instrumentX[0].getTranslation()));
					
						
						}
						
						   
						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						getQWidget()->update();
						if(grasperActivatedLeft == true && graspCloseLeft == true){
							updateGrasper();
						}

						if(gripped == true){
							breakTrans -= encoderStep;
							gripped = false;
						}
						if(breakTrans <= -0.09 && fC!=NULL){
							fC->clearConstraints();
							breakTrans = 0.0;
						}

						}
						if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4)
										direction = Vec3d(0,1,0);
									else
										direction = Vec3d(0,0,1);
									temp->updateRay(position,direction);
									itCount++;
								}
						 }
					}
					}
						break;
					
					case 'e':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}

						for(int hm=0; hm<=3; hm++){
							if(transPaused <= (0.0 * depthEncoderStep)) 
						   instrumentX[0].getTranslation() += depthEncoderStep;
							else 
								transPaused -= depthEncoderStep;
							//std::cout<<transPaused<<std::endl;
						//	std::cerr<<"e pressed"<<std::endl;
						if(grasperActivatedLeft == true && graspCloseLeft == true){
							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
							getQWidget()->update();
							updateGrasper();
						}

						if(cameraLap != NULL){
							
							double zoomDistance = depthEncoderStep;
            
							Vector3 trans(0.0, 0.0, zoomDistance);
							trans = currentCamera->cameraToWorldTransform(trans);	// rotate the z-pointing vector to camera orientation
							currentCamera->translate(trans);	// move camera along new direction
					//		sofa::component::visualmodel::SpotLight* spLight = NULL;
					//		spLight = getScene()->get<sofa::component::visualmodel::SpotLight>();
					//		if(spLight != NULL)
					//			spLight->position.setValue(Vector3(0,0,instrumentX[0].getTranslation()));
											
						}

						if(gripped == true){
							breakTrans += encoderStep;
							gripped = false;
						}
						 if(breakTrans >=0.09 && fC!=NULL){
							 fC->clearConstraints();
							 breakTrans = 0.0;
						 }
						 
						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						getQWidget()->update();	
						}
						  if(LHook != NULL && l_HookActivated == true && kPressed == true){
							 Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> burnTipX = *LHook->read(core::VecCoordId::position());
								int itCount = 0;
								for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
									BurnFunction* temp = *it;
									position = burnTipX[burnTipIndex[itCount]];
									if(itCount < 4)
										direction = Vec3d(0,1,0);
									else
										direction = Vec3d(0,0,1);
									temp->updateRay(position,direction);
									itCount++;
								}
						 }
					}
					}
						break;

					case 'h':
						{
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
							if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
						//std::cerr<<"h pressed"<<std::endl;
						// Changing position of grasper jaws
							for(int hm=0; hm<=6; hm++){
						if(graspRight != NULL && graspLeft != NULL){
						if (!Vec1Objects.empty()){
							for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects.begin(); it!=Vec1Objects.end();it++){
								sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
								sofa::helper::vector<double> k;
								if(gripPos1>=0.0)
								{
									graspCloseLeft = true;
									gripPos1-=graspEncoderStep;
									k.push_back(-gripPos1);
					
								k.push_back(0.0);
								k.push_back(0.0);
								temp->forcePointPosition(0,k);
								k[0] = gripPos1;
				
								temp->forcePointPosition(1,k);
								
									if(grasperActivatedLeft == true /*&& graspCloseLeft == false*/){
										//	gFunc->reactivateRay(getScene());
										for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecLeftJaw.begin(); it != gLFuncVecLeftJaw.end(); it++){
											GraspFunction* temp = *it;
											temp->reactivateRay(getScene());
										}
										for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecRightJaw.begin(); it != gLFuncVecRightJaw.end(); it++){
											GraspFunction* temp = *it;
											temp->reactivateRay(getScene());
										}
									}
								

								}
						
							}
						}
						int gInd = 0;
						int rayCount = 1;
						if(gripPos1<=0.25){
						if(graspLeft != NULL && graspRight != NULL && grasperActivatedLeft == false){
						//	for(int iter=1;iter<=4;iter=iter++){		// for original Johans
							for(int iter=0;iter<16;iter=iter+2){
								GraspFunction* tempGFuncR = new GraspFunction();
								tempGFuncR->init(getScene(),rayCount);
								gLFuncVecRightJaw.push_back(tempGFuncR);
								rayCount++;

								GraspFunction* tempGFuncL = new GraspFunction();
								tempGFuncL->init(getScene(),rayCount);
								gLFuncVecLeftJaw.push_back(tempGFuncL);
								rayCount++;

								graspInsideIndexL[gInd] = (iter+32);
								gInd++;

								/*graspInsideIndexL[gInd] = iter;
								gInd++;*/
								
								// Original Johans indices below

								/*graspInsideIndexL[gInd] = (iter+65);
								gInd++;
								graspInsideIndexL[gInd] = (iter+75);
								gInd++;
								graspInsideIndexL[gInd] = (iter+85);
								gInd++;
								graspInsideIndexL[gInd] = (iter+95);
								gInd++;
								graspInsideIndexL[gInd] = (iter+105);
								gInd++;
								graspInsideIndexL[gInd] = (iter+115);
								gInd++;*/
							}	
							graspInsideIndexL[gInd] = 26;
							gInd++;
							graspInsideIndexL[gInd] = 29;
							
							// The Loop Below is exclusively for original Johans grasper to initialize
							// rays at rest of the indices

							//for(int iter=1;iter<=3;iter=iter++){
							//	GraspFunction* tempGFuncR = new GraspFunction();
							//	tempGFuncR->init(getScene());
							//	gLFuncVecRightJaw.push_back(tempGFuncR);

							//	GraspFunction* tempGFuncL = new GraspFunction();
							//	tempGFuncL->init(getScene());
							//	gLFuncVecLeftJaw.push_back(tempGFuncL);

							//	/*graspInsideIndexL[gInd] = iter;
							//	gInd++;*/

							//	// Original rest of Johans indices below

							//	/*graspInsideIndexL[gInd] = (iter+29);
							//	gInd++;
							//	graspInsideIndexL[gInd] = (iter+41);
							//	gInd++;
							//	graspInsideIndexL[gInd] = (iter+53);
							//	gInd++;*/
							//}
							grasperActivatedLeft = true;
						}
						}
						}

						//Scissors Articulation Code
						else if(scissors != NULL){
							if (!Vec1Objects.empty()){
								for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects.begin(); it!=Vec1Objects.end();it++){
									sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
									sofa::helper::vector<double> k;
									//std::cout<<temp->getName()<<std::endl;
									if(scissorsPos >= 0.0){
										scissorsClose = true;
										scissorsPos -= graspEncoderStep;
										k.push_back(-scissorsPos);					
										k.push_back(0.0);
										k.push_back(0.0);
										temp->forcePointPosition(0,k);
										k[0] = scissorsPos;
				
										temp->forcePointPosition(1,k);
									}
						
								}
							}

						}
						/************************** CLIPPER FUNC ***************************/
						//else if(clipperTop != NULL && clipperBottom != NULL){
						//	if(clipperActivated == false){
						//		clipfunc = new ClipFunction();
						//		clipfunc->init(getScene());
						//		clipperActivated = true;
						//	}
						//	if (!Vec1Objects.empty()){
						//		for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects.begin(); it!=Vec1Objects.end();it++){
						//			sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
						//			sofa::helper::vector<double> k;
						//			//std::cout<<temp->getName()<<std::endl;
						//			if(clipperPos >= -0.3){
						//				
						//				clipperClose = true;
						//				clipperPos -= graspEncoderStep;
						//				if((clipperPos - graspEncoderStep) < -0.3)
						//					clipperFullClose = true;
						//				k.push_back(-clipperPos);					
						//				k.push_back(0.0);
						//				k.push_back(0.0);
						//				temp->forcePointPosition(0,k);
						//				k[0] = clipperPos;
				
						//				temp->forcePointPosition(1,k);
						//			}
						//
						//		}
						//	}
						//}

						/*******************************************************************************/

				/*		sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						getQWidget()->update();*/

						if(scissors != NULL && scissorsActivated == true && scissorsClose == true){
							
							helper::vector<component::engine::BoxROI<sofa::defaulttype::Vec3dTypes>*> boxVec;
							simulation::Node* artery1 = NULL;
							artery1 = getScene()->getTreeNode("Artery1");
							if(artery1 != NULL)
								artery1->getTreeObjects<component::engine::BoxROI<defaulttype::Vec3dTypes>,helper::vector<component::engine::BoxROI<sofa::defaulttype::Vec3dTypes>*>>(&boxVec);

							helper::vector<component::engine::BoxROI<sofa::defaulttype::Vec3dTypes>*> boxVecScissors;
							simulation::Node* scissorsNody = getScene()->getTreeNode("InstrumentScissors");
							scissorsNody->getTreeObjects<component::engine::BoxROI<defaulttype::Vec3dTypes>,helper::vector<component::engine::BoxROI<sofa::defaulttype::Vec3dTypes>*>>(&boxVecScissors);
							
							sofa::component::misc::TopologicalChangeProcessor* tcp = NULL;
							tcp = getScene()->getTreeObject<sofa::component::misc::TopologicalChangeProcessor>();
							if(tcp != NULL && !boxVecScissors.empty()){
								std::cout<<"Found Top Change"<<std::endl;
								if(boxVecScissors[0]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash0.tetrahedronIndices");
								else if(boxVecScissors[1]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash.tetrahedronIndices");
								else if(boxVecScissors[2]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash1.tetrahedronIndices");
								else if(boxVecScissors[3]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash2.tetrahedronIndices");
								else if(boxVecScissors[4]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash3.tetrahedronIndices");
								else if(boxVecScissors[5]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash4.tetrahedronIndices");
								else if(boxVecScissors[6]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash5.tetrahedronIndices");
								else if(boxVecScissors[7]-> f_pointsInROI.getValue().size() > 0)
									tcp->m_tetrahedraToRemove.setParent("@trash6.tetrahedronIndices");
								tcp->m_timeToRemove.setValue(getScene()->getTime()+getScene()->getDt());
							}
							else{

								Vec3d position, direction;
								helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> scissorsX = *scissors->read(core::VecCoordId::position());
								direction = Vec3d(0, -1, 0);
								if(rollPositiveAngle > 0)
									direction = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(direction);
								else if(rollNegativeAngle > 0)
									direction = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(direction);
								position = scissorsX[180];
								iFunc->updateRay(position,direction,true);
								position = scissorsX[322];
								iFunc->updateRay(position,direction,false);
							}
									
						}
						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						getQWidget()->update();
							}
						}
						}
						break;
					
					case 'g':
						{
							if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
							if(previousX == true){
							instrumentMenuFlag = !instrumentMenuFlag;
							showHideInstrumentMenu();
							previousX = false;
							firstX = false;
						}
						/*sofa::component::collision::GraspingPipeline* gPipe = NULL;
						gPipe = getScene()->get<component::collision::GraspingPipeline>();
						if(gPipe != NULL){
							if(gPipe->waitForRelease)
								gPipe->released = true;
						}*/
						// Changing position of grasper jaws
							for(int hm=0; hm<=6; hm++){
								if(graspRight != NULL && graspLeft != NULL){
									if (!Vec1Objects.empty()){
										for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects.begin(); it!=Vec1Objects.end();it++){
											sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
											sofa::helper::vector<double> k;
											if (gripPos1<=0.5)
											{
												gripPos1+=graspEncoderStep;
												graspCloseLeft = false;
												k.push_back(-gripPos1);
					
												k.push_back(0.0);
												k.push_back(0.0);
												temp->forcePointPosition(0,k);
												k[0] = gripPos1;
				
												temp->forcePointPosition(1,k);

												if(localDetachedFlag == true)
													localDetachedFlag = false;

												if(grasperActivatedLeft == true ){
													//gFunc->deactivateRay();
													for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecLeftJaw.begin(); it != gLFuncVecLeftJaw.end(); it++){
														GraspFunction* temp = *it;
														temp->deactivateRay();
													}
													for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecRightJaw.begin(); it != gLFuncVecRightJaw.end(); it++){
														GraspFunction* temp = *it;
														temp->deactivateRay();
													}
												}
											}
						
										}
									}
								}
								//Scissors Articulation Code
								else if(scissors != NULL){
									if (!Vec1Objects.empty()){
										for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects.begin(); it!=Vec1Objects.end();it++){
											sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
											sofa::helper::vector<double> k;
											//std::cout<<temp->getName()<<std::endl;
											if(scissorsPos <= 0.5){
												scissorsClose = false;
												scissorsPos += graspEncoderStep;
												k.push_back(-scissorsPos);					
												k.push_back(0.0);
												k.push_back(0.0);
												temp->forcePointPosition(0,k);
												k[0] = scissorsPos;
				
												temp->forcePointPosition(1,k);
											}
						
										}
									}

								}
								/********************************* CLIPPER FUNC ******************************/
								//else if(clipperTop != NULL && clipperBottom != NULL){
								//	if (!Vec1Objects.empty()){
								//		for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects.begin(); it!=Vec1Objects.end();it++){
								//			sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
								//			sofa::helper::vector<double> k;
								//			//std::cout<<temp->getName()<<std::endl;
								//			if(clipperPos <= -0.05){
								//				if(clipperFullClose == true){
								//					clipperFullClose = false;
								//					clipfunc->addClip();
								//			}
								//			clipperPos += graspEncoderStep;
								//		
								//			k.push_back(-clipperPos);					
								//			k.push_back(0.0);
								//			k.push_back(0.0);
								//			temp->forcePointPosition(0,k);
								//			k[0] = clipperPos;
				
								//			temp->forcePointPosition(1,k);
								//		}
						
								//	}
								//	}
								//}

								/*************************************************************************/

							sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
							sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
							getQWidget()->update();
						}

						//std::cerr<<"g pressed"<<std::endl;
						}
						}
						break;

					case 'k':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						if(LHook != NULL){
							if(l_HookActivated == false){
								for(int iter = 0; iter <=9; iter++){
									BurnFunction* tempbFunc = new BurnFunction();
									tempbFunc->init(getScene());
									bFuncVec.push_back(tempbFunc);
								}
								burnTipIndex[0] = 2;
								burnTipIndex[1] = 43;
								burnTipIndex[2] = 64;
								burnTipIndex[3] = 85;
								burnTipIndex[4] = 137;
								burnTipIndex[5] = 127;
								burnTipIndex[6] = 148;
								burnTipIndex[7] = 169;
								burnTipIndex[8] = 190;
								burnTipIndex[9] = 211;
								l_HookActivated = true;
								kPressed = true;
							}
						}
						else if(LHookRight != NULL){
							if(l_HookActivated2 == false){
								for(int iter = 0; iter <=9; iter++){
									BurnFunction* tempbFunc = new BurnFunction();
									tempbFunc->init(getScene());
									bFuncVec2.push_back(tempbFunc);
								}
								burnTipIndexR[0] = 2;
								burnTipIndexR[1] = 43;
								burnTipIndexR[2] = 64;
								burnTipIndexR[3] = 85;
								burnTipIndexR[4] = 137;
								burnTipIndexR[5] = 127;
								burnTipIndexR[6] = 148;
								burnTipIndexR[7] = 169;
								burnTipIndexR[8] = 190;
								burnTipIndexR[9] = 211;
								l_HookActivated2 = true;
								kPressed = true;
							}
						}
						if(kPressed == false && l_HookActivated == true){
							for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
								BurnFunction* temp = *it;
								temp->reactivateRay(getScene());
							}
							kPressed = true;
						}
						else if(kPressed == false && l_HookActivated2 == true){
							for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
								BurnFunction* temp = *it;
								temp->reactivateRay(getScene());
							}
							kPressed = true;
						}
						
					}
					}
						break;

					case 'K':
					{
						if(translationStart && pitchStart && yawStart && translationStart2 && pitchStart2 && yawStart2){
						kPressed = false;
						if(LHook != NULL && l_HookActivated == true){
							for(std::vector<BurnFunction*>::const_iterator it=bFuncVec.begin(); it!=bFuncVec.end();it++){
								BurnFunction* temp = *it;
								temp->deactivateRay();
							}
						}
						else if(LHook != NULL && l_HookActivated2 == true){
							for(std::vector<BurnFunction*>::const_iterator it=bFuncVec2.begin(); it!=bFuncVec2.end();it++){
								BurnFunction* temp = *it;
								temp->deactivateRay();
							}
						}
					}
					}
						break;

					default:
						break;

				}
				
						
			}
			}
		}
		else
			std::cerr<<"not reading"<<std::endl;
}

void QtViewer::updateGrasper()
{
		Vec3d position, direction;
		int itCount = 0;
		bool elePicked = false;

		if(graspLeft != NULL && !gLFuncVecLeftJaw.empty() != NULL ){
			helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspLeftX = *graspLeft->read(core::VecCoordId::position());
				
			for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecLeftJaw.begin(); it != gLFuncVecLeftJaw.end(); it++){
				GraspFunction* temp = *it;
				position =  graspLeftX[graspInsideIndexL[itCount]];
				direction = Vec3d(0, 0, 1);
				if(rollPositiveAngle > 0)
					direction = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(direction);
				else if(rollNegativeAngle > 0)
					direction = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(direction);
				elePicked = temp->updateRay(position,direction);
				if(elePicked == true)
					gripped = true;
				itCount++;
				}
				
			}

			if(graspRight != NULL && !gLFuncVecRightJaw.empty() != NULL){
				helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspRightX = *graspRight->read(core::VecCoordId::position());
				itCount = 0;
								
				for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecRightJaw.begin(); it != gLFuncVecRightJaw.end(); it++){
					GraspFunction* temp = *it;
					position =  graspRightX[graspInsideIndexL[itCount]];
					direction = Vec3d(0, 0, -1);
					if(rollPositiveAngle > 0)
						direction = Quat(Vector3(0,0,1),rollPositiveAngle).rotate(direction);
					else if(rollNegativeAngle > 0)
						direction = Quat(Vector3(0,0,-1),rollNegativeAngle).rotate(direction);
					elePicked = temp->updateRay(position,direction);
					if(elePicked == true)
						gripped = true;
					itCount++;
				}
			}

}

void QtViewer::updateSecondGrasper()
{
		Vec3d position, direction;
		int itCount = 0;
		bool elePicked = false;

		if(graspLeft2 != NULL && !gLFuncVecLeftJaw2.empty() != NULL ){
			helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspLeftX = *graspLeft2->read(core::VecCoordId::position());
				
			for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecLeftJaw2.begin(); it != gLFuncVecLeftJaw2.end(); it++){
				GraspFunction* temp = *it;
				position =  graspLeftX[graspInsideIndexR[itCount]];
				direction = Vec3d(0, 0, 1);
				if(rollPositiveAngle2 > 0)
					direction = Quat(Vector3(0,0,1),rollPositiveAngle2).rotate(direction);
				else if(rollNegativeAngle2 > 0)
					direction = Quat(Vector3(0,0,-1),rollNegativeAngle2).rotate(direction);
				elePicked = temp->updateRay(position,direction);
				if(elePicked == true)
					gripped2 = true;
				itCount++;
				}
				
			}

			if(graspRight2 != NULL && !gLFuncVecRightJaw2.empty() != NULL){
				helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspRightX = *graspRight2->read(core::VecCoordId::position());
				itCount = 0;
								
				for(std::vector<GraspFunction*>::const_iterator it = gLFuncVecRightJaw2.begin(); it != gLFuncVecRightJaw2.end(); it++){
					GraspFunction* temp = *it;
					position =  graspRightX[graspInsideIndexR[itCount]];
					direction = Vec3d(0, 0, -1);
					if(rollPositiveAngle2 > 0)
						direction = Quat(Vector3(0,0,1),rollPositiveAngle2).rotate(direction);
					else if(rollNegativeAngle > 0)
						direction = Quat(Vector3(0,0,-1),rollNegativeAngle2).rotate(direction);
					elePicked = temp->updateRay(position,direction);
					if(elePicked == true)
						gripped2 = true;
					itCount++;
				}
			}

}

void QtViewer::registerInstrument()
{
			/*std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > instruments;
			groot->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > >(&instruments);
			if (!instruments.empty()){
				instrument = instruments[0];
				if(instruments.size() > 1){
					instrument1 = instruments[1];
			
					std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*> Vec3Objects1;
					
					sofa::simulation::Node* inst1 = NULL;

					inst1 = dynamic_cast<simulation::Node*>(instrument1->getContext());
					if(inst1 != NULL){
						inst1->getTreeObjects<sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>, std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > >(&Vec1Objects1);
						inst1->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > >(&Vec3Objects1);

						if(!Vec3Objects1.empty()){
							for(std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*>::const_iterator it = Vec3Objects1.begin(); it!=Vec3Objects1.end(); it++){
								sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* temp = *it;
								if(temp->getName() == "CollisionLR" || temp->getName() == "CollisionLL")
									graspLeft2 = temp;
								if(temp->getName() == "CollisionRR" || temp->getName() == "CollisionRL")
									graspRight2 = temp;
							}				 
						}
					}
				}

				std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*> Vec3Objects;
				sofa::simulation::Node* leftInstrumentNode = NULL;

				leftInstrumentNode = dynamic_cast<simulation::Node*>(instrument->getContext());
				if(leftInstrumentNode != NULL){
					leftInstrumentNode->getTreeObjects<sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>, std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > >(&Vec1Objects);
					leftInstrumentNode->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > >(&Vec3Objects);

					if(!Vec3Objects.empty()){
						for(std::vector<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*>::const_iterator it = Vec3Objects.begin(); it!=Vec3Objects.end(); it++){
							sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* temp = *it;
							if(temp->getName() == "Camera")
								cameraLap = temp;
							if(temp->getName() == "CollisionLL" || temp->getName() == "CollisionLR")
								graspLeft = temp;
							if(temp->getName() == "CollisionRL" || temp->getName() == "CollisionRR")
								graspRight = temp;
							if(temp->getName() == "Cut")
								scissors = temp;
							if(temp->getName() == "Burn")
								LHook = temp;
							if(temp->getName() == "ClipTop")
								clipperTop = temp;
							if(temp->getName() == "ClipBottom")
								clipperBottom = temp;
						}			 
					}
				}
			}*/
}


// ---------------------------------------------------------
// --- Destructor
// ---------------------------------------------------------
QtViewer::~QtViewer()
{
}

//**********************************************************
//***
//**********************************************************
void QtViewer::showHideInstrumentMenu()
{
	if(instrumentMenuFlag == true){
		instrumentMenuIndex = 0; //first Instrument in menu selected
		button = new QPushButton(this);
        button->setText("Button 1");
        button->setFixedHeight(100);
        button->setFixedWidth(100);
        QPixmap pixmap("E:/Zohaib/menu2/menu2/clipphl.jpg");
        QIcon ButtonIcon(pixmap);
        button->setIcon(ButtonIcon);
        button->setIconSize(pixmap.rect().size());
        // button->setIcon("C:/Users/Bushra/Documents/cliper.jpg");

        //Set Starting point of region 5 pixels inside , make region width & height
        //values same and less than button size so that we obtain a pure-round shape
        QRegion* region = new QRegion(*(new QRect(button->x()+5,button->y()+5,90,90)),QRegion::Ellipse);
        button->setMask(*region);
		button->move(0,670);
		button->show();
 
        button2 = new QPushButton(this);
        button2->setText("Button 1");
        button2->setFixedHeight(100);
        button2->setFixedWidth(100);
       // button2->setGeometry(0, -20, 200, 200);
        
		QPixmap pixmap2("E:/Zohaib/menu2/menu2/scisors.jpg");
        QIcon ButtonIcon2(pixmap2);
        button2->setIcon(ButtonIcon2);
        button2->setIconSize(pixmap2.rect().size());
       // button->setIcon("C:/Users/Bushra/Documents/cliper.jpg");

        //Set Starting point of region 5 pixels inside , make region width & height
        //values same and less than button size so that we obtain a pure-round shape
        QRegion* region2 = new QRegion(*(new QRect(button2->x()+5,button2->y()+5,90,90)),QRegion::Ellipse);
        button2->setMask(*region2);
        button2->move(100,670);
		button2->show();

        QPushButton* button3 = new QPushButton(this);
        button3->setText("Button 1");
        button3->setFixedHeight(100);
        button3->setFixedWidth(100);
       // button2->setGeometry(0, -20, 200, 200);
        QPixmap pixmap3("E:/Zohaib/menu2/menu2/hookss.jpg");
        QIcon ButtonIcon3(pixmap3);
        button3->setIcon(ButtonIcon3);
        button3->setIconSize(pixmap3.rect().size());
       // button->setIcon("C:/Users/Bushra/Documents/cliper.jpg");

        //Set Starting point of region 5 pixels inside , make region width & height
        //values same and less than button size so that we obtain a pure-round shape
        QRegion* region3 = new QRegion(*(new QRect(button3->x()+5,button3->y()+5,90,90)),QRegion::Ellipse);
        button3->setMask(*region3);
        button3->move(200,670);

        QPushButton* button4 = new QPushButton(this);
        button4->setText("Button 1");
        button4->setFixedHeight(100);
        button4->setFixedWidth(100);
       // button2->setGeometry(0, -20, 200, 200);
        QPixmap pixmap4("E:/Zohaib/menu2/menu2/forcep.jpg");
        QIcon ButtonIcon4(pixmap4);
        button4->setIcon(ButtonIcon4);
        button4->setIconSize(pixmap4.rect().size());
       // button->setIcon("C:/Users/Bushra/Documents/cliper.jpg");

        //Set Starting point of region 5 pixels inside , make region width & height
        //values same and less than button size so that we obtain a pure-round shape
        QRegion* region4 = new QRegion(*(new QRect(button4->x()+5,button4->y()+5,90,90)),QRegion::Ellipse);
        button4->setMask(*region4);
        button4->move(300,670);

        QPushButton* button5 = new QPushButton(this);
        button5->setText("Button 1");
        button5->setFixedHeight(100);
        button5->setFixedWidth(100);
       // button2->setGeometry(0, -20, 200, 200);
        QPixmap pixmap5("E:/Zohaib/menu2/menu2/grasperr.jpg");
        QIcon ButtonIcon5(pixmap5);
        button5->setIcon(ButtonIcon5);
        button5->setIconSize(pixmap5.rect().size());
       // button->setIcon("C:/Users/Bushra/Documents/cliper.jpg");

        //Set Starting point of region 5 pixels inside , make region width & height
        //values same and less than button size so that we obtain a pure-round shape
        QRegion* region5 = new QRegion(*(new QRect(button5->x()+5,button5->y()+5,90,90)),QRegion::Ellipse);
        button5->setMask(*region5);
        button5->move(400,670);

        QPushButton* button6 = new QPushButton(this);
        button6->setText("Button 1");
        button6->setFixedHeight(100);
        button6->setFixedWidth(100);
       // button2->setGeometry(0, -20, 200, 200);
        QPixmap pixmap6("E:/Zohaib/menu2/menu2/suction.jpg");
        QIcon ButtonIcon6(pixmap6);
        button6->setIcon(ButtonIcon6);
        button6->setIconSize(pixmap6.rect().size());
       // button->setIcon("C:/Users/Bushra/Documents/cliper.jpg");

        //Set Starting point of region 5 pixels inside , make region width & height
        //values same and less than button size so that we obtain a pure-round shape
        QRegion* region6 = new QRegion(*(new QRect(button6->x()+5,button6->y()+5,90,90)),QRegion::Ellipse);
        button6->setMask(*region6);
        button6->move(500,670);
		}
		if(instrumentMenuFlag == false){
			button->hide();
			button2->hide();
		}
}

// *********************************************************
// ***
// *********************************************************
void QtViewer::selectNextInstrument()
{
	if(instrumentMenuIndex == 0){
		instrumentMenuIndex = 1;
		 QPixmap pixmap("E:/Zohaib/menu2/menu2/clipp.jpg");
        QIcon ButtonIcon(pixmap);
        button->setIcon(ButtonIcon);
  
        QPixmap pixmap2("E:/Zohaib/menu2/menu2/scisorshl.jpg");
        QIcon ButtonIcon2(pixmap2);
        button2->setIcon(ButtonIcon2);
	}

}



// -----------------------------------------------------------------
// --- OpenGL initialization method - includes light definitions,
// --- color tracking, etc.
// -----------------------------------------------------------------
void QtViewer::initializeGL(void)
{
  static GLfloat specref[4];
  static GLfloat ambientLight[4];
  static GLfloat diffuseLight[4];
  static GLfloat specular[4];
  static GLfloat lmodel_ambient[] =
	{ 0.0f, 0.0f, 0.0f, 0.0f };
  static GLfloat lmodel_twoside[] =
	{ GL_FALSE };
  static GLfloat lmodel_local[] =
	{ GL_FALSE };
  bool initialized = false;

  if (!initialized)
  {
    //std::cout << "progname=" << sofa::gui::qt::progname << std::endl;
    //sofa::helper::system::SetDirectory cwd(sofa::helper::system::SetDirectory::GetProcessFullPath(sofa::gui::qt::progname));

//#ifdef __APPLE__
//        std::cout << "QtViewer: disabling vertical refresh sync (Mac version)" << std::endl;
//        const GLint swapInterval = 0;
//        CGLSetParameter(CGLGetCurrentContext(), kCGLCPSwapInterval, &swapInterval);
//#endif

    // Define light parameters
    //_lightPosition[0] = 0.0f;
    //_lightPosition[1] = 10.0f;
    //_lightPosition[2] = 0.0f;
    //_lightPosition[3] = 1.0f;

    _lightPosition[0] = -0.7f;
    _lightPosition[1] = 0.3f;
    _lightPosition[2] = 0.0f;
    _lightPosition[3] = 1.0f;

    ambientLight[0] = 0.5f;
    ambientLight[1] = 0.5f;
    ambientLight[2] = 0.5f;
    ambientLight[3] = 1.0f;

    diffuseLight[0] = 0.9f;
    diffuseLight[1] = 0.9f;
    diffuseLight[2] = 0.9f;
    diffuseLight[3] = 1.0f;

    specular[0] = 1.0f;
    specular[1] = 1.0f;
    specular[2] = 1.0f;
    specular[3] = 1.0f;

    specref[0] = 1.0f;
    specref[1] = 1.0f;
    specref[2] = 1.0f;
    specref[3] = 1.0f;

    // Here we initialize our multi-texturing functions
#ifdef SOFA_HAVE_GLEW
    glewInit();
    if (!GLEW_ARB_multitexture)
      std::cerr << "Error: GL_ARB_multitexture not supported\n";
#endif

    _clearBuffer = GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT;
    _lightModelTwoSides = false;

    glDepthFunc(GL_LEQUAL);
    glClearDepth(1.0);
    glEnable(GL_NORMALIZE);

    glHint(GL_PERSPECTIVE_CORRECTION_HINT, GL_NICEST);

    // Set light model
    glLightModelfv(GL_LIGHT_MODEL_LOCAL_VIEWER, lmodel_local);
    glLightModelfv(GL_LIGHT_MODEL_TWO_SIDE, lmodel_twoside);
    glLightModelfv(GL_LIGHT_MODEL_AMBIENT, lmodel_ambient);

    // Setup 'light 0'
    glLightfv(GL_LIGHT0, GL_AMBIENT, ambientLight);
    glLightfv(GL_LIGHT0, GL_DIFFUSE, diffuseLight);
    glLightfv(GL_LIGHT0, GL_SPECULAR, specular);
    glLightfv(GL_LIGHT0, GL_POSITION, _lightPosition);

    // Enable color tracking
    glColorMaterial(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE);

    // All materials hereafter have full specular reflectivity with a high shine
    glMaterialfv(GL_FRONT, GL_SPECULAR, specref);
    glMateriali(GL_FRONT, GL_SHININESS, 128);

    glShadeModel(GL_SMOOTH);

    // Define background color
    //	    glClearColor(0.0f, 0.0f, 1.0f, 1.0f);

    //glBlendFunc(GL_SRC_ALPHA, GL_ONE);
    //Load texture for logo
    setBackgroundImage();

    glEnableClientState(GL_VERTEX_ARRAY);
    //glEnableClientState(GL_NORMAL_ARRAY);

    // Turn on our light and enable color along with the light
    //glEnable(GL_LIGHTING);
    glEnable(GL_LIGHT0);
    //glEnable(GL_COLOR_MATERIAL);

    //init Quadrics
    _arrow = gluNewQuadric();
    gluQuadricDrawStyle(_arrow, GLU_FILL);
    gluQuadricOrientation(_arrow, GLU_OUTSIDE);
    gluQuadricNormals(_arrow, GLU_SMOOTH);

    _tube = gluNewQuadric();
    gluQuadricDrawStyle(_tube, GLU_FILL);
    gluQuadricOrientation(_tube, GLU_OUTSIDE);
    gluQuadricNormals(_tube, GLU_SMOOTH);

    _sphere = gluNewQuadric();
    gluQuadricDrawStyle(_sphere, GLU_FILL);
    gluQuadricOrientation(_sphere, GLU_OUTSIDE);
    gluQuadricNormals(_sphere, GLU_SMOOTH);

    _disk = gluNewQuadric();
    gluQuadricDrawStyle(_disk, GLU_FILL);
    gluQuadricOrientation(_disk, GLU_OUTSIDE);
    gluQuadricNormals(_disk, GLU_SMOOTH);

    // change status so we only do this stuff once
    initialized = true;

    _beginTime = CTime::getTime();

    printf("GL initialized\n");
  }

   
   // switch to preset view
  resetView();
}

// ---------------------------------------------------------
// ---
// ---------------------------------------------------------
void QtViewer::PrintString(void* font, char* string)
{
  int len, i;

  len = (int) strlen(string);
  for (i = 0; i < len; i++)
  {
    glutBitmapCharacter(font, string[i]);
  }
}

// ---------------------------------------------------------
// ---
// ---------------------------------------------------------
void QtViewer::Display3DText(float x, float y, float z, char* string)
{
  char* c;

  glPushMatrix();
  glTranslatef(x, y, z);
  for (c = string; *c != '\0'; c++)
  {
    glutStrokeCharacter(GLUT_STROKE_ROMAN, *c);
  }
  glPopMatrix();
}

// ---------------------------------------------------
// ---
// ---
// ---------------------------------------------------
void QtViewer::DrawAxis(double xpos, double ypos, double zpos, double arrowSize)
{
  float fontScale = (float) (arrowSize / 600.0);

  Enable<GL_DEPTH_TEST> depth;
  Enable<GL_LIGHTING> lighting;
  Enable<GL_COLOR_MATERIAL> colorMat;

  glPolygonMode(GL_FRONT_AND_BACK, GL_FILL);
  glShadeModel(GL_SMOOTH);

  // --- Draw the "X" axis in red
  glPushMatrix();
  glColor3f(1.0, 0.0, 0.0);
  glTranslated(xpos, ypos, zpos);
  glRotatef(90.0f, 0.0, 1.0, 0.0);
  gluCylinder(_tube, arrowSize / 50.0, arrowSize / 50.0, arrowSize, 10, 10);
  glTranslated(0.0, 0.0, arrowSize);
  gluCylinder(_arrow, arrowSize / 15.0, 0.0, arrowSize / 5.0, 10, 10);
  // ---- Display a "X" near the tip of the arrow
  glTranslated(-0.5 * fontScale * (double) glutStrokeWidth(GLUT_STROKE_ROMAN,
                                                           88), arrowSize / 15.0, arrowSize / 5.0);
  glLineWidth(3.0);
  glScalef(fontScale, fontScale, fontScale);
  glutStrokeCharacter(GLUT_STROKE_ROMAN, 88);
  glScalef(1.0f / fontScale, 1.0f / fontScale, 1.0f / fontScale);
  glLineWidth(1.0f);
  // --- Undo transforms
  glTranslated(-xpos, -ypos, -zpos);
  glPopMatrix();

  // --- Draw the "Y" axis in green
  glPushMatrix();
  glColor3f(0.0, 1.0, 0.0);
  glTranslated(xpos, ypos, zpos);
  glRotatef(-90.0f, 1.0, 0.0, 0.0);
  gluCylinder(_tube, arrowSize / 50.0, arrowSize / 50.0, arrowSize, 10, 10);
  glTranslated(0.0, 0.0, arrowSize);
  gluCylinder(_arrow, arrowSize / 15.0, 0.0, arrowSize / 5.0, 10, 10);
  // ---- Display a "Y" near the tip of the arrow
  glTranslated(-0.5 * fontScale * (double) glutStrokeWidth(GLUT_STROKE_ROMAN,
                                                           89), arrowSize / 15.0, arrowSize / 5.0);
  glLineWidth(3.0);
  glScalef(fontScale, fontScale, fontScale);
  glutStrokeCharacter(GLUT_STROKE_ROMAN, 89);
  glScalef(1.0f / fontScale, 1.0f / fontScale, 1.0f / fontScale);
  glLineWidth(1.0);
  // --- Undo transforms
  glTranslated(-xpos, -ypos, -zpos);
  glPopMatrix();

  // --- Draw the "Z" axis in blue
  glPushMatrix();
  glColor3f(0.0, 0.0, 1.0);
  glTranslated(xpos, ypos, zpos);
  glRotatef(0.0f, 1.0, 0.0, 0.0);
  gluCylinder(_tube, arrowSize / 50.0, arrowSize / 50.0, arrowSize, 10, 10);
  glTranslated(0.0, 0.0, arrowSize);
  gluCylinder(_arrow, arrowSize / 15.0, 0.0, arrowSize / 5.0, 10, 10);
  // ---- Display a "Z" near the tip of the arrow
  glTranslated(-0.5 * fontScale * (double) glutStrokeWidth(GLUT_STROKE_ROMAN,
                                                           90), arrowSize / 15.0, arrowSize / 5.0);
  glLineWidth(3.0);
  glScalef(fontScale, fontScale, fontScale);
  glutStrokeCharacter(GLUT_STROKE_ROMAN, 90);
  glScalef(1.0f / fontScale, 1.0f / fontScale, 1.0f / fontScale);
  glLineWidth(1.0);
  // --- Undo transforms
  glTranslated(-xpos, -ypos, -zpos);
  glPopMatrix();
}

// ---------------------------------------------------
// ---
// ---
// ---------------------------------------------------
void QtViewer::DrawBox(SReal* minBBox, SReal* maxBBox, SReal r)
{
  //std::cout << "box = < " << minBBox[0] << ' ' << minBBox[1] << ' ' << minBBox[2] << " >-< " << maxBBox[0] << ' ' << maxBBox[1] << ' ' << maxBBox[2] << " >"<< std::endl;
  if (r == 0.0)
    r = (Vector3(maxBBox) - Vector3(minBBox)).norm() / 500;

  Enable<GL_DEPTH_TEST> depth;
  Enable<GL_LIGHTING> lighting;
  Enable<GL_COLOR_MATERIAL> colorMat;

  glPolygonMode(GL_FRONT_AND_BACK, GL_FILL);
  glShadeModel(GL_SMOOTH);

  // --- Draw the corners
  glColor3f(0.0, 1.0, 1.0);
  for (int corner = 0; corner < 8; ++corner)
  {
    glPushMatrix();
    glTranslated((corner & 1) ? minBBox[0] : maxBBox[0],
                 (corner & 2) ? minBBox[1] : maxBBox[1],
                 (corner & 4) ? minBBox[2] : maxBBox[2]);
    gluSphere(_sphere, 2 * r, 20, 10);
    glPopMatrix();
  }

  glColor3f(1.0, 1.0, 0.0);
  // --- Draw the X edges
  for (int corner = 0; corner < 4; ++corner)
  {
    glPushMatrix();
    glTranslated(minBBox[0], (corner & 1) ? minBBox[1] : maxBBox[1],
                 (corner & 2) ? minBBox[2] : maxBBox[2]);
    glRotatef(90, 0, 1, 0);
    gluCylinder(_tube, r, r, maxBBox[0] - minBBox[0], 10, 10);
    glPopMatrix();
  }

  // --- Draw the Y edges
  for (int corner = 0; corner < 4; ++corner)
  {
    glPushMatrix();
    glTranslated((corner & 1) ? minBBox[0] : maxBBox[0], minBBox[1],
                 (corner & 2) ? minBBox[2] : maxBBox[2]);
    glRotatef(-90, 1, 0, 0);
    gluCylinder(_tube, r, r, maxBBox[1] - minBBox[1], 10, 10);
    glPopMatrix();
  }

  // --- Draw the Z edges
  for (int corner = 0; corner < 4; ++corner)
  {
    glPushMatrix();
    glTranslated((corner & 1) ? minBBox[0] : maxBBox[0],
                 (corner & 2) ? minBBox[1] : maxBBox[1], minBBox[2]);
    gluCylinder(_tube, r, r, maxBBox[2] - minBBox[2], 10, 10);
    glPopMatrix();
  }
}

// ----------------------------------------------------------------------------------
// --- Draw a "plane" in wireframe. The "plane" is parallel to the XY axis
// --- of the main coordinate system
// ----------------------------------------------------------------------------------
void QtViewer::DrawXYPlane(double zo, double xmin, double xmax, double ymin,
                           double ymax, double step)
{
  register double x, y;

  Enable<GL_DEPTH_TEST> depth;

  glBegin(GL_LINES);
  for (x = xmin; x <= xmax; x += step)
  {
    glVertex3d(x, ymin, zo);
    glVertex3d(x, ymax, zo);
  }
  glEnd();

  glBegin(GL_LINES);
  for (y = ymin; y <= ymax; y += step)
  {
    glVertex3d(xmin, y, zo);
    glVertex3d(xmax, y, zo);
  }
  glEnd();
}

// ----------------------------------------------------------------------------------
// --- Draw a "plane" in wireframe. The "plane" is parallel to the XY axis
// --- of the main coordinate system
// ----------------------------------------------------------------------------------
void QtViewer::DrawYZPlane(double xo, double ymin, double ymax, double zmin,
                           double zmax, double step)
{
  register double y, z;
  Enable<GL_DEPTH_TEST> depth;

  glBegin(GL_LINES);
  for (y = ymin; y <= ymax; y += step)
  {
    glVertex3d(xo, y, zmin);
    glVertex3d(xo, y, zmax);
  }
  glEnd();

  glBegin(GL_LINES);
  for (z = zmin; z <= zmax; z += step)
  {
    glVertex3d(xo, ymin, z);
    glVertex3d(xo, ymax, z);
  }
  glEnd();

}

// ----------------------------------------------------------------------------------
// --- Draw a "plane" in wireframe. The "plane" is parallel to the XY axis
// --- of the main coordinate system
// ----------------------------------------------------------------------------------
void QtViewer::DrawXZPlane(double yo, double xmin, double xmax, double zmin,
                           double zmax, double step)
{
  register double x, z;
  Enable<GL_DEPTH_TEST> depth;

  glBegin(GL_LINES);
  for (x = xmin; x <= xmax; x += step)
  {
    glVertex3d(x, yo, zmin);
    glVertex3d(x, yo, zmax);
  }
  glEnd();

  glBegin(GL_LINES);
  for (z = zmin; z <= zmax; z += step)
  {
    glVertex3d(xmin, yo, z);
    glVertex3d(xmax, yo, z);
  }
  glEnd();
}

// -------------------------------------------------------------------
// ---
// -------------------------------------------------------------------
void QtViewer::DrawLogo()
{
  int w = 0;
  int h = 0;

  if (texLogo && texLogo->getImage())
  {
    h = texLogo->getImage()->getHeight();
    w = texLogo->getImage()->getWidth();
  }
  else
    return;

  Enable<GL_TEXTURE_2D> tex;
  glDisable(GL_DEPTH_TEST);
  glMatrixMode(GL_PROJECTION);
  glPushMatrix();
  glLoadIdentity();
  glOrtho(-0.5, _W, -0.5, _H, -1.0, 1.0);
  glMatrixMode(GL_MODELVIEW);
  glLoadIdentity();

  if (texLogo)
    texLogo->bind();

  glColor3f(1.0f, 1.0f, 1.0f);
  glBegin(GL_QUADS);
  glTexCoord2d(0.0, 0.0);
  glVertex3d((_W - w) / 2, (_H - h) / 2, 0.0);

  glTexCoord2d(1.0, 0.0);
  glVertex3d(_W - (_W - w) / 2, (_H - h) / 2, 0.0);

  glTexCoord2d(1.0, 1.0);
  glVertex3d(_W - (_W - w) / 2, _H - (_H - h) / 2, 0.0);

  glTexCoord2d(0.0, 1.0);
  glVertex3d((_W - w) / 2, _H - (_H - h) / 2, 0.0);
  glEnd();

  glBindTexture(GL_TEXTURE_2D, 0);

  glMatrixMode(GL_PROJECTION);
  glPopMatrix();
  glMatrixMode(GL_MODELVIEW);
}



// ---------------------------------------------------------
// ---
// ---------------------------------------------------------
void QtViewer::drawColourPicking(ColourPickingVisitor::ColourCode code)
{
  // Define background color
  glClearColor(0.0f, 0.0f, 0.0f, 1.0f);
  glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);

  glMatrixMode(GL_PROJECTION);
  glPushMatrix();
  glLoadIdentity();
  glMultMatrixd(lastProjectionMatrix);
  glMatrixMode(GL_MODELVIEW);


  ColourPickingVisitor cpv(sofa::core::visual::VisualParams::defaultInstance(), code);
  cpv.execute( groot.get() );

  glMatrixMode(GL_PROJECTION);
  glPopMatrix();
  glMatrixMode(GL_MODELVIEW);
  glPopMatrix();

}
// -------------------------------------------------------------------
// ---
// -------------------------------------------------------------------
void QtViewer::DisplayOBJs()
{
  if (!groot)
    return;
  Enable<GL_LIGHTING> light;
  Enable<GL_DEPTH_TEST> depth;


  vparams->sceneBBox() = groot->f_bbox.getValue();


  glShadeModel(GL_SMOOTH);
  //glColorMaterial(GL_FRONT_AND_BACK, GL_AMBIENT_AND_DIFFUSE);
  glColor4f(1, 1, 1, 1);
  glDisable(GL_COLOR_MATERIAL);

  if (!initTexturesDone)
  {
    // 		std::cout << "-----------------------------------> initTexturesDone\n";
    //---------------------------------------------------
    getSimulation()->initTextures(groot.get());
    //---------------------------------------------------
    initTexturesDone = true;
  }

  {

    getSimulation()->draw(vparams,groot.get());

    if (_axis)
    {
      DrawAxis(0.0, 0.0, 0.0, 10.0);
      if (vparams->sceneBBox().minBBox().x() < vparams->sceneBBox().maxBBox().x())
        DrawBox(vparams->sceneBBox().minBBoxPtr(),
                vparams->sceneBBox().maxBBoxPtr());
    }
  }

  // glDisable(GL_COLOR_MATERIAL);
}

// -------------------------------------------------------
// ---
// -------------------------------------------------------
void QtViewer::DisplayMenu(void)
{
  Disable<GL_LIGHTING> light;

  glMatrixMode(GL_PROJECTION);
  glPushMatrix();
  glLoadIdentity();
  glOrtho(-0.5, _W, -0.5, _H, -1.0, 1.0);
  glMatrixMode(GL_MODELVIEW);
  glPushMatrix();
  glLoadIdentity();

  glColor3f(0.3f, 0.7f, 0.95f);
  glRasterPos2i(_W / 2 - 5, _H - 15);
  //sprintf(buffer,"FPS: %.1f\n", _frameRate.GetFPS());
  //PrintString(GLUT_BITMAP_HELVETICA_12, buffer);

  glMatrixMode(GL_PROJECTION);
  glPopMatrix();
  glMatrixMode(GL_MODELVIEW);
  glPopMatrix();
}

void QtViewer::MakeStencilMask()
{
  glMatrixMode(GL_PROJECTION);
  glPushMatrix();
  glLoadIdentity();
  gluOrtho2D(0,_W, 0, _H );
  glMatrixMode(GL_MODELVIEW);
  glPushMatrix();
  glLoadIdentity();

  glClear(GL_STENCIL_BUFFER_BIT);
  glStencilFunc(GL_ALWAYS, 0x1, 0x1);
  glStencilOp(GL_REPLACE, GL_REPLACE, GL_REPLACE);
  glColor4f(0,0,0,0);
  glBegin(GL_LINES);
  for (float f=0 ; f< _H ;f+=2.0)
  {
    glVertex2f(0.0, f);
    glVertex2f(_W, f);
  }
  glEnd();

  glMatrixMode(GL_PROJECTION);
  glPopMatrix();
  glMatrixMode(GL_MODELVIEW);
  glPopMatrix();

}

// ---------------------------------------------------------
// ---
// ---------------------------------------------------------
void QtViewer::DrawScene(void)
{
    if (!groot) return;

    if(!currentCamera)
    {
        std::cerr << "ERROR: no camera defined" << std::endl;
        return;
    }

  calcProjection();

  if (_background == 0)
    DrawLogo();

  glLoadIdentity();


  GLdouble mat[16];

  currentCamera->getOpenGLMatrix(mat);
  glMultMatrixd(mat);

  glGetDoublev(GL_MODELVIEW_MATRIX, lastModelviewMatrix);

  //for(int i=0 ; i<16 ;i++)
  //	std::cout << lastModelviewMatrix[i] << " ";
//
//	std::cout << std::endl;

  //Vec position() const { return inverseCoordinatesOf(Vec(0.0,0.0,0.0)); };

  //std::cout << "P " << currentCamera->getPosition() << std::endl;


  if(currentCamera)
  {
	//	std::cout << currentCamera->getPosition() << " " << currentCamera->getOrientation() << std::endl;
	//	std::cout << currentCamera->getZNear() << " " << currentCamera->getZFar() << std::endl;
  }

  if (_renderingMode == GL_RENDER)
  {
    //STEREO MODE
    if(_stereoEnabled)
    {
      //calcProjection();

      //window()->showNormal();
      glEnable(GL_STENCIL_TEST);
      MakeStencilMask();

      //1st pass
      glStencilFunc(GL_EQUAL, 0x1, 0x1);
      glStencilOp(GL_KEEP, GL_KEEP, GL_KEEP);
      DisplayOBJs();

      //2nd pass
      glMatrixMode(GL_MODELVIEW);
      glPushMatrix();
      //glLoadIdentity();
      //translate slighty the camera
      vparams->sceneTransform().translation[0] += _stereoShift;
      vparams->sceneTransform().Apply();
      glStencilFunc(GL_NOTEQUAL, 0x1, 0x1);
      glStencilOp(GL_KEEP, GL_KEEP, GL_KEEP);
      DisplayOBJs();
      glMatrixMode(GL_MODELVIEW);
      glPopMatrix();
      glDisable(GL_STENCIL_TEST);

      vparams->sceneTransform().translation[0] -= _stereoShift;
    }
    else
    {
      //SPLIT MODE
      if (_binocularModeEnabled)
      {
        glMatrixMode(GL_PROJECTION);
        glPushMatrix();
        glViewport(0, 0, _W/2, _H);
        glPopMatrix();
        glMatrixMode(GL_MODELVIEW);
        DisplayOBJs();

        glMatrixMode(GL_PROJECTION);
        glPushMatrix();
        glViewport(_W/2, 0, _W, _H);
        glPopMatrix();
        glMatrixMode(GL_MODELVIEW);
        DisplayOBJs();
      }
      //NORMAL MODE
      else
      {
        //calcProjection(0,0, _W, _H);
        //window()->showNormal();
        DisplayOBJs();
      }
    }

    DisplayMenu(); // always needs to be the last object being drawn
  }

}


// ---------------------------------------------------------
// --- Reshape of the window, reset the projection
// ---------------------------------------------------------
void QtViewer::resizeGL(int width, int height)
{

  _W = width;
  _H = height;

  if(currentCamera)
    currentCamera->setViewport(width, height);

  // 	std::cout << "GL window: " <<width<<"x"<<height <<std::endl;

  calcProjection();
  this->resize(width, height);
  emit( resizeW(_W));
  emit( resizeH(_H));
}

// ---------------------------------------------------------
// --- Reshape of the window, reset the projection
// ---------------------------------------------------------
void QtViewer::calcProjection()
{
  int width = _W;
  int height = _H;
  double xNear, yNear, xOrtho, yOrtho;
  double xFactor = 1.0, yFactor = 1.0;
  double offset;
  double xForeground, yForeground, zForeground, xBackground, yBackground,
    zBackground;
  Vector3 center;

  /// Camera part
  if (!currentCamera)
    return;

  if (groot && (!groot->f_bbox.getValue().isValid() || _axis))
  {
    vparams->sceneBBox() = groot->f_bbox.getValue();
    currentCamera->setBoundingBox(vparams->sceneBBox().minBBox(), vparams->sceneBBox().maxBBox());
  }
  currentCamera->computeZ();

  vparams->zNear() = currentCamera->getZNear();
  vparams->zFar() = currentCamera->getZFar();
  ///


  xNear = 0.35 * vparams->zNear();
  yNear = 0.35 * vparams->zNear();
  offset = 0.001 * vparams->zNear(); // for foreground and background planes

  xOrtho = fabs(vparams->sceneTransform().translation[2]) * xNear
    / vparams->zNear();
  yOrtho = fabs(vparams->sceneTransform().translation[2]) * yNear
    / vparams->zNear();

  if ((height != 0) && (width != 0))
  {
    if (height > width)
    {
      xFactor = 1.0;
      yFactor = (double) height / (double) width;
    }
    else
    {
      xFactor = (double) width / (double) height;
      yFactor = 1.0;
    }
  }
  vparams->viewport() = sofa::helper::make_array(0,0,width,height);

  glViewport(0, 0, width, height);
  glMatrixMode(GL_PROJECTION);
  glLoadIdentity();

  xFactor *= 0.01;
  yFactor *= 0.01;

  //std::cout << xNear << " " << yNear << std::endl;

  zForeground = -vparams->zNear() - offset;
  zBackground = -vparams->zFar() + offset;

  if (currentCamera->getCameraType() == core::visual::VisualParams::PERSPECTIVE_TYPE)
    gluPerspective(currentCamera->getFieldOfView(), (double) width / (double) height, vparams->zNear(), vparams->zFar());
  else
  {
    float ratio = vparams->zFar() / (vparams->zNear() * 20);
    Vector3 tcenter = vparams->sceneTransform() * center;
    if (tcenter[2] < 0.0)
    {
      ratio = -300 * (tcenter.norm2()) / tcenter[2];
    }
    glOrtho((-xNear * xFactor) * ratio, (xNear * xFactor) * ratio, (-yNear
                                                                    * yFactor) * ratio, (yNear * yFactor) * ratio,
            vparams->zNear(), vparams->zFar());
  }

  xForeground = -zForeground * xNear / vparams->zNear();
  yForeground = -zForeground * yNear / vparams->zNear();
  xBackground = -zBackground * xNear / vparams->zNear();
  yBackground = -zBackground * yNear / vparams->zNear();

  xForeground *= xFactor;
  yForeground *= yFactor;
  xBackground *= xFactor;
  yBackground *= yFactor;

  glGetDoublev(GL_PROJECTION_MATRIX, lastProjectionMatrix);

  glMatrixMode(GL_MODELVIEW);
}

// ---------------------------------------------------------
// ---
// ---------------------------------------------------------
void QtViewer::paintGL()
{
	if(startExercise){
  // clear buffers (color and depth)
  if (_background == 0)
    glClearColor(0.0f, 0.0f, 0.0f, 1.0f);
  else if (_background == 1)
    glClearColor(0.0f, 0.0f, 0.0f, 0.0f);
  else if (_background == 2)
    glClearColor(backgroundColour[0], backgroundColour[1],
                 backgroundColour[2], 1.0f);
  glClearDepth(1.0);
  glClear( _clearBuffer);

  // draw the scene
  DrawScene();

  if (_video)
  {
#ifdef CAPTURE_PERIOD
    static int counter = 0;
    if ((counter++ % CAPTURE_PERIOD)==0)
#endif
      }

  SofaViewer::captureEvent();

  if (_waitForRender)
    _waitForRender = false;

  emit( redrawn());
}
}

// ---------------------------------------------------------
// ---
// ---------------------------------------------------------
void QtViewer::ApplySceneTransformation(int /* x */, int /* y */)
{
  update();
}

// ---------------------------------------------------------
// ---
// ---------------------------------------------------------
void QtViewer::ApplyMouseInteractorTransformation(int x, int y)
{
  // Mouse Interaction
  double coeffDeplacement = 0.025;
  const sofa::defaulttype::BoundingBox sceneBBox = vparams->sceneBBox();
  if (sceneBBox.isValid() && ! sceneBBox.isFlat())
    coeffDeplacement *= 0.001 * (sceneBBox.maxBBox()
                                 - sceneBBox.minBBox()).norm();
  Quaternion conjQuat, resQuat, _newQuatBckUp;

  float x1, x2, y1, y2;

  if (_mouseInteractorMoving)
  {
    if (_mouseInteractorRotationMode)
    {
      if ((_mouseInteractorSavedPosX != x) || (_mouseInteractorSavedPosY
                                               != y))
      {
        x1 = 0;
        y1 = 0;
        x2 = (2.0f * (x + (-_mouseInteractorSavedPosX + _W / 2.0f))
              - _W) / _W;
        y2 = (_H - 2.0f
              * (y + (-_mouseInteractorSavedPosY + _H / 2.0f))) / _H;

        _mouseInteractorTrackball.ComputeQuaternion(x1, y1, x2, y2);
        _mouseInteractorCurrentQuat
          = _mouseInteractorTrackball.GetQuaternion();
        _mouseInteractorSavedPosX = x;
        _mouseInteractorSavedPosY = y;

        _mouseInteractorNewQuat = _mouseInteractorCurrentQuat
          + _mouseInteractorNewQuat;
        _mouseRotate = true;
      }
      else
      {
        _mouseRotate = false;
      }

      update();
    }
    else if (_mouseInteractorTranslationMode)
    {
      _mouseInteractorAbsolutePosition = Vector3(0, 0, 0);

      if (_translationMode == XY_TRANSLATION)
      {
        _mouseInteractorAbsolutePosition[0] = coeffDeplacement * (x
                                                                  - _mouseInteractorSavedPosX);
        _mouseInteractorAbsolutePosition[1] = -coeffDeplacement * (y
                                                                   - _mouseInteractorSavedPosY);

        _mouseInteractorSavedPosX = x;
        _mouseInteractorSavedPosY = y;
      }
      else if (_translationMode == Z_TRANSLATION)
      {
        _mouseInteractorAbsolutePosition[2] = coeffDeplacement * (y
                                                                  - _mouseInteractorSavedPosY);

        _mouseInteractorSavedPosX = x;
        _mouseInteractorSavedPosY = y;
      }

      _mouseTrans = true;
      update();
    }
  }
}

// ----------------------------------------
// --- Handle events (mouse, keyboard, ...)
// ----------------------------------------


void QtViewer::keyPressEvent(QKeyEvent * e)
{
  if (isControlPressed()) // pass event to the scene data structure
  {
    //	cerr<<"QtViewer::keyPressEvent, key = "<<e->key()<<" with Control pressed "<<endl;
    if (groot)
    {
      sofa::core::objectmodel::KeypressedEvent keyEvent(e->key());
      groot->propagateEvent(core::ExecParams::defaultInstance(), &keyEvent);
    }
  }
  else
    // control the GUI
    switch (e->key())
    {

#ifdef TRACKING
    case Qt::Key_X:
    {
      tracking = !tracking;
      break;
    }
#endif // TRACKING
    case Qt::Key_C:
    {
      // --- switch interaction mode
      if (!_mouseInteractorTranslationMode)
      {
        std::cout << "Interaction Mode ON\n";
        _mouseInteractorTranslationMode = true;
        _mouseInteractorRotationMode = false;
      }
      else
      {
        std::cout << "Interaction Mode OFF\n";
        _mouseInteractorTranslationMode = false;
        _mouseInteractorRotationMode = false;
      }
      break;
    }
    default:
    {
      SofaViewer::keyPressEvent(e);
      e->ignore();
    }
    update();
    }
}

void QtViewer::keyReleaseEvent(QKeyEvent * e)
{
  SofaViewer::keyReleaseEvent(e);
}

void QtViewer::wheelEvent(QWheelEvent* e)
{
  SofaViewer::wheelEvent(e);
}

void QtViewer::mousePressEvent(QMouseEvent * e)
{
  mouseEvent(e);

  SofaViewer::mousePressEvent(e);
}

void QtViewer::mouseReleaseEvent(QMouseEvent * e)
{
  mouseEvent(e);

  SofaViewer::mouseReleaseEvent(e);

}

void QtViewer::mouseMoveEvent(QMouseEvent * e)
{

#ifdef TRACKING
  if (tracking)
  {
    if (groot)
    {
      if (firstTime)
      {
        savedX = e->x();
        savedY = e->y();
        firstTime = false;
      }

      sofa::core::objectmodel::MouseEvent mouseEvent(sofa::core::objectmodel::MouseEvent::Move,e->x()-savedX,e->y()-savedY);
      groot->propagateEvent(core::ExecParams::defaultInstance(), &mouseEvent);
      QCursor::setPos(mapToGlobal(QPoint(savedX, savedY)));
    }
  }
  else
  {
    firstTime = true;
  }
#endif // TRACKING
  //if the mouse move is not "interactive", give the event to the camera
  if(!mouseEvent(e))
    SofaViewer::mouseMoveEvent(e);
}

// ---------------------- Here are the mouse controls for the scene  ----------------------
bool QtViewer::mouseEvent(QMouseEvent * e)
{
  bool isInteractive = false;
  int eventX = e->x();
  int eventY = e->y();
  if (_mouseInteractorRotationMode)
  {
    switch (e->type())
    {
    case QEvent::MouseButtonPress:
      // Mouse left button is pushed
      if (e->button() == Qt::LeftButton)
      {
        _mouseInteractorMoving = true;
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
      }
      break;

    case QEvent::MouseMove:
      //
      break;

    case QEvent::MouseButtonRelease:
      // Mouse left button is released
      if (e->button() == Qt::LeftButton)
      {
        if (_mouseInteractorMoving)
        {
          _mouseInteractorMoving = false;
        }
      }
      break;

    default:
      break;
    }
    ApplyMouseInteractorTransformation(eventX, eventY);
  }
  else if (_mouseInteractorTranslationMode)
  {
    switch (e->type())
    {
    case QEvent::MouseButtonPress:
      // Mouse left button is pushed
      if (e->button() == Qt::LeftButton)
      {
        _translationMode = XY_TRANSLATION;
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
        _mouseInteractorMoving = true;
      }
      // Mouse right button is pushed
      else if (e->button() == Qt::RightButton)
      {
        _translationMode = Z_TRANSLATION;
        _mouseInteractorSavedPosY = eventY;
        _mouseInteractorMoving = true;
      }

      break;

    case QEvent::MouseButtonRelease:
      // Mouse left button is released
      if ((e->button() == Qt::LeftButton) && (_translationMode
                                              == XY_TRANSLATION))
      {
        _mouseInteractorMoving = false;
      }
      // Mouse right button is released
      else if ((e->button() == Qt::RightButton) && (_translationMode
                                                    == Z_TRANSLATION))
      {
        _mouseInteractorMoving = false;
      }
      break;

    default:
      break;
    }

    ApplyMouseInteractorTransformation(eventX, eventY);
  }
  else if (e->state() & Qt::ShiftButton)
  {
    isInteractive = true;
    SofaViewer::mouseEvent(e);
  }
  else if (e->state() & Qt::ControlButton)
  {
    isInteractive = true;
  }
  else if (e->state() & Qt::AltButton)
  {
    isInteractive = true;
    switch (e->type())
    {
    case QEvent::MouseButtonPress:
      // Mouse left button is pushed
      if (e->button() == Qt::LeftButton)
      {
        _navigationMode = BTLEFT_MODE;
        _mouseInteractorMoving = true;
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
      }
      // Mouse right button is pushed
      else if (e->button() == Qt::RightButton)
      {
        _navigationMode = BTRIGHT_MODE;
        _mouseInteractorMoving = true;
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
      }
      // Mouse middle button is pushed
      else if (e->button() == Qt::MidButton)
      {
        _navigationMode = BTMIDDLE_MODE;
        _mouseInteractorMoving = true;
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
      }
      break;

    case QEvent::MouseMove:
      //
      break;

    case QEvent::MouseButtonRelease:
      // Mouse left button is released
      if (e->button() == Qt::LeftButton)
      {
        if (_mouseInteractorMoving)
        {
          _mouseInteractorMoving = false;
        }
      }
      // Mouse right button is released
      else if (e->button() == Qt::RightButton)
      {
        if (_mouseInteractorMoving)
        {
          _mouseInteractorMoving = false;
        }
      }
      // Mouse middle button is released
      else if (e->button() == Qt::MidButton)
      {
        if (_mouseInteractorMoving)
        {
          _mouseInteractorMoving = false;
        }
      }
      break;

    default:
      break;
    }
    if (_mouseInteractorMoving && _navigationMode == BTLEFT_MODE)
    {
      int dx = eventX - _mouseInteractorSavedPosX;
      int dy = eventY - _mouseInteractorSavedPosY;
      if (dx || dy)
      {
        _lightPosition[0] -= dx * 0.1;
        _lightPosition[1] += dy * 0.1;
        std::cout << "Light = " << _lightPosition[0] << " "
                  << _lightPosition[1] << " " << _lightPosition[2]
                  << std::endl;
        update();
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
      }
    }
    else if (_mouseInteractorMoving && _navigationMode == BTRIGHT_MODE)
    {
      int dx = eventX - _mouseInteractorSavedPosX;
      int dy = eventY - _mouseInteractorSavedPosY;
      if (dx || dy)
      {
        //g_DepthBias[0] += dx*0.01;
        //g_DepthBias[1] += dy * 0.01;
        //std::cout << "Depth bias = " << g_DepthBias[0] << " "
        //          << g_DepthBias[1] << std::endl;
        update();
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
      }
    }
    else if (_mouseInteractorMoving && _navigationMode == BTMIDDLE_MODE)
    {

      int dx = eventX - _mouseInteractorSavedPosX;
      int dy = eventY - _mouseInteractorSavedPosY;
      if (dx || dy)
      {
          //g_DepthOffset[0] += dx * 0.01;
          //g_DepthOffset[1] += dy * 0.01;
          //std::cout << "Depth offset = " << g_DepthOffset[0] << " "
          //          << g_DepthOffset[1] << std::endl;
        update();
        _mouseInteractorSavedPosX = eventX;
        _mouseInteractorSavedPosY = eventY;
      }
    }
  }

  return isInteractive;
}

void QtViewer::moveRayPickInteractor(int eventX, int eventY)
{

  const sofa::core::visual::VisualParams::Viewport& viewport = vparams->viewport();

  Vec3d p0, px, py, pz, px1, py1;
  gluUnProject(eventX, viewport[3] - 1 - (eventY), 0,
               lastModelviewMatrix, lastProjectionMatrix,
               viewport.data(), &(p0[0]), &(p0[1]), &(p0[2]));
  gluUnProject(eventX + 1, viewport[3] - 1 - (eventY), 0,
               lastModelviewMatrix, lastProjectionMatrix,
               viewport.data(), &(px[0]), &(px[1]), &(px[2]));
  gluUnProject(eventX, viewport[3] - 1 - (eventY + 1), 0,
               lastModelviewMatrix, lastProjectionMatrix,
               viewport.data(), &(py[0]), &(py[1]), &(py[2]));
  gluUnProject(eventX, viewport[3] - 1 - (eventY), 0.1,
               lastModelviewMatrix, lastProjectionMatrix,
               viewport.data(), &(pz[0]), &(pz[1]), &(pz[2]));
  gluUnProject(eventX + 1, viewport[3] - 1 - (eventY), 0.1,
               lastModelviewMatrix, lastProjectionMatrix,
               viewport.data(), &(px1[0]), &(px1[1]), &(px1[2]));
  gluUnProject(eventX, viewport[3] - 1 - (eventY + 1), 0,
               lastModelviewMatrix, lastProjectionMatrix,
               viewport.data(), &(py1[0]), &(py1[1]), &(py1[2]));
  px1 -= pz;
  py1 -= pz;
  px -= p0;
  py -= p0;
  pz -= p0;
  double r0 = sqrt(px.norm2() + py.norm2());
  double r1 = sqrt(px1.norm2() + py1.norm2());
  r1 = r0 + (r1 - r0) / pz.norm();
  px.normalize();
  py.normalize();
  pz.normalize();
  Mat4x4d transform;
  transform.identity();
  transform[0][0] = px[0];
  transform[1][0] = px[1];
  transform[2][0] = px[2];
  transform[0][1] = py[0];
  transform[1][1] = py[1];
  transform[2][1] = py[2];
  transform[0][2] = pz[0];
  transform[1][2] = pz[1];
  transform[2][2] = pz[2];
  transform[0][3] = p0[0];
  transform[1][3] = p0[1];
  transform[2][3] = p0[2];
  Mat3x3d mat;
  mat = transform;
  Quat q;
  q.fromMatrix(mat);

  Vec3d position, direction;
  position = transform * Vec4d(0, 0, 0, 1);
  direction = transform * Vec4d(0, 0, 1, 0);
  direction.normalize();
  pick.updateRay(position, direction);
}

// -------------------------------------------------------------------
// ---
// -------------------------------------------------------------------
void QtViewer::resetView()
{
  Vec3d position;
  Quat orientation;
  bool fileRead = false;

  if (!sceneFileName.empty())
  {
    std::string viewFileName = sceneFileName + "." + VIEW_FILE_EXTENSION;
    /*std::ifstream in(viewFileName.c_str());
      if (!in.fail())
      {
      in >> position[0];
      in >> position[1];
      in >> position[2];
      in >> orientation[0];
      in >> orientation[1];
      in >> orientation[2];
      in >> orientation[3];
      orientation.normalize();

      in.close();
      fileRead = true;

      setView(position, orientation);
      }*/
    fileRead = currentCamera->importParametersFromFile(viewFileName);
  }

  //if there is no .view file , look at the center of the scene bounding box
  // and with a Up vector in the same axis as the gravity
  if (!fileRead)
  {
    newView();
  }

  update();
  //updateGL();

  //SofaViewer::resetView();
  //ResetScene();
}

void QtViewer::newView()
{
  SofaViewer::newView();
}

void QtViewer::getView(Vec3d& pos, Quat& ori) const
{
  SofaViewer::getView(pos, ori);
}

void QtViewer::setView(const Vec3d& pos, const Quat &ori)
{
  SofaViewer::setView(pos, ori);
}

void QtViewer::moveView(const Vec3d& pos, const Quat &ori)
{
  SofaViewer::moveView(pos, ori);
}

void QtViewer::saveView()
{
  if (!sceneFileName.empty())
  {
    std::string viewFileName = sceneFileName + "." + VIEW_FILE_EXTENSION;
    /*std::ofstream out(viewFileName.c_str());
      if (!out.fail())
      {
      const Vec3d& camPosition = currentCamera->getPosition();
      const Quat& camOrientation = currentCamera->getOrientation();

      out << camPosition[0] << " "
      << camPosition[1] << " "
      << camPosition[2] << "\n";
      out << camOrientation[0] << " "
      << camOrientation[1] << " "
      << camOrientation[2] << " "
      << camOrientation[3] << "\n";
      out.close();
      }*/
    if(currentCamera->exportParametersInFile(viewFileName))
      std::cout << "View parameters saved in " << viewFileName << std::endl;
    else 
      std::cout << "Error while saving view parameters in " << viewFileName << std::endl;
  }
}

void QtViewer::setSizeW(int size)
{
  resizeGL(size, _H);
  updateGL();
}

void QtViewer::setSizeH(int size)
{
  resizeGL(_W, size);
  updateGL();
}

QString QtViewer::helpString()
{
  QString
    text(
    "<H1>QtViewer</H1><hr>\
<ul>\
<li><b>Mouse</b>: TO NAVIGATE<br></li>\
<li><b>Shift & Left Button</b>: TO PICK OBJECTS<br></li>\
<li><b>B</b>: TO CHANGE THE BACKGROUND<br></li>\
<li><b>C</b>: TO SWITCH INTERACTION MODE: press the KEY C.<br>\
Allow or not the navigation with the mouse.<br></li>\
<li><b>Ctrl + L</b>: TO DRAW SHADOWS<br></li>\
<li><b>O</b>: TO EXPORT TO .OBJ<br>\
The generated files scene-time.obj and scene-time.mtl are saved in the running project directory<br></li>\
<li><b>P</b>: TO SAVE A SEQUENCE OF OBJ<br>\
Each time the frame is updated an obj is exported<br></li>\
<li><b>R</b>: TO DRAW THE SCENE AXIS<br></li>\
<li><b>S</b>: TO SAVE A SCREENSHOT<br>\
The captured images are saved in the running project directory under the name format capturexxxx.bmp<br></li>\
<li><b>T</b>: TO CHANGE BETWEEN A PERSPECTIVE OR AN ORTHOGRAPHIC CAMERA<br></li>\
<li><b>V</b>: TO SAVE A VIDEO<br>\
Each time the frame is updated a screenshot is saved<br></li>\
<li><b>Esc</b>: TO QUIT ::sofa:: <br></li></ul>");
  return text;
}

}// namespace qt

} // namespace viewer

}

} // namespace gui

} // namespace sofa

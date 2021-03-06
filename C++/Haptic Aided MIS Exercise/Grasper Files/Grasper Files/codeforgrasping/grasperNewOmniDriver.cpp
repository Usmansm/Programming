/******************************************************************************
*       SOFA, Simulation Open-Framework Architecture, version 1.0 RC 1        *
*                (c) 2006-2011 MGH, INRIA, USTL, UJF, CNRS                    *
*                                                                             *
* This library is free software; you can redistribute it and/or modify it     *
* under the terms of the GNU Lesser General Public License as published by    *
* the Free Software Foundation; either version 2.1 of the License, or (at     *
* your option) any later version.                                             *
*                                                                             *
* This library is distributed in the hope that it will be useful, but WITHOUT *
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or       *
* FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License *
* for more details.                                                           *
*                                                                             *
* You should have received a copy of the GNU Lesser General Public License    *
* along with this library; if not, write to the Free Software Foundation,     *
* Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301 USA.          *
*******************************************************************************
*                               SOFA :: Plugins                               *
*                                                                             *
* Authors: The SOFA Team and external contributors (see Authors.txt)          *
*                                                                             *
* Contact information: contact@sofa-framework.org                             *
******************************************************************************/

#include "NewOmniDriver.h"

#include <iostream>
#include <math.h>
#include <sofa/helper/Factory.inl>
#include <sofa/component/visualmodel/VisualStyle.h>
#include <sofa/core/visual/DisplayFlags.h>
#include <sofa/defaulttype/LaparoscopicRigidTypes.h>

#include <sofa/simulation/common/PropagateEventVisitor.h>

#include <sofa/simulation/common/MechanicalVisitor.h>
#include <sofa/simulation/common/UpdateMappingVisitor.h>


#include <sofa/component/collision/MouseInteractor.h>
#include <sofa/component/mapping/IdentityMapping.h>
#include <sofa/core/Mapping.h>

#include <sofa/component/collision/RayContact.h>

#include <sofa/component/container/MechanicalObject.h>
#include <sofa/component/projectiveconstraintset/FixedConstraint.h>

//sensable namespace

double prevTime;
bool frame;
bool visuCreation;

namespace sofa
{

namespace component
{

namespace controller
{

using namespace sofa::defaulttype;

static HHD hHD = HD_INVALID_HANDLE ;
vector< HHD > hHDVector;
vector<NewOmniDriver*> autreOmniDriver;
static HDSchedulerHandle hStateHandle = HD_INVALID_HANDLE;
bool initDeviceBool;
bool frameAvant = false;
bool desktop = false;
int compteur_debug = 0;
int tempFlag = 0;//ANAS WORK
int mouseInteractorSavedPosX;//ANAS WORK
int mouseInteractorSavedPosY;//ANAS WORK
int mouseInteractorSavedPosZ;//ANAS WORK

int tempFlag1 = 0;//ANAS WORK-second instrument
int mouseInteractorSavedPosX1;//ANAS WORK-second instrument
int mouseInteractorSavedPosY1;//ANAS WORK-second instrument
int mouseInteractorSavedPosZ1;//ANAS WORK-second instrument
bool _chooseSecondInstrument = false;
bool sceneCameraCheck = false;

 
//retour en cas d'erreur
//TODO: rajouter le numero de l'interface qui pose pb
void printError(const HDErrorInfo *error, const char *message)
{
    cout<<hdGetErrorString(error->errorCode)<<endl;
    cout<<"HHD: "<<error->hHD<<endl;
    cout<<"Error Code: "<<error->hHD<<endl;
    cout<<"Internal Error Code: "<<error->internalErrorCode<<endl;
    cout<<"Message: "<<message<<endl;
}


//boucle qui recupere les info sur l'interface et les copie sur data->servoDeviceData
HDCallbackCode HDCALLBACK stateCallback(void * /*userData*/)
{
	//vector<NewOmniDriver*> autreOmniDriver = static_cast<vector<NewOmniDriver*>>(userData);
	//OmniData* data = static_cast<OmniData*>(userData);
	//FIXME : Apparenlty, this callback is run before the mechanical state initialisation. I've found no way to know whether the mechcanical state is initialized or not, so i wait ...
	//cout<<"\n\n\n\n\n\n Anas wants to see" <<"\n\n\n\n";
	//ANAS => this loop will keep runnning from the time we hit the run sofa button regardless of wether or not we are ANIMATING it
	RigidTypes::VecCoord positionDevs;
	RigidTypes::VecDeriv forceDevs;

	forceDevs.clear();
	positionDevs.resize(autreOmniDriver.size());
	forceDevs.resize(autreOmniDriver.size());  	
	
	for(unsigned int i=0;i<autreOmniDriver.size();i++)
	{
		//cout<<"\n\n\n\n\n\n Anas wants to see" <<"\n\n\n\n";
		if(autreOmniDriver[i]->data.servoDeviceData.stop)
		{
			//cout<<"\n\n\n\n\n\n Anas wants to see"<<autreOmniDriver[i]->data.servoDeviceData.stop<<"\n\n\n\n";
			return HD_CALLBACK_DONE;
		}
		if (!autreOmniDriver[i]->data.servoDeviceData.ready)
		{
			return HD_CALLBACK_CONTINUE;
		}
		HHD hapticHD = hHDVector[i];
		hdMakeCurrentDevice(hapticHD);

		hdBeginFrame(hapticHD);

		if((autreOmniDriver[i]->data.servoDeviceData.m_buttonState & HD_DEVICE_BUTTON_1) || autreOmniDriver[i]->data.permanent_feedback)
			hdSetDoublev(HD_CURRENT_FORCE, autreOmniDriver[i]->data.currentForce);

		autreOmniDriver[i]->data.servoDeviceData.id = hapticHD;

		// Retrieve the current button(s).
		hdGetIntegerv(HD_CURRENT_BUTTONS, &autreOmniDriver[i]->data.servoDeviceData.m_buttonState);
		//cout<<"\nAnas"<<autreOmniDriver[i]->data.servoDeviceData.m_buttonState;
		

		//get the position
		hdGetDoublev(HD_CURRENT_POSITION, autreOmniDriver[i]->data.servoDeviceData.m_devicePosition);
		//cout<<"\n"<<autreOmniDriver[i]->data.servoDeviceData.m_devicePosition<<"\n";

		// Get the column major transform
		HDdouble transform[16];
		hdGetDoublev(HD_CURRENT_TRANSFORM, transform);
		

		// get Position and Rotation from transform => put in servoDeviceData	
		Mat3x3d mrot;
		Quat rot;
		for (int u=0; u<3; u++)
		{for (int j=0; j<3; j++)
			{mrot[u][j] = transform[j*4+u];
		//cout<<"ANAS WANT TO SEE TRANSFORM\n"<<transform[j*4+u];
		}}
		
		rot.fromMatrix(mrot);
		rot.normalize();
		    
		double factor = 0.001;
		Vec3d pos(transform[12+0]*factor, transform[12+1]*factor, transform[12+2]*factor); // omni pos is in mm => sofa simulation are in meters by default
		autreOmniDriver[i]->data.servoDeviceData.pos=pos;
		//cout<<"\n\n\n\nANAS is checking\n\n\n\n\n\n"<<pos;
		
		// verify that the quaternion does not flip:
		if ((rot[0]*autreOmniDriver[i]->data.servoDeviceData.quat[0]
		    +rot[1]*autreOmniDriver[i]->data.servoDeviceData.quat[1]
			+rot[2]*autreOmniDriver[i]->data.servoDeviceData.quat[2]
			+rot[3]*autreOmniDriver[i]->data.servoDeviceData.quat[3]) < 0)
			for (int u=0;u<4;u++)
				rot[u] *= -1;

		for (int u=0;u<4;u++)
			autreOmniDriver[i]->data.servoDeviceData.quat[u] = rot[u];

		SolidTypes<double>::Transform baseOmni_H_endOmni(pos* autreOmniDriver[i]->data.scale, rot);
		SolidTypes<double>::Transform world_H_virtualTool = autreOmniDriver[i]->data.world_H_baseOmni * baseOmni_H_endOmni * autreOmniDriver[i]->data.endOmni_H_virtualTool;
		
		
//partie pour ff simulatnn?e
#if 1
		positionDevs[i].getCenter()=world_H_virtualTool.getOrigin();
		positionDevs[i].getOrientation()=world_H_virtualTool.getOrientation();
		//cout<<"\n\n\n\nANAS is checking\n\n\n\n\n\n"<<world_H_virtualTool.getOrientation();

		//angles
		hdGetFloatv(HD_CURRENT_JOINT_ANGLES,autreOmniDriver[i]->angle1);
		hdGetFloatv(HD_CURRENT_GIMBAL_ANGLES,autreOmniDriver[i]->angle2);

		hdEndFrame(hapticHD);

	}

	if(autreOmniDriver[0]->data.forceFeedback != NULL)
	{
		(autreOmniDriver[0]->data.forceFeedback)->computeForce(positionDevs,forceDevs);
		//cout<<"\n\n\n\nANAS is checking\n\n\n\n\n\n"<<positionDevs;
		//cout<<"\n\n\n\nANAS is checking\n\n\n\n\n\n"<<forceDevs;
	}



	for(unsigned int i=0;i<autreOmniDriver.size();i++)
	{
		

		/// COMPUTATION OF THE vituralTool 6D POSITION IN THE World COORDINATES
		SolidTypes<double>::Transform baseOmni_H_endOmni((autreOmniDriver[i]->data.servoDeviceData.pos)* autreOmniDriver[i]->data.scale, autreOmniDriver[i]->data.servoDeviceData.quat);
		

		Vec3d world_pos_tool = positionDevs[i].getCenter();
		Quat world_quat_tool = positionDevs[i].getOrientation();

		// we compute its value in the current Tool frame:
		SolidTypes<double>::SpatialVector Wrench_tool_inTool(world_quat_tool.inverseRotate(forceDevs[i].getVCenter()),  world_quat_tool.inverseRotate(forceDevs[i].getVOrientation())  );	
		// we transport (change of application point) its value to the endOmni frame
		SolidTypes<double>::SpatialVector Wrench_endOmni_inEndOmni = autreOmniDriver[i]->data.endOmni_H_virtualTool * Wrench_tool_inTool;
		// we compute its value in the baseOmni frame
		SolidTypes<double>::SpatialVector Wrench_endOmni_inBaseOmni( baseOmni_H_endOmni.projectVector(Wrench_endOmni_inEndOmni.getForce()), baseOmni_H_endOmni.projectVector(Wrench_endOmni_inEndOmni.getTorque()) );

		autreOmniDriver[i]->data.currentForce[0] = Wrench_endOmni_inBaseOmni.getForce()[0] * autreOmniDriver[i]->data.forceScale;
		autreOmniDriver[i]->data.currentForce[1] = Wrench_endOmni_inBaseOmni.getForce()[1] * autreOmniDriver[i]->data.forceScale;
		autreOmniDriver[i]->data.currentForce[2] = Wrench_endOmni_inBaseOmni.getForce()[2] * autreOmniDriver[i]->data.forceScale;

		//cout<<currentForce[0]<<currentForce[1]<<currentForce[2]<<endl;

 	//	if((autreOmniDriver[i]->data.servoDeviceData.m_buttonState & HD_DEVICE_BUTTON_1) || autreOmniDriver[i]->data.permanent_feedback)
		//{
		//	if(currentForce[0]>0.1)
		//		cout<<currentForce[0]<<" "<<currentForce[1]<<" "<<currentForce[2]<<endl;
		//	HHD hapticHD = hHDVector[i];
		//	hdMakeCurrentDevice(hapticHD);
		//	hdBeginFrame(hapticHD);
		//	//hdSetDoublev(HD_CURRENT_FORCE, autreOmniDriver[i]->data.currentForce);
		//	hdEndFrame(hapticHD);
		//}

		autreOmniDriver[i]->data.servoDeviceData.nupdates++;
	}

#else

		Vec3d world_pos_tool = world_H_virtualTool.getOrigin();
		Quat world_quat_tool = world_H_virtualTool.getOrientation();
		//truc sur le forcefeedback
		/////////////// 6D rendering ////////////////	
		SolidTypes<double>::SpatialVector Twist_tool_inWorld(Vec3d(0.0,0.0,0.0), Vec3d(0.0,0.0,0.0)); // Todo: compute a velocity !!
		SolidTypes<double>::SpatialVector Wrench_tool_inWorld(Vec3d(0.0,0.0,0.0), Vec3d(0.0,0.0,0.0));

		if (autreOmniDriver[i]->data.forceFeedback != NULL)
			(autreOmniDriver[i]->data.forceFeedback)->computeWrench(world_H_virtualTool,Twist_tool_inWorld,Wrench_tool_inWorld ); //en faire qu'un et uttiliser compute force

		// we compute its value in the current Tool frame:
		SolidTypes<double>::SpatialVector Wrench_tool_inTool(world_quat_tool.inverseRotate(Wrench_tool_inWorld.getForce()),  world_quat_tool.inverseRotate(Wrench_tool_inWorld.getTorque())  );	
		// we transport (change of application point) its value to the endOmni frame
		SolidTypes<double>::SpatialVector Wrench_endOmni_inEndOmni = autreOmniDriver[i]->data.endOmni_H_virtualTool * Wrench_tool_inTool;
		// we compute its value in the baseOmni frame
		SolidTypes<double>::SpatialVector Wrench_endOmni_inBaseOmni( baseOmni_H_endOmni.projectVector(Wrench_endOmni_inEndOmni.getForce()), baseOmni_H_endOmni.projectVector(Wrench_endOmni_inEndOmni.getTorque()) );

		double currentForce[3];
		currentForce[0] = Wrench_endOmni_inBaseOmni.getForce()[0] * autreOmniDriver[i]->data.forceScale;
		currentForce[1] = Wrench_endOmni_inBaseOmni.getForce()[1] * autreOmniDriver[i]->data.forceScale;
		currentForce[2] = Wrench_endOmni_inBaseOmni.getForce()[2] * autreOmniDriver[i]->data.forceScale;     

 		if(autreOmniDriver[i]->data.permanent_feedback)
		{
			hdSetDoublev(HD_CURRENT_FORCE, currentForce);
			HDErrorInfo error;
			if (HD_DEVICE_ERROR(error = hdGetError()))
			{
				cout<<hdGetErrorString(error.errorCode)<<endl;
				cout<<"HHD: "<<error.hHD<<endl;
				cout<<"Error Code: "<<error.hHD<<endl;
				cout<<"Internal Error Code: "<<error.internalErrorCode<<endl;
			}
		}

		autreOmniDriver[i]->data.servoDeviceData.nupdates++;

		//angles
		hdGetFloatv(HD_CURRENT_JOINT_ANGLES,autreOmniDriver[i]->angle1);
		hdGetFloatv(HD_CURRENT_GIMBAL_ANGLES,autreOmniDriver[i]->angle2);

		hdEndFrame(hapticHD);
	}

#endif


	return HD_CALLBACK_CONTINUE;
}

//stop le Scheduler
void exitHandler()
{
    hdStopScheduler();
    hdUnschedule(hStateHandle);
}


//copie les info sur le device de data->servoDeviceData ? data->deviceData 
//TODO: ou plutot remplir le PosD ici et gicler data->deviceData qui servirait plus a rien
HDCallbackCode HDCALLBACK copyDeviceDataCallback(void * /*pUserData*/)
{
	//OmniData *data = static_cast<OmniData*>(pUserData);
	//memcpy(&data->deviceData, &data->servoDeviceData, sizeof(DeviceData));
	//data->servoDeviceData.nupdates = 0;
	//data->servoDeviceData.ready = true;
	//vector<NewOmniDriver*> autreOmniDriver = static_cast<vector<NewOmniDriver*>>(pUserData);
	for(unsigned int i=0;i<autreOmniDriver.size();i++)
	{//cout<<"\n\n\n\n\n\n Anas wants to see"<<&autreOmniDriver[i]->data.deviceData<<"\n\n\n\n";
		memcpy(&autreOmniDriver[i]->data.deviceData, &autreOmniDriver[i]->data.servoDeviceData, sizeof(DeviceData));
		autreOmniDriver[i]->data.servoDeviceData.nupdates = 0;
		autreOmniDriver[i]->data.servoDeviceData.ready = true;
	}
    return HD_CALLBACK_DONE;
}

//stop le callback > difference avec exithandler??
HDCallbackCode HDCALLBACK stopCallback(void * /*pUserData*/)
{
	//OmniData *data = static_cast<OmniData*>(pUserData);
	//data->servoDeviceData.stop = true;
	//vector<NewOmniDriver*> autreOmniDriver = static_cast<vector<NewOmniDriver*>>(pUserData);
	for(unsigned int i=0;i<autreOmniDriver.size();i++)
	{
		autreOmniDriver[i]->data.servoDeviceData.stop =true;
	//cout<<"\n\n\n\n\n\n Anas wants to see"<<&autreOmniDriver[i]->data.deviceData<<"\n\n\n\n";
	}
    return HD_CALLBACK_DONE;
}

/**
 * Sets up the device,
 */
//initialise l'omni > TODO: a appeler plusieur fois depuis l'interface n?1
int NewOmniDriver::initDevice()
{  
	cout<<"init Device is called"<<endl;
	HDErrorInfo error;
	for(unsigned int i=0;i<autreOmniDriver.size();i++)
	{
		//cout<<"\n\n\n\n\n\n Anas wants to see"<<&autreOmniDriver[i]->data.servoDeviceData<<"\n\n\n\n";
		//if(autreOmniDriver[i]->isInitialized)
		//{
		//	return 0;
		//}
		while(autreOmniDriver[i]->isInitialized && i<autreOmniDriver.size())
		{//cout<<"\n\n\n\n\n\n Anas wants to see"<<&autreOmniDriver[i]->data.servoDeviceData<<"\n\n\n\n";
			i++;
			if(i==autreOmniDriver.size())
				return 0;
		}

		autreOmniDriver[i]->isInitialized = true;
		autreOmniDriver[i]->data.deviceData.quat.clear();
		autreOmniDriver[i]->data.servoDeviceData.quat.clear(); 

		if(hHDVector[i] == HD_INVALID_HANDLE)
		{
			hHDVector[i] = hdInitDevice(autreOmniDriver[i]->deviceName.getValue().c_str());
			
			if (HD_DEVICE_ERROR(error = hdGetError())) 
			{
				cout<<"[NewOmni] Failed to initialize the device "<<autreOmniDriver[i]->deviceName.getValue()<<endl;
			}
			cout<<deviceName.getValue()<<"[NewOmni] Found device "<<autreOmniDriver[i]->deviceName.getValue()<<endl;

			hdEnable(HD_FORCE_OUTPUT);
			hdEnable(HD_MAX_FORCE_CLAMPING);
		}
	}

	 //Start the servo loop scheduler.
	hdStartScheduler();
	if (HD_DEVICE_ERROR(error = hdGetError())) 
	{
		cout<<"[NewOmni] Failed to start the scheduler"<<endl;
	}

	for(unsigned int i=0;i<autreOmniDriver.size();i++)
	{
		autreOmniDriver[i]->data.servoDeviceData.ready = false;
		autreOmniDriver[i]->data.servoDeviceData.stop = false;
	}

    hStateHandle = hdScheduleAsynchronous( stateCallback, (void*) &autreOmniDriver, HD_MAX_SCHEDULER_PRIORITY);

    if (HD_DEVICE_ERROR(error = hdGetError()))
    {
		printError(&error, "erreur avec le device");
		cout<<deviceName.getValue()<<endl;
    }
	return 0;
}

//constructeur
NewOmniDriver::NewOmniDriver()
: forceScale(initData(&forceScale, 1.0, "forceScale","Default forceScale applied to the force feedback. "))
, scale(initData(&scale, 100.0, "scale","Default scale applied to the Phantom Coordinates. "))
, positionBase(initData(&positionBase, Vec3d(0,0,0), "positionBase","Position of the interface base in the scene world coordinates"))
, orientationBase(initData(&orientationBase, Quat(0,0,0,1), "orientationBase","Orientation of the interface base in the scene world coordinates"))
, positionTool(initData(&positionTool, Vec3d(0,0,0), "positionTool","Position of the tool in the omni end effector frame"))
, orientationTool(initData(&orientationTool, Quat(0,0,0,1), "orientationTool","Orientation of the tool in the omni end effector frame"))
, permanent(initData(&permanent, false, "permanent" , "Apply the force feedback permanently"))
, omniVisu(initData(&omniVisu, false, "omniVisu", "Visualize the position of the interface in the virtual scene"))
, posDevice(initData(&posDevice, "posDevice", "position of the base of the part of the device"))
, posStylus(initData(&posStylus, "posStylus", "position of the base of the stylus"))
, locDOF(initData(&locDOF,"locDOF","localisation of the DOFs MechanicalObject"))
, deviceName(initData(&deviceName,std::string("Default PHANToM"),"deviceName","name of the device"))
, deviceIndex(initData(&deviceIndex,1,"deviceIndex","index of the device"))
, openTool(initData(&openTool,"openTool","opening of the tool"))
, maxTool(initData(&maxTool,1.0,"maxTool","maxTool value"))
, minTool(initData(&minTool,0.0,"minTool","minTool value"))
, openSpeedTool(initData(&openSpeedTool,0.1,"openSpeedTool","openSpeedTool value"))
, closeSpeedTool(initData(&closeSpeedTool,0.1,"closeSpeedTool","closeSpeedTool value"))
{
	this->f_listening.setValue(true);
	data.forceFeedback = NULL;
	noDevice = false;
	firstInit=true;
	firstDevice = true;
        pi=3.1415926535897932384626433832795;
		graspClose = false;	// default is false 
		grasperActivated = false;			//	default is false
		//sF = new sofa::gui::qt::viewer::SofaViewer();
		//cout<<"\n\n\n\n\n\n Anas wants to see"<<"\n\n\n\n";
}

//destructeur
NewOmniDriver::~NewOmniDriver()
{
  
}

//arrete le call back TODO: a ne lancer que depuis l'interface n?1
void NewOmniDriver::cleanup()
{
	cout << "NewOmniDriver::cleanup()" << endl;
	if(firstDevice)
		hdScheduleSynchronous(stopCallback, (void*) &autreOmniDriver, HD_MIN_SCHEDULER_PRIORITY);
    isInitialized = false;
}

//configure l'effort
void NewOmniDriver::setForceFeedback(LCPForceFeedback<Rigid3dTypes>* ff)
{
	// the forcefeedback is already set
	if(data.forceFeedback == ff)
	{
		//Anas WORK below
		//cout<< "\n\n\nAnas want see"<<ff<<"\n\n\n" ;
		return;
		
	}

	data.forceFeedback = ff;
	//ANAS WORK BELOW
	//cout<< "\n\n\nAnas want see"<<ff<<"\n\n\n";
};

//execut? 1 fois au d?marrage de runsofa, initialisation de toutes les variables sauf celle en lien avec l'haptique
void NewOmniDriver::init() 
{
	if(firstDevice)
	{
		simulation::Node *context = dynamic_cast<simulation::Node*>(this->getContext());
		context->getTreeObjects<NewOmniDriver>(&autreOmniDriver);
		cout<<"OmniDriver detectes:"<<endl;
		for(unsigned int i=0; i<autreOmniDriver.size(); i++)
		{
			cout<<"newOmniDriver component "<<i<<" = "<<autreOmniDriver[i]->getName()<<autreOmniDriver[i]->deviceName.getValue()<<endl;
			autreOmniDriver[i]->deviceIndex.setValue(i);
			hHDVector.push_back(HD_INVALID_HANDLE);
			autreOmniDriver[i]->firstDevice=false;
			autreOmniDriver[i]->data.currentForce[0]=0;
			autreOmniDriver[i]->data.currentForce[1]=0;
			autreOmniDriver[i]->data.currentForce[2]=0;
		}
		firstDevice=true;
	}
//cout<<"\n\nANAS\n\n"<<"\n\n";
	std::cout << deviceName.getValue()+" [NewOmni] init" << endl;	

	
	modX=false;
	modY=false;
	modZ=false;
	modS=false;
	axesActif=false;

	initDeviceBool=false;

	VecCoord& posD =(*posDevice.beginEdit());
	posD.resize(11);	
	posDevice.endEdit();

	initVisu=false;
	changeScale=false;
	visuActif=false;
	isInitialized = false;
	frame=false;
	visuCreation=false;

	for(int i=0;i<10;i++)
	{
		visualNode[i].visu = NULL;
		visualNode[i].mapping = NULL;
	}

	parent = dynamic_cast<simulation::Node*>(this->getContext());

	sofa::simulation::tree::GNode *parentRoot = dynamic_cast<sofa::simulation::tree::GNode*>(this->getContext());

        nodePrincipal= parentRoot->createChild("omniVisu "+deviceName.getValue());

       // nodePrincipal = sofa::simulation::getSimulation()->createNewGraph();


	parentRoot->getParent()->addChild(nodePrincipal);
	nodePrincipal->updateContext();

	DOFs=NULL;
	
	//if(DOFs==NULL)
	//{
	//	nodeDOF = sofa::simulation::getSimulation()->newNode("nodeDOF");

	//	DOFs = new sofa::component::container::MechanicalObject<sofa::defaulttype::Rigid3dTypes>();
	//	nodeDOF->addObject(DOFs);
	//	DOFs->name.setValue("DOFs");

	//	VecCoord& posS =*(DOFs->x.beginEdit());
	//	posS.resize(1);
	//	posS[0].getCenter()=Vec3d(0.0,0.0,0.0);
	//	posS[0].getOrientation()=sofa::helper::Quater<float>::Quater(0.0,0.0,0.0,1.0);
	//	DOFs->x.endEdit();

	//	VecCoord& posS2 =*(DOFs->xfree.beginEdit());
	//	posS2.resize(2);
	//	posS2[0].getCenter()=Vec3d(0.0,0.0,0.0);
	//	posS2[0].getOrientation()=sofa::helper::Quater<float>::Quater(0.0,0.0,0.0,1.0);
	//	DOFs->xfree.endEdit();

	//	DOFs->init();			
	//}
	//nodePrincipal->addChild(nodeDOF);
	//nodeDOF->updateContext();
	firstInit=false;

	if(!initVisu)
	{
		rigidDOF=NULL;
		
		if(rigidDOF==NULL)
		{

                        rigidDOF = sofa::core::objectmodel::New<MMechanicalObject>();

			nodePrincipal->addObject(rigidDOF);
			rigidDOF->name.setValue("rigidDOF");

			VecCoord& posDOF =*(rigidDOF->x.beginEdit());
			posDOF.resize(11);
			rigidDOF->x.endEdit();

			rigidDOF->init();

			nodePrincipal->updateContext();				
		}

		
                visualNode[0].node = nodePrincipal->createChild("stylet");
                visualNode[1].node = nodePrincipal->createChild("articulation 2");
                visualNode[2].node = nodePrincipal->createChild("articulation 1");
                visualNode[3].node = nodePrincipal->createChild("arm 2");
                visualNode[4].node = nodePrincipal->createChild("arm 1");
                visualNode[5].node = nodePrincipal->createChild("base");
                visualNode[6].node = nodePrincipal->createChild("socle");
                visualNode[7].node = nodePrincipal->createChild("axe X");
                visualNode[8].node = nodePrincipal->createChild("axe Y");
                visualNode[9].node = nodePrincipal->createChild("axe Z");
		
		for(int i=0;i<10;i++)
		{
			if(i>6)
				nodePrincipal->addChild(visualNode[i].node);
			
			if(visualNode[i].visu == NULL && visualNode[i].mapping == NULL)
			{

                            // create the visual model and add it to the graph //
                                visualNode[i].visu = sofa::core::objectmodel::New<sofa::component::visualmodel::OglModel>();

				visualNode[i].visu->name.setValue("VisualParticles");
				if(i==0)
				{
					visualNode[i].visu->fileMesh.setValue("mesh/stylusO.obj");
				}
				if(i==1)
					visualNode[i].visu->fileMesh.setValue("mesh/articulation5O.obj");
				if(i==2)
					visualNode[i].visu->fileMesh.setValue("mesh/articulation4O.obj");
				if(i==3)
					visualNode[i].visu->fileMesh.setValue("mesh/articulation3O.obj");
				if(i==4)
					visualNode[i].visu->fileMesh.setValue("mesh/articulation2O.obj");
				if(i==5)
					visualNode[i].visu->fileMesh.setValue("mesh/articulation1O.obj");
				if(i==6)
					visualNode[i].visu->fileMesh.setValue("mesh/BASEO.obj");
				if(i==7)
					visualNode[i].visu->fileMesh.setValue("mesh/axeX.obj");
				if(i==8)
					visualNode[i].visu->fileMesh.setValue("mesh/axeY.obj");
				if(i==9)
					visualNode[i].visu->fileMesh.setValue("mesh/axeZ.obj");
				visualNode[i].visu->init();
				visualNode[i].visu->initVisual();
				visualNode[i].visu->updateVisual();
                                visualNode[i].node->addObject(visualNode[i].visu);

                            // create the visual mapping and at it to the graph //
                                visualNode[i].mapping = sofa::core::objectmodel::New< sofa::component::mapping::RigidMapping< Rigid3dTypes, ExtVec3fTypes > > ();
                                visualNode[i].mapping->setModels(rigidDOF.get(), visualNode[i].visu.get());
				visualNode[i].node->addObject(visualNode[i].mapping);
				visualNode[i].mapping->name.setValue("RigidMapping");
				visualNode[i].mapping->f_mapConstraints.setValue(false);
				visualNode[i].mapping->f_mapForces.setValue(false);
				visualNode[i].mapping->f_mapMasses.setValue(false);
                                //visualNode[i].mapping->m_inputObject.setValue("@../RigidDOF");
                                //visualNode[i].mapping->m_outputObject.setValue("@VisualParticles");
				visualNode[i].mapping->index.setValue(i+1);
				visualNode[i].mapping->init();
			}
		}

		visualNode[7].visu->setColor(1.0,0.0,0.0,0);
		visualNode[8].visu->setColor(0.0,1.0,0.0,0);
		visualNode[9].visu->setColor(0.0,0.0,1.0,0);

		nodePrincipal->updateContext();

		for(int i=0;i<11;i++)
		{
			visualNode[i].node->updateContext();
		}


		for(int j=0;j<8;j++)
		{
                        sofa::defaulttype::ResizableExtVector< sofa::defaulttype::Vec<3,float> > &scaleMapping = *(visualNode[j].mapping->points.beginEdit());
			for(unsigned int i=0;i<scaleMapping.size();i++)
				for(int p=0;p<3;p++)
				scaleMapping[i].at(p)*=(float)(1.0*scale.getValue()/100.0);
			visualNode[j].mapping->points.endEdit();
		}

		oldScale=(float)scale.getValue();
		changeScale=false;
		initVisu=true;
		visuActif=false;
	}

	Vec1d& openT = (*openTool.beginEdit());
	openT[0]=maxTool.getValue();
	openTool.endEdit();


}


//recupere dans la scene l'effort a donner a l'interface
void NewOmniDriver::bwdInit()
{
    std::cout<<"NewOmniDriver::bwdInit() is called"<<std::endl;

    simulation::Node *context = dynamic_cast<simulation::Node *>(this->getContext()); // access to current node
    LCPForceFeedback<Rigid3dTypes>* ff = context->getTreeObject< LCPForceFeedback<Rigid3dTypes> >();
    //it does not even come here
//	cout<<"ANAS want to see"<<ff;
	if(ff)
	{
		this->setForceFeedback(ff);
		//it does not even come here
		//cout<<"ANAS want to see"<<ff;
		}

	setDataValue();
	  
    if(firstDevice && initDevice()==-1)
	{
		noDevice=true;
		std::cout<<"WARNING NO DEVICE"<<std::endl;
	}

	if(firstDevice)
	{
		DOFs = context->get<sofa::component::container::MechanicalObject<sofa::defaulttype::Rigid3dTypes> > ();

		if (DOFs==NULL)
		{
			std::cout<<" no Meca Object found"<<std::endl;
		}
		else
		{
			VecCoord& posT = *(DOFs->x0.beginEdit());
			posT.resize(autreOmniDriver.size());
			DOFs->x0.endEdit();
			for(unsigned int i=1; i<autreOmniDriver.size();i++)
				autreOmniDriver[i]->DOFs=DOFs;
		}
	}
}
	
//configure data
void NewOmniDriver::setDataValue()
{
	data.scale = scale.getValue();
	data.forceScale = forceScale.getValue();
	Quat q = orientationBase.getValue();
	q.normalize();
	orientationBase.setValue(q);
	data.world_H_baseOmni.set( positionBase.getValue(), q		);
	q=orientationTool.getValue();
	q.normalize();
	data.endOmni_H_virtualTool.set(positionTool.getValue(), q);
	data.permanent_feedback = permanent.getValue();
	//cout<<"\n\n\n\n\n\n Anas wants to see"<<"\n\n\n\n";
}

//lance toute les fonction de reset (cas d'un update)
void NewOmniDriver::reset()
{
	std::cout<<"NewOmniDriver::reset() is called" <<std::endl;
	this->reinit();
}

//idem
void NewOmniDriver::reinit()
{
	std::cout<<"NewOmniDriver::reinit() is called" <<std::endl;
	
	this->cleanup();
	this->bwdInit();
	if(data.scale!=oldScale)
		changeScale = true;	
//cout<<"\n\n\n\n\n\n Anas wants to see"<<"\n\n\n\n";
    std::cout<<"NewOmniDriver::reinit() done" <<std::endl;
}

//recupere les coordonn?es de l'interface dans le composant omnidriver pour les mettre dans le conposant mechanical object 
//TODO: copier directement lesdonn?es dans le mechanical object dans la fonction on animated event
//adapte l'echelle des composant
//?? qu'est ce qui apelle ctte fonction?
void NewOmniDriver::draw()
{
    //cout << "NewOmniDriver::draw is called" << endl;

	if(initVisu)
	{
		if(!visuActif && omniVisu.getValue())
		{
			for(int i=0;i<7;i++)
			{
				nodePrincipal->addChild(visualNode[i].node);
				visualNode[i].node->updateContext();
			}
			nodePrincipal->updateContext();
			visuActif=true;
		}
		VecCoord& posD =(*posDevice.beginEdit());
		VecCoord& posDOF =*(rigidDOF->x.beginEdit());
		posD.resize(11);
		posDOF.resize(11);
		for(int i=0;i<11;i++)
		{
			posDOF[i].getCenter() = posD[i].getCenter();
			posDOF[i].getOrientation() = posD[i].getOrientation();
		}
		//for(int i=0;i<10;i++)
		//{
		//	if(omniVisu.getValue() || i>6)
		//	{
		//		visualNode[i].visu->drawVisual();
		//		visualNode[i].mapping->draw();
		//	}
		//}
		rigidDOF->x.endEdit();
		posDevice.endEdit();

		
		//scale
		if(changeScale)
		{
			float rapport=((float)data.scale)/oldScale;
			for(int j = 0; j<11 ; j++)
			{
                                sofa::defaulttype::ResizableExtVector< sofa::defaulttype::Vec<3,float> > &scaleMapping = *(visualNode[j].mapping->points.beginEdit());
				for(unsigned int i=0;i<scaleMapping.size();i++)
				{
					for(int p=0;p<3;p++)
						scaleMapping[i].at(p)*=rapport;
				}
				visualNode[j].mapping->points.endEdit();
				oldScale=(float)data.scale;					
			}
			changeScale=false;
		}
	}
	//delete omnivisual
	if(initVisu && visuActif && !omniVisu.getValue())
	{
		for(int i=0;i<7;i++)
		{
			nodePrincipal->removeChild(visualNode[i].node);
		}
		visuActif=false;
	}

}

//evenement touche clavier appuiy?e
void NewOmniDriver::onKeyPressedEvent(core::objectmodel::KeypressedEvent *kpe)
{
	//cout<<kpe->getKey()<<" "<<int(kpe->getKey())<<endl;
	if(axesActif && omniVisu.getValue())
	{
		if ((kpe->getKey()=='X' || kpe->getKey()=='x') && !modX )
		{
			modX=true;
		}
		if ((kpe->getKey()=='Y' || kpe->getKey()=='y') && !modY )
		{
			modY=true;
		}
		if ((kpe->getKey()=='Z' || kpe->getKey()=='z') && !modZ )
		{
			modZ=true;
		}
		if ((kpe->getKey()=='Q' || kpe->getKey()=='q') && !modS )
		{
			modS=true;
		}
		if (kpe->getKey()==18) //left
		{
			if(modX || modY || modZ)
			{
				Quat& orientB =(*orientationBase.beginEdit());
				Vec3d deplacement=orientB.rotate(Vec3d(-(int)modX,-(int)modY,-(int)modZ));
				orientationBase.endEdit();
				Vec3d& posB =(*positionBase.beginEdit());
				posB+=deplacement;
				positionBase.endEdit();
			}
			else
			if(modS)
			{
				data.scale--;
				changeScale = true;	
			}
		}
		else
		if (kpe->getKey()==20) //right
		{
			
			if(modX || modY || modZ)
			{
				Quat& orientB =(*orientationBase.beginEdit());
				Vec3d deplacement=orientB.rotate(Vec3d((int)modX,(int)modY,(int)modZ));
				orientationBase.endEdit();
				Vec3d& posB =(*positionBase.beginEdit());
				posB+=deplacement;
				positionBase.endEdit();
			}
			else
			if(modS)
			{
				data.scale++;
				changeScale = true;	
			}
		}
		else
		if ((kpe->getKey()==21) && (modX || modY || modZ)) //down
		{
			Quat& orientB =(*orientationBase.beginEdit());
			sofa::helper::Quater<double> quarter_transform(Vec3d((int)modX,(int)modY,(int)modZ),-pi/50);
			orientB*=quarter_transform;
			orientationBase.endEdit();
		}
		else
		if ((kpe->getKey()==19) && (modX || modY || modZ)) //up
		{
			Quat& orientB =(*orientationBase.beginEdit()); 
			sofa::helper::Quater<double> quarter_transform(Vec3d((int)modX,(int)modY,(int)modZ),pi/50);
			orientB*=quarter_transform;
			orientationBase.endEdit();
		}
		if ((kpe->getKey()=='E' || kpe->getKey()=='e'))
		{
			std::cout<<"reset position"<<std::endl;

			Quat& orientB =(*orientationBase.beginEdit());
			orientB.clear();
			orientationBase.endEdit();

			Vec3d& posB =(*positionBase.beginEdit());
			posB.clear();
			positionBase.endEdit();
		}
	}
	if ((kpe->getKey()==48+deviceIndex.getValue()) && initVisu)
	{
		if(!axesActif)
		{
			visualNode[7].visu->setColor(1.0,0.0,0.0,1);
			visualNode[8].visu->setColor(0.0,1.0,0.0,1);
			visualNode[9].visu->setColor(0.0,0.0,1.0,1);
			axesActif=true;
		}
		else
		{
			visualNode[7].visu->setColor(1.0,0.0,0.0,0);
			visualNode[8].visu->setColor(0.0,1.0,0.0,0);
			visualNode[9].visu->setColor(0.0,0.0,1.0,0);
			axesActif=false;
		}
	}
}

//evenement touche clavier relach?e
void NewOmniDriver::onKeyReleasedEvent(core::objectmodel::KeyreleasedEvent *kre)
{
	if (kre->getKey()=='X' || kre->getKey()=='x' )
	{
		modX=false;
	}
	if (kre->getKey()=='Y' || kre->getKey()=='y' )
	{
		modY=false;
	}
	if (kre->getKey()=='Z' || kre->getKey()=='z' )
	{
		modZ=false;
	}
	if (kre->getKey()=='Q' || kre->getKey()=='q' )
	{
		modS=false;
	}	
}


//boucle animation
void NewOmniDriver::onAnimateBeginEvent()
{//cout<<"\n\nANAS\n\n"<<"\n\n";
	// copy data->servoDeviceData to gDeviceData
	if(firstDevice)
		hdScheduleSynchronous(copyDeviceDataCallback, (void*) &autreOmniDriver, HD_MIN_SCHEDULER_PRIORITY);

	if (data.deviceData.ready)
	{
		data.deviceData.quat.normalize();
	
		// COMPUTATION OF THE vituralTool 6D POSITION IN THE World COORDINATES
        SolidTypes<double>::Transform baseOmni_H_endOmni(data.deviceData.pos*data.scale, data.deviceData.quat);

		Quat& orientB =(*orientationBase.beginEdit());
		Vec3d& posB =(*positionBase.beginEdit());
		orientB.normalize();
		data.world_H_baseOmni.set(posB,orientB);
		orientationBase.endEdit();
		positionBase.endEdit();	

		VecCoord& posD =(*posDevice.beginEdit());
		//posD.resize(11);

		SolidTypes<double>::Transform world_H_virtualTool = data.world_H_baseOmni * baseOmni_H_endOmni * data.endOmni_H_virtualTool;
		SolidTypes<double>::Transform tampon = data.world_H_baseOmni;

		sofa::helper::Quater<float> q;	

#if 1
		//get position base
		posD[0].getCenter() =  tampon.getOrigin();
		posD[0].getOrientation() =  tampon.getOrientation();
		//cout<<"\n\nANAS\n\n"<<posD[0].getOrientation()<<"\n\n";

		//get position stylus
		tampon*=baseOmni_H_endOmni;
		posD[1].getCenter() =  tampon.getOrigin();
		posD[1].getOrientation() =  tampon.getOrientation();
		//cout<<"\n\nANAS\n\n"<<posD[1].getOrientation()<<"\n\n";

		//get pos articulation 2
		sofa::helper::Quater<float> quarter2(Vec3d(0.0,0.0,1.0),angle2[2]);
		SolidTypes<double>::Transform transform_segr2(Vec3d(0.0,0.0,0.0),quarter2);
		tampon*=transform_segr2;
		posD[2].getCenter() =  tampon.getOrigin();
		posD[2].getOrientation() =  tampon.getOrientation();

		//get pos articulation 1
		sofa::helper::Quater<float> quarter3(Vec3d(1.0,0.0,0.0),angle2[1]);
		SolidTypes<double>::Transform transform_segr3(Vec3d(0.0,0.0,0.0),quarter3);
		tampon*=transform_segr3;
		posD[3].getCenter() =  tampon.getOrigin();
		posD[3].getOrientation() =  tampon.getOrientation();

		//get pos arm 2
		sofa::helper::Quater<float> quarter4(Vec3d(0.0,1.0,0.0),-angle2[0]);
		SolidTypes<double>::Transform transform_segr4(Vec3d(0.0,0.0,0.0),quarter4);
		tampon*=transform_segr4;
		posD[4].getCenter() =  tampon.getOrigin();
		posD[4].getOrientation() =  tampon.getOrientation();

		//get pos arm 1
		sofa::helper::Quater<float> quarter5(Vec3d(1.0,0.0,0.0),-(float)(pi/2)+angle1[2]-angle1[1]);
		SolidTypes<double>::Transform transform_segr5(Vec3d(0.0,13.33*data.scale/100,0.0),quarter5);
		tampon*=transform_segr5;
		posD[5].getCenter() =  tampon.getOrigin();
		posD[5].getOrientation() =  tampon.getOrientation();

		//get pos articulation 0
		sofa::helper::Quater<float> quarter6(Vec3d(1.0,0.0,0.0),angle1[1]);
		SolidTypes<double>::Transform transform_segr6(Vec3d(0.0,13.33*data.scale/100,0.0),quarter6);
		tampon*=transform_segr6;
		posD[6].getCenter() =  tampon.getOrigin();
		posD[6].getOrientation() =  tampon.getOrientation();

		//get pos base
		sofa::helper::Quater<float> quarter7(Vec3d(0.0,0.0,1.0),angle1[0]);
		SolidTypes<double>::Transform transform_segr7(Vec3d(0.0,0.0,0.0),quarter7);
		tampon*=transform_segr7;
		posD[7].getCenter() =  tampon.getOrigin();
		posD[7].getOrientation() =  tampon.getOrientation();
#else 

		q.clear();
		SolidTypes<double>::Transform transform_segr[6];		
		transform_segr[0].set(Vec3d(0.0,0.0,0.0),q);//get position base
		transform_segr[1].set(baseOmni_H_endOmni.getOrigin(),baseOmni_H_endOmni.getOrientation());//get position stylus
		transform_segr[2].set(Vec3d(0.0,0.0,0.0),q.axisToQuat(Vec3d(0.0,0.0,1.0),angle2[2]));//get pos articulation 2
		transform_segr[3].set(Vec3d(0.0,0.0,0.0),q.axisToQuat(Vec3d(1.0,0.0,0.0),angle2[1]));//get pos articulation 1
		transform_segr[4].set(Vec3d(0.0,0.0,0.0),q.axisToQuat(Vec3d(0.0,1.0,0.0),-angle2[0]));//get pos arm 2
		transform_segr[5].set(Vec3d(0.0,13.33*data.scale/100,0.0),q.axisToQuat(Vec3d(1.0,0.0,0.0),-(float)(pi/2)+angle1[2]-angle1[1]));//get pos arm 1
		transform_segr[6].set(Vec3d(0.0,13.33*data.scale/100,0.0),q.axisToQuat(Vec3d(1.0,0.0,0.0),angle1[1]));//get pos articulation 0
		transform_segr[7].set(Vec3d(0.0,0.0,0.0),q.axisToQuat(Vec3d(0.0,0.0,1.0),angle1[0]));//get pos base
		
		for(int i=0;i<8;i++)
		{
			tampon*=transform_segr[i];
			posD[i].getCenter() =  tampon.getOrigin();
			posD[i].getOrientation() =  tampon.getOrientation();
		}
#endif
		
		//get pos of axes

		posD[8].getCenter() =  data.world_H_baseOmni.getOrigin();
		posD[9].getCenter() =  data.world_H_baseOmni.getOrigin();
		posD[10].getCenter() =  data.world_H_baseOmni.getOrigin();
		posD[8].getOrientation() =  (data.world_H_baseOmni).getOrientation()*q.axisToQuat(Vec3d(0.0,0.0,1.0),(float)-pi/2);
		posD[9].getOrientation() =  (data.world_H_baseOmni).getOrientation()*q.axisToQuat(Vec3d(1.0,0.0,0.0),0);
		posD[10].getOrientation() = (data.world_H_baseOmni).getOrientation()*q.axisToQuat(Vec3d(1.0,0.0,0.0),(float)pi/2);

		posDevice.endEdit();

		if(DOFs!=NULL)
		{
				VecCoord& posS =*(DOFs->x0.beginEdit());

				posS[deviceIndex.getValue()].getCenter()=world_H_virtualTool.getOrigin();
				posS[deviceIndex.getValue()].getOrientation()=world_H_virtualTool.getOrientation();

				DOFs->x0.endEdit();
		}

		//button state
		Vec1d& openT = (*openTool.beginEdit());
		//cout<<"\n\nANASHD_DEVICE_BUTTON_1\n\n"<<HD_DEVICE_BUTTON_1<<"\n\n";
		//cout<<"\n\nANASdata.deviceData.m_buttonState\n\n"<<data.deviceData.m_buttonState<<"\n\n";
		if(data.deviceData.m_buttonState & HD_DEVICE_BUTTON_1)
		{
			if(openT[0]>minTool.getValue())
				openT[0]-=closeSpeedTool.getValue();
			else
				openT[0]=minTool.getValue();	 
		}
		else
		{
			if(openT[0]<maxTool.getValue())
				openT[0]+=openSpeedTool.getValue();
			else
				openT[0]=maxTool.getValue();
		}
		openTool.endEdit();

		// store actual position of interface for the forcefeedback (as it will be used as soon as new LCP will be computed)
        data.forceFeedback->setReferencePosition(world_H_virtualTool);
		//cout<<"\n\nANAS\n\n"<<world_H_virtualTool<<"\n\n";
		//cout<<"\n\nANAS\n\n"<<data.deviceData.id<<"\n\n";


		/// TODO : SHOULD INCLUDE VELOCITY !!
	}
	else
		std::cout<<"data not ready \n"<<std::endl;
}

//boucle qui se declanche si il y a un evenement
void NewOmniDriver::handleEvent(core::objectmodel::Event *event)
{
	if (dynamic_cast<sofa::simulation::AnimateBeginEvent *>(event))
	{
		core::objectmodel::BaseContext* scene = getContext();
		simulation::Node* groot = dynamic_cast<simulation::Node*>(scene);
		// Finding all the LaparoscopicRigid Objects (Instruments) in the graph
		std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > instruments;
		groot->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* > >(&instruments);
		
		//cout<<"\nAnas"<<autreOmniDriver[0]->data.servoDeviceData.m_buttonState;
		if(autreOmniDriver[0]->data.servoDeviceData.m_buttonState == 1 && ButtonFlag == 1)
		{
			ButtonFlag=0;
			if (!instruments.empty())
			{
				sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* instrument = instruments[0];
				/*if(instruments.size() > 1 && _chooseSecondInstrument == true)
					instrument = instruments[1];
				else
					instrument = instruments[0];*/
				
				// Finding all the Vec1d Objects in the graph
				std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > Vec1Objects;
				groot->getTreeObjects<sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>, std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* > >(&Vec1Objects);
				
				// Changing position of grasper jaws
				if (!Vec1Objects.empty())
				{
					for(std::vector< sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* >::const_iterator it= Vec1Objects.begin(); it!=Vec1Objects.end();it++)
					{
						sofa::component::container::MechanicalObject<sofa::defaulttype::Vec1dTypes>* temp = *it;
						sofa::helper::vector<double> k;
						if(graspClose == false)
							k.push_back(-0.1);
						else
							k.push_back(-0.5);					
						k.push_back(0.0);
						k.push_back(0.0);
						temp->forcePointPosition(0,k);
						if(graspClose == false)
						{
							k[0] = 0.1;
							graspClose = true;
							if(grasperActivated == true)
							{
//								gFunc->reactivateRay(getScene());
								for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecLeft.begin(); it != gFuncVecLeft.end(); it++)
								{
									sofa::gui::GraspFunction* temp = *it;
									temp->reactivateRay(groot);
								}
								for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecRight.begin(); it != gFuncVecRight.end(); it++){
									sofa::gui::GraspFunction* temp = *it;
									temp->reactivateRay(groot);
								}
							}
						}
						else
						{
							k[0] = 0.5;
							graspClose = false;
							if(grasperActivated == true)
							{
								//gFunc->deactivateRay();
								for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecLeft.begin(); it != gFuncVecLeft.end(); it++)
								{
									sofa::gui::GraspFunction* temp = *it;
									temp->deactivateRay();
								}
								for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecRight.begin(); it != gFuncVecRight.end(); it++)
								{
									sofa::gui::GraspFunction* temp = *it;
									temp->deactivateRay();
								}
							}
						}
						temp->forcePointPosition(1,k);
					}
				}
				// Activating the Grasper Rays if required
				if(graspClose == true )
				{
					if(grasperActivated == false)
					{
						for(int iter=0;iter<=17;iter++)
						{
							sofa::gui::GraspFunction* tempGFuncR = new sofa::gui::GraspFunction();
							tempGFuncR->init(groot);
							gFuncVecRight.push_back(tempGFuncR);

							sofa::gui::GraspFunction* tempGFuncL = new sofa::gui::GraspFunction();
							tempGFuncL->init(groot);
							gFuncVecLeft.push_back(tempGFuncL);

							graspInsideIndex[iter] = iter;
						}
						grasperActivated = true;
					}
				}
				
				// Updating the whole instrument node - Mechanical update as well as update of all the mappings
				sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
				sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
//				getQWidget()->update();

				sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* graspLeft = NULL;
				sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* graspRight = NULL;

				std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > Vec3Objects;
				groot->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > >(&Vec3Objects);
				
				if(!Vec3Objects.empty())
				{
					for(std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*>::const_iterator it=Vec3Objects.begin();it != Vec3Objects.end();it++){
						sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* temp = *it;
						if(temp->getName() == "CollisionL")
							graspLeft = temp;
						if(temp->getName() == "CollisionR")
							graspRight = temp;
					}
				}
				
				// Update Left Jaw Grasp Rays
				if(graspLeft != NULL && !gFuncVecLeft.empty() && graspClose == true){
					Vec3d position, direction;
					helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspLeftX = *graspLeft->read(core::VecCoordId::position());
								
					int itCount = 0;
					for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecLeft.begin(); it != gFuncVecLeft.end(); it++)
					{
						sofa::gui::GraspFunction* temp = *it;
						position =  graspLeftX[graspInsideIndex[itCount]];
						direction = Vec3d(0, -1, 0);
						temp->updateRay(position,direction);
						itCount++;
					}		
				}

				// Update Right Jaw Grasp Rays
				if(graspRight != NULL && !gFuncVecRight.empty() && graspClose == true)
				{
					Vec3d position, direction;
					helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspRightX = *graspRight->read(core::VecCoordId::position());
					
					int itCount = 0;
					for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecRight.begin(); it != gFuncVecRight.end(); it++)
					{
						sofa::gui::GraspFunction* temp = *it;
						position =  graspRightX[graspInsideIndex[itCount]];
						direction = Vec3d(0, 1, 0);
						temp->updateRay(position,direction);
						itCount++;
					}
				}
				if(graspClose == false)
				{
					gFuncVecRight.clear();
					gFuncVecLeft.clear();
					grasperActivated = false;
				}
}
} 
else if(autreOmniDriver[0]->data.servoDeviceData.m_buttonState != 1)
{
	ButtonFlag=1;
}
//Zohaib's Work for grasping START

//sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* cameraLap = NULL;

		sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* graspLeft = NULL;
		sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* graspRight = NULL;
		
				
		std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*> fCVec;
		
		std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > Vec3Objects;
		groot->getTreeObjects<sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>, std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* > >(&Vec3Objects);

		groot->getTreeObjects<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>, std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*>>(&fCVec);
		
		if(!Vec3Objects.empty()){
			for(std::vector< sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>*>::const_iterator it=Vec3Objects.begin();it != Vec3Objects.end();it++){
				sofa::core::behavior::MechanicalState<sofa::defaulttype::Vec3dTypes>* temp = *it;
				/*if(temp->getName() == "Camera")
					cameraLap = temp;*/
				if(temp->getName() == "CollisionL")
					graspLeft = temp;
				if(temp->getName() == "CollisionR")
					graspRight = temp;
			
			}
		}
		
		
		
		//Zohaib's Work for grasping END


		if (instruments.empty())
		{
			onAnimateBeginEvent();//ANAS=>original work
		}
		else
		{
		if(firstDevice)
			hdScheduleSynchronous(copyDeviceDataCallback, (void*) &autreOmniDriver, HD_MIN_SCHEDULER_PRIORITY);
		
		if (data.deviceData.ready)
		{
			data.deviceData.quat.normalize();
			
			// COMPUTATION OF THE vituralTool 6D POSITION IN THE World COORDINATES
			SolidTypes<double>::Transform baseOmni_H_endOmni(data.deviceData.pos*data.scale, data.deviceData.quat);
			
			Quat& orientB =(*orientationBase.beginEdit());
			Vec3d& posB =(*positionBase.beginEdit());
			orientB.normalize();
			data.world_H_baseOmni.set(posB,orientB);
			orientationBase.endEdit();
			positionBase.endEdit();
			
			VecCoord& posD =(*posDevice.beginEdit());
			
			SolidTypes<double>::Transform world_H_virtualTool = data.world_H_baseOmni * baseOmni_H_endOmni * data.endOmni_H_virtualTool;
			SolidTypes<double>::Transform tampon = data.world_H_baseOmni;
			
			sofa::helper::Quater<float> q;
			
			//get position base
			posD[0].getCenter() =  tampon.getOrigin();
			posD[0].getOrientation() =  tampon.getOrientation();
			//get position stylus
			tampon*=baseOmni_H_endOmni;
			posD[1].getCenter() =  tampon.getOrigin();
			posD[1].getOrientation() =  tampon.getOrientation();
			
			double eventX = posD[1].getCenter()[0];
			double eventY = posD[1].getCenter()[1];
			double eventZ = posD[1].getCenter()[2];

			double eventX1 = posD[1].getCenter()[0];//ANAS-WORK-SECOND-INSTRUMENT
			double eventY1 = posD[1].getCenter()[1];//ANAS-WORK-SECOND-INSTRUMENT
			double eventZ1 = posD[1].getCenter()[2];//ANAS-WORK-SECOND-INSTRUMENT
			
			if (!instruments.empty())
			{//////////ANAS WORK START
				//sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* instrument = instruments[0];//commented aas per zohaib's work
				sofa::core::behavior::MechanicalState<sofa::defaulttype::LaparoscopicRigidTypes>* instrument = instruments[0];//zohaib
				
				/*if(instruments.size() > 1 && _chooseSecondInstrument == true)//zohaib				
					instrument = instruments[1];//zohaib
				else//zohaib
					instrument = instruments[0];//zohaib
*/
				if(!tempFlag)
				{
					tempFlag = 1;
					mouseInteractorSavedPosX = eventX;
					mouseInteractorSavedPosY = eventY;
					mouseInteractorSavedPosZ = eventZ;
				}
				//////////ANAS WORK END
				helper::WriteAccessor<Data<sofa::defaulttype::LaparoscopicRigidTypes::VecCoord> > instrumentX = *instrument->write(core::VecCoordId::position());
				//zohaib's work start				
				
				/*if(cameraLap != NULL && sceneCameraCheck == false)
				{
					currentCamera -> p_position = Vector3(0,0,instrumentX[0].getTranslation());
					currentCamera->p_orientation = instrumentX[0].getOrientation();
					
					sceneCameraCheck = true;
				}*/
				
				//zohaib's work end
				//////////ANAS WORK START
				double dx = eventX - mouseInteractorSavedPosX;
				double dy = eventY - mouseInteractorSavedPosY;
				double dz = eventZ - mouseInteractorSavedPosZ;
				
				if (dx || dy || dz)
				{
					instrumentX[0].getOrientation() = tampon.getOrientation();
					instrumentX[0].getTranslation() = eventZ;
					//if(cameraLap != NULL && _chooseSecondInstrument == false)
					//{
					//	currentCamera->p_orientation = instrumentX[0].getOrientation();
					//	currentCamera->computeZ();

					//	const unsigned int heightViewport = currentCamera->p_heightViewport.getValue();
					//	double sceneRadius = 0.5*( currentCamera->p_maxBBox.getValue() - currentCamera->p_minBBox.getValue()).norm();
					//	double zoomStep = 250  *( 0.01*sceneRadius )/heightViewport; /*zoomspeed is 250*/
					//	double zoomDistance = eventZ;//dy*0.01;//CHECK ANAS
     //       
					//	Vector3 trans(0.0, 0.0, zoomDistance);
					//	trans = currentCamera->cameraToWorldTransform(trans);	// rotate the z-pointing vector to camera orientation
					//	currentCamera->translate(trans);	// move camera along new direction
					//	Vector3 newLookAt = currentCamera->cameraToWorldCoordinates(Vector3(0,0,-zoomStep));
					//	if (dot(currentCamera->getLookAt() - currentCamera->getPosition(), newLookAt - currentCamera->getPosition()) < 0)
					//	currentCamera->translateLookAt(newLookAt - currentCamera->getLookAt());
					//	currentCamera->getDistance(); // update distance between camera position and lookat
					//		
					//	currentCamera -> p_orientation = instrumentX[0].getOrientation();
					//	
					//	currentCamera->computeZ();
					//}
					mouseInteractorSavedPosX = eventX;
					mouseInteractorSavedPosY = eventY;
					mouseInteractorSavedPosZ = eventZ;
					bool gripped = false;
					bool tempGrip = false;
					// Update Left Jaw Grasp Rays
					if(graspLeft != NULL && !gFuncVecLeft.empty() != NULL && graspClose == true){
						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						//getQWidget()->update();
						Vec3d position, direction;
						helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspLeftX = *graspLeft->read(core::VecCoordId::position());
						
						int itCount = 0;
						for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecLeft.begin(); it != gFuncVecLeft.end(); it++){
							sofa::gui::GraspFunction* temp = *it;
							position =  graspLeftX[graspInsideIndex[itCount]];
							direction = Vec3d(0, -1, 0);
							tempGrip = temp->updateRay(position,direction);
							if(tempGrip == true)
								gripped = true;
							itCount++;
						}
						if(gripped == true)
						{
							breakLengthYaw = eventX;//(dy)*0.01;//check ANAS
							breakLengthTran = eventZ;//(dy)*0.01;//check ANAS
							breakLengthPitch =eventY;//(dx)*0.01;//check ANAS
							std::cout<<breakLengthPitch<<std::endl;
						}
					}

					// Update Right Jaw Grasp Rays
					if(graspRight != NULL && !gFuncVecRight.empty() != NULL && graspClose == true){
						sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
						sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
						//getQWidget()->update();
						Vec3d position, direction;
						helper::ReadAccessor<Data<sofa::defaulttype::Vec3dTypes::VecCoord>> graspRightX = *graspRight->read(core::VecCoordId::position());
						
						int itCount = 0;
						for(std::vector<sofa::gui::GraspFunction*>::const_iterator it = gFuncVecRight.begin(); it != gFuncVecRight.end(); it++){
							sofa::gui::GraspFunction* temp = *it;
							position =  graspRightX[graspInsideIndex[itCount]];
							direction = Vec3d(0, 1, 0);
							temp->updateRay(position,direction);
							itCount++;
							
						}			
						if(abs(breakLengthYaw) >= 3 || abs(breakLengthTran) >= 3 || abs(breakLengthPitch) >= 3){
							for(std::vector<sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>*>::const_iterator it = fCVec.begin(); it != fCVec.end(); it++)
							{
								sofa::component::projectiveconstraintset::FixedConstraint<defaulttype::Vec3dTypes>* temp = *it;
								if(temp->getName() == "FC1")
									temp->clearConstraints();
							}
								breakLengthYaw = 0;
								breakLengthTran = 0;
								breakLengthPitch = 0;
						}
					}
						//zohaib work part2 start
					
					
					
					//zohaib work part2 ends
					
				}//////////ANAS WORK ENDS
				
				sofa::simulation::MechanicalPropagatePositionAndVelocityVisitor(core::MechanicalParams::defaultInstance()).execute(instrument->getContext());
				sofa::simulation::UpdateMappingVisitor(core::ExecParams::defaultInstance()).execute(instrument->getContext());
			}//data.forceFeedback->setReferencePosition(world_H_virtualTool);//ANAS Checking Feed back	
		}
		}

}//ANAS=>Here original starts
	else
		if (dynamic_cast<core::objectmodel::KeypressedEvent *>(event))
		{
			//cout<<"\n\nANAS\n\n";
			core::objectmodel::KeypressedEvent *kpe = dynamic_cast<core::objectmodel::KeypressedEvent *>(event);
			onKeyPressedEvent(kpe);
		}
		else
			if (dynamic_cast<core::objectmodel::KeyreleasedEvent *>(event))
			{
				core::objectmodel::KeyreleasedEvent *kre = dynamic_cast<core::objectmodel::KeyreleasedEvent *>(event);
				onKeyReleasedEvent(kre);
			}//ANAS=>Here original ends
}

int NewOmniDriverClass = core::RegisterObject("Solver to test compliance computation for new articulated system objects")
.add< NewOmniDriver >()
.addAlias("OmniDriver");

SOFA_DECL_CLASS(NewOmniDriver)

} // namespace controller

} // namespace component

} // namespace sofa

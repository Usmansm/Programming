
#include <sofa/gui/InciseFunction.h>

#include <sofa/component/mapping/IdentityMapping.h>
#include <sofa/core/Mapping.h>

#include <sofa/component/collision/RayContact.h>

#include <sofa/simulation/common/MechanicalVisitor.h>
#include <sofa/simulation/common/PropagateEventVisitor.h>
#include <sofa/component/visualmodel/OglModel.h>

namespace sofa
{
namespace gui
{

void InciseFunction::init(simulation::Node* groot)
{
	interNode = groot->createChild("Incise");

	inciseMech= sofa::core::objectmodel::New<sofa::component::container::MechanicalObject< defaulttype::Vec3Types >>(); 
	inciseMech->resize(1);
	inciseMech->setName("ScissorPosition");
	interNode->addObject(inciseMech);

	inciseRay = sofa::core::objectmodel::New<sofa::component::collision::RayModel>();
	inciseRay->setNbRay(1);
	inciseRay->getRay(0).setL(1);
	inciseRay->setName("CutRay");
	interNode->addObject(inciseRay);

	interNode->init(sofa::core::ExecParams::defaultInstance());
	inciseMech->init();
	inciseRay->init();

	groot->addChild(interNode);

	nodeRayPick = interNode->createChild("Interaction");
			
	interMech = sofa::core::objectmodel::New< sofa::component::container::MechanicalObject< defaulttype::Vec3Types > >(); 
	interMech->resize(1);
	interMech->setName("interPosition");
	nodeRayPick->addObject(interMech);

	inciseInteractor = new sofa::component::collision::MouseInteractor< defaulttype::Vec3Types > ();
	inciseInteractor->setName("MouseInteractor");
	nodeRayPick->addObject(inciseInteractor);

	sofa::core::Mapping< defaulttype::Vec3Types, defaulttype::Vec3Types >::SPtr theMapping = sofa::core::objectmodel::New< sofa::component::mapping::IdentityMapping< defaulttype::Vec3Types, defaulttype::Vec3Types > >();
	theMapping->setModels(static_cast<sofa::component::container::MechanicalObject< defaulttype::Vec3Types >*> (inciseMech.get()), static_cast< sofa::component::container::MechanicalObject< defaulttype::Vec3Types >* >(interMech.get()));

	nodeRayPick->addObject(theMapping);

	theMapping->setNonMechanical();
	interMech->init();
	inciseInteractor->init();
	theMapping->init();

	interNode->addChild(nodeRayPick);

	incisePerformer = new sofa::component::collision::InciseAlongPathPerformer(inciseInteractor);
	incisePerformer->setIncisionMethod(0);
	//incisePerformer->setPerformerFreeze();

	result = sofa::component::collision::BodyPicked();

}

bool InciseFunction::updateRay(defaulttype::Vec3d position,defaulttype::Vec3d direction, bool first)
{
	inciseRay->getRay(0).setOrigin(position);
	inciseRay->getRay(0).setDirection(direction);
	
	simulation::MechanicalPropagatePositionVisitor(sofa::core::MechanicalParams::defaultInstance() /* PARAMS FIRST */, 0, sofa::core::VecCoordId::position(), true).execute(inciseRay->getContext());

	const double& maxLength = inciseRay->getRay(0).l();

	const std::set< sofa::component::collision::BaseRayContact*> &contacts = inciseRay->getContacts();
	for (std::set< sofa::component::collision::BaseRayContact*>::const_iterator it=contacts.begin(); it != contacts.end();++it)
	{

		const sofa::helper::vector<core::collision::DetectionOutput*>& output = (*it)->getDetectionOutputs();
		sofa::core::CollisionModel *modelInCollision;
		for (unsigned int i=0;i<output.size();++i)
		{
			if (output[i]->elem.first.getCollisionModel() == inciseRay)
			{
				modelInCollision = output[i]->elem.second.getCollisionModel();
				if (!modelInCollision->isSimulated()) continue;

				const double d = (output[i]->point[1]-position)*direction;
				if (d<0.0 || d>maxLength) continue;
				if (result.body == NULL || d < result.rayLength)
				{
					result.body=modelInCollision;
					result.indexCollisionElement = output[i]->elem.second.getIndex();
					result.point = output[i]->point[1];
					result.dist  = (output[i]->point[1]-output[i]->point[0]).norm();
					result.rayLength  = d;
				}
			}
			else if (output[i]->elem.second.getCollisionModel() == inciseRay)
			{
				modelInCollision = output[i]->elem.first.getCollisionModel();
				if (!modelInCollision->isSimulated()) continue;

				const double d = (output[i]->point[0]-position)*direction;
				if (d<0.0 || d>maxLength) continue;
				if (result.body == NULL || d < result.rayLength)
				{
					result.body=modelInCollision;
					result.indexCollisionElement = output[i]->elem.first.getIndex();
					result.point = output[i]->point[0];
					result.dist  = (output[i]->point[1]-output[i]->point[0]).norm();
					result.rayLength  = d;
				}
			}
		}
	}
	inciseInteractor->setMouseRayModel(inciseRay.get());
	inciseInteractor->setBodyPicked(result);
	
	if(result.body && first == true){
		//incisePerformer->setPerformerFreeze();
	
		incisePerformer->start();
		incisePerformer->execute();
		result.body = NULL;
		result = sofa::component::collision::BodyPicked();
		return true;
	}
	
	if(result.body && first == false){
		std::cout<<"Second Point "<<result.body->getName()<<" "<<result.indexCollisionElement<<std::endl;
		incisePerformer->start();
		incisePerformer->execute();
		/*sofa::simulation::Node* cutObjectNode = dynamic_cast<sofa::simulation::Node*>(result.body->getContext());
		sofa::simulation::Node* cutVisualNode = cutObjectNode->getChild("Visual");
		if(cutVisualNode != NULL){
			sofa::component::visualmodel::OglModel* visCutObject = cutVisualNode->get<sofa::component::visualmodel::OglModel>();
			visCutObject->handleTopologyChange();
		}*/
		delete incisePerformer;
		incisePerformer = new sofa::component::collision::InciseAlongPathPerformer(inciseInteractor);
		incisePerformer->setIncisionMethod(0);
		result.body = NULL;
		result = sofa::component::collision::BodyPicked();
		return true;
	
	}

	return false;
}

}
}

#include <sofa/gui/GraspFunction.h>

#include <sofa/component/mapping/IdentityMapping.h>
#include <sofa/core/Mapping.h>

#include <sofa/component/collision/RayContact.h>

#include <sofa/simulation/common/MechanicalVisitor.h>
#include <sofa/simulation/common/PropagateEventVisitor.h>

#include <boost/lexical_cast.hpp>

namespace sofa
{
namespace gui
{

void GraspFunction::init(simulation::Node* groot, int rayCount)
{
	std::string name = "Grasp"+ boost::lexical_cast<std::string>(rayCount );
	interNode = groot->createChild(name);

	graspMech= sofa::core::objectmodel::New<sofa::component::container::MechanicalObject< defaulttype::Vec3Types >>(); 
	graspMech->resize(1);
	graspMech->setName("GraspPosition");
	interNode->addObject(graspMech);

	graspRay = sofa::core::objectmodel::New<sofa::component::collision::RayModel>();
	graspRay->setNbRay(1);
	graspRay->getRay(0).setL(0.3);
	graspRay->setName("GraspRay");
	graspRay->setGroup(1);
	interNode->addObject(graspRay);

	interNode->init(sofa::core::ExecParams::defaultInstance());
	graspMech->init();
	graspRay->init();

	groot->addChild(interNode);

	nodeRayPick = interNode->createChild("Interaction");
			
	interMech = sofa::core::objectmodel::New< sofa::component::container::MechanicalObject< defaulttype::Vec3Types > >(); 
	interMech->resize(1);
	interMech->setName("interPosition");
	nodeRayPick->addObject(interMech);

	graspInteractor = new sofa::component::collision::MouseInteractor< defaulttype::Vec3Types > ();
	graspInteractor->setName("MouseInteractor");
	nodeRayPick->addObject(graspInteractor);

	sofa::core::Mapping< defaulttype::Vec3Types, defaulttype::Vec3Types >::SPtr theMapping = sofa::core::objectmodel::New< sofa::component::mapping::IdentityMapping< defaulttype::Vec3Types, defaulttype::Vec3Types > >();
	theMapping->setModels(static_cast<sofa::component::container::MechanicalObject< defaulttype::Vec3Types >*> (graspMech.get()), static_cast< sofa::component::container::MechanicalObject< defaulttype::Vec3Types >* >(interMech.get()));

	nodeRayPick->addObject(theMapping);

	theMapping->setNonMechanical();
	interMech->init();
	graspInteractor->init();
	theMapping->init();

	interNode->addChild(nodeRayPick);

	graspPerformer = new sofa::component::collision::AttachBodyPerformer<defaulttype::Vec3Types>(graspInteractor);

	graspInteractor->setMouseRayModel(graspRay.get());
	graspPerformer->setStiffness(100000);
}

bool GraspFunction::updateRay(defaulttype::Vec3d position,defaulttype::Vec3d direction)
{

	if(interNode->isActive() == true){
	graspRay->getRay(0).setOrigin(position);
	graspRay->getRay(0).setDirection(direction);
	
	simulation::MechanicalPropagatePositionVisitor(sofa::core::MechanicalParams::defaultInstance() /* PARAMS FIRST */, 0, sofa::core::VecCoordId::position(), true).execute(graspRay->getContext());

	const double& maxLength = graspRay->getRay(0).l();

	const std::set< sofa::component::collision::BaseRayContact*> &contacts = graspRay->getContacts();

	
	if(bodyPicked == false){
	for (std::set< sofa::component::collision::BaseRayContact*>::const_iterator it=contacts.begin(); it != contacts.end();++it)
	{

		const sofa::helper::vector<core::collision::DetectionOutput*>& output = (*it)->getDetectionOutputs();
		sofa::core::CollisionModel *modelInCollision;
		for (unsigned int i=0;i<output.size();++i)
		{
			if (output[i]->elem.first.getCollisionModel() == graspRay)
			{
				modelInCollision = output[i]->elem.second.getCollisionModel();
				if (!modelInCollision->isSimulated() || modelInCollision->getName() == "InstColl") continue;

				const double d = (output[i]->point[1]-position)*direction;
				if (d<0.0 || d>maxLength) continue;
				if (result.body == NULL || d < result.rayLength)
				{
					result.body=modelInCollision;
					result.indexCollisionElement = output[i]->elem.second.getIndex();
					result.point = output[i]->point[1];
					result.dist  = (output[i]->point[1]-output[i]->point[0]).norm();
					result.rayLength  = 0;
					bodyPicked = true;
					graspInteractor->setBodyPicked(result);
				}
			}
			else if (output[i]->elem.second.getCollisionModel() == graspRay)
			{
				modelInCollision = output[i]->elem.first.getCollisionModel();
				if (!modelInCollision->isSimulated() || modelInCollision->getName() == "InstColl") continue;

				const double d = (output[i]->point[0]-position)*direction;
				if (d<0.0 || d>maxLength) continue;
				if (result.body == NULL || d < result.rayLength)
				{
					result.body=modelInCollision;
					result.indexCollisionElement = output[i]->elem.first.getIndex();
					result.point = output[i]->point[0];
					result.dist  = (output[i]->point[1]-output[i]->point[0]).norm();
					result.rayLength  = 0;
					bodyPicked = true;
					graspInteractor->setBodyPicked(result);
				}
			}
		}
	}
	graspPerformer->start();
	}

	
	graspPerformer->execute();
	}
	else 
		bodyPicked = false;

	return bodyPicked;

}

void GraspFunction::deactivateRay()
{
	graspPerformer->clear();
//	interNode->detachFromGraph();
//	nodeRayPick->detachFromGraph();
	nodeRayPick->setActive(false);
	interNode->setActive(false);
	bodyPicked = false;
	result = sofa::component::collision::BodyPicked();
	graspInteractor->setBodyPicked(result);
}

void GraspFunction::reactivateRay(simulation::Node* groot)
{
	interNode->setActive(true);
	nodeRayPick->setActive(true);
	//groot->addChild(interNode);
	//interNode->addChild(nodeRayPick);
}

std::string GraspFunction::getBodyName()
{
	if(result.body)
		return result.body->getName();
	else
		return NULL;
}



}
}
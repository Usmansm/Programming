
#include <sofa/gui/BurnFunction.h>

#include <sofa/component/mapping/IdentityMapping.h>
#include <sofa/core/Mapping.h>

#include <sofa/component/collision/RayContact.h>

#include <sofa/simulation/common/MechanicalVisitor.h>
#include <sofa/simulation/common/PropagateEventVisitor.h>

#include <sofa/component/misc/ParticleSource.h>
#include <sofa/component/topology/TetrahedronSetTopologyModifier.h>

namespace sofa
{
namespace gui
{

void BurnFunction::init(simulation::Node* groot)
{
	interNode = groot->createChild("Burn");
	rootNode = groot;

	burnMech= sofa::core::objectmodel::New<sofa::component::container::MechanicalObject< defaulttype::Vec3Types >>(); 
	burnMech->resize(1);
	burnMech->setName("BurnPosition");
	interNode->addObject(burnMech);

	burnRay = sofa::core::objectmodel::New<sofa::component::collision::RayModel>();
	burnRay->setNbRay(1);
	burnRay->getRay(0).setL(0.5);
	burnRay->setName("BurnRay");
	interNode->addObject(burnRay);

	interNode->init(sofa::core::ExecParams::defaultInstance());
	burnMech->init();
	burnRay->init();

	groot->addChild(interNode);

	nodeRayPick = interNode->createChild("Interaction");
			
	interMech = sofa::core::objectmodel::New< sofa::component::container::MechanicalObject< defaulttype::Vec3Types > >(); 
	interMech->resize(1);
	interMech->setName("interPosition");
	nodeRayPick->addObject(interMech);

	burnInteractor = new sofa::component::collision::MouseInteractor< defaulttype::Vec3Types > ();
	burnInteractor->setName("MouseInteractor");
	nodeRayPick->addObject(burnInteractor);

	sofa::core::Mapping< defaulttype::Vec3Types, defaulttype::Vec3Types >::SPtr theMapping = sofa::core::objectmodel::New< sofa::component::mapping::IdentityMapping< defaulttype::Vec3Types, defaulttype::Vec3Types > >();
	theMapping->setModels(static_cast<sofa::component::container::MechanicalObject< defaulttype::Vec3Types >*> (burnMech.get()), static_cast< sofa::component::container::MechanicalObject< defaulttype::Vec3Types >* >(interMech.get()));

	nodeRayPick->addObject(theMapping);

	theMapping->setNonMechanical();
	interMech->init();
	burnInteractor->init();
	theMapping->init();

	interNode->addChild(nodeRayPick);

	burnPerformer = new sofa::component::collision::RemovePrimitivePerformer<defaulttype::Vec3Types>(burnInteractor);

}

void BurnFunction::updateRay(defaulttype::Vec3d position,defaulttype::Vec3d direction)
{

	burnRay->getRay(0).setOrigin(position);
	burnRay->getRay(0).setDirection(direction);
	
	simulation::MechanicalPropagatePositionVisitor(sofa::core::MechanicalParams::defaultInstance() /* PARAMS FIRST */, 0, sofa::core::VecCoordId::position(), true).execute(burnRay->getContext());

	const double& maxLength = burnRay->getRay(0).l();
	
	
	sofa::component::collision::BodyPicked result = sofa::component::collision::BodyPicked();;
	bodyPicked = false;
	const std::set< sofa::component::collision::BaseRayContact*> &contacts = burnRay->getContacts();
	for (std::set< sofa::component::collision::BaseRayContact*>::const_iterator it=contacts.begin(); it != contacts.end();++it)
	{

		const sofa::helper::vector<core::collision::DetectionOutput*>& output = (*it)->getDetectionOutputs();
		sofa::core::CollisionModel *modelInCollision;
		for (unsigned int i=0;i<output.size();++i)
		{
			if (output[i]->elem.first.getCollisionModel() == burnRay)
			{
				modelInCollision = output[i]->elem.second.getCollisionModel();
				if (!modelInCollision->isSimulated() || modelInCollision->getName() == "TipColl") continue;

				const double d = (output[i]->point[1]-position)*direction;
				if (d<0.0 || d>maxLength) continue;
				if (result.body == NULL || d < result.rayLength)
				{
					result.body=modelInCollision;
					result.indexCollisionElement = output[i]->elem.second.getIndex();
					result.point = output[i]->point[1];
					result.dist  = (output[i]->point[1]-output[i]->point[0]).norm();
					result.rayLength  = d;
					bodyPicked = true;
				}
			}
			else if (output[i]->elem.second.getCollisionModel() == burnRay)
			{
				modelInCollision = output[i]->elem.first.getCollisionModel();
				if (!modelInCollision->isSimulated() || modelInCollision->getName() == "TipColl") continue;

				const double d = (output[i]->point[0]-position)*direction;
				if (d<0.0 || d>maxLength) continue;
				if (result.body == NULL || d < result.rayLength)
				{
					result.body=modelInCollision;
					result.indexCollisionElement = output[i]->elem.first.getIndex();
					result.point = output[i]->point[0];
					result.dist  = (output[i]->point[1]-output[i]->point[0]).norm();
					result.rayLength  = d;
					bodyPicked = true;
				}
			}
		}
	}
	burnInteractor->setMouseRayModel(burnRay.get());
	burnInteractor->setBodyPicked(result);

	burnPerformer->execute();

	if(result.body){
		sofa::component::topology::TetrahedronSetTopologyModifier* topMod = NULL;
		topMod = result.body->getContext()->get<sofa::component::topology::TetrahedronSetTopologyModifier>();
		if(topMod != NULL)
			startSmoke();
	}

}

void BurnFunction::deactivateRay()
{
	interNode->detachFromGraph();
	nodeRayPick->detachFromGraph();
}

void BurnFunction::reactivateRay(simulation::Node* groot)
{
	groot->addChild(interNode);
	interNode->addChild(nodeRayPick);
}

void BurnFunction::startSmoke()
{			
	sofa::component::misc::ParticleSource<sofa::defaulttype::Vec3dTypes>* source = NULL;
	sofa::simulation::Node* smokeNode = NULL;
	smokeNode = rootNode->getChild("Fluid");

	if(smokeNode != NULL){
		source = smokeNode->getNodeObject<sofa::component::misc::ParticleSource<sofa::defaulttype::Vec3dTypes>>();
	}

	if(source != NULL){
		if(source->f_start.getValue() != source->getTime()){
			source->f_start.beginEdit();
			source -> f_start.setValue(source->getTime());
			source->f_start.endEdit();
			source->f_stop.beginEdit();
			source -> f_stop.setValue((source->getTime())+(rootNode->getDt()*2));
			source->f_stop.endEdit();
			std::cerr<<source->getTime()<<std::endl;
			std::cerr<<(source->getTime()+(rootNode->getDt()*2))<<std::endl;
			source -> init();
		}
		
		/*source->f_start.beginEdit();
		source -> f_start.setValue(source->getTime());
		source->f_start.endEdit();
		source->f_stop.beginEdit();
		source -> f_stop.setValue((source->getTime())+(rootNode->getDt()*2));
		source->f_stop.endEdit();*/
		
	}
}

}
}
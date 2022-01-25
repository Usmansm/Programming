
#ifndef SOFA_GUI_BURNFUNCTION_H
#define SOFA_GUI_BURNFUNCTION_H

#include "C:\\SOFA\\applications\\sofa\\gui\\SofaGUI.h"


#include <sofa/simulation/common/Simulation.h>
#include <sofa/simulation/common/Node.h>

#include <sofa/component/container/MechanicalObject.h>

#include <sofa/component/collision/RayModel.h>
#include <sofa/component/collision/MouseInteractor.h>

#include <sofa/component/collision/RemovePrimitivePerformer.h>

namespace sofa
{
	namespace gui
	{
		class SOFA_SOFAGUI_API BurnFunction
		{
		public:
			sofa::component::collision::RayModel::SPtr burnRay;
			sofa::component::collision::BaseMouseInteractor* burnInteractor;
			sofa::component::collision::RemovePrimitivePerformer<defaulttype::Vec3Types> * burnPerformer;

			sofa::component::container::MechanicalObject< defaulttype::Vec3Types >::SPtr burnMech;
			sofa::core::behavior::BaseMechanicalState::SPtr interMech;

			simulation::Node::SPtr interNode;
			simulation::Node::SPtr nodeRayPick;

			simulation::Node::SPtr rootNode;
			bool bodyPicked;

			BurnFunction(){bodyPicked = false;}
			~BurnFunction(){}

			void init(simulation::Node* groot);

			void updateRay(defaulttype::Vec3d position,defaulttype::Vec3d direction);

			void deactivateRay();

			void reactivateRay(simulation::Node* groot);

			void startSmoke();

		};
	}
}

#endif
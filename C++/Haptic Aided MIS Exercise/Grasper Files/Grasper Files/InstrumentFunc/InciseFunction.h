
#ifndef SOFA_GUI_INCISEFUNCTION_H
#define SOFA_GUI_INCISEUNCTION_H

#include "C:\\SOFA\\applications\\sofa\\gui\\SofaGUI.h"


#include <sofa/simulation/common/Simulation.h>
#include <sofa/simulation/common/Node.h>

#include <sofa/component/container/MechanicalObject.h>

#include <sofa/component/collision/RayModel.h>
#include <sofa/component/collision/MouseInteractor.h>

#include <sofa/component/collision/InciseAlongPathPerformer.h>


namespace sofa
{
	namespace gui
	{
		class SOFA_SOFAGUI_API InciseFunction
		{
		public:
			sofa::component::collision::RayModel::SPtr inciseRay;
			sofa::component::collision::BaseMouseInteractor* inciseInteractor;
			sofa::component::collision::InciseAlongPathPerformer* incisePerformer;

			sofa::component::container::MechanicalObject< defaulttype::Vec3Types >::SPtr inciseMech;
			sofa::core::behavior::BaseMechanicalState::SPtr interMech;

			simulation::Node::SPtr interNode;
			simulation::Node::SPtr nodeRayPick;

			sofa::component::collision::BodyPicked result;

			unsigned int previousIndex;
					
			InciseFunction(){}
			~InciseFunction(){}

			void init(simulation::Node* groot);

			bool updateRay(defaulttype::Vec3d position, defaulttype::Vec3d direction, bool first);

		};
	}
}

#endif
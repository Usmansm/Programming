
#ifndef SOFA_GUI_GRASPFUNCTION_H
#define SOFA_GUI_GRASPFUNCTION_H

#include "SofaGUI.h"


#include <sofa/simulation/common/Simulation.h>
#include <sofa/simulation/common/Node.h>

#include <sofa/component/container/MechanicalObject.h>

#include <sofa/component/collision/RayModel.h>
#include <sofa/component/collision/MouseInteractor.h>

#include <sofa/component/collision/AttachBodyPerformer.h>


namespace sofa
{
	namespace gui
	{
		class SOFA_SOFAGUI_API GraspFunction
		{
		public:
			sofa::component::collision::RayModel::SPtr graspRay;
			sofa::component::collision::BaseMouseInteractor* graspInteractor;
			sofa::component::collision::AttachBodyPerformer<defaulttype::Vec3Types> * graspPerformer;

			sofa::component::container::MechanicalObject< defaulttype::Vec3Types >::SPtr graspMech;
			sofa::core::behavior::BaseMechanicalState::SPtr interMech;

			simulation::Node::SPtr interNode;
			simulation::Node::SPtr nodeRayPick;

			
			sofa::component::collision::BodyPicked result;
			bool bodyPicked;

			GraspFunction()
			{
				bodyPicked = false;
			}
			~GraspFunction(){}

			void init(simulation::Node* groot);

			bool updateRay(defaulttype::Vec3d position,defaulttype::Vec3d direction);

			void deactivateRay();

			void reactivateRay(simulation::Node* groot);

			std::string getBodyName();

		};
	}
}

#endif
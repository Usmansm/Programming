<?php
/*
@OPENEMIS SCHOOL LICENSE LAST UPDATED ON 2014-01-30

OpenEMIS School
Open School Management Information System

Copyright © 2014 KORD IT. This program is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by the Free Software Foundation, 
either version 3 of the License, or any later version. This program is distributed in the hope 
that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should 
have received a copy of the GNU General Public License along with this program.  If not, see 
<http://www.gnu.org/licenses/>.  For more information please email contact@openemis.org.
*/

App::uses('AppModel', 'Model');

class Student extends AppModel {
	public $displayField = 'student_no';
	public $belongsTo = array(
		'SecurityUser',
		'StudentStatus',
		'ModifiedUser' => array(
			'className' => 'SecurityUser',
			'fields' => array('first_name', 'last_name'),
			'foreignKey' => 'modified_user_id',
			'type' => 'LEFT'
		),
		'CreatedUser' => array(
			'className' => 'SecurityUser',
			'fields' => array('first_name', 'last_name'),
			'foreignKey' => 'created_user_id',
			'type' => 'LEFT'
		)
	);

	public $hasMany = array(
		'AssessmentResult', 
		'AssessmentItemResult', 
		'ClassStudent', 
		'StudentGuardian', 
		'StudentBehaviour', 
		'StudentProfileImage',
		'StudentIdentity',
		'StudentCustomField',
		'StudentCustomValue');

	public $actsAs = array(
		'ControllerAction',
		'Export' => array('module' => 'Student')
	);

    public function __construct() {
        parent::__construct();

        $this->validate = array(
            'student_no' => array(
                'ruleRequired' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'message' => $this->getErrorMessage('studentNo')
                )
            ),
            'student_status_id' => array(
                'ruleRequired' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'message' => $this->getErrorMessage('studentNo')
                )
            ),
            'start_date' => array(
                'ruleRequired' => array(
                    'rule'       => 'date',
                    'allowEmpty' => false,
                    'required' => true,
                    'message'    => $this->getErrorMessage('startDate')
                )
            )
        );
    }
	
	public function getFields($options=array()) {
		$currentCustomField = $this->alias.'CustomField';
		$currentCustomValue = $this->alias.'CustomValue';

		parent::getFields();
		$user = $this->SecurityUser->getFields();
		$order = 1;
		
		$this->setField('first_name', $user, $order++);
		$this->setField('middle_name', $user, $order++);
		$this->setField('last_name', $user, $order++);
		$this->setField('date_of_birth', $user, $order++);
		$this->setField('photo_content', $user, $order++);
		$this->setField('country_id', $user, $order++);
		$this->setField('identification_no', $user, $order++);
		$this->setField('gender', $user, $order++);
		$this->setField('address', $user, $order++);
		$this->setField('postal_code', $user, $order++);
		$this->setFieldOrder('student_no', 1);
		$this->fields['photo_content']['type'] = 'image';
		$this->fields['photo_content']['visible'] = array('edit' => true);
		$this->fields['security_user_id']['type'] = 'hidden';
		$this->fields['start_year']['type'] = 'hidden';
		$this->fields['gender']['type'] = 'select';
		$this->fields['gender']['options'] = $this->getGenderOptions();
		$this->fields['student_status_id']['type'] = 'select';
		$this->fields['student_status_id']['options'] = $this->StudentStatus->getOptions('name', 'order', 'asc', array('visible'=>1));

		// $this->fields[$currentCustomField] = $this->$currentCustomField->getCustomFields();

		// $valueModel = $this->alias.'CustomValue';
		// if ($this->$valueModel->validator()->offsetExists($this->alias.'CustomValue')) {
		// 	$this->$valueModel->validator()->getField($this->alias.'CustomValue')->setRule('required', array(
		// 		'rule' => 'required',
		// 		'required' => true
		// 	));
		// } else {
		// 	$this->$valueModel->validator()->add($this->alias.'CustomValue', 'required', array(
		// 		'rule' => 'notEmpty',
		// 		'required' => 'create'
		// 	));
		// }

		return $this->fields;
	}

	public function reportGetFieldNames() {
		$rawFields = $this->getFields();
		return $this->getFieldNamesFromFields($rawFields);
	}

	public function reportGetData() {
		$currentModel = 'Student';
		$rawFields = $this->getFields();
		$conditions = array();
		App::uses('CakeSession', 'Model/Datasource');
		if (CakeSession::check($currentModel.'.search.conditions')) {
			$sessionConditions = CakeSession::read($currentModel.'.search.conditions');
			$conditions = $this->paginateConditions($sessionConditions);
		}
		
		$order = array();
		if (CakeSession::read($currentModel.'.search.sort.processedOrder')) {
			$order = CakeSession::read($currentModel.'.search.sort.processedOrder');
		}
		$data = $this->find(
			'all',
			array(
				'recursive' => 0,
				'conditions' => $conditions,
				'order' => $order
			)
		);
		$data = $this->handleOptionsInData($rawFields,$data);

		return $data;
	}

	public function getStudentData($id) {
		$data = $this->findById($id);
		$additionalData = $this->StudentCustomField->getCustomFieldValues(array('id'=>$id));
		$data[$this->alias.'CustomValue'] = $additionalData;

		return $data;
	}

	public function getStudentIdList($type='student_no', $order='DESC') {
		$value = 'Student.' . $type;
		$result = $this->find('list', array(
			'fields' => array('Student.id', $value),
			'order' => array($value . ' ' . $order)
		));
		return $result;
	}

	public function getStudentList() {
		$data = $this->find('all', array(
			'fields' => array('Student.id', 'Student.student_no', 'SecurityUser.first_name', 'SecurityUser.last_name'),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'security_users',
					'alias' => 'SecurityUser',
					'conditions' => array('SecurityUser.id = Student.security_user_id')
				)
			),
			'order' => array('SecurityUser.first_name')
		));

		$list = array();
		foreach($data as $obj) {
			$id = $obj['Student']['id'];
			$student_no = $obj['Student']['student_no'];
			$first_name = $obj['SecurityUser']['first_name'];
			$last_name = $obj['SecurityUser']['last_name'];
			$list[$id] = sprintf('%s - %s, %s', $student_no, $first_name, $last_name);
		}
		return $list;
	}
	
	public function getAcademicByStudentId() {
		
	}

	public function getStudentIdBySecurityId($securityId) {
		$data = $this->find('first', 
			array(
				'fields' => array('SecurityUser.id'),
				'conditions' => array(
					'SecurityUser.id' => $securityId
				)
			)
		);
		$studentId = null;
		if (!empty($data)) {
			$studentId = $data['Student']['id'];
		} else {
			$studentId = null;
		}
		return $studentId;
	}
	
	public function autocomplete($search) {
		$search = sprintf('%%%s%%', $search);
		$list = $this->find('all', array(
			'recursive' => -1,
			'fields' => array('Student.id', 'SecurityUser.first_name', 'SecurityUser.last_name', 'SecurityUser.identification_no'),
			'joins' => array(
				array(
					'table' => 'security_users',
					'alias' => 'SecurityUser',
					'conditions' => array('SecurityUser.id = Student.security_user_id')
				)
			),
			'conditions' => array(
				'OR' => array(
					'SecurityUser.first_name LIKE' => $search,
					'SecurityUser.last_name LIKE' => $search,
					'SecurityUser.identification_no LIKE' => $search
				)
			),
			'order' => array('SecurityUser.identification_no', 'SecurityUser.first_name', 'SecurityUser.last_name')
		));

		
		$data = array();
		
		foreach($list as $obj) {
			$studentId = $obj['Student']['id'];
			$identification = $obj['SecurityUser']['identification_no'];
			$firstName = $obj['SecurityUser']['first_name'];
			$lastName = $obj['SecurityUser']['last_name'];
			
			$data[] = array(
				'label' => trim(sprintf('%s - %s %s', $identification, $firstName, $lastName)),
				'value' => array('student-id' => $studentId, 'identification-no' => $identification,'first-name' => $firstName, 'last-name' => $lastName)
			);
		}

		return $data;
	}
	
	public function paginateJoins($joins, $params) {
		
		return $joins;
	}
	
	public function paginateConditions($params) {
		$conditions = array('OR' => array());
		foreach($params as $model => $values) {
			foreach($values as $name => $val) {
				if(!empty($val)) {
					$key = $model.'.'.$name;
					if($this->endsWith($name, '_id')) {
						$conditions[$key] = $val;
					} else {
						$key .= ' LIKE';
						$conditions['OR'][$key] = '%' . $val . '%';
					}
				}
			}
		}

		// if this is guardian view... should add a condition
		App::uses('CakeSession', 'Model/Datasource');
		if (CakeSession::check('Security.accessViewType')) {
			$accessViewType = CakeSession::read('Security.accessViewType');
		} else {
			// maybe want to kill the operation as the person is an unidentified user
			die();
		}

		$accessConditions = array();
		switch($accessViewType) {
			case 4:
				$StudentGuardian = ClassRegistry::init('StudentGuardian');
				$studentGuardian = $StudentGuardian->find(
					'list',
					array(
						'recursive' => -1,
						'fields' => array(
							'StudentGuardian.student_id'
						),
						'conditions' => array(
							'StudentGuardian.security_user_id' => AuthComponent::user('id')
						)
					)
				);
				$accessConditions = array('Student.id' => $studentGuardian);
				break;
			case 2:
				// teacher view
				$Staff = ClassRegistry::init('Staff');
				$staffId = $Staff->getStaffIdBySecurityId(AuthComponent::user('id'));

				$ClassTeacher = ClassRegistry::init('ClassTeacher');
				$classIdArray = $ClassTeacher->getClassesByStaffId($staffId);

				$ClassStudent = ClassRegistry::init('ClassStudent');
				$studentsInClasses = $ClassStudent->find(
					'list',
					array(
						'fields' => array('ClassStudent.student_id'),
						'conditions' => array(
							'ClassStudent.class_id' => $classIdArray
						)
					)
				);
				$accessConditions = array('Student.id' => $studentsInClasses);
				break;
			default:

				break;
		}

		$conditions = array_merge($conditions, $accessConditions);
		
		return $conditions;
	}
	
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		$model = $this->name;
		$fields = array(
			$model.'.id',
			$model.'.student_no',
			'SecurityUser.first_name',
			'SecurityUser.middle_name',
			'SecurityUser.last_name',
			'StudentStatus.name'
		);
		$joins = array();

		$data = $this->find('all', array(
			'recursive' => 0,
			'fields' => $fields,
			'joins' => $this->paginateJoins($joins, $conditions),
			'conditions' => $this->paginateConditions($conditions),
			'limit' => $limit,
			'offset' => (($page-1)*$limit),
			'group' => null,
			'order' => $order
		));

		foreach ($data as $key => $val) {
			$data[$key]['SecurityUser']['full_name'] = $this->Message->getFullName($val);
		}
		return $data;
	}
	
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$joins = array();
		$count = $this->find('count', array(
			'recursive' => 0,
			'joins' => $this->paginateJoins($joins, $conditions),
			'conditions' => $this->paginateConditions($conditions)
		));
		return $count;
	}
}
?>

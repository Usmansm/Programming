<?php
/*
@OPENEMIS SCHOOL LICENSE LAST UPDATED ON 2014-01-30

OpenEMIS School
Open School Management Information System

Copyright � 2014 KORD IT. This program is free software: you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by the Free Software Foundation, 
either version 3 of the License, or any later version. This program is distributed in the hope 
that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should 
have received a copy of the GNU General Public License along with this program.  If not, see 
<http://www.gnu.org/licenses/>.  For more information please email contact@openemis.org.
*/

App::uses('AppModel', 'Model');

class Staff extends AppModel {
	public $displayField = 'staff_no';
	public $useTable = "staff";
	public $belongsTo = array(
		'SecurityUser',
		'StaffStatus',
		'StaffCategory',
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
	public $hasMany = array('ClassLesson', 'ClassTeacher');

	public $actsAs = array(
		'ControllerAction',
		'Export' => array('module' => 'Staff')
	);

    public function __construct() {
        parent::__construct();

        $this->validate = array(
            'staff_no' => array(
                'ruleRequired' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'message' => $this->getErrorMessage('staffNo')
                )
            ),
            'start_date' => array(
                'ruleRequired' => array(
                    'rule'       => 'date',
                    'allowEmpty' => false,
                    'required' => true,
                    'message'    => $this->getErrorMessage('startDate'),
                )
            ),
            'staff_category_id' => array(
                'ruleRequired' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'message' => $this->getErrorMessage('category')
                )
            ),
            'staff_status_id' => array(
                'ruleRequired' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'message' => $this->getErrorMessage('status')
                )
            ),
            'type' => array(
                'ruleRequired' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'message' => $this->getErrorMessage('type')
                )
            )
        );
    }

    public function getStaffIdBySecurityId($securityId) {
		$data = $this->find('first', 
			array(
				'fields' => array('SecurityUser.id'),
				'conditions' => array(
					'SecurityUser.id' => $securityId
				)
			)
		);
		$staffId = null;
		if (!empty($data)) {
			$staffId = $data['Staff']['id'];
		} else {
			$staffId = null;
		}
		return $staffId;
	}

    public function getFields($options=array()) {
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
		$this->setFieldOrder('staff_no', 1);
		$this->fields['photo_content']['type'] = 'image';
		$this->fields['photo_content']['visible'] = array('edit' => true);
		$this->fields['security_user_id']['type'] = 'hidden';
		$this->fields['start_year']['type'] = 'hidden';
		$this->fields['gender']['type'] = 'select';
		$this->fields['gender']['options'] = $this->getGenderOptions();
		$this->fields['type']['type'] = 'select';
		$this->fields['type']['options'] = $this->getStaffTypeOptions();
		$this->fields['staff_status_id']['type'] = 'select';
		$this->fields['staff_status_id']['options'] = $this->StaffStatus->getOptions('name', 'order', 'asc', array('visible'=>1));
		$this->fields['staff_category_id']['type'] = 'select';
		$this->fields['staff_category_id']['options'] = $this->StaffCategory->getOptions('name', 'order', 'asc', array('visible'=>1));
		return $this->fields;
	}

	public function reportGetFieldNames() {
		$rawFields = $this->getFields();
		return $this->getFieldNamesFromFields($rawFields);
	}

	public function reportGetData() {
		$currentModel = 'Staff';
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

	public function getClassStudentIdsByStaffId() {
		$staffId = $this->getStaffIdBySecurityId(AuthComponent::user('id'));
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
		return $studentsInClasses;
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
		return $conditions;
	}
	
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		$model = $this->name;
		$fields = array(
			$model.'.*',
			'SecurityUser.first_name',
			'SecurityUser.middle_name',
			'SecurityUser.last_name',
			'StaffStatus.name'
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

	public function getStaffList($type = 'list', $options=array()) {
		$conditions = (array_key_exists('conditions', $options)) ? $options['conditions'] : null;

		$data = $this->find('all', array(
			'fields' => array('Staff.id', 'Staff.staff_no', 'SecurityUser.first_name', 'SecurityUser.middle_name', 'SecurityUser.last_name'),
			'recursive' => -1,
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'security_users',
					'alias' => 'SecurityUser',
					'conditions' => array('SecurityUser.id = Staff.security_user_id')
				)
			),
			'order' => array('SecurityUser.first_name')
		));

		if($type == 'list'){
			$list = array();
			foreach($data as $obj) {
				$id = $obj['Staff']['id'];
				$staff_no = $obj['Staff']['staff_no'];
				$full_name = $this->Message->getFullName($obj);

				$list[$id] = sprintf('%s - %s', $staff_no, $full_name);
			}
			return $list;
		}
		else{
			return $data;	
		}
	}

	public function autocomplete($search) {
		$search = sprintf('%%%s%%', $search);
		$list = $this->find('all', array(
			'recursive' => -1,
			'fields' => array('Staff.id', 'Staff.staff_no', 'SecurityUser.first_name', 'SecurityUser.last_name', 'SecurityUser.identification_no'),
			'joins' => array(
				array(
					'table' => 'security_users',
					'alias' => 'SecurityUser',
					'conditions' => array('SecurityUser.id = Staff.security_user_id')
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
			$staffId = $obj['Staff']['id'];
			$staffNo = $obj['Staff']['staff_no'];
			$identification = $obj['SecurityUser']['identification_no'];
			$firstName = $obj['SecurityUser']['first_name'];
			$lastName = $obj['SecurityUser']['last_name'];
			
			$data[] = array(
				'label' => trim(sprintf('%s - %s %s', $identification, $firstName, $lastName)),
				'value' => array('staff-id' => $staffId, 'staff-no' => $staffNo, 'identification-no' => $identification,'first-name' => $firstName, 'last-name' => $lastName)
			);
		}

		return $data;
	}


	public function getSelectedStaff($id, $mode = 'min'){
		$options['recursive'] = -1;
		if($mode == 'full'){
			$options['fields'] = array('Staff.*', 'SecurityUser.*');
		}else{
			$options['fields'] = array('Staff.id', 'SecurityUser.first_name', 'SecurityUser.last_name', 'Staff.staff_no');
		}
		$options['joins'] = array(
								array(
									'table' => 'security_users',
									'alias' => 'SecurityUser',
									'conditions' => array('SecurityUser.id = Staff.security_user_id')
								)
							);
		$options['conditions'] = array(
									'Staff.id' => $id
								);
		
		$data = $this->find('first', $options);	
		
		return $data;
	}
}
?>
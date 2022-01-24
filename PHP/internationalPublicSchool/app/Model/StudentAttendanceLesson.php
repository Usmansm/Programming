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
/**
 * Attendance Model
 *
 * @property Class $Class
 */
class StudentAttendanceLesson extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';

	//Page
	public $actsAs = array('ControllerAction');
	
	public function lesson($controller, $params){
		$className = $controller->Session->read('Class.name');
		$classId = $controller->Session->read('Class.id');
		
		$ClassSubject = ClassRegistry::init('ClassSubject');
		$subjectsOptions = $ClassSubject->getSubjectByClass($classId,'list');
		
		if(!empty($subjectsOptions)){//pr('No Subject');die;
			//$controller->redirect(array('action' => 'index'));
                    
                    $selectedSubject = empty($subjectsOptions)? 0: key($subjectsOptions);
                    $selectedSubject = empty($params['pass'][0])? $selectedSubject: $params['pass'][0];
                    
                    $selectedDate = !empty($params['pass'][1])?$params['pass'][1]:"";
		}
		else{
                    $selectedSubject = 0;
                    $selectedDate = !empty($params['pass'][0])?$params['pass'][0]:"";
                }
		
		$datepickerData = $controller->Utility->datepickerStartEndDate($selectedDate);
		$startDate = $datepickerData['startDate'];
		$endDate = $datepickerData['endDate'];
		$dateDiff = $datepickerData['dateDiff']; 
                
                if(!empty($subjectsOptions) && count($params['pass']) != 2){
                    $controller->redirect(array('action' => 'attendance_class', key($subjectsOptions), $startDate));
                }
		
		$controller->set('subjectsOptions', $controller->Utility->getSetupOptionsData($subjectsOptions));
		$controller->set('className', $className);
		$controller->set('classId', $classId);
		
		$controller->set('dateDiff', $dateDiff);
		$controller->set('startDate', $startDate);
		$controller->set('endDate', $endDate);
		$controller->set('selectedSubject', $selectedSubject);
		
		$attendanceType = ClassRegistry::init('StudentAttendanceType')->getAttendanceList('all', true);
		$controller->set('attendanceType', $attendanceType);
		
		$timetableSource = ClassRegistry::init('TimetableEntry')->getClassTimetable($classId, $startDate, $endDate, array('education_subject_id'=> $selectedSubject));
		
		$ClassLesson = ClassRegistry::init('ClassLesson');
		$period = $ClassLesson->getLessonPeriod($classId,$timetableSource, $startDate, $endDate, array('education_subject_id'=> $selectedSubject));
		//pr($period);
		$controller->set('period', $period);
		$controller->set('tableHeaderData', $this->arrayValueCounter($period, 'date'));
		
		$data = ClassRegistry::init('ClassStudent')->getStudentsByClass($classId);
		$controller->set('data', $data);
		
		$teachersData = $ClassLesson->getTeachersList($classId, $selectedSubject);
		$controller->set('teachersData', $teachersData);
		
		$attendanceData = $this->getAttendanceByClass($classId, $selectedSubject, $startDate, $endDate);
		$controller->set('attendanceData', $attendanceData);
		//pr($selectedSubject);
		
		if(empty($period) || empty($data) ){
			$controller->Message->alert('general.view.notExists', array('type' => 'info'));	
		}
	
		//Breadcrumb
		//$controller->Navigation->addCrumb($subjectsOptions[$selectedSubject]);
                $header = $className;
                if(isset($subjectsOptions[$selectedSubject])){
                    $header .= ' / '.$subjectsOptions[$selectedSubject];
                }
                
		$controller->set('header', $header);
		
		$controller->request->data['SClass']['startDate'] = $datepickerData['startDate'];
		$controller->request->data['SClass']['endDate'] =  $datepickerData['endDate'];
		$controller->request->data['SubjectList'] =  $selectedSubject;
	}
	
	public function lesson_edit($controller, $params){
		$classId = $params['pass'][0];
		$className = $controller->Session->read('Class.name');
		
		$selectedSubject = $params['pass'][1];
		$controller->set('selectedSubject', $selectedSubject);
		
		$date = empty($params['pass'][2])? date('Y-m-d'):date('Y-m-d', $params['pass'][2]);
		$controller->set('selectedDate', $date);
		
		if($controller->request->is('post')){
			$postData = $controller->request->data;
			//pr(	$postData);
			/*foreach($postData as $key =>$attendance){
				if(empty($attendance['StudentAttendanceLesson']['class_lesson_id'])){
					unset($postData[$key]['StudentAttendanceLesson']['class_lesson_id']);
				}
			}*/
			//$firstObj = $postData;
			
			if($this->saveAll($postData)){
				return $controller->redirect(array('action' => 'attendance_class',  $selectedSubject, $date));
			}
			else{
				$controller->Message->alert('general.edit.failed', array('type' => 'error'));	
			}
		}
		
		$controller->EducationSubject->recursive = -1;
		$subjectData = $controller->EducationSubject->findById($selectedSubject, array('name', 'code'));
		
		
		$controller->set('className', $className);
		$controller->set('classId', $classId);
		//$controller->set('subjectName', $subjectData['EducationSubject']['name']);
		
		//Get list of attendance option and filter the list
		$attendanceType = ClassRegistry::init('StudentAttendanceType')->getAttendanceList();
		$controller->set('attendanceType', $attendanceType);
		
		$attendanceTypeOptions = array();
		foreach($attendanceType as $item){
			$attendanceTypeOptions[$item['StudentAttendanceType']['id']] = $item['StudentAttendanceType']['short_form'];
		}
		$controller->set('attendanceTypeOptions', $attendanceTypeOptions);
		
		//Get list of stundents
		$data = ClassRegistry::init('ClassStudent')->getStudentsByClass($classId);
		$controller->set('data', $data);
		
		$ClassLesson = ClassRegistry::init('ClassLesson');
		$teachersData = $ClassLesson->getTeachersList($classId, $selectedSubject);
		$controller->set('teachersData', $teachersData);
		
		//Getting lesson info
		$selectedLesson =  $ClassLesson->getSelectedLessonPeriod($classId, $selectedSubject, date('Y-m-d H:i:s', $params['pass'][2]));
		//pr($selectedLesson);
		
		$classLessonId = empty($selectedLesson['ClassLesson']['id'])? 0:$selectedLesson['ClassLesson']['id'];
		$controller->set('classLessonId', $classLessonId);
		
		$startTime = date('H:i a', strtotime($selectedLesson['ClassLesson']['start_time']));
		$endTime = date('H:i a', strtotime($selectedLesson['ClassLesson']['end_time']));
		$controller->set('attendanceTimeSlot', $startTime. " - ".$endTime);
		
		$attendanceData = $this->getAttendanceByClassLessonId($classLessonId);
		$controller->set('attendanceData', $attendanceData);
		
		
		//Breadcrumb
		$controller->Navigation->addCrumb($className, array('controller'=>'Attendance', 'action' => 'index', 2, $classId, $selectedSubject));
		$controller->Navigation->addCrumb($subjectData['EducationSubject']['name'],array('controller'=>'Attendance', 'action' => 'lesson', $classId, $selectedSubject,$date));
		$controller->Navigation->addCrumb($this->Message->getLabel('general.edit'));
		$controller->set('header', $className.'/'.$subjectData['EducationSubject']['name']);
		
	}
	
	function arrayValueCounter($arr, $assocKey){
		$arr2=array(); 
		if(is_array(current($arr))){
			foreach($arr as $sArr){
				foreach($sArr as $key => $item){
					if($key == $assocKey){
						if(!isset($arr2[$item])){
							$arr2[$item]=1;
						}else{
							$arr2[$item]++;
						} 
					}
				}
			}
		}
		
		return $arr2;
	}
	
	public function lesson_view($controller, $params){
		$controller->Navigation->addCrumb($this->Message->getLabel('general.attendance'));

		$studentId = $controller->Session->read('Student.id');
		//$classId = $controller->Session->read('Class.id');
	
		if(empty($studentId)){
			return $controller->redirect(array('controller'=>'Students', 'action' => 'index'));
		}
		
		$filterBySubject = "";
		$filterByType = "";
		$selectedDate ="";
		$header ='';
		
		if(count($params['pass']) == 1){
			$selectedDate = $params['pass'][0];
			//$selectedDate = $params['pass'][1];
		}
		else if(count($params['pass']) >= 3){
			$filterBySubject = $params['pass'][0];
			$filterByType = $params['pass'][1];
			$selectedDate = $params['pass'][2];
		}
		
		$datepickerData = $controller->Utility->datepickerStartEndDate($selectedDate, 14);
		
		$StudentAttendanceType = ClassRegistry::init('StudentAttendanceType');
		$attendanceType = $StudentAttendanceType->getAttendanceList();
		$attendanceTypeOptions = array();
		foreach($attendanceType as $item){
			$attendanceTypeOptions[$item['StudentAttendanceType']['id']] = $item['StudentAttendanceType']['name'];
		}
		
		$filterByType = (empty($filterByType))? key($attendanceTypeOptions):$filterByType ;
		
		$ClassSubject = ClassRegistry::init('ClassSubject');
		$classSubjectsList = $ClassSubject->getSubjectByStudentId($studentId);
		$subjectsOptions = array();
		foreach($classSubjectsList as $item){
			$subjectsOptions[$item['ClassSubject']['education_grade_subject_id']] = $item['EducationSubject']['code']." - ".$item['EducationSubject']['name'];
		}
		
		$selectedGradeSubjectId = !empty($filterBySubject)? $filterBySubject : key($subjectsOptions); 
		$selectedGradeSubjectId = !empty($selectedGradeSubjectId)? $selectedGradeSubjectId : 0;
		
		$attendancesList = array();
		
		$data = $controller->Student->find('first', array('recursive' => 0, 'conditions' => array('Student.id' => $studentId)));
		$header = $data['SecurityUser']['first_name'].' '.$data['SecurityUser']['last_name'].' ('.$data['Student']['student_no'].')';
		
		$attendancesList = $this->getAttendanceByStudentId($studentId, $selectedGradeSubjectId, $datepickerData['startDate'],$datepickerData['endDate'],$filterByType);
		
		if(empty($attendancesList) ){
			$controller->Message->alert('general.view.notExists', array('type' => 'info'));	
		}
		
		$controller->set('attendanceType', $controller->Utility->getSetupOptionsData($attendanceType));
		$controller->set('data', $data);
		$controller->set('attendancesList', $attendancesList);
		$controller->set('attendanceTypeOptions', $controller->Utility->getSetupOptionsData($attendanceTypeOptions));
		$controller->set('subjectsOptions', $controller->Utility->getSetupOptionsData($subjectsOptions));
		$controller->set('selectedGradeSubject', $selectedGradeSubjectId);
		$controller->set('isEdit', true);
		$controller->set('selectedAttendanceType', $filterByType);
		$controller->set('startDate', $datepickerData['startDate']);
		$controller->set('endDate', $datepickerData['endDate']);
		$controller->set('header', $header);
		
		$controller->request->data['Student']['startDate'] = $datepickerData['startDate'];
		$controller->request->data['Student']['endDate'] =  $datepickerData['endDate'];
		$controller->request->data['SubjectList'] =  $filterBySubject;
		$controller->request->data['StudentAttendanceType'] =  $filterByType;
	}
	
	public function setupDataBeforeSave($data, $classLessonId = NULL){
		unset($data['StudentAttendanceLesson']);
		
		for($i = 0; $i < count($data); $i ++){
			$data[$i]['StudentAttendanceLesson']['class_lesson_id'] = 	$classLessonId;
		}
		
		return $data;
	}
	
	public function getAttendanceByClass($classId, $educationSubjectId, $startDate, $endDate = NULL){
		$options['joins'] = array(
			array('table' => 'class_lessons',
				'alias' => 'ClassLesson',
				'type' => 'LEFT',
				'conditions' => array(
					'ClassLesson.id = StudentAttendanceLesson.class_lesson_id',
				)
			),
			array('table' => 'education_grades_subjects',
				'alias' => 'EducationGradeSubject',
				'type' => 'LEFT',
				'conditions' => array(
					'EducationGradeSubject.id = ClassLesson.education_grade_subject_id',
				)
			),
			array('table' => 'class_students',
				'alias' => 'ClassStudent',
				'type' => 'LEFT',
				'conditions' => array(
					'ClassStudent.class_id = '.$classId,
					'ClassStudent.student_id = StudentAttendanceLesson.student_id',
				)
			),
			array('table' => 'student_attendance_types',
				'alias' => 'StudentAttendanceType',
				'type' => 'LEFT',
				'conditions' => array(
					'StudentAttendanceType.id = StudentAttendanceLesson.student_attendance_type_id'
				)
			),
		);
		
		$options['conditions'] = array(
			'ClassLesson.class_id = '.$classId,
			'EducationGradeSubject.education_subject_id = '.$educationSubjectId,
		);
		
		if(!empty($startDate) && !empty($endDate)){
			$options['conditions']['ClassLesson.start_time >='] = $startDate;
			$options['conditions']['ClassLesson.start_time <='] = $endDate;
		}
		else{
			$options['conditions']['ClassLesson.start_time'] = $startDate;
		}
		
		$options['recursive'] = -1;
		$options['fields'] = array('StudentAttendanceLesson.*', 'StudentAttendanceType.short_form','ClassLesson.start_time');
		$options['order'] = array('StudentAttendanceLesson.student_id ', 'ClassLesson.start_time ASC');
		$data = $this->find('all', $options);
		//pr($data);die;
		$newData = array();
		for($i = 0; $i < count($data); $i ++){
			$student_id = $data[$i]['StudentAttendanceLesson']['student_id'];
			//$tempArr = array();
			$tempArr = $data[$i]['StudentAttendanceLesson'];
			$tempArr['short_form'] = $data[$i]['StudentAttendanceType']['short_form'];
			$tempArr['datetime'] = $data[$i]['ClassLesson']['start_time'];
			
			$newData['StudentAttendanceLesson'][$student_id][] = $tempArr;
		}
		return $newData;
	}
	
	public function getAttendanceByClassLessonId($classLessonId){
		$options['joins'] = array(
			array('table' => 'class_students',
				'alias' => 'ClassStudent',
				'type' => 'LEFT',
				'conditions' => array(
					'ClassStudent.student_id = StudentAttendanceLesson.student_id',
				)
			),
			array('table' => 'student_attendance_types',
				'alias' => 'StudentAttendanceType',
				'type' => 'LEFT',
				'conditions' => array(
					'StudentAttendanceType.id = StudentAttendanceLesson.student_attendance_type_id'
				)
			),
		);
		
		$options['conditions'] = array(
			'StudentAttendanceLesson.class_lesson_id = '.$classLessonId
		);
		
		$options['recursive'] = -1;
		$options['fields'] = array('StudentAttendanceLesson.*', 'StudentAttendanceType.short_form');
	//	$options['order'] = array('StudentAttendanceLesson.student_id ', 'ClassLesson.start_time ASC');
		$data = $this->find('all', $options);
		
		$newData = array();
		for($i = 0; $i < count($data); $i ++){
			$student_id = $data[$i]['StudentAttendanceLesson']['student_id'];
			//$tempArr = array();
			$tempArr = $data[$i]['StudentAttendanceLesson'];
			$tempArr['short_form'] = $data[$i]['StudentAttendanceType']['short_form'];
			//$tempArr['datetime'] = $data[$i]['ClassLesson']['start_time'];
			
			$newData['StudentAttendanceLesson'][$student_id][] = $tempArr;
		}
		return $newData;
	}
	
	
	public function getAttendanceByStudentId($id, $educationGradeSubjectId, $startDate, $endDate = NULL, $filterBy = ''){
		$options['joins'] = array(
			array('table' => 'class_lessons',
				'alias' => 'ClassLesson',
				'type' => 'LEFT',
				'conditions' => array(
					'ClassLesson.id = StudentAttendanceLesson.class_lesson_id',
				)
			),
			array('table' => 'student_attendance_types',
				'alias' => 'StudentAttendanceType',
				'type' => 'LEFT',
				'conditions' => array(
					'StudentAttendanceType.id = StudentAttendanceLesson.student_attendance_type_id'
				)
			),
		);
		
		$options['conditions'] = array(
			'StudentAttendanceLesson.student_id = '.$id,
			'ClassLesson.education_grade_subject_id = '.$educationGradeSubjectId,
		);
		
		if(!empty($startDate) && !empty($endDate)){
			$options['conditions']['ClassLesson.start_time >='] = $startDate;
			$options['conditions']['ClassLesson.start_time <='] = $endDate;
		}
		else{
			$options['conditions']['ClassLesson.start_time'] = $startDate;
		}
		if(!empty($filterBy)){
			$options['conditions']['student_attendance_type_id'] = $filterBy;	
		}
		
		
		$options['recursive'] = -1;
		$options['fields'] = array('StudentAttendanceLesson.*', 'StudentAttendanceType.short_form','ClassLesson.start_time','ClassLesson.end_time');
		$options['order'] = array('StudentAttendanceLesson.student_id ', 'ClassLesson.start_time ASC');
		$data = $this->find('all', $options);
		
		$newData = array();
		for($i = 0; $i < count($data); $i ++){
			$student_id = $data[$i]['StudentAttendanceLesson']['student_id'];
			//$tempArr = array();
			$tempArr = $data[$i]['StudentAttendanceLesson'];
			$tempArr['short_form'] = $data[$i]['StudentAttendanceType']['short_form'];
			$tempArr['start_time'] = $data[$i]['ClassLesson']['start_time'];
			$tempArr['end_time'] = $data[$i]['ClassLesson']['end_time'];
			
			$newData[] = $tempArr;
		}
		return $newData;
	}
}

?>
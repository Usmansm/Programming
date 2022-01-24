<?php
if (isset($valueType)) {
	switch ($valueType) {
		case 'dropdown':
			echo $this->Form->input('value', array('options' => $options));
			break;

		case 'time':
			echo $this->FormUtility->timepicker('value', array('attr' => $attr));
			break;
			
		default:
			break;
	}
} else {
	echo $this->Form->input('value');
}
?>

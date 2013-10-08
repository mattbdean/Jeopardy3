<?php

class QAData {
	public $value;
	public $error;

	function __construct($value = '', $error = '') {
		$this->value = $value;
		$this->error = $error;
	}
}

?>
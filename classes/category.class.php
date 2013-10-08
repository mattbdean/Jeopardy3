<?php
require 'classes/qadata.class.php';

/**
* 
*/
class Category {
	public $name;
	public $answers;
	public $questions;

	function __construct($name = null, $answers = [], $questions = []) {
		if ($name === null) {
			$name = new QAData();
		}
		$this->name = $name;
		$this->answers = $answers;
		$this->questions = $questions;
	}
}
?>
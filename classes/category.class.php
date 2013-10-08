<?php
require 'classes/qadata.class.php';

/**
* 
*/
class Category {
	public $name;
	public $answers;
	public $questions;

	function __construct($name = null, $answers = null, $questions = null) {
		if ($name === null) {
			$name = new QAData();
		}
		$this->name = $name;

		$empty = [];
		for ($i = 0; $i < 5; $i++) {
			$empty[$i] = new QAData();
		}

		if ($answers === null) {
			$answers = $empty;
		}
		if ($questions === null) {
			$questions = $empty;
		}
		$this->answers = $answers;
		$this->questions = $questions;
	}
}
?>
<?php
/**
* 
*/
class Category {
	public $name;
	public $answers;
	public $questions;

	function __construct($name = null, $answers = [], $questions = []) {
		$this->name = $name;
		$this->answers = $answers;
		$this->questions = $questions;
	}
}
?>
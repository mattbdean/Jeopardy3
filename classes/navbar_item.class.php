<?php
/**
* 
*/
class NavbarItem {

	public $name;
	public $location;
	public $newTab;

	function __construct($name, $location, $newTab = false) {
		$this->name = $name;
		$this->location = $location;
		$this->newTab = $newTab;	
	}
}
?>
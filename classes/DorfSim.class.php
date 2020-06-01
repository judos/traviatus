<?php
	
class DorfSim {
	public static $save = false;
	
	protected $name;
	
	public function __construct($name) {
		$this->name=$name;
	}
	
	public function getLink() {
		return $this->name;
	}
}

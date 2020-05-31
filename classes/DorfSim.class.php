<?php
	
class DorfSim {
	public static $save = false;
	
	protected $name;
	
	public function DorfSim($name) {
		$this->name=$name;
	}
	
	public function getLink() {
		return $this->name;
	}
}

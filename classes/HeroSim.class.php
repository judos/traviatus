<?php

class HeroSim {
	public static $save=false;
	
	protected $name;
	
	public function __construct($name) {
		$this->name=$name;
	}
	
	public function get($att) {
		if ($att=='name')
			return $this->name;
		else
			new Errorlog("Can't find any attribute for object HeroSim");
	}
	
}
<?php


class WW {

	public static $save = false;
	
	
	public static function getExcludeFields() {
		return array(25,26,29,30,33);
	}
	
	public static function getField() {
		return 26;
	}
	
	public static function isWWDorf($dorf) {
		$land = Land::getByXY($dorf->get('x'),$dorf->get('y'));
		return $land->get('ww')==1;
	}
	
	public static function getWWGebId() {
		return 40;
	}
	
	public static function getImageCount() {
		return 6;
	}
	
}
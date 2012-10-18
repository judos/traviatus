<?php


class Fest {
	
	public static $save=false;
	
	protected $id;
	protected $data;
	
	protected static $objekte;
	
	private function Fest($id,$data) {
		$this->id=$id;
		$this->data=$data;
		self::$objekte[$id]=$this;
	}
	
	public function feierbar($dorf) {
		$highest=$dorf->highest();
		foreach($this->get('needs') as $geb => $stufe) {
			if ($highest[$geb]<$stufe) return false;
		}
		return true;
	}
	
	public function get($att) {
		return $this->data[$att];
	}
	
	public function dauer($dorf) {
		$highest=$dorf->highest();
		$stufe_ratshaus=$highest[24];
		return round($this->get('dauer')*
									(100-($stufe_ratshaus-1)/19*50)/100);
	}
	
	public function kp($dorf) {
		$highest=$dorf->highest();
		$stufe_brauerei=$highest[35];
		return $this->get('kp')*(1+$stufe_brauerei/10);
	}
	
	public static function getAll() {
		if (!isset(self::$objekte))
			self::loadFeste();
		if (!isset(self::$objekte))
			self::$objekte=array();
		return self::$objekte;
	}
	
	public static function getById($id) {
		if (!isset(self::$objekte))
			self::loadFeste();
		if (!isset(self::$objekte))
			self::$objekte=array();
		return self::$objekte[$id];
	}
	
	protected static function loadFeste() {
		new Fest(0,array('id'=>0,'name'=>'kleines Fest',
											'kosten'=>array(6400,6650,5940,1340),
											'kp'=>500,
											'dauer'=>3600*24,
						 					'needs'=>array(24=>1)));
		new Fest(1,array('id'=>1,'name'=>'grosses Fest',
										 'kosten'=>array(29700,33250,32000,6700),
										 'kp'=>2000,
										 'dauer'=>3600*48,
						 				 'needs'=>array(24=>10)));
	}
}
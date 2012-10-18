<?php

class Bericht {
	
	protected $id;
	protected $data;
	
	public static $save=false;
	
	protected static $db_table='msg';
	protected static $objekte=array();
	
	protected static $all_loaded=false;
	
	
	
	public function Bericht($id,$data) {
		$this->id=$id;
		$this->data=$data;
		if (isset(self::$objekte[$id]))
			new Errorlog('Bericht - objekt bereits vorhanden');
		self::$objekte[$id]=$this;
	}
	
	public function get($att) {
		if ($att=='betreff' && $this->data[$att]=='')
			return '-kein Betreff-';
		return $this->data[$att];
	}
	
	public function read() {
		$sql="UPDATE tr".ROUND_ID."_".self::$db_table."
			SET neu=0 WHERE keyid='".$this->id."';";
		mysql_query($sql);
	}
	
	public static function getById($id) {
		if (!self::$all_loaded)
			if (!isset(self::$objekte[$id]))
				self::loadById($id);
		
		return self::$objekte[$id];
	}
	
	//Stellt alle Berichte eines Types zusammen (0=alle)
	public static function getAll($typ) {
		if (!self::$all_loaded) self::loadAll();
		if ($typ==0) return self::$objekte;
		else {
			$result=array();
			foreach(self::$objekte as $id => $bericht) {
				if ($bericht->get('typ')==$typ)
					$result[$id]=$bericht;
			}
			return $result;
		}
	}
	
	public static function loadById($id) {
		global $login_user;
		$sql="SELECT * FROM `tr".ROUND_ID."_".self::$db_table."`
			WHERE `an`='".$login_user->get('name')."' AND von=''
				AND keyid='$id';";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)==0) return NULL;
		$data=mysql_fetch_array($result);
		return new Bericht($id,$data);
	}
	
	public static function loadAll() {
		global $login_user;
		if (!self::$all_loaded) {
			$username=$login_user->get('name');
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE von='' AND an='$username'
				ORDER BY `zeit` DESC;";
			$result=mysql_query($sql);
			while($data=mysql_fetch_assoc($result)) {
				new Bericht($data['keyid'],$data);
			}
			self::$all_loaded=true;
		}
	}
}
<?php

class Bericht extends InfoMessage {
	public static $save=false;
	protected static $db_table='msg';
	protected static $objekte=array();
	protected static $all_loaded=false;
	
	const TYPE_ALLE           = 0;
	const TYPE_HANDEL         = 1;
	const TYPE_UNTERSTUETZUNG = 2;
	const TYPE_ANGRIFFE       = 3;
	const TYPE_SONSTIGE       = 4;

	
	protected $id;
	protected $data;
	
	protected function Bericht($id,$data) {
		$this->id=$id;
		$this->data=$data;
		if (isset(self::$objekte[$id]))
			new Errorlog('Bericht - objekt bereits vorhanden');
		self::$objekte[$id]=$this;
		parent::__construct($this->data['text']);
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
	
	//Stellt alle Berichte eines Types zusammen
	public static function getAll($typ) {
		if (!self::$all_loaded) self::loadAll();
		if ($typ==self::TYPE_ALLE) return self::$objekte;
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
		if (isset(self::$objekte[$id]))
			return;
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
				if (!isset(self::$objekte[$data['keyid']]))
					new Bericht($data['keyid'],$data);
			}
			self::$all_loaded=true;
		}
	}
}

?>
<?php

class AllianzBericht extends InfoMessage {
	public static $save=false;
	protected static $db_table='ally_kampfe';
	protected static $objekte=array(); //[$ally_id][$keyid]
	protected static $all_loaded=array(); //[$ally_id]
	
	protected $id;
	protected $allyId;
	protected $data;
	
	protected function __construct($data) {
		$this->id=$data['keyid'];
		$this->allyId=$data['ally_id'];
		$this->data=$data;
		if (isset(self::$objekte[$this->allyId][$this->id]))
			new Errorlog('AllianzBericht - objekt bereits vorhanden');
		@self::$objekte[$this->allyId][$this->id]=$this;
		parent::__construct($this->data['text']);
	}
	
	public function get($att) {
		if ($att=='betreff' && $this->data[$att]=='')
			return '-kein Betreff-';
		return $this->data[$att];
	}

	public static function getByAllyId($ally,$id) {
		if (is_object($ally))
			$ally=$ally->get('id');
		if (!isset(self::$all_loaded[$ally]))
			self::loadByAlly($ally);
		return @self::$objekte[$ally][$id];
	}
	
	//Stellt alle Berichte einer Allianz zusammen
	public static function getByAlly($ally) {
		if (is_object($ally))
			$ally=$ally->get('id');
		if (!isset(self::$all_loaded[$ally]))
			self::loadByAlly($ally);
		if (!isset(self::$objekte[$ally]))
			self::$objekte[$ally]=array();
		return self::$objekte[$ally];
	}
	
	public static function loadByAlly($ally_id) {
		global $login_user;
		
		$sql="SELECT * FROM `tr".ROUND_ID."_".self::$db_table."`
			WHERE `ally_id`='$ally_id' ORDER BY datetime DESC;";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)==0) return;
		while(($data=mysql_fetch_array($result))!=null){
			new AllianzBericht($data);
		}
	}
}

?>
<?php

class AllianzRang {
	
	protected $id,$ally;
	protected $data;
	protected $changed;
	
	protected static $objekte; // [$ally][$id]
	protected static $loaded;  // [$ally][$id]
	protected static $db_table='ally_range';
	protected static $db_key=array('ally_id','rang_id');
	
	protected static $rechte;
	
	public static $save=true;
	
	public function __construct($ally,$id,$data) {
		$this->id=$id;
		$this->ally=$ally;
		$this->data=$data;
		self::$objekte[$ally][$id]=$this;
		self::$loaded[$ally][$id]=true;
	}
	
	public function delete() {
		$sql="DELETE FROM tr".ROUND_ID."_".self::$db_table."
			WHERE ally_id=".$this->ally." AND rang_id=".$this->id.";";
		mysql_query($sql);
		unset(self::$objekte[$this->ally][$this->id]);
	}
	
	public function dump() {
		ob_start();
		var_dump($this->data);
		return ob_get_flush();
	}
	
	public function get($att) {
		return $this->data[$att];
	}
	
	public function set($att,$value) {
		$this->data[$att]=$value;
		$this->changed=true;
	}
	
	public function save() {
		$sql="UPDATE tr".ROUND_ID."_".self::$db_table." SET ";
		foreach(self::$data[$this->id] as $key => $value) {
			$sql.="$key='$value',";
		}
		$sql=substr($sql,0,-1);
		$sql.=" WHERE ";
		foreach(self::$db_key as $att) {
			$sql.="$att='".$this->data[$value]
				."' AND ";
		}
		$sql=substr($sql,0,-4);
		mysql_query($sql);
	}

	public static function saveAll() {
		if (self::$objekte!=NULL) {
			foreach (self::$objekte as $ally => $arr) {
				foreach($arr as $id => $rang) {
					if ($rang->changed) {
						$rang->save();
					}
				}
			}
		}
	}
	
	public static function rechte() {
		if (!isset(self::$rechte)) {
			$sql="EXPLAIN tr".ROUND_ID."_".self::$db_table.";";
			$result=mysql_query($sql);
			self::$rechte=array();
			while ($data=mysql_fetch_assoc($result)) {
				if ($data['Type']=='tinyint(1)')
					array_push(self::$rechte,$data['Field']);
			}
		}
		return self::$rechte;
	}
	
	public static function rechteNamen() {
		$namen=array('Spieler in Allianz einladen',
							 'Posten vergeben',
							 'Spieler entlassen',
							 'Beschreibung ändern',
							 'Rundmail schicken',
							 'Allianz Diplomatie',
							 'Forum verwalten',
							 'Allianz auflösen');
		return $namen;
	}
	
	public static function create($ally,$name,$rechte) {
		$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."
			(ally_id,rang_name,";
		$x=array_keys($rechte);
		foreach($x as $key)
			$sql.=$key.',';
		$sql=substr($sql,0,-1).") VALUES ('$ally','$name',";
		$x=array_values($rechte);
		foreach($x as $value){
		
			$sql.="'".($value?1:0)."',";
		}
		$sql=substr($sql,0,-1).");";
		mysql_query($sql);
		
		$id=mysql_insert_id();
		
		unset(self::$loaded[$ally]);
		$rang=self::getByAI($ally,$id);
		return $rang;
	}
	
	public static function getByAI($ally,$id) {
		if (is_object($ally)) $ally=$ally->get('id');
		else $ally=(int)$ally;
		if (@!self::$loaded[$ally]) {
			self::loadEntry($ally);
		}
		return self::$objekte[$ally][$id];
	}
	
	public static function getByAlly($ally) {
		if (is_object($ally)) $ally=$ally->get('id');
		else $ally=(int)$ally;
		if (@!isset(self::$loaded[$ally])) {
			self::loadEntry($ally);
		}
		return self::$objekte[$ally];
	}
	
	protected static function loadEntry($ally) {
		if (!isset(self::$loaded[$ally])) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE ally_id=$ally;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			while($data=mysql_fetch_assoc($result)) {
				new AllianzRang($ally,$data['rang_id'],$data);
			}
		}
	}
}
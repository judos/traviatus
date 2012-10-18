<?php


class ___ {		//TODO

	protected $id;
	protected $data;
	
	protected $changed;	//TODO
	
	protected static $objekte;
	protected static $db_key=array('id');
	protected static $db_table='___';	//TODO
	
	public static $save=true;
	
	public function ___($id,$data) {	//TODO
		$this->id = $id;
		$this->data=$data;
		$this->changed=false;
		if (isset(self::$objekte[$id]))
			new Errorlog('new '.get_class($this).'. Objekt existiert bereits.
					Gefahr der Überschreibung.');
		self::$objekte[$id]=$this;
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
			$sql.="$att='".$this->data[$att]
				."' AND ";
		}
		$sql=substr($sql,0,-4);
		mysql_query($sql);
	}

	public static function saveAll() {
		if (self::$objekte!=NULL) {
			foreach (self::$objekte as $id => $objekt) {
				if ($objekt->changed) {
					$objekt->save();
				}
			}
		}
	}
	
	public static function getById($id) {
		if (!isset(self::$objekte[$id])) {
			self::loadEntry($id);
		}
		return self::$objekte[$id];
	}
	
	protected static function loadEntry($id) {
		if (!isset(self::$objekte[$id])) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE id=$id;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			new ______($id,$data);		//TODO
		}
	}
}

?>
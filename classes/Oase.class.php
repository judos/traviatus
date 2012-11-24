<?php
class Oase {

	protected $id;
	protected $data;
	protected $changed;

	protected static $objekte=array();	// [$id]
	protected static $loaded=array();	// [$id]
	
	public static $save=TRUE;

	protected function Oase($id,$data) {
		$this->id=$id;
		$this->data=$data;
		$this->changed=false;
		self::$objekte[$id]=$this;
		self::$loaded[$id]=TRUE;
	}

	public function get($att) {
		return $this->data[$att];
	}
	
	public function set($att,$value) {
		if ($this->data[$att]!=$value){
			$this->data[$att]=$value;
			$this->changed=true;
		}
	}

	public function bonus() {
		return explode(':',$this->get('bonus'));
	}

	//XXX: remove if everything works without it
	//public function tierGrenze() {
	//	return explode(':',$this->get('tier_grenze'));
	//}
	
	public function tierGrenzeIds() {
		$nrs=explode(':',$this->get('tier_grenze'));
		$ids=array();
		for($i=0;$i<10;$i++)
			$ids[$i+31]=$nrs[$i];
		return $ids;
	}
	
	protected function save() {
		$sql="UPDATE tr".ROUND_ID."_oasen SET
			bonus = '{$this->data['bonus']}',
			tier_grenze = '{$this->data['tier_grenze']}'
			WHERE typ= $this->id;";
		mysql_query($sql);
	}

	public static function getById($id) {
		if (!isset(self::$loaded[$id]) or !self::$loaded[$id]) {
			self::loadById($id);
		}
		return self::$objekte[$id];
	}

	protected static function loadById($id) {
		if (!isset(self::$loaded[$id]) or !self::$loaded[$id]) {
			$sql="SELECT * FROM tr".ROUND_ID."_oasen WHERE typ=$id;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return FALSE;
			$data=mysql_fetch_assoc($result);
			new Oase($id,$data);
		}
	}
	
	public static function saveAll() {
		foreach (self::$objekte as $id=>$objekt)
			if($objekt->changed)
				$objekt->save();
	}
}

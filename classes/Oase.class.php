<?php



class Oase {

	protected $id;
	protected $data;

	protected static $objekte;	// [$id]
	protected static $loaded;		// [$id]
	
	public static $save=FALSE;

	protected function Oase($id,$data) {
		$this->id=$id;
		$this->data=$data;
		self::$objekte[$id]=$this;
		self::$loaded[$id]=TRUE;
	}

	public function get($att) {
		return $this->data[$att];
	}


	public function bonus() {
		return explode(':',$this->get('bonus'));
	}

	public function tierGrenze() {
		return explode(':',$this->get('tier_grenze'));
	}





	public static function getById($id) {
		if (!self::$loaded[$id]) {
			self::loadById($id);
		}
		return self::$objekte[$id];
	}


	protected static function loadById($id) {
		if (!self::$loaded[$id]) {
			$sql="SELECT * FROM tr".ROUND_ID."_oasen WHERE typ=$id;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return FALSE;
			$data=mysql_fetch_assoc($result);
			new Oase($id,$data);
		}
	}
}

<?php

class Auftrag {

	protected $x,$y;
	protected $data;

	public static $save=false;

	protected static $objekte=array();	// [$x][$y][$index]
	protected static $db_table='others';

	public function Auftrag($x,$y,$data) {
		$this->x=$x;
		$this->y=$y;
		$this->data=$data;
		if (self::$objekte[$x][$y]===NULL)
			self::$objekte[$x][$y]=array();
		array_push(self::$objekte[$x][$y],$this);
	}

	public function bauAuftrag() {
		return $this->get('typ')==10;
	}

	public function toString() {
		return 'Auftrag: keyID '.$this->get('keyid').', in ( '.$this->x.' | '.$this->y.' ), Typ: '.$this->get('typ').
			', Anzahl: '.$this->get('anzahl').', Fertig:'.zeitAngabe(strtotime($this->get('zeit'))).
			', Dauer: '.$this->get('dauer').'s, BauID: '.$this->get('id');
	}

	//Typen: 1Kaserne,2Stall,3Werkstatt,4Residenz/Palast
	//       5Akademie,6Waffenschmid,7Rüstungsschmid,8Ratshaus
	//       9Gebäude abreissen,10Gebäude bauen,11Fallen bauen
	//			12Held ausbilden
	public function hatTyp($typ) {
		return $this->get('typ')==$typ;
	}

	public function get($att) {
		return $this->data[$att];
	}

	public function delete() {
		$sql="DELETE FROM `tr".ROUND_ID."_".self::$db_table."`
			WHERE keyid='".$this->get('keyid')."';";
		mysql_query($sql);
		unset(self::$objekte[$this->x][$this->y]);
	}

	public static function create($x,$y,$typ,$id,$time,$anz,$dauer) {
		$sql="INSERT INTO `tr".ROUND_ID."_".self::$db_table."`
			(`x`,`y`,`typ`,`id`,`zeit`,`anzahl`,`dauer`)
			VALUES ('$x','$y','$typ','$id','".
				date('Y-m-d H:i:s',$time)."','$anz','$dauer');";
		mysql_query($sql);
		unset(self::$objekte[$x][$y]);
	}

	public static function getByXYTI($x,$y,$typ,$id) {
		$result=array();
		if (!isset(self::$objekte[$x][$y]))
			self::loadByXY($x,$y);
		foreach(self::$objekte[$x][$y] as $auftrag) {
			if ($auftrag->get('typ')==$typ && $auftrag->get('id')==$id)
				array_push($result,$auftrag);
		}
		return $result;
	}

	public static function getByD($dorf) {
		return self::getByXY($dorf->get('x'),$dorf->get('y'));
	}

	public static function getByXY($x,$y) {
		if (!isset(self::$objekte[$x][$y])) {
			self::loadByXY($x,$y);
		}
		return self::$objekte[$x][$y];
	}

	public static function getByUser($user) {
		if (!is_object($user)) {
			$user=Spieler::getById($user);
		}
		$dorfer=$user->dorfer();
		$result=array();
		foreach($dorfer as $dorf) {
			$a=self::getByD($dorf);
			foreach($a as $auftrag) {
				array_push($result,$auftrag);
			}
		}
		return $result;
	}

	public static function loadByXY($x,$y) {
		if (!isset(self::$objekte[$x][$y])) {
			$sql="SELECT *
				FROM `tr".ROUND_ID."_".self::$db_table."`
				WHERE `x`='$x' AND `y`='$y' ORDER BY `zeit` ASC;";
			$result=mysql_query($sql);
			self::$objekte[$x][$y]=array();
			while($data=mysql_fetch_assoc($result)) {
				new Auftrag($data['x'],$data['y'],$data);
			}
		}
	}

}

?>
<?php

class Land {

	private $x,$y;

	protected $data;
	protected $changed;

	protected static $objekte;
	public static $save=true;

	protected static $db_key=array('x','y');
	protected static $db_table='lander';

	protected static $land_typen;

	protected static $sizeX,$sizeY;

	public static function size() {
		if (!isset(self::$sizeX)) {
			$sql="SELECT MAX(x) as sizeX,MAX(y) as sizeY
				FROM tr".ROUND_ID."_".self::$db_table.";";
			$result=mysql_query($sql);
			$data=mysql_fetch_assoc($result);
			self::$sizeX=$data['sizeX'];
			self::$sizeY=$data['sizeY'];
		}
		return array(self::$sizeX,self::$sizeY);
	}

	public function __construct($x,$y,$data) {
		$this->x=$x;
		$this->y=$y;
		$this->data=$data;
		$this->changed=false;
	}

	public function rohstoffGebeude() {
		if($this->get('oase')==1) return NULL;
		$geb=$this->gebeude();
		$rohstoffMinen=array(1=>0,2=>0,3=>0,4=>0);
		foreach($geb as $gebTyp)
			$rohstoffMinen[$gebTyp]++;
		return $rohstoffMinen;
	}

	public function gebeude() {
		if (!isset(self::$land_typen[$this->get('typ')])) {
			self::loadLandTypen();
		}
		if (!isset(self::$land_typen[$this->get('typ')])) {
			x('landGebäude eines Landstückes dessen '.
						 'Typ '.$this->get('typ').' ist, können '.
						 'nicht abgefragt werden.');
			return NULL;
		}
		return explode(':',self::$land_typen[$this->get('typ')]);
	}

	public function __toString() {
		return 'Land: '.$this->get('x').' | '.$this->get('y').', Oase: '.$this->get('oase');
	}

	public function get($att) {
		return $this->data[$att];
	}

	public function set($att,$value) {
		$this->changed=true;
		$this->data[$att]=$value;
	}

	public function save() {
		$sql="UPDATE tr".ROUND_ID."_".self::$db_table." SET ";
		foreach($this->data as $key => $value) {
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

	public static function getByXY($x,$y) {
		if (!isset(self::$objekte[$x][$y])) {
			self::loadEntry($x,$y);
		}
		return @self::$objekte[$x][$y];
	}

	public static function anzLandTypen() {
		if (!isset(self::$land_typen)) self::loadLandTypen();
		return sizeof(self::$land_typen);
	}

	public static function findFree() {
		$map=Diverses::startPos();
		$anz=0;
		$felder=($map['xmax']-$map['xmin'])*
				($map['ymax']-$map['ymin']);
		do {
			$x=mt_rand($map['xmin'],$map['xmax']);
			$y=mt_rand($map['ymin'],$map['ymax']);
			$anz++;
			if ($anz>=$felder*2) return NULL;
			$free=self::isFree($x,$y);
		} while (!$free);
		return array('x'=>$x,'y'=>$y);
	}

	public static function isFree($x,$y) {
		$dorf=Dorf::getByXY($x,$y);
		$land=self::getByXY($x,$y);
		return $dorf==NULL && $land->get('oase')==0;
	}

	public static function saveAll() {
		if (self::$objekte!=NULL) {
			foreach(self::$objekte as $x => $arr) {
				foreach($arr as $y => $objekt) {
					if ($objekt->changed) {
						$objekt->save();
					}
				}
			}
		}
	}

	//Erstellt eine völlig neue Landkarte der Grösse $sizeX*$sizeY
	//$oasen gibt die Häufigkeit von Oasen an %
	//mit $typ kann die wkeit von typen verändert werden
	//Z.B: typ=array(6=>20) damit erhöht man die wkeit der 15er
	//     getreide felder auf 20%
	public static function createNewMap($sizeX,$sizeY,$oasen,$typ) {
		$dorf_koords=Dorf::getAllKoords();
		self::deleteAll();
		self::loadLandTypen();
		$wkeiten=self::verteileWkeit($typ);
		for($x=1;$x<=$sizeX;$x++) {
			for($y=1;$y<=$sizeY;$y++) {
				$oase=wahrscheinlichkeit($oasen);
				if ($oase==1) {
					if ($dorf_koords[$x][$y]) $oase=0;
				}
				$aussehen=0;
				if ($oase==1)	$typ=mt_rand(1,12);
				else {
					$typ=wkeitVerteilung($wkeiten);
					if ($typ==6) $aussehen=4;
					else {
						$aussehen=mt_rand(0,8);
						if ($aussehen>3) $aussehen++;
					}
				}
				self::create($x,$y,$oase,$typ,$aussehen);
			}
		}

	}

	protected static function loadEntry($x,$y) {
		if (!isset(self::$objekte[$x][$y])) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE x=$x AND y=$y;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			self::$objekte[$x][$y]=new Land($x,$y,$data);
		}
	}

	protected static function verteileWkeit($typen) {
		$wtot=100;
		$anz=sizeof(self::$land_typen);
		foreach($typen as $typ => $wkeit) {
			if (!is_numeric($wkeit)) {
				$wkeit = floatval($wkeit);
			}
			$wtot-=$wkeit;
			$anz--;
		}
		$wkeit_andere=$wtot/$anz;
		foreach(self::$land_typen as $typ => $gebs) {
			if (!isset($typen[$typ])) {
				$typen[$typ]=$wkeit_andere;
			}
		}
		return $typen;
	}

	private static function create($x,$y,$oase,$typ,$aussehen) {
		$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."
				(`x`,`y`,`oase`,`typ`,`aussehen`)
			VALUES ('$x','$y','$oase','$typ','$aussehen');";
		mysql_query($sql);
	}

	private static function deleteAll() {
		self::$objekte=NULL;
		self::$land_typen=NULL;
		self::$sizeX=NULL;
		self::$sizeY=NULL;
		$sql="DELETE FROM `tr".ROUND_ID."_lander`;";
		mysql_query($sql);
	}

	private static function loadLandTypen() {
		if (!isset(self::$land_typen)) {
			$sql="SELECT * FROM tr".ROUND_ID."_land_typen;";
			$result=mysql_query($sql);
			self::$land_typen=array();
			while ($data=mysql_fetch_assoc($result)) {
				self::$land_typen[$data['typ']]=$data['geb'];
			}
		}
	}


}
?>
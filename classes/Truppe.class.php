<?php


class Truppe {

	//Key
	private $x;
	private $y;
	private $userid;
	private $gefangen;

	private $data;
	private $changed;
	private $created;

	protected static $objekte;	// [$x][$y][$userid][$gefangen]
	protected static $loaded;	// [$x][$y]

	protected static $db_key=array('x','y','user','gefangen');
	protected static $db_table='truppen';

	public static $save=true;

	protected function Truppe($x,$y,$userid,$gefangen,$data) {
		$this->x = $x;
		$this->y = $y;
		$this->userid = $userid;
		$this->data = $data;
		$this->changed=false;
		$this->gefangen=$gefangen;
		self::$objekte[$x][$y][$userid][$gefangen]=$this;
	}

	public function get($att) {
		return $this->data[$att];
	}

	public function set($att,$value) {
		$this->data[$att]=$value;
		$this->changed=true;
	}
	
	public function volk() {
		if($this->userid>0) {
			$user=Spieler::getById($this->userid);
			$volk=$user->get('volk');
		}
		else
			$volk=4;
		return $volk;
	}

	public function anzSoldaten() {
		$soldaten=explode(':',$this->get('troops'));
		return array_sum($soldaten);
	}

	public function soldatenId() {
		$volk=$this->volk();
		$soldaten=explode(':',$this->get('troops'));
		foreach ($soldaten as $nr => $anz) {
			$id=$nr+1+($volk-1)*10;
			if ($nr==10) $id='hero';
			$result[$id]=$anz;
		}
		return $result;
	}

	public function soldatenNr() {
		$soldaten=explode(':',$this->get('troops'));
		return $soldaten;
	}

	public function leer() {
		return $this->anzSoldaten()==0;
	}

	public function getVersorgung() {
		return TruppenTyp::getVersorgung($this->soldatenId());
	}

	public function hinzufugen($soldaten) {
		$aktuell=explode(':',$this->get('troops'));
		if (is_array($soldaten)) {
			foreach ($soldaten as $id => $anzahl) {
				if ($id=='hero')
					$nr=10;
				else
					$nr=($id-1)%10;
				if (!isset($aktuell[$nr])) $aktuell[$nr]=0;
				$aktuell[$nr]+=$anzahl;
			}
		}
		if (is_string($soldaten)) {
			$soldaten_arr=explode(':',$soldaten);
			foreach($soldaten_arr as $nr => $anzahl) {
				$aktuell[$nr]+=$anzahl;
			}
		}
		$this->set('troops',implode(':',$aktuell));
		$this->changed=true;
	}

	public function entfernen($soldaten) {
		$aktuell=explode(':',$this->get('troops'));
		if (is_array($soldaten)) {
			foreach ($soldaten as $id => $anzahl) {
				if ($id=='hero')
					$nr=10;
				else
					$nr=($id-1)%10;
				if (!isset($aktuell[$nr])) $aktuell[$nr]=0;
				$aktuell[$nr]-=$anzahl;
			}
		}
		if (is_string($soldaten)) {
			$soldaten_arr=explode(':',$soldaten);
			foreach($soldaten_arr as $nr => $anzahl) {
				$aktuell[$nr]-=$anzahl;
			}
		}
		$this->set('troops',implode(':',$aktuell));
		$this->changed=true;
	}

	public function __toString() {
		if ($this->get('user')!=0)
			$name=Spieler::getById($this->get('user'))->get('name');
		else
			$name='Natur ('.$this->get('user').')';
		return 'Truppe: von '.$name.',
			Standort: ('.$this->get('x').' | '.$this->get('y').'),
			Soldaten: '.$this->get('troops').', Gefangen: '.$this->get('gefangen').',
			Ursprung: ('.$this->get('ursprung_x').' | '.$this->get('ursprung_y').')';
	}
	
	public function toHtmlBox($user_viewing,$dorf_viewing,$special=null) {
		$dorf=Dorf::getByXY($this->x,$this->y);
		$owner=Spieler::getById($this->userid);
		if ($user_viewing==$owner) {
			if ($dorf==$dorf_viewing)
				$title="Eigene Truppen";
			else {
				$u=$dorf->user();
				$title='Unterstützung für '.$u->getLink();
			}
		}
		else{
			$title='Truppen von '.$owner->getLink();
		}
		$volk=$this->volk();
		$units=$this->soldatenId();
		$supply=$this->getVersorgung();
		$arrival=null;
		$out=Outputer::truppenBox($dorf,$dorf_viewing,$title,$volk,$units,$supply,$arrival,$special);
		return $out;
	}

	public function save() {
		if ($this->created==false) {
			$sql="UPDATE tr".ROUND_ID."_".self::$db_table." SET ";
			foreach($this->data as $key => $value) {
				$sql.="$key='$value',";
			}
			$sql=substr($sql,0,-1);
			$sql.=" WHERE ";
			foreach(self::$db_key as $key) {
				$sql.="$key='".$this->data[$key]
					."' AND ";
			}
			$sql=substr($sql,0,-4);
		}
		else {
			unset($keys,$values);
			$keys='';$values='';
			foreach($this->data as $key => $value) {
				$keys.=$key.',';
				$values.="'$value',";
			}
			$keys=substr($keys,0,-1);
			$values=substr($values,0,-1);
			$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."
				($keys) VALUES ($values);";
		}
		mysql_query($sql);
	}

	public static function getString($soldatenId) {
		foreach($soldatenId as $id=>$anz) {
			if ($id!='hero')
				$soldaten[($id-1)%10]=$anz;
		}
		for ($i=0;$i<10;$i++){
			if(!isset($soldaten[$i])) {
				$soldaten[$i]=0;
			}
		}
		ksort($soldaten);
		$soldaten[11]=$soldatenId['hero'];
		return implode(':',$soldaten);
	}

	public static function saveAll() {
		if (self::$objekte!==NULL) {
			foreach (self::$objekte as $x => $arr) {
				foreach ($arr as $y => $arr2) {
					foreach ($arr2 as $userid => $arr3) {
						foreach($arr3 as $gefangen => $objekt) {
							if ($objekt->changed) {
								$objekt->save();
							}
						}
					}
				}
			}
		}
	}

	//user der einheiten ausgeben
	public static function getByXYU($x,$y,$user,$gefangen=0) {
		if (is_object($user))
			$id=$user->get('id');
		elseif (is_int($user) or $user==(int)$user)
			$id=(int)$user;
		else x('Truppe::getByXYU(x,y,user,gefangen=0): user nicht gefunden',$user);
		if (!isset(self::$loaded[$x][$y])) {
			self::loadEntry($x,$y);
		}
		if (!isset(self::$objekte[$x][$y][$id][$gefangen])) {
			self::createEntry($x,$y,$id,$gefangen);
		}
		return self::$objekte[$x][$y][$id][$gefangen];
	}

	public static function getByDU($dorf,$user,$gefangen=0) {
		return self::getByXYU($dorf->get('x'),$dorf->get('y'),$user,$gefangen);
	}

	//Liefert User IDs von den Truppen
	// (mit mindestens 1 Soldat) zurück
	public static function getUsersByD($dorf,$gefangen=0) {
		$x=$dorf->get('x');
		$y=$dorf->get('y');
		return self::getUsersByXY($x,$y,$gefangen);
	}

	//Liefert User IDs von den Truppen
	// (mit mindestens 1 Soldat) zurück
	public static function getUsersByXY($x,$y,$gefangen=0) {
		if (@!self::$loaded[$x][$y]) {
			self::loadEntry($x,$y);
		}
		if (!isset(self::$objekte[$x][$y])) return array();
		$users=array_keys(self::$objekte[$x][$y]);
		$result=array();
		foreach($users as $userid) {
			if (!Truppe::getByXYU($x,$y,$userid,$gefangen)->leer())
				array_push($result,$userid);
		}
		return $result;
	}

	protected static function createEntry($x,$y,$userid,$gefangen=0) {
		if (!isset(self::$objekte[$x][$y][$userid][$gefangen])) {
			$t=new Truppe($x,$y,$userid,$gefangen,
				array('x'=>$x,'y'=>$y,'user'=>$userid,'gefangen'=>$gefangen,
					  'troops'=>'0:0:0:0:0:0:0:0:0:0','ursprung_x'=>$x,'ursprung_y'=>$y));
			$t->created=true;
		}
	}

	protected static function loadEntry($x,$y) {
		if (@!self::$loaded[$x][$y]) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE x=$x AND y=$y;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			while ($data=mysql_fetch_assoc($result)) {
				$x=$data['x'];$y=$data['y'];$u=$data['user'];
				$gefangen=$data['gefangen'];
				$t=new Truppe($x,$y,$u,$gefangen,$data);
				$t->created=false;
			}
			self::$loaded[$x][$y]=true;
		}
	}
}

?>
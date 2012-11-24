<?php


class Truppe extends Soldaten {
	public static $save=true;

	//Key
	private $x;
	private $y;
	private $userid;
	private $gefangen;

	private $data;
	private $created;

	protected static $objekte;	// [$x][$y][$userid][$gefangen]
	protected static $loaded;	// [$x][$y]

	protected static $db_key=array('x','y','user','gefangen');
	protected static $db_table='truppen';


	protected function Truppe($x,$y,$userid,$gefangen,$data) {
		$this->x = $x;
		$this->y = $y;
		$this->userid = $userid;
		$this->data = $data;
		$this->changed=false;
		$this->gefangen=$gefangen;
		self::$objekte[$x][$y][$userid][$gefangen]=$this;
		$this->initTruppe();
	}
	
	private function initTruppe() {
		if($this->userid > 0){
			$user=Spieler::getById($this->userid);
			$volk=$user->get('volk');
		}
		else
			$volk=4;
		$soldaten=explode(':',$this->data['troops']);
		if (isset($soldaten[10]) && $soldaten[10]!=0){
			$held=$user->held();
		}
		else
			$held=null;
		parent::__construct($volk,$soldaten,$held);
	}

	public function get($att) {
		if ($att=='truppen')
			return parent::soldatenString();
		return $this->data[$att];
	}

	public function set($att,$value) {
		if ($att=='troops')
			x('Not allowed to change troops attribute for object of class Truppe');
		$this->data[$att]=$value;
		$this->changed=true;
	}
	
	public function __toString() {
		if ($this->get('user')!=0)
			$name=Spieler::getById($this->get('user'))->get('name');
		else
			$name='Natur';
		return 'Truppe: von '.$name.',
			Standort: ('.$this->get('x').' | '.$this->get('y').'),
			Soldaten: '.parent::__toString().', Gefangen: '.$this->get('gefangen').',
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
			$this->data['troops']=$this->get('troops');
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
			$this->data['troops']=$this->get('troops');
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
<?php

class TruppeMove {

	protected $keyid;
	protected $data;

	protected $changed;

	protected static $objekte_user;  //[$user][$keyid]
	protected static $objekte_start; //[$x][$y][$nr]
	protected static $objekte_ziel;  //[$x][$y][$nr]

	protected static $loaded_user=array();
	protected static $loaded_start=array();
	protected static $loaded_ziel=array();

	protected static $db_key=array('id');
	protected static $db_table='truppen_move';

	public static $save=true;

	public function TruppeMove($keyid,$d) {
		$this->keyid=$keyid;
		$this->data=$d;
		$this->changed=false;

		//Den 1. User hinzufügen
		$user1=$this->startDorf()->get('user');
		if (self::$objekte_user[$user1]===NULL)
			self::$objekte_user[$user1]=array();
		if (!arrayObjectsContains(
				self::$objekte_user[$user1],'keyid',$keyid))
			array_push(self::$objekte_user[$user1],$this);

		//Den 2. User hinzufügen
		if ($this->zielDorf()!==NULL) {
			$user2=$this->zielDorf()->get('user');
			if (self::$objekte_user[$user2]===NULL)
				self::$objekte_user[$user2]=array();
			if (!arrayObjectsContains(
					self::$objekte_user[$user2],'keyid',$keyid))
				array_push(self::$objekte_user[$user2],$this);
		}

		//Start Dorf hinzufügen
		if (self::$objekte_start[$d['start_x']][$d['start_y']]===NULL)
			self::$objekte_start[$d['start_x']][$d['start_y']]=array();
		if (!arrayObjectsContains(self::$objekte_start
					[$d['start_x']][$d['start_y']],'keyid',$keyid))
			array_push(self::$objekte_start[
									$d['start_x']][$d['start_y']],$this);

		//Ziel Dorf hinzufügen
		if (self::$objekte_ziel[$d['ziel_x']][$d['ziel_y']]===NULL)
			self::$objekte_ziel[$d['ziel_x']][$d['ziel_y']]=array();
		if (!arrayObjectsContains(self::$objekte_ziel
					[$d['ziel_x']][$d['ziel_y']],'keyid',$keyid))
			array_push(self::$objekte_ziel[
									$d['ziel_x']][$d['ziel_y']],$this);
	}

	public function soldatenId() {
		$user=Spieler::getById($this->get('user'));
		$volk=$user->get('volk');
		$soldaten=explode(':',$this->get('truppen'));
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

	public function get($att) {
		return $this->data[$att];
	}

	protected function set($att,$value) {
		$this->data[$att]=$value;
		$this->changed=true;
	}

	public function startDorf() {
		return Dorf::getByXY($this->get('start_x'),$this->get('start_y'));
	}

	public function zielDorf() {
		return Dorf::getByXY($this->get('ziel_x'),$this->get('ziel_y'));
	}

	private function save() {
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

	//Testet ob der User diese Aktion auf dieses Ziel ausführen
	// kann.
	public static function zielbar($zx,$zy,$user,$aktion) {
		$zielDorf=Dorf::getByXY($zx,$zy);
		//Besiedeln
		if ($aktion==1) {
			//kein Dorf vorhanden
			if ($zielDorf!==NULL) return false;
			$land=Land::getByXY($zx,$zy);
			//Kein Ödland
			if ($land===NULL) return false;
			return true;
		}
		//Andere Aktion -> Dorf muss vorhanden sein
		if ($zielDorf===NULL) return false;
		//Angriff auf eigenes Dorf nicht möglich
		if ($zielDorf->get('user')==$user->get('id')) {
			if ($aktion>2) return false;
		}
		return true;
	}

	//User sollte ein Spieler sein
	//Aktion: 1=Neues Dorf gründen 2=Unterstützen 3=normal 4=Raubzug
	//        5=spionage
	public static function create($startDorf,$zx,$zy,$user,$aktion,
																$soldaten,$ress=NULL) {
		if ($ress===NULL) $ress=array(0,0,0,0);
		//Genug Ress vorhanden
		if ($startDorf->genugRess($ress)) {
			//Genug Soldaten vorhanden
			if ($startDorf->genugSoldaten($user,$soldaten)) {
				//Aktion auf Ziel durchführbar
				if (self::zielbar($zx,$zy,$user,$aktion)) {
					$weg=sqrt(pow($zx-$startDorf->get('x'),2)+
										pow($zy-$startDorf->get('y'),2));
					$speed=self::maxSpeed($soldaten,$user);
					if ($weg>=Diverses::get('turnierplatz_ausdauer')) {
						$speed*=1+self::speedBoni($startDorf,$zx,$zy,$user);
					}
					$dauer=$weg/$speed*3600;
					$time=date('Y-m-d H:i:s',time()+$dauer);

					//Rohstoffe abziehen
					$startDorf->subRess($ress);
					//Einheiten abziehen
					$truppe=Truppe::getByDU($startDorf,$user);
					$truppe->entfernen($soldaten);
					$tstring=Truppe::getString($soldaten);
					
					//Truppe losschicken
					$sql="INSERT INTO `tr".ROUND_ID."_truppen_move`
							(`user`,`start_x`,`start_y`,`ziel_x`,`ziel_y`,
							 `start_zeit`,`ziel_zeit`,`aktion`,`truppen`)
						VALUES ('".$user->get('id')."',".$startDorf->get('x').",
										".$startDorf->get('y').",'$zx','$zy','".now()."',
										'$time',$aktion,'$tstring');";
					mysql_query($sql);
					$keyid=mysql_insert_id();

					$sql="SELECT * FROM `tr".ROUND_ID."_truppen_move`
						WHERE keyid=$keyid;";
					self::sqlQuery($sql);
				}
			}
		}
	}

	public static function speedBoni($start,$zx,$zy,$user) {
		$uid=$user->get('id');
		if ($start->get('user')==$uid) {
			$highest=$start->highest();
			return $highest[14]*0.1;
		}
		$ziel=Dorf::getByXY($zx,$zy);
		if ($ziel!==NULL) {
			if ($ziel->get('user')==$uid) {
				$highest=$ziel->highest();
				return $highest[14]*0.1;
			}
			new Errorlog('TruppeMove::speedBoni $ziel!=null but user is'.
									 ' not found');
		}
	}

	//Soldaten = array ( $id => $anz )
	public static function maxSpeed($soldaten,$user=null) {
		foreach($soldaten as $id=>$anz){
			if($anz>0){
				if ($id=='hero')
					$einheit=$user->held()->getTruppenTyp();
				else
					$einheit=TruppenTyp::getById($id);
				$s=$einheit->get('speed');
				if (!isset($speed)) $speed=$s;
				if ($s<$speed) $speed=$s;
			}
		}
		return $speed;
	}

	public static function getByUser($user) {
		if (is_object($user))
			$id=$user->get('id');
		elseif (is_int($user) or (int)$user==$user)
			$id=(int)$user;
		else {
			x('TruppeMove::getByUser('.$user.') hat user nicht erkannt.',$user);
		}
		if (!self::$loaded_user[$id]) {
			self::loadByUser($id);
		}
		return self::$objekte_user[$id];
	}

	protected static function loadByUser($user) {
		$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
			WHERE user=$user;";
		self::sqlQuery($sql);
		if (self::$objekte_user[$user]===NULL)
			self::$objekte_user[$user]=array();
		self::$loaded_user[$user]=true;
	}

	public static function getByZiel($x,$y) {
		if (!@self::$loaded_ziel[$x][$y])
			self::loadByZiel($x,$y);
		return @self::$objekte_ziel[$x][$y];
	}

	protected static function loadByZiel($x,$y) {
		$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
			WHERE ziel_x=$x AND ziel_y=$y;";
		self::sqlQuery($sql);
		if (@self::$loaded_ziel[$x][$y]===NULL)
			self::$loaded_ziel[$x][$y]=array();
		self::$loaded_ziel[$x][$y]=true;
	}

	public static function getByStart($x,$y) {
		if (!@self::$loaded_start[$x][$y])
			self::loadByStart($x,$y);
		return @self::$objekte_start[$x][$y];
	}

	protected static function loadByStart($x,$y) {
		$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
			WHERE start_x=$x AND start_y=$y;";
		self::sqlQuery($sql);
		if (@self::$loaded_start[$x][$y]===NULL)
			self::$loaded_start[$x][$y]=array();
		self::$loaded_start[$x][$y]=true;
	}

	protected static function sqlQuery($sql) {
		$result=mysql_query($sql);
		while ($data=mysql_fetch_assoc($result)) {
			new TruppeMove($data['keyid'],$data);
		}
	}

	public static function saveAll() {
		if (self::$objekte_start!==NULL) {
			foreach(self::$objekte_start as $x=>$arr) {
				foreach($arr as $y => $arr2) {
					foreach($arr2 as $nr => $truppenmove){
						if ($truppenmove->changed)
							$truppenmove->save();
					}
				}
			}
		}
	}
}




?>
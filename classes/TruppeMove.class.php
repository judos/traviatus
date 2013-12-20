<?php

class TruppeMove extends Soldaten{
	public static $save=true;
	
	const TYP_BESIEDLUNG     = 1;
	const TYP_UNTERSTUETZUNG = 2;
	const TYP_ANGRIFF        = 3;
	const TYP_RAUBZUG        = 4;
	const TYP_AUSSPAEHEN     = 5;

	protected $keyid;
	protected $data;

	protected static $objekte_user;  //[$user][$keyid]
	protected static $objekte_start; //[$x][$y][$nr]
	protected static $objekte_ziel;  //[$x][$y][$nr]
	protected static $objekte_id;    //[$keyid]

	protected static $loaded_user=array();
	protected static $loaded_start=array();
	protected static $loaded_ziel=array();
	protected static $loaded_id=array();

	protected static $db_key=array('keyid');
	protected static $db_table='truppen_move';


	public function TruppeMove($keyid,$d) {
		$this->keyid=$keyid;
		$this->data=$d;
		$this->changed=false;
		$this->initTruppeMove();
		$this->addToReferences();
	}
	
	private function initTruppeMove() {
		$user=Spieler::getById($this->get('user'));
		$volk=$user->get('volk');
		$soldatenNr=explode(':',$this->data['truppen']);
		if (isset($soldatenNr[10]) && $soldatenNr[10]!=0){
			$held=$user->held();
		}
		else
			$held=null;
		$soldatenIds = TruppenTyp::arrIndexNrsToIds($soldatenNr,$volk);
		parent::__construct($volk,$soldatenIds,$held);
	}
	
	public function getRess() {
		if($this->get('ress')=='')
			return array(0,0,0,0);
		return explode(':',$this->get('ress'));
	}
	
	public function addRess($additionalRess) {
		$ress=$this->getRess();
		foreach($ress as $nr=> $amount)
			$ress[$nr] += $additionalRess[$nr];
		$this->set('ress',implode(':',$ress));
	}
	
	public function getHerkunft() {
		return $this->startDorf();
	}
	
	protected function addToReferences() {
		$keyid=$this->keyid;
		$d=$this->data;
		//Den 1. User hinzufügen
		$user1=$this->startDorf()->get('user');
		if (!isset(self::$objekte_user[$user1]))
			self::$objekte_user[$user1]=array();
		if (!isset(self::$objekte_user[$user1][$keyid]))
			array_push(self::$objekte_user[$user1],$this);

		//Den 2. User hinzufügen
		if ($this->zielDorf()!==NULL) {
			$user2=$this->zielDorf()->get('user');
			if (!isset(self::$objekte_user[$user2]))
				self::$objekte_user[$user2]=array();
			if (!isset(self::$objekte_user[$user2][$keyid]))
				array_push(self::$objekte_user[$user2],$this);
		}

		//Start Dorf hinzufügen
		if (@self::$objekte_start[$d['start_x']][$d['start_y']]===NULL)
			self::$objekte_start[$d['start_x']][$d['start_y']]=array();
		if (!arrayObjectsContains(self::$objekte_start
					[$d['start_x']][$d['start_y']],'keyid',$keyid))
			array_push(self::$objekte_start[
									$d['start_x']][$d['start_y']],$this);

		//Ziel Dorf hinzufügen
		if (@self::$objekte_ziel[$d['ziel_x']][$d['ziel_y']]===NULL)
			self::$objekte_ziel[$d['ziel_x']][$d['ziel_y']]=array();
		if (!arrayObjectsContains(self::$objekte_ziel
					[$d['ziel_x']][$d['ziel_y']],'keyid',$keyid))
			array_push(self::$objekte_ziel[
									$d['ziel_x']][$d['ziel_y']],$this);
		
		//ID hinzufügen
		if (!isset(self::$objekte_id[$keyid]))
			self::$objekte_id[$keyid]=$this;
	}
	
	public function setNumbers($soldatenIds,$held) {
		$this->soldaten=$soldatenIds;
		if (isset($soldatenIds['held']) and $soldatenIds['hero']==1){
			$this->held=$held;
			if ($held==null)
				x('setNumbers erwartet ein Held objekt falls dieser in der Truppe sein soll.');
		}
		$this->changed=true;
	}

	public function get($att) {
		if ($att=='truppen')
			return parent::soldatenString();
		return $this->data[$att];
	}

	protected function set($att,$value) {
		if($att=='truppen')
			x('Es ist nicht erlaubt das Attribut truppen für ein Objekt der Klasse TruppeMove zu ändern.');
		$this->data[$att]=$value;
		$this->changed=true;
	}

	public function startDorf() {
		return Dorf::getByXY($this->get('start_x'),$this->get('start_y'));
	}

	public function zielDorf() {
		return Dorf::getByXY($this->get('ziel_x'),$this->get('ziel_y'));
	}
	
	public function getUser() {
		return Spieler::getById($this->get('user'));
	}
	
	public function delete() {
		$sql="DELETE FROM tr".ROUND_ID."_truppen_move
			WHERE keyid='".$this->keyid."';";
		mysql_query($sql);
		$this->deleteReference();
	}
	
	protected function deleteReference() {
		self::$objekte_id[$this->keyid]=null;
		
		$user1=$this->startDorf()->get('user');
		unset(self::$objekte_user[$user1][$this->keyid]);
		if ($this->zielDorf()!==NULL) {
			$user2=$this->zielDorf()->get('user');
			unset(self::$objekte_user[$user1][$this->keyid]);
		}
		$sx=$this->get('start_x');
		$sy=$this->get('start_y');
		$zx=$this->get('ziel_x');
		$zy=$this->get('ziel_y');
		arrayObjectsDelete(self::$objekte_start[$sx][$sy],'keyid',$this->keyid);
		arrayObjectsDelete(self::$objekte_ziel[$zx][$zy],'keyid',$this->keyid);
	}
	
	public function turnBack() {
		$this->deleteReference();
		$z=strtotime($this->get('ziel_zeit'));
		$s=strtotime($this->get('start_zeit'));
		$start_zeit=$this->get('ziel_zeit');
		$ziel_zeit=date('Y-m-d H:i:s',2*$z-$s);
		
		//swap start with ziel
		list($this->data['start_x'],$this->data['ziel_x']) =
			array($this->data['ziel_x'],$this->data['start_x']);
		list($this->data['start_y'],$this->data['ziel_y']) =
			array($this->data['ziel_y'],$this->data['start_y']);
		//change other values
		$this->set('aktion',2);
		$this->set('msg',0);
		$this->set('start_zeit',$start_zeit);
		$this->set('ziel_zeit',$ziel_zeit);
		
		$this->addToReferences();
		//must save in order to prevent duplicated object is loaded via loadByStart/Ziel
		$this->save();
	}
	
	public function toHtmlBox() {
		$b=new InfoMessage();
		Outputer::troopMoveTitle($this,$b);
		$b->addPartUnitTypes($this->getUser()->get('volk'));
		$b->addPartUnitCount('Einheiten',$this->soldaten);
		if (array_sum($this->getRess())>0)
			$b->addPartRess("Rohstoffe",$this->getRess());
			
		$b->addPartTimeArrival($this->get('ziel_zeit'));
		return $b->toHtml();
	}

	private function save() {
		$sql="UPDATE tr".ROUND_ID."_".self::$db_table." SET ";
		$this->data['truppen']=$this->get('truppen');
		foreach($this->data as $key => $value) {
			$sql.="$key='$value',";
		}
		$sql=substr($sql,0,-1);
		$sql.=" WHERE ";
		foreach(self::$db_key as $att) {
			$sql.="$att='".$this->get($att)
				."' AND ";
		}
		$sql=substr($sql,0,-4);
		mysql_query($sql);
	}
	
	public function __toString() {
		return $this->toHtmlBox();
	}

	//Testet ob der User diese Aktion auf dieses Ziel ausführen
	// kann.
	public static function zielbar($zx,$zy,$user,$aktion) {
		$zielDorf=Dorf::getByXY($zx,$zy);
		//Besiedeln
		if ($aktion==self::TYP_BESIEDLUNG) {
			//kein Dorf vorhanden
			if ($zielDorf!==NULL) return false;
			$land=Land::getByXY($zx,$zy);
			//Land muss vorhanden sein, kein Ödland
			if ($land===NULL) return false;
			return true;
		}
		//Andere Aktion -> Dorf muss vorhanden sein
		if ($zielDorf===NULL) return false;
		//Angriff auf eigenes Dorf nicht möglich
		if ($zielDorf->get('user')==$user->get('id')) {
			if (self::aktionIstFeindlich($aktion))
				return false;
		}
		return true;
	}
	
	public static function aktionIstFeindlich($aktion) {
		if ($aktion==self::TYP_ANGRIFF)
			return true;
		if ($aktion==self::TYP_RAUBZUG)
			return true;
		if ($aktion==self::TYP_AUSSPAEHEN)
			return true;
		return false;
	}
	
	public function getSpaher() {
		$sum=0;
		$soldaten = $this->soldatenIdWithoutHero();
		foreach($soldaten as $id => $anz) {
			if (TruppenTyp::getById($id)->isSpy())
				$sum+=$anz;
		}
		return $sum;
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
					$tstring=implode(':',Soldaten::soldatenNr($soldaten));
					
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

	//$zx,$zy = zielDorf
	public static function speedBoni($startDorf,$zx,$zy,$user) {
		$uid=$user->get('id');
		if ($startDorf->get('user')==$uid) {
			$highest=$startDorf->highest();
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
		if (!isset(self::$loaded_user[$id])) {
			self::loadByUser($id);
		}
		return self::$objekte_user[$id];
	}
	
	public static function getById($id) {
		if (!isset(self::$loaded_id[$id]))
			self::loadById($id);
		return self::$objekte_id[$id];
	}

	protected static function loadByUser($user) {
		$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
			WHERE user=$user;";
		self::sqlQuery($sql);
		if (self::$objekte_user[$user]===NULL)
			self::$objekte_user[$user]=array();
		self::$loaded_user[$user]=true;
	}
	
	protected static function loadById($id) {
		$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
			WHERE keyid=$id;";
		self::sqlQuery($sql);
		self::$loaded_id[$id]=true;
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
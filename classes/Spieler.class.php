<?php


class Spieler {

	protected $id;
	protected $data;
	protected $changed;
	protected $attChanged;
	protected $rang;
	protected $dorfer;
	protected $anzDorfer;
	protected $einladungen;

	protected static $objekte = array();	// [$id] => $spieler  oder auch [$name] => $spieler
	protected static $db_key=array('id');
	protected static $db_table='user';

	public static $save=true;

	protected static $anz;
	protected static $volker;

	protected function __construct($id,$data) {
		$this->id = $id;
		$this->data=$data;
		$this->changed=false;
		foreach($this->data as $key=>$value)
			$this->attChanged[$key]=false;
		if (isset(self::$objekte[$id]))
			new Errorlog('new Spieler, Spieler existierte bereits.');
		self::$objekte[$id]=$this;
		self::$objekte[$data['name']]=$this;
	}
	
	public function getAllianz() {
		$allyId=$this->get('ally');
		if ($allyId==0)
			return null;
		else
			return Allianz::getById($allyId);
	}
	
	// [$bold:boolean]
	public function getLink() {
		$a=func_get_args();
		$bold= (isset($a[0])? $a[0] : false);
		$b1=($bold?'<b>':'');
		$b2=($bold?'</b>':'');
		return '<a href="?page=spieler&name='.$this->data['name'].'">'.
			$b1.$this->data['name'].$b2.'</a>';
	}

	public function held() {
		return Held::getAliveOrNonByUser($this);
	}

	public function loscheEinladung($id) {
		if ($id=='all') {
			$this->einladungen=array();
			$sql="DELETE FROM tr".ROUND_ID."_ally_einladungen
				WHERE user_id=".$this->get('id').";";
			mysql_query($sql);
		}
		else {
			unset($this->einladungen);
			$sql="DELETE FROM tr".ROUND_ID."_ally_einladungen
				WHERE user_id=".$this->get('id')." AND ally_id=$id;";
			mysql_query($sql);
		}
	}

	public function allianzEinladungen() {
		if (!isset($this->einladungen)) {
			$sql="SELECT ally_id FROM tr".ROUND_ID."_ally_einladungen
				WHERE user_id=".$this->get('id').";";
			$result=mysql_query($sql);
			$this->einladungen=array();
			while ($data=mysql_fetch_assoc($result)) {
				array_push($this->einladungen,Allianz::getById($data['ally_id']));
			}
		}
		return $this->einladungen;
	}

	//1=letzte 5min aktiv
	//2=letzte 12h aktiv
	//3=letzte 3tage aktiv
	//4=letzte 7tage aktiv
	//5=inaktiv
	//last_update wird aktualisiert falls login geprüft wird
	// (bei jedem Aufruf einer Seite)
	public function onlineStatus() {
		$x=time()-strtotime($this->get('last_online'));
		if ($x<5*60) return 1;
		if ($x<12*3600) return 2;
		if ($x<3*24*3600) return 3;
		if ($x<7*24*3600) return 4;
		return 5;
	}

	public function handlerTragfahigkeit($dorf) {
		$volk=$this->get('volk');
		$highest=$dorf->highest();
		if ($volk==1) $tragen=500;
		if ($volk==2) $tragen=1000;
		if ($volk==3) $tragen=750;
		$tragen+=50*$highest[28];
		return $tragen;
	}

	public function handlerSpeed() {
		$volk=$this->get('volk');
		$speed=16;
		if ($volk==2) $speed=12;
		if ($volk==3) $speed=24;
		return $speed;
	}

	public function truppenTypen() {
		$volk=$this->get('volk');
		$typen=array();
		for ($i=1;$i<=10;$i++) {
			$id=$i+($volk-1)*10;
			$typ=TruppenTyp::getById($id);
			$typen[$id]=$typ;
		}
		$held=Held::getAliveOrNonByUser($this);
		if ($held!=null)
			$typen['hero']=$held;
		return $typen;
	}

	public function rang() {
		if (!isset($this->rang)) {
			$sql="SELECT COUNT(`name`) FROM `tr".ROUND_ID."_user`
				WHERE `einwohner`>".$this->get('einwohner').";";
			$result=mysql_query($sql);
			$data=mysql_fetch_assoc($result);
			$this->rang=$data['COUNT(`name`)']+1;
		}
		return $this->rang;
	}

	public function anzDorfer() {
		if (!isset($this->anzDorfer)) {
			if (isset($this->dorfer))
				$this->anzDorfer=sizeof($this->dorfer);
			else {
				$sql="SELECT COUNT(*) as anz
					FROM tr".ROUND_ID."_dorfer
					WHERE user=".$this->id.";";
				$result=mysql_query($sql);
				$data=mysql_fetch_array($result);
				$this->anzDorfer=$data['anz'];
			}
		}
		return $this->anzDorfer;
	}

	public function dorfer() {
		if (!isset($this->dorfer)) {
			$sql="SELECT x,y FROM tr".ROUND_ID."_dorfer
				WHERE user=".$this->id.";";
			$result=mysql_query($sql);
			$this->dorfer=array();
			while ($data=mysql_fetch_array($result)) {
				array_push($this->dorfer,Dorf::getByXY($data['x'],$data['y']));
			}
		}
		return $this->dorfer;
	}

	public function auftrage($typ) {
		$auftrage=Auftrag::getByUser($this);
		return ArrayObjectsContaining($auftrage,'typ',$typ);
	}

	public function update() {
		//Kulturpunkte updaten
		$faktor2=(time()-strtotime($this->get('update')))/(3600*24);
		//Faktor in Produktionstagen
		$neue_kp=$faktor2*$this->get('einwohner')/2;
		$this->set('kps',$this->get('kps')+$neue_kp);
	}

	public function hatNeueNachrichten() {
		$sql="SELECT * FROM `tr".ROUND_ID."_msg`
			WHERE `an`='".$this->get('name')."' AND `neu`='1' AND
				`von`!='' AND `typ`='0';";
		$result=mysql_query($sql);
		return mysql_num_rows($result)>0;
	}

	public function hatNeueBerichte() {
		$sql="SELECT * FROM tr".ROUND_ID."_msg
			WHERE `an`='".$this->get('name')."' AND `neu`='1' AND
				`von`='';";
		$result=mysql_query($sql);
		return mysql_num_rows($result)>0;
	}

	public function __toString() {
		return 'Spieler: '.$this->get('name');
	}

	public function get($att) {
		if ($att=='konfig' and $this->data['konfig']=='')
			$this->set('konfig',self::getStdKonfig());
		return $this->data[$att];
	}

	public function set($att,$value) {
		if ($att=='ally') {
			$ally=Allianz::getById($this->get('ally'));
			$this->data[$att]=$value;
			if ($ally!==NULL) $ally->mitgliederEntlasse($this);
		}
		$this->data[$att]=$value;
		$this->attChanged[$att]=true;
		$this->changed=true;
	}

	public function waffen() {
		return explode(':',$this->get('weapons'));
	}

	public function rustungen() {
		return explode(':',$this->get('arms'));
	}

	public function forschungen() {
		return explode(':',$this->get('research'));
	}

	public function einheitErforscht($id) {
		$volk=$this->get('volk')-1;
		if ($id>$volk*10 && $id<=($volk+1)*10) {}
		else return false;
		$f=$this->forschungen();
		if ($f[($id-1)%10]==1) return true;
		return false;
	}

	private static $konfigHashMap=array('berichte'=>0,'geb_16'=>1,'geb_19'=>2,'geb_20'=>3,
		'geb_21'=>4,'geb_17'=>5,'geb_37'=>6,'geb_25'=>7,'geb_26'=>7,'geb_24'=>8,
		'geb_29'=>9,'geb_30'=>10,'dorf2_stufen_anzeige'=>11);

	public function getKonfig($att) {
		
		$konfig=explode(':',$this->get('konfig'));
		return $konfig[self::$konfigHashMap[$att]];
	}

	public function setKonfig($att,$value) {
		$konfig=explode(':',$this->get('konfig'));
		$konfig[self::$konfigHashMap[$att]]=$value;
		$this->set('konfig',implode(':',$konfig));
	}

	public function erforsche($id) {
		$w=$this->forschungen();
		$w[($id-1)%10]=1;
		$this->set('research',implode(':',$w));
	}

	public function verbessereWaffe($id) {
		$w=$this->waffen();
		$w[($id-1)%10]++;
		$this->set('weapons',implode(':',$w));
	}

	public function verbessereRustung($id) {
		$r=$this->rustungen();
		$r[($id-1)%10]++;
		$this->set('arms',implode(':',$r));
	}

	public function addiereKulturPunkte($anzahl) {
		$this->set('kps',$this->get('kps')+$anzahl);
	}

	public function startDorf() {
		$sql="SELECT x,y FROM tr".ROUND_ID."_dorfer
			WHERE user=".$this->id." AND grosse=1;";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)==0) {
			new Errorlog("Spieler->startDorf hat kein ".
						 "Startdorf gefunden\nSpieler: ".
						 $this->dump());
		}
		$data=mysql_fetch_assoc($result);
		return Dorf::getByXY($data['x'],$data['y']);
	}

	public function delete() {
		$sql="DELETE FROM tr".ROUND_ID."_".self::$db_table."
			WHERE id='".$this->id."';";
		$result=mysql_query($sql);
		unset(self::$objekte[$this->id]);
		unset(self::$objekte[$this->get('name')]);
	}

	public function save() {
		$sql="UPDATE tr".ROUND_ID."_".self::$db_table." SET ";
		foreach($this->data as $key => $value) {
			if ($this->attChanged[$key])
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
		$this->changed=false;
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

	public static function statistikVolker() {
		if (!@isset(self::$volker)) {
			$sql="SELECT COUNT(*) as anz,volk
				FROM `tr".ROUND_ID."_".self::$db_table."`
				GROUP BY volk;";
			$result=mysql_query($sql);
			while($data=mysql_fetch_assoc($result))
				self::$volker[$data['volk']]=$data['anz'];
			for($i=1;$i<=3;$i++)
				if (!@isset(self::$volker[$i])) self::$volker[$i]=0;
		}
		return self::$volker;
	}

	public static function anzahl() {
		if (!@isset(self::$anz)) {
			$sql="SELECT Count(*) as anz
				FROM `tr".ROUND_ID."_".self::$db_table."`;";
			$result=mysql_query($sql);
			$data=mysql_fetch_assoc($result);
			self::$anz=$data['anz'];
		}
		return self::$anz;
	}

	public static function getById($id) {
		if (!array_key_exists($id, self::$objekte)) {
			self::loadEntry($id);
		}
		return self::$objekte[$id];
	}

	public static function getByName($name) {
		if (!array_key_exists($name, self::$objekte)) {
			self::loadEntry($name,'name');
		}
		return self::$objekte[$name];
	}

	protected static function loadEntry($id,$att='id') {
		if (!array_key_exists($id, self::$objekte)) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE $att='$id';";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			new Spieler($data['id'],$data);
		}
	}

	//setzt last_action auf NOW()
	public static function checkLogin($name,$pw) {
		$sql="SELECT id FROM tr".ROUND_ID."_".self::$db_table."
			WHERE name='$name' AND pw='".$pw."';";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)==0) return NULL;
		if (mysql_num_rows($result)>1) {
			new Errorlog("Mehr als 1 Benutzer für login-Daten ".
						 "gefunden\n".
						 "Benutzername: ".$name);
			return NULL;
		}
		$data=mysql_fetch_assoc($result);
		$spieler=Spieler::getById($data['id']);
		$spieler->set('last_online',now());
		return $spieler;
	}

	public static function exists($name) {
		$sql="SELECT name FROM tr".ROUND_ID."_user
			WHERE name='$name';";
		$result=mysql_query($sql);
		return mysql_num_rows($result);
	}
	
	protected static function getStdKonfig() {
		return implode(':',array_fill(0,sizeof(self::$konfigHashMap),'1'));
	}

	public static function create($name,$pw,$email,$volk) {

		$std_konfig=self::getStdKonfig();


		$sql="INSERT INTO tr".ROUND_ID."_user
				(name,pw,email,volk, research,weapons, 
				arms,ally,ally_rang,konfig,last_update)
			VALUES
				('$name','$pw','$email','$volk',
			 	'1:0:0:0:0:0:0:0:0:0','0:0:0:0:0:0:0:0:0:0',
				'0:0:0:0:0:0:0:0:0:0', 0,0, '$std_konfig', '".now()."');";
		$result=mysql_query($sql);
		return self::getById(mysql_insert_id());
	}

}

?>
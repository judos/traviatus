<?php


class Held {
	//TODO: refactor to support simulation only
	protected $id;
	protected $data;

	protected $changed;

	protected $typWerte;

	protected static $objekte;
	protected static $loaded;
	protected static $ob_user; //[$user] -> array of heroes
	protected static $loaded_user;

	protected static $db_key=array('keyid');
	protected static $db_table='hero';

	public static $save=true;

	public function Held($id,$data) {
		$this->id = $id;
		$this->data=$data;
		$this->changed=false;
		if (isset(self::$objekte[$id]))
			new Errorlog('new '.get_class($this).'. Objekt existiert bereits.
					Gefahr der Überschreibung.');
		self::$objekte[$id]=$this;
		self::$loaded[$id]=true;

		$user=$data['user'];
		if (!ArrayObjectsContains(self::$ob_user[$user],'keyid',$id))
			array_push(self::$ob_user[$user],$this);
	}
	
	public function resetBoni() {
		if ($this->level()==0) {
			$this->set('bonus','0:0:0:0:0');
		}
	}
	
	public function addPoint($index) {
		if ($this->freiePunkte()>0) {
			$boni=$this->punkteVerteilt();
			$boni[$index]++;
			$boni=implode(':',$boni);
			$this->set('bonus',$boni);
		}
	}

	public function angriff() {
		$this->loadWerte();
		$att=$this->typWerte[0];
		$boni=$this->punkteVerteilt();
		$boni_att=$boni[0];
		$angriff=roundTo((2*$att/3+27.5)*$boni_att + 1.25*$att,5);
		return $angriff;
	}

	public function verteidigung() {
		$this->loadWerte();
		$werte=$this->typWerte;
		$boni=$this->punkteVerteilt();
		$boni_ver=$boni[1];
		$verhaltniss=pow($werte[1]/$werte[2],0.2);
		$vert_i=roundTo((2*$werte[1]/3+27.5*$verhaltniss)*$boni_ver + 5*$werte[1]/3,5);
		$vert_k=roundTo((2*$werte[2]/3+27.5/$verhaltniss)*$boni_ver + 5*$werte[2]/3,5);
		return array($vert_i,$vert_k);
	}

	public function offWert() {
		$boni=$this->punkteVerteilt();
		return $boni[2]/5;
	}

	public function deffWert() {
		$boni=$this->punkteVerteilt();
		return $boni[3]/5;
	}

	public function regeneration() {
		$boni=$this->punkteVerteilt();
		return $boni[4]*15;
	}

	protected function loadWerte() {
		if (!isset($this->typWerte)) {
			$typ=TruppenTyp::getById($this->get('troop_id'));
			$this->typWerte=$typ->werte();
		}
	}


	public function punkteVerteilt() {
		return explode(':',$this->get('bonus'));
	}

	public function freiePunkte() {
		$punkte=$this->level()*5+5;
		$bonus=explode(':',$this->get('bonus'));
		$verteilt=array_sum($bonus);
		return $punkte-$verteilt;
	}

	public function erfahrungProzent() {
		$exp=$this->get('erfahrung');
		$lvl=(-50+sqrt(2500+$exp*200));
		return $lvl%100;
	}

	public function level() {
		$exp=$this->get('erfahrung');
		return floor((-50+sqrt(2500+$exp*200))/100);
	}
	
	public function getTruppenTyp() {
		$id=$this->get('troop_id');
		return TruppenTyp::getById($id);
	}

	public function toString() {
		return 'Held: keyId: '.$this->id.', Name: '.$this->get('name').', user: '.$this->get('user').
			', bonus: '.$this->get('bonus');
	}

	public function get($att) {
		//Anlehnung an id der TruppenTyp Klasse
		if ($att=='id')
			return 'hero';
		return $this->data[$att];
	}

	public function set($att,$value) {
		$this->data[$att]=$value;
		$this->changed=true;
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

	public static function create($user,$tid) {
		$helden=self::getByUser($user);
		$helden_lebend=ArrayObjectsContaining($helden,'lebt',true);
		if (!empty($helden_lebend)) return false;
		$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table." (user,name,lebt,troop_id,erfahrung,hp,bonus)
			VALUES (".$user->get('id').",'Unbekannter Held',1,$tid,0,100,'0:0:0:0:0');";
		$result=mysql_query($sql);
		unset(self::$loaded[mysql_insert_id()]);
		self::loadById(mysql_insert_id());
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
	
	public static function getAliveOrNonByUser($user) {
		$arr=self::getByUser($user);
		foreach($arr as $held)
			if ($held->get('lebt')==1)
				return $held;
		return null;
	}

	//parameter: $user id or object
	public static function getByUser($user) {
		if (is_object($user))
			$id=$user->get('id');
		elseif (((int)$user)==$user)
			$id=(int)$user;
		else {
			x('Held::getByUser, user id nicht herausgefunden',$user);
			return false;
		}
		if (!self::$loaded_user[$id]) {
			self::loadByUser($id);
		}
		return self::$ob_user[$id];
	}

	public static function loadByUser($user) {
		if (!self::$loaded_user[$user]) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE user=$user;";
			$result=mysql_query($sql);
			self::$ob_user[$user]=array();
			while ($data=mysql_fetch_assoc($result)) {
				$id=$data['keyid'];
				if (!self::$loaded[$id]) {
					new Held($id,$data);
				}
			}
			self::$loaded_user[$user]=true;
		}
	}

	public static function getById($id) {
		if (!self::$loaded[$id]) {
			self::loadById($id);
		}
		return self::$objekte[$id];
	}

	protected static function loadByID($id) {
		if (!isset(self::$loaded[$id])) {
			self::$loaded[$id]=true;
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE keyid=$id;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			new Held($id,$data);
		}
	}
}

?>
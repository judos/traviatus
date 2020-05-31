<?php

class Nachricht {

	public static $save=false;

	protected $id;
	protected $t;
	protected $data;

	protected static $objekte;	// [$typ][$id]
	protected static $db_key=array('keyid');
	protected static $db_table='msg';

	protected static $all_loaded=false;

	public function Nachricht($id,$t,$data) {
		$this->id=$id;
		$this->t=$t;
		$this->data=$data;
		self::$objekte[$t][$id]=$this;
	}

	public function neu() {
		if ($this->t==0)
			return $this->get('neu');
		else {	//Nachricht vom Postausgang
			$msg=self::getById($this->id-1);
			if ($msg==NULL) return 0;
			return $msg->get('neu');
		}
	}

	public function read() {
		$sql="UPDATE `tr".ROUND_ID."_msg` SET neu='0'
			WHERE keyid='".$this->id."';";
		mysql_query($sql);
	}

	//Gibt True zurück, wenn für diese Nachricht die
	// zugehörige Nachricht im Eingang gelöscht wurde
	public function deleted() {
		return self::getById($this->id-1)===NULL;
	}

	public function get($att) {
		if ($att=='betreff')
			if ($this->data[$att]=='') return '-kein Betreff-';
		return $this->data[$att];
	}

	public static function getAll($typ) {
		global $login_user;
		if (!self::$all_loaded) self::loadAll();

		$result=array();
		if (!isset(self::$objekte[$typ]))
			self::$objekte[$typ]=array();
		foreach(self::$objekte[$typ] as $id => $nachricht) {
			if ($nachricht->get('an')==$login_user->get('name'))
				$result[$id]=$nachricht;
		}
		return $result;
	}

	public static function getById($id) {
		if (!self::$all_loaded) {
			if (!isset(self::$objekte[1][$id]) &&
					!isset(self::$objekte[0][$id])) {
				return self::loadById($id);
			}
			if (!isset(self::$objekte[1][$id]))
				return self::$objekte[0][$id];
			else
				return self::$objekte[1][$id];
		}
		if (isset(self::$objekte[0][$id]))
			return self::$objekte[0][$id];
		else
			return @self::$objekte[1][$id];
	}

	public static function loadById($id) {
		if (!isset(self::$objekte[1][$id]) &&
					!isset(self::$objekte[0][$id])) {
			$sql="SELECT * FROM `tr".ROUND_ID."_".self::$db_table."`
				WHERE keyid='$id';";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			return new Nachricht($id,$data['typ'],$data);
		}
	}

	public static function sendTo($an,$betreff,$text,$options=array(true,true)) {
		global $login_user;
		
		$username=$login_user->get('name');
		//Nachricht senden
		if ($options[0]){
			if (Spieler::getByName($an)===NULL)
				return false;
			$sql="INSERT INTO `tr".ROUND_ID."_msg`
				(`von`,`an`,`betreff`,`zeit`,`text`,`typ`)
				VALUES ('$username','$an','$betreff',NOW(),'$text','0');";
			mysql_query($sql);
		}

		//Kopie im Postausgang speichern
		if ($options[1]) {
			$sql="INSERT INTO `tr".ROUND_ID."_msg`
				(`von`,`an`,`betreff`,`zeit`,`text`,`typ`)
				VALUES ('$an','$username','$betreff',NOW(),
								'$text','1');";
			mysql_query($sql);
		}
		return true;
	}

	public static function loadAll() {
		global $login_user;
		if (!self::$all_loaded) {
			$username=$login_user->get('name');
			$sql="SELECT * FROM `tr".ROUND_ID."_msg`
				WHERE (an='$username' or von='$username') AND von!=''
				ORDER BY zeit DESC;";
			$result=mysql_query($sql);
			while ($data=mysql_fetch_assoc($result)) {
				new Nachricht($data['keyid'],$data['typ'],$data);
			}
			self::$all_loaded=true;
		}
	}
}
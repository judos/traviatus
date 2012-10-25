<?php


class Allianz {

	protected $id;
	protected $data;

	protected $attChanged;
	protected $changed;

	protected $range;  //[$id]
	protected $rang;   //in der rangliste
	protected $anzMitglieder;
	protected $mitglieder;
	protected $invites;
	protected $news;	// [] = array()

	protected static $objekte;
	protected static $loaded;
	protected static $db_key=array('id');
	protected static $db_table='ally';

	public static $save=true;

	public function Allianz($id,$data) {
		$this->id = $id;
		$this->data=$data;
		$this->changed=false;
		$this->attChanged=array();
		foreach($this->data as $key=>$value)
			$this->attChanged[$key]=false;
		if (isset(self::$objekte[$id]))
			new Errorlog('new '.get_class($this).'. Objekt existiert bereits.
					Gefahr der Überschreibung.');
		self::$objekte[$id]=$this;
	}

	public function __toString() {
		return 'Allianz: '.$this->get('name');
	}

	public function getNews() {
		if (!isset($this->news)) {
			$sql="SELECT news,datum FROM tr".ROUND_ID."_ally_news
				WHERE ally_id=".$this->id." ORDER BY datum DESC limit 0,50;";
			$result=mysql_query($sql);
			$this->news=array();
			while ($data=mysql_fetch_assoc($result)) {
				$this->news[]=$data;
			}
		}
		return $this->news;
	}

	public function insertNews($news) {
		if (isset($this->news)) {
			array_insert($this->news,0,array('news'=>$news,'datum'=>now()));
		}
		$sql="INSERT INTO tr".ROUND_ID."_ally_news
			(ally_id,news,datum) VALUES (".$this->id.",'$news','".now()."');";
		mysql_query($sql);
	}

	public function auflosen() {
		$sql="UPDATE tr".ROUND_ID."_user SET ally=0,ally_rang=0
			WHERE ally=".$this->id.";";
		mysql_query($sql);
		$sql="DELETE FROM tr".ROUND_ID."_ally_einladungen
			WHERE ally_id=".$this->id.";";
		mysql_query($sql);
		$sql="DELETE FROM tr".ROUND_ID."_ally
			WHERE id=".$this->id.";";
		mysql_query($sql);
		$sql="DELETE FROM tr".ROUND_ID."_ally_range
			WHERE ally_id=".$this->id.";";
		mysql_query($sql);
		$sql="DELETE FROM tr".ROUND_ID."_ally_chat
			WHERE ally_id=".$this->id.";";
		mysql_query($sql);
		$sql="DELETE FROM tr".ROUND_ID."_ally_news
			WHERE ally_id=".$this->id.";";
		mysql_query($sql);
	}

	public function spielerEntlassen($spieler) {
		$spieler->set('ally',0);
		$spieler->set('ally_rang',0);
		return 'Spieler entlassen.';
	}

	public function spielerAusladen($spieler) {
		$sql="DELETE FROM tr".ROUND_ID."_".self::$db_table."_einladungen
			WHERE ally_id=".$this->get('id')."
				AND user_id=".$spieler->get('id').";";
		mysql_query($sql);
		unset($this->invites);
	}


	public function spielerEinladen($spieler) {
		$eingeladen=$this->spielerEingeladen();
		if (!arrayObjectsContains($eingeladen,'id',$spieler->get('id'))) {
			$mitglieder=$this->mitglieder();
			if (!arrayObjectsContains($mitglieder,'id',$spieler->get('id'))) {
				$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."_einladungen
					(ally_id,user_id)
					VALUES (".$this->get('id').",".$spieler->get('id').");";
				mysql_query($sql);
				return 'Spieler eingeladen.';
			}
			else
				return 'Spieler ist schon in der Allianz.';
		}
		else
			return 'Spieler ist schon eingeladen.';
	}

	public function spielerEingeladen() {
		if (!isset($this->invites)) {
			$sql="SELECT user_id
				FROM tr".ROUND_ID."_".self::$db_table."_einladungen
				WHERE ally_id=".$this->get('id').";";
			$result=mysql_query($sql);
			$this->invites=array();
			while ($data=mysql_fetch_assoc($result)) {
				array_push($this->invites,Spieler::getById($data['user_id']));
			}
		}
		return $this->invites;
	}

	public function rang() {
		if (!isset($this->rang)) {
			$sql="SELECT COUNT(`name`) FROM tr".ROUND_ID."_".self::$db_table."
				WHERE `einwohner`>".$this->get('einwohner').";";
			$result=mysql_query($sql);
			$data=mysql_fetch_assoc($result);
			$this->rang=$data['COUNT(`name`)']+1;
		}
		return $this->rang;
	}

	public function mitgliederEntlasse($entlassender) {
		$entlassender->save();
		if (isset($this->mitglieder)) {
			foreach($this->mitglieder as $nr=>$spieler) {
				if ($spieler->get('id')==$entlassender->get('id')) {
					unset($this->mitglieder[$nr]);
					if (isset($this->anzMitglieder))
						$this->anzMitglieder--;
				}
			}
		}
	}

	public function mitglieder() {
		if (!isset($this->mitglieder)) {

			$sql="SELECT id FROM tr".ROUND_ID."_user
				WHERE ally=".$this->get('id').";";
			$result=mysql_query($sql);
			$this->mitglieder=array();
			while($data=mysql_fetch_assoc($result)) {
				array_push($this->mitglieder,Spieler::getById($data['id']));
			}
		}
		return $this->mitglieder;
	}

	public function anzMitglieder() {
		if (!isset($this->anzMitglieder)) {
			if (isset($this->mitglieder))
				$this->anzMitglieder=sizeof($this->mitglieder);
			else{
				$sql="SELECT COUNT(`name`) as anz FROM tr".ROUND_ID."_user
					WHERE ally=".$this->get('id').";";
				$result=mysql_query($sql);
				$data=mysql_fetch_assoc($result);
				$this->anzMitglieder=$data['anz'];
			}
		}
		return $this->anzMitglieder;
	}

	public function createRang($name,$rights) {
		$names=AllianzRang::rechte();
		if ($rights==='all_rights') {
			$rights=array_fill(0,sizeof($names),1);
		}
		$rang=AllianzRang::create($this->get('id'),$name,
		                    array_combine($names,$rights));
		unset($this->range);
		return $rang;
	}

	public function getRang($id) {
		if ($id==0)
			return null;
		if (!isset($this->range)) {
			$this->range=AllianzRang::getByAlly($this->get('id'));
		}
		return $this->range[$id];
	}

	public function alleRange() {
		if (!isset($this->range)) {
			$this->range=AllianzRang::getByAlly($this->get('id'));
		}
		return $this->range;
	}

	public function get($att) {
		return $this->data[$att];
	}

	public function set($att,$value) {
		$this->data[$att]=$value;
		$this->attChanged[$att]=true;
		$this->changed=true;
	}

	public function add($att,$value) {
		$this->data[$att]+=$value;
		$this->attChanged[$att]=true;
		$this->changed=true;
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

	public static function create($tag,$name) {
		$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."
			(tag,name) VALUES ('$tag','$name');";
		$result=mysql_query($sql);
		unset(self::$loaded[mysql_insert_id()]);
		return self::getById(mysql_insert_id());
	}

	public static function getById($id) {
		if (!isset(self::$loaded[$id]) or !self::$loaded[$id]) {
			self::loadEntry($id);
		}
		return @self::$objekte[$id];
	}

	protected static function loadEntry($id) {
		if (!isset(self::$loaded[$id]) or !self::$loaded[$id]) {
			self::$loaded[$id]=true;
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE id='$id';";
			$result=mysql_query($sql);
			if ($result===FALSE) return NULL;
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			new Allianz($id,$data);
		}
	}
}

?>
<?php


class Angebot {

	protected $id;
	protected $data;

	protected static $objekte;   // [$x][$y]
	protected static $ob_id;     // [$id]
	protected static $loaded;    // [$x][$y]
	protected static $loaded_id; // [$id]
	protected static $db_key=array('keyid');
	protected static $db_table='angebote';

	public static $save=false;

	public function __construct($id,$data) {
		$this->id = $id;
		$this->data=$data;
		$x=$data['ursprung_x'];
		$y=$data['ursprung_y'];
		if (!isset(self::$objekte[$x][$y]))
			self::$objekte[$x][$y]=array();
		if (!ArrayObjectsContains(self::$objekte[$x][$y],'keyid',$id))
			array_push(self::$objekte[$x][$y],$this);
		self::$ob_id[$id]=$this;
		self::$loaded_id[$id]=true;
	}

	public function dorf() {
		return Dorf::getByXY($this->get('ursprung_x'),$this->get('ursprung_y'));
	}

	public function toString() {
		$r=array('','Holz','Lehm','Eisen','Getreide');
		return 'Angebot aus '.$this->dorf()->toString().', '.
			'Angebot '.$this->get('angebot').' '.$r[$this->get('angebot_id')].', '.
			'Nachfrage '.$this->get('nachfrage').' '.$r[$this->get('nachfrage_id')];
	}

	public function get($att) {
		return $this->data[$att];
	}

	//Buy this offer from a village
	public function buy($dorf) {
		$nachfrage=$this->nachfrageRess();
		//genug Ress
		if ($dorf->genugRess($nachfrage)) {
			$handler=$dorf->freieHandler();
			$zutragen=array_sum($nachfrage);
			$handlerTragKap=$dorf->handlerKapazitat();
			//Genug freie Händler
			if ($zutragen/$handlerTragKap<=$handler) {
				$angebotDorf=$this->dorf();
				$angebot=$this->angebotRess();
				$angebotUser=$angebotDorf->user();

				$nachfrageUser=$dorf->user();
				//Angebot löschen, Händler und Ress freigeben
				$this->delete($angebotDorf);

				//Nachfrage abschicken
				Handler::create($nachfrageUser,$dorf,$angebotDorf,$nachfrage);

				//Ress des Angebots dem Angebotdorf hinzufügen
				$angebotDorf->addRess($angebot);
				//Ress abschicken (Ress werden durch Händler direkt abgezogen)
				Handler::create($angebotUser,$angebotDorf,$dorf,$angebot);

			}
			else
				return 'Nicht genügend freie Händler';
		}
		else
			return 'Nicht genügend Rohstoffe';
	}


	//Only works if you give the right village
	public function delete($dorf) {
		$ux=$this->get('ursprung_x');
		$uy=$this->get('ursprung_y');
		if ($ux==$dorf->get('x') and $uy==$dorf->get('y')) {
			$sql="DELETE FROM tr".ROUND_ID."_".self::$db_table." WHERE keyid=".$this->id.";";
			mysql_query($sql);
			unset(self::$ob_id[$this->id]);
			ArrayObjectsDelete(self::$objekte[$ux][$uy],'keyid',$this->id);
		}
	}
	
	//Liefert einen String zurück mit angebot_id:menge:nachfrage_id:menge
	public function tradeRess() {
		return $this->get('angebot_id').':'.$this->get('angebot').':'.
			$this->get('nachfrage_id').':'.$this->get('nachfrage');
	}

	public function angebotRess() {
		$ress=array_fill(0,4,0);
		$ress[$this->get('angebot_id')-1]=$this->get('angebot');
		return $ress;
	}

	public function nachfrageRess() {
		$ress=array_fill(0,4,0);
		$ress[$this->get('nachfrage_id')-1]=$this->get('nachfrage');
		return $ress;
	}

	public function drawForYou() {
		global $gid;
		$nach=$this->get('nachfrage');
		$nachid=$this->get('nachfrage_id');
		$ang=$this->get('angebot');
		$angid=$this->geT('angebot_id');
		echo'<tr align="center" bgcolor="#ffffff"><td>
			<a href="?page=build&gid='.$gid.'&tab=3&do=deloffer&keyid='.$this->get('keyid').'">
			<img src="img/un/a/del.gif" alt="löschen" title="löschen" border="0" height="12" width="12">
			</a></td><td><img src="img/un/r/'.$angid.'.gif" height="12" width="18">'.$ang.'</td>
			<td><img src="img/un/r/'.$nachid.'.gif" height="12" width="18">'.$nach.'</td>
			<td>'.$this->get('handler').'</td><td>';
		if ($this->get('ally')==1)
			echo'<img src="img/un/a/b3.gif" height="12" width="12"
				title="Nur für Allianz">';
		else
			echo'<img src="img/un/a/b2.gif" height="12" width="12"
				title="Für alle sichtbar">';
		echo'</td><td>'.$this->get('maxzeit').'h</td></tr>';
	}

	public static function create($dorf,$typ1,$r1,$typ2,$r2,$max,$ally) {
		$error=true;
		if ($r1>0 and $r2>0) {
			if ($r1/$r2>=0.5) {
				if ($typ1!=$typ2) {
					$lager=$dorf->lager();
					if ($r1<=$lager[$typ1-1]) {
						$tragen=$dorf->handlerKapazitat();
						$anz_freie_handler=$dorf->freieHandler();
						if ($r1/$tragen<=$anz_freie_handler) {
							$h=ceil($r1/$tragen);

							$ress=array_fill(0,4,0);
							$ress[$typ1-1]=$r1;
							$dorf->subRess($ress);
							$dx=$dorf->get('x');
							$dy=$dorf->get('y');
							$uid=$dorf->get('user');
							$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."
							(user,ursprung_x,ursprung_y,angebot_id,angebot,nachfrage_id,nachfrage,handler,maxzeit,ally)
							VALUES ($uid,$dx,$dy,$typ1,$r1,$typ2,$r2,$h,$max,$ally);";
							$result=mysql_query($sql);
							$id=mysql_insert_id();
							$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table." WHERE keyid=$id;";
							$result=mysql_query($sql);
							$data=mysql_fetch_assoc($result);
							new Angebot($id,$data);
						}
						else $error="Zu wenig Händler";
					}
					else $error="Zu wenig Rohstoffe";
				}
				else $error="Ungültiges Angebot";
			}
			else $error="maximal 2:1";
		}
		else $error="Ungültiges Angebot";
		return $error;
	}

	public static function getById($id) {
		if (!isset(self::$loaded_id[$id])) {
			self::loadByID($id);
		}
		return self::$ob_id[$id];
	}

	public static function getByD($dorf) {
		$x=$dorf->get('x');
		$y=$dorf->get('y');
		if (!isset(self::$loaded[$x][$y])) {
			self::loadEntry($x,$y);
		}
		return self::$objekte[$x][$y];
	}

	protected static function loadByID($id) {
		if (!isset(self::$loaded_id[$id])) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE keyid=$id;";
			$result=mysql_query($sql);
			while ($data=mysql_fetch_assoc($result)) {
				if (!self::$loaded_id[$data['keyid']])
					new Angebot($data['keyid'],$data);
			}
		}
	}

	protected static function loadEntry($x,$y) {
		if (!isset(self::$loaded[$x][$y])) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE ursprung_x=$x AND ursprung_y=$y;";
			$result=mysql_query($sql);
			while ($data=mysql_fetch_assoc($result)) {
				if (!self::$loaded_id[$data['keyid']])
					new Angebot($data['keyid'],$data);
			}
			self::$loaded[$x][$y]=true;
			if (!isset(self::$objekte[$x][$y]))
				self::$objekte[$x][$y]=array();
		}
	}
}

?>
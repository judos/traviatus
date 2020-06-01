<?php

class GebeudeTyp {

	public $id;
	private $data;

	public static $save=false;
	protected static $objekte;
	protected static $db_key=array('id');
	protected static $db_table='gebeude';
	public static $anz=40;

	public function __construct($id,$data) {
		$this->id = $id;
		$this->data=$data;
	}
	
	public static function mauerSchutzBonus($gid,$stufe) {
		if($gid<31 or $gid>33) {
			x('Invalid call to GebeudeTyp::mauerSchutzbonus. $gid must be between 31 and 33');
			return 0;
		}
		$max=explode(':',Diverses::get('mauer_schutz_max'));
		$volk=$gid-30;
		$maxStufe=$max[$volk-1];
		$bonus=round($maxStufe*$stufe/20);
		return $bonus;
	}

	public function needs() {
		$needs=explode(':',$this->get('needs'));
		$gebeude=array();
		for ($j=1;$j<=$needs[0];$j++) {
			$gebeude[$needs[$j*2-1]]=$needs[$j*2];
		}
		return $gebeude;
	}

	public function bauKosten($stufe) {
		$k=explode(':',$this->get('baukosten'));
		$faktor=$k[4];
		for ($i=0;$i<4;$i++)
			$kosten[$i]=roundTo($k[$i]*pow($faktor,$stufe-1),5);
		return $kosten;
	}

	public function bauZeit($stufe,$dorf=NULL) {
		$zeit=explode(':',$this->get('bauzeit'));
		$faktor=$zeit[2];
		if ($dorf!==NULL) {
			$highest=$dorf->highest();
  	  $HG_stufe=$highest[15];
  	}
  	else $HG_stufe=1;

		if ($stufe==1)
			$bauzeit=$zeit[0];
		else
			$bauzeit=$zeit[0]+roundTo($zeit[1]*pow($faktor,$stufe-2),10);
		//HG Bonus
		$bauzeit=round($bauzeit*round(100-60*(($HG_stufe-1)/19))/100);
		return $bauzeit;
	}

	public function baubar($dorf,$stufe,$gid) {
		if ($gid>18) {
			$geb2=$dorf->gebeude2typ();
			if ($geb2[$gid-19]!=$this->id and $geb2[$gid-19]!=0)
				return 'Nicht möglich';
		}

		$max_stufe=$this->get('stufen');
		if ($gid<19 and $dorf->get('grosse')==0) {
			$max_stufe=Diverses::get('max_ausbau_nicht_hd');
		}

		//Nicht voll ausgebaut
		if ($stufe>$max_stufe)
			return $this->get('name').' vollständig ausgebaut';
		//Genug Ress
		if (!$dorf->genugRess($this->baukosten($stufe)))
			return 'Zuwenig Rohstoffe';
		//Kein Getreidemangel oder Getreidefarm wird ausgebaut
		$produktion=$dorf->produktion();
		if ($produktion[3]-$dorf->get('einwohner')
				-$this->get('arbeiter')<=2 and
				$this->get('name')!='Getreidefarm')
			return 'Nahrungsmangel: Erst eine Getreidefarm ausbauen';
		//Im Dorf wird nichts gebaut / Römer nicht im gleichen Teil

		return true;
	}

	public function neuBaubar($dorf,$gid) {
		$user=$dorf->user();
		$allowed=0;
		//Versammlungsplatz nur auf Versammlungsbauplatz bauen
		if ($gid!=39 and $this->id==16)
			$allowed=-1;
		//Auf Versammlungsplatz sonst nichts bauen
		if ($gid==39 and $this->id!=16)
			$allowed=-1;
		//Stadtmauer nur aussen bauen
		if ($this->id==$user->get('volk')+30 and $gid!=40)
			$allowed=-1;
		//Aussen sonst nichts bauen
		if ($gid==40 and ($this->id<31 or $this->id>33))
			$allowed=-1;
			
		//Weltwunderbauplatz
		if (WW::isWWDorf($dorf) and $gid==WW::getField() and $this->id == WW::getWWGebId())
			$allowed=1;
		elseif (WW::isWWDorf($dorf) and $gid==WW::getField())
			$allowed=-1;

		return (($this->anforderungenOk($dorf) or $allowed==1)
				and $allowed!=-1);
	}

	public function anforderungenOk($dorf) {
		$debug=false;
		//if ($this->get('name')=='Getreidemühle') $debug=true;
		if ($debug) echo $this->get('name');
		$highest=$dorf->highest();
		$gebAnzahl=$dorf->gebeudeAnzahl();
		$user=Spieler::getById($dorf->get('user'));
		$needs=explode(':',$this->get('needs'));
		$ok=true;
		if ($debug) var_dump($needs);
		if ($debug) echo ($ok?'1':'0');
		//Needs
		if ($this->get('needs')!='' and $needs[0]>-1) {
			for ($j=1;$j<=$needs[0];$j++) {
				$id=$needs[2*$j-1];
				$stufe=$needs[2*$j];
				if ($id>0 and $highest[$id]<$stufe) $ok=false;
				if (($id==-1 or $id=='volk') and $user->get('volk')!=$stufe)
					$ok=false;
				if ($id=='dorf') $ok=false;
			}
		}
		elseif ($this->get('needs')!='' and $needs[0]==0) $ok=true;
		else $ok=false;
		if ($debug) echo ($ok?'1':'0');
		//Mehrfachbau?
		if ($ok and $gebAnzahl[$this->id]>0) {
			$ok=false;
			$min_stufe=$this->get('rebuild');
			if ($min_stufe>0 and $highest[$this->id]>=$min_stufe)
				$ok=true;
		}
		if ($debug) echo ($ok?'1':'0');
		//Residenz im Hauptdorf nicht baubar
		if ($this->id==25 and $dorf->get('grosse')==1) $ok=false;
		//Palast im Nebendorf nicht baubar
		if ($this->id==26 and $dorf->get('grosse')==0) $ok=false;
		if ($debug) echo ($ok?'1':'0');
		if ($debug) echo'<br>';
		return $ok;
	}

	public function dump() {
		ob_start();
		var_dump($this->data);
		return ob_get_flush();
	}

	public function get($att) {
		return $this->data[$att];
	}

	public static function getById($id) {
		if (!isset(self::$objekte[$id])) {
			self::loadEntry($id);
		}
		return self::$objekte[$id];
	}

	protected static function loadEntry($id) {
		if (!isset(self::$objekte[$id])) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE id=$id;";
			$result=mysql_query($sql);
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			self::$objekte[$id]=new GebeudeTyp($id,$data);
		}
	}
}


?>
<?php


class TruppenTyp {

	private $id;
	protected $data;

	protected static $objekte;
	protected static $db_key=array('id');
	protected static $db_table='truppen_typen';

	public static $save=false;

	public function TruppenTyp($id,$data) {
		$this->id = $id;
		$this->data=$data;
	}

	public function toString() {
		return 'TruppenTyp: '.$this->get('name').' (id '.$this->id.')';
	}

	public function get($att) {
		if ($att=='mehrzahl' && $this->data[$att]=='')
			return $this->data['name'];
		return $this->data[$att];
	}
	
	public function named($anz) {
		if ($anz==1) return $this->get('name');
		else return $this->get('mehrzahl');
	}

	public function werte() {
		return explode(':',$this->get('werte'));
	}

	public function baukosten() {
		$x=explode(':',$this->get('baukz'));
		unset($x[4]);	//Bauzeit rausnehmen
		return $x;
	}

	public function maxAnzahlBaubar($lager) {
		$k=$this->baukosten();
		return self::maxAnzahlBaubarLager($k,$lager);
	}

	public static function maxAnzahlBaubarLager($k,$lager) {
		$max[0]=floor($lager[0]/$k[0]);
		$max[1]=floor($lager[1]/$k[1]);
		$max[2]=floor($lager[2]/$k[2]);
		$max[3]=floor($lager[3]/$k[3]);
		$buildmax=min($max);
		return $buildmax;
	}

	public function forschungszeit() {
		$kosten=explode(':',$this->get('reskost'));
		return $kosten[4];
	}

	public function forschungskosten() {
		$kosten=explode(':',$this->get('reskost'));
		unset($kosten[4]);
		return $kosten;
	}

	public function verbesserungskosten($stufe) {
		$f=explode(':',$this->get('forsch'));
		$kosten=array();
		for ($i=0;$i<4;$i++)
			$kosten[$i]=$f[$i]*pow($f[4],$stufe-1);
		return $kosten;
	}

	public function verbesserungszeit($stufe) {
		$f=explode(':',$this->get('forsch'));
		return $f[5]*pow($f[4],$stufe-1);
	}

	public function bauzeit($dorf) {
		$highest=$dorf->highest();
		$x=explode(':',$this->get('baukz'));
		$start=$x[4];
		switch($this->get('typ')) {
		case 1:
			if ($highest[19]==0) return $start;
			return round($start*(100-4.8*($highest[19]-1))/100);break;
		case 2:
			return round($start*(100-4*($highest[20]-1))/100);break;
		case 3:
			return round($start*(100-4*($highest[21]-1))/100);break;
		case 4:
			if ($highest[25]>$highest[26])
				return round($start*(100-4*($highest[25]-1))/100);
			else
				return round($start*(100-4*($highest[26]-1))/100);
			break;
		}
	}

	public function needs() {
		$needs=explode(':',$this->get('needs'));
		$gebeude=array();
		for ($j=1;$j<=$needs[0];$j++) {
			$gebeude[$needs[$j*2-1]]=$needs[$j*2];
		}
		return $gebeude;
	}

	public function erforschbar($spieler,$dorf) {
		if ($this->get('volk')!=$spieler->get('volk'))
			return false;

		$highest=$dorf->highest();
		$needs=explode(':',$this->get('needs'));
		for ($j=1;$j<=$needs[0];$j++)
			if ($highest[$needs[$j*2-1]]<$needs[$j*2])
				return false;
		if ($needs[0]==-1) return false;
		return true;
	}

	public function baubar($spieler) {
		//Prüfen ob die Einheit vom Volk ist
		if ($this->get('volk')!=$spieler->get('volk'))
			return false;

		//Prüfen ob erforscht ist.
		$r=$spieler->forschungen();
		$id=($this->get('id')-1)%10;

		if ($r[$id]==1 or $this->get('needs')==-1) return true;
		return false;
	}

	public function vonVolk($spieler) {
		return $this->get('volk')==$spieler->get('volk');
	}

	public function waffenUpgrade($spieler,$dorf) {
		if ($this->data['typ']==4) return NULL;
		$forsch=explode(':',$this->get('forsch'));
		$stufen_waffen=$spieler->waffen();
		$index=($this->id-1)%10;
		$faktor_waffen=pow($stufen_waffen[$index]+1,$forsch[4]);
		for ($j=0;$j<=3;$j++) {
			$kosten[$j]=roundTo($faktor_waffen*$forsch[$j],5);
		}
		$highest=$dorf->highest();
		$kosten['zeit']=round($faktor_waffen*$forsch[5]/100*
							(100-50*($highest[12]-1)/19));
		return $kosten;
	}

	public function rustungenUpgrade($spieler,$dorf) {
		if ($this->data['typ']==4) return NULL;
		$forsch=explode(':',$this->get('forsch'));
		$stufen_rustungen=$spieler->rustungen();
		$index=($this->id-1)%10;
		$faktor_rustungen=pow($stufen_rustungen[$index]+1,
												$forsch[4]);
		for ($j=0;$j<=3;$j++) {
			$kosten[$j]=roundTo($faktor_rustungen*$forsch[$j],5);
		}
		$highest=$dorf->highest();
		$kosten['zeit']=round($faktor_rustungen*$forsch[5]/100*
							(100-50*($highest[13]-1)/19));
		return $kosten;
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
			if ($result===FALSE) return NULL;
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			self::$objekte[$id]=new TruppenTyp($id,$data);
		}
	}
}

?>
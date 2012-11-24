<?php

class Bericht {
	
	protected $id;
	protected $data;
	
	public static $save=false;
	
	protected static $db_table='msg';
	protected static $objekte=array();
	
	protected static $all_loaded=false;
	
	const PART_TEXT_ONLY  =   0;
	const PART_TEXT_TITLE =   1;
	const PART_RESS       =   2;
	const PART_UNIT_TYPES =   3;
	const PART_UNIT_COUNT =   4;
	const PART_SUPPLY     =   5;
	const PART_NEW_TABLE  = 100;
	
	const TYPE_ALLE           = 0;
	const TYPE_HANDEL         = 1;
	const TYPE_UNTERSTUETZUNG = 2;
	const TYPE_ANGRIFFE       = 3;
	const TYPE_SONSTIGE       = 4;
	
	protected function Bericht($id,$data) {
		$this->id=$id;
		$this->data=$data;
		if (isset(self::$objekte[$id]))
			new Errorlog('Bericht - objekt bereits vorhanden');
		self::$objekte[$id]=$this;
	}
	
	public function get($att) {
		if ($att=='betreff' && $this->data[$att]=='')
			return '-kein Betreff-';
		return $this->data[$att];
	}
	
	public function toHtml() {
		$alles=explode(chr(13),$this->get('text'));
		$html='<table class="tbg" cellpadding="2" cellspacing="1"><tbody>';
		for ($teil=0;$teil<sizeof($alles);$teil++) {
			$zeile=explode(':',$alles[$teil]);
			$html.=self::partToHtml($zeile);
		}
		$html.='</tbody></table>';
		return $html;
	}
	
	public function read() {
		$sql="UPDATE tr".ROUND_ID."_".self::$db_table."
			SET neu=0 WHERE keyid='".$this->id."';";
		mysql_query($sql);
	}
	
	public static function partToHtml($arr) {
		$type=$arr[0];
		if ($type==self::PART_TEXT_ONLY)
			return self::partTextOnlyToHtml($arr);
		elseif ($type==self::PART_TEXT_TITLE)
			return self::partTextTitleToHtml($arr);
		elseif ($type==self::PART_RESS)
			return self::partRessToHtml($arr);
		elseif ($type==self::PART_UNIT_TYPES)
			return self::partUnitTypesToHtml($arr);
		elseif ($type==self::PART_UNIT_COUNT)
			return self::partUnitCountToHtml($arr);
		elseif ($type==self::PART_SUPPLY)
			return self::partSupplyToHtml($arr);
		elseif ($type==self::PART_NEW_TABLE)
			return self::partNewTableToHtml($arr);
	}
	
	protected static function partTextOnlyToHtml($arr) {
		return'<tr class="cbg1"><td width="100%" colspan="11">'.
			$arr[1].'</td></tr>';
	}
	
	protected static function partTextTitleToHtml($arr) {
		$html='<tr class="cbg1">';
		if ($arr[1]=='Angreifer')
			$html.='<td width="21%" class="c2 b">'.$arr[1].'</td>';
		elseif($arr[1]=='Verteidiger')
			$html.='<td width="21%" class="c1 b">'.$arr[1].'</td>';
		else //($arr[1]=='Unterstützung')
			$html.='<td width="21%" class="c1 b">'.$arr[1].'</td>';
		$html.='<td colspan=10 class="b">'.$arr[2].'</td></tr>';
		return $html;
	}
	
	protected static function partRessToHtml($arr) {
		$html='<tr><td width="100" class="left">&nbsp;Rohstoffe</td><td class="s7">';
		for ($i=1;$i<=4;$i++){
			if ($arr[$i]=='')
				$arr[$i]=0;
			$html.='<img class="res" src="img/un/r/'.$i.'.gif">'.$arr[$i];
			if ($i<4)
				$html.=' | ';
		}
		$html.='</td></tr>';
		return $html;
	}
	
	protected static function partUnitTypesToHtml($arr) {
		$html='<tr class="unit"><td>&nbsp;</td>';
		$volk=$arr[1];
		$einheiten = TruppenTyp::getByVolk($volk);
		foreach($einheiten as $tid => $einheit){
			$html.='<td><img src="img/un/u/'.$tid.'.gif" title="'.
				$einheit->get('name').'"></td>';
		}
		$html.='</tr>';
		return $html;
	}
	
	protected static function partUnitCountToHtml($arr) {
		$html='<tr><td>'.$arr[1].'</td>';
		for ($j=1;$j<=10;$j++) {
			if ($arr[$j+1]>0)
				$html.='<td>'.$arr[$j+1].'</td>';
			else
				$html.='<td class="c">0</td>';
		}
		$html.='</tr>';
		return $html;
	}
	
	protected static function partSupplyToHtml($arr) {
		return'<tr class="cbg1"><td>Unterhalt</td>
			<td class="s7" colspan="10">'.$arr[1].
				'<img class="res" src="img/un/r/4.gif">pro Stunde</td>
				</tr>';
	}
	
	protected static function partNewTableToHtml($arr){
		return'</tbody></table>
			<table class="tbg" cellpadding="2" cellspacing="1">
			<tbody>';
	}
	
	public static function getById($id) {
		if (!self::$all_loaded)
			if (!isset(self::$objekte[$id]))
				self::loadById($id);
		
		return self::$objekte[$id];
	}
	
	//Stellt alle Berichte eines Types zusammen
	public static function getAll($typ) {
		if (!self::$all_loaded) self::loadAll();
		if ($typ==self::TYPE_ALLE) return self::$objekte;
		else {
			$result=array();
			foreach(self::$objekte as $id => $bericht) {
				if ($bericht->get('typ')==$typ)
					$result[$id]=$bericht;
			}
			return $result;
		}
	}
	
	public static function loadById($id) {
		if (isset(self::$objekte[$id]))
			return;
		global $login_user;
		$sql="SELECT * FROM `tr".ROUND_ID."_".self::$db_table."`
			WHERE `an`='".$login_user->get('name')."' AND von=''
				AND keyid='$id';";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)==0) return NULL;
		$data=mysql_fetch_array($result);
		return new Bericht($id,$data);
	}
	
	public static function loadAll() {
		global $login_user;
		if (!self::$all_loaded) {
			$username=$login_user->get('name');
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE von='' AND an='$username'
				ORDER BY `zeit` DESC;";
			$result=mysql_query($sql);
			while($data=mysql_fetch_assoc($result)) {
				if (!isset(self::$objekte[$data['keyid']]))
					new Bericht($data['keyid'],$data);
			}
			self::$all_loaded=true;
		}
	}
}
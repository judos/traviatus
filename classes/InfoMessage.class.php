<?php

class InfoMessage {
	public static $save=false;
	
	const PART_TEXT_ONLY     =   0;
	const PART_TEXT_TITLE    =   1;
	const PART_RESS          =   2;
	const PART_UNIT_TYPES    =   3;
	const PART_UNIT_COUNT    =   4;
	const PART_SUPPLY        =   5;
	const PART_TIME_ARRIVAL  =   6;
	const PART_TIME_DURATION =   7;
	const PART_NEW_TABLE     = 100;
	
	const SEPARATOR = '::';
	
	protected $text;
	
	public function __construct($text='') {
		$this->text=$text;
	}
	
	public function toHtml() {
		$alles=explode(chr(13),$this->text);
		$html='<table class="tbg" cellpadding="2" cellspacing="1"><tbody>';
		for ($teil=0;$teil<sizeof($alles);$teil++) {
			$zeile=explode(self::SEPARATOR,$alles[$teil]);
			$html.=self::partToHtml($zeile)."\n";
		}
		$html.='</tbody></table>';
		return $html;
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
		elseif ($type==self::PART_TIME_ARRIVAL)
			return self::partTimeArrivalToHtml($arr);
		elseif ($type==self::PART_TIME_DURATION)
			return self::partTimeDurationToHtml($arr);
		elseif ($type==self::PART_NEW_TABLE)
			return self::partNewTableToHtml($arr);
	}
	
	protected static function partTimeDurationToHtml($arr) {
		global $timerNr;
		$result='<tr class="cbg1"><td>'.$arr[1].'</td><td colspan="11">
		<table cellspacing="0" cellpadding="0" class="tbg">
		<tr><td width="50%">in '.zeit_dauer($arr[2]).' Std.</td>
		<td width="50%">um <span id=tp2>'.
		date('H:i:s',time()+$arr[2]).'</span><span> Uhr</span></td></tr>
		</table></td></tr>';
		return $result;
	}
	
	protected static function partTimeArrivalToHtml($arr) {
		global $timerNr;
		$dauer=zeit_dauer(strtotime($arr[1])-time());
		$akt=date('H:i:s',strtotime($arr[1]));
		
		$result='<tr class="cbg1"><td width="21%">Ankunft</td>
			<td colspan=11>
			<table class="f10" cellpadding="0" cellspacing="0" width="100%">
				<tbody><tr align="center">
				<td width="50%">&nbsp; in <span id="timer'.$timerNr.'">'.
					$dauer.'</span> Std.</td>
				<td width="50%">um '.$akt.'<span> Uhr</span>
				</td></tr></tbody></table>
			</td></tr>';
		$timerNr++;
		return $result;
	}
	
	protected static function partTextOnlyToHtml($arr) {
		return'<tr class="cbg1"><td width="100%" colspan="12">'.
			$arr[1].'</td></tr>';
	}
	
	protected static function partTextTitleToHtml($arr) {
		$html='<tr class="cbg1">';
		if ($arr[1]=='Angreifer')
			$html.='<td width="21%" class="c2 b">'.$arr[1].'</td>';
		elseif($arr[1]=='Verteidiger' or $arr[1]=='Unterstützung')
			$html.='<td width="21%" class="c1 b">'.$arr[1].'</td>';
		else
			$html.='<td width="21%" class="b">'.$arr[1].'</td>';
		$html.='<td colspan=11 class="b">'.$arr[2].'</td></tr>';
		return $html;
	}
	
	protected static function partRessToHtml($arr) {
		$html='<tr><td width="100">&nbsp;'.$arr[1].'</td><td class="s7" colspan="11">';
		for ($i=1;$i<=4;$i++){
			if ($arr[$i+1]=='')
				$arr[$i+1]=0;
			$html.='<img class="res" src="img/un/r/'.$i.'.gif">'.round($arr[$i+1],0);
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
		for ($j=1;$j<=11;$j++) {
			if (isset($arr[$j+1]) and $arr[$j+1]=='?')
				$html.='<td class="c">?</td>';
			elseif (isset($arr[$j+1]) and $arr[$j+1]>0)
				$html.='<td>'.$arr[$j+1].'</td>';
			else
				$html.='<td class="c">0</td>';
		}
		$html.='</tr>';
		return $html;
	}
	
	protected static function partSupplyToHtml($arr) {
		return'<tr class="cbg1"><td>Unterhalt</td>
			<td class="s7" colspan="11">'.$arr[1].
				'<img class="res" src="img/un/r/4.gif">pro Stunde</td>
				</tr>';
	}
	
	protected static function partNewTableToHtml($arr){
		return'</tbody></table>
			<table class="tbg" cellpadding="2" cellspacing="1">
			<tbody>';
	}
	
	protected function addPart($type,$arr) {
		$string=$type. self::SEPARATOR .implode(self::SEPARATOR,$arr);
		if ($this->text!='')
			$this->text.=chr(13);
		$this->text.=$string;
	}
	
	// $text: string
	public function addPartTextOnly($text) {
		$this->addPart(self::PART_TEXT_ONLY,array($text));
	}
	
	// $text,$title: string
	public function addPartTextTitle($text,$title) {
		$this->addPart(self::PART_TEXT_TITLE,array($text,$title));
	}
	
	// $ress: array(0-3 => $amount: int)
	public function addPartRess($text,$ress) {
		$this->addPart(self::PART_RESS,array_merge(array($text),$ress));
	}
	
	// $volk: int
	public function addPartUnitTypes($volk) {
		$this->addPart(self::PART_UNIT_TYPES,array($volk));
	}
	
	// $known = array('spaher'=>bool,'troops'=>bool);
	public function addPartUnitCountOrUnknown($text,$units,$known) {
		foreach($units as $tid => $count) {
			$typ=TruppenTyp::getById($tid);
			if ($typ!=null){
				$show=$typ->isSpy() && @$known['spaher']==true;
				$show|= @$known['troops']==true;
				if (! $show)
					$units[$tid]='?';
			}
		}
		$this->addPartUnitCount($text,$units,$known);
	}
	
	// $text: string
	// $units: array(1-30 => $amount: int)
	public function addPartUnitCount($text,$units) {
		
		$u=array(0=>$text);
		//new array with indices between 1 and 10
		foreach($units as $gid => $count){
			if (is_int($gid)){
				$nr= ($gid-1) % 10;
				$u[$nr+1]=$count;
			}
			elseif ($gid=='hero')
				$u[11]=$count;
		}
		//check that all indices 1-10 exist
		for($i=1;$i<=11;$i++)
			if (!isset($u[$i]))
				$u[$i]=0;
		$this->addPart(self::PART_UNIT_COUNT,$u);
	}
	
	public function addPartTimeDuration($text,$duration) {
		$this->addPart(self::PART_TIME_DURATION,array($text,$duration));
	}
	
	// $supply: int
	public function addPartSupply($supply) {
		$this->addPart(self::PART_SUPPLY,array($supply));
	}
	
	// $datetimeArrival: datetime(Y-m-d H:i:s)
	public function addPartTimeArrival($datetimeArrival) {
		$this->addPart(self::PART_TIME_ARRIVAL,array($datetimeArrival));
	}
	
	public function addPartNewTable() {
		$this->addPart(self::PART_NEW_TABLE,array());
	}
	
	public function sendToAllianzen($allianz_arr,$betreff) {
		foreach($allianz_arr as $ally) {
			$this->sendToAllianz($ally,$betreff);
		}
	}
	
	public function sendToUsers($users_arr,$betreff,$typ) {
		foreach($users_arr as $user) {
			$this->sendToUser($user,$betreff,$typ);
		}
	}
	
	public function sendToAllianz($ally,$betreff) {
		if (is_object($ally))
			$ally=$ally->get('id');
		elseif (is_int($ally)){}
		else
			new Errorlog("$ally is not an object or int as exptected");
			
		//insert message
		$sql="INSERT INTO tr".ROUND_ID."_ally_kampfe
			(ally_id,datetime,betreff,text)
			VALUES
			('$ally','".now()."','$betreff','".utf8_encode($this->text)."');";
		mysql_query($sql);
		
		//don't overfill database, count tuples for this alliance
		$r=mysql_query("SELECT COUNT(*) as anz FROM tr".ROUND_ID."_ally_kampfe WHERE ally_id='$ally';");
		$d=mysql_fetch_assoc($r);
		$anz = $d['anz'];
		
		$maxAnz = Diverses::get('allianz_max_anz_berichte');
		if ($anz>$maxAnz) {
			$delRows = $anz - $maxAnz;
			mysql_query("DELETE FROM tr1_ally_kampfe WHERE ally_id=$ally ORDER BY datetime ASC LIMIT $delRows;");
		}
	}
	
	// $user: string, or Spieler-object
	// $betreff: string, name des Spielers
	public function sendToUser($user,$betreff,$typ) {
		if (is_object($user))
			$name=$user->get('name');
		elseif (is_string($user))
			$name=$user;
		else
			new Errorlog("$user is not an object or string as expected");
		$sql="INSERT INTO tr".ROUND_ID."_msg
			(von,an,typ,zeit,betreff,text)
			VALUES
			('','$name','$typ','".now()."','$betreff','".utf8_encode($this->text)."');";
		mysql_query($sql);
	}
}

?>
<?php

class Dorf {

	private $x,$y;

	private $data;
	private $changed;
	private $highest;			//Bei jeder Funktion prüfen
	private $highest_gid;	//ob dies updated werden muss.

	protected static $objekte;
	protected static $loaded;

	public static $save=true;
	protected static $db_key=array('x','y');
	protected static $db_table='dorfer';

	protected static $anz;

	protected static $all_koords;

	public function Dorf($x,$y,$data) {
		$this->x=$x;
		$this->y=$y;
		$this->data=$data;
		$this->changed=false;
		if (@self::$objekte[$x][$y]!==NULL)
			new Errorlog('new Dorf, Dorf existierte bereits.');
		self::$objekte[$x][$y]=$this;
		$this->update();
	}
	
	public function getLink() {
		return '<a href="?page=karte-show&x='.$this->x.'&y='.$this->y.'">'.
			$this->get('name').' ('.$this->x.'|'.$this->y.')</a>';
	}

	public function truppenBewegungen() {
		$result=array();
		$typen=array('def1','att1','def2','att2');
		foreach($typen as $typ)
			$result[$typ]['anz']=0;
		//Truppenbewegungen
		$truppenmove1=TruppeMove::getByZiel($this->x,$this->y);
		$truppenmove2=TruppeMove::getByStart($this->x,$this->y);
		//Truppen die in dieses Dorf laufen
		if (!empty($truppenmove1)) {
			foreach($truppenmove1 as $move) {
				$ak=$move->get('aktion');
				unset($typ);
				if ($ak==2) $typ='def1';
				if ($ak>2) $typ='att1';
				if (isset($typ)) {
					$result[$typ]['anz']++;
					if (substr($typ,0,3)=='def') {
						$result[$typ]['text']='Unterst.';
						$result[$typ]['volltext']='Ankommende Unterstützung';
					}
					if (substr($typ,0,3)=='att') {
						$result[$typ]['text']='Angriff';
						$result[$typ]['volltext']='Ankommender Angriff';
					}
					if (!isset($result[$typ]['first'])) $result[$typ]['first']=strtotime($move->get('ziel_zeit'));
					if (strtotime($move->get('ziel_zeit'))<$result[$typ]['first'])
						$result[$typ]['first']=strtotime($move->get('ziel_zeit'));
				}
			}
		}
		//Truppen die von diesem Dorf weglaufen
		if (!empty($truppenmove2)) {
			foreach($truppenmove2 as $move) {
				if ($move->get('user')==$this->get('user')) {
					$ak=$move->get('aktion');
					unset($typ);
					if ($ak==1) $typ='def1';
					if ($ak==2) $typ='def2';
					if ($ak>2) $typ='att2';
					if (substr($typ,0,3)=='def') {
						$result[$typ]['text']='Unterst.';
						$result[$typ]['volltext']='Ausgehende Unterstützung';
					}
					if (substr($typ,0,3)=='att') {
						$result[$typ]['text']='Angriff';
						$result[$typ]['volltext']='Ausgehender Angriff';
					}
					if (isset($typ)) {
						$result[$typ]['anz']++;
						if (!isset($result[$typ]['first'])) $result[$typ]['first']=strtotime($move->get('ziel_zeit'));
						if (strtotime($move->get('ziel_zeit'))<$result[$typ]['first'])
							$result[$typ]['first']=strtotime($move->get('ziel_zeit'));
					}
				}
			}
		}
		$farben=array('def1'=>3,'def2'=>4,'att1'=>5,'att2'=>4);
		foreach($result as $typ => &$arr) {
				$arr['farbe']=$farben[$typ];
				if ($result[$typ]['anz']==0)
					unset($result[$typ]);
		}
		return $result;
	}

	public function entfernung($dorf) {
		$x1=$this->get('x');
		$y1=$this->get('y');
		$x2=$dorf->get('x');
		$y2=$dorf->get('y');
		return sqrt(pow($x1-$x2,2)+pow($y1-$y2,2));
	}

	public function versteck() {
		$geb=$this->gebeude2();
		$gebt=$this->gebeude2typ();
		$total=0;
		$versteck=explode(':',Diverses::get('versteck'));
		foreach($geb as $gid => $stufe) {
			if ($gebt[$gid]==23) {
				$total+=$versteck[$stufe-1];
			}
		}
		if ($this->user()->get('volk')==3) $total*=2;
		return $total;
	}

	public function genugSoldaten($user,$soldaten) {
		$truppe=Truppe::getByDU($this,$user);
		$soldatenVorhanden=$truppe->soldatenId();
		foreach($soldaten as $id=>$anz) {
			if ($anz>$soldatenVorhanden[$id])
				return false;
		}
		return true;
	}

	public function dorf_slots() {
		$geb2=$this->gebeude2();
		$geb2t=$this->gebeude2typ();
		$slots=0;
		for ($i=0;$i<=21;$i++) {
			if ($geb2t[$i]==25 or $geb2t[$i]==26) {
				if ($geb2[$i]>9) $slots++;
				if ($geb2t[$i]==26 and $geb2[$i]>14) $slots++;
				if ($geb2[$i]>19) $slots++;
			}
		}
		return $slots;
	}


	public function user() {
		return Spieler::getById($this->get('user'));
	}

	public function mauerSchutzbonus($addStufe=0) {
		$user=$this->user();
		$volk=$user->get('volk');
		$highest=$this->highest();
		$gid=0;
		$mauer=0;
		for($i=1;$i<=3;$i++){
			if ($highest[30+$i]>$mauer){
				$mauer=$highest[30+$i];
				$gid=30+$i;
			}
		}
		return GebeudeTyp::mauerSchutzBonus($gid,$mauer+$addStufe);
	}

	public function handler() {
		$highest=$this->highest();
		$handler_pro_mp=Diverses::get('handler_pro_mp_stufe');
		return $highest[17]*$handler_pro_mp;
	}

	public function freieHandler() {
		$handler=$this->handler();

		$handler_unterwegs=Handler::getByU($this);
		$handler_gebraucht=arrayObjectsSum($handler_unterwegs,'handler');

		$angebote=Angebot::getByD($this);
		$handler_gebraucht2=arrayObjectsSum($angebote,'handler');
		return $handler-$handler_gebraucht-$handler_gebraucht2;
	}

	public function handlerKapazitat($addStufe=0) {
		$volk=$this->user()->get('volk');
		$stragen=500;
		if ($volk==2) $stragen=1000;
		if ($volk==3) $stragen=750;
		$highest=$this->highest();
		$handelskontor=$highest[28]+$addStufe;
		$tragen=$stragen+$handelskontor*50;
		return $tragen;
	}

	public static function anzahl() {
		if (!isset(self::$anz)) {
			$sql="SELECT Count(*) as anz
				FROM `tr".ROUND_ID."_".self::$db_table."`;";
			$result=mysql_query($sql);
			$data=mysql_fetch_assoc($result);
			self::$anz=$data['anz'];
		}
		return self::$anz;
	}

	public function anzahlSiedler() {
		$truppe=Truppe::getByXYU($this->x,$this->y,$this->get('user'));
		$soldaten=$truppe->soldatenNr();
		return $soldaten[9];
	}

	public function deinDorf() {
		global $login_user;
		return $this->get('user')==$login_user->get('id');
	}

	public function baumeisterFrei($gid) {
		$auftrage=$this->bauAuftrage();
		//Keine Aufträge
		if (empty($auftrage)) return true;
		//Gebäude wird bereits ausgebaut
		if (isset($auftrage[$gid])) return false;
		$volk=Spieler::getById($this->get('user'))->get('volk');
		//Nur Römer können mehr als 1 Gebäude ausbauen
		if ($volk>1) return false;
		//Falls mehr als 1 gebaut wird
		if (sizeof($auftrage)>1) return false;
		foreach($auftrage as $auftrag_gid => $arr) {
			if ($gid<19 && $auftrag_gid<19) return false;
			if ($gid>18 && $auftrag_gid>18) return false;
		}
		return true;
	}

	public function baumeisterFreiMitGrund($gid) {
		$auftrage=$this->bauAuftrage();
		//Keine Aufträge
		if (empty($auftrage)) return true;
		//Gebäude wird bereits ausgebaut
		if (isset($auftrage[$gid]))
			return 'Dieses Gebäude wird bereits ausgebaut';
		$volk=Spieler::getById($this->get('user'))->get('volk');
		//Nur Römer können mehr als 1 Gebäude ausbauen
		if ($volk>1) return 'Es wird bereits gebaut';
		//Falls mehr als 1 gebaut wird
		if (sizeof($auftrage)>1) return 'Es wird bereits gebaut';
		foreach($auftrage as $auftrag_gid => $arr) {
			if ($gid<19 && $auftrag_gid<19)
				return 'Es wird bereits gebaut';
			if ($gid>18 && $auftrag_gid>18)
				return 'Es wird bereits gebaut';
		}
		return true;
	}

	public function neuerBauAuftrag($gid,$id,$zeit) {
		Auftrag::create($this->x,$this->y,10,$gid,$zeit,0,0);
	}

	//Zeit als timestamp angeben!
	public function neuerAuftrag($typ,$id,$zeit,$anz,$dauer) {
		Auftrag::create($this->x,$this->y,$typ,$id,$zeit,$anz,$dauer);
	}

	//Typen: 1Kaserne,2Stall,3Werkstatt,4Residenz/Palast
	//       5Akademie,6Waffenschmid,7Rüstungsschmid,8Ratshaus
	//       9Gebäude abreissen,10Gebäude bauen,11=Fallen bauen,
	//      12Held ausbilden
	public function auftrage($typ) {
		$auftrage=Auftrag::getByXY($this->x,$this->y);
		$auftrage=ArrayObjectsContaining($auftrage,'typ',$typ);
		return $auftrage;
	}

	//Liefert Zeitpunkt, zu dem die letzte Einheit/Objekt
	// fertig gestellt ist.
	public function auftrageNachsteStartZeit($typ) {
		$auftrage=$this->auftrage($typ);
		if (empty($auftrage)) return time();
		$auftrag=$auftrage[sizeof($auftrage)-1];
		$zeit= strtotime($auftrag->get('zeit'))+
			$auftrag->get('anzahl')*$auftrag->get('dauer');
		return $zeit;
	}

	public function bauAuftrage() {
		$gebeude1t=$this->gebeude1typ();
		$gebeude1=$this->gebeude1();
		$gebeude2=$this->gebeude2();
		$gebeude2t=$this->gebeude2typ();

		$auftrage=Auftrag::getByXY($this->x,$this->y);
		$result=array();
		foreach($auftrage as $auftrag) {
			if ($auftrag->bauAuftrag()) {
				$gid=$auftrag->get('id');
				if ($gid<19) {
					$id=$gebeude1t[$gid-1];
					$stufe=$gebeude1[$gid-1];
				}
				else {
					$id=$gebeude2t[$gid-19];
					$stufe=$gebeude2[$gid-19];
				}
				$result[$gid]['id']=$id;
				$result[$gid]['name']=GebeudeTyp::getById($id)->get('name');
				$result[$gid]['stufe']=$stufe+1;
				$result[$gid]['zeit']=$auftrag->get('zeit');
			}
		}
		return $result;
	}

	public function alleSoldaten() {
		$users=Truppe::getUsersByXY($this->x,$this->y);
		$soldaten=array();
		foreach($users as $user) {
			$truppe=Truppe::getByXYU($this->x,$this->y,$user);
			$soldaten=array_add($soldaten,$truppe->soldatenId());
		}
		return $soldaten;
	}

	public function eigeneTruppe() {
		return Truppe::getByXYU($this->x,$this->y,$this->get('user'));
	}

	public function expansion() {
		$exp=explode(':',$this->get('expansion'));
		$dorfer=array();
		for ($i=1;$i<=$exp[0];$i++) {
			$dorfer[]=Dorf::getByXY($exp[$i*2-1],$exp[$i*2]);
		}
		return $dorfer;
	}

	public function neueExpansion($x,$y) {
		$exp=explode(':',$this->get('expansion'));
		$exp[$exp[0]*2+1]=$x;
		$exp[$exp[0]*2+2]=$y;
		$exp[0]++;
		$this->set('expansion',implode(':',$exp));
	}

	public function __toString() {
		return 'Dorf: '.$this->get('name').' ( '.$this->get('x').' | '.$this->get('y').' )';
	}

	public function get($att) {
		return $this->data[$att];
	}

	public function set($att,$value) {
		$this->data[$att]=$value;
		$this->changed=true;
		$this->data['lastupdate']=date('Y-m-d H:i:s',time());
	}

	public function gebeude1() {
		return explode(':',$this->get('geb1'));
	}

	public function gebeude1typ() {
		return Land::getByXY($this->x,$this->y)->gebeude();
	}

	public function gebeude2() {
		return explode(':',$this->get('geb2'));
	}

	public function gebeude2typ() {
		return explode(':',$this->get('geb2t'));
	}

	public function gebeudeStufe($gid) {
		if ($gid<18) {
			$g1=$this->gebeude1();
			return $g1[$gid-1];
		}
		else {
			$g2=$this->gebeude2();
			return $g2[$gid-19];
		}
	}

	public function gebeudeTyp($gid) {
		if ($gid<18) {
			$g1=$this->gebeude1typ();
			return $g1[$gid-1];
		}
		else {
			$g2=$this->gebeude2typ();
			return $g2[$gid-19];
		}
	}

	public function neuesGebeude($gid,$id) {
		$geb2t=$this->gebeude2typ();
		$geb2t[$gid-19]=$id;
		$this->set('geb2t',implode(':',$geb2t));
	}

	public function gebeudeBau($gid,$stufe) {
		//Stufe=1 für Ausbau und -1 für Abriss
		if ($gid<19) {
			if ($stufe<0)
				new Errorlog('Abriss eines Landgebäudes.');
			$geb1=$this->gebeude1();
			$geb1[$gid-1]+=$stufe;
			if ($geb1[$gid-1]<0) $geb1[$gid-1]=0;
			$this->set('geb1',implode(':',$geb1));
		}
		else {
			$geb2=$this->gebeude2();
			$geb2[$gid-19]+=$stufe;
			if ($geb2[$gid-19]<0) $geb2[$gid-19]=0;
			if ($geb2[$gid-19]==0) {
				$geb2t=$this->gebeude2typ();
				$geb2t[$gid-19]=0;
				$this->set('geb2t',implode(':',$geb2t));
			}
			$this->set('geb2',implode(':',$geb2));
		}
		unset($this->highest);
	}

	public function gebeudeAnzahl() {
		$geb2_typ=$this->gebeude2typ();
		for ($i=1;$i<=40;$i++)
			$count[$i]=0;
		for ($i=0;$i<sizeof($geb2_typ);$i++) {
			$id=$geb2_typ[$i];
			if ($id>0)
				$count[$id]++;
		}
		return $count;
	}

	public function highestGid() {
		if (!isset($this->highest)) {
			$this->highest();
		}
		return $this->highestGid;
	}

	public function highest() {
		if (!isset($this->highest)) {
			$geb1_stufe=$this->gebeude1();
			$geb1_typ=$this->gebeude1typ();
			$geb2_stufe=$this->gebeude2();
			$geb2_typ=$this->gebeude2typ();
			//Höchste Stufe der Gebäude der Stadt berechnen
			// für Sägewerk,Lehmbrennerei,Eisenschmelze,Mühle,Bäckerei
			for ($i=1;$i<=40;$i++) {
				$this->highest[$i]=0;
				$this->highestGid[$i]=0;
			}
			for ($i=0;$i<sizeof($geb1_stufe);$i++) {
				$id=$geb1_typ[$i];
				$stufe=$geb1_stufe[$i];
				if ($this->highest[$id]<$stufe) {
					$this->highest[$id]=$stufe;
					$this->highestGid[$id]=$i+1;
				}
			}
			for ($i=0;$i<sizeof($geb2_stufe);$i++) {
				$id=$geb2_typ[$i];
				$stufe=$geb2_stufe[$i];
				if (!isset($this->highest[$id]) or $this->highest[$id]<$stufe) {
					$this->highest[$id]=$stufe;
					$this->highestGid[$id]=$i+19;
				}
			}
		}
		return $this->highest;
	}

	public function produktion() {
		//Produktion der Stufen der Gebäude
		$produktion_der_stufen=Diverses::produktion();
		//Land und die Gebäude darauf
		$land=Land::getByXY($this->get('x'),$this->get('y'));
		if ($land===NULL) {
			x('Dorf::produktion(): land nicht gefunden',$this);
		}
		$rohstoff_gebeude=$land->gebeude();

		//Gebäude auf dem Land und im Zentrum
		$geb1_stufe=$this->gebeude1();

		//Höchste Gebäude berechnen
		$highest=$this->highest();

		//Produktion:
		$dorf_produktion=array(0,0,0,0);
		for ($i=0;$i<sizeof($geb1_stufe);$i++)
			$dorf_produktion[$rohstoff_gebeude[$i]-1]+=
				$produktion_der_stufen[$geb1_stufe[$i]];
		//Boni der Mehrwertgebäude
		$dorf_produktion[0]=round($dorf_produktion[0]*
							(1+0.05*$highest[5]),0);
		$dorf_produktion[1]=round($dorf_produktion[1]*
							(1+0.05*$highest[6]),0);
		$dorf_produktion[2]=round($dorf_produktion[2]*
							(1+0.05*$highest[7]),0);
		$dorf_produktion[3]= round($dorf_produktion[3]*
			(1+0.05*($highest[8]+$highest[9])),0);
//		return array(3600,3600,3600,3600);
		return $dorf_produktion;
	}

	public function addRess($ress) {
		$lager=$this->lager();
		if (is_string($ress)) {
			$ress=explode(':',$ress);
		}
		foreach ($ress as $ressNr => $anz) {
			$lager[$ressNr]+=$anz;
		}
		$lagerNeu=implode(':',$lager);
		$this->set('lager',$lagerNeu);
	}

	public function subRess($ress) {
		$lager=$this->lager();
		if (is_string($ress)) {
			$ress=explode(':',$ress);
		}
		foreach ($ress as $ressNr => $anz) {
			$lager[$ressNr]-=$anz;
		}
		$lagerNeu=implode(':',$lager);
		$this->set('lager',$lagerNeu);
	}

	public function lager() {
		return explode(':',$this->get('lager'));
	}

	public function zeitGenugRess($kosten) {
		$produktion=$this->produktion();
		$lager=$this->lager();
		for ($i=0;$i<4;$i++)
			$time_x[$i]=($kosten[$i]-$lager[$i])/$produktion[$i]*3600;
		$timed=max($time_x);
		return time()+$timed;
	}

	public function genugRess($kosten) {
		$lager=$this->lager();
		return vector4gt($lager,$kosten);
	}

	public function genugLager($kosten) {
		list($lg[0],$lg[3])=$this->lagerGrosse();
		$lg[1]=$lg[0];$lg[2]=$lg[0];
		return vector4gt($lg,$kosten);
	}

	public function versorgung() {
		$users=Truppe::getUsersByXY($this->x,$this->y);
		$versorgung=$this->get('einwohner');
		if ($users===NULL) {
			e('Dorf->versorgung(): $users ist gleich Null');
		}
		else {
			foreach ($users as $user) {
				$truppe=Truppe::getByXYU($this->x,$this->y,$user);
				$versorgung+=$truppe->getVersorgung();
			}
		}
		return $versorgung;
	}

	private function update() {
		//Lager und Produktion
		$lager=$this->lager();
		$lager_grosse=$this->lagerGrosse();
		$produktion=$this->produktion();

		//Faktor in Produktions-stunden
		$faktor=(time()-strtotime($this->get('lastupdate')))/3600;

		//Ressourcen updaten
		$lager[0]+=$produktion[0]*$faktor;
		$lager[1]+=$produktion[1]*$faktor;
		$lager[2]+=$produktion[2]*$faktor;
		$lager[3]+=($produktion[3]-$this->versorgung())*$faktor;
		if ($lager[0]>$lager_grosse[0]) $lager[0]=$lager_grosse[0];
		if ($lager[1]>$lager_grosse[0]) $lager[1]=$lager_grosse[0];
		if ($lager[2]>$lager_grosse[0]) $lager[2]=$lager_grosse[0];
		if ($lager[3]>$lager_grosse[1]) $lager[3]=$lager_grosse[1];
		if ($lager[3]<0) $lager[3]=$this->starvingUnits($lager[3]);
		$lagerstring=implode(':',$lager);
		$this->set('lager',$lagerstring);
	}

	private function starvingUnits($getreide) {
		//Soldaten --> $anz,$typ,$user,$verbrauch
		$array=array();
		$array_index=0;

		$tusers=Truppe::getUsersByD($this);

		foreach($tusers as $tuser) {
			$truppe=Truppe::getByDU($this,$tuser);
			$soldaten=$truppe->soldatenId();
			foreach($soldaten as $id => $anz) {
				if ($anz>0) {
					$array[$array_index] = array ('anz'=>$anz,'typ'=>$id,'user'=>$tuser,'truppe'=>$truppe);
					$array_index++;
				}
			}
		}
		$x=microtime();
		while ($getreide<0) {

			$load_time=microtime();
			$grosste_versorgung=FALSE;
			foreach($array as $index => $soldaten) {
				$tid=$soldaten['typ'];
				if ($tid!='hero') {
				  $typ=TruppenTyp::getById($tid);
          $ver=$typ->get('versorgung');
        }
        else $ver=6;
        $total_versorgung=$soldaten['anz']*$ver;
        if ($grosste_versorgung===FALSE or $total_versorgung>$grosste_versorgung)
           $grosste_versorgung=$index;
			}
			if ($grosste_versorgung!==FALSE and $array[$grosste_versorgung]['anz']>0) {
				$tid=$array[$grosste_versorgung]['typ'];
				if ($tid!='hero') {
					$typ=TruppenTyp::getById($tid);
					$baukosten=$typ->baukosten();
				}
				else {
					$baukosten=100;
				}
				$getreide+=$baukosten[3];
				$array[$grosste_versorgung]['anz']--;
				$array[$grosste_versorgung]['truppe']->entfernen(array($tid=>1));
			}
			else break;
			if (round((microtime()-$x)*1000)>100) break;
		}

		if ($getreide<0) $getreide=0;
		return $getreide;
	}

	public function lagerGrosse() {
		//Lagergrösse der Stufen
		$lager_grosse_der_stufen=Diverses::lagerGrosse();
		//Lager grï¿½sse berechnen
		$lager_grosse[0]=0;
		$lager_grosse[1]=0;
		$geb2_stufe=$this->gebeude2();
		$geb2_typ=$this->gebeude2typ();
		for ($i=19;$i<=40;$i++) {
			if ($geb2_stufe[$i-19]>0){
				//Rohstofflager
				if ($geb2_typ[$i-19]==10)
					$lager_grosse[0]+=$lager_grosse_der_stufen[
										$geb2_stufe[$i-19]-1]*100;
				//Kornspeicher
				if ($geb2_typ[$i-19]==11)
					$lager_grosse[1]+=$lager_grosse_der_stufen[
										$geb2_stufe[$i-19]-1]*100;
				//Grosses Rohstofflager
				if ($geb2_typ[$i-19]==38)
					$lager_grosse[0]+=$lager_grosse_der_stufen[
										$geb2_stufe[$i-19]-1]*300;
				//Grosser Kornspeicher
				if ($geb2_typ[$i-19]==39)
					$lager_grosse[1]+=$lager_grosse_der_stufen[
										$geb2_stufe[$i-19]-1]*300;
			}
		}
		if ($lager_grosse[0]==0) $lager_grosse[0]=800;
		if ($lager_grosse[1]==0) $lager_grosse[1]=800;
		return $lager_grosse;
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

	public static function searchByKoordsOrName($x,$y,$dname) {
		global $login_dorf;
		$msg='';
		//Koordinaten sind prÃ¤ziser
		if ($x!='' and $y!='') $dname='';
		//nach Dorfname suchen
		if ($dname!='') {
			$dorfer=Dorf::getByName($dname);
			if (sizeof($dorfer)==0) $msg='Dorfname nicht vorhanden.';
			if (sizeof($dorfer)>1) $msg='Dorfname nicht eindeutig.';
			if (sizeof($dorfer)==1) {
				$dorf=$dorfer[0];
				$x=$dorf->get('x');
				$y=$dorf->get('y');
			}
		}
		//Keine Koordinaten gefunden
		if (($x=='' or $y=='')and $msg=='') $msg='Keine Zielangabe';
		//Truppen sind schon im Dorf
		if ($x==$login_dorf->get('x') and $y==$login_dorf->get('y'))
			$msg='+OBJECT_IS+ schon in diesem Dorf';
		//Dorf finden
		if (!isset($dorf)) {
			$dorf=Dorf::getByXY($x,$y);
		}
		//Dorf nicht gefunden
		if (!isset($dorf) and $msg=='') {
			$msg='Dorf nicht gefunden';
		}
		return array('msg'=>$msg,'x'=>$x,'y'=>$y,'dorf'=>$dorf);
	}

	public static function getAllKoords() {
		if (!isset(self::$all_koords)) {
			$sql="SELECT x,y FROM tr".ROUND_ID."_".self::$db_table.";";
			$result=mysql_query($sql);
			while ($data=mysql_fetch_assoc($result)) {
				self::$all_koords[$data['x']][$data['y']]=true;
			}
		}
		return self::$all_koords;
	}

	protected static function loadEntry($x,$y) {
		if (!isset(self::$loaded[$x][$y]) or !self::$loaded[$x][$y]) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE x=$x AND y=$y;";
			$result=mysql_query($sql);
			self::$loaded[$x][$y]=true;
			if (mysql_num_rows($result)==0) return NULL;
			$data=mysql_fetch_assoc($result);
			new Dorf($x,$y,$data);
		}
	}

	public static function getByName($name) {
		$sql="SELECT x,y FROM tr".ROUND_ID."_".self::$db_table."
				WHERE name='$name';";
		$sqlres=mysql_query($sql);
		if (mysql_num_rows($sqlres)==0) return array();
		$result=array();
		while ($data=mysql_fetch_assoc($sqlres)) {
			array_push($result,self::getByXY($data['x'],$data['y']));
		}
		return $result;
	}

	public static function isFree($x,$y) {
		$dorf=self::getByXY($x,$y);
		return $dorf==NULL;
	}

	public static function getByXY($x,$y) {
		if (!isset(self::$loaded[$x][$y]) or !self::$loaded[$x][$y]) {
			self::loadEntry($x,$y);
		}
		return @self::$objekte[$x][$y];
	}

	public static function create($x,$y,$user,$first_village) {
		if (!(Land::isFree($x,$y))) {
			new Errorlog('Dorf::create not possible. Land::isFree('.
				$x.':'.$y.' is false');
			return NULL;
		}
		
		if (is_object($user))
			$user=$user->get('id');
		
		//Gebï¿½ude zusammen setzen
		$geb1=strRepeatSep('0',18,':');
		$geb2=array_fill(0,22,0);
		$geb2t=array_fill(0,22,0);
		$geb2[7]=1;
		$geb2t[7]=15;
		$geb2=implode(':',$geb2);
		$geb2t=implode(':',$geb2t);

		//Dorf erstellen
		$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."
			(x,y,user,grosse,lager,geb1,geb2,geb2t,lastupdate)
			VALUES
				('$x','$y','$user','$first_village','750:750:750:750',
				 '$geb1','$geb2','$geb2t',NOW());";
		$result=mysql_query($sql);

		$starttyp=Diverses::get('startdorftyp');
		self::$loaded[$x][$y]=false;
		if ($first_village==1)
			Land::getByXY($x,$y)->set('typ',$starttyp);
		return self::getByXY($x,$y);
	}


	public static function check_change_Dorf() {
		global $login_user;
		if (isset($_GET['dorfx']) and isset($_GET['dorfy'])) {
			if (Dorf::getByXY($_GET['dorfx'],$_GET['dorfy'])->get('user')==$login_user->get('id')) {
				setcookie('dorfx',$_GET['dorfx']);
				setcookie('dorfy',$_GET['dorfy']);
				$_COOKIE['dorfx']=$_GET['dorfx'];
				$_COOKIE['dorfy']=$_GET['dorfy'];
			}
		}
		if (!isset($_COOKIE['dorfx']) or
			!isset($_COOKIE['dorfy'])) {
			$dorf=$login_user->startDorf();
		}
		else {
			$dorf=Dorf::getByXY($_COOKIE['dorfx'],$_COOKIE['dorfy']);
			if ($dorf==null){
				$dorf=$login_user->startDorf();
				$x=$dorf->get('x');
				$y=$dorf->get('y');
				setcookie('dorfx',$x);
				setcookie('dorfy',$y);
				$_COOKIE['dorfx']=$x;
				$_COOKIE['dorfy']=$y;
			}
		}
		return $dorf;
	}

	public static function saveAll() {
		if (self::$objekte!=NULL) {
			foreach (self::$objekte as $x => $arr) {
				foreach($arr as $y => $objekt) {
					if ($objekt->changed)
						$objekt->save();
				}
			}
		}
	}

}
?>
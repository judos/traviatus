<?php

class Updater {

	public static $save=false;

	public static function natur1h() {
		$value=Diverses::get('natur_last_update');
		if ($value<time()-3600) {
			Diverses::set('natur_last_update',now());

			$sql="SELECT x,y,typ FROM tr".ROUND_ID."_lander WHERE oase=1 AND annektiert_user=0;";
			$result=mysql_query($sql);
			while ($data=mysql_fetch_assoc($result)) {

				$typ=$data['typ'];

				$truppe=Truppe::getByXYU($data['x'],$data['y'],0);
				$tiere=$truppe->soldatenIdWithoutHero();

				$oase=Oase::getById($typ);
				$tiergrenze=$oase->tierGrenzeIds();

				foreach($tiere as $tierId => $anz) {

					$grenze=$tiergrenze[$tierId];

					//Nur Anteil berechnen der hinzugef�gt wird
					if ($grenze>0) {
						$tiere[$tierId]= 1/(pow($grenze,1.4)) * $anz * ($grenze-$anz);
						$tiere[$tierId]+= mt_rand(-2000,2000)/1000;
						if ($tiere[$tierId]<0) $tiere[$tierId]=0;
					}
				}

				$truppe->hinzufugen($tiere);
			}

			return TRUE;
		}
		return FALSE;
	}

	public static function spielerAllies() {
		static $done=FALSE;
		if (!$done) {
			$done=TRUE;
			$sql="SELECT user,einwohner,lastupdate FROM `tr".ROUND_ID."_dorfer`;";
			$result=mysql_query($sql);

			while($data=mysql_fetch_array($result)) {
				if (!isset($user_punkte[$data['user']]))
					$user_punkte[$data['user']]=0;
				$user_punkte[$data['user']]+=$data['einwohner'];
			}

			foreach($user_punkte as $userid => $einwohner) {
				$spieler=Spieler::getById($userid);
				$spieler->set('einwohner',$einwohner);
				$dauer=time()-strtotime($spieler->get('last_update'));
				$kps=($einwohner/2)*($dauer/86400);
				$spieler->addiereKulturpunkte($kps);
				$spieler->set('last_update',now());

				//Allys
				$aid=$spieler->get('ally');
				if ($aid>0) {
					if (!isset($allianz[$aid])) {
						$allianz[$aid]=Allianz::getById($aid);
						$allianz[$aid]->set('einwohner',0);
					}
					$allianz[$aid]->add('einwohner',$einwohner);
				}
			}
		}
	}

	public static function dorf($dorf) {
		//Ress im Dorf werden automatisch updated sobald das
		//Dorf �ber die Klasse Dorf instanziert wurde.
		self::spielerAllies();
		self::natur1h();

		$user=$dorf->user();
		$username=$user->get('name');
		$dorfx=$dorf->get('x');
		$dorfy=$dorf->get('y');

		$land=Land::getByXY($dorfx,$dorfy);
		
		self::dorfAuftrage($dorf,$user);
		self::dorfHandler($dorf);
		self::truppen();
	}
	
	public static function dorfAuftrage($dorf,$user) {
		$dorfx=$dorf->get('x');
		$dorfy=$dorf->get('y');
		//Auftrage bearbeiten
		$sql="SELECT * FROM tr".ROUND_ID."_others
			WHERE x=$dorfx AND y=$dorfy AND zeit<='".now()."';";
		$result=mysql_query($sql);
		while($data=mysql_fetch_assoc($result)) {
			if ($data['typ']==10) {	//Hauptgeb�ude, Bau
				$dorf->gebeudeBau($data['id'],1);
			}
			if ($data['typ']==9) {	//Hauptgeb�ude, Abriss
				$dorf->gebeudeBau($data['id'],-1);
			}
			if ($data['typ']==8) {	//Ratshaus
				$user->addiereKulturpunkte($data['anzahl']);
			}
			if ($data['typ']==7) {	//R�stungsschmid
				$user->verbessereRustung($data['id']);
			}
			if ($data['typ']==6) {	//Waffenschmid
				$user->verbessereWaffe($data['id']);
			}
			if ($data['typ']==5) {	//Akademie
				$user->erforsche($data['id']);
			}
			$select=array(1,2,3, 4,11, 12,13, 14,15);
			//Kaserne, Stall, Werkstatt
			//Residenz/Palast, Fallensteller
			//Held ausbilden, Held wiederbeleben
			//Grosse Kaserne, grosser Stall
			if (in_array($data['typ'],$select)) {

			//anzahl fertig gestellter Einheiten
				$anz=1+floor((time()-strtotime($data['zeit']))/
							$data['dauer']);
				if ($anz>$data['anzahl']) $anz=$data['anzahl'];
				if ($data['typ']==12 or $data['typ']==13) {
					$truppe=Truppe::getByXYU($dorfx,$dorfy,$user);
					$truppe->hinzufugen(array('hero'=>1));
				}
				elseif ($data['id']!=99) {
					$truppe=Truppe::getByXYU($dorfx,$dorfy,$user);
					$truppe->hinzufugen(array($data['id'] => $anz));
				}
				else {	//Fallen
					$dorf->set('fallen',$dorf->get('fallen')+$anz);
				}

				//Neue Anzahl speichern, falls nicht alle
				// fertig wurden
				if ($anz<$data['anzahl']) {
					$ende=strtotime($data['zeit'])+
						$anz*$data['dauer'];
					$zeit=date('Y-m-d H:i:s',$ende);
					$nochZuBauen=$data['anzahl']-$anz;
					$sql2="UPDATE `tr".ROUND_ID."_others`
						SET `zeit`='$zeit',
							`anzahl`='$nochZuBauen'
						WHERE keyid='".$data['keyid']."';";
					$result2=mysql_query($sql2);
				}
			}
		}
		$sql="DELETE FROM `tr".ROUND_ID."_others`
			WHERE `x`='$dorfx' AND `y`='$dorfy' AND
				`zeit`<='".now()."';";
		$result=mysql_query($sql);
	}
	
	public static function dorfHandler($dorf) {
		$dorfx=$dorf->get('x');
		$dorfy=$dorf->get('y');
		//H�ndler
		$sql="SELECT * FROM `tr".ROUND_ID."_handler`
			WHERE ((ursprung_x=$dorfx AND ursprung_y=$dorfy) OR
				 (nach_x=$dorfx AND nach_y=$dorfy)) AND
				ziel<='".now()."';";
		$result=mysql_query($sql);
		while($data=mysql_fetch_array($result)) {

			//ankunftszeit der n�chsten reise
			$ruckfahrt=date('Y-m-d H:i:s',2*strtotime(
					$data['ziel'])-strtotime($data['start']));
			$vx=$data['von_x'];
			$vy=$data['von_y'];
			$nx=$data['nach_x'];
			$ny=$data['nach_y'];
			$ux=$data['ursprung_x'];
			$uy=$data['ursprung_y'];

			//Falls H�ndler zuhause ist -> Datensatz l�schen
			if ($ux==$nx and $uy==$ny) {
				$sql2="DELETE FROM `tr".ROUND_ID."_handler`
					WHERE keyid='".$data['keyid']."';";
				$result2=mysql_query($sql2);
			}
			//Andernfalls H�ndler umkehren lassen
			else {
				//Rohstoffe ausladen
				$zielDorf=Dorf::getByXY($nx,$ny);
				$zielDorf->addRess($data['ress']);
				
				//H�ndler umkehren lassen
				$sql2="UPDATE `tr".ROUND_ID."_handler`
					SET nach_x=$vx, nach_y=$vy,
						von_x=$nx, von_y=$ny, ress='0:0:0:0',
						start='".$data['ziel']."',
						ziel='".$ruckfahrt."'
					WHERE keyid='".$data['keyid']."';";
				mysql_query($sql2);

				$dorfVon=Dorf::getByXY($vx,$vy);
				$userVon=$dorfVon->user();

				$dorfNach=Dorf::getByXY($nx,$ny);
				$userNach=$dorfNach->user();

				//Bericht
				$betreff=$dorfVon->get('name').
					' beliefert '.$dorfNach->get('name');
				//Wer von wo hat gesendet
				$who_from_where='<a href="?page=spieler&name='.$userVon->get('name').'">'.$userVon->get('name').
					'</a> aus Dorf <a href="?page=karte-show&x='.$vx.'&y='.$vy.'">'.$dorfVon->get('name').'</a>';

				//Inhalt der Nachricht
				$text='1::'.$who_from_where.chr(13).'2:'.$data['ress'];

				//Bericht erstatten fall n�tig
				Automessages::bericht($userVon,$betreff,$text);
				if ($userVon->get('id')!=$userNach->get('id'))
					Automessages::bericht($userNach,$betreff,$text);
			}
		}
	}
	
	public static function truppen() {

		//Truppen ankommen lassen
		$sql="SELECT keyid FROM `tr".ROUND_ID."_truppen_move`
			WHERE `ziel_zeit`<='".now()."';";
		$result=mysql_query($sql);
		while ($data=mysql_fetch_assoc($result)) {
			
			$truppe=TruppeMove::getById($data['keyid']);
			$start_dorf=$truppe->startDorf();
			$ziel_dorf=$truppe->zielDorf();
			$sx=$start_dorf->get('x');
			$sy=$start_dorf->get('y');
			$zx=$ziel_dorf->get('x');
			$zy=$ziel_dorf->get('y');
			
			$dieserUser=$truppe->getUser();

			//Neues Dorf gr�nden
			if ($truppe->get('aktion')==1)	{
				//Falls Dorf noch frei ist
				if (Dorf::isFree($zx,$zy)) {
					//Truppe l�schen
					$truppe->delete();

					//Dorf gr�nden
					Dorf::create($zx,$zy,$dieserUser,0);

					//Expansion festhalten
					$start_dorf->neueExpansion($zx,$zy);
				}
				//Falls Dorf schon besiedelt wurde, Siedler umkehren lassen
				else {
					Automessages::siedlerUmgekehrt($dieserUser,
						$truppe->get('ziel_zeit'));
						
					$truppe->turnBack();
				}
			}
			//Unterst�tzung
			if ($truppe->get('aktion')==2)	{
				//Truppe l�schen
				$truppe->delete();

				//Soldaten einf�gen
				$zieltruppe=Truppe::getByXYU($zx,$zy,$dieserUser);
				$zieltruppe->hinzufugen($truppe->get('truppen'));

				//Ziel user
				$zielUser=$ziel_dorf->user();

				if ($truppe->get('msg')==1){
					$betreff=$start_dorf->get('name').' unterst�tzt '.
						$ziel_dorf->get('name');
					$msg="1:Absender:".$dieserUser->get('name').
						" aus Dorf ".$start_dorf->get('name').chr(13).
						"3:".$dieserUser->get('volk').chr(13)."4:Einheiten:".
						$truppe->get('truppen').chr(13)."5:0";

					Automessages::unterstutzung($dieserUser,$betreff,$msg,$truppe->get('ziel_zeit'));
	
					if ($zielUser !=$dieserUser) {
						Automessages::unterstutzung($zielUser,$betreff,$msg,$truppe->get('ziel_zeit'));
					}
				}
			}
			
			//Angriff normal oder Raubzug
			if ($truppe->get('aktion')==3 or $truppe->get('aktion')==4)	{
				$angreifer=$dieserUser;
				$angreifer_dorf=$start_dorf;

				//Deff Truppen laden
				$deff_dorf=new DeffDorf($ziel_dorf);
				
				$off=$truppe->soldatenId();
				if ($off['hero']==1){
					$held=Held::getByUser($angreifer);
					$off['heroboni']=$held->offWert();
				}
				$off['volk']=$angreifer->get('volk');
				//TODO: deffdorf can't know attack_user
				$schlachtFeld=$deff_dorf->attack($off,$truppe->get('aktion'));
				
				if ($schlachtFeld->someOffSurvived()) {
					$truppe->setNumbers($schlachtFeld->getRemainingOff());
					$truppe->turnBack();
				}
				else
					$truppe->delete();
				
				$deff_dorf->saveDeffInDb();
				
				$bericht = $schlachtFeld->getBericht();
				//schickt den Bericht an alle Beteiligten
				$users = $schlachtFeld->getUsers();
				$betreff = $schlachtFeld->getBerichtBetreff();
				$bericht->sendToUsers($users,$betreff,Bericht::TYPE_ANGRIFFE);
			}
		}
	}
}
?>
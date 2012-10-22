<?php

class Updater {

	public static $save=false;

	public static function natur1h() {
		$value=Diverses::get('natur_last_update');
		if ($value<time()-3600) {
			Diverses::set('natur_last_update',now());

			$sql="SELECT x,y,typ FROM tr".ROUND_ID."_lander WHERE oase=1 AND annektiert=0;";
			$result=mysql_query($sql);
			while ($data=mysql_fetch_assoc($result)) {

				$typ=$data['typ'];

				$truppe=Truppe::getByXYU($data['x'],$data['y'],0);
				$tiere=$truppe->soldatenNr();

				$oase=Oase::getById($typ);
				$tiergrenze=$oase->tierGrenze();

				foreach($tiere as $tierNr => $anz) {

					$grenze=$tiergrenze[$tierNr];

					//Nur Anteil berechnen der hinzugef¸gt wird
					if ($grenze>0) {
						$tiere[$tierNr]= 1/(pow($grenze,1.4)) * $anz * ($grenze-$anz);
						$tiere[$tierNr]+= mt_rand(-2000,2000)/1000;
						if ($tiere[$tierNr]<0) $tiere[$tierNr]=0;
					}
				}

				$truppe->hinzufugen(implode(':',$tiere));
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
		//Dorf ¸ber die Klasse Dorf instanziert wurde.
		self::spielerAllies();

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
			if ($data['typ']==10) {	//Hauptgeb‰ude, Bau
				$dorf->gebeudeBau($data['id'],1);
			}
			if ($data['typ']==9) {	//Hauptgeb‰ude, Abriss
				$dorf->gebeudeBau($data['id'],-1);
			}
			if ($data['typ']==8) {	//Ratshaus
				$user->addiereKulturpunkte($data['anzahl']);
			}
			if ($data['typ']==7) {	//R¸stungsschmid
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
		//H‰ndler
		$sql="SELECT * FROM `tr".ROUND_ID."_handler`
			WHERE ((ursprung_x=$dorfx AND ursprung_y=$dorfy) OR
				 (nach_x=$dorfx AND nach_y=$dorfy)) AND
				ziel<='".now()."';";
		$result=mysql_query($sql);
		while($data=mysql_fetch_array($result)) {

			//ankunftszeit der n‰chsten reise
			$ruckfahrt=date('Y-m-d H:i:s',2*strtotime(
					$data['ziel'])-strtotime($data['start']));
			$vx=$data['von_x'];
			$vy=$data['von_y'];
			$nx=$data['nach_x'];
			$ny=$data['nach_y'];
			$ux=$data['ursprung_x'];
			$uy=$data['ursprung_y'];

			//Falls H‰ndler zuhause ist -> Datensatz lˆschen
			if ($ux==$nx and $uy==$ny) {
				$sql2="DELETE FROM `tr".ROUND_ID."_handler`
					WHERE keyid='".$data['keyid']."';";
				$result2=mysql_query($sql2);
			}
			//Andernfalls H‰ndler umkehren lassen
			else {
				//Rohstoffe ausladen
				$zielDorf=Dorf::getByXY($nx,$ny);
				$zielDorf->addRess($data['ress']);
				
				//H‰ndler umkehren lassen
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

				//Bericht erstatten fall nˆtig
				Automessages::bericht($userVon,$betreff,$text);
				if ($userVon->get('id')!=$userNach->get('id'))
					Automessages::bericht($userNach,$betreff,$text);
			}
		}
	}
	
	public static function truppen() {

		//Truppen ankommen lassen
		$sql="SELECT * FROM `tr".ROUND_ID."_truppen_move`
			WHERE `ziel_zeit`<='".now()."';";
		$result=mysql_query($sql);
		while ($data=mysql_fetch_array($result)) {

			$sx=$data['start_x'];$sy=$data['start_y'];
			$zx=$data['ziel_x'];$zy=$data['ziel_y'];
			$start_dorf=Dorf::getByXY($sx,$sy);
			$ziel_dorf=Dorf::getByXY($zx,$zy);
			$dieserUser=Spieler::getById($data['user']);

			//Neues Dorf gr¸nden
			if ($data['aktion']==1)	{
				//Falls Dorf noch frei ist
				if (Dorf::isFree($zx,$zy)) {
					//Truppe lˆschen
					$sql3="DELETE FROM `tr".ROUND_ID."_truppen_move`
						WHERE keyid='".$data['keyid']."';";
					$result3=mysql_query($sql3);

					//Dorf gr¸nden
					Dorf::create($zx,$zy,$data['user'],0);

					//Expansion festhalten
					$start_dorf->neueExpansion($zx,$zy);
				}
				//Falls Dorf schon besiedelt wurde, Siedler umkehren lassen
				else {
					$ziel_zeit=date('Y-m-d H:i:s',2*strtotime(
						$data['ziel_zeit'])-strtotime($data['start_zeit']));
					$sql3="UPDATE `tr".ROUND_ID."_truppen_move`
						SET start_x='$zx', start_y='$zy',
							ziel_x='$sx',ziel_y='$sy',
							aktion='2',start_zeit='".$data['ziel_zeit']."',
							ziel_zeit='$ziel_zeit'
						WHERE keyid='".$data['keyid']."';";
					$result3=mysql_query($sql3);

					Automessages::siedlerUmgekehrt($dieserUser,
						$data['ziel_zeit']);
				}
			}
			//Unterst¸tzung
			if ($data['aktion']==2)	{
				//Truppe lˆschen
				$sql3="DELETE FROM `tr".ROUND_ID."_truppen_move`
					WHERE keyid='".$data['keyid']."';";
				$result3=mysql_query($sql3);

				//Soldaten einf¸gen
				$zieltruppe=Truppe::getByXYU($data['ziel_x'],
												$data['ziel_y'],$data['user']);
				$zieltruppe->hinzufugen($data['truppen']);

				//Ziel user
				$zielUser=$ziel_dorf->user();

				if ($data['msg']==1){
					$betreff=$start_dorf->get('name').' unterst¸tzt '.
						$ziel_dorf->get('name');
					$msg='4'.chr(13)."1:Absender:".$dieserUser->get('name').
						" aus Dorf ".$start_dorf->get('name').chr(13).
						"3:".$dieserUser->get('volk').chr(13)."4:Einheiten:".
						$data['truppen'].chr(13)."5:0";

					Automessages::unterstutzung($dieserUser,$betreff,$msg,$data['ziel_zeit']);

					if ($zielUser->get('id')!=$dieserUser->get('id')) {
						Automessages::unterstutzung($zielUser,$betreff,$msg,$data['ziel_zeit']);
					}
				}
			}
			/*
			//Angriff normal oder Raubzug
			if ($data['aktion']==3 or $data['aktion']==4)	{
				//Name und Volk des Angreiffers herausfinden
				$sql2="SELECT `name`,`volk` FROM `tr".ROUND_ID."_user` WHERE `id`='".$data['user']."';";
				$result2=mysql_query($sql2);
				$data2=mysql_fetch_array($result2);
				$name=$data2['name']; $volk=$data2['volk'];

				//Name des Angreiffenden Dorfes
				$sql2="SELECT `name` FROM `tr".ROUND_ID."_dorfer` WHERE `x`='".$data['start_x']."' AND `y`='".$data['start_y']."';";
				$result2=mysql_query($sql2);
				$data2=mysql_fetch_array($result2);
				$angreiffendes_dorf=$data2['name'];

				//Deff Truppen laden
				unset($deff_truppen);
				unset($deff_truppen_spieler);
				$sql2="SELECT tr".ROUND_ID."_truppen.troops,tr".ROUND_ID."_truppen.user,tr".ROUND_ID."_user.volk
					FROM `tr".ROUND_ID."_truppen`,`tr".ROUND_ID."_user`
					WHERE tr".ROUND_ID."_truppen.x='".$data['ziel_x']."' AND tr".ROUND_ID."_truppen.y='".$data['ziel_y']."'
						AND tr".ROUND_ID."_user.id=tr".ROUND_ID."_truppen.user;";
				$result2=mysql_query($sql2);
				for ($i=1;$i<=mysql_num_rows($result2);$i++)
				{
					$data2=mysql_fetch_array($result2);

					$t=split(':',$data2['troops']);
					$v=$data2['volk'];
					for ($j=0;$j<=9;$j++)
					{
						//Deff Truppen gesamtzahl und zahl jedes Spielers speichern
						$deff_truppen[$j+1+$v*10-10]+=$t[$j];
						$deff_truppen_spieler[$data2['user']][$j+1+$v*10-10]+=$t[$j];
						$deff_truppen_string_start[$data2['user']].=$t[$j];
						if ($j<9) $deff_truppen_string_start[$data2['user']].=':';
					}
				}

				//Angriffstruppen laden
				$angriffs_truppen_09=split(':',$data['truppen']);
				for ($i=1;$i<=10;$i++)
					$angriffs_truppen[$i+$volk*10-10]=$angriffs_truppen_09[$i-1];

				//Kampfsim
				$neu_truppen=kampfsim($troops,$angriffs_truppen,$deff_truppen,$data['aktion']-2);

				//Angriffstruppen berechnen, string formen
				$anz_angreifer=0;
				$neu_truppen1_string='';
				$verluste_angreifer_string='';
				for ($i=1;$i<=10;$i++)
				{
					$anz_angreifer+=$neu_truppen[1][$i+$volk*10-10];
					$neu_truppen1_string.=$neu_truppen[1][$i+$volk*10-10];
					if ($i<10) $neu_truppen1_string.=':';
					$verluste_angreifer[$i]=$angriffs_truppen[$i+$volk*10-10]-$neu_truppen[1][$i+$volk*10-10];

					$verluste_angreifer_string.=$verluste_angreifer[$i];
					if ($i<10) $verluste_angreifer_string.=':';
				}
				if ($anz_angreifer>0)	//Zur√ºck schicken
				{
					$neue_ziel_zeit=date('Y-m-d H:i:s',2*strtotime($data['ziel_zeit'])-strtotime($data['start_zeit']));

					$sql2="UPDATE `tr".ROUND_ID."_truppen_move` SET `ziel_x`='".$data['start_x']."', `ziel_y`='".$data['start_y']."',
						`start_x`='".$data['ziel_x']."', `start_y`='".$data['ziel_y']."', `start_zeit`='".$data['ziel_zeit']."',
						`ziel_zeit`='$neue_ziel_zeit', `aktion`='2', `truppen`='$neu_truppen1_string', `msg`='0' ";
				}
				else		//oder l√∂schen
					$sql2="DELETE FROM `tr".ROUND_ID."_truppen_move` ";

				$sql2.=" WHERE `user`='".$data['user']."' AND `ziel_x`='".$data['ziel_x']."' AND `ziel_y`='".$data['ziel_y']."'
						AND `ziel_zeit`='".$data['ziel_zeit']."' AND `truppen`='".$data['truppen']."';";
				$result2=mysql_query($sql2);

				//Deff truppen berechnen
				$anzahl_deff=0;
				$sql2="SELECT tr".ROUND_ID."_truppen.user,tr".ROUND_ID."_user.volk,tr".ROUND_ID."_user.name,
						tr".ROUND_ID."_dorfer.user AS dorfuser,tr".ROUND_ID."_dorfer.name AS dorfname
					FROM `tr".ROUND_ID."_truppen`,`tr".ROUND_ID."_user`,`tr".ROUND_ID."_dorfer`
					WHERE tr".ROUND_ID."_truppen.x='".$data['ziel_x']."' AND tr".ROUND_ID."_truppen.y='".$data['ziel_y']."' AND
					tr".ROUND_ID."_user.id=tr".ROUND_ID."_truppen.user AND tr".ROUND_ID."_truppen.x=tr".ROUND_ID."_dorfer.x
						AND tr".ROUND_ID."_truppen.y=tr".ROUND_ID."_dorfer.y;";
				$result2=mysql_query($sql2);
				for ($i=1;$i<=mysql_num_rows($result2);$i++)
				{
					$data2=mysql_fetch_array($result2);

					$volk=$data2['volk'];
					$deff_truppen_string='';
					$deff_truppen_verluste_string='';
					$anz=0;
					for ($j=1;$j<=10;$j++)
					{
						//Deff Truppen prozentsatz der √ºbriggebliebenen berechnen
						if ($deff_truppen[$j+$volk*10-10]==0)
							$prozent=0;
						else
							$prozent=$deff_truppen_spieler[$data2['user']][$j+$volk*10-10]/$deff_truppen[$j+$volk*10-10];

						$deff_truppen_einheit=round($prozent*$neu_truppen[2][$j+$volk*10-10],0);
						$anz+=$deff_truppen_einheit;
						$deff_truppen_string.=$deff_truppen_einheit;
						if ($j<10) $deff_truppen_string.=':';

						$verluste=$deff_truppen_spieler[$data2['user']][$j+$volk*10-10]-$deff_truppen_einheit;
						$deff_truppen_verluste_string.=$verluste;
						if ($j<10) $deff_truppen_verluste_string.=':';
					}
					if ($anz>0)
						$sql3="UPDATE `tr".ROUND_ID."_truppen` SET `troops`='$deff_truppen_string' ";
					else
						$sql3="DELETE FROM `tr".ROUND_ID."_truppen` ";
					$sql3.="WHERE `x`='".$data['ziel_x']."' AND `y`='".$data['ziel_y']."' AND `user`='".$data2['user']."';";
					$result3=mysql_query($sql3);

					//Nachrichten verschicken
					$anzahl_deff++;
					if ($data2['dorfuser']==$data2['user'])	//Besitzer des Dorfes
					{
						$temp3='<a href="spieler.php?name='.$data2['name'].'">'.$data2['name'].'</a> aus Dorf <a href="'.
							'karte.php?do=show&x='.$data['ziel_x'].'&y='.$data['ziel_y'].'">'.$data2['dorfname'].'</a>';
						$deff_string[$anzahl_deff]="1:Verteidiger:".$temp3.chr(13).
							"3:".$volk.chr(13)."4:Einheiten:".$deff_truppen_string_start[$data2['user']].chr(13).
							"4:Verluste:".$deff_truppen_verluste_string.chr(13);
						$name_des_angegriffenen=$data2['name'];
						$name_des_dorfes=$data2['dorfname'];
					}
					else	//Unterst√ºtzungen sonst
					{
						$temp4='<a href="spieler.php?name='.$data2['name'].'">'.$data2['name'].'</a>';
						$deff_string[$anzahl_deff]="1:Unterst√ºtzung:von ".$temp4.chr(13).
							"3:".$volk.chr(13)."4:Einheiten:".$deff_truppen_string_start[$data2['user']].chr(13).
							"4:Verluste:".$deff_truppen_verluste_string.chr(13);

						$sql3="INSERT INTO `tr".ROUND_ID."_msg` (`an`,`typ`,`zeit`,`betreff`,`text`) VALUES
							( '".$data2['name']."','2','".$data['ziel_zeit']."','Unterst√ºtzung in ".$data2['dorfname'].
							" wurde angegriffen','4".chr(13)."1:".$name.":Hat eine Ihrer Unterst√ºtzungen angegriffen".chr(13).
							"3:".$volk.chr(13)."4:Einheiten:".$deff_truppen_string_start[$data2['user']].chr(13).
							"4:Verluste:".$deff_truppen_verluste_string."');";
						$result3=mysql_query($sql3);
					}

				}

				//Nachricht an den Angegriffenen
				$temp1='4';
				for ($j=1;$j<=$anzahl_deff;$j++)
					$temp1.=':4';

				$temp2='<a href="spieler.php?name='.$name.'">'.$name.'</a> aus Dorf <a href="karte.php?do=show&x='.
					$data['start_x'].'&y='.$data['start_y'].'">'.$angreiffendes_dorf.'</a>';

				$sql3="INSERT INTO `tr".ROUND_ID."_msg` (`an`,`typ`,`zeit`,`betreff`,`text`) VALUES
					( '$name_des_angegriffenen','3','".$data['ziel_zeit']."','$angreiffendes_dorf greift ".
					$name_des_dorfes." an','$temp1".chr(13)."1:Angreifer:$temp2".chr(13).
					"3:$volk".chr(13)."4:Einheiten:".$data['truppen'].chr(13).
					"4:Verluste:$verluste_angreifer_string".chr(13);
				for ($j=1;$j<=$anzahl_deff;$j++)
					$sql3.=$deff_string[$j];
				$sql3.="');";
				$result3=mysql_query($sql3);

				//Nachricht an den Angreiffer
				if ($data['aktion']==3) $betreff='Angriff';
				else $betreff='Raubzug';
				$betreff.=' auf '.$name_des_dorfes;
				if ($anz_angreifer==0) $text="5".chr(13)."1:Angreifer:Ihre Truppen".chr(13)."3:$volk".chr(13).
					"4:Einheiten:".$data['truppen'].chr(13)."4:Verluste:$verluste_angreifer_string".chr(13).
					"1:Info:Es sind keine Truppen zur√ºckgekehrt";
				else
				{
					$text="$temp1".chr(13)."1:Angreifer:Ihre Truppen".chr(13)."3:$volk".chr(13).
						"4:Einheiten:".$data['truppen'].chr(13)."4:Verluste:$verluste_angreifer_string".chr(13);
				for ($j=1;$j<=$anzahl_deff;$j++)
					$text.=$deff_string[$j];
				}
				$sql3="INSERT INTO `tr".ROUND_ID."_msg` (`an`,`typ`,`zeit`,`betreff`,`text`) VALUES
					('$name','3',NOW(),'$betreff','$text');";
				$result3=mysql_query($sql3);
			}*/
		}
	}
}
?>
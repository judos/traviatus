<?php
$do=$_GET['do'];

//Gebäude bauen
if ($do=='build') {
	$id=$_GET['id'];
	$gid=$_GET['gid'];
	$gebeude=GebeudeTyp::getById($id);
	$stufe=$login_dorf->gebeudeStufe($gid);
	$nachste_stufe=$stufe+1;
	if ($gebeude->baubar($login_dorf,$nachste_stufe,$gid)===true) {
		if ($login_dorf->baumeisterFrei($gid)) {
			
			//Auftrag speichern
			$time=$gebeude->bauzeit($nachste_stufe,$login_dorf);

			$login_dorf->neuerBauAuftrag($gid,$id,$time+time());

			//Einwohner und Rohstoffe aktualisieren
			$login_dorf->subRess($gebeude->bauKosten($nachste_stufe));

			$einwohner=$login_dorf->get('einwohner')+
									$gebeude->get('arbeiter');
			$login_dorf->set('einwohner',$einwohner);

			//Gerüst für das Gebäude im Dorf
			if ($gid>18) $login_dorf->neuesGebeude($gid,$id);

			if ($gid<19) gotoP('dorf1');
			if ($gid>18) gotoP('dorf2');
		}
	}
}


//Gebäude Ausbau abbrechen
if ($do=='builddel') {
	$id=$_GET['id'];
	$gid=$_GET['gid'];
	if ($gid<19) {$stufe=$geb1_stufe[$gid-1];}
	if ($gid>18) {$stufe=$geb2_stufe[$gid-19];}

	//Auftrag löschen
	$x=$login_dorf->get('x');
	$y=$login_dorf->get('y');
	$auftrage=Auftrag::getByXYTI($x,$y,10,$gid);
	if (sizeof($auftrage)>1) {
		new Errorlog('Gebäudeausbau abbrechen - '.
								 'mehrere Gebäude der gleichen gid gefunden.');
	}
	if (sizeof($auftrage)==0) {
		new Errorlog('Gebäudeausbau abbrechen - '.
								 'kein Auftrag gefunden');
		gotoP('dorf1');
	}
	$auftrag=$auftrage[0];
	$auftrag->delete();

	//Gebäude wegputzen
	if ($gid>18) $login_dorf->gebeudeBau($gid,-1);

	//Einwohner und Rohstoffe aktualisieren
	if ($stufe<1) $stufe=1;
	$gebeude=GebeudeTyp::getById($id);
	$kosten=$gebeude->bauKosten($stufe);
	$login_dorf->addRess($kosten);

	$einwohner=$login_dorf->get('einwohner')-
							$gebeude->get('arbeiter');
	$login_dorf->set('einwohner',$einwohner);

	if ($gid<19) gotoP('dorf1');
	if ($gid>18) gotoP('dorf2');
}


//Held punkte verteilen
if ($do=='held_boni') {
	$index=$_GET['p'];
	$held=$login_user->held();
	if ($held!==NULL) {
		$held->addPoint($index);
	}
}

//Held punkte zurücksetzen
if ($do=='hero_reset_bonus') {
	$held=$login_user->held();
	if ($held!==NULL) {
		$held->resetBoni();
	}
}

//Held umbenennen
if ($do=='hero_rename') {
	$held=$login_user->held();
	if ($held!==NULL) {
		$held->set('name',make_save_text($_POST['rename']));
	}
}


//Held wiederbeleben
if ($do=='revive_hero') {
	$faktor=Diverses::get('held_kosten');	//Wieviel mal mehr kostet ein Held
	$faktor_wiederbelebung= Diverses::get('held_wiederbeleben_exp');	//1 Erfahrung kostet 1% zusätzlich

	//Prüfen, dass kein Held bereits existiert
	$helden=Held::getByUser($login_user);
	$helden_lebend=ArrayObjectsContaining($helden,'lebt',true);
  if (empty($helden_lebend)) {
    $hid=$_GET['hid'];
    $held=Held::getById($hid);
    if ($held!==NULL) {
      if ($held->get('user')==$login_user->get('id') and $held->get('lebt')==0) {
        $tid=$held->get('troop_id');
        $typ=TruppenTyp::getById($tid);
        $faktor_individuell =$faktor+$held->get('erfahrung')*$faktor_wiederbelebung;
        $kosten=vector4mul($typ->baukosten(),$faktor_individuell);
        if ($login_dorf->genugRess($kosten)) {
 	        $auftrage=$login_user->auftrage(13);
 	        if (empty($auftrage)) {
	          $dauer=$faktor_individuell*$typ->bauzeit($login_dorf);
	          $login_dorf->subRess($kosten);
						$zeit=$login_dorf->auftrageNachsteStartZeit(13);
	          $login_dorf->neuerAuftrag(13,$hid,$zeit+$dauer,1,$dauer);
	          $held->set('lebt',1);
	        }
        }
      }
    }
  }
}

//Truppen rekrutieren oder Held ausbilden
if ($do=='recrut_kaserne' or $do=='recrut_stall' or $do=='recrut_werkstatt' or
	$do=='recrut_pr' or $do=='recrut_fallen' or $do=='recrut_hero') {
	if ($do=='recrut_kaserne') $typ=1;
	if ($do=='recrut_stall') $typ=2;
	if ($do=='recrut_werkstatt') $typ=3;
	if ($do=='recrut_pr') $typ=4;
	if ($do=='recrut_fallen') $typ=11;
	if ($do=='recrut_hero') {
		$typ=12;
		$_POST['re']='hero';
		$_POST['thero']=1;
	}
	if (isset($_POST['re'])) {
		$tid=$_POST['re'];
		$anzahl=$_POST['t'.$tid];
		if ($anzahl>0) {
			if ($tid=='hero') {	//Held ausbilden
				$tid=$_GET['tid'];
				$einheit=TruppenTyp::getById($tid);
				$baukosten=vector4mul($einheit->baukosten(),3);
				$dauer=3*$einheit->bauzeit($login_dorf);
				//Prüfen, dass kein Held bereits existiert
				$helden=Held::getByUser($login_user);
				$helden_lebend=ArrayObjectsContaining($helden,'lebt',true);
				if (!empty($helden_lebend)) $anzahl=0;
				//Prüfen dass Soldat vorhanden ist
				$truppe=$login_dorf->eigeneTruppe();
        $soldaten=$truppe->soldatenId();
        $als_held_ausbildbar=array();
        foreach($soldaten as $id => $anz) {
          if ($anz>0) {
            $truppentyp=TruppenTyp::getById($id);
            if ($truppentyp->get('typ')<3)
              array_push($als_held_ausbildbar,$id);
          }
        }
        if (!in_array($tid,$als_held_ausbildbar)) $anzahl=0;
			}
			elseif ($tid!=99) {	//Normale Truppen
        $einheit=TruppenTyp::getById($tid);
        $baukosten=$einheit->baukosten();
        $dauer=$einheit->bauzeit($login_dorf);
      }
      else {	//Fallen
      	$baukosten=explode(':',Diverses::get('fallen_kosten'));
      	$faktor=explode(':',Diverses::get('fallen_bauzeit'));
      	$highest=$login_dorf->highest();
      	$dauer=$faktor[0]*pow($faktor[1],$highest[36]-1);

      	//Anzahl mögliche Fallen berechnen
      	$akt_fallen=Diverses::fallen($highest[36]);
        $werdenGebaut=0;
        $auftrage=$login_dorf->auftrage(11);
        if (!empty($auftrage)) {
          foreach($auftrage as $index => $auftrag) {
            $werdenGebaut+=$auftrag->get('anzahl');
          }
        }
      	$moglich=$akt_fallen-$login_dorf->get('fallen')-$werdenGebaut;
      	if($anzahl>$moglich) $anzahl=$moglich;
      }
      $kosten=vector4mul($baukosten,$anzahl);
			if ($login_dorf->genugRess($kosten) and $anzahl>0) {
				$login_dorf->subRess($kosten);
				$zeit=$login_dorf->auftrageNachsteStartZeit($typ);
				$login_dorf->neuerAuftrag($typ,$tid,$zeit+$dauer,$anzahl,$dauer);
				if ($typ==12) {
					Held::create($login_user,$tid);
					$truppe=$login_dorf->eigeneTruppe();
					$truppe->entfernen(array($tid=>1));
				}
			}
		}
	}
}


//Forschen in der Akademie
if ($do=='research') {
	$tid=$_GET['tid'];
	$einheit=TruppenTyp::getById($tid);
	$kosten=$einheit->forschungskosten();
	$auftrage=$login_dorf->auftrage(5);

	if ($login_dorf->genugRess($kosten) and empty($auftrage)) {
		$login_dorf->subRess($kosten);
		$zeit=time()+$einheit->forschungszeit();
		$login_dorf->neuerAuftrag(5,$tid,$zeit,0,0);
	}
}

//Waffen oder Rüstungen verbessern
if ($do=='res1' or $do=='res2') {
	$tid=$_GET['tid'];
	$index=($tid-1)%10;
	$wr=substr($_GET['do'],-1)-1;
	if ($wr==0) $stufen=$login_user->waffen();
	if ($wr==1) $stufen=$login_user->rustungen();
	if ($wr<0 or $wr>1) gotoP('dorf2');
	$einheit=TruppenTyp::getById($tid);
	if ($login_user->einheitErforscht($tid) and $einheit->get('typ')<4) {

		$kosten=$einheit->verbesserungskosten($stufen[$index]+1);
		$zeit=$einheit->verbesserungszeit($stufen[$index]+1);

		$highest=$login_dorf->highest();
		$stufe_tech=$stufen[$index];
		$stufe_geb=$highest[12+$wr];
		if ($stufe_geb>$stufe_tech) {

			$auftrage=$login_dorf->auftrage(6+$wr);
			$auftrage_user=$login_user->auftrage(6+$wr);
			$auftrage_ids=array();
			foreach($auftrage_user as $a) {
				array_push($auftrage_ids,$a->get('id'));
			}
			if (empty($auftrage)) {
				if (!in_array($tid,$auftrage_ids)) {
					if ($login_dorf->genugRess($kosten)) {
						$fertig=time()+$zeit;
						$login_dorf->subRess($kosten);
						$login_dorf->neuerAuftrag(6+$wr,$tid,$fertig,0,0);
					}
				}
			}
		}
	}
}
//Gebäude abreissen
if ($do=='crash') {
	$gid=$_POST['abriss_gid'];

	$stufe=$login_dorf->gebeudeStufe($gid);
	$typ=$login_dorf->gebeudeTyp($gid);

	$gebeude=GebeudeTyp::getById($typ);
	$auftrage=$login_dorf->auftrage(9);

	if (empty($auftrage)) {

		$zeit=time()+$gebeude->bauzeit($stufe-1,$login_dorf)/3;

		$login_dorf->neuerAuftrag(9,$gid,$zeit,0,0);
	}
}
//Gebäude abriss abbrechen
if ($do=='delcrash') {
	$auftrage=$login_dorf->auftrage(9);
	if(!empty($auftrage)) {
		$auftrag=$auftrage[0];
		$auftrag->delete();
	}
}
//Fest starten
if ($do=='fest') {
	$auftrage=$login_dorf->auftrage(8);
	if (empty($auftrage)) {
		$fest_id=$_GET['x'];
		$fest=Fest::getById($fest_id);
		$kosten=$fest->get('kosten');
		if ($login_dorf->genugRess($kosten)) {
			if ($fest->feierbar($login_dorf)) {
				$kp=$fest->kp($login_dorf);
				$login_dorf->subRess($kosten);

				$zeit=time()+$fest->dauer($login_dorf);
				$kp=$fest->kp($login_dorf);
				$login_dorf->neuerAuftrag(8,$fest_id,$zeit,$kp,0);
			}
		}
	}
}


//Rohstoffe versenden
if ($do=='sendgoods') {
	for ($i=1;$i<=4;$i++) {
		$ress[$i-1]=$_POST['r'.$i];
		if (!isset($ress[$i-1])) $ress[$i-1]=0;
	}
	$ziel=Dorf::getByXY($_POST['zielx'],$_POST['ziely']);
	$msg=Handler::create($login_user,$login_dorf,$ziel,$ress);
}


//Neues Angebot
if ($do=='newoffer') {
	$r1=$_POST['r1'];
	$typ1=$_POST['typ1'];
	$r2=$_POST['r2'];
	$typ2=$_POST['typ2'];
	$max=9999;
	if ($_POST['d1']==1) $max=$_POST['d2'];
	$ally=savePost('ally',0);
	$msg=Angebot::create($login_dorf,$typ1,$r1,$typ2,$r2,$max,$ally);
	$_GET['r1']=$r1;
	$_GET['r2']=$r2;
	$_GET['t1']=$typ1;
	$_GET['t2']=$typ2;
	$_GET['max']=$max;
	$_GET['ally']=$ally;
	if ($msg===1 or $msg===true) unset($msg);
}




//Angebot löschen
if ($do=='deloffer') {
	$id=$_GET['keyid'];
	$angebot=Angebot::getById($id);
	if ($angebot!==NULL) {
		$ress=$angebot->angebotRess();
		$login_dorf->addRess($ress);
		$angebot->delete($login_dorf);
	}
}


//Angebot kaufen
if ($do=='buyoffer') {
	$keyid=$_GET['keyid'];
	$angebot=Angebot::getById($keyid);
	if ($angebot!==NULL) {
		$_GET['ress']=$angebot->tradeRess();
		$_GET['user']=$angebot->dorf()->user()->get('id');
		$msg=$angebot->buy($login_dorf);
	}
	else $msg='Angebot nicht gefunden';

	if ($msg!='') $_GET['s']=2;
}
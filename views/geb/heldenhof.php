<?php


$helden=Held::getByUser($login_user);
$helden_lebend=ArrayObjectsContaining($helden,'lebt',true);
$helden_tot=ArrayObjectsContaining($helden,'lebt',false);

$s=saveGet('s','');

if (empty($helden_lebend)) {	//Neuen Held ausbilden oder wiederbeleben

	$truppe=$login_dorf->eigeneTruppe();
	$soldaten=$truppe->soldatenId();
	$als_held_ausbildbar=array();
	
	foreach($soldaten as $tid => $anz) {
		if ($anz>0 and $tid!='hero') {
			$typ=TruppenTyp::getById($tid);
			if ($typ!==NULL and $typ->get('typ')<3)
				array_push($als_held_ausbildbar,$typ);
		}
	}

	$faktor=Diverses::get('held_kosten');	//Wieviel mal mehr kostet ein Held
	$faktor_wiederbelebung= Diverses::get('held_wiederbeleben_exp');	//1 Erfahrung kostet 1% zusätzlich

	//Neue Helden ausbilden
	echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
		<tr class="cbg1"><td colspan="2">Held ausbilden</td></tr>';
	if (empty($als_held_ausbildbar)) {
		echo'<tr class="c"><td colspan="2">Du hast keine Einheit die als Held ausgebildet werden kann.</td></tr>';
	}
	foreach($als_held_ausbildbar as $typ) {
		$tid=$typ->get('id');
		$kosten=vector4mul($typ->baukosten(),$faktor);
		echo'<tr><td>
			<table class="f10" cellpadding="0" cellspacing="2"
				width="100%">
			<tbody><tr><td rowspan="2" class="s7"
				valign="top" width="6%">
			<img class="unit" src="img/un/u/'.$tid.'.gif"></td>
			<td class="s7"><div>
			<a href="#" onclick="Popup(1,'.$tid.');"> '.
			$typ->get('name').'</a>
			<span class="c f75">(Vorhanden: '.$soldaten[$tid].')</span>
			</div></td></tr>
			<tr><td class="s7">';
		for ($j=0;$j<4;$j++)
			echo'<img class="res" src="img/un/r/'.($j+1).'.gif">'.
				$kosten[$j].' | ';

		echo'<br><img class="res" src="img/un/r/5.gif">6 |
			<img class="clock" src="img/un/a/clock.gif"> '.
				zeit_dauer($faktor*$typ->bauzeit($login_dorf)).'</nobr>
			</td></tr></tbody></table></td>
			<td>';
		if ($login_dorf->genugRess($kosten))
			echo'<a href="?page='.$page.'&gid='.$gid.'&do=recrut_hero&tid='.$tid.'">Ausbilden</a>';
		else
			echo'<span class="c">Zuwenig Rohstoffe</span>';
		echo'</td></tr>';
	}
	echo'</table>';


	//Helden wiederbeleben
	if (!empty($helden_tot)) {
    echo'<br><table class="tbg" cellpadding="2" cellspacing="1"><tbody>
      <tr class="cbg1"><td colspan="2">Helden wiederbeleben</td></tr>';
    foreach($helden_tot as $held) {
      $tid=$held->get('troop_id');
      $typ=TruppenTyp::getById($tid);
      $name=$held->get('name');
      if ($name=='') $name='Unbekannter Held';
      $faktor_individuell =$faktor+$held->get('erfahrung')*$faktor_wiederbelebung;
      $kosten=vector4mul($typ->baukosten(),$faktor_individuell);
      echo'<tr><td>
        <table class="f10" cellpadding="0" cellspacing="2"
          width="100%">
        <tbody><tr><td rowspan="2" class="s7"
          valign="top" width="6%">
        <img class="unit" src="img/un/u/'.$tid.'.gif"></td>
        <td class="s7"><div>
        <a href="#" onclick="Popup(1,'.$tid.');"> '.
        $name.'</a>
        <span class="c f75">(Level: '.$held->level().', Erfahrung: '.$held->get('erfahrung').')</span>
        </div></td></tr>
        <tr><td class="s7">';
      for ($j=0;$j<4;$j++)
        echo'<img class="res" src="img/un/r/'.($j+1).'.gif">'.
          $kosten[$j].' | ';

      echo'<br><img class="res" src="img/un/r/5.gif">6 | <nobr>
        <img class="clock" src="img/un/a/clock.gif"> '.
          zeit_dauer($faktor_individuell*$typ->bauzeit($login_dorf)).'</nobr>
        </td></tr></tbody></table></td>
        <td>';
      if ($login_dorf->genugRess($kosten))
      	echo'<a href="?page='.$page.'&gid='.$gid.'&do=revive_hero&hid='.$held->get('keyid').'">Wiederbeleben</a>';
      else
      	echo'<span class="c">Zuwenig Rohstoffe</span>';
      echo'</td></tr>';
    }
    echo'</table>';
  }
}
else {
	$auftrage=$login_user->auftrage(12);
	if (!empty($auftrage)) {	//Held wird ausgebildet
		$auftrag=$auftrage[0];
		$dauer=strtotime($auftrag->get('zeit'))-time();
		echo'<p>Dein Held wird gerade ausgebildet und ist in <b>
			<span id=timer1>'.zeit_dauer($dauer).'</span></b> um <b>'.date('H:i',strtotime($auftrag->get('zeit'))).'</b> fertig.</p>';
	}
	else
		$auftrage2=$login_user->auftrage(13);
	if (!empty($auftrage2)) {	//Held wird wiederbelebt
		$auftrag=$auftrage2[0];
		$dauer=strtotime($auftrag->get('zeit'))-time();
		echo'<p>Dein Held wird gerade wiederbelebt und ist in <b>
			<span id=timer1>'.zeit_dauer($dauer).'</span></b> um <b>'.date('H:i',strtotime($auftrag->get('zeit'))).'</b> fertig.</p>';
	}

	if (empty($auftrage) and empty($auftrage2)) {	//Held lebt
		$held=$helden_lebend[0];

		//Punkteverteilung und Stärke des Helden anzeigen
		if ($s=='') {
			$typ=TruppenTyp::getById($held->get('troop_id'));

			$text=array('Angriff','Verteidigung','Off-Bonus','Def-Bonus','Regeneration');
			$funktion=array('angriff','verteidigung','offwert','deffwert','regeneration');
			$anzeige=array('$0','$0/$1','$0%','$0%','$0%/Tag');
			$punkte=$held->punkteVerteilt();
			$freie_punkte=$held->freiePunkte();
			$max_moglich=$held->level()*5+5;
			if (max($punkte)+$freie_punkte+5<$max_moglich)
				$max_moglich=max($punkte)+$freie_punkte+5;

			echo'<table class="tbg" cellpadding="2" cellspacing="1">
				<tbody><tr>
				<td colspan="5" class="rbg">
				<a href="?page=build&gid='.$gid.'&s=rename">'.$held->get('name').' Stufe '.$held->level().'
				<span class="info">('.$typ->get('name').')</span></a></td>
				</tr>';

			foreach($text as $index=>$titel) {
				$link='<span class="c">(<b>+</b>)</span>';
				if ($freie_punkte>0)
					$link='<a href="?page=build&gid='.$gid.'&do=held_boni&p='.$index.'">(+)</a>';
				$werte=$held->$funktion[$index]();
				
				$anzeige_aktuell=$anzeige[$index];
				if (!is_array($werte))
					$werte=array($werte);

				$anzeige_aktuell=str_replace('$0',$werte[0],$anzeige_aktuell);
				if (sizeof($werte)>1)
					$anzeige_aktuell=str_replace('$1',$werte[1],$anzeige_aktuell);

				echo'<tr>
					<td class="s7">'.$titel.':</td>
					<td class="s7" width="70">'.$anzeige_aktuell.'</td>
					'.draw_balken($punkte[$index]/$max_moglich).'
					<td width="35">'.$link.'</td>
					<td width="35">'.$punkte[$index].'</td>
					</tr>';
			}
			echo'<tr>
				<td colspan="5" class="empty"></td>
				</tr><tr>
				<td class="s7" title="bis zur nächsten Stufe">Erfahrung:</td>
				<td class="s7">'.$held->erfahrungProzent().'%</td>
				'.draw_balken($held->erfahrungProzent()/100).'
				<td></td>
				<td><b>'.$freie_punkte.'</b></td>
				</tr>
				</tbody>
				</table>';
			if ($held->level()==0) {
				echo'<p>Bei Stufe <b>0</b> kannst du die Punkteverteilung deines Helden noch ändern.<br>
					<a href="?page=build&gid='.$gid.'&do=hero_reset_bonus">» Punkte neu verteilen</a></p>';
			}
			echo'<p>Dein Held besitzt <b>'.$held->get('hp').'</b>% seiner Lebenspunkte<br>
				und hat für dieses Dorf bisher <b>0</b>
				<a href="?page=build&gid='.$gid.'&s=lands">Ländereien</a> erobert.</p>';
		}
		if ($s=='rename') {
			echo'<form method="post" action="?page=build&gid='.$gid.'&do=hero_rename">
				<table class="tbg" style="width:300px;" cellpadding="2" cellspacing="1" >
				<tbody>
				<tr class="rbg"><td colspan="2">Namen ändern</td></tr>
				<tr>
				<td class="s7">Name:</td>
				<td class="s7"><input class="fm" style="width:93%" name="rename" value="'.$held->get('name').'" maxlength="24"></td>
				</tr></tbody>
				</table><p>';
			Outputer::button('ok','ok');
			echo'</p></form>';
		}

		//Ländereien Infos anzeigen
		if ($s=='lands') {
			echo'<h2>Was sind Ländereien (Oasen)?</h2>
				Auf der Karte um dein Dorf gibt es Wälder, Lehmgebiete,
				Berge und Getreidefelder. Diese Rohstoffvorkommen kannst
				du mit deinem Helden annektieren und damit die Rohstoffproduktion
				deines Dorfes steigern. Bisher besitzt dein Dorf noch keine Ländereien<br><br>
				<h2>Wie erobert man Ländereien?</h2>
				Um eine Oase zu annektieren, musst du alle dort befindlichen Einheiten
				besiegen und mit deinem Helden dort einmarschieren. Die Oase muss im 7x7
				Kartenausschnitt um dein Dorf liegen. Für die erste Oase benötigst du
				einen Heldenhof Stufe 10, für die 2. Oase Stufe 15 und mit Heldenhof
				Stufe 20 kann dein Dorf drei Oasen besitzen.<br><br>
				<h2>Kann man Oasen auch wieder abgeben?</h2>
				Ja, das ist möglich. Besetzte Oasen können vom Gegner geplündert werden.
				Pro Angriff im Intervall von min. 10 Minuten 10% der im Heimatdorf
				befindlichen Rohstoffe. Daher kann es sinnvoll sein, eine Oase wieder zu
				verlassen um dem Gegner weniger angriffsfläche zu geben. Sobald du mindestens
				eine Oase hast, findest du diese Option anstatt der Hilfe an dieser Stelle.<br>'.
				'<a href="?page=build&highest=37">Zurück</a>';
		}
	}
}



function draw_balken($wert) {
	$px=floor($wert*207);
	if ($px<1) $px=1;
	if ($px>207) $px=207;
	return'<td align="left" width="207" style="padding:2px 7px;">
        	<img src="img/un/a/c3.gif" style="width:'.$px.'px; height:10px;"></td>';
}




?>
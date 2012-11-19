<?php

$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');

$handler=$login_dorf->handler();

$anz_ver_handler=$login_dorf->freieHandler();

$tragen=$login_user->handlerTragfahigkeit($login_dorf);
$speed=$login_user->handlerSpeed();

$tab=(int)saveGet('tab',0);

echo'<p class="txt_menue">
	<a href="?page=build&gid='.$gid.'" '.($tab==0?'class="selected"':'').'>Rohstoffe verschicken</a> |
	<a href="?page=build&gid='.$gid.'&tab=2" '.($tab==2?'class="selected"':'').'>Kaufen</a> |
	<a href="?page=build&gid='.$gid.'&tab=3" '.($tab==3?'class="selected"':'').'>Verkaufen</a></p><p>
	</p>
	<script language="JavaScript" type="text/javascript">
	var haendler = '.$anz_ver_handler.';
	var carry = '.$tragen.';

	</script>';


//Ressourcen verschicken bestätigen
if ($tab==1) {
	$transport=array();
	for ($i=0;$i<4;$i++)
		$transport[$i]=$_POST['r'.($i+1)];

	$need_handler=ceil(array_sum($transport)/$tragen);
	$dname=$_POST['dname'];
	$x=$_POST['x'];
	$y=$_POST['y'];
	if ($need_handler>0) {
		if ($need_handler<=$anz_ver_handler) {

      $result=Dorf::searchByKoordsOrName($x,$y,$dname);
      $msg=str_replace('+OBJECT_IS+','Die Händler sind',$result['msg']);

      if ($msg=='') {
        $x=$result['x'];
        $y=$result['y'];
        $dauer=sqrt(pow($x-$dx,2)+pow($y-$dy,2))/$speed*3600;
        $dorf=$result['dorf'];
        $spieler=$dorf->user();
        echo'<form method="post" action="?page=build&gid='.$gid.'&do=sendgoods">

          <input type=hidden name="zielx" value="'.$x.'">
          <input type=hidden name="ziely" value="'.$y.'">

          <table valign="top" cellpadding="0"
          cellspacing="0" width="100%">
          <tbody><tr valign="top">
          <td width="45%"><table class="f10"><tbody>';

				$rname=array(1=>'Holz',2=>'Lehm',3=>'Eisen',4=>'Getreide');
        for($i=1;$i<=4;$i++) {
          echo'<tr><td><img class="res" src="img/un/r/'.$i.'.gif"></td>
            <td>'.$rname[$i].':</td><td align="right">
            <input class="fm" name="r'.$i.'" value="'.$transport[$i-1].'" size="4"
              readonly="readonly" type="text"></td>
            <td class="s7 f8 c">(+'.$tragen.')</td></tr>';
        }
				echo'</tbody></table></td><td valign="top" width="55%">
          <p class="f135">'.$dorf->get('name').' ('.$x.'|'.$y.')</p>
          <table><tbody><tr class="left">
          <td>Spieler:</td>
          <td>'.$spieler->getLink().'</td></tr>
          <tr class="left"><td>Dauer:</td>
          <td>'.zeit_dauer($dauer).'</td></tr>
          <tr class="left"><td>Händler:</td>
          <td>'.$need_handler.'</td></tr>
          </tbody></table>
          </td></tr></tbody></table><p>';
        Outputer::button('s1','ok');
        echo '</p></form>';
      }
    }
    else
       $msg='Zu wenig Händler. Es werden '.$need_handler.' benötigt,
       	es sind im Moment aber nur '.$anz_ver_handler.' Händler verfügbar.';
  }
	else
		$msg='Keine Rohstoffe ausgewählt.';
	if ($msg!='') {
		unset($_GET['s']);
		gotoP('build&gid='.$gid.'&x='.$x.'&y='.$y.'&dname='.$dname.'&t='.implode($transport,':'));
	}
}

//Ressourcen verschicken & Ziel eingeben
elseif ($tab==0) {
	$dorfer_zusatz='<a href="#" onclick="dgei(\'mpx\').value=$x;dgei(\'mpy\').value=$y;dgei(\'dname\').value=\'$dname\';">Ziel</a>';
	$x=saveGet('x','');
	$y=saveGet('y','');
	$dname=saveGet('dname','');
	$transport=explode(':',saveGet('t','0:0:0:0'));
	echo'<form method="post" name="snd"
		action="?page=build&gid='.$gid.'&tab=1">
	  <table valign="top" cellpadding="0" cellspacing="0" width="100%">
    <tbody><tr valign="top">
    <td width="45%">
    <table class="f10"><tbody>';
  $rname=array(1=>'Holz',2=>'Lehm',3=>'Eisen',4=>'Getreide');
  for($i=1;$i<=4;$i++) {
  	echo'<tr><td><a href="#" onclick="max_res('.$i.')">
      <img class="res" src="img/un/r/'.$i.'.gif"></a></td>
      <td>'.$rname[$i].':</td><td align="right">
      <input class="fm" name="r'.$i.'" id="r'.$i.'" value="'.$transport[$i-1].'" size="4"
        maxlength="5" onkeyup="check_res(this);max_res_calc()" tabindex="'.$i.'" type="text" autocomplete="off"></td>
      <td class="s7 f8">
      <a href="#" onclick="add_res('.$i.')">(+'.$tragen.')</a></td></tr>';
  }
  echo'<tr><td></td><td>Händler:</td><td><span id="handler">0</span></td></tr>';

  echo'</tbody></table>
    </td><td valign="top" width="55%"><table class="f10">
    <tbody><tr>
    <td colspan="2">Händler '.$anz_ver_handler.'/'.$handler.'<br><br>
    </td></tr>
    <tr><td colspan="2"><span class="f135 b">Dorf:</span>
    <input class="fm" name="dname" id="dname" value="'.$dname.'" size="10"
      maxlength="20" tabindex="5" type="text"></td></tr>
    <tr><td colspan="2"><i>oder</i></td></tr>
    <tr><td colspan="2"><span class="f135 b">X:
    <input class="fm" id="mpx" name="x" value="'.$x.'" size="2"
      maxlength="4" tabindex="6" type="text" autocomplete="off">
    Y: <input class="fm" id="mpy" name="y" value="'.$y.'" size="2"
      maxlength="4" tabindex="7" type="text" autocomplete="off">
    </span></td></tr></tbody></table></td></tr></tbody></table>
    <p>';
	Outputer::button('s1','ok');
	echo'</p>';
	if (!isset($msg) and !isset($done))
		echo'<p>Jeder deiner Händler kann <b>'.$tragen.'
		</b> Rohstoffe tragen.</p>';
	elseif ($msg!='') echo'<p class="b c5">'.$msg.'</p>';
	elseif ($done!='') echo'<p class="b c3">'.$done.'</p>';
}

//Händler anzeigen
if ($tab==0 or $tab==1) {
  unset($dorfnamen);

	$ankommend=Handler::getByZ($login_dorf);
  if (!empty($ankommend)) {

    ob_start();
    foreach ($ankommend as $handler) {
    	//Ankommende Eigene Händler werden bereits unten angezeigt
    	if ($handler->get('ursprung_x')!=$dx or $handler->get('ursprung_y')!=$dy)
				$handler->draw_transport();
    }
    $c=ob_get_contents();
    ob_end_clean();
    if ($c!='') {
	    echo'<p class="b">Ankommende Händler:</p><p></p>';
	    echo $c;
	  }
  }
  $eigene=Handler::getByU($login_dorf);
  if (!empty($eigene)) {
  	echo'<p class="b">Eigene Händler unterwegs:</p><p></p>';
	  foreach ($eigene as $handler)  {
			$handler->draw_transport();
	  }
  }
}

//Angebote anschauen und kaufen
if ($tab==2) {
	//Filter mechanismus
	//suche id
	$s_id=saveGet('n',saveGet('nv',null));
	unset($_GET['nv']);
	//biete id
	$b_id=saveGet('b',saveGet('bv',null));
	$b_v=saveGet('bv',null);
	unset($_GET['bv']);
	//bei klick auf vorheriges -> suche/biete alle
	if ($s_id==saveGet('nv',null)) unset($s_id);
	if ($b_id==saveGet('bv',null)) unset($b_id);
	//biete und suche gleich
	if (isset($s_id) and isset($b_id) and $s_id==$b_id) {
		if ($b_id!=$b_v) unset($s_id);
		else unset($b_id);
	}

	//Faktor
	$fak=saveGet('f',saveGet('fv',null));
	if (!isset($s_id)) $s_id='';
	if (!isset($b_id)) $b_id='';

	//link für Filter Angebote
	$lk='?page=build&gid='.$gid.'&tab=2&nv='.$s_id.'&bv='.$b_id.'&fv='.$fak.'&';
	//klasse für selektierte
	$c=' class="hr"';

  echo'<table><tr><td>
  	<table class="tbg" cellpadding="2" cellspacing="1" style="height:45px;">
	  <tbody><tr class="rbg">
    <td colspan="4">Suche</td>
    </tr>
    <tr><td'.($s_id==1?$c:'').'><a href="'.$lk.'n=1"><img src="img/un/r/1.gif" alt="Holz" title="Holz"></a></td>
    <td'.($s_id==2?$c:'').'><a href="'.$lk.'n=2"><img src="img/un/r/2.gif" alt="Lehm" title="Lehm"></a></td>
    <td'.($s_id==3?$c:'').'><a href="'.$lk.'n=3"><img src="img/un/r/3.gif" alt="Eisen" title="Eisen"></a></td>
    <td'.($s_id==4?$c:'').'><a href="'.$lk.'n=4"><img src="img/un/r/4.gif" alt="Getreide" title="Getreide"></a></td></tr></tbody>
    </table></td><td>&nbsp;</td>

    <td><table class="tbg" cellpadding="2" cellspacing="1" style="height:45px;"><tbody>
    <tr class="rbg"><td colspan="3">Angebot/Nachfrage</td>
    </tr><tr>
    <td'.($fak==2?$c:'').'><a href="'.$lk.'f=2">>1</a></td>
    <td'.($fak==1?$c:'').'><a href="'.$lk.'f=1">>=1</a></td>
    <td'.($fak==0?$c:'').'><a href="'.$lk.'f=0">Alle</a></td>
    </tr>
    </tbody></table></td><td>&nbsp;</td>

    <td><table class="tbg" cellpadding="2" cellspacing="1" style="height:45px;">
    <tbody><tr class="rbg">

    <td colspan="4">Biete</td>
    </tr>
    <tbody><tr><td'.($b_id==1?$c:'').'><a href="'.$lk.'b=1"><img src="img/un/r/1.gif" alt="Holz" title="Holz"></a></td>
    <td'.($b_id==2?$c:'').'><a href="'.$lk.'b=2"><img src="img/un/r/2.gif" alt="Lehm" title="Lehm"></a></td>
    <td'.($b_id==3?$c:'').'><a href="'.$lk.'b=3"><img src="img/un/r/3.gif" alt="Eisen" title="Eisen"></a></td>
    <td'.($b_id==4?$c:'').'><a href="'.$lk.'b=4"><img src="img/un/r/4.gif" alt="Getreide" title="Getreide"></a></td></tr></tbody>
    </table></td></tr></table><br>

  	<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
    <tr class="rbg"><td colspan="8"><a name="h2"></a>Angebote am Marktplatz</td></tr>
    <tr class="cbg1"><td colspan="2">Biete</td><td colspan="2">Suche</td><td>Spieler</td>
    <td>Händler</td><td>Dauer</td><td>Aktion</td></tr>';

	$rohstoff_name=array(1=>'Holz',2=>'Lehm',3=>'Eisen',4=>'Getreide');
	
	$where='';
	if (isset($s_id) and $s_id!='') $where.=' AND angebot_id='.$s_id;
	if (isset($b_id) and $b_id!='') $where.=' AND nachfrage_id='.$b_id;
	if ($fak==1) $where.=' HAVING faktor>=1';
	if ($fak==2) $where.=' HAVING faktor>1';
	
	$sql="SELECT keyid,angebot/nachfrage as faktor FROM `tr".ROUND_ID."_angebote`
		WHERE (ursprung_x!=$dx OR ursprung_y!=$dy) ".$where.";";
	$result=mysql_query($sql);
	$alle=array();
	while($data=mysql_fetch_array($result)){
		$angebot=Angebot::getById($data['keyid']);
		$dorf=$angebot->dorf();
		$spieler=$dorf->user();
		$spielerspeed=$spieler->handlerSpeed();

		$weg=$login_dorf->entfernung($dorf);
		$h=$weg/$spielerspeed;
		$zeit=round($h*3600);
		if ($h<=$angebot->get('maxzeit')) {
			if ($angebot->get('ally')==0 or ($login_user->get('ally')==$spieler->get('ally'))) {
				if (!isset($alle[$zeit])) $alle[$zeit]=array();
				array_push($alle[$zeit],$angebot);
			}
		}
	}

	//Nach Lieferzeit sortieren
	ksort($alle);

	//Alle Lieferzeiten durchgehen
	foreach($alle as $dauer => $arr) {
		//Alle Angebote einer bestimmten Lieferzeit durchgehen
		foreach($arr as $angebot) {
			$spieler=$angebot->dorf()->user();
			$ress=$angebot->nachfrageRess();

      $handler=ceil($angebot->get('nachfrage')/$tragen);
      $anz++;
      echo'<tr><td><img class="res" src="img/un/r/'.$angebot->get('angebot_id').'.gif" title="'.
        $rohstoff_name[$angebot->get('angebot_id')].'"></td><td>'.$angebot->get('angebot').'</td><td>
        <img class="res" src="img/un/r/'.$angebot->get('nachfrage_id').'.gif" title="'.
        $rohstoff_name[$angebot->get('nachfrage_id')].'"></td><td>'.$angebot->get('nachfrage').'</td>
        <td><a href="?page=karte-show&x='.$angebot->get('ursprung_x').
          '&y='.$angebot->get('ursprung_y').'">'.$spieler->get('name').'</a></td>
        <td>'.$handler.'</td>
        <td>'.zeit_dauer($dauer).'</td>';


      if ($login_dorf->genugRess($ress)) {
        if ($handler<=$anz_ver_handler) {
          echo'<td><a href="?page=build&gid='.$gid.
            '&tab=4&do=buyoffer&keyid='.$angebot->get('keyid').'&b='.$b_id.'&n='.$s_id.'&f='.$fak.'">Angebot annehmen</a>';
        }
        else echo'<td class="c">Zu wenig Händler';
      }
      else echo'<td class="c">Zu wenig Rohstoffe';
      echo'</td></tr>';
    }
  }
  if ($anz==0)
    echo'<tr bgcolor="#f5f5f5"><td class="rowpic" colspan="8"><span class="c">
      <b>Keine Angebote vorhanden</b></span></td></tr>';

  echo'</tbody></table>';

}

//Angebote reinstellen
if ($tab==3) {
	$ress=array(1=>'Holz',2=>'Lehm',3=>'Eisen',4=>'Getreide');
	echo'<form method="post" action="?page=build&gid='.$gid.'&tab=3&do=newoffer" autocomplete="off">
		<table class="f10"><tbody><tr>
		<td>Biete</td><td><input class="fm" name="r1" value="'.saveGet('r1','').'" size="4" maxlength="5" tabindex="1"></td>
		<td><select name="typ1" size="" class="fm" tabindex="2">';
	foreach($ress as $nr => $name) {
		$t1=saveGet('t1','');
		$x='';
		if ($nr==$t1 or ($t1=='' and $nr==1)) $x='selected="selected"';
		echo'<option value="'.$nr.'" '.$x.'>'.$name.'</option>';
	}

	$hasmax='checked';
	if (!isset($_GET['max'])) $_GET['max']=2;
	if ($_GET['max']==9999) {$_GET['max']=2; $hasmax='';}

	echo'</select></td>
		<td>&nbsp;</td><td><input name="d1" value="1" type="checkbox" '.$hasmax.' tabindex="5"> Max. Transportdauer:
		<input class="fm fm25" name="d2" value="'.$_GET['max'].'" maxlength="2" tabindex="6"> Stunden</td></tr>
		
		<tr><td>Suche</td><td><input class="fm" name="r2" value="'.saveGet('r2','').'" size="4" maxlength="5" tabindex="3"></td>
		<td><select name="typ2" size="" class="f8" tabindex="4">';
	foreach($ress as $nr => $name) {
		$x='';
		$t2=saveGet('t2','');
		if ($nr==$t2 or ($t2=='' and $nr==2)) $x='selected="selected"';
		echo'<option value="'.$nr.'" '.$x.'>'.$name.'</option>';
	}
	echo'</select></td>
		<td>&nbsp;</td>';
	$ally=saveGet('ally','');
	if ($ally==1) $ally='checked';

	if ($login_user->get('ally')>0)
		echo'<td><input name="ally" value="1" type="checkbox" tabindex="7" '.$ally.'> Nur eigene Allianz</td>';
	echo'</tr><tr><td colspan=6>&nbsp;</td></tr>
		<tr><td colspan=4>Händler: '.$anz_ver_handler.'/'.$handler.'</td>
		<td colspan=2>&nbsp;Tragfähigkeit: '.$tragen.'</td></tr></tbody></table></p>';
	if (isset($msg)) echo'<p class="e">'.$msg.'</p>';
	echo'<p>';
	Outputer::button('s1','ok');
	echo'</p></form>';

	$angebote=Angebot::getByD($login_dorf);
	if (!empty($angebote)) {
		echo'<p></p><table class="f10" bgcolor="#c0c0c0" cellpadding="1" cellspacing="1" width="100%">
			<tbody><tr align="center" bgcolor="#f5f5f5">
			<td colspan="6"><b>Eigene Angebote</b></td></tr><tr align="center" bgcolor="#f5f5f5">
			<td>&nbsp;</td><td>Biete</td><td>Suche</td><td width="20%">Händler</td><td>Öffentlich</td>
			<td>Max. Transportdauer</td></tr>';
		foreach ($angebote as $angebot) {
			$angebot->drawForYou();
		}
		echo'</tbody></table>';
	}
}
if ($tab==4) {
	$ress=explode(':',$_GET['ress']);
	$user=Spieler::getById($_GET['user'])->get('name');
  $rohstoff_name=array(1=>'Holz',2=>'Lehm',3=>'Eisen',4=>'Getreide');
  echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody>
  <tr class="rbg"><td colspan="3">Marktplatz</td></tr>
  <tr><td colspan="3">Das Angebot von '.$user.' wurde angenommen.</td></tr>
  <tr><td><img src="img/un/r/'.$ress[0].'.gif" alt="'.$rohstoff_name[$ress[0]].'" height="12"
   width="18"></td><td>'.$ress[1].'</td><td>sind zu dir unterwegs</td></tr>
  <tr><td><img src="img/un/r/'.$ress[2].'.gif" alt="'.$rohstoff_name[$ress[2]].'" height="12"
   width="18"></td><td>'.$ress[3].'</td><td>haben deine Händler soeben verschickt</td></tr>
   </tbody></table><p></p>
   <a href="?page=build&gid='.$gid.'&tab=2&b='.$_GET['b'].'&n='.$_GET['n'].'&f='.$_GET['f'].'">Zurück zu den Angeboten</a>';
}
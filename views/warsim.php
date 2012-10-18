<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

echo'<table width="100%"><tr><td><h1>Kampfsimulator</h1></td>
	<td class="right" valign="top"><a href="?page=build&gid=39">(Zurück zum Versammlungsplatz)</a></td></tr></table>
	<form method="get" action="?">
	<input type="hidden" name="page" value="warsim" />
	<br>';

if ($_GET['s']=='show') {
	//Mauern
	$wall=array(31=>$_GET['w1'],32=>$_GET['w2'],33=>$_GET['w3']);

	//Truppen
	$deffTruppen=array();
	for ($i=1;$i<=4;$i++) {
		unset($truppe);
    $d_v[$i]=$_GET['d_v'.$i];
    if ($d_v[$i]==1) {
    	$truppe=array('volk'=>$i);
    	for ($tid=$i*10-9;$tid<=$i*10;$tid++)
    		$truppe[$tid]=$_GET['d_'.$tid];
 	    $deffTruppen[]=$truppe;
    }
  }

  //Deffheld
  if ($_GET['hero_v']>0) {
  	$deffTruppen[0]['hero']=1;
  	$deffTruppen[0]['heroboni']=$_GET['hero_v'];
  }
	
	//Deffdorf erstellen
  $dorf=new DeffDorf($_GET['pala'],$wall,$_GET['fa'],$deffTruppen);
  
  //Angreifer zusammenstellen
  $offTruppen=array();
  $volk=$_GET['a_v'];
  $offTruppen['volk']=$volk;
  for ($tid=$volk*10-9;$tid<=$volk*10;$tid++) {
  	$offTruppen[$tid]=$_GET['a_'.$tid];
  }
  
  //OffHeld
  if ($_GET['hero_a']>0) {
  	$offTruppen['hero']=1;
  	$offTruppen['heroboni']=$_GET['hero_a'];
  }
  
  //Angriff simulieren
  $deffboni=$dorf->getDeffBoni();
  $offTruppen2=$dorf->attack($offTruppen,$_GET['ktyp']);
  
  //Angreiffer ausgeben
	print_volk($offTruppen['volk'],$offTruppen,$offTruppen2,1,'Angreifer');
	
	//Verbleibende DeffTruppen abfragen und ausgeben
	$deffTruppen2=$dorf->getDeffTruppen();
	$t='Verteidiger';
	foreach($deffTruppen as $nr => $truppe) {
		print_volk($truppe['volk'],$truppe,$deffTruppen2[$nr],2,$t);
		$t='Unterst.';
	}
	
}

/* Eingegebene Werte + Standardwerte */
$volk=$login_user->get('volk');
$c='checked="checked"';


//Angreifer eingeben
$a_v=$_GET['a_v'];	//Angreiffer volk
if (!isset($a_v)) $a_v=$volk;
echo'<table class="tbg" cellpadding="2" cellspacing="1" width="90%">
  <tr class="rbg"><td colspan="10">Angreifer </td></tr>
  <tr><td colspan="10"><input class="radio" name="a_v" value="1"
    '.($a_v==1?$c:'').' type="radio"> Römer
  <input class="radio" name="a_v" value="2"
    '.($a_v==2?$c:'').' type="radio"> Germanen
  <input class="radio" name="a_v" value="3"
    '.($a_v==3?$c:'').' type="radio"> Gallier</td></tr>
  <tr>';
for ($tid=$a_v*10-9;$tid<=$a_v*10;$tid++) {
	$einheit=TruppenTyp::GetById($tid);
  echo'<td><img src="img/un/u/'.$tid.'.gif" title="'.$einheit->get('name').'"></td>';
}
echo'</tr><tr>';
for ($tid=$a_v*10-9;$tid<=$a_v*10;$tid++) {
  echo'<td style="padding:0px;">
  	<input class="fm" name="a_'.$tid.'" value="'.$_GET['a_'.$tid].'" maxlength="6" type="text" style="width:40px;"></td>';
}
echo'</tr></table>';


//Verteidiger eingeben
$set=FALSE;
for ($i=1;$i<=4;$i++) {
	$d_v[$i]=$_GET['d_v'.$i];
	if ($d_v[$i]==1) $set=TRUE;
}
if (!$set)
	$d_v[$volk]=1;
echo'<table class="tbg" cellpadding="2" cellspacing="1" width="90%">
  <tr class="rbg"><td colspan="10">Verteidiger </td></tr>
  <tr><td colspan="10"><input class="check" name="d_v1" value="1"
			'.($d_v[1]?$c:'').' type="checkbox"> Römer
		<input class="check" name="d_v2" value="1"
			'.($d_v[2]?$c:'').' type="checkbox"> Germanen
		<input class="check" name="d_v3" value="1"
			'.($d_v[3]?$c:'').' type="checkbox"> Gallier
		<input class="check" name="d_v4" value="1"
			'.($d_v[4]?$c:'').' type="checkbox"> Natur</td></tr>';
for ($volk=1;$volk<=4;$volk++) {
	if ($d_v[$volk]==1) {
		echo'<tr>';
		for ($tid=$volk*10-9;$tid<=$volk*10;$tid++) {
			$einheit=TruppenTyp::GetById($tid);
		  echo'<td><img src="img/un/u/'.$tid.'.gif" title="'.$einheit->get('name').'"></td>';
		}
		echo'</tr><tr>';
		for ($tid=$volk*10-9;$tid<=$volk*10;$tid++) {
      echo'<td style="padding:0px;"><input class="fm" name="d_'.$tid.'" value="'.$_GET['d_'.$tid].'"
      	maxlength="6" type="text" style="width:40px;"></td>';
    }
    echo'</tr>';
	}
}
echo'</table>';


//Sonstiges
$row2='';
echo'<table class="tbg" cellpadding="2" cellspacing="1" width="90%">
	<tr class="rbg"><td colspan="10">Sonstiges</td></tr><tr>';
//Helden
echo'<td><img src="img/un/a/hero_a.gif" title="Angriffsbonus in % - Held Angreifer"></td>
	<td><img src="img/un/a/hero_v.gif" title="Verteidigungsbonus in % - Held Verteidiger"></td>';
$row2.='<td style="padding:0px;">
	<input class="fm" type="text" name="hero_a" value="'.$_GET['hero_a'].'" style="width:40px;"></td>
	<td style="padding:0px;">
	<input class="fm" type="text" name="hero_v" value="'.$_GET['hero_v'].'" style="width:40px;"></td>';
//Verteidigungsmauer
for ($i=1;$i<=3;$i++) {
	$geb=GebeudeTyp::getById(30+$i);
	echo'<td><img src="img/un/a/wall'.$i.'.gif" title="Stufe '.$geb->get('name').'"></td>';
	$row2.='<td style="padding:0px;">
		<input class="fm" type="text" name="w'.$i.'" value="'.$_GET['w'.$i].'" style="width:40px;"></td>';
}
//Palast & Fallen
echo'<td><img src="img/un/a/pala.gif" title="Stufe Palast"></td>
	<td><img src="img/un/u/99.gif" title="Anzahl Fallen"></td>';
$row2.='<td style="padding:0px;">
	<input class="fm" type="text" name="pala" value="'.$_GET['pala'].'" style="width:40px;"></td>
	<td style="padding:0px;">
	<input class="fm" type="text" name="fa" value="'.$_GET['fa'].'" style="width:40px;"></td>';

//Angriff normal und Raubzug
$ktyp=$_GET['ktyp'];
if(!isset($ktyp)) $ktyp=4;
echo'<td colspan="3" style="width:142px;" rowspan="2" class="left"><input class="radio" name="ktyp" value="3"
			'.($ktyp==3?$c:'').' type="radio"> Angriff Normal<br>
		<input class="radio" name="ktyp" value="4"
			'.($ktyp==4?$c:'').'type="radio"> Raubzug</td>
	</tr><tr>'.$row2.'</tr>
	</table><p></p>';



Outputer::button('s','ok',array('value'=>'show'));
echo'</form>';





/* Anhang */
/* Prüfen: */
function print_volk($volk,$army_vor,$army_nach,$typ,$title,$info=NULL) {
	echo'<table class="tbg" cellpadding="1" cellspacing="1">
		<tbody><tr class="cbg1">
		<td width="21%">';
	if ($typ==1) echo'<span class="c2 b">&nbsp;'.$title.'</span>';
	if ($typ==2) echo'<font color="#71d000"><b>&nbsp;'.$title.'</b></font>';
	echo'</td>';
	for ($i=$volk*10-9;$i<=$volk*10;$i++) {
		$einheit=TruppenTyp::getById($i);
		echo'<td width="40"><img class="unit" src="img/un/u/'.$i.'.gif" title="'.$einheit->get('name').'"></td>';
	}
	echo'</tr><tr><td class="left">&nbsp;Einheiten</td>';
	for ($i=$volk*10-9;$i<=$volk*10;$i++) {
		if ($army_vor[$i]==0) echo'<td class="c">0</td>';
		else			echo'<td>'.$army_vor[$i].'</td>';
	}
	echo'</tr><tr><td class="left">&nbsp;Verluste</td>';
	for ($i=$volk*10-9;$i<=$volk*10;$i++) {
		if ($army_vor[$i]-$army_nach[$i]==0) echo'<td class="c">0</td>';
		else			echo'<td>-'.($army_vor[$i]-$army_nach[$i]).'</td>';
	}
	echo'</tr><tr><td class="left">&nbsp;Übrig</td>';
	for ($i=$volk*10-9;$i<=$volk*10;$i++) {
		if ($army_nach[$i]==0) echo'<td class="c">0</td>';
		else			echo'<td>'.$army_nach[$i].'</td>';
	}
	if (is_array($info) or is_string($info)) {
		if (is_string($info)) $info=array($info);
		foreach($info as $text) {
			echo'</tr><tr><td class="left">&nbsp;Info:</td><td class="left" colspan="10">&nbsp;'.$text.'</td>';
		}
	}
	echo'</tr></tbody></table><p></p>';
}
/* Ok ab hier */
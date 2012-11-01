<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title></title><link rel=stylesheet type="text/css" href="unx.css">
	<meta http-equiv="imagetoolbar" content="no">
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>
<?php

$template=false;
$volker=explode(':',Diverses::get('volker'));
$typ=saveGet('typ',0);


if ($typ==0) { // Übersicht
?>
<h1><img src="img/un/u/geb.gif" class="unit"> Übersicht</h1>

<p>Diese Ingame Hilfe ermöglicht es dir wichtige Informationen jederzeit schnell nachzuschlagen.
<img src="img/un/a/troops.gif" width="140" height="89" border="0" align="right" style="position:absolute; z-index:61; right:0px; top:80px; margin-right:10px;"><img src="img/un/a/buildings.gif" width="140" height="89" border="0" align="right" style="position:absolute; z-index:61; right:0px; top:170px; margin-right:10px;"><ul>
<li>Die Einheiten</li>

<ul>

<?php
foreach($volker as $nr=>$name)
	echo'<li><a href="?page=manual&typ=1&volk='.($nr+1).'">'.$name.
		'</a></li>';
?>
</ul>

<br>
<li>Die Gebäude</li>

<ul>
<li><a href="?page=manual&typ=2&gebs=1">Rohstoffe</a></li>
<li><a href="?page=manual&typ=2&gebs=2">Militär</a></li>

<li><a href="?page=manual&typ=2&gebs=3">Infrastruktur</a></li>
</ul>


<br>
<li><a href="?page=anleitung" target="_blank">Spielanleitung
<img src="img/un/a/external.gif" width="10" height="10" border="0"></a><br>
Die Ingame Hilfe stellt nur Kurzinformationen zur Verfügung. Mehr Informationen zum Spiel gibt es<br>
in der Spielanleitung.</li><br>
<br>
<li><a href="http://help.travian.de/" target="_blank">Travian FAQ
<img src="img/un/a/external.gif" width="10" height="10" border="0"></a><br>
Sehr ausführliche Travian Dokumentation</li></ul></p>
<?php
}
if ($typ==1) {
	$volk=$_GET['volk'];
	if (isset($volk)) {	//Einheiten eines Volkes im Überblick

		echo'<h1><img src="img/un/u/geb.gif" class="unit"> Einheiten ('.
			$volker[$volk-1].')</h1>
		<ul>';
		if ($volk==4) {
			echo'<li>Über dieses sehr aggressive und feindliche Volk
				ist leider fast nichts bekannt.<br>Die wenigen Truppen
				die Angriffe der Nataren überstanden haben, sind kurz
				darauf gestorben.</li>';
		}
		else {
			for ($i=1+($volk-1)*10;$i<=$volk*10;$i++)
				echo'<li><a href="?page=manual&typ=1&id='.$i.'">'.
					TruppenTyp::getById($i)->get('name').'</a></li>';
		}
		echo'</ul>';
		$vor_volk=($volk+2)%4 +1;
		$nac_volk=($volk)%4 +1;
		$para1='typ=1&volk='.$vor_volk;
		$para2='typ=1&volk='.$nac_volk;
	}
	else	//Eine Einheit anzeigen
	{
		$id=$_GET['id'];
		$e=TruppenTyp::getById($id);
		$name=$e->get('name');
		$werte=$e->werte();
		$kosten=$e->baukosten();
		echo'<div style="background-image:url(img/un/u2/u'.$id.'.gif);
			background-repeat:no-repeat;background-position:260px 95px;">
			<h1><img class="unit" src="img/un/u/'.$id.'.gif" border="0"
			alt="'.$name.'"> '.$name.'<span class="f10 c t">('.
			$volker[$e->get('volk')-1].')</span></h1>
			<p><table cellspacing="1" cellpadding="2" class="tbg">
			<tr class="rbg">
			<td><img src="img/un/a/att_all.gif" alt="Angriffswert"
			title="Angriffswert"></td>
			<td><img src="img/un/a/def_i.gif" alt="Verteidigungswert gegen
			Infantrie" title="Verteidigungswert gegen Infantrie"></td>
			<td><img src="img/un/a/def_c.gif" alt="Verteidigungswert gegen
			Kavallerie" title="Verteidigungswert gegen Kavallerie"></td>
			<td><img class="res" src="img/un/r/1.gif" alt="Holz"></td>
			<td><img class="res" src="img/un/r/2.gif" alt="Lehm"></td>
			<td><img class="res" src="img/un/r/3.gif" alt="Eisen"></td>
			<td><img class="res" src="img/un/r/4.gif" alt="Getreide"></td>
			</tr>
			<tr><td>'.$werte[0].'</td><td>'.$werte[1].'</td>
			<td>'.$werte[2].'</td>
			<td>'.$kosten[0].'</td><td>'.$kosten[1].'</td>
			<td>'.$kosten[2].'</td><td>'.$kosten[3].'</td></tr></table></p>
			<p><table cellspacing="1" cellpadding="2" class="f10">
			<tr><td>Geschwindigkeit:</td><td><b>'.$e->get('speed').'</b>
			Felder/Stunde</td></tr><tr>
			<td>Tragfähigkeit:</td><td><b>'.$e->get('tragen').'</b>
			Rohstoffe</td></tr><tr>
			<td>Versorgung:</td><td><img class="res" src="img/un/r/5.gif"
			alt="supply"> '.$e->get('versorgung').'</td>
			</tr><tr><td>Ausbildungszeit:</td><td>
			<img class="clock" src="img/un/a/clock.gif" width="18"
			height="12" border="0">
			'.zeit_dauer($e->bauzeit($login_dorf)).'</td></tr>
			</table></p><p>'.t($e->get('besch')).
			'</p><div class="b">Voraussetzungen:</div>';
		if ($e->get('needs')==-1)
			echo'<a href="?page=manual&typ=2&gid=26"
				>Palast</a> Stufe 10 oder
				<a href="?page=manual&typ=2&gid=25">Residenz</a> Stufe 10';
			$needs=$e->needs();
			unset($output);
			foreach($needs as $gebeude => $stufe) {
					$output.='<a href="?page=manual&typ=2&gid='.$gebeude.'">'.
						GebeudeTyp::getById($gebeude)->get('name').'</a> Stufe '.
						$stufe.', ';
			}
			echo substr($output,0,-2);
			$nid=$id+1;if ($nid>30) $nid=1;
			$vid=$id-1;if ($vid<1) $vid=30;
			$para1='typ=1&id='.$vid;
			$para2='typ=1&id='.$nid;
	}
}
if ($typ==2) {	//Gebäude im Überblick
	
	$gebs=saveGet('gebs',null);
	$gid=saveGet('gid',saveGet('id',null));
	
	if (isset($gebs) AND !isset($gid)) {
		$gebeude_typ=array(1=>'Rohstoffe',2=>'Militär',3=>'Infrastruktur');
		echo'	<h1><img src="img/un/u/geb.gif" class="unit"> Gebäude ('.
			$gebeude_typ[$gebs].')</h1><ul>';

		for ($i=1;$i<=GebeudeTyp::$anz;$i++) {
			if (GebeudeTyp::getById($i)->get('typ')==$gebs)
				echo'<li><a href="?page=manual&typ=2&gid='.$i.'">'.
					GebeudeTyp::getById($i)->get('name').'</a></li>';
		}
		echo'</ul>';
		$vor_gebs=($gebs+1)%3 +1;
		$nac_gebs=($gebs)%3 +1;
		$para1='typ=2&gebs='.$vor_gebs;
		$para2='typ=2&gebs='.$nac_gebs;
	}
	if (!isset($gebs) AND isset($gid)) {	//Ein Gebäude anzeigen
		$g=GebeudeTyp::getById($gid);
		$kosten=$g->baukosten(1);
		echo'<h1><img class="unit" src="img/un/u/geb.gif"> '.
			$g->get('name').'</h1>'.
			'<p>'.t(insert_div($g->get('besch')).$g->get('volksvorteile')).'</p><p><b>Kosten</b> und <b>Bauzeit</b>
			bei Stufe 1:<br>
			<img class="res" src="img/un/r/1.gif" alt="Holz"
				title="Holz" style="padding-top:4px">'.$kosten[0].' |
			<img class="res" src="img/un/r/2.gif" alt="Lehm"
				title="Lehm">'.$kosten[1].' |
			<img class="res" src="img/un/r/3.gif" alt="Eisen"
				title="Eisen">'.$kosten[2].' |
			<img class="res" src="img/un/r/4.gif" alt="Getreide"
				title="Getreide">'.$kosten[3].' |
			<img src="img/un/r/5.gif" width="18" height="12"
				alt="Getreideverbrauch" title="Getreideverbrauch">'.
				$g->get('arbeiter').' |
			<img src="img/un/a/clock.gif" class="clock"> '.
				zeit_dauer($g->bauzeit(1,$login_dorf)).'<br><br>
			<b>Voraussetzungen:</b> <br>';
		
		if ($g->get('needs')==0)
			echo'keine';
		elseif ($g->get('needs')==-1)
			echo'nicht baubar';
		else {
			$output='';
			foreach($g->needs() as $gebeude => $stufe) {
				if ($gebeude==-1 or $gebeude=='volk')
					$output.='<a href="">Nur '.$volker[$stufe-1].'</a>';
				elseif ($gebeude=='dorf')
					$output.='<a href="">Nur in einem '.$stufe.' Dorf baubar</a>';
				elseif ($gebeude>0)
					$output.='<a href="?page=manual&typ=2&gid='.$gebeude.'">'.
						GebeudeTyp::getById($gebeude)->get('name').'</a> Stufe '.
						$stufe;
				$output.=', ';
			}
			echo substr($output,0,-2);
		}
		echo'<br><br><b>Wiederbaubar:</b><br>';
		$nid=$gid+1;
		if ($nid>GebeudeTyp::$anz) $nid=1;
		$vid=$gid-1;
		if ($vid<1) $vid=GebeudeTyp::$anz;
		$para1='typ=2&gid='.$vid;
		$para2='typ=2&gid='.$nid;
		if ($g->get('rebuild')==0) echo'Nein';
		else	echo'Ja, ab Stufe '.$g->get('rebuild');
		echo'</p>';
	}
}
?>

<map name="nav">
<?php
if (isset($para1))
	echo'<area href="?page=manual&'.$para1.'" title="zurück" coords="0,0,45,18" shape="rect">';
echo'<area href="?page=manual" title="Übersicht" coords="46,0,70,18" shape="rect">';
if (isset($para2))
	echo'<area href="?page=manual&'.$para2.'" title="weiter" coords="71,0,116,18" shape="rect">';
?>
</map>

<img usemap="#nav" src="img/un/a/navi.gif" border="0" class="m_navi">
</body>
</html>
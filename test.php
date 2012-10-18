<?php

//zur Datenbank verbinden
$link = mysql_pconnect('localhost','root','localsqlpw');
if (!$link)
    {die('Verbindung nicht möglich : ' . mysql_error());}
if(!mysql_select_db('yingyang_aku'))
    {die('Fehler Datenbank konnte nicht ausgewÃ¤hlt werden.');}
/*
$link = mysql_pconnect('yinyangm.mysql.db.internal','yinyangm','Pw1234cw');
if (!$link)
    {die('Verbindung nicht möglich : ' . mysql_error());}
if(!mysql_select_db('yinyangm_aku'))
    {die('Fehler Datenbank konnte nicht ausgewÃ¤hlt werden.');}
*/
?>
<style type="text/css">
.tr_head {
	background-color:#FADACA;
}
table tr td {
	padding:1px 2px 1px 3px;
}
a,span {
	color:#9B0810;
	font-family:Tahoma,Arial,Helvetica,Sans-Serif;
	font-size:13px;
}
a:hover {
	text-decoration:none;
}
</style>

<?php



if (!isset($_GET['id'])) {

$start=(int)$_GET['start'];
$aktuelle_seite=floor($start/20);

$sql="SELECT COUNT(*) as anz FROM akupunkte;";
$result=mysql_query($sql);
$data=mysql_fetch_assoc($result);
$anz=$data['anz'];
$seiten=ceil($anz/20);

$sql="SELECT ID,punkt,beschreibung FROM akupunkte ORDER BY punkt ASC LIMIT $start,20;";

$result=mysql_query($sql);

echo'<table border="1" style="border:1px #DDD solid; border-collapse:collapse;">
	<tr class="tr_head"><td>Punkt:</td><td>Beschreibung:</td></tr>';
while($data=mysql_fetch_assoc($result)) {

	echo'<tr><td valign="top">'.$data['punkt'].'</td>
		<td><a href="test.php?id='.$data['ID'].'">'.$data['beschreibung'].'</a></td></tr>';


}
echo'</table><br>';

if ($start>0) echo'<a href="?start='.($start-20).'">Zurück</a> ';

//Erste Seiten die angezeigt werden
$anzeigende_seiten=array(0,1,2);
//Seiten vor und nach aktueller die angezeigt werden
for ($i=-1;$i<=1;$i++) {
	array_push($anzeigende_seiten,$i+$aktuelle_seite);
}
//Letzte Seiten die angezeigt werden
array_push($anzeigende_seiten,$seiten-3,$seiten-2,$seiten-1);

//Anzeigen
echo'( ';
$akt=-1;
//Alle Seiten im Array
foreach ($anzeigende_seiten as $seite) {
	//Falls Seite übersprungen wurde:
	if ($akt<$seite-1) echo'..., ';
	//Seiten ausgeben, falls vorwärts gegangen wird
	if ($akt<$seite and $akt<$seiten-1) {
		
		if ($aktuelle_seite==$seite)
			echo '<span style="font-weight:bold; color:#500000;">'.$seite.'</span>';
		else
			echo'<a href="?start='.($seite*20).'"'.$c.'>'.$seite.'</a>';
		if ($seite<$seiten-1) echo', ';
		$akt=$seite;
	}
}
echo' ) ';
if ($start+20<$anz) echo'<a href="?start='.($start+20).'">Weiter</a> ';


}
else {
	$id=$_GET['id'];

	$sql="SELECT * FROM akupunkte WHERE ID=$id;";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)==0) echo'Nicht gefunden.';
	else {
		$data=mysql_fetch_assoc($result);
		echo'<table border="1" style="border:1px #DDD solid; border-collapse:collapse;">';
		unset($data['ID']);
		foreach ($data as $feld => $wert) {
			echo '<tr><td>'.$feld.'</td><td>'.$wert.'</td></tr>';
		}
		echo'</table><br><a href="?">Zurück</a>';
	}
}

?>
<?php
if (!$execute) die('');
needed_login();
$stview=1;


Updater::Dorf($login_dorf);


$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');

$gebeude2=$login_dorf->gebeude2();
$gebeude2t=$login_dorf->gebeude2typ();

unset($na_st);
for ($i=19;$i<=40;$i++) {
	if ($gebeude2t[$i-19]>0)
		$beschriftung[$i]=
			GebeudeTyp::getById($gebeude2t[$i-19])->get('name').
				' Stufe '.$gebeude2[$i-19];
	else {
		if ($i<39) $beschriftung[$i]='Bauplatz';
		if ($i==39) $beschriftung[$i]='Versammlungsbauplatz';
		if ($i==40) $beschriftung[$i]='Aussen Bauplatz';
	}
}


//Dorfname
?>
<div class="dname">
<h1><?php echo $login_dorf->get('name'); ?></h1>
</div>



<?php
$show=$login_user->getKonfig('dorf2_stufen_anzeige');
$klasse= ($show?'on':'off');

echo'<img id="d2show_lvl_button" class="'.$klasse.'" onclick="d2show_lvl_toggle();" />

	<script type="text/javascript">
	var d2show_lvl= '.$show.';
	</script>';
?>



<?php
//Gebäude anzeigen
for ($i=0;$i<20;$i++) {
	if ($gebeude2t[$i]>0) {
		if ($gebeude2[$i]>0)
			echo'<img class="d'.($i+1).'" src="img/un/g/g'.
				$gebeude2t[$i].'.gif">';
		else
			echo'<img class="d'.($i+1).'" src="img/un/g/g'.
				$gebeude2t[$i].'b.gif">';
	}
	else
		echo'<img class="d'.($i+1).'" src="img/un/g/iso.gif"
			height="100" width="75">';
}

echo'<div class="d2show_lvl" id="d2_lvl" '.(!$show?'style="visibility:hidden;"':'').' >';
for ($i=0;$i<20;$i++) {
	if ($gebeude2[$i]>0) echo'<div class="d'.($i+1).'">'.$gebeude2[$i].'</div>';
}
//Versammlungsplatz
if ($gebeude2[20]>0) echo'<div class="dx1">'.$gebeude2[20].'</div>';
//Mauer
if ($gebeude2[21]>0) echo'<div class="dx2">'.$gebeude2[21].'</div>';
echo'</div>';






//Versammlungsplatz falls nicht gebraucht einfach weglassen
if ($gebeude2t[20]==16 and $gebeude2[20]>0)
	echo'<img class="dx1" src="img/un/g/g16.gif">';

//Stadtmauer: d2_11, Erdwall: d2_12, Palisade: d2_1, keine Mauer: d2_0
if ($gebeude2t[21]==0 or $gebeude2[21]==0)
	echo'<div class="d2_x d2_0">';
elseif ($gebeude2t[21]==31)
	echo'<div class="d2_x d2_1">';
elseif ($gebeude2t[21]==32)
	echo'<div class="d2_x d2_2">';
elseif ($gebeude2t[21]==33)
	echo'<div class="d2_x d2_3">';
?>


<img usemap="#map2" class="dmap" src="img/un/a/x.gif" border="0" height="450" width="540">




</div>

<?php
Outputer::dorf2Karte($beschriftung);
?>


<img class="dmap" usemap="#map1" src="img/un/a/x.gif" border="0" height="339" width="422">

<div id="lplz2"> </div>
<br><br>
<?php
Outputer::dorfAuftrage(2);
?>
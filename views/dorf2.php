<?php
if (!$execute) die('');
needed_login();
$stview=1;


Updater::Dorf($login_dorf);


$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');

$gebeude2=$login_dorf->gebeude2();
$gebeude2t=$login_dorf->gebeude2typ();

$ww = WW::isWWDorf($login_dorf);
$wwFelderAuslassen = WW::getExcludeFields();

//Beschriftung setzen
unset($na_st);
for ($i=19;$i<=40;$i++) {
	if ($gebeude2t[$i-19]>0)
		$beschriftung[$i]=
			GebeudeTyp::getById($gebeude2t[$i-19])->get('name').
				' Stufe '.$gebeude2[$i-19];
	else {
		if ($i<39) $beschriftung[$i]='Bauplatz';
		elseif ($i==39) $beschriftung[$i]='Versammlungsbauplatz';
		elseif ($i==40) $beschriftung[$i]='Aussen Bauplatz';
	}
}


//Dorfname
?>
<div class="dname">
<h1><?php echo $login_dorf->get('name'); ?></h1>
</div>



<?php
//Knopf um die Stufen anzuzeigen oder auszublenden
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
	if(! ($ww and in_array($i+19,$wwFelderAuslassen))) {
		if ($gebeude2t[$i]>0) {
			if ($gebeude2[$i]>0)
				echo'<img class="d'.($i+1).'" src="img/un/g/g'.
					$gebeude2t[$i].'.gif" />';
			else
				echo'<img class="d'.($i+1).'" src="img/un/g/g'.
					$gebeude2t[$i].'b.gif" />';
		}
		else
			echo'<img class="d'.($i+1).'" src="img/un/g/iso.gif"
				height="100" width="75" />';
	}
}
//Versammlungsplatz falls nicht gebraucht einfach weglassen
if ($gebeude2t[20]==16 and $gebeude2[20]>0)
	echo'<img class="dx1" src="img/un/g/g16.gif" />';
if ($ww and $gebeude2[WW::getField()-19]>0) {
	$id = WW::getWWGebId();
	$wwGeb = GebeudeTyp::getById($id);
	$progress = $gebeude2[WW::getField()-19] / $wwGeb->get('stufen');
	$imgNr = floor( $progress * (WW::getImageCount()-1) );
	echo'<img class="dww" src="img/un/g/g'.$id.'_'.$imgNr.'.gif" />';
}



//Stufenanzeige anzeigen/ ausblenden
echo'<div class="d2show_lvl" id="d2_lvl" '.(!$show?'style="visibility:hidden;"':'').' >';

//Stufe der Gebäude anzeigen
for ($i=0;$i<20;$i++) {
	if ($gebeude2[$i]>0) echo'<div class="d'.($i+1).'">'.$gebeude2[$i].'</div>';
}
//Versammlungsplatz Stufe anzeigen
if ($gebeude2[20]>0) echo'<div class="dx1">'.$gebeude2[20].'</div>';
//Mauer Stufe anzeigen
if ($gebeude2[21]>0) echo'<div class="dx2">'.$gebeude2[21].'</div>';
echo'</div>';


//Stadtmauer: d2_11, Erdwall: d2_12, Palisade: d2_1, keine Mauer: d2_0
$anzeigeBild = 0;
if ($gebeude2t[21]==31 and $gebeude2[21]>0)
	$anzeigeBild = 1;
if ($gebeude2t[21]==32 and $gebeude2[21]>0)
	$anzeigeBild = 2;
if ($gebeude2t[21]==33 and $gebeude2[21]>0)
	$anzeigeBild = 3;
//Weltwunder spezial dorf (Anzeigebild hintergrund
if ($ww)
	$anzeigeBild.='ww';

echo'<div class="d2_x d2_'.$anzeigeBild.'"></div>';


//Weltwunderbauplatz anzeigen
echo'<div class="d2_x d2_ww_iso"></div>';


//Gibt anwählbare Elemente auf der Karte aus (Gebäudeanklick punkt)
Outputer::dorf2Karte($beschriftung,$ww,$wwFelderAuslassen);

?>
<div id="lplz2"> </div>
<br><br>
<?php

//Gebäude Aufträge werden hier angezeigt
Outputer::dorfAuftrage(2);
?>
<?php


$stview=1;

echo'<table>';

$starke=array(25=>13000,50=>28000);

for ($i=1;$i<=12;$i++){
	$oase=Oase::getById($i);
	echo'<tr><td>
		<img src="img/un/m/w'.$i.'.jpg" width="100">
		</td><td width="100">';
	
	$tiere=$oase->tierGrenzeIds();
	$werte=array(0,0,0);
	
	$soll_wert = $starke[array_sum($oase->bonus())];
	
	foreach($tiere as $id => $max) {
		if ($max>0) {
			echo'<img src="img/un/u/'.$id.'.gif" />
				'.$max.'<br/>';
			$typ=TruppenTyp::getById($id);
			$w=$typ->werte();
			foreach($w as $nr=>$value)
				$werte[$nr]+=$value*$max;
		}
	}
	$faktor=$soll_wert/($werte[1]+$werte[2]);
	echo'</td><td><img src="img/un/h/def_i.gif" /> '.$werte[1].'<br><img src="img/un/h/def_c.gif" /> '.$werte[2];
	echo'<br>'.$faktor;
	echo'</td><td>';
	foreach($tiere as $id => $max) {
		if ($max>0) {
			echo'<img src="img/un/u/'.$id.'.gif" />
				'.round($max*$faktor).'<br/>';
			$tiere[$id]=round($max*$faktor);
		}
	}
	
	echo implode(':',$tiere);
	$oase->set('tier_grenze',implode(':',$tiere));
	
	echo'</td></tr>';
	
}
echo'</table>';


?>
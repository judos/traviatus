<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::Dorf($login_dorf);

$dorfx=$login_dorf->get('x');
$dorfy=$login_dorf->get('y');

$dorfer=$login_user->dorfer();

?>
<p class="txt_menue">
<?php
$seiten=array(''=>'Übersicht','2'=>'Rohstoffe','3'=>'Lager','4'=>'KP','5'=>'Truppen');
foreach($seiten as $s=>$name) {
	$c='';
	if ($_GET['s']==$s) $c=' class="selected"';
	echo'<a href="?page=dorf3&s='.$s.'"'.$c.'>'.$name.'</a> ';
	if ($name!=end($seiten)) echo'| ';

}

echo'</p>';




if ($_GET['s']==5) {	//Truppen
  ?>
  <table class="tbg" cellpadding="2" cellspacing="1">
  <tbody><tr class="rbg">
  <td colspan="12">Eigene Truppen</td></tr>
  <tr class="unit"><td width="150">Dorfname</td>
  <?php
  $volk=$login_user->get('volk');
  for ($i=1;$i<=10;$i++) {
  	$id=$i+($volk-1)*10;
  	$typName=TruppenTyp::getById($id)->get('name');
    echo'<td><img src="img/un/u/'.$id.'.gif" title="'.$typName.'"></td>';
  }
  echo'<td><img src="img/un/u/hero.gif" title="Held"></td></tr>';

  unset($all);
  foreach($dorfer as $dorf) {
    $x=$dorf->get('x');
    $y=$dorf->get('y');
    $cl='';$clr='';$cll='';
    if ($x==$dorfx and $y==$dorfy) { $cl='ou'; $clr='ou re'; $cll='ou li'; }

    $truppe=$dorf->eigeneTruppe();
    $soldaten=$truppe->soldatenNr();

    //Dorf
    echo'<tr><td class="s7 '.$cll.'"><a href="?page=dorf1&dorfx='.$x.'&dorfy='.$y.'">'.$dorf->get('name').'</a></td>';

    //Truppen
    for ($j=0;$j<=10;$j++) {
      $all[$j]+=$soldaten[$j];
      if ($j==10) $cl=$clr;
      if ($soldaten[$j]==0) echo'<td class="c '.$cl.'">0</td>';
      else echo'<td class="'.$cl.'">'.$soldaten[$j].'</td>';
    }
    echo'</tr>';
  }
  echo'<tr><td colspan="12"></td></tr><tr><td class="s7 f10"><b>Summe</b></td>';
  for ($i=0;$i<=10;$i++) {
      if ($all[$i]==0) echo'<td class="c">0</td>';
      else echo'<td>'.$all[$i].'</td>';
  }
  echo'</tr></tbody></table>';
}


if ($_GET['s']==4) {	//Kulturpunkte KP
	?>
  <table class="tbg" cellpadding="2" cellspacing="1">
  <tbody><tr class="rbg"><td colspan="6">Kulturpunkte</td></tr>
  <tr><td width="150">Dorfname</td><td>KPs/Tag</td><td>Feste</td><td>Einheitenbau</td><td>Slots</td></tr>
  <?php
  $totkp=0;
  foreach($dorfer as $dorf) {

    $x=$dorf->get('x');
    $y=$dorf->get('y');
    $cl='';$clr='';$cll='';
    if ($x==$dorfx and $y==$dorfy) { $cl='ou'; $clr='ou re'; $cll='ou li'; }
    //Name und KP pro Tag
    echo'<tr><td class="s7 '.$cll.'"><a href="?page=dorf1&dorfx='.$x.'&dorfy='.$y.'">'.$dorf->get('name').'</a></td>
      <td class="'.$cl.'">'.round($dorf->get('einwohner')/2).'</td>';
    $kps+=round($dorf->get('einwohner')/2);

    //Feste
    $highest=$dorf->highest();
    $rathaus=$highest[24];

    if ($rathaus>0) {
      $auftrage=$dorf->auftrage(8);
      echo'<td class="'.$cl.'" title="Rathaus Stufe '.$rathaus.'">
        <a href="?page=build&dorfx='.$x.'&dorfy='.$y.'&highest=24">';
      if (empty($auftrage) and $rathaus<10) {
        echo'<b> • R'.$rathaus.' </b>';
      }
      elseif (empty($auftrage) and $rathaus>=10) {
      	echo'<b> &#9673; R'.$rathaus.' </b>';
      }
      else {
        $auftrag=$auftrage[0];
        $dauer=strtotime($auftrag->get('zeit'))-time();
        $fest=Fest::getById($auftrag->get('id'));
        $totkp+=$fest->get('kp');
        echo'<span id="timer'.$timerNr.'">'.zeit_dauer($dauer).'</span>';
        $timerNr++;
      }
      echo'</a></td>';
    }
    else {
      echo'<td class="'.$cl.'"></td>';
    }


    $auftrage=$dorf->auftrage(4);
    if (empty($auftrage)) {
    	echo'<td class="'.$cl.'"><span class="c">-</span></td>';
  	}
  	else {
  		$einheiten=array();
  		foreach($auftrage as $auftrag) {
  			$einheiten[$auftrag->get('id')]+=$auftrag->get('anzahl');
  		}
  		echo'<td class="'.$cl.'">';
  		foreach($einheiten as $tid => $anzahl) {
  			$typ=TruppenTyp::getById($tid);
  			echo'<a href="?page=build&dorfx='.$x.'&dorfy='.$y.'&highest=25">
  				<img src="img/un/u/'.$tid.'.gif" title="'.$anzahl.' '.$typ->named($anzahl).'"></a>';
  		}
  		echo'</td>';
    }


    //Slots
    $slots=$dorf->dorf_slots();
    $benutzt=sizeof($dorf->expansion());
    echo'</td><td class="'.$clr.'">'.$benutzt.'/'.$slots.'</td></tr>';
    $x1+=$benutzt;
    $x2+=$slots;
  }
  echo'<tr><td colspan="5"></td></tr><tr><td class="s7"><b>Summe</b></td><td>'.$kps.'</td>
  	<td>+'.$totkp.' KP</td><td></td><td>'.$x1.'/'.$x2.'</td></tr>
		</tbody></table>';
}



if ($_GET['s']==3) {	//Lager
	?>
  <table class="tbg" cellpadding="2" cellspacing="1">
  <tbody><tr class="rbg"><td colspan="7">Lager</td></tr>
  <tr><td width="150">Dorfname</td><td><img class="res" src="img/un/r/1.gif" title="Holz"></td>
  <td><img class="res" src="img/un/r/2.gif" title="Lehm"></td><td><img class="res" src="img/un/r/3.gif" title="Eisen"></td>
  <td><img class="res" src="img/un/a/clock.gif" title="Dauer"></td>
  <td><img class="res" src="img/un/r/4.gif" title="Getreide"></td>
  <td><img class="res" src="img/un/a/clock.gif" title="Dauer"></td></tr>
  <?php
  foreach($dorfer as $dorf) {

    $lager=$dorf->lager();
    $lagerGrosse=$dorf->lagerGrosse();
    $produktion=$dorf->produktion();
    $produktion[3]-=$dorf->versorgung();
    $x=$dorf->get('x');
    $y=$dorf->get('y');
    $cl='';$clr='';$cll='';
    if ($x==$dorfx and $y==$dorfy) { $cl='ou'; $clr='ou re'; $cll='ou li'; }
    echo'<tr><td class="s7 '.$cll.'"><a href="?page=dorf1&dorfx='.$x.'&dorfy='.$y.'">'.$dorf->get('name').'</a></td>';
    unset($dauer);
    for ($j=0;$j<=3;$j++) {
      if ($j<3) $lagerIndex=0;
      else $lagerIndex=1;

      $p=((int)$lager[$j])/$lagerGrosse[$lagerIndex];
      $frei=$lagerGrosse[$lagerIndex]-$lager[$j];
      if ($produktion[$j]>0)
	      $dauerAkt=$frei/$produktion[$j];
	    elseif ($produktion[$j]<0)
	    	$dauerAkt=abs($lager[$j]/$produktion[$j]);
	    else
	    	$dauerAkt=$dauer;
      if ($dauerAkt<$dauer or !isset($dauer)) $dauer=$dauerAkt;

      echo'<td class="r7 '.$cl.'" title="'.round($lager[$j]).' / '.$lagerGrosse[$lagerIndex].'">'.round($p*100).'%</td>';
      //Dauer
      if ($j>1) {
      	if ($j==3) $cl=$clr;
      	if ($dauer==0) {
      		if ($produktion[$j]>0) echo'<td class="m7 '.$cl.'" style="color:red;">Voll</td>';
      		if ($produktion[$j]==0) echo'<td class="m7 '.$cl.'" style="color:green;">-</td>';
      		if ($produktion[$j]<0) echo'<td class="m7 '.$cl.'" style="color:red;">Leer</td>';
      	}
      	else {
      		$c='';
      		if ($produktion[$j]<0) $c='style="color:red;"';
	      	echo'<td class="r7 '.$cl.'" id="timer'.$timerNr.'" '.$c.'>'.zeit_dauer($dauer*3600).'</td>';
  	    	$timerNr++;
  	    }
  	    unset($dauer);
      }

    }
  }
  echo'</tbody></table>';
}



if ($_GET['s']==2) {	//Rohstoffe
  ?>
  <table class="tbg" cellpadding="2" cellspacing="1">
  <tbody><tr class="rbg"><td colspan="6">Rohstoffe</td></tr>
  <tr><td width="150">Dorfname</td><td><img class="res" src="img/un/r/1.gif" title="Holz"></td>
  <td><img class="res" src="img/un/r/2.gif" title="Lehm"></td><td><img class="res" src="img/un/r/3.gif" title="Eisen"></td>
  <td><img class="res" src="img/un/r/4.gif" title="Getreide"></td><td>Händler</td></tr>
  <?php
  $dorfer=$login_user->dorfer();
  foreach($dorfer as $dorf) {

    $lager=$dorf->lager();
    $x=$dorf->get('x');
    $y=$dorf->get('y');
    $cl='';$clr='';$cll='';
    if ($x==$dorfx and $y==$dorfy) { $cl='ou'; $clr='ou re'; $cll='ou li'; }
    echo'<tr><td class="s7 '.$cll.'"><a href="?page=dorf1&dorfx='.$x.'&dorfy='.$y.'">'.$dorf->get('name').'</a></td>';
    for ($j=0;$j<=3;$j++) {
      $totlager[$j]+=$lager[$j];
      $la=number_format($lager[$j],0,'','´');
      echo'<td class="r7 '.$cl.'">'.$la.'</td>';
    }

    echo'<td class="'.$clr.'"><a href="?page=build&dorfx='.$x.'&dorfy='.$y.'&highest=17">
      '.$dorf->freieHandler().'/'.$dorf->handler().'</a></td></tr>';
    $gesamt_ver_handler+=$dorf->freieHandler();
    $gesamt_handler+=$dorf->handler();
  }
  echo'<tr><td colspan="12"></td></tr><tr><td class="s7 f10"><b>Summe</b></td>';
  for ($i=0;$i<=3;$i++) {
      if ($totlager[$i]==0) echo'<td class="c">0</td>';
      else echo'<td>'.number_format($totlager[$i],0,'','´').'</td>';
  }
  echo'<td>'.$gesamt_ver_handler.'/'.$gesamt_handler.'</td>';
  echo'</tbody></table>';
}


if ($_GET['s']=='') { 	//Übersicht

  echo'<table class="tbg" cellpadding="2" cellspacing="1">
    <tbody><tr class="rbg"><td colspan="6">Übersicht</td></tr>
    <tr><td width="150">Dorfname</td><td>Angriffe</td><td>Bau</td><td>Truppenbau</td><td>Händler</td></tr>';
  foreach($dorfer as $dorf) {

    $x=$dorf->get('x');
    $y=$dorf->get('y');
    $cl='';$clr='';$cll='';
    if ($x==$dorfx and $y==$dorfy) { $cl='ou'; $clr='ou re'; $cll='ou li'; }
    //Name
    echo'<tr><td class="s7 '.$cll.'"><a href="?page=dorf1&dorfx='.$x.'&dorfy='.$y.'">'.$dorf->get('name').'</a></td>';

    //Angriffe
    echo'<td class="'.$cl.'">';
    $moves=$dorf->truppenBewegungen();
//    x('');
    if (empty($moves)) echo'<span class="c">-</span>';
    else {
    	echo'<a href="?page=build&dorfx='.$x.'&dorfy='.$y.'&gid=39">';
    	foreach($moves as $typ => $move) {
    		echo'<img src="img/un/a/'.$typ.'.gif" border="0" onmouseover="TagToTip(\'tip'.$tooltip.'\',\'\')">
    			<span id="tip'.$tooltip.'">
    			<b class="c'.$move['farbe'].' f10">'.$move['anz'].' '.$move['volltext'].'</b><br>
					in <span id="timer'.$timerNr.'">'.zeit_dauer($move['first']-time()).'</span>
					Std.</span>';
    		$tooltip++;
    	}
    	echo'</a>';
    }
		echo'</td>';

    //Gebäude Bau
    echo'<td class="'.$cl.'">';
    $auftrage=$dorf->auftrage(10);
    if (empty($auftrage)) {
    	echo'<span class="c">-</span>';
    }
    else {
			foreach($auftrage as $auftrag) {
				$gid=$auftrag->get('id');
				$stufe=$dorf->gebeudeStufe($gid);
				$typ=$dorf->gebeudeTyp($gid);
				
				$gebeude=GebeudeTyp::getById($typ);
				$name=$gebeude->get('name');
				$stamp=strtotime($auftrag->get('zeit'));
				$fertig=date('H:i d.m.Y',$stamp);
				
				//Das Tooltip das angezeigt wird, nimmt den Inhalt des Span Elements an
				echo'<a href="?page=dorf1&dorfx='.$x.'&dorfy='.$y.'" onmouseover="TagToTip(\'tip'.$tooltip.'\',\'\')">
					<img src="img/un/a/bau.gif"></a>
					<span id="tip'.$tooltip.'">
					<b>'.$name.' '.$stufe.'</b><br>
					<span id="timer'.$timerNr.'">'.zeit_dauer($stamp-time()).'</span><br>
					Fertig: '.$fertig.'</span>';
				$tooltip++;
				$timerNr++;
			}
		}
    echo'</td>';

    //Truppen
    $none=TRUE;
    echo'<td class="'.$cl.'">';
    for ($typNr=1;$typNr<=4;$typNr++) {
   	  $auftrage=$dorf->auftrage($typNr);
      if (!empty($auftrage)) {
      	$none=FALSE;
        $einheiten=array();
        foreach($auftrage as $auftrag) {
          $einheiten[$auftrag->get('id')]+=$auftrag->get('anzahl');
          $stamp=strtotime($auftrag->get('zeit'))+($auftrag->get('anzahl')-1)*$auftrag->get('dauer');
          if ($stamp>$einheiten_finished[$auftrag->get('id')])
          	$einheiten_finished[$auftrag->get('id')]=$stamp;
        }
        foreach($einheiten as $tid => $anzahl) {
          $typ=TruppenTyp::getById($tid);
          if ($typNr<4) $h=18+$typNr;
          else $h=25;
          
          $stamp=$einheiten_finished[$tid];
          $fertig=date('H:i d.m.Y',$stamp);
          
          echo'<a href="?page=build&dorfx='.$x.'&dorfy='.$y.'&highest='.$h.'"
          	onmouseover="TagToTip(\'tip'.$tooltip.'\',\'\')">
						<img src="img/un/u/'.$tid.'.gif" ></a>
						<span id="tip'.$tooltip.'">
					<b>'.$anzahl.' '.$typ->named($anzahl).'</b><br>
					<span id="timer'.$timerNr.'">'.zeit_dauer($stamp-time()).'</span><br>
					Fertig: '.$fertig.'</span>';
				$tooltip++;
				$timerNr++;
            
        }
      }
    }
    if ($none) echo'<span class="c">-</span>';
    echo'</td>';

		//Händler
    echo'<td class="'.$clr.'"><a href="?page=build&dorfx='.$x.'&dorfy='.$y.'&highest=17">
      '.$dorf->freieHandler().'/'.$dorf->handler().'</a></td></tr>';
  }

  echo'</tbody></table>';

}
?>
<div>
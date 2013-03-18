<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::Dorf($login_dorf);


$size=Land::size();
$size_x=$size[0];
$size_y=$size[1];
$px=$_REQUEST['x'];
$py=$_REQUEST['y'];
if (!isset($px)) $px=$login_dorf->get('x');;
if (!isset($py)) $py=$login_dorf->get('y');;



//Ein Flecken Land anzeigen
$dorf=Dorf::getByXY($px,$py);
$land=Land::getByXY($px,$py);
if ($dorf!==NULL) $user=$dorf->user();

$highest=$login_dorf->highest();
$highestGid=$login_dorf->highestGid();

if ($dorf!==NULL) { //Ein Dorf ist darauf errichtet
	echo'<div class="dname"><h1>'.
		$dorf->get('name').' ('.$px.'|'.$py.')';
	if ($dorf->get('grosse')==1)
		echo'<br><div style="font-size:13px; color:#C0C0C0; ">(Hauptdorf)</div>';
	echo'</h1></div>
		<div id="f'.$land->get('typ').'"></div>
		<div class="map_details_right">
		<div class="f10 b">&nbsp;
		'.$dorf->get('name').' ('.$px.'|'.$py.')
		</div>';

	  //Allgemeine Informationen anzeigen
	echo'<table class="f10">
		<tbody><tr>
		<td><img src="img/un/a/x.gif" border="0"
		  height="12" width="3"></td>
		<td>Volk:</td><td> <b>
		'.Outputer::volk($user->get('volk')).'
		</b></td>
		</tr>

		<tr>
		<td><img src="img/un/a/x.gif" border="0" height="12"
		  width="3"></td>
		<td>Allianz:</td>
		<td>';
	//Allianz
	$allyid=$user->get('ally');
	if ($allyid==0)
		echo'-';
	else {
		$ally=Allianz::getById($allyid);
		echo'<a href="?page=allianz&id='.$allyid.'">'.$ally->get('tag').'</a>';
	}

	echo'</td>
		</tr>
		<tr>
		<td><img src="img/un/a/x.gif" border="0" height="12"
		  width="3"></td>
		<td>Besitzer:</td><td>
		'.$user->getLink(true).'</td>
		</tr>
		<tr>
		<td><img src="img/un/a/x.gif" border="0" height="12"
		  width="3"></td>
		<td>Einwohner:</td><td><b>
		'.$dorf->get('einwohner').'
		</b></td>

		</tr>
		</tbody></table>
		</div>';

	//Informationen über Einheiten
	echo'<div class="map_details_troops">
		<div class="f10 b">&nbsp;Einheiten:</div>
		<table class="f10">
		<tbody><tr>
		<td><img src="img/un/a/x.gif" border="0" height="12"
		  width="3"></td>
		<td>Es liegen keine <br>Informationen vor</td>
		</tr>
		</tbody></table></div>';

	//Aktionen für dieses Feld
	echo'<div class="map_details_actions">
		<div class="f10 b">Optionen:</div>
		<table class="f10" width="100%">
		<tbody><tr><td>
		<a href="?page=karte&x='.$px.
		  '&y='.$py.'">» Karte zentrieren</a>
		</td></tr>
		<tr>';
	//Truppen schicken
	if ($highest[16]>0)
		echo'<td><a href="?page=build&x='.$px.'&y='.$py.
			'&s=2&gid=39">» Truppen schicken</a>';
	else
		echo'<td class="c">» kein Versammlungsplatz vorhanden';
	echo'</td></tr><tr>';
	//Marktplatz
	if ($highest[17]>0)
		echo'<td><a href="?page=build&x='.$px.'&y='.$py.
			'&id='.$highestGid[17].'">» Händler schicken</a>';
	else
		echo'<td class="c">» kein Marktplatz vorhanden</td>';
	echo'</td></tr></tbody></table>';
}

if ($dorf===NULL) { //Kein Dorf darauf gebaut
  //Kein Bauland existiert -> Ödland
  if ($land===NULL) {
    echo'<h1>Ödland ('.$px.'|'.$py.')</h1>
      <div id="fo1"></div>
      <div id="pr" class="map_details_right">
      <div class="f10 b">&nbsp;Landverteilung:</div>
      <table class="f10"><tbody>
      <tr><td><img class="res" src="img/un/r/1.gif"></td>
      <td class="s7 b">0</td><td> Holzfäller</td></tr>
      <tr><td><img class="res" src="img/un/r/2.gif"></td>
      <td class="s7 b">0</td><td> Lehmgruben</td></tr>
      <tr><td><img class="res" src="img/un/r/3.gif"></td>
      <td class="s7 b">0</td><td> Eisenminen</td></tr>
      <tr><td><img class="res" src="img/un/r/4.gif"></td>
      <td class="s7 b">0</td><td> Getreidefarmen</td></tr>
      </tbody></table></div>
      <div class="map_details_actions">
      <div class="f10 b">Optionen:</div>
      <table class="f10" width="100%"><tbody>
      <tr><td><a href="?page=karte&x='.$px.
        '&y='.$py.'">» Karte zentrieren</a></td></tr>
      </tbody></table>';
  }
  //Bauland existiert -> freies Bauland
  if ($land!==NULL and $land->get('oase')==0) {
    $minen=$land->rohstoffGebeude();

    echo'<h1>verlassenes Tal ('.$px.'|'.$py.')</h1>
      <div id="f'.$land->get('typ').'"></div>
      <div id="pr" class="map_details_right">
      <div class="f10 b">&nbsp;Landverteilung:</div>
      <table class="f10">
      <tbody><tr>
      <td><img class="res" src="img/un/r/1.gif"></td>
      <td class="s7 b">'.$minen[1].'</td><td> Holzfäller</td>
      </tr>
      <tr>
      <td><img class="res" src="img/un/r/2.gif"></td>
      <td class="s7 b">'.$minen[2].'</td><td> Lehmgruben</td>
      </tr>
      <tr>
      <td><img class="res" src="img/un/r/3.gif"></td>
      <td class="s7 b">'.$minen[3].'</td><td> Eisenminen</td>
      </tr>
      <tr>
      <td><img class="res" src="img/un/r/4.gif"></td>
      <td class="s7 b">'.$minen[4].'</td><td>Getreidefarmen</td>
      </tr>

      </tbody></table>
      </div>';
    //Aktionen
    echo'<div class="map_details_actions">
      <div class="f10 b">Optionen:</div>
      <table class="f10" width="100%">
      <tbody><tr>

      <td>
      <a href="karte.php?x='.$px.
        '&y='.$py.'">» Karte zentrieren</a></td>
      </tr><tr>';
    //neues Dorf gründen lassen
    $siedler=$login_dorf->anzahlSiedler();
    if ($siedler<3)
      echo'<td class="c">» Neues Dorf gründen ('.
          $siedler.'/3 Siedlern vorhanden)</td></tr>';
    else
      echo'<tr><td><a href="?page=a2b&s=newvillage&x='.$px.
        '&y='.$py.'">» Neues Dorf gründen</a></td></tr>';

    echo'</tbody></table>';
  }
  //Eine Oase anzeigen
  if ($land!==NULL and $land->get('oase')==1) {
    $truppen=Truppe::getByXYU($px,$py,0);
    $tiere=1;
    $einheiten=array();
    if ($truppen!==NULL)
      $einheiten=$truppen->soldatenID();

    if (array_sum($einheiten)==0) $tiere=0;
    echo'<h1>verlassenes Tal ('.$px.'|'.$py.')</h1>
      <img src="img/un/m/w'.$land->get('typ').'.jpg"
        id="resfeld">

      <div id="pr" class="map_details_right"><p></p>
      <div class="f10 b">&nbsp;Einheiten:</div>
      <table class="f10"><tbody>';
    if ($tiere==0) echo'<tr><td>keine</td></tr>';
    else {
      foreach ($einheiten as $id=>$anz) {
        $anz=round($anz);
        if ($anz>0) {
          $typ=TruppenTyp::getById($id);
          if ($anz>1) $name=$typ->get('mehrzahl');
          else $name=$typ->get('name');
          echo'<tr><td><img class="unit" src="img/un/u/'.$id.'.gif" border="0"></td>
            <td align="right">&nbsp;<b>'.$anz.'</b></td><td>'.$name.'</td></tr>';
        }
      }
    }
    echo'</tbody></table></div>
      <div class="map_details_actions">
      <div class="f10 b">Optionen:</div>
      <table class="f10" width="100%">
      <tbody><tr>
      <td>
      <a href="?page=karte&x='.$px.
        '&y='.$py.'">» Karte zentrieren</a>
      </td>
      </tr><tr>';
    if ($highest[16]>0)
      echo'<td><a href="?page=build&x='.$px.'&y='.$py.
        '&s=2&gid=39&c=4'.
        '">» Verlassenes Tal erkunden</a>';
    else
      echo'<td class="c">» kein Versammlungsplatz vorhanden';

    echo'</td></tr></tbody></table>';
  }
}

?>
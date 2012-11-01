<?php
if (!$execute) die('');
outgame_links();
outgame_blocks();

$s=saveGet('s',0);
	
echo'<div style="position:relative; top:-30px;">
    <img src="img/de/t1/anleitung.gif" width="468" height="60" border="0" />
  </div>
  <div class="wholebox">
    <a href="?page=anleitung" '.($s==0?'class="selected"':'').'>Die V�lker</a> |
    <a href="?page=anleitung&s=1" '.($s==1?'class="selected"':'').'>Die Geb�ude</a> |
    <a href="?page=anleitung&s=2" '.($s==2?'class="selected"':'').'>FAQ</a> |
    <a href="?page=anleitung&s=3" '.($s==3?'class="selected"':'').'>Neue D�rfer</a>
  </div>';

if ($s==0) {
?>

<div class="wholebox">

<p class="f9">
Bei Travian gibt es drei V�lker: R�mer, Gallier und Germanen.
Jedes Volk hat seine speziellen Vor -und Nachteile. Auch die
Einheitentypen unterscheiden sich deutlich voneinander. Daher
ist es wichtig zu Beginn des Spieles das Volk zu nehmen, das zu einem passt.</p>

<div id="roemer" style="color: black;">
  <div id="desc">
    <h5><img src="img/de/t2/roemer.gif" width="160" height="15" border="0" /></h5>
    <p class="f9" align="justify">
    <img align="right" src="img/un/h/roemer.jpg" width="128" height="156" border="0" alt="R�mer" style="margin-left:10px;">
    Aufgrund der hohen gesellschaftlichen und technischen Entwicklung sind die
    R�mer Meister der Koordination in der Baukunst. Ihre Soldaten geh�ren zur
    Elite in Travian. Um diese Vielseitigkeit zu gew�hrleisten, durchlaufen sie
    jedoch eine langwierige und vor allem teure Ausbildung.<br><br>
    Ihre Infanterie ist legend�r, jedoch ist ihre Verteidigungsst�rke gegen
    Kavallerie deutlich niedriger als bei den anderen beiden V�lkern. Die r�mischen
    H�ndler besitzen die geringste Tragekapazit�t aller V�lker Travians, wodurch es
    gerne auch mal zu Versorgungsengp�ssen in den D�rfern kommen kann.<br><br>

    Da der R�mer gerade in der Anfangsphase seine Schw�chen hat, ist er f�r
    Travian Neulinge nicht zu empfehlen.</p>
    <br clear="all" />
  </div>
  <div id="tab">
    <table cellspacing="1" cellpadding="2" class="tbg"><tr class="rbg">
    <td colspan="12">Die R�mischen Truppen</td></tr>
    <?php
    table_atts();
    table_truppen(1);
    ?>
    </table>
    <br/>
  </div>
  <div class="f9" style="color: black; width: 100%">
    <strong>Die Besonderheiten</strong>
    <br/>
  </div>
  <div class="lbox" style="color: black; font-weight: normal; width: 100%;">
    <ul>
    <li>Gleichzeitiges Errichten von Rohstoff- und Stadtgeb�uden</li>
    <li>Hoher Verteidigungsbonus durch Stadtmauer</li>
    <li>H�ndler k�nnen 500 Rohstoffe tragen (Tempo: 16 Felder/Stunde)</li>
    <li>Sehr starke Infanterie, mittelm��ige Kavallerie</li>
    <li>Entwicklung ist teuer und langwierig</li></ul>
    <br/>
  </div>
</div>
<div id="gallier" style="color: black;">
  <div id="desc">
    <h5><img src="img/de/t2/gallier.gif" width="160" height="15" border="0" /></h5>
    <p class="f9" align="justify">
    <img align="right" src="img/un/h/gallier.jpg" width="96" height="156" border="0" alt="Gallier" style="margin-left:10px;">
    Das gallische Volk ist das friedliebendste von allen V�lkern. Seine Einheiten werden stark
    in der Verteidigung geschult, jedoch steht es in Angriffskraft den anderen V�lkern kaum nach.
    Der Gallier ist der geborene Reiter. Seine Pferde sind legend�r aufgrund ihrer Schnelligkeit,
    mit der sie die Feinde schnell und unvorbereitet �berraschen.<br><br>
    Dieses Volk ist relativ leicht zu verteidigen, aber auch eine offensive Spielweise ist
    realisierbar. Es bietet die M�glichkeit, sich in jede beliebige strategische Richtung
    (offensive oder defensive Spielweise, Eigenbr�tler oder Helfer in der Not, H�ndler oder
    Pl�nderer, Infanterist oder Kavallerist, Siedler oder Eroberer) zu entwickeln, jedoch
    ist hierf�r ein wenig spielerisches Geschick erforderlich.<br><br>
    F�r Anf�nger und diejenigen, die nicht wissen, was genau sie spielen sollen ist der
    Gallier ideal.</p>
    <br clear="all" />
  </div>
  <div id="tab">
    <table cellspacing="1" cellpadding="2" class="tbg"><tr class="rbg">
    <td colspan="12">Die Truppen der Gallier</td></tr>
    <?php
    table_atts();
    table_truppen(3);
    ?>
    </table>
    <br/>
  </div>
  <div class="f9" style="color: black; width: 100%">
    <strong>Die Besonderheiten</strong>
    <br/>
  </div>
  <div class="lbox" style="color: black; font-weight: normal; width: 100%;">
    <ul>
    <li>Geschwindigkeitsbonus: Schnellste Einheiten im Spiel</li>
    <li>Mittlerer Verteidigungsbonus durch Palisade</li>
    <li>H�ndler k�nnen 750 Rohstoffe tragen (Tempo: 24 Felder/Stunde)</li>
    <li>Doppelt so gro�es Versteck (Pl�nderschutz)</li>

    <li>Teure Kriegsmaschinerie</li>
    <li>Billige Siedler</li></ul>
    <br/>
  </div>
</div>
<div id="germanen" style="color: black;">
  <div id="desc">
    <h5><img src="img/de/t2/germanen.gif" width="160" height="15" border="0" /></h5>
    <p class="f9" align="justify">
    <img align="left" src="img/un/h/germane.jpg" width="104" height="151" border="0" alt="Germane" style="margin-right:10px;">
    Das germanische Volk ist das offensivste von allen. Germanische
    Krieger sind allesamt gef�rchtet aufgrund ihrer berserkerartigen
    Wildheit im Angriff. Sie ziehen als pl�ndernde Horde durch die
    Lande, ohne Furcht vor dem Tod.
    <br><br>
    Allerdings fehlt den Germanen die milit�rische Disziplin der R�mer
    und Gallier, weshalb ihre Schw�chen in der Geschwindigkeit und in
    der Defensive liegen.<br><br><br>
    F�r offensive und erfahrene Spieler ist der Germane gut geeignet.</p>
    <br clear="all" />
  </div>
  <div id="tab">
    <table cellspacing="1" cellpadding="2" class="tbg"><tr class="rbg">
    <td colspan="12">Die Germanischen Truppen</td></tr>
    <?php
    table_atts();
    table_truppen(2);
    ?>
    </table>
    <br/>
  </div>
  <div class="f9" style="color: black; width: 100%">
    <strong>Die Besonderheiten</strong>
    <br/>
  </div>
  <div class="lbox" style="color: black; font-weight: normal; width: 100%;">
    <ul>
    <li>Pl�nderbonus: Das Versteck von Gegnern z�hlt nur zu 2/3</li>
    <li>Erdwall fast unzerst�rbar, bietet jedoch nur geringen Schutz</li>
    <li>H�ndler k�nnen 1000 Rohstoffe tragen (Tempo: 12 Felder/Stunde)</li>
    <li>�u�erst billige, pl�nderstarke und schnell produzierbare Einheiten</li>
    <li>Schw�che in der Verteidigung</li></ul>

    <br/>
  </div>
</div>


</div>
<?php
}


if ($s==1) {
?>
<br>
Zu Beginn des Spiels sollte man zun�chst f�r eine solide wirtschaftliche
Basis sorgen. Hierf�r m�ssen die 18 Rohstofffelder (der Dorf�bersicht)
ausgebaut werden. Hierbei gibt es vier Typen von Feldern: Holzf�ller,
Lehmgrube, Eisenmine und Getreidefarm. Wenn man auf eines der Felder
klickt, bekommt man weitere Infos und die M�glichkeit das Feld auszubauen.
<br><br>
Im weiteren Spielverlauf werden auch die Geb�ude in deinem Dorf wichtig.
Um ein neues Geb�ude zu errichten, musst du auf einen der gr�nen Kreise
klicken. Dann erscheint eine Liste aller im Moment zur Verf�gung stehenden Bauten.
<br><br>
Manche Geb�ude kann man erst sp�ter bauen. Sie ben�tigen andere Geb�ude als Voraussetzung.</p>
<br><br>
<?php
$gebeude=array(15,10,11,23,18,16,17,19,20,21,22,12,13,26,25,28,14);
$align=array(0,1,1,0,1,1,0,0,0,0,1,0,1,1,1,1,0);

foreach ($gebeude as $index=>$id) {
	$geb=GebeudeTyp::getByID($id);
	$name=$geb->get('name');
	$a=($align[$index]==0?'left':'right');
	$kosten=$geb->baukosten(1);
	$zeit=$geb->bauzeit(1);
	$vorteile=$geb->get('volksvorteile');
	$besch=insert_div($geb->get('besch'));

	echo'<h1>'.$name.'</h1><br>
		<img src="img/un/h/gid'.$id.'.gif" alt="'.$name.'" title="'.$name.'" align="'.$a.'"
			border="0" height="150" width="166">
		<p class="f9">'.t($besch).'<br><br>';
	if ($vorteile!='') echo'<b>Volksvorteile:</b><br>'.t($vorteile).'<br><br>';
	echo'
		<b>Kosten</b> und <b>Bauzeit</b> bei Stufe 1:<br>
    <img src="img/un/r/1.gif" alt="Holz" title="Holz" style="padding-top: 4px;" height="12" width="18">
    '.$kosten[0].' | <img src="img/un/r/2.gif" alt="Lehm" title="Lehm" height="12" width="18">
    '.$kosten[1].' | <img src="img/un/r/3.gif" alt="Eisen" title="Eisen" height="12" width="18">
    '.$kosten[2].' | <img src="img/un/r/4.gif" alt="Getreide" title="Getreide" height="12" width="18">
    '.$kosten[3].' | <img src="img/un/r/5.gif" alt="Getreideverbrauch" title="Getreideverbrauch" height="12" width="18">
    '.$geb->get('arbeiter').' | <nobr><img src="img/un/a/clock.gif" height="12" width="18"> '.zeit_dauer($zeit).'</nobr><br>
    <br><b>Voraussetzungen:</b><br>';
  $needs=$geb->needs();
	if (!empty($needs)) {
		$anz=0;
	  foreach($needs as $id2 => $stufe) {
		  $geb2=GebeudeTyp::getById($id2);
		  echo $geb2->get('name').' Stufe '.$stufe;
		  $anz++;
		  if ($anz<sizeof($needs)) echo', ';
		}
	}
	else echo'keine';
	echo'</p>';
}

}

if ($s==2) {
?>
<div class="wholebox">
<img src="img/un/h/faq_vp.jpg" alt="Versammlungsplatz" title="Versammlungsplatz"
	align="right" border="0" height="128" width="116">
<p class="f10 e b">Wie kann ich einen Versammlungsplatz bauen?</p>
<div class="f10">Der Versammlungsplatz und die Mauer werden nur an einem fest
vorgegebenen Bauplatz gebaut. Um den Versammlungsplatz zu bauen, klicke rechts
vom Dorfmittelpunkt auf die Wiese. Um die Mauer zu bauen, klicke auf den Graben
der rings um das Dorf ausgehoben ist.</div>

<img src="img/un/h/faq_botschaft.jpg" alt="Botschaft" title="Botschaft"
	align="left" border="0" height="150" width="122">
<p class="f10 e b">Wie gr�nde ich eine Allianz?</p>
<div class="f10">Um eine Allianz gr�nden zu k�nnen, ben�tigst du eine
Botschaft Stufe 3. Um einer beitreten zu k�nnen, reicht eine Botschaft
Stufe 1, sowie die Einladung einer Allianz.</div>

<p class="f10 e b">Wie kann ich mein Dorf umbenennen?</p>
<div class="f10">Dazu klicke auf <i>Profil</i>, dort auf deinen <i>Profil
bearbeiten</i>. Im Feld <i>Dorfname</i> den neuen Namen eingeben.</div>

<p class="f10 e b">Wie baue ich Truppen?</p>
<div class="f10">Du ben�tigst einen Versammlungsplatz Stufe 1 und ein
Hauptgeb�ude Stufe 3. Dann kannst du eine Kaserne bauen und in dieser deine Truppen.</div>

<p class="f10 e b">Wie verteidige ich mein Dorf?</p>
<div class="f10">Solange du Truppen (eigene oder befreundete) in deinem
Dorf stationiert hast, verteidigen diese dein Dorf automatisch.</div>

<p class="f10 e b">Warum verliere ich beim Angriff auf ein leeres Dorf Truppen?</p>
<div class="f10">Jedes Dorf hat eine geringe Grundverteidigung. Mit Ausnahme
von starken Kavallerieeinheiten verliert man immer, wenn man nur mit einer einzigen
Einheit angreift. Rein defensive Einheiten wie die Phalanxen der Gallier
eignen sich �berhaupt nicht zum Angriff.</div>

<p class="f10 e b">Wie bekomme ich mehr Einwohner?</p>
<div class="f10">Jedes Geb�ude bringt dir eine gewisse Anzahl neuer Einwohner.
Wie viele Einwohner beim Geb�ude(aus)bau dazukommen, kannst du an der Zahl nach
dem Symbol <img src="img/un/res/5.gif" alt="Getreideverbrauch" title="Getreideverbrauch"
	border="0" height="12" width="18"> erkennen.</div>

<p class="f10 e b">Hilfe, meine Getreideproduktion wird immer weniger!</p>
<div class="f10">Bei <i>Produktion</i> (z.B. 10) wird deine Getreideproduktion
abz�glich dem Getreideverbrauch durch <i>Einwohner</i> (z.B. 5) und <i>Truppen</i>
angezeigt. Deine Bruttogetreideproduktion kannst du oben rechts ablesen. z.B.
<img src="img/un/res/5.gif" alt="Getreideverbrauch" title="Getreideverbrauch"
	border="0" height="12" width="18">  5/15<br>In dem Beispiel w�re 5 dein Verbrauch,
15 deine Getreideproduktion, <br>15 - 5 = 10 Getreide w�rde pro Stunde �brig bleiben.</div>

<p class="f10 e b">Warum rauben meine Truppen so wenige Rohstoffe?</p>
<div class="f10">Das kann mehrere Gr�nde haben. Zum einen kann jede Einheit
nur eine bestimmte Menge Rohstoffe tragen, zum anderen sch�tzt ein <i>Versteck</i>
automatisch eine bestimme Anzahl an Rohstoffen vor dem Feind.</div>

<p class="f10 e b">Wie kann ich neue D�rfer gr�nden oder erobern?</p>
<div class="f10">Um neue D�rfer zu gr�nden, brauchst du drei Siedler,
um ein Dorf zu erobern einen Stadtverwalter (je nach Volk Senator, H�uptling, Stammesf�hrer),
die du im Palast/Residenz ab Stufe 10 bauen kannst. Au�erdem musst du auch eine gewisse Menge
<a href="?page=anleitung&s=3">Kulturpunkte</a> angesammelt haben. Beim Gr�nden von neuen D�rfern,
braucht man zus�tzlich ein freies Slot in dem Dorf in dem man die Siedler losschicken will.<br>
Je ein Slot erh�lt man beim Ausbau der Residenz/Palast auf Stufe 10 und 20. Ein zus�tzliches
erh�lt man beim Ausbau des Palastes auf Stufe 15.</div>

<p class="f10 e b">Kann ich mein Dorf verlieren?</p>
<div class="f10">Dein Hauptdorf ist vor Eroberungen immer gesch�tzt.
Alle anderen D�rfer k�nnen erobert werden nachdem der Palast oder die Residenz
zerst�rt wurden und die Zustimmung auf 0 gesunken ist. Wenn du nur noch ein
Dorf besitzt, so ist dieses auch vor Eroberungen gesch�tzt, auch
wenn es kein Hauptdorf ist.</div>

</div>

<?php
}

if ($s==3) {
?>
<div class="wholebox">
<img align="right" src="img/un/u2/u30.gif" alt="Siedler" title="Siedler" style="margin-left:10px;">
<p class="f10 e b">Wie gr�nde ich ein neues Dorf?</p>
<div class="f10" align="justify">Um ein neues Dorf zu gr�nden braucht man drei Siedler,
ein leeres Feld auf der Karte, gen�gend Kulturpunkte und ein freies Slot.
Der Siedler muss nicht erforscht werden, sondern kann gleich im Palast/Residenz
ab Stufe 10 ausgebildet werden. Um eine Residenz bauen zu k�nnen, braucht man
ein Hauptgeb�ude Stufe 5, f�r einen Palast ist auch noch eine Botschaft notwendig.</div>

<p class="f10 e b">Wie erobere ich ein Dorf?</p>
<div class="f10">Auch hier braucht man mindestens einen Palast/Residenz Stufe 10.
Dazu ein  gegnerisches Dorf, das �bernommen werden kann sowie gen�gend Kulturpunkte.
Das Hauptdorf (Dorf mit Palast oder erstes Dorf) von allen Spielern, kann nicht
erobert werden.<br><br>
<img align="left" src="img/un/u2/u9.gif" alt="Senator" title="Senator" style="margin:0px -20px 0px -40px;">
Sobald der Stadtverwalter (Senator/ Stammesf�hrer/ H�uptling) in der Akademie
erforscht wurde, kann er im Palast/Residenz ab Stufe 10 ausgebildet werden.
Um ein Dorf zu erobern, muss man mit dem Stadtverwalter (und Armee) dieses
mehrmals angreifen. Mit jedem Angriff wird die Zustimmung der Dorfbev�lkerung
zu ihrem alten Herrscher gesenkt, f�llt sie auf Null, l�uft die Bev�lkerung
zu dir �ber und das Dorf geh�rt dir. Damit der Stadtverwalter die Zustimmung
senken kann, muss aber erstmal der Palast bzw. die Residenz in dem Dorf zerst�rt
worden sein.</div><p class="f10 e b">Wie bekomme ich Kulturpunkte?</p>

<div class="f10">Kulturpunkte (KPs) bekommt man durch den (Aus)Bau von
Geb�uden. Wie viele Kulturpunkte du bisher produziert hast, kannst du
in deinem Palast/Residenz sehen.</div><br>

<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr class="rbg">
<td colspan="21">
Ben�tigte Kulturpunkte f�r weitere D�rfer:</td>
</tr>
<?php

$kp_dorfer=explode(':',Diverses::get('neue_dorfer'));
$dorf='';$kp='';
for ($i=2;$i<=10;$i++) {
	$dorf.='<td>'.$i.'</td>';
	$kp_benotigt=$kp_dorfer[$i-2]*1000;
	$kp.='<td>'.$kp_benotigt.'</td>';
}


echo'<tr class="cbg1" align="center">
	<td class="left">Dorf</td>'.$dorf.'</tr>
	<tr class="cbg1" align="center">
	<td class="left">KP</td>'.$kp.'</tr>
	</tbody></table>
	</div>';

}



function table_truppen($volk) {
  for ($i=$volk*10-9;$i<=$volk*10;$i++) {
    $typ=TruppenTyp::getById($i);
    $werte=$typ->werte();
    $kosten=$typ->baukosten();
    echo'<tr><td width="25" align="center">
      <img src="img/un/u/'.$i.'.gif" width="16" height="16" border="0" alt=""></td>
      <td class="s7 f9" width="135">'.$typ->get('name').'</td>
      <td width="25">'.$werte[0].'</td><td>'.$werte[1].'</td><td>'.$werte[2].'</td>';
    for ($j=0;$j<4;$j++)
      echo'<td>'.$kosten[$j].'</td>';
    echo'<td>'.$typ->get('speed').'</td></tr>';
  }
}

function table_atts() {
	?>
  <tr class="cbg1">
  <td colspan="2">&nbsp;</td>
  <td><img src="img/un/h/att_all.gif" width="16" height="16"
    border="0" alt="Angriffswert" title="Angriffswert" /></td>
  <td><img src="img/un/h/def_i.gif" width="16" height="16" border="0"
    alt="Verteidigungswert gegen Infantrie" title="Verteidigungswert gegen Infantrie" /></td>
  <td><img src="img/un/h/def_c.gif" width="16" height="16" border="0"
    alt="Verteidigungswert gegen Kavallerie" title="Verteidigungswert gegen Kavallerie" /></td>
  <td><img src="img/un/r/1.gif" width="18" height="12" border="0" alt="Holz" /></td>
  <td><img src="img/un/r/2.gif" width="18" height="12" border="0" alt="Lehm" /></td>
  <td><img src="img/un/r/3.gif" width="18" height="12" border="0" alt="Eisen" /></td>
  <td><img src="img/un/r/4.gif" width="18" height="12" border="0" alt="Getreide" /></td>
  <td title="Felder/Stunde">Tempo</td></tr>
  <?php
}
?>
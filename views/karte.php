<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::Dorf($login_dorf);

$size=Land::size();
$size_x=$size[0];
$size_y=$size[1];
if (isset($_REQUEST['x']))
	$px=$_REQUEST['x'];
else
	$px=$login_dorf->get('x');
if (isset($_REQUEST['y']))
	$py=$_REQUEST['y'];
else
	$py=$login_dorf->get('y');
if (!isset($_GET['s'])) {
	if ($px-3<1) $px=3+1;
	if ($py-3<1) $py=3+1;
	if ($px+3>$size_x) $px=-3+$size_x;
	if ($py+3>$size_y) $py=-3+$size_y;
}



//Infobox der Karte
?>
<div class="map_link_to_xxlmap"><a href="#" onclick="PopupMap();">
<img src="img/un/m/max.gif" alt="Grosse Karte" title="Grosse Karte"></a></div>
<div id="lplz3"></div>
<script language="JavaScript" type="text/javascript">
<!--
text_details = 'Details:';
text_spieler = 'Spieler:';
text_einwohner = 'Einwohner:';
text_allianz = 'Allianz:';
// -->
</script>
<div class="map_infobox" id="tb">
  <table class="f8 map_infobox_grey" cellpadding="2"
    cellspacing="1"><tbody>
  <tr>
  <td class="c b" colspan="2" align="center">Details:</td>
  </tr>
  <tr><td class="c s7" width="45%">Spieler:</td>
    <td class="c s7">-</td></tr>
  <tr>
  <td class="c s7">Einwohner:</td><td class="c s7">-</td>
  </tr><tr>
  <td class="c s7">Allianz:</td><td class="c s7">-</td>
  </tr>
  </tbody></table>
</div>
<div class="mbg"></div>
<div id="map_content"><div class="map_show_xy">
<table cellpadding="0" cellspacing="0" width="100%">
<tbody><tr><td width="30%"><h1>Karte</h1></td>
<td class="right nbr" width="33%"><h1>(<span id="x"><?php echo $px; ?></span></h1></td>
<td align="center" width="4%"><h1>|</h1></td>
<td class="left nbr" width="33%"><h1><span id="y"><?php echo $py; ?></span>)</h1></td>
</tr>
</tbody></table>
</div>


<?php

//Bilder der Karte

echo'<div class="mdiv" style="z-index: 2;">';
$nr=1;
$size=Land::size();
for ($y=-3;$y<=3;$y++) {
  for ($x=-3;$x<=3;$x++) {
    $land=Land::getByXY($x+$px,$y+$py);
    if ($land===NULL) {
      $typ=(pow($px+$x,2)+($py+$y)*$size[0])%6+1;
      echo'<img class="mt'.$nr.'" src="img/un/m/t_no'.
        $typ.'.png">';
    }
    elseif ($land->get('oase')==1) {
      $bild=$land->get('typ');
      echo'<img class="mt'.$nr.'" src="img/un/m/o'.$bild.'.gif">';
    }
    else {
      $dorf=Dorf::getByXY($x+$px,$y+$py);
      if ($dorf!==NULL) {
        $bild=0;
        if ($dorf->get('einwohner')>99) $bild+=10;
        if ($dorf->get('einwohner')>399) $bild+=10;
        if ($dorf->get('einwohner')>999) $bild+=10;
        if ($dorf->deinDorf()) $bild+=1;
        if ($bild<10) $bild='0'.$bild;
        echo'<img class="mt'.$nr.'" src="img/un/m/d'.
          $bild.'.gif">';
      }
      else {
        echo'<img class="mt'.$nr.'" src="img/un/m/t'.
          $land->get('aussehen').'.gif">';
      }
    }
    $nr++;
  }
}
echo'</div>';



//Massstab
for ($y=1;$y<=7;$y++)
  echo'<div class="my'.$y.'">'.($y-4+$py).'</div>';
for ($x=1;$x<=7;$x++)
  echo'<div class="mx'.$x.'">'.($x-4+$px).'</div>';


//Anklickbare Flächen
echo'<map id="map190888" name="map190888">';
if ($py-4>=1)
  echo'<area href="?page=karte&x='.$px.'&y='.($py-1).
    '" coords="422,137,25" shape="circle" title="Norden">';
if ($px+4<=$size_x)
  echo'<area href="?page=karte&x='.($px+1).'&y='.$py.
    '" coords="427,324,25" shape="circle" title="Osten">';
if ($py+4<=$size_y)
  echo'<area href="?page=karte&x='.$px.'&y='.($py+1).
    '" coords="119,325,25" shape="circle" title="Süden">';
if ($px-4>=1)
  echo'<area href="?page=karte&x='.($px-1).'&y='.$py.'"
    coords="114,133,25" shape="circle" title="Westen">';

echo'<area href="?page=karte&x='.$px.'&y='.($py-7).'"
    coords="475,369, 497,357, 519,369, 497,381" shape="poly"
    title="Norden">
  <area href="?page=karte&x='.($px+7).'&y='.$py.'"
    coords="475,395, 497,383, 519,395, 497,407" shape="poly"
    title="Osten">
  <area href="?page=karte&x='.$px.'&y='.($py+7).'"
    coords="428,395, 450,383, 472,395, 450,407" shape="poly"
    title="Süden">
  <area href="?page=karte&x='.($px-7).'&y='.$py.'"
    coords="428,369, 450,357, 472,369, 450,381" shape="poly"
    title="Westen">';

 //Infos auf der Karte
for ($y=-3;$y<=3;$y++) {
  for ($x=-3;$x<=3;$x++) {
    $dorf=Dorf::getByXY($px+$x,$py+$y);
    $coords='coords="'.(229+37*($x+3)-36*($y+3)).','.
      (110+20*($x+3)+20*($y+3)).','.
      (265+37*($x+3)-36*($y+3)).','.
      (90+20*($x+3)+20*($y+3)).','.
      (302+37*($x+3)-36*($y+3)).','.
      (110+20*($x+3)+20*($y+3)).','.
      (265+37*($x+3)-36*($y+3)).','.
      (130+20*($x+3)+20*($y+3)).'" shape="poly"';
    $link='<area href="?page=karte-show&x='.
      ($px+$x).'&y='.($py+$y).'" ';
    $xy="'".($px+$x)."','".($py+$y)."'";
    if ($dorf!==NULL) {
      $dname=$dorf->get('name');
      $deinwohner=$dorf->get('einwohner');
      $user=Spieler::getById($dorf->get('user'));
      $sname=$user->get('name');
      $allyid=$user->get('ally');
      $ally='-';
      if ($allyid>0) $ally=Allianz::getByID($allyid)->get('tag');
      echo $link.' onmouseover="'."map('".$dname."','".$sname."',
        '".$deinwohner."','".$ally."',".$xy.")".'"
        onmouseout="'."map('','','','',".$xy.")".'" '.
        $coords.'>';
    }
    else {
    	$ress='';
    	$land=Land::getByXY($px+$x,$py+$y);
		if ($land!=null){
			$gebs=$land->rohstoffGebeude();
			if ($gebs!==NULL) {
				ksort($gebs);
				$ress=",0,'".implode('-',$gebs)."'";
			}
			else {
				$ress=",1";
			}
		}
      echo $link.' onmouseover="x_y('.$xy.$ress.')"
        onmouseout="x_y('.$xy.')" '.$coords.'>';
    }
  }
}
echo'</map>';



//Inputform um auf der Karte zu springen
echo'<img class="mdiv" style="z-index: 15;"
  usemap="#map190888" src="img/un/a/x.gif">
  <div class="map_insert_xy">
  <form method="post" action="?page=karte">
  <table align="center" cellpadding="3" cellspacing="0">
  <tbody><tr>
  <td><b>x</b></td><td><input class="fm fm25" name="x"
    value="'.$px.'" size="2" maxlength="4"></td>
  <td><b>y</b></td><td><input class="fm fm25" name="y"
    value="'.$py.'" size="2" maxlength="4"></td>
  <td></td><td>
  <input value="ok" name="s1" src="img/de/b/ok1.gif"
    onMouseDown="btm1(\'s1\',\'\',\'img/de/b/ok2.gif\',1)"
    onMouseOver="btm1(\'s1\',\'\',\'img/de/b/ok3.gif\',1)"
    onMouseUp="btm0()" onMouseOut="btm0()" height="20"
    type="image" width="50"></td></tr></tbody>
  </table>
  </form>';


echo'</div>';


?>
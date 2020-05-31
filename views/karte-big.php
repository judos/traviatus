<?php
if (!$execute) die('');
needed_login();

hideblock('menu');
hideblock('servertime');



$template=false;

$imgpath='img/un/m';



$size=Land::size();
$size_x=$size[0];
$size_y=$size[1];

if (isset($_REQUEST['x']))
	$px=$_REQUEST['x'];
else
	$px=$login_dorf->get('x');;
if (isset($_REQUEST['y']))
	$py=$_REQUEST['y'];
else
	$py=$login_dorf->get('y');;

if (!isset($_GET['s'])) {
	if ($px-3<1) $px=3+1;
	if ($py-3<1) $py=3+1;
	if ($px+3>$size_x) $px=-3+$size_x;
	if ($py+3>$size_y) $py=-3+$size_y;
}



?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>BigMap</title>
<link rel=stylesheet type="text/css" href="unx.css">
<script src="js/unx.js" type=text/javascript></script>
<link rel="SHORTCUT ICON" href="img/favicon.ico" type="image/x-icon">
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content="0" />
</head>
<body>
<table style="position:absolute; top:10px; left:45px; width:750px; height:420px;"><tr><td>

<?php


//Karte
echo'<img usemap="#karte" src="'.$imgpath.'2/blank.gif" border=0
    style="position:absolute; top:-50px; left:50px; width:675px; height:545px; z-index:10;">';
echo'<img src="'.$imgpath.'2/map.gif" border=0 style="position:absolute; top:-50px; left:50px; width:675px; height:545px;">';
echo'<div>';

//Link zurück
echo'<p style="font-size:18pt; position:absolute; left:65px; top:0px;">
	<b>Karte &nbsp;&nbsp;(<span id="x">'.$px.'</span>|<span id="y">'.$py.'</span>)</b></p>';

//Diverses
?>
<div id="lplz3"></div>
<script language="JavaScript" type="text/javascript">
<!--
text_details = 'Details:';
text_spieler = 'Spieler:';
text_einwohner = 'Einwohner:';
text_allianz = 'Allianz:';
// -->
</script>
<?php


//Info Kasten
echo'<div id="tb" class="map_infobox" style="position:absolute; top:20px; left:610px;">
  <table cellspacing="1" cellpadding="2" class="f8 map_infobox_grey"><tbody>
  <tr><td align="center" colspan="2" class="c b">Details:</td></tr>
  <tr><td width="45%" class="c s7">Spieler:</td><td class="c s7">-</td></tr>
  <tr><td class="c s7">Einwohner:</td><td class="c s7">-</td></tr>
  <tr><td class="c s7">Allianz:</td><td class="c s7">-</td></tr>
  </tbody></table>
  </div>';

//Felder
$size=Land::size();
for ($y=-8;$y<=8;$y++) {
  $showy=$py+$y;
  for ($x=-8;$x<=8;$x++) {
    $showx=$px+$x;

    echo'<img style="position:absolute; width:38px; height:37px; '.
    'left:'.(367+$x*19+$y*19).'; top:'.(199+$x*10-$y*10).';" ';

    $land=Land::getByXY($x+$px,$py-$y);
    if ($land===NULL) {
      $typ=(pow($px+$x,2)+abs(-$py-$y)*$size[0])%6+1;
      echo'src="'.$imgpath.'/t_no'.$typ.'.png">';
    }
    elseif ($land->get('oase')==1) {
      $bild=$land->get('typ');
      echo'src="'.$imgpath.'/o'.$bild.'.gif">';
    }
	elseif ($land->get('ww')==1) {
		echo'src="'.$imgpath.'/o99.gif">';
	}
    else {
      $dorf=Dorf::getByXY($x+$px,$py-$y);
      if ($dorf!==NULL) {
        $bild=0;
        if ($dorf->deinDorf()) $bild+=1;
        echo'src="'.$imgpath.'2/d'.
        $bild.'.gif">';
      }
      else {
        echo'src="'.$imgpath.'/t'.
        $land->get('aussehen').'.gif">';
      }
    }
  }
}

//Y-Beschriftungen
for ($y=-8;$y<=8;$y++) {
  $showy=$py+$y;
  echo'<div style="position:absolute; left:'.(216-$y*19).'px; top:'.(129+$y*10).';
    font-size:6pt; text-align:center;">'.($showy).'</div>';
}
//X-Beschriftungen
for ($x=-8;$x<=8;$x++) {
  $showx=$px+$x;
  echo'<div style="position:absolute; left:'.(213+$x*19).'px; top:'.(313+$x*10).';
    font-size:6pt; text-align:center;">'.($showx).'</div>';
}
echo'</div>';

//Pfeile um Karte zu verschieben
echo'<map name="karte">';

$x1=$px+4; $y1=$py+4; $x2=$px-4; $y2=$py-4;

//Westen
echo'<area shape=poly coords="120,145,120,185,180,155" title="nach Westen" href="?page=karte-big&x='.$x2.'&y='.$py.'">';
//Norden
echo'<area shape=poly coords="545,145,490,155,545,190" title="nach Norden" href="?page=karte-big&x='.$px.'&y='.$y2.'">';
//Osten
echo'<area shape=poly coords="550,365,500,395,555,405" title="nach Osten" href="?page=karte-big&x='.$x1.'&y='.$py.'">';
//Süden
echo'<area shape=poly coords="125,365,125,405,180,400" title="nach Süden" href="?page=karte-big&x='.$px.'&y='.$y1.'">';

//Felder
for ($y=-8;$y<=8;$y++) {
  $showy=$py+$y;
  for ($x=-8;$x<=8;$x++) {
    $showx=$px+$x;

    $posx=$x*19+$y*19;
    $posy=$x*10-$y*10;
    echo'<area target="_parent" href="?page=karte-show&x='.($px+$x).'&y='.($py-$y).'"
    	shape=poly coords="'.
      (338+$posx).','.(268+$posy).','.        //338 268
      (319+$posx).','.(279+$posy).','.
      (338+$posx).','.(290+$posy).','.
      (357+$posx).','.(279+$posy).'" ';
    $xy="'".($px+$x)."','".($py-$y)."'";

    $dorf=Dorf::getByXY($px+$x,$py-$y);
    if ($dorf!==NULL) {
      $dname=$dorf->get('name');
      $deinwohner=$dorf->get('einwohner');
      $user=$dorf->user();
      $sname=$user->get('name');
      $allyid=$user->get('ally');
      $ally='-';
      if ($allyid>0) $ally=Allianz::getByID($allyid)->get('tag');
      echo ' onmouseover="'."map('".$dname."','".$sname."',
        '".$deinwohner."','".$ally."',".$xy.")".'"
        onmouseout="'."map('','','','',".$xy.")".'">';
    }
    else {
      $ress='';
    	$land=Land::getByXY($px+$x,$py-$y);
    	if ($land!==NULL) {
	    	$gebs=$land->rohstoffGebeude();
	    	if ($gebs!==NULL) {
		    	ksort($gebs);
		    	$ress=",0,'".implode('-',$gebs)."'";
		    }
		    else {
		    	$ress=",1";
		    }
		  }
      echo ' onmouseover="x_y('.$xy.$ress.')"
        onmouseout="x_y('.$xy.')" >';
    }

  }
}



echo'</map></td></tr></table>';

echo'<div style="position:absolute; top:380px; left:100px; z-index:11;">'.
	'<form method=post action="?page=karte-big">
	<table>
	<tr>
	<td><b>x</b></td><td><input class="fm fm25" name="x" value="4" size="2" maxlength="4"></td>
  <td><b>y</b></td><td><input class="fm fm25" name="y" value="4" size="2" maxlength="4"></td>
  <td>';

ob_start();
Outputer::button('ok','ok');
$c=ob_get_contents();
ob_end_clean();
echo $c;//str_replace('img','../img',$c);
echo'</td></tr></table>
  </form></div>';
?>


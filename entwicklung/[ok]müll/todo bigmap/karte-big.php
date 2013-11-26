<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title></title><link rel=stylesheet type="text/css" href="unx.css">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
</head>
<body>

<script language="JavaScript" type="text/javascript">
function data(x,y,name,user,ally)
{
document.getElementById('x').firstChild.nodeValue = x;
document.getElementById('y').firstChild.nodeValue = y;
document.getElementById('name').firstChild.nodeValue = name;
document.getElementById('spieler').firstChild.nodeValue = user;
document.getElementById('ally').firstChild.nodeValue = ally;
}
</script>

<span style="font-size:18pt; font-family:Verdana,Arial,Helvetica,sans-serif;">

<table style="position:absolute; top:10px; left:45px; width:750px; height:420px;"><tr><td>

<?php

$px=4;$py=4;

//Karte
echo'<img usemap="#karte" src="blank.gif" border=0
    style="position:absolute; top:-50px; left:50px; width:675px; height:545px; z-index:10;">';
echo'<img src="map.gif" border=0 style="position:absolute; top:-50px; left:50px; width:675px; height:545px;">';
echo'<div>';

//Link zurück
echo'<p style="font-size:18pt; position:absolute; left:65px; top:0px;">
	<b>Karte &nbsp;&nbsp;(<span id="x">'.$px.'</span>|<span id="y">'.$py.'</span>)</b></p>';

//Info Kasten

echo'

<div id="tb" class="map_infobox" style="position:absolute; top:20px; left:610px;">
<table cellspacing="1" cellpadding="2" class="f8 map_infobox_grey"><tbody>
<tr><td align="center" colspan="2" class="c b">Details:</td></tr>
<tr><td width="45%" class="c s7">Spieler:</td><td class="c s7">-</td></tr>
<tr><td class="c s7">Einwohner:</td><td class="c s7">-</td></tr>
<tr><td class="c s7">Allianz:</td><td class="c s7">-</td></tr>
</tbody></table>
</div>
';

//Felder
for ($py=-8;$py<=8;$py++)
{
    $showy=$py+$y;
    if ($showy>400) $showy=$showy-801;
    if ($showy<-400) $showy=$showy+801;
    for ($px=-8;$px<=8;$px++)
    {
        $showx=$px+$x;
        if ($showx>400) $showx=$showx-801;
        if ($showx<-400) $showx=$showx+801;

        echo'<img style="position:absolute; width:38px; height:37px; '.
            'left:'.(367+$px*19+$py*19).'; top:'.(199+$px*10-$py*10).';" ';

        if (isset($dorf_name[$showx][$showy]))
        {
            switch($dorf_user[$showx][$showy])
            {
            case $own_name:echo'src="city-blue-t.gif">';break;
            default:
                if ($dorf_ally[$showx][$showy]==$own_allytag)
                    echo'src="city-green-t.gif">';
                elseif ($dorf_ally[$showx][$showy]=='')
                    echo'src="city-black-t.gif">';
                else
                	echo'src="city-yellow-t.gif">';
            }
        }
        else
            echo'src="terrain0.gif">';


    }
}

//Y-Beschriftungen
for ($y=-8;$y<=8;$y++) {
    $showy=$py+$y;
    echo'<div style="position:absolute; left:'.(216+$y*19).'px; top:'.(129-$y*10).';
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
echo'<area shape=poly coords="120,145,120,185,180,155" title="nach Westen" href="map.php?px='.$x2.'&py='.$y.'">';
//Norden
echo'<area shape=poly coords="545,145,490,155,545,190" title="nach Norden" href="map.php?px='.$x.'&py='.$y1.'">';
//Osten
echo'<area shape=poly coords="550,365,500,395,555,405" title="nach Osten" href="map.php?px='.$x1.'&py='.$y.'">';
//Süden
echo'<area shape=poly coords="125,365,125,405,180,400" title="nach Süden" href="map.php?px='.$x.'&py='.$y2.'">';

echo'<area shape=rect coords="10,80,75,110" title="Zurück" href="../einträge.php?kat=9">';

//Felder
for ($y=-8;$y<=8;$y++) {
    $showy=$py+$y;
    for ($x=-8;$x<=8;$x++) {
        $showx=$px+$x;

        $posx=$x*19+$y*19;
        $posy=$x*10-$y*10;
        echo'<area shape=poly coords="'.
            (338+$posx).','.(268+$posy).','.        //338 268
            (319+$posx).','.(279+$posy).','.
            (338+$posx).','.(290+$posy).','.
            (357+$posx).','.(279+$posy).'" ';
        $du=$dorf_user[$showx][$showy];
        echo'onmouseover="data('."'".$showx."','".$showy."','".$dorf_name[$showx][$showy]."',".
            "'".$dorf_user[$showx][$showy]."','".$dorf_ally[$showx][$showy]."')".'">';
    }
}
echo'</map></td></tr></table>';

echo'<div style="position:absolute; top:380px; left:100px; z-index:11;">'.
	'<form method=post action="map.php">
	<table>
	<tr>
	<td><b>x</b></td><td><input class="fm fm25" name="x" value="4" size="2" maxlength="4"></td>
  <td><b>y</b></td><td><input class="fm fm25" name="y" value="4" size="2" maxlength="4"></td>
  <td>
  <input value="ok" name="s1" src="ok.gif" height="20" type="image" width="50"></td></tr></table>
  </form></div>';
?>

</font>
</body>
</html>
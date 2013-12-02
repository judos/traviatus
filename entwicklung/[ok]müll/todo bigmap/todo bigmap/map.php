<html>
<head>
<style type="text/css">

p { margin:5px; font-family:Comic Sans MS; }
input { text-align:center; }

</style>
</head>
<body background="../GFX/back1.gif" text=black link=black alink=black vlink=black>

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

<font face="Comic Sans MS">

<table style="position:absolute; top:10px; left:45px; width:750px; height:420px; background:white;"><tr><td>

<?php



//Karte
echo'<img usemap="#karte" src="blank.gif" border=0
    style="position:absolute; top:-50px; left:50px; width:675px; height:545px; z-index:10;">';
echo'<img src="map.gif" border=0 style="position:absolute; top:-50px; left:50px; width:675px; height:545px;">';
echo'<div>';

//Link zurück
echo'<p style="position:absolute; left:65px; top:30px;"><u>Travian</u></p>';

//Info Kasten

echo'<table border=1 style="border:1px black solid; border-collapse:collapse; margin:5px; position:absolute;
    top:20px; left:610px; width:160px; font-size:8pt; font-weight:bold; text-align:center;">';
echo'<tr><td width=10><p>Karte:</p></td><td width=110><nobr><span id="x">'.($px+$x).'</span>/';
echo'<span id="y">'.($py+$y).'</span></nobr></td></tr>';
echo'<tr><td><p>Dorf:</p></td><td><span id="name">&nbsp;</span></td></tr>';
echo'<tr><td><p>Spieler:</p></td><td><span id="spieler">&nbsp;</span></td></tr>';
echo'<tr><td><p>Ally:</p></td><td><span id="ally">&nbsp;</span></td></tr>';
echo'</table>';


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
for ($py=-8;$py<=8;$py++)
{
    $showy=$py+$y;
    if ($showy>400) $showy=$showy-801;
    if ($showy<-400) $showy=$showy+801;
    echo'<div style="position:absolute; left:'.(218+$py*19).'px; top:'.(127-$py*10).';
        font-size:6pt; text-align:center;">'.($showy).'</div>';
}
//X-Beschriftungen
for ($px=-8;$px<=8;$px++)
{
    $showx=$px+$x;
    if ($showx>400) $showx=$showx-801;
    if ($showx<-400) $showx=$showx+801;
    echo'<div style="position:absolute; left:'.(212+$px*19).'px; top:'.(312+$px*10).';
        font-size:6pt; text-align:center;">'.($showx).'</div>';
}
echo'</div>';

//Pfeile um Karte zu verschieben
echo'<map name="karte">';

$x1=$x+4; $y1=$y+4; $x2=$x-4; $y2=$y-4;
if ($x1>400) $x1=$x1-801;
if ($y1>400) $y1=$y1-801;
if ($x2<-400) $x2=$x2+801;
if ($y2<-400) $y2=$y2-801;

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

        $posx=$px*19+$py*19;
        $posy=$px*10-$py*10;
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

echo'<div style="position:absolute; top:320px; left:670px; z-index:11;">'.
	'<form method=post action="map.php"><table><tr><td><p>Koords:</p></td></tr>'.
	'<tr><td><input name="px" value="'.$x.'" size=2> / <input name="py" value="'.$y.'" size=2></td></tr>'.
	'<tr><td><input type=image src="ok.gif"></td></tr></table></form></div>';
?>

</font>
</body>
</html>
<?php
if (!$execute) die('');
needed_login();
$stview=1;

$limit=20;

$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');


Outputer::statistikenMenu();

if (isset($_GET['start']))
	$start=$_GET['start'];
else
	$start=0;

?>


<table class="tbg" cellpadding="2" cellspacing="1">

<tbody><tr>
<td class="rbg" colspan="5"><a name="h2"></a>Die gr��ten D�rfer</td>
</tr>

<tr>
<td width="6%">&nbsp;</td>
<td width="35%">Dorf</td>
<td width="25%">Spieler</td>
<td width="17%">Einwohner</td>
<td width="17%">Koordinaten</td>
</tr>

<?php
$sql="SELECT * FROM `tr".ROUND_ID."_dorfer`
	ORDER BY `einwohner` DESC, `name` ASC
	LIMIT $start,$limit;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++) {
	$data=mysql_fetch_array($result);
	$c='';$c_li='';$c_re='';
	if ($data['x']==$dx and $data['y']==$dy) {
		$c='ou ';
		$c_li='li ';
		$c_re='re ';
	}
	$spieler=Spieler::getById($data['user']);
	echo'<tr>
		<td class="'.$c.$c_li.'nbr right">'.($i+$start).'.&nbsp;</td>
		<td class="'.$c.'s7"><a href="?page=karte&x='.
			$data['x'].'&y='.$data['y'].'">'.$data['name'].'</a></td>
		<td class="'.$c.'s7">'.$spieler->getLink().'</td>
		<td class="'.$c.'">'.$data['einwohner'].'</td>
		<td class="'.$c.$c_re.'">('.$data['x'].'|'.$data['y'].')</td>
		</tr>';
}

?>

</tbody></table>
<p></p>
<table class="tbg" cellpadding="0" cellspacing="1"><tbody><tr><td>
<table class="tbg" cellpadding="2" cellspacing="0">
<tbody><tr><td width="240"></td><td class="c r7">

<?php
$anz=Dorf::anzahl();

if ($start>0)
	echo'<a href="?page=statistiken-dorfer&start='.
		($start-$limit).'">� zur�ck</a> | ';
else
	echo'<span class="c b">� zur�ck</span> | ';

if ($start+$limit<$anz)
	echo'<a href="?page=statistiken-dorfer&start='.
		($start+$limit).'">weiter �</a>';
else
	echo'<span class="c b">weiter �</span>';

echo'</td></tr></tbody></table></td></tr></tbody></table>';

echo'<div>';
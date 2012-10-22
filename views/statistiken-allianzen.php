<?php
if (!$execute) die('');
needed_login();
$stview=1;

$limit=20;

$dx=$login_dorf->get('x');
$dy=$login_dorf->get('y');

Updater::spielerAllies();
Outputer::statistikenMenu();

if (isset($_GET['start']))
	$start=$_GET['start'];
else
	$start=0;

?>


<table class="tbg" cellpadding="2" cellspacing="1">

<tbody><tr>
<td class="rbg" colspan="5"><a name="h2"></a>Die größten Allianzen</td>
</tr>

<tr>
<td width="6%">&nbsp;</td>
<td width="35%">Allianz</td>
<td width="25%">Spieler</td>
<td width="17%">Ø</td>
<td width="17%">Punkte</td>
</tr>

<?php
$sql="SELECT id FROM `tr".ROUND_ID."_ally`
	ORDER BY `einwohner` DESC, `name` ASC
	LIMIT $start,$limit;";
$result=mysql_query($sql);
for ($i=1;$i<=mysql_num_rows($result);$i++) {
	$data=mysql_fetch_array($result);
	$c='';$c_li='';$c_re='';

	if ($data['id']==$login_user->get('ally')) {
		$c='ou ';
		$c_li='li ';
		$c_re='re ';
	}
	$ally=Allianz::getById($data['id']);
	if ($ally->anzMitglieder()>0)
		$schnitt=round($ally->get('einwohner')/$ally->anzMitglieder());
	else
		$schnitt=0;
	echo'<tr>
		<td class="'.$c.$c_li.'nbr right">'.($i+$start).'.&nbsp;</td>
		<td class="'.$c.'s7"><a href="?page=allianz&id='.
			$ally->get('id').'">'.$ally->get('name').'</a></td>
		<td class="'.$c.'">'.$ally->anzMitglieder().'</td>
		<td class="'.$c.'">'.$schnitt.'</td>
		<td class="'.$c.$c_re.'">'.$ally->get('einwohner').'</td>
		</tr>';
}
if (mysql_num_rows($result)==0) {
	echo'<tr><td colspan="5">Es wurden noch keine Allianzen gegründet</td>
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
		($start-$limit).'">« zurück</a> | ';
else
	echo'<span class="c b">« zurück</span> | ';

if ($start+$limit<$anz)
	echo'<a href="?page=statistiken-dorfer&start='.
		($start+$limit).'">weiter »</a>';
else
	echo'<span class="c b">weiter »</span>';

echo'</td></tr></tbody></table></td></tr></tbody></table>';


echo'<div>';
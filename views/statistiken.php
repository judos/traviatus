<?php
if (!$execute) die('');
needed_login();
$stview=1;


Outputer::statistikenMenu();

Updater::spielerAllies();

?>
<table class="tbg" cellpadding="2" cellspacing="1">
<tbody><tr>
<td class="rbg" colspan="5"><a name="h2"></a>Die größten Spieler</td>
</tr>


<tr>
<td width="6%">&nbsp;</td>
<td width="35%">Spieler</td>
<td width="25%">Bevölkerung</td>
<td width="20%">Bestes Dorf</td>
<td width="14%">Dörfer</td>
</tr>

<?php
$sql="SELECT id,einwohner,name FROM `tr".ROUND_ID."_user`
	ORDER BY `einwohner` DESC, `name` ASC;";
$result=mysql_query($sql);
$anz=mysql_num_rows($result);
for ($i=1;$i<=$anz;$i++) {
	$data=mysql_fetch_array($result);

	$sql2="SELECT einwohner FROM `tr".ROUND_ID."_dorfer`
		WHERE `user`='".$data['id']."'
		ORDER BY `einwohner` DESC
		LIMIT 0,1;";
	$result2=mysql_query($sql2);
	$data2=mysql_fetch_array($result2);

	if ($data['name']==$login_user->get('name')) {
		echo'<tr>'.
			'<td class="li ou nbr" align="right">'.$i.'.&nbsp;</td>'.
			'<td class="s7 ou"><a href="?page=spieler&name='.
				$data['name'].'">'.$data['name'].'</a></td>'.
			'<td class="ou">'.$data['einwohner'].'</td>'.
			'<td class="ou">'.$data2['einwohner'].'</td>'.
			'<td class="re ou">'.mysql_num_rows($result2).'</td>'.
			'</tr>';
	}
	else {
		echo'<tr>'.
			'<td class="nbr" align="right">'.$i.'.&nbsp;</td>'.
			'<td class="s7"><a href="?page=spieler&name='.
				$data['name'].'">'.$data['name'].'</a></td>'.
			'<td>'.$data['einwohner'].'</td>'.
			'<td>'.$data2['einwohner'].'</td>'.
			'<td>'.mysql_num_rows($result2).'</td>'.
			'</tr>';
	}
}

echo'</tbody></table>';

echo'<div>';
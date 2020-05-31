<?php
if (!$execute) die('');
needed_login();
$stview=1;

Updater::dorf($login_dorf);

if ($login_user->get('ally')>0 and @$_GET['s']==='new')
	unset($_GET['s']);



if (!isset($_GET['s'])) {
	if (!isset($_GET['id']))
		$ally=Allianz::getById($login_user->get('ally'));
	else
		$ally=Allianz::getById($_GET['id']);

	if ($ally===NULL) gotoP('dorf2');
	echo'<h1>'.$ally->get('name').'</h1>';
	if ($login_user->get('ally')==$ally->get('id'))
		Outputer::allianzMenu();


	echo'<table cellpadding="2" cellspacing="1" class="tbg"><tbody>
		<tr class="rbg"><td colspan="3">Allianz</td></tr>
		<tr>
			<td colspan="2" width="50%">Details:</td>
			<td>Beschreibung</td>
		</tr>
		<tr><td colspan="2"></td><td></td></tr>
		<tr class="s7">
			<td>Kürzel:</td>
			<td>'.$ally->get('tag').'</td>
			<td rowspan="11">'.t($ally->get('beschreibung')).'</td>
		</tr><tr class="s7">
			<td>Name:</td>
			<td>'.$ally->get('name').'</td>
		</tr><tr><td colspan="2"></td></tr><tr class="s7">
			<td>Rang:</td><td>'.$ally->rang().'.</td>
		</tr><tr class="s7">
			<td>Punkte:</td>
			<td>'.$ally->get('einwohner').'</td>
		</tr><tr class="s7">
			<td>Mitglieder:</td>
			<td>'.$ally->anzMitglieder().'</td>
		</tr><tr>
			<td colspan="2"></td>
		</tr>';
	$mitglieder=$ally->mitglieder();
	foreach($mitglieder as $spieler) {
		if ($spieler->get('ally_rang')>0) {
			$rang=$ally->getRang($spieler->get('ally_rang'));
			echo'<tr class="s7"><td>'.$rang->get('rang_name').'</td>
				<td>'.$spieler->getLink().'</td></tr>';
		}
	}
	echo'<tr><td colspan="2"></td></tr>
		<tr>
			<td colspan="2">&nbsp;'.t($ally->get('beschreibung2')).'</td>
		</tr></tbody></table><br>

		<table cellpadding="2" cellspacing="1" class="tbg"><tbody>
		<tr class="rbg">
			<td width="6%">&nbsp;</td><td width="44%">Spieler</td>
			<td>Bevölkerung</td>
			<td>Dörfer</td><td width="6%">&nbsp;</td>
		</tr>';
	$nr=1;
	$aktivitat=array(1=>'online',2=>'letzte 12h',3=>'letzte 3 Tage',
									 4=>'letzte 7 Tage',5=>'inaktiv');
	arrayObjectsSort($mitglieder,'einwohner','desc');
	foreach($mitglieder as $spieler) {
		$c='';$cl='';$cr='';
		if ($spieler->get('id')==$login_user->get('id')) {
			$c='ou ';
			$cl='li'; $cr='re';
		}
		echo'<tr><td class="'.$c.$cl.'">'.$nr.'.</td>
			<td class="s7 '.$c.'">'.$spieler->getLink().'</td>
			<td class="'.$c.'">'.$spieler->get('einwohner').'</td>
			<td class="'.$c.'">'.$spieler->anzDorfer().'</td>
			<td class="'.$c.$cr.'">
				<img src="img/un/a/b'.$spieler->onlineStatus().'.gif"
				title="'.$aktivitat[$spieler->onlineStatus()].'"></td></tr>';
			$nr++;
	}
	echo'</tbody></table>';


}


if (@$_GET['s']==='new') {
	echo'<h1><b>Allianz gründen</b></h1>
		<p class="f10">Gib bitte folgende Daten ein um die
		Allianz zu gründen:</p>

		<form method="post" action="?page=allianz&do=new">
		<table class="tbg" cellpadding="2" cellspacing="1">
		<tr class="cbg1"><td>Name</td>
			<td class="s7">
				<input type="text" class="fm" name="name" maxlength="20">
			</td></tr>
		<tr><td>Beschreibung</td>
			<td class="s7"><textarea name="besch" rows="4"></textarea>
			</td></tr>
		</table>
		<p>
		<input value="ok" src="img/de/b/s1.gif"
		border="0" height="20" type="image" width="80">
		</p>
		</form>
		';
}









?>
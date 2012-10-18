<?php

if ($login_user->get('ally')<1) {		//Spieler ist Allianzlos
	if ($stufe>1) {
		echo'<form method="post" action="?page=allianz&do=new">
			<table style="width: 60%;"
				cellpadding="2" cellspacing="1" class="tbg">
			<tbody><tr class="rbg">
				<td colspan="2">Allianz gründen</td>
			</tr>
			<tr bgcolor="#ffffff">
				<td class="s7">Kürzel:</td>
				<td class="s7">
					<input class="fm" name="tag" maxlength="8"
						style="width:40%;">
					<span class="error"></span>
				</td>
			</tr>
			<tr>
				<td class="s7">Name:</td>
				<td class="s7">
					<input class="fm" name="name" maxlength="25"
						style="width:90%;">
					<span class="error"></span>
				</td>
			</tr></tbody>
			</table>
			<p>';

		Outputer::button('s1','ok');
		echo'</form></p>';
	}
	echo'<table class="tbg" style="width: 60%;"
		cellpadding="2" cellspacing="1">
		<tbody><tr class="rbg">
		<td colspan="3">Allianz beitreten</td></tr>';
	$einladungen=$login_user->allianzEinladungen();
	if (empty($einladungen)) {
		echo'<tr><td colspan="3" class="c" align="center">
			Es liegen keine Einladungen vor</td></tr>';
	}
	else {
		foreach($einladungen as $allianz) {
			echo'<tr><td><a href="?page=allianz&id='.$allianz->get('id').'">
					'.$allianz->get('name').'</a></td>
				<td class="s7"><a href="?page=allianz&do=accept_invitation&'.
					'id='.$allianz->get('id').'">Annehmen</a></td>
				<td class="s7"><a href="?page=allianz&do=decline_invitation&'.
					'id='.$allianz->get('id').'">Ablehnen</a></td></tr>';
		}
	}
	echo'</tbody></table>';
}
else {
	$ally=Allianz::getById($login_user->get('ally'));
	if ($ally===NULL)
		x('Fehler, die Allianz konnte nicht gefunden werden');
	else {
		$rang=$ally->getRang($login_user->get('ally_rang'));
		if ($rang===NULL)
			$rangname='-';
		else
			$rangname=$rang->get('rang_name');

		echo'<table style="width: 60%;"
				cellpadding="2" cellspacing="1" class="tbg">
			<tbody><tr class="rbg">
				<td colspan="2">Allianz</td>
			</tr>
			<tr>
				<td class="s7">Kürzel:</td>
				<td class="s7">'.$ally->get('tag').'</td>
			</tr>
			<tr>
				<td class="s7">Name:</td>
				<td class="s7">'.$ally->get('name').'</td>
			</tr>
			<tr>
				<td class="s7">Dein Rang:</td>
				<td class="s7">'.$rangname.'</td>
			</tr>
			<tr>
				<td colspan="2">
				<a href="?page=allianz">&nbsp;» zur Allianz</a></td>
			</tr></tbody>
			</table>';
	}
}

?>
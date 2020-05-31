<?php

echo'<form method="post" action="?page=allianz-options&'.
	's=beschreibung_andern&do=beschreibung_andern">
	<table cellpadding="2" cellspacing="1" class="tbg"><tbody>
	<tr class="rbg"><td colspan="3">Allianz</td></tr>
	<tr>
		<td colspan="2" width="50%">Details:</td>
		<td>Beschreibung</td>
	</tr>
	<tr><td colspan="2"></td><td></td></tr>
	<tr>
		<td class="s7">Kürzel:</td>
		<td class="s7">
			<input class="fm" name="tag" maxlength="8"
				value="'.$ally->get('tag').'" style="width:50%" />
		</td>
		<td rowspan="11" border="1" style="padding:0px;">
			<textarea style="width:95%; height:300px;
				overflow-x:hidden;overflow-y:scroll;
				text-align:center;margin:0px;" name="beschreibung">'.
				$ally->get('beschreibung').'</textarea></td>
	</tr><tr>
		<td class="s7">Name:</td>
		<td class="s7"><input class="fm" name="name" maxlength="25"
			value="'.$ally->get('name').'" style="width:90%" /></td>
	</tr><tr><td colspan="2"></td></tr><tr>
		<td class="s7">Rang:</td><td>'.$ally->rang().'</td>
	</tr><tr>
		<td class="s7">Punkte:</td>
		<td>'.$ally->get('einwohner').'</td>
	</tr><tr>
		<td class="s7">Mitglieder:</td>
		<td>'.$ally->anzMitglieder().'</td>
	</tr><tr>
		<td colspan="2"></td>
	</tr>';
$mitglieder=$ally->mitglieder();
foreach($mitglieder as $spieler) {
	if ($spieler->get('ally_rang')>0) {
		$rang=$ally->getRang($spieler->get('ally_rang'));
		echo'<tr><td>'.$rang->get('rang_name').'</td>
			<td>'.$spieler->getLink().'</td></tr>';
	}
}
echo'<tr><td colspan="2"></td></tr>
	<tr>
		<td colspan="2"><textarea style="width:95%; height:150px;
				overflow-x:hidden;overflow-y:scroll;
				text-align:center;margin:0px;" name="beschreibung2">'.
				$ally->get('beschreibung2').'</textarea></td>
	</tr></tbody></table><center><p>'.@$msg.'</p>';
Outputer::button('save','s');
echo'</center></form>';
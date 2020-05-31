<?php

//Posten vergeben
echo'<form method="post" action="?page=allianz-options&'.
	's=rang_vergeben&do=rang_vergeben">
	<table cellpadding="2" cellspacing="1" class="tbg"
		style="width:70%;"><tbody>
	<tr class="rbg"><td colspan="3">Posten vergeben</td></tr>
	<tr>
		<td class="s7" style="width:75px;">Spieler:</td>
		<td><select class="fm" name="id" style="width:90%">';
$mitglieder=$ally->mitglieder();
foreach($mitglieder as $spieler) {
	$rang=$ally->getRang($spieler->get('ally_rang'));
	$r='';
	if ($rang!==NULL) $r=' ('.$rang->get('rang_name').')';
	echo'<option value="'.$spieler->get('id').'">'.
		$spieler->get('name').$r.'</option>';
}
echo'</select></td></tr>
	<tr><td class="s7">Posten:</td>
	<td><select class="fm" name="posten" style="width:90%">';
$range=$ally->alleRange();
foreach($range as $rang) {
	echo'<option value="'.$rang->get('rang_id').'">'.
		$rang->get('rang_name').'</option>';
}
echo'</select></td></tr>
	</table><p>'.(@$_GET['i']==='vergabe'?$msg:'').'</p>';
Outputer::button('save','s');
echo'</form><br>';

//Posten erstellen
echo'<form method="post" action="?page=allianz-options&'.
	's=rang_vergeben&do=rang_new">
	<table cellpadding="2" cellspacing="1" class="tbg"
		style="width:70%;"><tbody>
	<tr class="rbg"><td colspan="3">Posten erstellen</td></tr>
	<tr><td class="s7" style="width:50px;">Name:</td>
	<td><input class="fm" style="width:90%" name="name"
		maxlength="32" /></td></tr>';
$namen=AllianzRang::rechteNamen();
foreach($namen as $nr=>$name) {
	echo'<tr><td class="right"><input type="checkbox" name="r'.$nr.'">
		</td><td class="s7">'.$name.'</td></tr>';
}
echo'</table><p>'.(@$_GET['i']==='new'?$msg:'').'</p>';
Outputer::button('save','s');
echo'</form><br>';

//Posten löschen
echo'<form method="post" action="?page=allianz-options&'.
	's=rang_vergeben&do=rang_delete">
	<table cellpadding="2" cellspacing="1" class="tbg"
		style="width:70%;"><tbody>
	<tr class="rbg"><td colspan="3">Posten löschen</td></tr>
	<tr><td class="s7" style="width:75px;">Posten:</td>
	<td><select class="fm" name="posten" style="width:90%">';
$range=$ally->alleRange();
foreach($range as $rang) {
	echo'<option value="'.$rang->get('rang_id').'">'.
		$rang->get('rang_name').'</option>';
}
echo'</select></td></tr></table>
	<p>'.(@$_GET['i']==='del'?$msg:'').'</p>';
Outputer::button('delete','delete');
echo'</form>';
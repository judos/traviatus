<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();
?>


<div align="center">
	<img src="img/de/t1/anmelden.gif" width="468" height="60">
</div>
<h5><img src="img/de/t2/u05.gif" width="160"
	height="15" border="0"></h5>


<?php

if (!isset($_GET['mdo'])) {
?>
<form name="snd" method="post"
	action="?page=anmelden&mdo=register">
<p>
<table class="p1" style="width:100%" cellspacing="1"
	cellpadding="0"><tr><td>
	<table width="100%" cellspacing="1" cellpadding="0">
		<tr><td><label>Nickname:</label>
		<input class="fm fm110" type="text" name="name" value=""
			maxlength="15"> <span class="e f7"></span></td></tr>

		<tr><td><label>Email:</label>
        <input class="fm fm110" type="text" name="email" value=""
        	 maxlength="40"> <span class="e f7"></span></td></tr>

        <tr><td><label>Passwort</label>
        <input class="fm fm110" type="password" name="pw"
        	value="" maxlength="20"> <span class="e f7"></span>
	</td></tr></table>
</td></tr></table>
</p>

<p></p>

<table class="p1" style="width: 100%;" cellpadding="0"
	cellspacing="1"><tbody><tr><td>
	<table cellpadding="0" cellspacing="1"><tbody>
	<tr>
    	<td colspan="2">
        <img src="img/de/t2/u06.gif" border="0"
        	height="15" width="160">
        </td>
	</tr>

    <tr>
    	<td width="25"><input name="vid" value="1"
        	type="radio" checked></td>

        <td class="f8" width="150">Römer</td>
	</tr>

	<tr>
		<td><input name="vid" value="2" type="radio"></td>
		<td class="f8">Germanen</td>
	</tr>

	<tr>
		<td><input name="vid" value="3" type="radio"></td>
		<td class="f8">Gallier</td>
	</tr>
	</tbody></table>
</td></tr></tbody></table>

<p align="center">

<?php
if (Diverses::get('register')==1) {
	Outputer::button('s1','anm');
}
else
	echo'<p style="color:red;">'.Diverses::get('register').'</p>';
?>

</p>
</form>

<p align="center" class="f10 e">
	Jeder Spieler darf nur einen Account besitzen</p>

<?php
}
else
{

$name=$_POST['name'];
$pw=$_POST['pw'];
$email=$_POST['email'];
$volk=$_POST['vid'];


if (Diverses::get('register')==1)
{
	if (Spieler::exists($name)>0 or $pw=='' or $email=='')
	{
		if (Spieler::exists($name)>0)
			echo'<p align="center" class="f10 e">
				Dieser Name existiert schon.</p>';
		if ($pw=='')
			echo'<p align="center" class="f10 e">
				Kein Passwort eingegeben.</p>';
		if ($email=='')
			echo'<p align="center" class="f10 e">
				Keine Email Adresse angegeben.</p>';
		echo'<br><a href="?page=anmelden">zurück</a></p>';
	}
	else
	{
		$fehler=0;
		$spieler=Spieler::create($name,md5($pw),$email,$volk);
		if ($spieler===NULL)
			$fehler='Spieler konnte nicht erstellt werden.';
		if ($fehler==0) {
			$pos=Land::findFree();
			if ($pos===NULL)
				$fehler='Kein freies Land gefunden.';
		}
		if ($fehler==0) {
			$dorf=Dorf::create($pos['x'],$pos['y'],
								$spieler->get('id'),1);
			if ($dorf===NULL)
				$fehler='Startdorf konnte nicht erstellt werden.';
		}
		if ($fehler==0) {
			echo'<p align="center" class="f10 e">
				Du wurdest erfolgreich angemeldet.</p>
				<a href="?page=login">Zum login</a>';
		}
		else {
			$spieler->delete();
			echo'<p align="center" class="f10 e">
				Ein Fehler ist aufgetreten. '.$fehler.'</p>';
		}
	}
}
else
	echo'<p align="center" class="f10 e">Die Runde ist voll.</p>';

}
?>
<?php
if ($_GET['do']=='sendtroops') {
	$einheiten=$login_user->truppenTypen();
	foreach($einheiten as $id => $einheit) {
		$soldaten[$id]=$_POST['t'.$id];
	}
	
	$aktion=$_POST['aktion'];
	$x=$_POST['x'];
	$y=$_POST['y'];

	//Ress (von allem 0)
	$ress=array(0,0,0,0);
	TruppeMove::create($login_dorf,$x,$y,$login_user,$aktion,
												$soldaten,$ress);
	gotoP('build&gid=39');
}


//Neues Dorf besiedeln
if ($_GET['do']=='newvillage') {
	if ($_POST['s1']=='ok') {
		$x=$_POST['x'];
		$y=$_POST['y'];
		$volk=$login_user->get('volk');
		//Einfach 3 Siedler
		$soldaten=array($volk*10=>3);
		//Kosten bzw Ress (von allem 750)
		$ress=array_fill(0,4,750);
		TruppeMove::create($login_dorf,$x,$y,$login_user,1,
												$soldaten,$ress);
		gotoP('build&gid=39');
	}
}




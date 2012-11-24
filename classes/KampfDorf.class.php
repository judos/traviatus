<?php

class KampfDorf extends KampfSim {
	public static $save = FALSE;
	protected $dorf;

	public function KampfDorf($dorf) {
		//Dorf merken, damit sp�ter Ver�nderungen gespeichert werden k�nnen.
		$this->dorf=$dorf;

		$highest=$dorf->highest();
		//Reisdenz
		$palace=$highest[25];
		//Palast
		if ($this->palace==0) $palace=$highest[26];
		
		$wall=array();
		for ($gid=31;$gid<=33;$gid++)
			$wall[$gid]=$highest[$gid];

		//Fallen
		$fallen=$dorf->get('fallen');

		//Truppen zusammentr�llern
		$truppen = findTruppenInDorf();
		
		parent::__construct($palace,$wall,$fallen,$truppen);
	}
	
	protected function findTruppenInDorf() {
		$deffTruppen=array();
		$users=Truppe::getUsersByXY($dorf->get('x'),$dorf->get('y'));
		foreach($users as $userid) {
			$truppe=Truppe::getByXYU($dorf->get('x'),$dorf->get('y'),$userid);
			$deffTruppen[$userid]=$truppe;
		}
		return $deffTruppen;
	}

}
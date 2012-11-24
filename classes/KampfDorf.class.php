<?php

class KampfDorf extends KampfSim {
	public static $save = FALSE;
	protected $dorf;

	public function KampfDorf($dorf) {
		//Dorf merken, damit später Veränderungen gespeichert werden können.
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

		//Truppen zusammenträllern
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
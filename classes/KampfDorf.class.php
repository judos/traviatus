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
		$truppen = $this->findTruppenInDorf();
		
		parent::__construct($palace,$wall,$fallen,$truppen);
	}
	
	protected function findTruppenInDorf() {
		$deffTruppen=array();
		$dorf=$this->dorf;
		$users=Truppe::getUsersByD($dorf,0,true);
		foreach($users as $userid) {
			$truppe=Truppe::getByDU($dorf,$userid);
			$deffTruppen[$userid]=$truppe;
		}
		
		if (empty($users)) {
			$user=$dorf->user();
			$truppe=Truppe::createEntry($dorf->get('x'),$dorf->get('y'),$user->get('id'),0);
			$deffTruppen[$user->get('id')]=$truppe;
		}
		return $deffTruppen;
	}

}

?>
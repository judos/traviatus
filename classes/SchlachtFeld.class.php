<?php

class SchlachtFeld {
	public static $save=false;
	
	protected $off;
	protected $deff;
	protected $offVorher;
	protected $deffVorher;
	protected $bericht;
	protected $attackerLoot;
	
	//$off and $deff mustn't be modified!
	public function SchlachtFeld($offVorher,$deffVorher,$off,$deff) {
		$this->offVorher=$offVorher;
		$this->deffVorher=$deffVorher;
		$this->off=$off;
		$this->deff=$deff;
		$this->bericht==null;
	}
	
	public function setAttackerLoot($ress) {
		$this->attackerLoot=$ress;
	}
	
	public function someOffSurvived() {
		return $this->off->anzSoldaten()>0;
	}
	
	public function getUsers() {
		$users=array();
		$users[]=$this->off->getUser();
		foreach($this->deff as $nr=>$soldaten)
			$users[]=$soldaten->getUser();
		return $users;
	}
	
	public function getAllianzen() {
		$users=$this->getUsers();
		$allianzen=array();
		foreach($users as $user) {
			$a=$user->getAllianz();
			if ($a!=null && !in_array($a,$allianzen))
				$allianzen[]=$a;
		}
		return $allianzen;
	}
	
	public function getBericht() {
		if(!isset($this->bericht))
			$this->writeBericht();
		return $this->bericht;
	}
	
	protected function writeBericht() {
		$angreifer = saveObject($this->off->getLink(),'');
		//Bericht schreiben
		$b=new InfoMessage();
		$b->addPartTextTitle('Angreifer',$angreifer);
		$b->addPartUnitTypes($this->off->volk());
		$b->addPartUnitCount('Einheiten',$this->offVorher->soldatenId());
		$this->offVorher->entfernen($this->off->soldatenId());
		$b->addPartUnitCount('Verluste',$this->offVorher->soldatenId());
		$b->addPartUnitCount('Übrig',$this->off->soldatenId());
		if (isset($this->attackerLoot))
			$b->addPartRess('Beute',$this->attackerLoot);
		

		$t='Verteidiger';
		if (empty($this->deffVorher)) {
			$b->addPartNewTable();
			$b->addPartTextTitle($t,'Keine Truppen verteidigten das Dorf');
		}
		else foreach($this->deff as $nr => $soldaten) {
			$vorher=$this->deffVorher[$nr];
			$b->addPartNewTable();
			$b->addPartTextTitle($t,saveObject($soldaten->getLink(),''));
			$b->addPartUnitTypes($soldaten->volk());
			$b->addPartUnitCount('Einheiten',$vorher->soldatenId());
			$vorher->entfernen($soldaten->soldatenId());
			$b->addPartUnitCount('Verluste',$vorher->soldatenId());
			$b->addPartUnitCount('Übrig',$soldaten->soldatenId());
			$t='Unterst.';
		}
		
		$this->bericht = $b;
	}
	
}
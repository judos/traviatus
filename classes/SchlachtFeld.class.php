<?php

class SchlachtFeld {
	public static $save=false;
	
	protected $off;
	protected $deff;
	protected $offVorher;
	protected $deffVorher;
	protected $bericht;
	
	public function SchlachtFeld($offVorher,$deffVorher,$off,$deff) {
		$this->offVorher=$offVorher;
		$this->deffVorher=$deffVorher;
		$this->off=$off;
		$this->deff=$deff;
		$this->writeBericht();
	}
	
	public function someOffSurvived() {
		return $this->off->anzSoldaten()>0;
	}
	
	//returns array ( $tid => $count);
	public function getRemainingOff() {
		$result=array();
		$volk=$this->off['volk'];
		$einheiten = TruppenTyp::getIdsByVolk($volk);
		foreach($einheiten as $tid) {
			if (isset($this->off[$tid]) and $this->off[$tid]>0)
				$result[$tid]=$this->off[$tid];
			else
				$result[$tid]=0;
		}
		if (@$this->off['hero']==1 and $this->off['herolive']>0)
			$result['hero']=1;
		else
			$result['hero']=0;
		return $offPartySurvived;
	}
	
	public function getUsers() {
		//TODO: implement
		return array();
	}
	
	public function getBerichtBetreff() {
		//TODO: implement
		return '';
	}
	
	public function getBericht() {
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

		$t='Verteidiger';
		if (empty($this->deffVorher) or Soldaten::alleLeer($this->deffVorher)) {
			$b->addPartNewTable();
			$b->addPartTextTitle($t,'Keine Truppen verteidigten das Dorf');
		}
		else foreach($this->deffVorher as $nr => $soldaten) {
			$b->addPartNewTable();
			$b->addPartTextTitle($t,saveObject($soldaten->getLink(),''));
			$b->addPartUnitTypes($soldaten->volk());
			$b->addPartUnitCount('Einheiten',$soldaten->soldatenId());
			$soldaten->entfernen($this->deff[$nr]->soldatenId());
			$b->addPartUnitCount('Verluste',$soldaten->soldatenId());
			$b->addPartUnitCount('Übrig',$this->deff[$nr]->soldatenId());
			$t='Unterst.';
		}
		
		$this->bericht = $b;
	}
	
}
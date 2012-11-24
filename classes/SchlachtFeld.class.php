<?php

class SchlachtFeld {
	public static $save=false;
	
	protected $off;
	protected $deff;
	protected $bericht;
	
	public function SchlachtFeld($off,$deff) {
		$this->off=$off;
		$this->deff=$deff;
		$this->writeBericht();
	}
	
	public function someOffSurvived() {
		$volk=$this->off['volk'];
		$einheiten = TruppenTyp::getIdsByVolk($volk);
		$offPartySurvived=false;
		foreach($einheiten as $tid) {
			if (isset($this->off[$tid]) and $this->off[$tid]>0)
				$offPartySurvived=true;
		}
		if (@$this->off['hero']==1){
			if ($this->off['herolive']>0)
				$offPartySurvived=true;
		}
		return $offPartySurvived;
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
		//Bericht schreiben
		$b=new InfoMessage();
		$b->addPartTextTitle('Angreifer',
			$angreifer->getLink().' aus Dorf '.$angreifer_dorf->getLink());
		$b->addPartUnitTypes($angreifer->get('volk'));
		$b->addPartUnitCount('Einheiten',$off);
		$b->addPartUnitCount('Verluste',array_sub($off,$left));
		$b->addPartUnitCount('Übrig',$left);
		
		//TODO: ab hier zahlen richtig anpassen
		$deffTruppen2=$deff_dorf->getDeffTruppen();
		$t='Verteidiger';
		if (empty($deffTruppen2)){
			$b->addPartNewTable();
			$b->addPartTextTitle($t,'');
			$b->addPartUnitTypes($truppe['volk']);
			$b->addPartUnitCount('Übrig',$truppe);
		}
		foreach($deffTruppen2 as $nr => $truppe) {
			$b->addPartNewTable();
			$b->addPartTextTitle($t,'');
			$b->addPartUnitTypes($truppe['volk']);
			$b->addPartUnitCount('Übrig',$truppe);
			$t='Unterst.';
		}
		
		x($b->toHtml());
		$this->bericht = $b;
	}
	
}
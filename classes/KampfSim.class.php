<?php

class KampfSim {
	public static $save = FALSE;

	protected $wall=array();	//Schutzmauern [$gid] => $stufe
	protected $palace;			//Palast oder Residenz
	protected $fallen;			//Fallen im Dorf

	protected $deffTruppen;		//array($userid => $soldaten)
	protected $deffBoni;		//boni in percent for deff
	
	protected $truppeAngreifer; //array('volk'=>wert,tid=>anz,'hero'=>0/1,'heroboni'=>in%)
	protected $offboni;			//boni in percent for off
	protected $offWerte;		//array('infa'=>wert,'kava'=>wert,'total'=>wert)
	protected $deffAufteilung;	//array('infa'=>wert[0,1], 'kava'=>wert[0,1])
	protected $deffWerte;		//array('infa'=>wert,'kava'=>wert,'total'=>wert)
	protected $winner;			//'off','deff' oder 'none'
	protected $pointsWinner;
	protected $pointsLooser;
	protected $damageWinner;	//in %
	protected $damageLooser;	//in %
	protected $deffWipe;		// Deff Truppen Verluste in %
	protected $offWipe;			// Off Truppen Verluste in %

	public function __construct($palace,$wall,$fallen,$deffTruppen) {
		$this->palace=$palace;
		$this->wall=$wall;
		$this->fallen=$fallen;
		$this->deffTruppen=$deffTruppen;
	}

	private function calcDeffBoni() {
		$deffboni=0;

		// Schutzmauern
		foreach($this->wall as $gid => $stufe) {
			$deffboni+=GebeudeTyp::mauerSchutzBonus($gid,$stufe);
		}
		
		//Durch Held gewonnene Boni
		foreach($this->deffTruppen as $nr => $truppe) {
			$held=$truppe->held();
			if ($held!=null)
				$deffboni+=$held->deffWert();
		}

		$this->deffBoni=$deffboni;
	}
	
	//Liefert DeffTruppen zurück nach Angriff
	public function getDeffTruppen() {
		return $this->deffTruppen;
	}
	
	//Liefert die DeffBoni zurück
	public function getDeffBoni() {
		if ($this->deffBoni===null)
			$this->calcDeffBoni();
		return $this->deffBoni;
	}
	
	protected function calcOffBoni($truppeAngreifer) {
		$this->offboni=0;
		if ($truppeAngreifer->held()!=null)
			$this->offboni+=$truppeAngreifer->held()->offWert();
	}
	
	protected function calcOffValues($truppeAngreifer) {
		//Offwerte ausrechnen
		$this->offWerte=$truppeAngreifer->getOffWerte(); // ( 'infa'=>$wert, 'kava'=>$wert );
		
		$this->offWerte['total']=$this->offWerte['infa']+$this->offWerte['kava'];
		//Offboni hinzuzählen
		$this->offWerte['total']*= (100+$this->offboni)/100;
	}
	
	protected function calcDeffAufteilung() {
		//Deffaufteilung ausrechnen (wieviel prozent deffen kavallerie und wieviel deffen infanterie
		if ($this->offWerte['total']>0)
			$this->deffAufteilung=array(
				'infa'=>$this->offWerte['infa']/$this->offWerte['total'],
				'kava'=>$this->offWerte['kava']/$this->offWerte['total'] );
		else
			$this->deffAufteilung=array('infa'=>50,'kava'=>50);
	}
	
	protected function calcDeffValues() {
		//Deffwerte ausrechnen
		$this->deffWerte=array('infa'=>0,'kava'=>0);
		foreach($this->deffTruppen as $nr => $soldaten) {
			$this->deffWerte = array_add($this->deffWerte, $soldaten->getDeffWerte());
		}
		
		//Deffwerte aufteilen
		$this->deffWerte['total']=
			$this->deffAufteilung['infa']*$this->deffWerte['infa'] + 
			$this->deffAufteilung['kava']*$this->deffWerte['kava'];
		//Deffpunkte vom Palast hinzuzählen
		$this->deffWerte['total']+= 2*pow($this->palace,2);
		//Deffboni
		$this->deffWerte['total']*= (100+$this->deffBoni)/100;
	}
	
	protected function findWinner() {
		//Gewinner herausfinden
		if ($this->deffWerte['total'] > $this->offWerte['total']) {
			$this->winner = 'deff';
			$this->pointsWinner = $this->deffWerte['total'];
			$this->pointsLooser = $this->offWerte['total'];
		}
		elseif ($this->deffWerte['total'] < $this->offWerte['total']) {
			$this->winner = 'off';
			$this->pointsWinner = $this->offWerte['total'];
			$this->pointsLooser = $this->deffWerte['total'];
		}
		else {
			$this->winner='none';
			$this->pointsWinner = $this->offWerte['total'];
			$this->pointsLooser = $this->offWerte['total'];
		}
	}
	
	protected function calcDamage($ktyp) {
		//Schadensprozente des Gewinners ausrechnen
		if ($this->pointsWinner>0)
			$damage= 100*(pow($this->pointsLooser/$this->pointsWinner,3/2));
		else
			$damage=100;
		if ($ktyp==3) { //Normaler Angriff
			$this->damageWinner=$damage;
			$this->damageLooser=100;
		}
		elseif ($ktyp==4) {	//Raubzug
			//Schadensprozente berechnen, erweiterte Formel
			$this->damageWinner=100*$damage/(100+$damage);
			$this->damageLooser=100-$this->damageWinner;
		}
	}
	
	protected function calcOffAndDeffWipe() {
		if ($this->winner=='deff') {
			$this->offWipe=$this->damageLooser;
			$this->deffWipe=$this->damageWinner;
		}
		elseif ($this->winner=='off') {
			$this->deffWipe=$this->damageLooser;
			$this->offWipe=$this->damageWinner;
		}
		else { //zur Sicherheit wird hier aber niemand benachteiligt
			$this->deffWipe=$this->damageWinner;
			$this->offWipe=$this->damageWinner;
		}
	}
	
	protected function outputValues() {
		x('Offwerte:',$this->offWerte,'DeffVerteilung:',$this->deffAufteilung,
			'Deffwerte:',$this->deffWerte,'Winner:',$this->winner,
			'pointsWinner:',$this->pointsWinner,'pointsLooser:',$this->pointsLooser,
			'DamageWinner:',$this->damageWinner,'DamageLooser:',$this->damageLooser,
			'OffWipe:',$this->offWipe,'DeffWipe:',$this->deffWipe);
	}
	
	protected function killSomeDeffTroops() {
		//Deff auslöschen soviel wie ausgerechnet
		foreach($this->deffTruppen as $nr => $truppe)
			$truppe->killPercentage($this->deffWipe);
	}
	
	protected function killSomeOffTroops() {
		//Angreifer auslöschen soviel wie ausgerechnet
		$this->truppeAngreifer->killPercentage($this->offWipe);
	}

	//Simuliert einen Angriff auf das verteidigende Dorf
	//return: Ein Schlachtfeld objekt
	//$truppeAngreifer = object(Soldaten)
	//$ktyp= 3Angriff 4Raubzug
	public function attack($truppeAngreifer,$ktyp) {
		//clone objects to stored previous values for report
		$deffVorher=array();
		foreach($this->deffTruppen as $nr=>$soldaten)
			$deffVorher[$nr]= $soldaten->getRawCopy();
		$offVorher= $truppeAngreifer->getRawCopy();
		
		$this->truppeAngreifer=$truppeAngreifer;
		
		//Deffboni ausrechnen fürs Dorf
		$this->calcDeffBoni();
		
		$this->calcOffBoni($truppeAngreifer);
		$this->calcOffValues($truppeAngreifer);
		$this->calcDeffAufteilung();
		$this->calcDeffValues();
		$this->findWinner();
		$this->calcDamage($ktyp);
		$this->calcOffAndDeffWipe();
		
		$this->killSomeDeffTroops();
		$this->killSomeOffTroops();
		
		//erstellt das Schlachtfeld mit den übriggebliebenen Truppen
		return new SchlachtFeld($offVorher,$deffVorher,$this->truppeAngreifer,$this->deffTruppen);
	}
	
}

?>
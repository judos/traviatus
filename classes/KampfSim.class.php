<?php

class KampfSim {
	public static $save = FALSE;

	protected $wall=array();	//Schutzmauern [$gid] => $stufe
	protected $palace;		//Palast oder Residenz
	protected $fallen;		//Fallen im Dorf

	//TODO: refactor entry of hero to save a reference to an object
	protected $deffTruppen;	//Array [$nr] => array ('user'=>$user, 'volk'=>$volk,
							//'hero'=>0/1, 'heroboni'=>x%, $tid => $anz ...)

	protected $deffBoni;
	
	protected $truppeAngreifer; //array('volk'=>wert,tid=>anz,'hero'=>0/1,'heroboni'=>in%)
	protected $offboni;
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

	public function KampfSim($palace,$wall,$fallen,$deffTruppen) {
		$this->palace=$palace;
		$this->wall=$wall;
		$this->fallen=$fallen;
		$this->deffTruppen=$deffTruppen;
	}

	public function deffBoni() {
		if ($this->deffboni===null)
			calcDeffBoni();
		return $this->deffboni;
	}


	private function calcDeffBoni() {
		$deffboni=0;

		// Schutzmauern
		foreach($this->wall as $gid => $stufe) {
			$deffboni+=GebeudeTyp::mauerSchutzBonus($gid,$stufe);
		}

		//Durch Held gewonnene Boni
		foreach($this->deffTruppen as $nr => $truppe) {
			if (@$truppe['hero']==1)
				$deffboni+=$truppe['heroboni'];
		}

		$this->deffBoni=$deffboni;
	}
	
	//Liefert DeffTruppen zur�ck nach Angriff
	public function getDeffTruppen() {
		return $this->deffTruppen;
	}
	
	//Liefert die DeffBoni zur�ck
	public function getDeffBoni() {
		$this->calcDeffBoni();
		return $this->deffBoni;
	}
	
	protected function calcOffBoni($truppeAngreifer) {
		$this->offboni=0;
		if (@$truppeAngreifer['hero']==1)
			$this->offboni+=$truppeAngreifer['heroboni'];
	}
	
	protected function calcOffValues($truppeAngreifer) {
		//Offwerte ausrechnen
		$this->offWerte=array('infa'=>0,'kava'=>0);	// ( 'infa'=>$wert, 'kava'=>$wert );
		$volkAngreifer=$truppeAngreifer['volk'];
		$einheiten = TruppenTyp::getByVolk($volkAngreifer);
		foreach($einheiten as $tid => $einheit) {
			if (isset($truppeAngreifer[$tid]) and $truppeAngreifer[$tid]>0) {
				$werte=$einheit->werte();
				$anzahl=$truppeAngreifer[$tid];
				if ($einheit->get('typ')==2)
					$this->offWerte['kava']+= $anzahl*$werte[0];
				else
					$this->offWerte['infa']+= $anzahl*$werte[0];
			}
		}
		
		$this->offWerte['total']=$this->offWerte['infa']+$this->offWerte['kava'];
		//Offboni hinzuz�hlen
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
		$this->deffWerte=array('infa'=>0,'kava'=>0); // ( 'infa'=>$wert, 'kava'=>$wert );
		foreach($this->deffTruppen as $nr => $truppe) {
			$volk=$truppe['volk'];
			$einheiten = TruppenTyp::getByVolk($volk);
			foreach($einheiten as $tid=>$einheit) {
				$werte=$einheit->werte();
				$anzahl=$truppe[$tid];
				$this->deffWerte['infa']+= $anzahl*$werte[1];
				$this->deffWerte['kava']+= $anzahl*$werte[2];
			}
		}
		
		//Deffwerte aufteilen
		$this->deffWerte['total']=
			$this->deffAufteilung['infa']*$this->deffWerte['infa'] + 
			$this->deffAufteilung['kava']*$this->deffWerte['kava'];
		//Deffpunkte vom Palast hinzuz�hlen
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
		//Deff ausl�schen soviel wie ausgerechnet
		foreach($this->deffTruppen as $nr => &$truppe) {
			$volk=$truppe['volk'];
			$einheiten = TruppenTyp::getIdsByVolk($volk);
			foreach($einheiten as $tid) {
				$truppe[$tid] = round((100-$this->deffWipe)/100 * $truppe[$tid],0);
			}
			if (@$truppe['hero']==1)
				$truppe['herolive']= -$this->deffWipe;
		}
	}
	
	protected function killSomeOffTroops() {
		//Angreiffer ausl�schen soviel wie ausgerechnet
		$volk=$this->truppeAngreifer['volk'];
		$einheiten = TruppenTyp::getIdsByVolk($volk);
		foreach($einheiten as $tid) {
			if (isset($this->truppeAngreifer[$tid]) and $this->truppeAngreifer[$tid]>0) {
				$this->truppeAngreifer[$tid] = round((100-$this->offWipe)/100 * $this->truppeAngreifer[$tid],0);
			}
		}
		if (@$this->truppeAngreifer['hero']==1){
			$this->truppeAngreifer['herolive']= -$this->offWipe;
		}
	}

	//Simuliert einen Angriff auf das verteidigende Dorf
	//Gibt �brige Truppen des Angreifers zur�ck
	//$truppeAngreifer = array('hero'=>0/1,'heroboni'=>in%,'volk'=>nr,tid=>anz
	//$ktyp= 3Angriff 4Raubzug
	public function attack($truppeAngreifer,$ktyp) {
		$this->truppeAngreifer=$truppeAngreifer;
		
		//Deffboni ausrechnen f�rs Dorf
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
		
		//erstellt das Schlachtfeld mit den �briggebliebenen Truppen
		$sf=new SchlachtFeld($this->truppeAngreifer,$this->deffTruppen);
		
		return $sf;
	}
	
}
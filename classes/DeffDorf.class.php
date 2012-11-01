<?php

class DeffDorf {

	private $wall=array();	//Schutzmauern [$gid] => $stufe
	private $palace;				//Palast oder Residenz
	private $fallen;				//Fallen im Dorf

	private $deffTruppen;		//Array [$nr] => array ('user'=>$user, 'volk'=>$volk,
													//											'hero'=>0/1, 'heroboni'=>x%, $tid => $anz ...)

	private $deffBoni;
	private $dorf;

	public static $save = FALSE;

	public function DeffDorf() {
		$array = func_get_args();
		if (sizeof($array)==1) $this->DeffDorf1($array[0]);
		elseif (sizeof($array)==4) $this->DeffDorf4($array[0],$array[1],$array[2],$array[3]);
		else x('DeffDorf aufruf ungültig, bitte Constructor Aufruf prüfen.');

	}

	public function DeffDorf1($dorf) {
		//Dorf merken, damit später Veränderungen gespeichert werden können.
		$this->dorf=$dorf;

		$highest=$dorf->highest();
		//Reisdenz
		$this->palace=$highest[25];
		//Palast
		if ($this->palace==0) $this->palace=$highest[26];

		for ($gid=31;$gid<=33;$gid++)
			$this->wall[$gid]=$highest[$gid];

		//Fallen
		$this->fallen=$dorf->get('fallen');

		//Truppen zusammenträllern
		$nr=0;
		$this->deffTruppen=array();
		$users=Truppe::getUsersByXY($dorf->get('x'),$dorf->get('y'));
		foreach($users as $user) {
			$truppe=Truppe::getByXYU($dorf->get('x'),$dorf->get('y'),$user);
			$soldaten=$truppe->soldatenId();
			$volk=$truppe->volk();
			$this->deffTruppen[$nr]= array_merge( array('user'=>$user,'volk'=>$volk), $truppe->soldatenId() );
			if ($this->deffTruppen[$nr]['hero']==1) {
				$held=Held::getByUser($user);
				if ($held===NULL) {
					new Errorlog('DeffDorf::__construct Dorf erstellt, Held gefunden in der Truppe. Könnte ihn aber anhand der userid
						nicht ausfindig machen. userid:'.$user.', truppe:'.$truppe.', soldaten:'.$soldaten);
				}
				else {
					$this->deffTruppen[$nr]['heroboni']=$held->deffBoni();
				}
			}
			$nr++;
		}
	}

	//$wall an array with $gid => $stufe
	public function DeffDorf4($palace,$wall,$fallen,$deffTruppen) {
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
	
	//Liefert DeffTruppen zurück nach Angriff
	public function getDeffTruppen() {
		return $this->deffTruppen;
	}
	
	//Liefert die DeffBoni zurück
	public function getDeffBoni() {
		$this->calcDeffBoni();
		return $this->deffBoni;
	}

	//Simuliert einen Angriff auf das verteidigende Dorf
	//Gibt übrige Truppen des Angreifers zurück
	//$ktyp= 3Angriff 4Raubzug
	public function attack($truppeAngreifer,$ktyp) {
		$debug=false;
		
		//Deffboni ausrechnen fürs Dorf
		$this->calcDeffBoni();
		
		//Offboni ausrechnen für Angreifer
		$offboni=0;
		if (@$truppeAngreifer['hero']==1)
			$offboni+=$truppeAngreifer['heroboni'];
		
		//Offwerte ausrechnen
		$offWerte=array('infa'=>0,'kava'=>0);	// ( 'infa'=>$wert, 'kava'=>$wert );
		$volkAngreifer=$truppeAngreifer['volk'];
		$einheiten = TruppenTyp::getByVolk($volkAngreifer);
		foreach($einheiten as $tid => $einheit) {
			if (isset($truppeAngreifer[$tid]) and $truppeAngreifer[$tid]>0) {
				$werte=$einheit->werte();
				$anzahl=$truppeAngreifer[$tid];
				if ($einheit->get('typ')==2)
					$offWerte['kava']+= $anzahl*$werte[0];
				else
					$offWerte['infa']+= $anzahl*$werte[0];
			}
		}
		
		
		$offWerte['total']=$offWerte['infa']+$offWerte['kava'];
		//Offboni hinzuzählen
		$offWerte['total']*= (100+$offboni)/100;
		
		//Deffaufteilung ausrechnen (wieviel prozent deffen kavallerie und wieviel deffen infanterie
		if ($offWerte['total']>0)
			$deffVerteilung=array( 'infa'=>$offWerte['infa']/$offWerte['total'], 'kava'=>$offWerte['kava']/$offWerte['total'] );
		else
			$deffVerteilung=array('infa'=>50,'kava'=>50);
		
		//Deffwerte ausrechnen
		$deffWerte=array('infa'=>0,'kava'=>0); // ( 'infa'=>$wert, 'kava'=>$wert );
		foreach($this->deffTruppen as $nr => $truppe) {
			$volk=$truppe['volk'];
			for($tid=$volk*10-9;$tid<=$volk*10;$tid++) {
				$einheit=TruppenTyp::getById($tid);
				$werte=$einheit->werte();
				$anzahl=$truppe[$tid];
				$deffWerte['infa']+= $anzahl*$werte[1];
				$deffWerte['kava']+= $anzahl*$werte[2];
			}
		}
		
		//Deffwerte aufteilen
		$deffWerte['total']=$deffVerteilung['infa']*$deffWerte['infa'] + $deffVerteilung['kava']*$deffWerte['kava'];
		//Deffpunkte vom Palast hinzuzählen
		$deffWerte['total']+= 2*pow($this->palace,2);
		//Deffboni
		$deffWerte['total']*= (100+$this->deffBoni)/100;
		
		//Gewinner herausfinden
		if ($deffWerte['total']>$offWerte['total']) {
			$winner='deff';
			$pointsWinner=$deffWerte['total'];
			$pointsLooser=$offWerte['total'];
		}
		elseif ($deffWerte['total']<$offWerte['total']) {
			$winner='off';
			$pointsWinner=$offWerte['total'];
			$pointsLooser=$deffWerte['total'];
		}
		else {
			$winner='none';
			$pointsWinner=$offWerte['total'];
			$pointsLooser=$offWerte['total'];
		}
		
		//Schadensprozente des Gewinners ausrechnen
		if ($pointsWinner>0)
			$damage= 100*(pow($pointsLooser/$pointsWinner,3/2));
		else
			$damage=100;
		$deffWipe=0;	//Prozent der Truppen des Verteidigers die untergehen
		$offWipe=0;		//Prozent der Truppen des Angreiffers die untergehen
		
		if ($ktyp==3) {	//Normaler Angriff
			if ($winner=='deff') {
				$offWipe=100;
				$deffWipe=$damage;
			}
			elseif ($winner=='off') {
				$deffWipe=100;
				$offWipe=$damage;
			}
			else {
				$deffWipe=100;
				$offWipe=100;
			}
		}
		
		if ($ktyp==4) {	//Raubzug
			//Schadensprozente berechnen, erweiterte Formel
			$damageWinner=100*$damage/(100+$damage);
			$damageLooser=100-$damageWinner;
			if ($winner=='deff') {
				$offWipe=$damageLooser;
				$deffWipe=$damageWinner;
			}
			elseif ($winner=='off') {
				$deffWipe=$damageLooser;
				$offWipe=$damageWinner;
			}
			else {
				$deffWipe=$damageWinner;	//Sollte eigentlich beides 50% sein, 
				$offWipe=$damageWinner;		//zur Sicherheit wird hier aber niemand benachteiligt
			}
		}
		
		if ($debug)
			x('Offwerte:',$offWerte,'DeffVerteilung:',$deffVerteilung,'Deffwerte:',$deffWerte,'Winner:',$winner,
				'pointsWinner:',$pointsWinner,'pointsLooser:',$pointsLooser,
				'Damage:',$damage,'OffWipe:',$offWipe,'DeffWipe:',$deffWipe);
		
		//Deff auslöschen soviel wie ausgerechnet
		foreach($this->deffTruppen as $nr => &$truppe) {
			$volk=$truppe['volk'];
			$einheiten = TruppenTyp::getIdsByVolk($volk);
			foreach($einheiten as $tid) {
				$truppe[$tid] = round((100-$deffWipe)/100 * $truppe[$tid],0);
			}
			if (@$truppe['hero']==1)
				$truppe['herolive']= -$deffWipe;
		}
		
		//Angreiffer auslöschen soviel wie ausgerechnet
		$volk=$truppeAngreifer['volk'];
		$einheiten = TruppenTyp::getIdsByVolk($volk);
		foreach($einheiten as $tid) {
			if (isset($truppeAngreifer[$tid]) and $truppeAngreifer[$tid]>0) {
				$truppeAngreifer[$tid] = round((100-$offWipe)/100 * $truppeAngreifer[$tid],0);
			}
		}
		if (@$truppeAngreifer['hero']==1)
			$truppeAngreifer['herolive']= -$offWipe;
				
		//fertig Angriffstruppe zurückliefern, DeffTruppen sind noch gespeichert,
		// müssen separat abgefragt werden.
		return $truppeAngreifer;
	}


}
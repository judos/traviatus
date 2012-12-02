<?php

class Soldaten {
	public static $save=false;
	
	//array($tid=>$count ,'hero'=>$count);
	protected $soldaten;
	
	//int (1=römer,2=germanen,3=gallier,4=natur)
	protected $volk;
	
	//object held or null
	protected $held;
	
	protected $changed;
	
	//$volk: int(1-4)
	//$arr: array(0-9 => $anz)
	//$held: object (? extends Held)
	//$dorfHerkunft: object (? extends DorfSim)
	public function Soldaten($volk,$arr,$held) {
		$this->volk=$volk;
		$this->held=$held;
		$this->initSoldaten($arr);
		$this->changed=false;
	}
	
	public function getLink() {
		$u=$this->getUser();
		if ($u==null)
			return null;
		$result=$u->getLink();
		$result.=' aus Dorf '.$this->getHerkunft()->getLink();
		return $result;
	}
	
	public function getUser() {
		return null;
	}
	
	public function getHerkunft() {
		return null;
	}
	
	private function initSoldaten($arr){
		$this->soldaten=array();
		for($i=0;$i<10;$i++){
			$tid=($this->volk-1)*10+$i+1;
			if (isset($arr[$i]))
				$this->soldaten[$tid]=(int)$arr[$i];
			else
				$this->soldaten[$tid]=0;
		}
		if ($this->held!=null)
			$this->soldaten['hero']=1;
		else
			$this->soldaten['hero']=0;
	}
	
	public function volk() {
		return $this->volk;
	}
	
	public function anzSoldaten() {
		return array_sum($this->soldaten);
	}
	
	public function soldatenId() {
		return $this->soldaten;
	}
	
	public function soldatenIdWithoutHero() {
		$result=$this->soldaten;
		unset($result['hero']);
		return $result;
	}
	
	public function soldatenString() {
		return implode(':',self::soldatenNr($this->soldaten));
	}
	
	public static function soldatenNr($soldaten) {
		$result=array();
		foreach($soldaten as $id=>$anz) {
			if ($id=='hero')
				$i=10;
			else
				$i=($id-1)%10;
			$result[$i]=$anz;
		}
		ksort($result);
		$result[10]=$soldaten['hero'];
		return $result;
	}
	
	public function held() {
		return $this->held;
	}
	
	public function leer() {
		return $this->anzSoldaten()==0;
	}
	
	public function getDeffWerte() {
		$result=array('infa'=>0,'kava'=>0);
		foreach ($this->soldaten as $id => $anz) {
			if ($id!='hero'){
				$typ=TruppenTyp::getById($id);
				$werte=$typ->werte();
			}elseif($anz==1)
				$werte=$this->held->werte();
			else
				$werte=array(0,0,0);
			$result['infa']+=$werte[1]*$anz;
			$result['kava']+=$werte[2]*$anz;
		}
		return $result;
	}
	
	public function getOffWerte() {
		$result=array('infa'=>0,'kava'=>0);
		foreach ($this->soldaten as $id => $anz) {
			if ($id!='hero'){
				$typ=TruppenTyp::getById($id);
				$werte=$typ->werte();
			}elseif($anz==1){
				$typ=$this->held->getTruppenTyp();
				$werte=$this->held->werte();
			}
			if ($anz>0) {
				if ($typ->get('typ')==2)
					$result['kava']+=$werte[0]*$anz;
				else
					$result['infa']+=$werte[0]*$anz;
			}
		}
		return $result;
	}

	public function getVersorgung() {
		$versorgung=0;
		foreach ($this->soldaten as $id => $anz) {
			if ($id!='hero'){
				$typ=TruppenTyp::getById($id);
				$food=$typ->get('versorgung');
			}else
				$food=6;
			$versorgung+=$anz*$food;
		}
		return round($versorgung);
	}
	
	public function killPercentage($per) {
		//use reference for $anz, saves changes in array
		foreach($this->soldaten as $id => &$anz) {
			if ($id!='hero'){
				$anz -= $per/100*$anz;
				$anz = round($anz,0);
			}
			elseif ($anz==1){
				$this->held->looseHealthPercentage($per);
				if (!$this->held->isAlive()) {
					$anz=0;
					$this->held=null;
				}
			}
		}
	}
	
	public function hinzufugen($soldaten,$hero=null) {
		if(is_array($soldaten)){
			foreach($soldaten as $tid=>$anz)
				$this->soldaten[$tid]+=$anz;
			if (isset($soldaten['hero']) and $soldaten['hero']==1 and $hero==null)
				x('Held muss explizit angegeben werden falls dieser den Soldaten hinzugefügt wird');
			$this->changed=true;
		}
		elseif (is_object($soldaten) && ($soldaten instanceof Soldaten)){
			$this->hinzufugen($soldaten->soldatenId(),$soldaten->held());
		}
		else
			x('Ungültiger Typ für Parameter $soldaten');
	}
	
	public function entfernen($soldaten) {
		if(is_array($soldaten)){
			foreach($soldaten as $tid=>$anz)
				$this->soldaten[$tid]-=$anz;
			$this->changed=true;
		}
		elseif (is_object($soldaten) && ($soldaten instanceof Soldaten)){
			$this->entfernen($soldaten->soldatenId());
		}
		else
			x('Ungültiger Typ für Parameter $soldaten');
	}
	
	public function __toString() {
		$s='Soldaten: ';
		foreach($this->soldaten as $tid=>$anz) {
			if ($tid!='hero')
				$s.=$anz.' '.TruppenTyp::getById($tid)->imgSymbol().', ';
			else
				$s.=$anz.' '.Held::imgSymbol().', ';
		}
		return substr($s,0,-2);
	}
	
	public static function alleLeer($arr) {
		foreach($arr as $soldaten)
			if (!$soldaten->leer())
				return false;
		return true;
	}

}
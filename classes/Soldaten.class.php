<?php

class Soldaten {
	public static $save=false;
	
	//array($tid=>$count);
	protected $soldaten;
	
	//int (1=römer,2=germanen,3=gallier,4=natur)
	protected $volk;
	
	//object held or null
	protected $held;
	
	protected $changed;
	
	//$volk: int(1-4)
	//$arr: array(0-9 => $anz)
	//$held: object
	public function Soldaten($volk,$arr,$held) {
		$this->volk=$volk;
		$this->held=$held;
		$this->initSoldaten($arr);
		$this->changed=false;
	}
	
	private function initSoldaten($arr){
		$this->soldaten=array();
		for($i=0;$i<10;$i++){
			$tid=($this->volk-1)*10+$i+1;
			if (isset($arr[$i]))
				$this->soldaten[$tid]=$arr[$i];
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

	public function getVersorgung() {
		return TruppenTyp::getVersorgung($this->soldatenId());
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
		$s='';
		foreach($this->soldaten as $tid=>$anz)
			$s.=TruppenTyp::getById($tid)->imgSymbol().' '.$anz.', ';
		return substr($s,0,-2);
	}
	

}
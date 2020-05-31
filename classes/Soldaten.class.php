<?php
class Soldaten {
	public static $save=false;
	
	protected $herkunft;
	protected $user;
	
	//array($tid=>$count ,'hero'=>$count);
	protected $soldaten;
	
	//int (1=römer,2=germanen,3=gallier,4=natur)
	protected $volk;
	
	//object held or null
	protected $held=null;
	
	protected $changed;
	
	//$volk: int(1-4)
	//$arr: array($id => $anz, 'hero'=>$anz)
	//$held: object (? extends Held)
	protected function Soldaten($volk,$arr,$held) {
		$this->herkunft=null;
		$this->user=null;
		
		if (!isset($arr['hero'])) $arr['hero']=0;
		if ($arr['hero']!=0 and $held==null)
			x("value for hero does not match in Soldaten::newFromIds()");
		if ($arr['hero']>1)
			x("value for hero cannot be greater than 1");
		$this->volk=$volk;
		$this->held=$held;
		if (isset($arr[0]))
			x("index 0 used for soldiers, invalid!");
		$this->soldaten=$arr;
		$this->changed=false;
	}
	
	public function setHerkunft($herkunft) {
		$this->herkunft = $herkunft;
	}
	
	public function setUser($user) {
		$this->user = $user;
	}
	
	public static function newFromIds($arr,$hero=null) {
		list($volk,$arr) = self::idsToVolkIds($arr);
		return new Soldaten($volk,$arr,$hero);
	}
	
	protected static function idsToVolkIds($arr) {
		$volk=self::getVolkByIds($arr);
		$ids = TruppenTyp::getIdsByVolk($volk);
		foreach($ids as $id)
			if(!isset($arr[$id]))
				$arr[$id]=0;
		return array($volk,$arr);
	}	
	
	//$arr = array(0-9 => $anz, 'hero'=>$anz)
	//$hero: object (? extends Held)
	public static function newFromNrs($volk,$arr,$hero=null) {
		//nrsVolkToIds //TODO: implement helper function for inheritance
		$ids=array();
		foreach($arr as $nr => $anz) {
			if ($nr!='hero')
				$ids[$nr+1 + ($volk-1)*10] = $anz;
			else
				$ids[$nr] = $anz;
		}
		return new Soldaten($volk,$ids,$hero);
	}
	
	public static function newFromString($volk,$string,$hero=null) {
		return self::newFromNrs($volk,explode(':',$string),$hero);
	}
	
	public function getTragfahigkeit() {
		$tragen=0;
		foreach($this->soldaten as $tid => $anz) {
			if ($tid!='hero') {
				$typ=TruppenTyp::getById($tid);
				$tragen += $anz * $typ->get('tragen');
			}
			elseif ($anz==1) {
				if ($this->hero!=null) {
					$typ = $this->hero->getTruppenTyp();
					$faktor = Diverses::get('held_trag_faktor');
					$tragen+= $typ->get('tragen') * $faktor;
				}
			}
		}
		return $tragen;
	}
	
	//generates a raw Soldaten object which is not represented in the db
	public function getRawCopy() {
		if (!isset($this->hero))
			$this->hero=null;
		return new Soldaten($this->volk,$this->soldaten,$this->hero);
	}
	
	//returns link with url to user and url to dorf
	public function getLink() {
		$u=$this->getUser();
		if ($u==null)
			return null;
		$result=$u->getLink();
		$result.=' aus Dorf '.$this->getHerkunft()->getLink();
		return $result;
	}
	
	public function getUser() {
		return $this->user;
	}
	
	public function getHerkunft() {
		return $this->herkunft;
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
	
	//TODO: constructors to create instances from string/arrayNr/arrayIds/$_GET easier
	
	public static function soldatenNr($soldaten) {
		$result=array();
		for($i=0;$i<10;$i++)
			$result[$i]=0;
		foreach($soldaten as $id=>$anz) {
			if ($id=='hero')
				$i=10;
			else
				$i=($id-1)%10;
			$result[$i]=$anz;
		}
		ksort($result);
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
	
	//$per: double[0,100]
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
		if ($per!=0) {
			$this->changed=true;
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
			if ($tid==='hero')
				$s.=$anz.' '.Held::imgSymbol().', ';
			elseif ($tid>0) //TODO: change to else, as soon as id0 is not contained
				$s.=$anz.' '.TruppenTyp::getById($tid)->imgSymbol().', ';
		}
		return substr($s,0,-2);
	}
	
	protected static function getVolkByIds($arr) {
		foreach($arr as $id=>$anz){
			if($anz>0){
				$volk = floor(($id - 1)/10) +1;
				break;
			}
		}
		if (!isset($volk)){
			foreach($arr as $id=>$anz){
				$volk= floor(($id-1)/10) +1;
				break;
			}
		}
		if (!isset($volk)){
			x("could not find out volk by ids, no soldiers/keys in array",$arr);
		}
		return $volk;
	}
	
	public static function alleLeer($arr) {
		foreach($arr as $soldaten)
			if (!$soldaten->leer())
				return false;
		return true;
	}
}
?>
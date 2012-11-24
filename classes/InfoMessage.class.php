<?php

class InfoMessage extends Bericht {
	
	public function InfoMessage() {
		$this->data=array('text'=>'');
	}
	
	protected function addPart($type,$arr) {
		$string=$type.':'.implode(':',$arr);
		if ($this->data['text']!='')
			$this->data['text'].=chr(13);
		$this->data['text'].=$string;
	}
	
	// $text: string
	public function addPartTextOnly($text) {
		$this->addPart(parent::PART_TEXT_ONLY,array($text));
	}
	
	// $text,$title: string
	public function addPartTextTitle($text,$title) {
		$this->addPart(parent::PART_TEXT_TITLE,array($text,$title));
	}
	
	// $ress: array(0-3 => $amount: int)
	public function addPartRess($ress) {
		$this->addPart(parent::PART_RESS,$ress);
	}
	
	// $volk: int
	public function addPartUnitTypes($volk) {
		$this->addPart(parent::PART_UNIT_TYPES,array($volk));
	}
	
	// $text: string
	// $units: array(1-30 => $amount: int)
	public function addPartUnitCount($text,$units) {
		
		$u=array(0=>$text);
		//new array with indices between 1 and 10
		foreach($units as $gid => $count){
			if (is_int($gid)){
				$nr= ($gid-1) % 10;
				$u[$nr+1]=$count;
			}
		}
		//check that all indices 1-10 exist
		for($i=1;$i<=10;$i++)
			if (!isset($u[$i]))
				$u[$i]=0;
		$this->addPart(parent::PART_UNIT_COUNT,$u);
	}
	
	// $supply: int
	public function addPartSupply($supply) {
		$this->addPart(parent::PART_SUPPLY,array($supply));
	}
	
	public function addPartNewTable() {
		$this->addPart(parent::PART_NEW_TABLE,array());
	}
	
	public function sendToUsers($users_arr,$betreff,$typ) {
		foreach($users_arr as $user) {
			$this->sendTo($user,$betreff,$typ);
		}
	}
	
	// $user: string, or Spieler-object
	// $betreff: string, name des Spielers
	public function sendTo($user,$betreff,$typ) {
		if (is_object($user))
			$name=$user->get('name');
		elseif (is_string($user))
			$name=$user;
		else
			throw new Warning("$user is not an object or string as expected");
		$text=$this->toHtml();
		$sql="INSERT INTO tr".ROUND_ID."_msg
			(von,an,typ,zeit,betreff,text)
			VALUES
			('','$name','$typ','".now()."','$betreff','$text');";
		mysql_query($sql);
		$id=mysql_insert_id();
		super::loadById($id);
	}
}
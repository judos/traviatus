<?php

class SchlachtFeld {
	public static $save=false;
	
	public function SchlachtFeld() {
		//TODO: implement
	}
	
	protected function writeBericht() {
		//Bericht schreiben
		$b=new InfoMessage();
		$b->addPartTextTitle('Angreifer',
			$angreifer->getLink().' aus Dorf '.$angreifer_dorf->getLink());
		$b->addPartUnitTypes($angreifer->get('volk'));
		$b->addPartUnitCount('Einheiten',$off);
		$b->addPartUnitCount('Verluste',array_sub($off,$left));
		$b->addPartUnitCount('�brig',$left);
		
		//TODO: ab hier zahlen richtig anpassen
		$deffTruppen2=$deff_dorf->getDeffTruppen();
		$t='Verteidiger';
		if (empty($deffTruppen2)){
			$b->addPartNewTable();
			$b->addPartTextTitle($t,'');
			$b->addPartUnitTypes($truppe['volk']);
			$b->addPartUnitCount('�brig',$truppe);
		}
		foreach($deffTruppen2 as $nr => $truppe) {
			$b->addPartNewTable();
			$b->addPartTextTitle($t,'');
			$b->addPartUnitTypes($truppe['volk']);
			$b->addPartUnitCount('�brig',$truppe);
			$t='Unterst.';
		}
		
		x($b->toHtml());	
	}
	
}
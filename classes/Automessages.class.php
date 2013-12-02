<?php


class Automessages {

	public static $save=false;
	
	public static function spioBericht($start,$ziel,$offVorher,$offNachher,$deffTruppen) {
		$betreff = $start->get('name') . ' spioniert '.$ziel->get('name');
		$users = array( $start->user(), $ziel->user() );
		$allianzen = array();
		foreach($users as $u)
			if ($u->getAllianz()!=null)
				$allianzen[] = $u->getAllianz();
		
		$b = new InfoMessage();
		$b -> addPartTextTitle('Angreifer', saveObject($offVorher->getLink(),'') );
		$b -> addPartUnitTypes($offVorher->volk());
		$b -> addPartUnitCount('Einheiten',$offVorher->soldatenId());
		$offVorher -> entfernen($offNachher->soldatenId());
		$b -> addPartUnitCount('Verluste',$offVorher->soldatenId());
		$b -> addPartUnitCount('&Uuml;brig',$offNachher->soldatenId());
		
		$known=array();
		
		
		if ($offNachher->getSpaher() >= 5) $known['spaher']=true;
		else $b->addPartTextOnly('Zuwenig Späher überlebten um die gegnerische Anzahl Späher auszukundschaften');
			
		if ($offNachher->getSpaher() >= 50) $known['troops']=true;
		else $b->addPartTextOnly('Zuwenig Späher überlebten um die gesamte gegnerische Truppe auszukundschaften');
		
		$b -> addPartNewTable();
		
		foreach($deffTruppen as $nr => $truppe) {
			$b -> addPartTextTitle('Verteidiger', saveObject($truppe->getLink(),'') );
			$b -> addPartUnitTypes($truppe->volk());
			$b -> addPartUnitCountOrUnknown('Einheiten',$truppe->soldatenId(),$known);
		}
		
		//TODO: ausspionierte Sachen dem Bericht anfügen
		
		//Bericht absenden
		$b->sendToUsers($users,$betreff,Bericht::TYPE_SONSTIGE);
		$b->sendToAllianzen($allianzen,$betreff);
	}

	public static function bericht($user,$betreff,$text,$zeit) {
		if ($user->getKonfig('berichte')==1) {
			$sql2="INSERT INTO `tr".ROUND_ID."_msg`
				(`an`,`typ`,`zeit`,`betreff`,`text`)
				VALUES ('".$user->get('name')."','1','$zeit',
					'$betreff','$text');";
			$result2=mysql_query($sql2);
		}
	}

	public static function unterstutzung($user,$betreff,$text,$zeit) {
		$sql2="INSERT INTO `tr".ROUND_ID."_msg`
			(`an`,`typ`,`neu`,`zeit`,`betreff`,`text`)
			VALUES ('".$user->get('name')."','2','1','$zeit',
				'$betreff','$text');";
		$result2=mysql_query($sql2);
	}

	public static function siedlerUmgekehrt($user,$zeit) {
		$sql3="INSERT INTO `tr".ROUND_ID."_msgs`
			(`an`,`typ`,`neu`,`zeit`,`betreff`,`text`)
			VALUES ('".$user->get('name')."','4','1','".$zeit."',
				'Dorf konnte nicht gegründet werden','1".chr(13).
				"1::Ein Dorf welches Sie besiedeln wollten, wurde vorher schon besiedelt. Ihre Siedler kehren desshalb in euer Dorf zurück, ohne ein Dorf gegründet zu haben.');";
		$result3=mysql_query($sql3);
	}



}



?>
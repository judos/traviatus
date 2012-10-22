<?php


class Automessages {

	public static $save=false;


	public static function bericht($user,$betreff,$text) {
		if ($user->getKonfig('berichte')==1) {
			$sql2="INSERT INTO `tr".ROUND_ID."_msg`
				(`an`,`typ`,`zeit`,`betreff`,`text`)
				VALUES ('".$user->get('name')."','1',NOW(),
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
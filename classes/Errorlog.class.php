<?php

class Errorlog {
	
	public static $save=false;
	
	public function Errorlog($msg) {
		$url=curPageUrl();
		$sql="INSERT INTO tr".ROUND_ID."_errorlog
			(page,msg,time) VALUES ('$url','$msg',NOW() );";
		$result=mysql_query($sql);
	}
}

?>
<?php

class Diverses {

	protected static $data = array();
	public static $save=true;
	protected static $dataChanged = array();

	public static function get($att) {
		if (!isset(self::$data[$att])) {
			self::load($att);
		}
		return self::$data[$att];
	}
	
	public static function set($att,$value) {
		self::$data[$att]=$value;
		self::$dataChanged[$att]=true;
	}
	
	public static function saveAll() {
		foreach(self::$data as $id => $value) {
			if (self::$dataChanged[$id]) {
				$sql="UPDATE tr".ROUND_ID."_diverses SET value='".$value."' WHERE id='".$id."';";
				if (!mysql_query($sql))
					x(mysql_error());
			}
		}
	}

	public static function fallen($stufe_fallensteller) {
		$fallen=explode(':',self::get('fallen'));
		return $fallen[$stufe_fallensteller-1];
	}

	public static function startPos() {
		$v=self::get('start_pos');
		$w=explode(':',$v);
		$x=explode('-',$w[0]);
		$y=explode('-',$w[1]);
		return array('xmin'=>$x[0],'xmax'=>$x[1],
					'ymin'=>$y[0],'ymax'=>$y[1]);
	}

	public static function produktion() {
		return explode(':',self::get('produktion'));
	}

	public static function lagerGrosse() {
		return explode(':',self::get('lager'));
	}

	protected static function load($att) {
		$sql="SELECT value FROM tr".ROUND_ID."_diverses
			WHERE id='$att';";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)==0) return;
		$data=mysql_fetch_assoc($result);
		self::$data[$att]=$data['value'];
		self::$dataChanged[$att]=false;
	}
}
?>
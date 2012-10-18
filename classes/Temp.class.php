<?php

class Temp {
	
	public $id;
	public $var1,$var2,$var3,$var4;
	
	public static $save=false;
	private static $db_table='temp';
	
	public function Temp($var1='',$var2='',$var3='',$var4='',
											 $id=0,$insert=true) {
		$this->var1=$var1;
		$this->var2=$var2;
		$this->var3=$var3;
		$this->var4=$var4;
		$this->id=$id;
		if ($insert===true) {
			$sql="INSERT INTO tr".ROUND_ID."_".self::$db_table."
				(var1,var2,var3,var4)
				VALUES ('$var1','$var2','$var3','$var4');";
			$result=mysql_query($sql);
			$this->id=mysql_insert_id();
		}
	}
	
	public static function load($id) {
		$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
			WHERE keyid='$id';";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)==0) return NULL;
		$data=mysql_fetch_assoc($result);
		$temp=new Temp($data['var1'],$data['var2'],$data['var3'],
										$data['var4'],$id,false);
		$sql="DELETE FROM tr".ROUND_ID."_".self::$db_table."
			WHERE keyid='$id';";
		mysql_query($sql);
		return $temp;
	}
}
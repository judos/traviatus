<?php

if (isset($_GET['do']) and $_GET['do']=='update'){
	
	$ver=@db_getVersion();
	
	if ($ver['used']!==null){
		
		$sql="Show tables;";
		$result=mysql_query($sql);
		$tables=array();
		while($data=mysql_fetch_array($result))
			$tables[]=$data[0];
		
		$sql="DROP Table ";
		foreach($tables as $name)
			$sql.='`'.$name.'`, ';
		$sql=substr($sql,0,-2);
		$result=mysql_query($sql);
	}
	
	$n=$ver['current'];
	if ($n<10) $n='0'.$n;
	$sql=file_get_contents('db/traviatus '.$n.'.sql.txt');
	
	$result=sql_multiQuery($sql);
	foreach($result as $arr){
		if (trim($arr['sql'])!='')
			x($arr['error'],$arr['sql']);
	}
	
	Diverses::set('db_version',$ver['current']);
	unset($_GET['do']);
	
	global_save();
}

?>
<?php

function sql_multiQuery($sql) {
	$sql = preg_replace("%/\*(.*)\*/%Us", '', $sql);
	$sql = preg_replace("%^--(.*)\n%mU", '', $sql);
	$sql = preg_replace("%^$\n%mU", '', $sql);
	mysql_real_escape_string($sql); 
	$sql = explode(";", $sql);
	$result=array();
	$nr=0;
	foreach ($sql as $imp){
		if ($imp != '' && $imp != ' '){
			if (!mysql_query($imp)){
				$result[$nr]['error']=mysql_error();
				$result[$nr]['sql']=$imp;
			}
		}
	} 
	return $result;
}

function db_getVersion() {
	global $path;
	$ver=array();
	$ver['used']=Diverses::get('db_version');
	$arr=scandir($path.'db/');
	$cur=$arr[sizeof($arr)-1];
	$ver['current']=(int)substr($cur,10,2);
	return $ver;
}

function db_saveNewVersion() {
	$ver=db_getVersion();
	$nr=$ver['current']+1;
	if ($nr<10) $nr='0'.$nr;
	$name='traviatus '.$nr.'.sql.txt';
	backup_db_to_file('db/'.$name);
}

function backup_db_to_html() {
	return nl2br(htmlentities(backup_db()));
}

function backup_db_to_file($file) {
	return file_put_contents($file,backup_db());
}

function backup_db() {

	//Variabeln initialisieren
	$error=''; //Speichern einer Fehlermeldung
	$write_to_file=''; //Speichern des Outputs

    //Datenbank herausfinden
    $sql="SELECT DATABASE();";
    $result= @mysql_query($sql) or $error=mysql_error();
    if ($error!='') return $error;
    $data=mysql_fetch_array($result);
    $mysql_db=$data[0];

    //MySQL Version abfragen
    $sql="SELECT VERSION();";
    $result= mysql_query($sql) or $error=mysql_error();
    if ($error!='') return $error;
    $data=mysql_fetch_array($result);
    $mysql_version=$data[0];

    //User und Host herausfinden:
    $sql="SELECT USER();";
    $result= mysql_query($sql) or $error=mysql_error();
    if ($error!='') return $error;
    $data=mysql_fetch_array($result);
    $mysql_user=$data[0];

    // Inhalt vom Backup wird hier drin stehen
    $write_to_file="-- PHP SQL Dump".PHP_EOL.
		"-- Version 1.1".PHP_EOL.
		"-- Gemacht von Julian Schelker".PHP_EOL.
		"--".PHP_EOL.
		"-- User Host: ".$mysql_user.PHP_EOL.
		"-- Erstellungszeit: ".date('d. M Y \u\m H:i').PHP_EOL.
		"-- PHP-Version: ".phpversion().PHP_EOL.
		"-- MySQL-Version: ".$mysql_version.PHP_EOL.
		PHP_EOL.
		"--".PHP_EOL.
		"-- Datenbank: `".$mysql_db."`".PHP_EOL.
		"--".PHP_EOL.
		PHP_EOL.
		"-- --------------------------------------------------------".PHP_EOL.
		PHP_EOL;

    //Alle Tabellen abfragen
    $sql="SHOW TABLES;";
    $result = mysql_query($sql) or $error=mysql_error();
    if ($error!='') return $error;
    $tables=array();
    while($data=mysql_fetch_array($result)) {
        $tables[]=$data[0];
    }

    //Für jede Tabelle
    foreach($tables as $table) {

        //Kopf der Tabelle
        $sql="SHOW CREATE TABLE `".$table."`;";
        $result = mysql_query($sql) or $error=mysql_error();
    	if ($error!='') return $error;
        $data=mysql_fetch_array($result);
        $table_head_dump=$data[1];
        $write_to_file.="--".PHP_EOL."-- Tabellenstruktur für Tabelle `$table`".PHP_EOL.
        	"--".PHP_EOL.PHP_EOL.str_replace("\n",PHP_EOL,$table_head_dump).";".PHP_EOL.PHP_EOL;

        //Daten der Tabelle
		$sql = "SELECT * FROM `$table`;";
        $result = mysql_query($sql) or $error=mysql_error();
		if (mysql_num_rows($result)>0) {
			$write_to_file.="--".PHP_EOL."-- Daten für Tabelle `$table`".PHP_EOL."--".PHP_EOL.PHP_EOL;
			$write_to_file.="INSERT INTO `$table` (";
			
			if ($error!='') return $error;
			$feld_anzahl = mysql_num_fields($result);
			//Felder
			for ($i = 0;$i <$feld_anzahl;$i++){
				if ($i ==$feld_anzahl-1){
					$write_to_file.= '`'.mysql_field_name($result,$i).'`';
				} else {
					$write_to_file.= '`'.mysql_field_name($result,$i).'`, ';
				}
			}
			$write_to_file.=") VALUES ";
			//Daten selbst
			while($data=mysql_fetch_array($result)) {
				$write_to_file.=PHP_EOL."(";
				for($i=0;$i<$feld_anzahl;$i++){
					$ftyp=mysql_field_type($result,$i);
					$d=$data[$i];

					if ($d===NULL) {
						$write_to_file.='NULL';
					}
					elseif ($ftyp=='string' || $ftyp=='blob' || $ftyp=='date' || 
							$ftyp=='timestamp' || $ftyp=='datetime' || $ftyp=='time') {
						$write_to_file.="'".mysql_real_escape_string($d)."'";
					}
					elseif($ftyp=='int' || $ftyp=='real') {
						$write_to_file.=$d;
					}
					else {
						trigger_error('Unbekannter Datentyp von MySql erhalten: '.$ftyp,E_USER_WARNING);
					}
					//XXX: Find out more about other Types like bits
					if($i<$feld_anzahl-1)
						$write_to_file.=', ';
				}
				$write_to_file.='),';
			}
			//Letztes , durch ein ; ersetzen
			$write_to_file=substr($write_to_file,0,-1).";".PHP_EOL.PHP_EOL;
		}

    }
    return $write_to_file;
}
?>
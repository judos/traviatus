<?

$link = mysql_pconnect('localhost','meintb','sirius1989');
if (!$link)
    {die('Verbindung nicht m�glich : ' . mysql_error());}
if(!mysql_select_db('meintb'))
    {die('Fehler Datenbank konnte nicht ausgew�hlt werden.');}

echo'###

';

$anzahl=$_REQUEST['anz'];
var_dump($anzahl);


for ($i=1;$i<=$anzahl;$i++)
{
	$sql=$_REQUEST['sql'.$i];
	$sql=str_replace('#',"'",$sql);
	echo'i='.$i.'
sql='.$sql.'
';

	$result=mysql_query($sql);
}

?>


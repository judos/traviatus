<?php

class Outputer {

	public static $save=false;

	public static function button($name,$button,$atts='') {
		$buttonValue=$button;
		if (is_array($atts)) {
			$attsnew='';
			foreach ($atts as $field => $value) {
				if (strtolower($field)!='value')
					$attsnew.=$field.'="'.$value.'" ';
				else
					$buttonValue=$value;
			}
			$atts=$attsnew;
		}
		echo'<input value="'.$buttonValue.'" name="'.$name.'"
		  src="img/de/b/'.$button.'1.gif"
      onmousedown="btm1(\''.$name.'\',\'\',\'img/de/b/'.
                        $button.'2.gif\',1)"
      onmouseover="btm1(\''.$name.'\',\'\',\'img/de/b/'.
                        $button.'3.gif\',1)"
      onfocus="this.blur();"
      onmouseup="btm0()" onmouseout="btm0()" border="0"
      height="20" type="image" '.$atts.'>';
	}
	
	
	public static function nachrichtenMenu() {
		global $page;
		echo'<p class="txt_menue">';
		if (isset($_GET['t']))
			$aktiv=(int)$_GET['t']+1;
		else
			$aktiv=1;
		if ($page=='nachrichten-send') $aktiv=0;
		$tabs=array('Schreiben','Eingang','Gesendet');
		$links=array('-send','&t=0','&t=1');
		foreach($tabs as $nr => $name) {
			$c=' class="selected"';
			echo '<a href="?page=nachrichten'.$links[$nr].'" '.($aktiv==$nr?$c:'').'>'.$name.'</a>';
			if ($nr<sizeof($tabs)-1) echo' | ';
		}
		echo'</p>';
	}

	public static function berichteMenu() {
		echo'<p class="txt_menue">';
		if (isset($_GET['t']))
			$aktiv=(int)$_GET['t'];
		else
			$aktiv=0;
		$tabs=array('Alle','Handel','Unterstützung',
								'Angriffe','Sonstige');
		foreach($tabs as $nr => $name) {
			$c=' class="selected"';
			echo '<a href="?page=berichte&t='.$nr.'" '.($aktiv==$nr?$c:'').'>'.$name.'</a>';
			if ($nr<sizeof($tabs)-1) echo' | ';
		}
		echo'</p>';
	}

	public static function profilMenu($title=true) {
		global $page;
		if ($title) echo'<h1>Spieler Profil</h1>';
		$c='class="selected"';
		echo'<p class="txt_menue">
			<a href="?page=spieler" '.($page=='spieler'?$c:'').'>Übersicht</a> |
			<a href="?page=spieler-profil" '.($page=='spieler-profil'?$c:'').'>Profil</a> |
			<a href="?page=spieler-account" '.($page=='spieler-account'?$c:'').'>Account</a>

			</p>';
	}

	public static function allianzMenu() {
		global $page;
		$links=array(''=>'Übersicht','-forum'=>'Forum','-chat'=>'Chat',
								 '-attacks'=>'Angriffe','-news'=>'News',
								 '-options'=>'Optionen');
		echo'<p class="txt_menue">';
		foreach($links as $link => $name) {
			$c= ($page=='allianz'.$link ? 'class="selected"':'');
			echo'<a href="?page=allianz'.$link.'" '.$c.'>'.$name.'</a> ';
			if (end($links)!=$name) echo'| ';
		}
		echo'</p>';
	}

	public static function statistikenMenu() {
		global $page;
		$links=array(''=>'Spieler','-dorfer'=>'Dörfer',
								 '-allianzen'=>'Allianzen','-allgemein'=>'Allgemein');
		echo'<h1>Statistiken</h1>
			<p class="txt_menue">';
		foreach($links as $link => $name) {
			$c= ($page=='statistiken'.$link ? 'class="selected"':'');
			echo'<a href="?page=statistiken'.$link.'" '.$c.'>'.$name.'</a> ';
			if (end($links)!=$name) echo'| ';
		}
		echo'</p>';
	}

	public static function volk($id) {
		//Völker laden
		$volker=explode(':',Diverses::get('volker'));
		return $volker[$id-1];
	}

	public static function dorfAuftrage($dorfIndex) {
		global $login_dorf,$page,$timerNr;
		if($timerNr===NULL) $timerNr=1;
		//Bauaufträge anzeigen
		$auftrage=$login_dorf->bauAuftrage();
		if (!empty($auftrage)) {
			echo'<div id="lbau'.$dorfIndex.'">';				//Titel
			echo'<div class="f10 b">Bauauftrag:</div>';
			echo'<table width="100%" class="f10"><tbody>';
		}
		foreach($auftrage as $gid => $data) {
			//Löschknopf
			echo'<tr><td><a href="?page=build&do=builddel&gid='.$gid.
				'&id='.$data['id'].'">'.
				'<img src="img/un/a/del.gif" border="0" width="12"
					height="12"
				title="abbrechen"></a></td>
				<td>'.$data['name'].' (Stufe '.$data['stufe'].')</td>
				<td><span id=timer'.$timerNr.'>'.
				zeit_dauer(strtotime($data['zeit'])-time()).
					'</span> Std.</td>'.
				'<td>Fertig um '.date('H:i',strtotime($data['zeit'])).
					'</span>
				<span> Uhr</td></tr>';
			$timerNr++;
		}
		if (!empty($auftrage)) {
			echo'</tbody></table></div>';
		}
	}

	public static function dorf2Karte($beschriftung) {
		//Map für das Anwählen von Bauplätzen oder Gebäuden (inklusive Stadtmauer)
		echo'<div class="d2_x" style="z-index:50;">
			<img usemap="#map3" style="z-index:10000;" src="img/un/a/x.gif" border="0" height="450" width="540">
			<map name="map3">';
			
		$coords=array(
			19=>'116,131,116,77,191,77,191,131,154,152',
			20=>'199,106,199,52,274,52,274,106,237,127',
			21=>'259,96,259,42,334,42,334,96,297,117',
			22=>'333,109,333,55,408,55,408,109,371,130',
			23=>'390,157,390,103,465,103,465,157,428,178',
			24=>'77,169,77,115,152,115,152,169,115,190',
			25=>'160,177,160,123,235,123,235,177,198,198',
			26=>'245,159,245,105,320,105,320,159,283,180',
			27=>'400,196,400,142,475,142,475,196,438,217',
			28=>'65,239,65,185,140,185,140,239,103,260',
			29=>'192,204,192,150,267,150,267,204,230,225',
			30=>'155,229,155,175,230,175,230,229,193,250',
			31=>'405,256,405,202,480,202,480,256,443,277',
			32=>'85,278,85,224,160,224,160,278,123,299',
			33=>'230,272,230,218,305,218,305,272,268,293',
			34=>'353,291,391,271,427,291,391,312',
			35=>'158,313,158,259,233,259,233,313,196,334',
			36=>'285,324,285,270,360,270,360,324,323,345',
			37=>'143,346,143,292,218,292,218,346,181,367',
			38=>'262,356,262,302,337,302,337,356,300,377',
			39=>'333,198,366,175,379,195,381,218,367,251,351,267,326,278,313,255',
			40=>'65,121,148,56,285,34,427,66,502,150,511,269,430,389,297,426,133,406,42,288,37,190,72,196,68,255,113,338,169,379,273,394,383,366,466,287,475,191,449,133,394,91,311,67,254,65,182,82,122,113,86,152,72,196,39,190,66,122'
			);
		
		for($i=19;$i<=40;$i++){
			if(isset($coords[$i]))
				echo'<area href="?page=build&gid='.$i.'" title="'.$beschriftung[$i].'"
					coords="'.$coords[$i].'" shape="poly">';
		}
		echo'</map>
			</div>';
	}

	public static function dorf1Karte() {
		global $login_dorf;
		$gebeude1=$login_dorf->gebeude1();
		$gebeude1t=$login_dorf->gebeude1typ();
		for ($i=0;$i<18;$i++) {
			$geb_name[$i]=
				GebeudeTyp::getById($gebeude1t[$i])->get('name');
		}
		//anklickbare Felder
		echo'<map name="rx">
			<area href="?page=build&gid=1" coords="101,33,28"
				shape="circle"
				title="'.$geb_name[0].' Stufe '.$gebeude1[0].'">'.
			'<area href="?page=build&gid=2" coords="165,32,28"
				shape="circle"
				title="'.$geb_name[1].' Stufe '.$gebeude1[1].'">'.
			'<area href="?page=build&gid=3" coords="224,46,28"
				shape="circle"
				title="'.$geb_name[2].' Stufe '.$gebeude1[2].'">'.
			'<area href="?page=build&gid=4" coords="46,63,28"
				shape="circle"
				title="'.$geb_name[3].' Stufe '.$gebeude1[3].'">'.
			'<area href="?page=build&gid=5" coords="138,74,28"
				shape="circle"
				title="'.$geb_name[4].' Stufe '.$gebeude1[4].'">'.
			'<area href="?page=build&gid=6" coords="203,94,28"
				shape="circle"
				title="'.$geb_name[5].' Stufe '.$gebeude1[5].'">'.
			'<area href="?page=build&gid=7" coords="262,86,28"
				shape="circle"
				title="'.$geb_name[6].' Stufe '.$gebeude1[6].'">'.
			'<area href="?page=build&gid=8" coords="31,117,28"
				shape="circle"
				title="'.$geb_name[7].' Stufe '.$gebeude1[7].'">'.
			'<area href="?page=build&gid=9" coords="83,110,28"
				shape="circle"
				title="'.$geb_name[8].' Stufe '.$gebeude1[8].'">'.
			'<area href="?page=build&gid=10" coords="214,142,28"
				shape="circle"
				title="'.$geb_name[9].' Stufe '.$gebeude1[9].'">'.
			'<area href="?page=build&gid=11" coords="269,146,28"
				shape="circle"
				title="'.$geb_name[10].' Stufe '.$gebeude1[10].'">'.
			'<area href="?page=build&gid=12" coords="42,171,28"
				shape="circle"
				title="'.$geb_name[11].' Stufe '.$gebeude1[11].'">'.
			'<area href="?page=build&gid=13" coords="93,164,28"
				shape="circle"
				title="'.$geb_name[12].' Stufe '.$gebeude1[12].'">'.
			'<area href="?page=build&gid=14" coords="160,184,28"
				shape="circle"
				title="'.$geb_name[13].' Stufe '.$gebeude1[13].'">'.
			'<area href="?page=build&gid=15" coords="239,199,28"
				shape="circle"
				title="'.$geb_name[14].' Stufe '.$gebeude1[14].'">'.
			'<area href="?page=build&gid=16" coords="87,217,28"
				shape="circle"
				title="'.$geb_name[15].' Stufe '.$gebeude1[15].'">'.
			'<area href="?page=build&gid=17" coords="140,231,28"
				shape="circle"
				title="'.$geb_name[16].' Stufe '.$gebeude1[16].'">'.
			'<area href="?page=build&gid=18" coords="190,232,28"
				shape="circle"
				title="'.$geb_name[17].' Stufe '.$gebeude1[17].'">'.
			'<area href="?page=dorf2" coords="144,131,36" shape="circle"
				title="Dorfzentrum"></map>';
	}

	public static function links() {
		add_link('Profil','?page=spieler');
		add_link('Anleitung','#',
			array('onclick'=>'Popup(0,0); return false;'));
		add_link('Logout','?do=logout');
		if (ADMINTOOLS) {
			add_link('Admintools','?page=admintools');
			add_link('&infin; Ress','?page=admintools&do=enoughress');
		}
	}

	public static function blocks() {
		showblock('footer');
		showblock('servertime');
		showblock('menu');
	}

	public static function menu() {
		global $login_user,$menu;
		//Neue Nachrichten oder Berichte?
		$show=4;
		if ($login_user->hatNeueNachrichten()) $show-=2;
		if ($login_user->hatNeueBerichte()) $show--;
		$menu.=''.
			'<a href="?page=dorf1" accesskey="1" id="navileft" onfocus="this.blur();">'.
			'	<img id="n1" src="img/un/a/x.gif"
				title="Dorfübersicht"></a>'.
			'<a href="?page=dorf2" accesskey="2" onfocus="this.blur();"><img id="n2" src="img/un/a/x.gif"
				title="Dorfzentrum"></a>'.
			'<a href="?page=karte" accesskey="3" onfocus="this.blur();"><img id="n3" src="img/un/a/x.gif"
				title="Karte"></a>'.
			'<a href="?page=statistiken" onfocus="this.blur();">
				<img id="n4" src="img/un/a/x.gif"
				title="Statistik"></a>'.
			//Neue Nachrichten oder Berichte
			'<img id="n5" src="img/un/l/m'.$show.'.gif" usemap="#nb">'.
			'</div></div>'.
			'<map name="nb">'.
			'<area shape=rect coords="0,0,35,100" href="?page=berichte"
				title="Berichte">'.
			'<area shape=rect coords="35,0,70,100"
				href="?page=nachrichten"
				title="Nachrichten">'.
			'</map>';
	}

	public static function dorfer() {
		global $page,$dorfer_zusatz;
		global $login_user,$login_dorf;
		$dx=$login_dorf->get('x');
		$dy=$login_dorf->get('y');
		echo'<div id="lright1">
			<a href="?page=dorf3"><span class="f10 c0 s7 b">Dörfer:</span>
			</a><table class="f10">';
		$dorfer=$login_user->dorfer();
		arrayObjectsSort($dorfer,'name','asc');

		$para='';
		if ($page=='build' and isset($_GET['gid'])) {
			$geb_id=$login_dorf->gebeudeTyp($_GET['gid']);
		}
		foreach($_GET as $key => $value) {
			if ($key!='page' and $key!='do' and
					$key!='dorfx' and $key!='dorfy' and $key!='msg') {
				if ($page=='build' and $key=='gid')
					$para.='&highest='.$geb_id;
				else
					$para.='&'.$key.'='.$value;
			}
		}
		foreach($dorfer as $dorf) {

			$x=$dorf->get('x');$y=$dorf->get('y');
			$name=$dorf->get('name');
			echo'<tr><td class="nbr"><span';
			if ($x==$dx and $y==$dy) echo' class="c2"';
			echo'>&#8226;</span>&nbsp; <a href="?page='.$page.
				'&dorfx='.$x.'&dorfy='.$y.$para.'"';
			if ($x==$dx and $y==$dy) echo' class="active_vl"';
			echo'>'.$name.'</a></td><td class="left">('.$x.'|'.$y.')</td><td style="text-align:left;">';
			if ($dorfer_zusatz!='') echo'&nbsp;'.str_replace(array('$x','$y','$dname'),array($x,$y,$name),$dorfer_zusatz);
			if ($name=='Neues Dorf') echo'&nbsp;<a href="?page=spieler-profil&dorfx='.$x.'&dorfy='.$y.'">(umbenennen)</a>';
			echo'</td></tr>';
		}
		echo'</table><br>';
		Outputer::usefulLinks();
		echo'</div>';
	}

	public static function usefulLinks() {
		global $login_dorf,$login_user;
		global $page;
		echo'<a href="?page=spieler-account">
			<span class="f10 c0 s7 b">Links:</span>
			</a><table class="f10">';
		$highest=$login_dorf->highest();
		$highestG=$login_dorf->highestGid();
		
		$arr=array();
		$gebeude=array(16,19,29,20,30,21,17,37,25,26,24);
		
		//aktuell angewähltes gebäude gelb markieren
		$gid=null;
		if ($page=='build' and isset($_GET['gid']))
			$gid=$login_dorf->gebeudeTyp($_GET['gid']);
		if (isset($_GET['highest']))
			$gid=$_GET['highest'];
		if ($gid==25 or $gid==26)
			$gid=array(25,26);
		else
			$gid=array($gid);
		
		$nr=0;
		foreach($gebeude as $gebId) {
			$gebName=GebeudeTyp::getById($gebId)->get('name');
			if ($highest[$gebId]>0 and $login_user->getKonfig('geb_'.$gebId)==1){
				$arr[$nr]['page']='build&highest='.$gebId;
				$arr[$nr]['name']=ucwords($gebName);
				if ($gid!=null and in_array($gebId,$gid))
					$arr[$nr]['selected']=true;
				else
					$arr[$nr]['selected']=false;
				$nr++;
			}
		}

		if ($login_user->get('ally')>0) {
			$arr[$nr]['page']='allianz';
			$arr[$nr]['name']='Allianz';
			$arr[$nr]['selected']= ($page=='allianz');
		}
		
		foreach($arr as $values){
			$c='';
			if($values['selected'])
				$c=' class="c2"';
			echo'<tr><td class="nbr"><span'.$c.'>&#8226;</span>'.
				'&nbsp; <a href="?page='.$values['page'].'">'.
				$values['name'].'</a></td></tr>';
		}
		echo'</table>';
	}

	public static function lager() {
		global $login_dorf;
		$land_produktion=$login_dorf->produktion();
		$lager=$login_dorf->lager();
		$lager_grosse=$login_dorf->lagerGrosse();
		$menu_big=false;
		if ($lager_grosse[0]+$lager_grosse[1]>=100000) $menu_big=true;
		echo'<div id="lres0">'.
			'<table align="center" cellspacing="0" cellpadding="0">
				<tr valign="top">'.
			'<td><img class="res" src="img/un/r/1.gif"
				title="Holz"></td>'.
			'<td id="l1" title='.$land_produktion[0].'>'.floor($lager[0]).
				'/'.$lager_grosse[0].'</td>'.
			'<td class="s7"><img class="res" src="img/un/r/2.gif"
				title="Lehm"></td>'.
			'<td id="l2" title='.$land_produktion[1].'>'.floor($lager[1]).
				'/'.$lager_grosse[0].'</td>'.
			'<td class="s7"> <img class="res" src="img/un/r/3.gif"
				title="Eisen"></td>'.
			'<td id="l3" title='.$land_produktion[2].'>'.floor($lager[2]).
				'/'.$lager_grosse[0].'</td>'.
			'<td class="s7"> <img class="res" src="img/un/r/4.gif"
				title="Getreide"></td>'.
			'<td id="l4" title='.($land_produktion[3]-
														$login_dorf->versorgung()).
				'>'.floor($lager[3]).'/'.$lager_grosse[1].'</td>';
		if ($menu_big) echo'</tr><tr><td colspan="6"></td>';
		echo'<td class="s7" colspan="2">
			<img class="res" src="img/un/r/5.gif"
				title="Getreideverbrauch">'.
			'&nbsp;'.($login_dorf->versorgung()).'/'.$land_produktion[3].
			'</td></tr></table>'.
			'</div>';

	}
}

?>
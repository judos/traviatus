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
		//Stadtmauer anwählen
		echo'<map name="map2"">
			<area href="?page=build&gid=40" title="'.$beschriftung[40].'"
				coords="325,225,180" shape="circle">
			<area href="?page=build&gid=40" title="'.$beschriftung[40].'"
				coords="220,230,185" shape="circle">
			</map>';
		//Alles andere
		echo'<map name="map1">
			<area href="?page=build&gid=19" title="'.$beschriftung[19].'"
				coords="53,91,53,37,128,37,128,91,91,112" shape="poly">
			<area href="?page=build&gid=20" title="'.$beschriftung[20].'"
				coords="136,66,136,12,211,12,211,66,174,87" shape="poly">
			<area href="?page=build&gid=21" title="'.$beschriftung[21].'"
				coords="196,56,196,2,271,2,271,56,234,77" shape="poly">
			<area href="?page=build&gid=22" title="'.$beschriftung[22].'"
				coords="270,69,270,15,345,15,345,69,308,90" shape="poly">
			<area href="?page=build&gid=23" title="'.$beschriftung[23].'"
				coords="327,117,327,63,402,63,402,117,365,138" shape="poly">
			<area href="?page=build&gid=24" title="'.$beschriftung[24].'"
				coords="14,129,14,75,89,75,89,129,52,150" shape="poly">
			<area href="?page=build&gid=25" title="'.$beschriftung[25].'"
				coords="97,137,97,83,172,83,172,137,135,158" shape="poly">
			<area href="?page=build&gid=26" title="'.$beschriftung[26].'"
				coords="182,119,182,65,257,65,257,119,220,140" shape="poly">
			<area href="?page=build&gid=27" title="'.$beschriftung[27].'"
				coords="337,156,337,102,412,102,412,156,375,177"
				shape="poly">
			<area href="?page=build&gid=28" title="'.$beschriftung[28].'"
				coords="2,199,2,145,77,145,77,199,40,220" shape="poly">
			<area href="?page=build&gid=29" title="'.$beschriftung[29].'"
				coords="129,164,129,110,204,110,204,164,167,185"
				shape="poly">
			<area href="?page=build&gid=30" title="'.$beschriftung[30].'"
				coords="92,189,92,135,167,135,167,189,130,210" shape="poly">
			<area href="?page=build&gid=31" title="'.$beschriftung[31].'"
				coords="342,216,342,162,417,162,417,216,380,237"
				shape="poly">
			<area href="?page=build&gid=32" title="'.$beschriftung[32].'"
				coords="22,238,22,184,97,184,97,238,60,259" shape="poly">
			<area href="?page=build&gid=33" title="'.$beschriftung[33].'"
				coords="167,232,167,178,242,178,242,232,205,253"
				shape="poly">
			<area href="?page=build&gid=34" title="'.$beschriftung[34].'"
				coords="290,251,328,231,364,251,328,272" shape="poly">
			<area href="?page=build&gid=35" title="'.$beschriftung[35].'"
				coords="95,273,95,219,170,219,170,273,133,294" shape="poly">
			<area href="?page=build&gid=36" title="'.$beschriftung[36].'"
				coords="222,284,222,230,297,230,297,284,260,305"
				shape="poly">
			<area href="?page=build&gid=37" title="'.$beschriftung[37].'"
				coords="80,306,80,252,155,252,155,306,118,327" shape="poly">
			<area href="?page=build&gid=38" title="'.$beschriftung[38].'"
				coords="199,316,199,262,274,262,274,316,237,337"
				shape="poly">

			<area href="?page=build&gid=39" title="'.$beschriftung[39].'"
				coords="270,158,303,135,316,155,318,178,304,211,'.
				'288,227,263,238,250,215" shape="poly">
			<area href="?page=build&gid=40" title="'.$beschriftung[40].'"
				coords="312,338,347,338,377,320,406,288,421,262,'.
				'421,222,396,275,360,311" shape="poly">
			<area href="?page=build&gid=40" title="'.$beschriftung[40].'"
				coords="49,338,0,274,0,240,33,286,88,338" shape="poly">
			<area href="?page=build&gid=40" title="'.$beschriftung[40].'"
				coords="0,144,34,88,93,39,181,15,252,15,305,31,358,63,'.
				'402,106,421,151,421,93,378,47,280,0,175,0,78,28,0,92"
				shape="poly">
			</map>';
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
		hideblock('footer');
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
		$gebeude=array(16,19,20,21,17,37,25,26,24);
		
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
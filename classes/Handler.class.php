<?php


class Handler {

	protected $id;
	protected $data;

	protected $changed;

	protected static $ob_ursprung; // [$x][$y]
	protected static $ob_ziel;     // [$x][$y]
	protected static $ob_ursprung_loaded; // [$x][$y]
	protected static $ob_ziel_loaded;     // [$x][$y]

	protected static $db_key=array('keyid');
	protected static $db_table='handler';

	public static $save=true;

	public function __construct($id,$data) {
		$this->id = $id;
		$this->data=$data;
		$this->changed=false;
		$ux=$data['ursprung_x'];
		$uy=$data['ursprung_y'];
		$zx=$data['nach_x'];
		$zy=$data['nach_y'];
		if (!isset(self::$ob_ursprung[$ux][$uy]))
			self::$ob_ursprung[$ux][$uy]=array();
		if (!isset(self::$ob_ziel[$zx][$zy]))
			self::$ob_ziel[$zx][$zy]=array();

		//Händler beim Ursprung hinzufügen
		if (!arrayObjectsContains(self::$ob_ursprung[$ux][$uy],'keyid',$id))
			array_push(self::$ob_ursprung[$ux][$uy],$this);
		//Händler beim Ziel hinzufügen
		if (!arrayObjectsContains(self::$ob_ziel[$zx][$zy],'keyid',$id))
			array_push(self::$ob_ziel[$zx][$zy],$this);
	}

	public static function create($user,$from,$to,$ress) {
		if ($from->genugRess($ress)) {
			$handler=self::anzHandlerNeeded($from,$ress);
			if ($from->freieHandler()>=$handler) {
				$ux=$from->get('x');
				$uy=$from->get('y');
				$zx=$to->get('x');
				$zy=$to->get('y');
				$speed=$user->handlerSpeed();
				$weg=$from->entfernung($to);
				$dauer=$weg/$speed*3600;

				$sql="INSERT INTO `tr".ROUND_ID."_handler`
					(user,ursprung_x,ursprung_y,von_x,von_y,nach_x,nach_y,start,ziel,handler,speed,ress)
					VALUES (".$user->get('id').",$ux,$uy,$ux,$uy,$zx,$zy,'".now()."',
						'".now($dauer)."',$handler,$speed,'".implode(':',$ress)."');";
				$result=mysql_query($sql);

				$from->subRess($ress);
			}
			else
				return 'nicht genug Händler';
		}
		else
			return 'Nicht genug Ress';
	}

	public static function anzHandlerNeeded($dorf,$ress) {
		$resstot=vector4sum($ress);
		$kapa=$dorf->handlerKapazitat();
		return ceil($resstot/$kapa);
	}

	public function dump() {
		ob_start();
		var_dump($this->data);
		return ob_get_flush();
	}

	public function get($att) {
		return $this->data[$att];
	}

	public function set($att,$value) {
		$this->data[$att]=$value;
		$this->changed=true;
	}

	public function draw_transport() {
    global $login_dorf,$login_user,$timerNr;
    $dx=$login_dorf->get('x');
    $dy=$login_dorf->get('y');
    $ress=explode(':',$this->get('ress'));

    $vx=$this->get('von_x');
    $vy=$this->get('von_y');
    $ux=$this->get('ursprung_x');
    $uy=$this->get('ursprung_y');
    $nx=$this->get('nach_x');
    $ny=$this->get('nach_y');

    $username=Spieler::getById($this->get('user'))->get('name');

    if ($vx==$dx and $vy==$dy) { $zx=$nx; $zy=$ny; }
    else    { $zx=$vx; $zy=$vy; }


    echo'<table class="tbg" cellpadding="2" cellspacing="1"><tbody><tr class="cbg1">
    <td width="21%"><span class="c0">'.$username.'</span></td><td colspan="2">';
    if ($vx==$dx and $vy==$dy)
    	echo'Transport nach <a href="?page=karte-show&x='.
    		$nx.'&y='.$ny.'"><span class="c0">'.(Dorf::getByXY($nx,$ny)->get('name'));
    elseif ($ux==$dx and $uy==$dy) {
    	echo'Rückkehr aus <a href="?page=karte-show&x='.$vx.'&y='.$vy.
    		'"><span class="c0">'.(Dorf::getByXY($zx,$zy)->get('name'));
			$ress=array_fill(0,4,0);
    }
    else
    	echo'Transport von <a href="?page=karte-show&x='.$zx.'&y='.$zy.
    		'"><span class="c0">'.(Dorf::getByXY($zx,$zy)->get('name'));
    echo'</span></a></td></tr>
    	<tr><td>Ankunft</td><td><span id="timer'.$timerNr.'">'.zeit_dauer(strtotime($this->get('ziel'))-time()).
    	'</span> Std.</td><td>um '.date('H:i',strtotime($this->get('ziel'))).' Uhr</td></tr>
    	<tr class="cbg1"><td>Rohstoffe</td><td class="s7" colspan="2">';
    $x='';
    if (array_sum($ress)==0) $x='c ';
    echo'<span class="'.$x.'f10">';
    for ($r=1;$r<=4;$r++) {
    	if ($ress[$r-1]=='') $ress[$r-1]=0;
    	echo'<img class="res" src="img/un/r/'.$r.'.gif">'.$ress[$r-1];
    	if ($r<4) echo' | ';
    }
    echo'</span></td></tr></tbody></table><p></p>';
    $timerNr++;
  }

	public function save() {
		$sql="UPDATE tr".ROUND_ID."_".self::$db_table." SET ";
		foreach(self::$data[$this->id] as $key => $value) {
			$sql.="$key='$value',";
		}
		$sql=substr($sql,0,-1);
		$sql.=" WHERE ";
		foreach(self::$db_key as $att) {
			$sql.="$att='".$this->data[$att]
				."' AND ";
		}
		$sql=substr($sql,0,-4);
		mysql_query($sql);
	}

	public static function saveAll() {
		if (self::$ob_ursprung!==NULL) {
			foreach (self::$ob_ursprung as $x => $arr) {
				foreach ($arr as $y=>$arr2) {
					foreach($arr2 as $nr => $handler){
						if ($handler->changed) {
							$handler->save();
						}
					}
				}
			}
		}
	}

	public static function getByZ($dorf) {
		$x=$dorf->get('x');
		$y=$dorf->get('y');
		if (!self::$ob_ziel_loaded[$x][$y]) {
			self::loadByZ($x,$y);
		}
		return self::$ob_ziel[$x][$y];
	}

	protected static function loadByZ($x,$y) {
		if (!self::$ob_ziel_loaded[$x][$y]) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE nach_x=$x AND nach_y=$y;";
			$result=mysql_query($sql);
			while($data=mysql_fetch_assoc($result)) {
				new Handler($data['keyid'],$data);
			}
			if (!isset(self::$ob_ziel[$x][$y]))
				self::$ob_ziel[$x][$y]=array();
			self::$ob_ziel_loaded[$x][$y]=true;
		}
	}

	public static function getByU($dorf) {
		$x=$dorf->get('x');
		$y=$dorf->get('y');
		if (@!self::$ob_ursprung_loaded[$x][$y]) {
			self::loadByU($x,$y);
		}
		return self::$ob_ursprung[$x][$y];
	}

	protected static function loadByU($x,$y) {
		if (@!self::$ob_ursprung_loaded[$x][$y]) {
			$sql="SELECT * FROM tr".ROUND_ID."_".self::$db_table."
				WHERE ursprung_x=$x AND ursprung_y=$y;";
			$result=mysql_query($sql);
			while($data=mysql_fetch_assoc($result)) {
				new Handler($data['keyid'],$data);
			}
			if (!isset(self::$ob_ursprung[$x][$y]))
				self::$ob_ursprung[$x][$y]=array();
			self::$ob_ursprung_loaded[$x][$y]=true;
		}
	}
}


?>
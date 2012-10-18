
/* Initialisierung */
var timer=new Object();
var m=new Object();
var l=new Object();
var V=A();
var O=0;
var auto_reload=0;
var F=new Object();

var neuurl=0;

/* Speichert aktuelle URL */
function get_url() {
  for (var i=1;i<=document.URL.length;i++) {
    if (document.URL.charAt(i)=='&') {
      break;
    }
  }
  neuurl=document.URL.slice(0,i);
}

/*Wird bei Dokumentstart ausgeführt */
function start() {
  get_url();  /* url herausfinden */
  G("l1"); /* Produktion updaten für alle 4 Rohstoffe */
  G("l2");
  G("l3");
  G("l4");
  ak();
}

/* Liefert aktuelle Zeit in MilliSekunden seit 1.1.1970 */
function Q() {
	return new Date().getTime();
}

/* Liefert aktuelle Zeit in Sekunden seit 1.1.1970 */
function A() {
	return Math.round(Q()/1000);
}

/* Liefert Anzahl Sekunden eines HTML-Elementes aus der Form hh:mm:ss */
function Z(c) {
  p=c.innerHTML.split(":");
  aY=p[0]*3600+p[1]*60+p[2]*1;
  return aY;
}

/* Bringt Anzahl Sekunden wieder in die Form hh:mm:ss */
function aa(s) {
  if(s> -2) {
    az=Math.floor(s/3600);	// stunden
    an=Math.floor(s/60)%60;  // minuten
    af=s%60;  // sekunden
    t=az+":";
    if(an<10) {t+="0";}
    t+=an+":";
    if(af<10) {t+="0";}
    t+=af;
  }
  else {
    t="<a href=\"#\" onClick=\"Popup(2,5); return false;\"><span class=\"c0 t\">0:00:0</span>?</a>";
  }
  return t;
}

/* Sucht alle tp Id's im Dokument und Speichert sie in einem Array */
/* m[i].ad ist dabei die Referenz auf das Objekt */
/* m[i].D speichert die Anzahl Sekunden des Elementes */
/* Das gleiche gilt für alle Timer-Elemente */
function ak() {
  for(i=1;;i++) {
    c=document.getElementById("tp"+i);
    if(c!=null) {
      m[i]=new Object();
      m[i].ad=c;
      m[i].D=Z(c);
    }
    else {
      break;
    }
  }
  for(i=1;;i++) {
    c=document.getElementById("timer"+i);
    if(c!=null) {
      l[i]=new Object();
      l[i].ad=c;
      l[i].D=Z(c);
    }
    else {
      break;
    }
  }
  J();
}


/* Durchläuft alle tp-Elemente */
function J() {
	for(i in m) {
		o=A()-V; /* A() Aktuelle Zeit, V Zeit zu beginn des Dokumentladevorganges */
		/* o = Sekunden seit Dokument geladen */
		U=aa(m[i].D+o); /* Sekunden des tp-Elementes werden hochgezählt, in hh:mm:ss umgeschrieben */
		m[i].ad.innerHTML=U; /* und wieder gespeichert */
	}

	/* gleich für alle Timer */
	for(i in l) {
		o=A()-V;
		ae=l[i].D-o; /* Zählt runter statt rauf */
		if(O==0&&ae<1) {
			O=1;
//		c=document.getElementById("test");
//		c.innerHTML=neuurl;
			if(auto_reload==1){setTimeout("document.location.href=neuurl",1000);}
			else if(auto_reload==0){setTimeout("k()",1000);}
		}
		U=aa(ae); /* speichern */
		l[i].ad.innerHTML=U;
	}
	if(O==0){window.setTimeout("J()",1000);}
}

/* Lager-Element wird geparst und so updated */
function G(f) {
  c=document.getElementById(f);
  if(c!=null) {
    F[f]=new Object();
    am=c.innerHTML.split("/"); /* inhalt teilen */
    R=parseInt(am[0]); /* ress */
    K=parseInt(am[1]); /* lager */
    r=c.title; /* produktion */
    if(r!=0) {
      aq=Q(); /* aktuelle ms */
      timer[f]=new Object();
      timer[f].start=aq;
      timer[f].ar=r;
      timer[f].R=R;
      timer[f].K=K;
      timer[f].aM=3600000/r;
      H=100;
      if(timer[f].aM<H) {timer[f].aM=H;}
      timer[f].ad=c;
      P(f);
    }
  }
}

/* updaten der Lager-Elemente */
function P(f) {
  o=Q()-timer[f].start;
  if(o>=0) {
    T=Math.round(timer[f].R+o*(timer[f].ar/3600000));
    if(T>=timer[f].K) {T=timer[f].K;}
    else {window.setTimeout("P('"+f+"')",timer[f].aM);}
    F[f].value=T;
    timer[f].ad.innerHTML=T+'/'+timer[f].K;
  }
}



/* Erstes Kind-Element eines beim Namen gesuchten Elementes wird zurückgegeben */
function ai(n,d) {
	if (!d) {
		d=document;
	}
	return d.getElementsByName(n).item(0);
}

function btm0() {	//Wird bei mouseout oder mouseup aufgerufen
	var i,x,a=document.ax;
	for(i=0;a&&i<a.length&&(x=a[i])&&x.at;i++) {
		x.src=x.at;
	}
}

function btm1() {	//Wird bei mousedown oder mouseover aufgerufen
	var i,j=0,x,a=btm1.arguments;	//0:Name,1:'',2:bild,3:1
	document.ax=new Array;

	//alert(var_dump(btm1.arguments));

  for(i=0;i<(a.length-2);i+=3) {
    //alert('i='+i+', a[i]='+a[i]);
    if((x=ai(a[i]))!=null) {
      document.ax[j++]=x;
      if(!x.at) {
        x.at=x.src;
      }
      x.src=a[i+2];
    }
  }
}


/* Manual wird in einem Popup geöffnet */
function Popup(i,j) {
  c=document.getElementById("ce");
  if(c!=null) {
    var aF='<div class="popup3"><iframe frameborder="0" id="Frame" src="?page=manual&typ='+i+'&id='+j+'"" width="412" height="440" border="0"></iframe></div><a href="#" onClick="Close()"><img src="img/un/a/x.gif" border="1" class="popup4" alt="Close"></a>';
    c.innerHTML=aF;
  }
}

/* Karte wird geöffnet */
function PopupMap() {
  c=document.getElementById("ce");
  if(c!=null) {
    var aF='<div class="popupmap"><iframe scrolling="no" style="z-index:651; overflow:hidden;" frameborder="0" id="Frame" src="?page=karte-big" width="840" height="450" border="0"></iframe></div><a href="#" onClick="Close()"><img src="img/un/a/x.gif" border="1" class="popupmap_close" alt="Close"></a>';
    c.innerHTML=aF;
  }
}

/* Popup schliessen */
function Close() {
  c=document.getElementById("ce");
  if(c!=null) {c.innerHTML='';}
}

/* Alle Nachrichten selektieren */
function Allmsg() {
  for(var x=0;x<document.msg.elements.length;x++) {
    var y=document.msg.elements[x];
    if(y.name!='s10') y.checked=document.msg.s10.checked;
  }
}


/* Infobox der Karte updaten wenn man über ein Feld fährt */
/* dorfname,spielername,einwohner,allianz,x,y */
function map(aO,ao,aT,au,x,y) {
	x_y(x,y);
  c=document.getElementById("tb");
  if(c!=null) {
    if(au=='') {au='-';}
    var aR="<table cellspacing='1' cellpadding='2' class='tbg f8'><tr><td class='rbg f8' colspan='2'>"+aO+"</td></tr><tr><td width='45%' class='s7 f8'>"+text_spieler+"</td><td class='s7 f8'>"+ao+"</td></tr><tr><td class='s7 f8'>"+text_einwohner+"</td><td class='s7 f8' id='aT'>"+aT+"</td></tr><tr><td class='s7 f8'>"+text_allianz+"</td><td class='s7 f8'>"+au+"</td></tr></table>";
    var aN="<table class='f8 map_infobox_grey' cellspacing='1' cellpadding='2'><tr><td class='c b' colspan='2' align='center'>"+text_details+"</td></tr><tr><td width='45%' class='c s7'>"+text_spieler+"</td><td class='c s7'>-</td></tr><tr><td class='c s7'>"+text_einwohner+"</td><td class='c s7'>-</td></tr><tr><td class='c s7'>"+text_allianz+"</td><td class='c s7'>-</td></tr></table>";
    if(ao!='') {c.innerHTML=aR;}
    else{c.innerHTML=aN;}
  }
}

/* infobox der Karte updaten wenn man über leeres Land fährt */
function x_y(x,y,oase,verteilung) {
  document.getElementById('x').firstChild.nodeValue=x;
  document.getElementById('y').firstChild.nodeValue=y;
  c=document.getElementById("tb");
 	if(c!=null) {
 		var te;
 		if (oase==0) te='Verlassenes Tal ('+verteilung+')';
 		else te='Oase';
 		
  	var aR="<table cellspacing='1' cellpadding='2' class='tbg f8'><tr><td class='f8' colspan='2' align='center'>"+te+"</td></tr><tr><td width='45%' class='c s7 f8'>"+text_spieler+"</td><td class='c s7 f8'>-</td></tr><tr><td class='c s7 f8'>"+text_einwohner+"</td><td class='c s7 f8'>-</td></tr><tr><td class='c s7 f8'>"+text_allianz+"</td><td class='c s7 f8'>-</td></tr></table>";
		var aN="<table class='f8 map_infobox_grey' cellspacing='1' cellpadding='2'><tr><td class='c b' colspan='2' align='center'>"+text_details+"</td></tr><tr><td width='45%' class='c s7'>"+text_spieler+"</td><td class='c s7'>-</td></tr><tr><td class='c s7'>"+text_einwohner+"</td><td class='c s7'>-</td></tr><tr><td class='c s7'>"+text_allianz+"</td><td class='c s7'>-</td></tr></table>";
    if(oase!=null) {c.innerHTML=aR;}
    else{c.innerHTML=aN;}
  }
}


/* Testen ob Elemente nach IDs selektiert werden können */
var aS=document.getElementById?1:0;
/* ? */
var bd=document.all?1:0;
/* User Agent, testen ob Mac */
var ba=(navigator.userAgent.indexOf("Mac")> -1)?1:0;
/* offscreenBuffering */
var ac=(bd&&(!ba)&&(typeof(window.offscreenBuffering)!='undefined'))?1:0;
var aJ=ac;
var bc=ac&&(window.navigator.userAgent.indexOf("SV1")!= -1);


/* Sichtbarkeit eines Elementes verändern */
function changeOpacity(aL,opacity) {
  if(ac) {aL.style.filter='progid:DXImageTransform.Microsoft.Alpha(opacity='+(opacity*100)+')';}
  else if(aS) {aL.style.MozOpacity=opacity;}
}

/* g initialisieren */
var g=false;


/* url ersetzen und so reload bewirken */
function k() {
	param='';
	url=window.location.href;
	if(url.indexOf(param)== -1) {
		if(url.indexOf('?')== -1) {url+='?'+param;}
		else {url+='&'+param;}
	}
	document.location.href=url;
}


/* gibt eine Variable für javascript aus */
function var_dump(obj) {
   if(typeof obj == "object") {
      return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
   } else {
      return "Type: "+typeof(obj)+"\nValue: "+obj;
   }
}


/* element nach id selektieren */
function dgei(id) {
	return document.getElementById(id);
}


/* Marktplatz maximale Menge von einem Rohstoff abfüllen */
function max_res(id) {
  var ref=dgei('r'+id);
  var maxLager=parseInt(dgei('l'+id).innerHTML);
  var i;
  if (carry*haendler<maxLager) {
  	for (i=1;i<=4;i++) {
	  	dgei('r'+i).value=0;
	  }
	}
  ref.value=maxLager;
  max_res_calc();
}
/* Prüfen ob eingabe gültig ist */
function check_res(ref) {
	if (ref.value!='') {
		ref.value=parseInt(ref.value);
		if (ref.value=='NaN') ref.value='';
	}
}
/* Einen weiteren Händler voll mit diesem Rohstoff beladen */
function add_res(id) {
  var ref=dgei('r'+id);
  if (ref.value=='') ref.value=0;
  ref.value=parseInt(ref.value)+carry;
  max_res_calc();
}
function max_res_calc() {
  var max=carry*haendler;
  var sum=0;
  var i;
  var x;
  var acthandler;
  var newsum=0;
  for (i=1;i<=4;i++) {
    x=dgei('r'+i).value;
    if (x=='') {
      x=0;
    }
    sum+=parseInt(x);
  }
  //alert(sum+" "+max);
  if (sum>max) {
    var faktor=sum/max;
    for (i=1;i<=4;i++) {
      if (dgei('r'+i).value!='') {
        dgei('r'+i).value=Math.floor(dgei('r'+i).value/faktor);
        newsum+=parseInt(dgei('r'+i).value);
      }
    }
  }
  else { newsum=sum; }
  acthandler=Math.ceil(newsum/carry);
  dgei('handler').innerHTML=acthandler;
}


//Im Dorf2 Level anzeige an-/ausschalten
function d2show_lvl_toggle() {
	d2show_lvl=1-d2show_lvl;
	if (d2show_lvl==1) {
		dgei('d2_lvl').style.visibility='';
		dgei('d2show_lvl_button').className='on';
	}
	else {
		dgei('d2_lvl').style.visibility='hidden';
		dgei('d2show_lvl_button').className='off';
	}
	var req=new_ajax_request('ajax/dorf2-showlvl.php',new Array('show'),new Array(''+d2show_lvl));

}
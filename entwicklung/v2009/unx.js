var timer=new Object();
var m=new Object();
var l=new Object();
var V=A();
var O=0;
var auto_reload=1;
var F=new Object();

var neuurl=0;

//var testvar=new Object();

function start()
{
test();

G("l1");
G("l2");
G("l3");
G("l4");
ak();
};


function test()
{
for (var i=1;i<=document.URL.length;i++)
	if (document.URL.charAt(i)=='&') break;
neuurl=document.URL.slice(0,i);
};


function Q()
{
return new Date().getTime();
};

function A()
{
return Math.round(Q()/1000);
};

function Z(c)
{
p=c.innerHTML.split(":");
aY=p[0]*3600+p[1]*60+p[2]*1;
return aY;
};

function aa(s)
{
if(s> -2)
{
	az=Math.floor(s/3600);
	an=Math.floor(s/60)%60;
	af=s%60;
	t=az+":";
	if(an<10) {t+="0";}
	t+=an+":";
	if(af<10) {t+="0";}
	t+=af;
}
else
{
	t="<a href=\"#\" onClick=\"Popup(2,5); return false;\"><span class=\"c0 t\">0:00:0</span>?</a>";
}
return t;
};

function ak()
{
for(i=1;;i++)
{
	c=document.getElementById("tp"+i);
	if(c!=null)
	{
		m[i]=new Object();
		m[i].ad=c
		m[i].D=Z(c);
	}
	else {break;}
}
for(i=1;;i++)
{
	c=document.getElementById("timer"+i);
	if(c!=null)
	{
		l[i]=new Object();
		l[i].ad=c;
		l[i].D=Z(c);
	}
	else {break;}
}
J();
};

function J()
{
	for(i in m)
	{
		o=A()-V;
		U=aa(m[i].D+o);
		m[i].ad.innerHTML=U;
	}
	for(i in l)
	{
		o=A()-V;
		ae=l[i].D-o;
		if(O==0&&ae<1)
		{
			O=1;
//			c=document.getElementById("test");
//			c.innerHTML=neuurl;

			if(auto_reload==1){setTimeout("document.location.href=neuurl",1000);}
			else if(auto_reload==0){setTimeout("k()",1000);}
		}
		else{}
		U=aa(ae);
		l[i].ad.innerHTML=U;
	}
	if(O==0){window.setTimeout("J()",1000);}
};

function G(f)
{
c=document.getElementById(f);
if(c!=null)
{
	F[f]=new Object();
	am=c.innerHTML.split("/");
	R=parseInt(am[0]);
	K=parseInt(am[1]);
	r=c.title;
	if(r!=0)
	{
		aq=Q();
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
};

function P(f)
{
o=Q()-timer[f].start;
if(o>=0)
{
	T=Math.round(timer[f].R+o*(timer[f].ar/3600000));
	if(T>=timer[f].K) {T=timer[f].K;}
	else {window.setTimeout("P('"+f+"')",timer[f].aM);}
	F[f].value=T;
	timer[f].ad.innerHTML=T+'/'+timer[f].K;
}
};

var v=new Array(0,0,0,0,0);

function add_res(B)
{
C=F['l'+(5-B)].value;I=haendler*carry;
v[B]=ap(v[B],C,I,carry);
document.getElementById('r'+B).value=v[B];
};

function upd_res(B,max)
{
C=F['l'+(5-B)].value;I=haendler*carry;
if(max) {L=C;}
else {L=parseInt(document.getElementById('r'+B).value);}
if(isNaN(L)) {L=0;}
v[B]=ap(parseInt(L),C,I,0);
document.getElementById('r'+B).value=v[B];
};

function ap(aj,X,M,aC)
{
ab=aj+aC;
if(ab>X) {ab=X;}
if(ab>M) {ab=M;}
if(ab==0) {ab='';}
return ab;
};

function ai(n,d)
{
var p,i,x;
if(!d) d=document;
if((p=n.indexOf("?"))>0&&parent.frames.length)
{
	d=parent.frames[n.substring(p+1)].document;
	n=n.substring(0,p);
}
if(!(x=d[n])&&d.all)  x=d.all[n];
for(i=0;!x&&i<d.forms.length;i++)
	x=d.forms[i][n];
for(i=0;!x&&d.layers&&i<d.layers.length;i++)
	x=ai(n,d.layers[i].document);
return x;
};

function btm0()
{
var i,x,a=document.ax;
for(i=0;a&&i<a.length&&(x=a[i])&&x.at;i++)
	x.src=x.at;
};

function btm1()
{
var i,j=0,x,a=btm1.arguments;document.ax=new Array;
for(i=0;i<(a.length-2);i+=3)
	if((x=ai(a[i]))!=null)
	{
		document.ax[j++]=x;
		if(!x.at)x.at=x.src;
		x.src=a[i+2];
	}
};

function Popup(i,j)
{
c=document.getElementById("ce");
if(c!=null)
{
	//var aF="<div class=\"popup3\"><iframe frameborder=\"0\" id=\"Frame\" src=\"manual.php?s="+i+"&typ="+j+"\" width=\"412\" height=\"440\" border=\"0\"></iframe></div><a href=\"#\" onClick=\"Close()\"><img src=\"img/un/a/x.gif\" border=\"1\" class=\"popup4\" alt=\"Close\"></a>"
	var aF='<div class="popup3"><iframe frameborder="0" id="Frame" src="manual.php?typ='+i+'&id='+j+'"" width="412" height="440" border="0"></iframe></div><a href="#" onClick="Close()"><img src="img/un/a/x.gif" border="1" class="popup4" alt="Close"></a>';
	c.innerHTML=aF;
}
};

function Close()
{
c=document.getElementById("ce");
if(c!=null) {c.innerHTML='';}
};

function Allmsg()
{
for(var x=0;x<document.msg.elements.length;x++)
{
	var y=document.msg.elements[x];
	if(y.name!='s10') y.checked=document.msg.s10.checked;
}
};

function xy()
{
aZ=screen.width+":"+screen.height;
document.snd.w.value=aZ;
};

function my_village()
{
var aU=Math.round(0);
var aD;
var e=document.snd.dname.value;
for(var i=0;i<df.length;i++)
{
	if(df[i].indexOf(e)> -1) {aU++;aD=df[i];}
}
if(aU==1) {document.snd.dname.value=aD;}
};

function map(aO,ao,aT,au,x,y)
{
document.getElementById('x').firstChild.nodeValue=x;
document.getElementById('y').firstChild.nodeValue=y;
c=document.getElementById("tb");
if(c!=null)
{
	if(au=='') {au='-';}
	var aR="<table cellspacing='1' cellpadding='2' class='tbg f8'><tr><td class='rbg f8' colspan='2'></a>"+aO+"</td></tr><tr><td width='45%' class='s7 f8'>"+text_spieler+"</td><td class='s7 f8'>"+ao+"</td></tr><tr><td class='s7 f8'>"+text_einwohner+"</td><td class='s7 f8' id='aT'>"+aT+"</td></tr><tr><td class='s7 f8'>"+text_allianz+"</td><td class='s7 f8'>"+au+"</td></tr></table>";
	var aN="<table class='f8 map_infobox_grey' cellspacing='1' cellpadding='2'><tr><td class='c b' colspan='2' align='center'></a>"+text_details+"</td></tr><tr><td width='45%' class='c s7'>"+text_spieler+"</td><td class='c s7'>-</td></tr><tr><td class='c s7'>"+text_einwohner+"</td><td class='c s7'>-</td></tr><tr><td class='c s7'>"+text_allianz+"</td><td class='c s7'>-</td></tr></table>";
	if(ao!='') {c.innerHTML=aR;}
	else{c.innerHTML=aN;}
}
};

function x_y(x,y)
{
document.getElementById('x').firstChild.nodeValue=x;
document.getElementById('y').firstChild.nodeValue=y;
};

function pop(aQ)
{
as=window.open(aQ,"map","top=100,left=25,width=975,height=550");
as.focus();
return false;
};

var aS=document.getElementById?1:0;
var bd=document.all?1:0;
var ba=(navigator.userAgent.indexOf("Mac")> -1)?1:0;
var ac=(bd&&(!ba)&&(typeof(window.offscreenBuffering)!='undefined'))?1:0;
var aJ=ac;var bc=ac&&(window.navigator.userAgent.indexOf("SV1")!= -1);

function changeOpacity(aL,opacity)
{
if(ac) {aL.style.filter='progid:DXImageTransform.Microsoft.Alpha(opacity='+(opacity*100)+')';}
else if(aS) {aL.style.MozOpacity=opacity;}
};

var g=false;

function T_Load(url,id)
{
g=false;
if(window.XMLHttpRequest)
{
	g=new XMLHttpRequest();
	if(g.overrideMimeType) {g.overrideMimeType('text/xml');}
}
else if(window.ActiveXObject)
{
	try{
		g=new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch(e){
		try{
			g=new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(e){}
	}
}
if(!g)
{
	alert('Can not create XMLHTTP-instance');
	return false;
}
g.onreadystatechange=function() {al(id);};

g.open('GET',url,true);
g.send(null);
};

function al(id)
{
if(g.readyState==4)
{
	if(g.status==200)
	{
		c=document.getElementById(id);
		if(c!=null)
		{
			c.innerHTML=g.responseText;
		}
	}
	else {alert('An error has occurred during request');}
}
};

function k()
{
param='reload=auto';
url=window.location.href;
if(url.indexOf(param)== -1)
{
	if(url.indexOf('?')== -1) {url+='?'+param;}
	else {url+='&'+param;}
}
document.location.href=url;
}
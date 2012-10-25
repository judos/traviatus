

/* UNGENÜTZT !!! */

var v=new Array(0,0,0,0,0);


/* Auflösung des Bildschirms in einem Element speichern */
function xy() {
	aZ=screen.width+":"+screen.height;
	document.snd.w.value=aZ;
}

/* Summe von aj und aC wird mit Begrenzung X und M zurückgegeben */
function ap(aj,X,M,aC) {
  ab=aj+aC;
  if(ab>X) {ab=X;}
  if(ab>M) {ab=M;}
  if(ab==0) {ab='';}
  return ab;
}


function pop(aQ) {
  as=window.open(aQ,"map","top=100,left=25,width=975,height=550");
  as.focus();
  return false;
}

function my_village() {
  var aU=Math.round(0);
  var aD;
  var e=document.snd.dname.value;
  for(var i=0;i<df.length;i++) {
    if(df[i].indexOf(e)> -1) {
    	aU++;aD=df[i];
    }
  }
  if(aU==1) {
  	document.snd.dname.value=aD;
  }
}
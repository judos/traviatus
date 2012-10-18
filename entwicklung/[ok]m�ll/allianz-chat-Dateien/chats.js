var S5217d36b9d4a931314f7a03a00fc7b6d; var Sda3e8fbf4f973b4e9bfd4dbf731af3fe; var S87301ac679b42401bf8e0964a39e0724;
var Sab35fa1b5e02041f027aa304da7e9aad=false; var Sb2a3c7c12fafcf6438f8b24adbb79835=new Array();
var S7253c6a5cf1a425193ffa9473463c581=new Array(); var Se086e53993d95bc9dde2add8fb54e4dd=0;
var S5733893dd3ffa3a59fcd9ee97da43de5=0; var S9f71fbbef9e3a8a50abb31976591b7a2, S0274a8f1a09dd4fc8e3aba658fa39301, S52a58ac62aea51d1543d2738e756d357, See445bab4ab2fde8973e4ce08dbf0d2e;
var S1594a8d7ea8274893b1a7013d2eb4db5=0; var S12cc8aac5c9abf4b438ae287d1ec67e1=0;
var S25398f34cc91057cc00d76e7b6642ca3=-1;  function S81ddb15fd7a390d66ea1e0508e64b1e3 (search, arr) {
for (i=0; i<arr.length; i++) if (arr[i]==search) return true; return false; }  function S17b607648a73355479a8c847c434e2a6() {
var Se80b73e8c1c8217b21434672e3fd8f27 = document.getElementById('div_chat'); var msg = document.getElementById('message').value;
var Sfef2576d54dbde017a3a8e4df699ef6d  = document.getElementById('room').value; var S9a813791f8c4e71b74a5f67433f5e446=new Date();
if ((msg!="") && (!Sab35fa1b5e02041f027aa304da7e9aad)) { if (S12cc8aac5c9abf4b438ae287d1ec67e1<(S9a813791f8c4e71b74a5f67433f5e446-1000)) {
msg=msg.replace("+","&plus;"); x_sendMsg(Sfef2576d54dbde017a3a8e4df699ef6d,uid,name,msg,S41472f4a6c0b9b21d49301f0ea9862d4);
document.getElementById('message').value = ''; document.getElementById('message').focus();
document.getElementById('scrollCheckbox').checked=true; S12cc8aac5c9abf4b438ae287d1ec67e1=S9a813791f8c4e71b74a5f67433f5e446;
} else { alert("Spam"); } } return false; } function S41472f4a6c0b9b21d49301f0ea9862d4(result) {
if (result!="") { var S78f0805fa8ffadabda721fdaf85b3ca9=result.split("<"); if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="j") {
S01c915ebc1993d96eec02d096ee77f26(S78f0805fa8ffadabda721fdaf85b3ca9[1], S78f0805fa8ffadabda721fdaf85b3ca9[2], -1);
S55365909fd1bd63efb0e61c4878a363a(S78f0805fa8ffadabda721fdaf85b3ca9[1]);}  if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="a") {
S01c915ebc1993d96eec02d096ee77f26(S78f0805fa8ffadabda721fdaf85b3ca9[1], "Ally-Chat", -2);
S55365909fd1bd63efb0e61c4878a363a(S78f0805fa8ffadabda721fdaf85b3ca9[1]);}  if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="check")
S01c915ebc1993d96eec02d096ee77f26(S78f0805fa8ffadabda721fdaf85b3ca9[1], S78f0805fa8ffadabda721fdaf85b3ca9[2], S78f0805fa8ffadabda721fdaf85b3ca9[3]);
if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="nocheck") S954ef4d6d4f9f04ee7c856d2a66732c1(S78f0805fa8ffadabda721fdaf85b3ca9[1]);
if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="x") alert ("Spieler nicht im Chat. Nachricht nicht erhalten.");
if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="ignore") { var S07cc694b9b3fc636710fa08b6922c42b=new Date((S78f0805fa8ffadabda721fdaf85b3ca9[1]*1000));
alert ("Dir wurden die Schreibrechte entzogen. \nEnde : "+S07cc694b9b3fc636710fa08b6922c42b.toLocaleString());}
if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="s") alert ("Serverantwort : "+S78f0805fa8ffadabda721fdaf85b3ca9[1]);
if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="list") { line=result.split("<>"); for (var i=1; i<line.length; i++) {
if (line[i]!="") { S78f0805fa8ffadabda721fdaf85b3ca9=line[i].split("<"); var S07cc694b9b3fc636710fa08b6922c42b=new Date((S78f0805fa8ffadabda721fdaf85b3ca9[1]*1000));
document.getElementById('div_chat').innerHTML+=S78f0805fa8ffadabda721fdaf85b3ca9[0]+" : "+S07cc694b9b3fc636710fa08b6922c42b.toLocaleString()+"<br>";}
}}  } }  function S95fe74466d488209f69148ba576ad553(result) { var Sfef2576d54dbde017a3a8e4df699ef6d  = document.getElementById('room').value;
var S78f0805fa8ffadabda721fdaf85b3ca9=result.split("<");  if (!(S78f0805fa8ffadabda721fdaf85b3ca9[0]>0)) {
if (S78f0805fa8ffadabda721fdaf85b3ca9[0]=="ping") x_readyToRead(Sfef2576d54dbde017a3a8e4df699ef6d,uid,name,S95fe74466d488209f69148ba576ad553);
}  if (Sfef2576d54dbde017a3a8e4df699ef6d==S78f0805fa8ffadabda721fdaf85b3ca9[2]) {
if ((S78f0805fa8ffadabda721fdaf85b3ca9[1]>((Sb2a3c7c12fafcf6438f8b24adbb79835[Sfef2576d54dbde017a3a8e4df699ef6d]*1)+1)) && (Sb2a3c7c12fafcf6438f8b24adbb79835[Sfef2576d54dbde017a3a8e4df699ef6d]>0)) {
for (i=((Sb2a3c7c12fafcf6438f8b24adbb79835[Sfef2576d54dbde017a3a8e4df699ef6d]*1)+1); i<S78f0805fa8ffadabda721fdaf85b3ca9[1]; i++) {
x_request(Sfef2576d54dbde017a3a8e4df699ef6d, i, S0eb64f994b5df604cd9a0410f40e2dc1);
Sb683e565814c86ac9c9cf269241e682d();}  } x_readyToRead(Sfef2576d54dbde017a3a8e4df699ef6d,uid,name,S95fe74466d488209f69148ba576ad553);
Sb2a3c7c12fafcf6438f8b24adbb79835[Sfef2576d54dbde017a3a8e4df699ef6d]=(S78f0805fa8ffadabda721fdaf85b3ca9[1]*1);
S0eb64f994b5df604cd9a0410f40e2dc1(result); Sb683e565814c86ac9c9cf269241e682d(); } else {
if (((S78f0805fa8ffadabda721fdaf85b3ca9[2]*1)>20) && ((S78f0805fa8ffadabda721fdaf85b3ca9[3]*1)>0)){
if (!S81ddb15fd7a390d66ea1e0508e64b1e3(S78f0805fa8ffadabda721fdaf85b3ca9[3],S7253c6a5cf1a425193ffa9473463c581)) {
x_debugger("d<"+S78f0805fa8ffadabda721fdaf85b3ca9[2]+"<"+result,Sa2e4822a98337283e39f7b60acf85ec9);
S7253c6a5cf1a425193ffa9473463c581.push(S78f0805fa8ffadabda721fdaf85b3ca9[3]); S01c915ebc1993d96eec02d096ee77f26(S78f0805fa8ffadabda721fdaf85b3ca9[2], S78f0805fa8ffadabda721fdaf85b3ca9[4], S78f0805fa8ffadabda721fdaf85b3ca9[3]);
} x_readyToRead(Sfef2576d54dbde017a3a8e4df699ef6d,uid,name,S95fe74466d488209f69148ba576ad553);
}}  }  function Sa2e4822a98337283e39f7b60acf85ec9(result) { }  function S78616d45e6fc28ae2c08aca4bb612c78(result) {
var S78f0805fa8ffadabda721fdaf85b3ca9=result.split("<"); S954ef4d6d4f9f04ee7c856d2a66732c1(S78f0805fa8ffadabda721fdaf85b3ca9[0]);
var Sd0e45878043844ffc41aac437e86b602=document.getElementById('rooms'); var S1b7d5726533ab525a8760351e9b5e415 =document.getElementById("room"+S78f0805fa8ffadabda721fdaf85b3ca9[0]);
Sd0e45878043844ffc41aac437e86b602.removeChild(S1b7d5726533ab525a8760351e9b5e415);
for (i=0; i<S7253c6a5cf1a425193ffa9473463c581.length; i++) if (S7253c6a5cf1a425193ffa9473463c581[i]==S78f0805fa8ffadabda721fdaf85b3ca9[1]) S7253c6a5cf1a425193ffa9473463c581[i]=-1;
S55365909fd1bd63efb0e61c4878a363a(-1);}   function S01c915ebc1993d96eec02d096ee77f26 (Sfef2576d54dbde017a3a8e4df699ef6d, name, uid2) {
var S5606c042be63f58e41b662ae19c60b76=false; var Sed871969bac9ed1fc1c320cea0b3c2b5  = uid;
obj=document.getElementsByTagName("span"); for (i=0;i<obj.length;i++) if (obj[i].id=="room"+Sfef2576d54dbde017a3a8e4df699ef6d)
S5606c042be63f58e41b662ae19c60b76=true; if (!S5606c042be63f58e41b662ae19c60b76) {
var tmp  = document.getElementById('rooms'); var S69039cae448d160782a27eadda30d075='<span id="room'+Sfef2576d54dbde017a3a8e4df699ef6d+'" name="'+uid2+'" class="roomselector" >';
var myname = name;while (myname.search("%u")!=-1){myname = myname.replace(/%u([0-9a-fA-F]{2,4})/g,'&#x$1;');}myname = myname.replace(/&amp;/g, '&');
S69039cae448d160782a27eadda30d075+='<span style="float:left;"><a href="javascript:S55365909fd1bd63efb0e61c4878a363a('+Sfef2576d54dbde017a3a8e4df699ef6d+');"><span id="channelName'+Sfef2576d54dbde017a3a8e4df699ef6d+'">'+myname+'</span></a></span>';
S69039cae448d160782a27eadda30d075+='<span id="userCount'+Sfef2576d54dbde017a3a8e4df699ef6d+'" style="float:left;"></span>';
if (uid2!=-2) { S69039cae448d160782a27eadda30d075+='<span onClick="x_closeRoom('+Sfef2576d54dbde017a3a8e4df699ef6d+','+uid2+','+Sed871969bac9ed1fc1c320cea0b3c2b5+',S78616d45e6fc28ae2c08aca4bb612c78);" style="float:right;"><img class="del" src="img/x.gif"/></span>';
if (S170bf9f84ff62bf99c863867536e43c2(Sfef2576d54dbde017a3a8e4df699ef6d)<0) { if (S170bf9f84ff62bf99c863867536e43c2(-99)<0) {
S1594a8d7ea8274893b1a7013d2eb4db5++; S488f8806b700c7859874fcee4ddd297b('chatRoomIDs['+S1594a8d7ea8274893b1a7013d2eb4db5+']', Sfef2576d54dbde017a3a8e4df699ef6d);
S488f8806b700c7859874fcee4ddd297b('chatRoomUIDs['+S1594a8d7ea8274893b1a7013d2eb4db5+']', uid2);
S488f8806b700c7859874fcee4ddd297b('chatRoomNames['+S1594a8d7ea8274893b1a7013d2eb4db5+']', name);
} else { index=S170bf9f84ff62bf99c863867536e43c2(-99); S488f8806b700c7859874fcee4ddd297b('chatRoomIDs['+index+']', Sfef2576d54dbde017a3a8e4df699ef6d);
S488f8806b700c7859874fcee4ddd297b('chatRoomUIDs['+index+']', uid2); S488f8806b700c7859874fcee4ddd297b('chatRoomNames['+index+']', name);
}}  } S69039cae448d160782a27eadda30d075+='</span>'; tmp.innerHTML+=S69039cae448d160782a27eadda30d075;
} else {}  }  function S9ed9148cfe779bb4031f5add68deaf18(uid2) { if (!S81ddb15fd7a390d66ea1e0508e64b1e3(uid2,S7253c6a5cf1a425193ffa9473463c581)) {
S7253c6a5cf1a425193ffa9473463c581.push(uid2); x_openQuery(uid,name,uid2,S38d0872944769a15617a164169707909);
}}   function S38d0872944769a15617a164169707909(result) { var S78f0805fa8ffadabda721fdaf85b3ca9=result.split("<");
if ((S78f0805fa8ffadabda721fdaf85b3ca9[0]*1)>0) { S01c915ebc1993d96eec02d096ee77f26(S78f0805fa8ffadabda721fdaf85b3ca9[0], S78f0805fa8ffadabda721fdaf85b3ca9[1], S78f0805fa8ffadabda721fdaf85b3ca9[2]);
S55365909fd1bd63efb0e61c4878a363a(S78f0805fa8ffadabda721fdaf85b3ca9[0]); } else {
alert ("User konnte nicht mehr gefunden werden");}  }  function S71c8340ebaa298cddcd1f697effdd199() {
var S5f0b6ebc4bea10285ba2b8a6ce78b863 = document.getElementById('chatContainer');
var Se80b73e8c1c8217b21434672e3fd8f27 = document.getElementById('div_chat'); S78f0805fa8ffadabda721fdaf85b3ca9=S5f0b6ebc4bea10285ba2b8a6ce78b863.style.height.split("px");
S5f0b6ebc4bea10285ba2b8a6ce78b863Height=parseInt(S78f0805fa8ffadabda721fdaf85b3ca9[0]);
tmp=document.getElementById('scrollCheckbox'); if ((Se80b73e8c1c8217b21434672e3fd8f27.scrollHeight>Se086e53993d95bc9dde2add8fb54e4dd) && (tmp.checked)){
dif=Se80b73e8c1c8217b21434672e3fd8f27.scrollHeight-Se086e53993d95bc9dde2add8fb54e4dd;
Se086e53993d95bc9dde2add8fb54e4dd+=Math.max(dif/5,3); Se80b73e8c1c8217b21434672e3fd8f27.style.top=-Math.ceil(Se086e53993d95bc9dde2add8fb54e4dd-S5f0b6ebc4bea10285ba2b8a6ce78b863Height)+"px";
p=(Se086e53993d95bc9dde2add8fb54e4dd-S5f0b6ebc4bea10285ba2b8a6ce78b863Height)/Se80b73e8c1c8217b21434672e3fd8f27.scrollHeight*(S5f0b6ebc4bea10285ba2b8a6ce78b863Height-22);
if (p<0) p=0; document.getElementById('scrollbar').style.top=Math.round(p)+"px";
S25398f34cc91057cc00d76e7b6642ca3=-1;}  if (S25398f34cc91057cc00d76e7b6642ca3>=0) {
dif=S25398f34cc91057cc00d76e7b6642ca3-Se086e53993d95bc9dde2add8fb54e4dd; if (Math.abs(dif)<=2) S25398f34cc91057cc00d76e7b6642ca3=-1;
if (dif>0) Se086e53993d95bc9dde2add8fb54e4dd+=Math.max(dif/5,2); else Se086e53993d95bc9dde2add8fb54e4dd+=Math.min(dif/5,-2);
Se80b73e8c1c8217b21434672e3fd8f27.style.top=-Math.ceil(Se086e53993d95bc9dde2add8fb54e4dd-S5f0b6ebc4bea10285ba2b8a6ce78b863Height)+"px";
p=(Se086e53993d95bc9dde2add8fb54e4dd-S5f0b6ebc4bea10285ba2b8a6ce78b863Height)/Se80b73e8c1c8217b21434672e3fd8f27.scrollHeight*(S5f0b6ebc4bea10285ba2b8a6ce78b863Height-22);
if (p<0) p=0; document.getElementById('scrollbar').style.top=Math.round(p)+"px";
}}   function S0eb64f994b5df604cd9a0410f40e2dc1(line) { S5733893dd3ffa3a59fcd9ee97da43de5++;
var Se80b73e8c1c8217b21434672e3fd8f27 = document.getElementById('div_chat'); S78f0805fa8ffadabda721fdaf85b3ca9=line.split("<");
var myStr = S78f0805fa8ffadabda721fdaf85b3ca9[4];while (myStr.search("%u")!=-1){myStr = myStr.replace(/%u([0-9a-fA-F]{2,4})/g,'&#x$1;');}myStr = myStr.replace(/&amp;/g, '&');S78f0805fa8ffadabda721fdaf85b3ca9[4] = myStr;
var S07cc694b9b3fc636710fa08b6922c42b=new Date((S78f0805fa8ffadabda721fdaf85b3ca9[0]*1000));
var S8850773a1ecf29987fba592e52b6d676=""; S8850773a1ecf29987fba592e52b6d676 += "<span class=\"chatTime\">["+S07cc694b9b3fc636710fa08b6922c42b.getHours()+":";
if (S07cc694b9b3fc636710fa08b6922c42b.getMinutes()<10) S8850773a1ecf29987fba592e52b6d676 += "0";
S8850773a1ecf29987fba592e52b6d676 += S07cc694b9b3fc636710fa08b6922c42b.getMinutes()+"]&nbsp;</span>";
if (!(typeof userMenu=="undefined")) { S8850773a1ecf29987fba592e52b6d676 += '<a href="" onclick="userMenu(\''+parts[4]+'\','+S78f0805fa8ffadabda721fdaf85b3ca9[3]+'); return false;"><span class="t">'+S78f0805fa8ffadabda721fdaf85b3ca9[4]+'</span></a>';
} else { S8850773a1ecf29987fba592e52b6d676 += '<a href="javascript:S9ed9148cfe779bb4031f5add68deaf18('+S78f0805fa8ffadabda721fdaf85b3ca9[3]+');"><span class="t">'+S78f0805fa8ffadabda721fdaf85b3ca9[4]+'</span></a>';
if (S78f0805fa8ffadabda721fdaf85b3ca9[3]==-1) S8850773a1ecf29987fba592e52b6d676+='<img src="code/ally/forum/irc.jpg">';
} S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[holz]","<img class='r1' src='img/x.gif'>");
S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[lehm]","<img class='r2' src='img/x.gif'>");
S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[eisen]","<img class='r3' src='img/x.gif'>");
S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[getreide]","<img class='r4' src='img/x.gif'>");
S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[h]","<img class='r1' src='img/x.gif'>");
S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[l]","<img class='r2' src='img/x.gif'>");
S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[e]","<img class='r3' src='img/x.gif'>");
S78f0805fa8ffadabda721fdaf85b3ca9[5]=S78f0805fa8ffadabda721fdaf85b3ca9[5].replace("[g]","<img class='r4' src='img/x.gif'>");
var myStr = S78f0805fa8ffadabda721fdaf85b3ca9[5];while (myStr.search("%u")!=-1){myStr = myStr.replace(/%u([0-9a-fA-F]{2,4})/g,'&#x$1;');}myStr = myStr.replace(/&amp;/g, '&');S78f0805fa8ffadabda721fdaf85b3ca9[5] = myStr;
S8850773a1ecf29987fba592e52b6d676 += ":&nbsp;"+S78f0805fa8ffadabda721fdaf85b3ca9[5]+"<br>";
Se80b73e8c1c8217b21434672e3fd8f27.innerHTML += S8850773a1ecf29987fba592e52b6d676;
if (S78f0805fa8ffadabda721fdaf85b3ca9[6]>0) document.getElementById('userCount'+S78f0805fa8ffadabda721fdaf85b3ca9[2]).innerHTML="("+S78f0805fa8ffadabda721fdaf85b3ca9[6]+")";
}  function Sb683e565814c86ac9c9cf269241e682d() { var Se80b73e8c1c8217b21434672e3fd8f27 = document.getElementById('div_chat');
Se80b73e8c1c8217b21434672e3fd8f27.innerHTML = Se80b73e8c1c8217b21434672e3fd8f27.innerHTML.substr(-20000);
Se80b73e8c1c8217b21434672e3fd8f27.innerHTML = Se80b73e8c1c8217b21434672e3fd8f27.innerHTML.substr(Se80b73e8c1c8217b21434672e3fd8f27.innerHTML.indexOf("["));
S78f0805fa8ffadabda721fdaf85b3ca9=document.getElementById('chatContainer').style.height.split("px");
S5f0b6ebc4bea10285ba2b8a6ce78b863Height=parseInt(S78f0805fa8ffadabda721fdaf85b3ca9[0]);
chatHeight=Se80b73e8c1c8217b21434672e3fd8f27.scrollHeight; scrollHeight=Math.max(5,(S5f0b6ebc4bea10285ba2b8a6ce78b863Height-22)*Math.min(1,S5f0b6ebc4bea10285ba2b8a6ce78b863Height/chatHeight));
document.getElementById('scrollbar').style.height=Math.round(scrollHeight)+"px";}
function S468279702cdab466e5fe960fb763ef26(result) { window.clearInterval(S5217d36b9d4a931314f7a03a00fc7b6d);
window.clearInterval(S87301ac679b42401bf8e0964a39e0724); var S5f0b6ebc4bea10285ba2b8a6ce78b863 = document.getElementById('chatContainer');
var Sfef2576d54dbde017a3a8e4df699ef6d  = document.getElementById('room').value; document.getElementById('div_chat').innerHTML='';
document.getElementById('div_chat').S25398f34cc91057cc00d76e7b6642ca3p=0; Sb2a3c7c12fafcf6438f8b24adbb79835[Sfef2576d54dbde017a3a8e4df699ef6d]=-1;
line=result.split("<>"); for (var i=0; i<line.length-1; i++) { if (line[i]!="") {
S0eb64f994b5df604cd9a0410f40e2dc1(line[i]);}  } Sb683e565814c86ac9c9cf269241e682d();
if (line[line.length-1]>0) document.getElementById('userCount'+Sfef2576d54dbde017a3a8e4df699ef6d).innerHTML="("+line[line.length-1]+")";
var Se80b73e8c1c8217b21434672e3fd8f27 = document.getElementById('div_chat'); S78f0805fa8ffadabda721fdaf85b3ca9=document.getElementById('chatContainer').style.height.split("px");
S5f0b6ebc4bea10285ba2b8a6ce78b863Height=parseInt(S78f0805fa8ffadabda721fdaf85b3ca9[0]);
Se086e53993d95bc9dde2add8fb54e4dd=Se80b73e8c1c8217b21434672e3fd8f27.scrollHeight;
Se80b73e8c1c8217b21434672e3fd8f27.style.top=-(Se086e53993d95bc9dde2add8fb54e4dd-S5f0b6ebc4bea10285ba2b8a6ce78b863Height)+"px";
S87301ac679b42401bf8e0964a39e0724=window.setInterval("S71c8340ebaa298cddcd1f697effdd199()", 100);
S78f0805fa8ffadabda721fdaf85b3ca9=document.getElementById('scrollbar').style.height.split("px")
h=parseInt(S78f0805fa8ffadabda721fdaf85b3ca9[0]); document.getElementById('scrollbar').style.top=(S5f0b6ebc4bea10285ba2b8a6ce78b863Height-22-h)+"px";
S25398f34cc91057cc00d76e7b6642ca3=-1; document.getElementById('scrollCheckbox').checked=true;
document.getElementById('message').focus(); x_readyToRead(Sfef2576d54dbde017a3a8e4df699ef6d,uid,name,S95fe74466d488209f69148ba576ad553);
}  function S55365909fd1bd63efb0e61c4878a363a(Sfef2576d54dbde017a3a8e4df699ef6d) {
document.getElementById('room').value=Sfef2576d54dbde017a3a8e4df699ef6d; var Sd5d3db1765287eef77d7927cc956f50a=document.getElementById('TitleName');
obj=document.getElementsByTagName("span"); for (i=0;i<obj.length;i++) { if (obj[i].id.substr(0,4)=="room") {
if (obj[i].id=="room"+Sfef2576d54dbde017a3a8e4df699ef6d) { obj[i].className="roomselectorActive";
tmp=document.getElementById('channelName'+Sfef2576d54dbde017a3a8e4df699ef6d); Sd5d3db1765287eef77d7927cc956f50a.innerHTML='<div style="float:left;"></div><div style="float:right;" id="TitleClose"></div><div style="text-align:middle">'+tmp.innerHTML+'</div>';
} else { obj[i].className="roomselector"; } } } if (Sfef2576d54dbde017a3a8e4df699ef6d>=0) {
document.getElementById('message').style.display="block"; document.getElementById('chatContainer').style.display="block";
x_enterChat(Sfef2576d54dbde017a3a8e4df699ef6d,uid,S468279702cdab466e5fe960fb763ef26);
} else { document.getElementById('message').style.display="none"; document.getElementById('chatContainer').style.display="none";
document.getElementById('TitleName').innerHTML="Chat";}  }  function S954ef4d6d4f9f04ee7c856d2a66732c1 (id) {
cookies=document.cookie.split("; "); for (i=0; i<cookies.length; i++) { S78f0805fa8ffadabda721fdaf85b3ca9=cookies[i].split("=");
if (S78f0805fa8ffadabda721fdaf85b3ca9[1]==id) { index=S78f0805fa8ffadabda721fdaf85b3ca9[0].match("\[(0-9)+\]");
S488f8806b700c7859874fcee4ddd297b('chatRoomIDs['+index+']','-99'); S488f8806b700c7859874fcee4ddd297b('chatRoomUIDs['+index+']','');
S488f8806b700c7859874fcee4ddd297b('chatRoomNames['+index+']','');}  } }  function S170bf9f84ff62bf99c863867536e43c2 (id) {
cookies=document.cookie.split("; "); for (i=0; i<cookies.length; i++) { S78f0805fa8ffadabda721fdaf85b3ca9=cookies[i].split("=");
if (S78f0805fa8ffadabda721fdaf85b3ca9[1]==id) { index=S78f0805fa8ffadabda721fdaf85b3ca9[0].match("\[(0-9)+\]");
return index;}  } return -1; }  function S488f8806b700c7859874fcee4ddd297b(name,value) {
expires = new Date(); expires.setTime(expires.getTime() + (1000 * 86400 * 365));
document.cookie = name + "="+value+"; expires=" + expires.toGMTString() +  "; path=/";}
function Sc3a84224362429d3ab068f23a067e803(evt) { if(!evt) var evt = window.event;
var S589768d24081ac9f85eeb1660564e2ef =(See445bab4ab2fde8973e4ce08dbf0d2e  + evt.screenY - S0274a8f1a09dd4fc8e3aba658fa39301);
height = parseInt(S383e3720ffba869ee819f5f40bb05ff6.style.height); S78f0805fa8ffadabda721fdaf85b3ca9=document.getElementById('scrollbarbackground').style.height.split("px");
S5f0b6ebc4bea10285ba2b8a6ce78b863Height=parseInt(S78f0805fa8ffadabda721fdaf85b3ca9[0]);
if (S589768d24081ac9f85eeb1660564e2ef<0) S589768d24081ac9f85eeb1660564e2ef=0; if (S589768d24081ac9f85eeb1660564e2ef>(S5f0b6ebc4bea10285ba2b8a6ce78b863Height-height)) S589768d24081ac9f85eeb1660564e2ef=S5f0b6ebc4bea10285ba2b8a6ce78b863Height-height;
S383e3720ffba869ee819f5f40bb05ff6.style.top  = S589768d24081ac9f85eeb1660564e2ef  + 'px';
S78f0805fa8ffadabda721fdaf85b3ca9=document.getElementById('chatContainer').style.height.split("px");
S5f0b6ebc4bea10285ba2b8a6ce78b863Height=parseInt(S78f0805fa8ffadabda721fdaf85b3ca9[0]);
chatHeight= document.getElementById('div_chat').scrollHeight; newChatTop=Math.round((S589768d24081ac9f85eeb1660564e2ef/(S5f0b6ebc4bea10285ba2b8a6ce78b863Height-22))*chatHeight);
document.getElementById('div_chat').style.top="-"+newChatTop+("px"); Se086e53993d95bc9dde2add8fb54e4dd=(newChatTop+S5f0b6ebc4bea10285ba2b8a6ce78b863Height);
tmp=document.getElementById('scrollCheckbox'); tmp.checked=false; }  var S383e3720ffba869ee819f5f40bb05ff6 = document.getElementById('scrollbar');
S383e3720ffba869ee819f5f40bb05ff6.ondragstart = function(evt) { return false; };
S383e3720ffba869ee819f5f40bb05ff6.onmousedown = function(evt) { if(!evt) var evt = window.event;
if((evt.which && evt.which == 3) || (evt.button && evt.button == 2)) return true;
See445bab4ab2fde8973e4ce08dbf0d2e  = parseInt(S383e3720ffba869ee819f5f40bb05ff6.style.top);
S0274a8f1a09dd4fc8e3aba658fa39301 = evt.screenY; document.onmousemove = Sc3a84224362429d3ab068f23a067e803;
S25398f34cc91057cc00d76e7b6642ca3=-1; };  document.onmouseup = function(evt) { if (!(typeof mouseMove=="undefined")) {
document.onmousemove = mouseMove; document.getElementById('userMenuDiv').style.display="none";
} else { document.onmousemove = null; } return true; };  document.getElementById('scrollbarbackground').onclick = S056b3674196b1de415d69d9a76fa28b5;
document.getElementById('scrollbarbackground2').onclick = S056b3674196b1de415d69d9a76fa28b5;
function S056b3674196b1de415d69d9a76fa28b5(evt) { if(!evt) var evt = window.event;
tmpPart=document.getElementById('scrollbarbackground2').style.height.split("px");
scrollBarHeight=parseInt(tmpPart[0]); var Se80b73e8c1c8217b21434672e3fd8f27 = document.getElementById('div_chat');
document.getElementById('scrollCheckbox').checked=false; percent=(evt.clientY-getAbsY('scrollbarbackground2'))/scrollBarHeight;
S25398f34cc91057cc00d76e7b6642ca3=Math.round(Se80b73e8c1c8217b21434672e3fd8f27.scrollHeight*percent);
scrollBarPos=document.getElementById('scrollbar').style.top.split("px")[0]; S78f0805fa8ffadabda721fdaf85b3ca9=document.getElementById('chatContainer').style.height.split("px");
S5f0b6ebc4bea10285ba2b8a6ce78b863Height=parseInt(S78f0805fa8ffadabda721fdaf85b3ca9[0]);
if (scrollBarPos>(evt.clientY-getAbsY('scrollbarbackground2'))) S25398f34cc91057cc00d76e7b6642ca3+=S5f0b6ebc4bea10285ba2b8a6ce78b863Height;
}  var S3d801aa532c1cec3ee82d87a99fdf63f=document.getElementById('joincmd').value;
if (S3d801aa532c1cec3ee82d87a99fdf63f=="") { S55365909fd1bd63efb0e61c4878a363a(-1);
} else { if (parseInt(S3d801aa532c1cec3ee82d87a99fdf63f)>0) { S55365909fd1bd63efb0e61c4878a363a(parseInt(S3d801aa532c1cec3ee82d87a99fdf63f));
} else { x_sendMsg(1,uid,"1",S3d801aa532c1cec3ee82d87a99fdf63f,S41472f4a6c0b9b21d49301f0ea9862d4);
}}
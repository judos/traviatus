<?php
if (!$execute) die('');
needed_login();
$stview=1;

//scroll down in the div
$body_onload.="scrollDown();";


$ally=Allianz::getById($login_user->get('ally'));
if ($ally===NULL) gotoP('dorf2');
$rang=$ally->getRang($login_user->get('ally_rang'));

echo'<h1>'.$ally->get('name').'</h1>';
Outputer::allianzMenu();




echo'<table cellpadding="2" cellspacing="1" class="tbg"><tbody>
  <tr class="rbg"><td colspan="2">Allianz-Chat</td></tr>
  <tr>
  <td><div class="allianz_chat" id="allianz_chat">';
$sql="SELECT * FROM tr".ROUND_ID."_ally_chat WHERE ally_id=".$ally->get('id')." ORDER BY zeit ASC;";
$result=mysql_query($sql);
while($data=mysql_fetch_assoc($result)) {
	$user=Spieler::getByID($data['user_id']);
	echo'<p>['.date('H:i',strtotime($data['zeit'])).'] '.$user->getLink().': '.$data['text'].'</p>';
	$lastmsg=$data['zeit'];
}

echo'</div></td></tr>
	</tbody></table><br>
	<table width="100%"><tr><td width="100%">
	<input type="text" id="text" class="fm" style="width:97%;" onKeyPress="send_msg2(this,event);" /></td><td>
	';
	Outputer::button('ok','snd','onclick="send_msg();"');
	echo'</td></tr></table>';
	
if (!isset($lastmsg))
	$lastmsg='';
?>

<script type="text/javascript">
<!--
try{
var lastmsg='<?php echo $lastmsg; ?>';
function send_msg() {
	var msg=dgei('text').value;
	var res;
	dgei('text').value='';
	var req=new_ajax_request("ajax/allianz-chat.php",new Array("newmsg","lastmsg"),new Array(msg,lastmsg));
	req.onreadystatechange = function() {
		if (ajax_ready(req)) {
			res=ajax_answer(req).split('<br>');
			lastmsg=res[0];
			dgei('allianz_chat').innerHTML=dgei('allianz_chat').innerHTML+res[1];
			scrollDown();
		}
		if (ajax_error(req)) {
			alert(ajax_answer(req));
		}
	}
}

function send_msg2(ref,e) {
	var keycode;
  if (window.event) keycode = window.event.keyCode;
  else if (e) keycode = e.which;
  else return true;
  if (keycode == 13) {
		send_msg();
		return false;
	}
  else
     return true;
}

function scrollDown() {
	dgei('allianz_chat').scrollTop = dgei('allianz_chat').scrollHeight;
}


}catch(e) {alert(e);}
-->
</script>
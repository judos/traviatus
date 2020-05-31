<?php
if (!$execute) die('');

outgame_links();
outgame_blocks();

$body_onload .= 'snd.name.focus(); ';
?>


<div align="center">
    <img src="img/de/t1/login.gif"
        width="468" height="60">
</div>

<h5>
    <img src="img/de/t2/u04.gif"
        width="160" height="15" border="0">
</h5>

<p class="f9">Um sich einloggen zu können, müssen in
    ihrem Browser Cookies aktiviert sein.
</p>

<form method="post" name="snd" action="?do=login">
<p>
    <table class="p1" style="width:100%" cellspacing="1"
        cellpadding="0"><tr><td>
    <table width="100%" cellspacing="1" cellpadding="0">
        <tr><td><label>Name:</label>
        <input class="fm fm110" type="text" name="name"
            value="" maxlength="15">
        <span class="e f7"></span>
        </td></tr>
        
        <tr><td><label>Passwort:</label>
        <input class="fm fm110" type="password" name="pw"
            value="" maxlength="20">
        <span class="e f7"></span>
        </td></tr>
    </table></td></tr></table>
</p>
<p align="center">
	<?php
	Outputer::button('login','l');
	?>
</p>
</form>

<?php
if ($page_msg!='') {
	echo '<span style="color:red;">'.$page_msg.'</span>';
}
?>
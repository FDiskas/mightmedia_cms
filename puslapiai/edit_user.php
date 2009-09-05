<?php

/**
 * @Projektas: MightMedia TVS
 * @Puslapis: www.coders.lt
 * @$Author$
 * @copyright CodeRS ©2008
 * @license GNU General Public License v2
 * @$Revision$
 * @$Date$
 **/
if (!defined("LEVEL") || !defined("OK") || !isset($_SESSION['username'])) {
	header("Location: ?");
	exit;
}
if (isset($url['m']) && isnum($url['m']) && $url['m'] > 0) {
	$mid = (int)$url['m'];
} else {
	$mid = 0;
}
if (isset($url['id']) && isnum($url['id']) && $url['id'] > 0) {
	$id = (int)$url['id'];
} else {
	$url['id'] = 0;
}
// ############ Apdorojomi duomenys kurie buvo pateikti is tam tikros redagavimo lenteles #####################
// ######### Slaptazodzio keitimas #############
if (isset($_POST['old_pass']) && count($_POST['old_pass']) > 0 && count($_POST['new_pass']) > 0 && count($_POST['new_pass2']) > 0) {
	$old_pass = koduoju($_POST['old_pass']);
	$sql = count(mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "users` WHERE nick=" . escape($_SESSION['username']) . " AND pass=" . escape($old_pass) . ""));
	if ($sql != 0) {
		$new_pass = koduoju($_POST['new_pass']);
		$new_pass2 = koduoju($_POST['new_pass2']);
		if ($new_pass == $new_pass2) {
			mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "users` SET pass=" . escape($new_pass) . " WHERE nick=" . escape($_SESSION['username']) . "");
			msg("{$lang['system']['done']}", "{$lang['user']['edit_updated']}.");

		} else {
			klaida("{$lang['system']['error']}", "{$lang['user']['edit_badconfirm']}.");
		}
	} else {
		klaida("{$lang['system']['error']}", "{$lang['user']['edit_badpass']}.");
	}
	unset($old_pass, $sql, $new_pass, $new_pass2);
}
// ################# kontaktu keitimas ######################
if (isset($_POST['action']) && $_POST['action'] == 'contacts_change') {
	$icq = input($_POST['icq']);
	$msn = input($_POST['msn']);
	$skype = input($_POST['skype']);
	$yahoo = input($_POST['yahoo']);
	$aim = input($_POST['aim']);
	$url = input($_POST['url']);
	$email = input($_POST['email']);
		$sql = mysql_query1("SELECT `email` FROM `" . LENTELES_PRIESAGA . "users` WHERE nick=" . escape($_SESSION['username']) . " LIMIT 1");
	if(file_exists('images/avatars/'.md5($sql['email']).'.jpeg'))
	rename('images/avatars/'.md5($sql['email']).'.jpeg','images/avatars/'.md5($email).'.jpeg');
	mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "users` SET icq=" . escape($icq) . ", msn=" . escape($msn) . ", skype=" . escape($skype) . ", yahoo=" . escape($yahoo) . ", aim=" . escape($aim) . ", url=" . escape($url) . ", email=" . escape($email) . " WHERE nick=" . escape($_SESSION['username']) . "");
	msg("{$lang['system']['done']}", "{$lang['user']['edit_updated']}");
	unset($icq, $msn, $skype, $yahoo, $aim, $url, $email);
}
// ################ Salies bei miesto nustatymai #############
if (isset($_POST['action']) && $_POST['action'] == 'country_change') {
	$miestas = input($_POST['miestas']);
	$salis = input($_POST['salis']);
	mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "users` SET salis=" . escape($salis) . ", miestas=" . escape($miestas) . " WHERE nick=" . escape($_SESSION['username']) . "");
	msg("{$lang['system']['done']}", "{$lang['user']['edit_updated']}");
}


// ################ Pagrindiniu nustatymu keitimas ###################
if (isset($_POST['action']) && $_POST['action'] == 'default_change') {
	$vardas = input($_POST['vardas']);
	$pavarde = input($_POST['pavarde']);
	$metai = (int)$_POST['metai'];
	$menesis = (int)$_POST['menesis'];
	$diena = (int)$_POST['diena'];
	$parasas = input($_POST['parasas']);
	$gimimas = $metai . "-" . $menesis . "-" . $diena;
	mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "users` SET vardas='" . $vardas . "', pavarde='" . $pavarde . "', parasas='" . $parasas . "', gim_data='" . $gimimas . "' WHERE nick='" . $_SESSION['username'] . "'");
	msg("{$lang['system']['done']}", "{$lang['user']['edit_updated']}");
}
// ################ Siulomi punktai redagavimui MENIU ##########################
$text = "
 <table width=100% border=0>
	<tr>
		<td>
			<div class=\"blokas\"><center><a href='?id," . $id . ";m,1'><img src=\"images/user/user-auth.png\" alt=\"slaptazodis\" />{$lang['user']['edit_pass']}</a></center></div>
			<div class=\"blokas\"><center><a href='?id," . $id . ";m,2'><img src=\"images/user/user-contact.png\" alt=\"kontaktai\" />{$lang['user']['edit_contacts']}</a></center></div>
			<div class=\"blokas\"><center><a href='?id," . $id . ";m,3'><img src=\"images/user/user-place.png\" alt=\"vietove\" />{$lang['user']['edit_locality']}</a></center></div>
<div class=\"blokas\"><center><a href='?id," . $id . ";m,4'><img src=\"images/user/user-avatar.png\" alt=\"avataras\" />{$lang['user']['edit_avatar']}</a></center></div>
			<div class=\"blokas\"><center><a href='?id," . $id . ";m,5'><img src=\"images/user/user-settings.png\" alt=\"nustatymai\" />{$lang['user']['edit_signature']}</a></center></div>
			
		</td>
	</tr>
</table>
";
lentele($lang['user']['edit_settings'], $text);
// ######################### Jei pasirinktas vienas is pasiulytu MENIU ####################
include_once ("priedai/class.php");
if (isset($mid) && isnum($mid)) {
	// Pakeisti slaptazodi
	if ($mid == 1) {
		$form = array("Form" => array("action" => "", "method" => "post", "enctype" => "", "id" => "", "class" => "", "name" => "change_password"), "{$lang['user']['edit_pass']}:" => array("type" => "password", "value" => "", "name" => "old_pass"), "{$lang['user']['edit_newpass']}:" => array("type" => "password", "value" => "", "name" => "new_pass"), "{$lang['user']['edit_confirmnewpass']}:" => array("type" => "password", "value" => "", "name" => "new_pass2"), "" => array("type" => "hidden", "name" => "action", "value" => "pass_change"), "" => array("type" => "submit", "value" => "{$lang['user']['edit_update']}"));
		$bla = new forma();
		lentele($lang['user']['edit_pass'], $bla->form($form));
	}
	// Pakeisti kontaktinius duomenis
	elseif ($mid == 2) {
		$info = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "users` WHERE nick=" . escape($_SESSION['username']) . "LIMIT 1");

		$form = array(
			"Form" => array("action" => "", "method" => "post", "enctype" => "", "id" => "", "extra" => "onSubmit=\"return checkMail('change_contacts','email')\"", "name" => "change_contacts"), 
			"ICQ:" => array("type" => "text", "value" => input($info['icq']), "name" => "icq", "class" => "input"), 
			"MSN:" => array("type" => "text", "value" => input($info['msn']), "name" => "msn", "class" => "input"), 
			"Skype:" => array("type" => "text", "value" => input($info['skype']), "name" => "skype", "class" => "input"), 
			"Yahoo:" => array("type" => "text", "value" => input($info['yahoo']), "name" => "yahoo", "class" => "input"), 
			"AIM:" => array("type" => "text", "value" => input($info['aim']), "name" => "aim", "class" => "input"), 
			"{$lang['user']['edit_web']}:" => array("type" => "text", "value" => input($info['url']), "name" => "url", "class" => "input"), 
			"{$lang['user']['edit_email']}:" => array("type" => "text", "value" => input($info['email']), "name" => "email", "class" => "input"), 
			"\r\r\r" => array("type" => "hidden", "name" => "action", "value" => "contacts_change"), 
			"" => array("type" => "submit", "value" => "{$lang['user']['edit_update']}")
		);
		$bla = new forma();
		lentele($lang['user']['edit_contacts'], $bla->form($form));


	}
	// Pakeisti sali, miesta
	elseif ($mid == 3) {
		$info = mysql_query1("SELECT salis, miestas FROM `" . LENTELES_PRIESAGA . "users` WHERE nick=" . escape($_SESSION['username']) . " LIMIT 1");

		$sql = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "salis`");
		$salis = array();
		foreach ($sql as $row) {
			//	$text .= "<option value='" . $row['iso'] . "' ";
			//	if ($row['iso'] == $info['salis']) {
			//		$text .= "selected";
			//}
			//$text .= ">" . $row['printable_name'] . "</option>\n";
			$salis[$row['iso']] = $row['printable_name'];
		}

		$forma = array("Form" => array("action" => "", "method" => "post", "name" => "change_country"), "{$lang['user']['edit_country']}:" => array("type" => "select", "value" => $salis, "name" => "salis", "selected" => $info['salis']), "{$lang['user']['edit_city']}:" => array("type" => "text", "value" => $info['miestas'], "name" => "miestas"), " \r " => array("type" => "hidden", "name" => "action", "value" => "country_change"), "" => array("type" => "submit", "value" => "{$lang['user']['edit_update']}"));

		$bla = new forma();
		lentele($lang['user']['edit_locality'], $bla->form($forma));


	}

	// Avataro keitimas
	//Žaidime mano šito nereikės

	elseif ($mid == 4) {
		$sql = mysql_query1("SELECT `email` FROM `" . LENTELES_PRIESAGA . "users` WHERE `nick`='" . $_SESSION['username'] . "' LIMIT 1");
		if(isset($_GET['a'])&&$_GET['a']==1)@unlink('images/avatars/'.md5($sql['email']).'.jpeg');
$avataras=avatar($sql['email']);
$name=md5($sql['email']);
		$avatar =<<<HTML
<style type="text/css">



</style>

<script type="text/javascript" src="javascript/jquery/jquery.uploader.js"></script>

<script  type="text/javascript">

$(document).ready(function(){

var button = $('#button1'), interval;
new AjaxUpload(button,{
action: 'upload.php',
name: 'userfile',
data: {
email : '{$sql['email']}'

},

onSubmit : function(file, ext){
$('#gravatar').hide();
if (! (ext && /^(jpg|jpeg|png|gif|bmp)$/.test(ext))){
alert('{$lang['admin']['download_badfile']}');
return false;
} else {
button.html('<img src="images/icons/Loading.gif" />{$lang['user']['edit_uploading']}...');
this.disable();
}
},
onComplete: function(file, response){
button.html('<img src="images/icons/picture__plus.png" alt="" class="middle"/>{$lang['user']['edit_upload']}');
this.enable();

$('#example1 .files').replaceWith('<div class="files"><img id="ikeltas_avataras" src="images/avatars/{$name}.jpeg?'+file+'" alt="" /></div>');
}
}); 
});
</script>
<div align='center'>
<ul>
	<li id="example1" class="example">
		
	
<div class="btns"><a onclick="return false" class="btn"><span id="button1"><img src="images/icons/picture__plus.png" alt="" class="middle"/>{$lang['user']['edit_upload']}</span></a>
<a class="btn" href="?id,{$_GET['id']};m,{$_GET['m']};a,1"><span><img src="images/icons/picture__plus.png" alt="" class="middle"/>{$lang['user']['edit_gravatar']}</span></a>
</div> 
		<p>{$lang['user']['edit_avatar']}:</p>

		<div class="files">{$avataras}</div>
		</li>
</ul></div>
HTML;
if(isset($_GET['a'])&&$_GET['a']==1)
$avatar .= "<div align='center' id='gravatar'>
{$lang['user']['edit_avatarcontent']} <b>" . $sql['email'] . "</b> .</div>
		";
/*	<div class="wrapper">
			<div id="button1" class="button  "><b>>Įkelti<</b></div>
		</div>*/
		lentele($lang['user']['edit_avatar'], $avatar);
	}
	// Pagrindiniai nustatymai
	elseif ($mid == 5) {
		$sql = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "users` WHERE nick=" . escape($_SESSION['username']) . " LIMIT 1");
		$data = explode("-", $sql['gim_data']);
		for ($a = 1; $a <= 31; $a++) {
			$day[$a] = $a;

		}
		for ($a = 1; $a <= 12; $a++) {
			$month[$a] = $a;

		}
		$a = date("Y") - 80;
		$viso = date("Y") - 7;
		while ($a < $viso) {
			$year[$a] = $a;
			$a++;
		}
		$forma = array(
			"Form" => array("action" => "", "method" => "post", "name" => "pagr_nustatymai"), 
			"{$lang['user']['edit_name']}:" => array("type" => "text", "value" => $sql['vardas'], "name" => "vardas", "class" => "input"), 
			"{$lang['user']['edit_secondname']}:" => array("type" => "text", "value" => $sql['pavarde'], "name" => "pavarde", "class" => "input"), 
			"{$lang['user']['edit_dateOfbirth']}:" => array("type" => "select", "value" => $year, "selected" => $data[0], "class" => "select", "name" => "metai"), 
			" " => array("type" => "select", "class" => "select", "value" => $month, "selected" => $data[1], "name" => "menesis"), 
			"\r " => array("type" => "select", "class" => "select", "value" => $day, "selected" => $data[2], "name" => "diena"), 
			"{$lang['user']['edit_signature']}" => array("type" => "textarea", "class" =>	"input", "value" => $sql['parasas'], "name" => "parasas"), 
			" \r \n" => array("type" => "hidden", "name" => "action", "value" => "default_change"), 
			"" => array("type" => "submit", "value" => "{$lang['user']['edit_update']}")
		);

		$bla = new forma();
		lentele($lang['user']['edit_mainsettings'], $bla->form($forma));


	}
}
//print_r($_POST);


?>
<script language="JavaScript1.2">
function checkMail(form,email) {
	var x = document.forms[form].email.value;
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (filter.test(x)) { return true; }
	else { alert('<?php

echo $lang['user']['edit_bademail'];

?>'); return false; }
}
</script>
<?php
/**
 * @Projektas: MightMedia TVS
 * @license GNU General Public License v2
 * @$Revision: 121 $
 * @$Date: 2009-05-23 17:22:13 +0300 (Št, 23 Geg 2009) $
 * @Apie: upgrade.php - TVS atnaujinimo įrankis
 * */
header("Content-type: text/html; charset=utf-8");
@ini_set('error_reporting', E_ALL);
@ini_set('display_errors', 'Off');
session_start();
ob_start();
include_once("../priedai/conf.php");
include_once("../priedai/prisijungimas.php");
if (isset($_SESSION['id']) && $_SESSION['id'] == 1) {
	//į kokią versiją atnaujinam
	$versija = versija();
	// Sarašas failų kurių teisės turi suteikti svetainei įrašymo galimybę
	$chmod_files[0] = "../siuntiniai/media";
	$chmod_files[] = "../sandeliukas";
	$chmod_files[] = "../images/avatars";
	//ką trinam
	$delete_files[] = "../puslapiai/dievai/";
	$delete_files[] = "../javascript/htmlarea/Xinha0.96beta2/";
	$delete_files[] = "../javascript/forum/perview.php";

	// Diegimo stadijų registravimas
	if (!isset($_GET['step']) || empty($_GET['step'])) {
		$_SESSION['step'] = 1;
		$step = 1;
	} else {
		if ($_GET['step'] != 0 && $_GET['step'] > 1) {
			$step = (int) $_GET['step'];
			if ($_SESSION['step'] == ($step - 1)) {
				$_SESSION['step'] = $step;
			}
		} else {
			header("Location: upgrade.php?step=" . $_SESSION['step']);
		}
	}

	// Duomenų bazės prisijungimo tikrinimo ir lentelių sukūrimo dalis
	if (isset($_POST['next_msyql'])) {
		// Sukuriamos visos MySQL leneteles is SVN Trunk
		if (!empty($_POST['sql'])) {
			switch ($_POST['sql']) {
				case '1.28-1.3': {
					$sql = (file_exists('../sql-upgrade.sql') ? file_get_contents('../sql-upgrade.sql') : file_get_contents('http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade.sql'));
					break;
				}
				case '1.3-1.4': {
					$sql = (file_exists('../sql-upgrade-1.3to1.4.sql') ? file_get_contents('../sql-upgrade-1.3to1.4.sql') : file_get_contents('http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade-1.3to1.4.sql'));
					break;
				}

				default: {
					if (versija() >= '1.4') {
						$sql = (file_exists('../sql-upgrade-1.3to1.4.sql') ? file_get_contents('../sql-upgrade-1.3to1.4.sql') : file_get_contents('http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade-1.3to1.4.sql'));
					} elseif (versija() >= '1.3') {
						$sql = (file_exists('../sql-upgrade.sql') ? file_get_contents('../sql-upgrade.sql') : file_get_contents('http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade.sql'));
					}
					break;
				}
			}
		} else {
			if (!file_exists('../sql-upgrade-1.3to1.4.sql')) {
				$sql = file_get_contents('http://code.assembla.com/mightmedia/subversion/node/blob/v1/sql-upgrade-1.3to1.4.sql');
			} else {
				$sql = file_get_contents('../sql-upgrade-1.3to1.4.sql');
			}
		}

		// Paruošiam užklausas
		$sql = str_replace("CREATE TABLE IF NOT EXISTS `", "CREATE TABLE IF NOT EXISTS `" . LENTELES_PRIESAGA, $sql);
		$sql = str_replace("CREATE TABLE `", "CREATE TABLE IF NOT EXISTS `" . LENTELES_PRIESAGA, $sql);
		$sql = str_replace("INSERT INTO `", "INSERT INTO `" . LENTELES_PRIESAGA, $sql);
		$sql = str_replace("ALTER TABLE `", "ALTER TABLE `" . LENTELES_PRIESAGA, $sql);
		$sql = str_replace("UPDATE `", "UPDATE `" . LENTELES_PRIESAGA, $sql);

		// Prisijungiam prie duombazės
		mysql_query("SET NAMES utf8");

		// Atliekam SQL apvalymą
		$match = '';
		preg_match_all("/(?:CREATE|UPDATE|INSERT|ALTER).*?;[\r\n]/s", $sql, $match);

		$mysql_info = "<ol>";
		$mysql_error = 0;
		foreach ($match[0] as $key => $val) {
			if (!empty($val)) {
				$query = mysql_query($val);
				if (!$query) {
					$mysql_info .= "<li><b>Klaida:" . mysql_errno() . "</b> " . mysql_error() . "<hr><b>Užklausa:</b><br/>" . $val . "</li><hr>";
					$mysql_error++;
				}
			}
		}
		$mysql_info .= "</ol>";

		if ($mysql_error == 0) {
			$mysql_info = 'Lentelės sėkmingai atnaujintos. Galite tęsti atnaujinimą.';
			$next_mysql = '<center><input type="reset" value="Toliau" onClick="Go(\'3\');" class="submit"></center>';
		} else {
			$next_mysql = '<center><input type="reset" value="Bandyti dar kartą" onClick="Go(\'2\');" class="submit"></center>';
		}
	}
	if (!isset($next_mysql)) {
		$next_mysql = '<select name="sql">
			<option value="1.28-1.3">1.28 atnaujinimas į 1.3</option>
			<option value="1.3-1.4">1.3 atnaujinimas į 1.4</option>
			</select>
			<br />';
		$next_mysql .= '<input name="next_msyql" type="submit" value="Atnaujinti lenteles" class="submit" style="margin-top: 5px;">';
	}

	// Administratoriaus sukūrimo dalis
	// Diegimo pabaiga
	if (!empty($_POST['finish'])) {

		unlink('upgrade.php');
		//}
		header("Location: ../index.php");
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="shortcut icon" href="../images/favicon.ico" />
		<meta name="resource-type" content="document" />
		<meta name="distribution" content="global" />
		<meta name="author" content="CodeRS - MightMedia TVS" />
		<meta name="copyright" content="copyright (c) by CodeRS www.coders.lt" />
		<meta name="rating" content="general" />
		<meta name="generator" content="notepad" />
	    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />	
	    <link rel="icon" href="favicon.ico" type="image/x-icon" />
		<script src="../javascript/jquery/jquery-1.3.2.min.js" type="text/javascript" ></script>
		<script src="../javascript/jquery/tooltip.js" type="text/javascript" ></script>
		<title>MightMedia TVS/CMS</title>
        <link rel="stylesheet" type="text/css" media="all" href="default.css" />
	</head>
	<body>
<body>
<div id="plotis">
<?php if (isset($_SESSION['id']) && $_SESSION['id'] == 1) {	?>
<div id="kaire">
<div class="skalpas"><a href="?" title="<?php echo adresas(); ?>"><div class="logo"></div></a></div>
<div class='pavadinimas'>Įdiegimo stadijos</div>
<div class='vidus'>
<div class='text'>
<ul>
<?php
$menu_pavad = array(1 => "Failų tikrinimas", 2 => "Duomenų bazės atnaujinimas", 3 => "conf.php keitimas", 4 => "Pabaiga");
foreach ($menu_pavad as $key => $value) {
 if ($key <= $step)
  echo "\t\t\t<li><img src=\"../images/icons/tick_circle.png\" style=\"vertical-align: middle;\" /><font color=\"green\"><b>" . $value . "</b></font></li>";
 else
  echo "\t\t\t<li><img src=\"../images/icons/cross_circle.png\" style=\"vertical-align: middle;\" /><b>" . $value . "</b></li>";
}
?>
</ul>
</div>
</div>
</div>
<div id="kunas">		
<div id="meniu_juosta">MightMedia TVS atnaujinimas</div>
<div id="centras">

<?php if (versija() < $versija)
echo "<div class=\"msg\"><b>Nepakeisti senosios versijos failai!</b><br /> 
<small>Naudodamiesi FTP naršykle, atnaujinkite senos <b>" . versija() . "</b> versijos failus į naująją <b>{$versija}</b> versiją.</div>"; 
?>
<?php
// HTML DALIS - Failų tikrinimas
if ($step == 1) {
?>
<div class='pavadinimas'>Failų tikrinimas</div>
<div class='vidus'>
<div class='text'>
Žemiau surašyti failai, kurių keitimas bus reikalingas atnaujinant
šią sistemą. Jei sistema surado klaidų prašome jas ištaisyti ir
spausti atnaujinti. Kitu atveju jums nebus leidžiama tęsti įdiegimo.
<br />
<br />
<h2>Legenda</h2>
<img src="../images/icons/tick.png" /> Jei prie failo nustatyta ši
ikonėlė vadinasi failas yra paruoštas sistemai.<br />
<img src="../images/icons/cross.png" /> Jei rasite šią ikonėlę prie
nurodyto failo tuomet reikia atlikti užduotį, aprašytą „Klaidos
aprašymas“ stulpelyje.
<br />
<br />
<table border="0" class="table">
<tr>
<th class="th" valign="top" width="10%">Failas</th>
<th class="th" valign="top" width="5%">Būsena</th>
<th class="th" valign="top" width="35%">Klaidos aprašymas</th>
</tr>
<?php
$kartot = count($chmod_files) - 1;
for ($i = 0; $i <= $kartot; $i++) {
$teises = substr(sprintf('%o', fileperms($chmod_files[$i])), -4);
if ($teises != 777 && $teises != 666 && !is_writable($chmod_files[$i])) {
$file_error = 'Y';
}
echo "
<tr class=\"tr\">
<td>" . $chmod_files[$i] . "</td>
<td>" . (($teises == 777) || ($teises == 666) || is_writable($chmod_files[$i]) ? "<img src=\"../images/icons/tick.png\" />" : "<img src=\"../images/icons/cross.png\" />") . "</td>
<td>" . (($teises == 777) || ($teises == 666) || is_writable($chmod_files[$i]) ? "-" : "Būtina nurodyti chmod 777 failui <strong>" . $chmod_files[$i] . "</strong> kadangi esamas chmod yra <strong>" . $teises . "</strong>") . "</td>
</tr>";
}
foreach ($delete_files as $file) {
if (file_exists($file)) {
$file_error = 'Y';
}
echo "
<tr class=\"tr\">
<td>" . $file . "</td>
<td>" . (!file_exists($file) ? "<img src=\"../images/icons/tick.png\" />" : "<img src=\"../images/icons/cross.png\" />") . "</td>
<td>" . (!file_exists($file) ? "-" : "Būtina ištrinti <strong>" . $file . "</strong> ") . "</td>
</tr>";
}
?>
</table>
<br />
<br />
<?php
if (isset($file_error) && $file_error == 'Y')
echo '<center><input type="reset" class="submit" value="Atnaujinti" onClick="JavaScript:location.reload(true);"> <input type="reset" class="submit" value="Jeigu esate isitikines, kad viskas gerai" onClick="Go(\'2\');"><center>';
else
echo '<center><input type="reset" class="submit" value="Toliau" onClick="Go(\'2\');"></center>';
}
//END
// HTML DALIS - MySQL duomenų bazės nustatymai
if ($step == 2) {
?>
<div class='pavadinimas'>MySQL Duomenų bazės atnaujinimas</div>
<div class='vidus'>
<div class='text'>
Norėdami atnaujinti lenteles, spauskite mygtuką, esantį žemiau.
<form name="mysql" method="post" action="?step=2">
<p id="mysql_response" style="text-align: center"><?php echo $next_mysql; ?></p>
<?php if (isset($mysql_info)):?> <br />
<table border="0" width="50%">
<tr>
<td class="title"><b>Informacija</b></td>
</tr>
<tr>
<td>
<div id="info"><?php echo $mysql_info; ?></div>
</td>
</tr>
</table>
<?php endif; ?></form>
<?php
}
//END
// HTML DALIS - TVS administratoriaus sukūrimas
if ($step == 3) {
?>
<div class='pavadinimas'>conf.php failo keitimas</div>
<div class='vidus'>
<div class='text'>
Atlikite žemiau nurodytus pakeitimus <i>priedai/conf.php</i> faile.
<h2>Legenda</h2>
<img src="../images/icons/tick.png" /> Jei prie užduoties matote šią
ikoną, ji atlikta teisingai. <br />
<img src="../images/icons/cross.png" /> Jei prie užduoties matote šią
ikoną, ji atlikta neteisingai. <br />
<hr />
<br />
<div class="tr">
<img src="<?php echo (isset($update_url) ? '../images/icons/tick.png' : '../images/icons/cross.png'); ?>" />
priedai/conf.php faile, prieš 
<input type="text" value='$prisijungimas_prie_mysql = mysql_connect($host, $user, $pass)' />
įklijuokite šį kodą 
<input type="text" value='$update_url = "http://www.assembla.com/code/mightmedia/subversion/node/blob/naujienos.json?jsoncallback=?";' />
</div>
<div class="tr">
<img src="<?php echo (!function_exists('lentele') ? '../images/icons/tick.png' : '../images/icons/cross.png'); ?>" />
Failo apačioje ištrinkite kodą 
<input type="text" value='require_once(realpath(dirname(__file__))."/../stiliai/".$conf[&#039;Stilius&#039;]."/sfunkcijos.php");' />
</div>
<div class="tr">
Atlikę šias užduotis spauskite „Atnaujinti“ ir
įsitikinlite, kadužduotys atliktos teisingai (žr. į ikonas šalia
užduoties)
</div>
<center>
<input type="reset" value="Atnaujinti" class="submit"  onclick="JavaScript:location.reload(true);" />
<input type="reset" value="Toliau" class="submit"  onclick="Go('3');" />
</center>
<?php
}
// HTML DALIS - Pabaiga
if ($step == 4) {
?>
<div class='pavadinimas'>Pabaiga</div>
<div class='vidus'>
<div class='text'>
Sveikiname įdiegus MightMedia TVS (Turinio Valdymo Sistemą).<br />
Spauskite "Pabaigti" galutinai užbaigti instaliaciją. Bus ištrintas <b>upgrade.php</b>
failas. Dėl visa ko - patikrinkite prisijungę prie FTP serverio.<br />
<br />
<form name="finish_install" method="post">
<center>
<input name="finish" type="submit" value="Pabaigti" class="submit" />
</center>
</form>
<?php
}
//END
?>

<script type="text/javascript">
	function Go(id) {
		document.location.href = "upgrade.php?step="+id;
	}
</script> 


</div>
</div>
</div>
<div class="sonas"></div>
<div id="kojos">
<div class="tekstas">MightMedia CMS / MightMedia TVS </div>
<a href="http://mightmedia.lt" target="_blank" title="Mightmedia"><div class="logo"></div></a>
</div>
</div>
<?php } else { ?>

<?php } ?>
</div>
<div id="another"class="clear"><div class="lygiuojam"><div class="taisom"></div></div></div>
</body>
</html>
<?php ob_end_flush(); ?>
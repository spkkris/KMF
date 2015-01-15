<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Plik: download_center_panel_admin.php
| Autor: krystian1988
| Wersja: 2.01
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "../../maincore.php";
if (!checkrights("KMFD") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }
require_once THEMES."templates/admin_header.php";
if (file_exists(INFUSIONS."download_center_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."download_center_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."download_center_panel/locale/Polish.php";
}
include INFUSIONS."download_center_panel/infusion_db.php";
include INFUSIONS."download_center_panel/version_check.php";
if (isset($_GET['status']) && !isset($message)) {
	if ($_GET['status'] == "su") {
	$message = $locale['ok'];
	} elseif ($_GET['status'] == "nsu") {
	$message = $locale['blad'];
	}elseif ($_GET['status'] == "nsue") {
	$message = $locale['blad1'];
	}
	if ($message) {	echo "<div id='close-message'><div class='admin-message'>".$message."</div></div>\n"; }
	}
if (isset($_POST['zapisz'])) {
			
	if (is_numeric($_POST['ile']) &&  is_numeric($_POST['slider'])) {
	$result = dbquery("UPDATE ".DB_KMF_DCP." SET ile='".(isNum($_POST['ile']) ? $_POST['ile'] : "0")."', slider='".(isNum($_POST['slider']) ? $_POST['slider'] : "0")."'");	
	if (!$result) { 
	redirect(FUSION_SELF.$aidlink."&status=nsu");
	} else {
	redirect(FUSION_SELF.$aidlink."&status=su");
	}
	}else{
	redirect(FUSION_SELF.$aidlink."&status=nsue");
	} 
	}
$kmfu_ustawienia = dbarray(dbquery("SELECT * FROM ".DB_KMF_DCP));
	$ile = isNum($kmfu_ustawienia['ile']);
	$slider = isNum($kmfu_ustawienia['slider']);
	$wlacz = "<img src='".INFUSIONS."download_center_panel/img/on.png' alt='".$locale['wlacz']."' class='admin-icons'/>";
	$wylacz = "<img src='".INFUSIONS."download_center_panel/img/off.png' alt='".$locale['wylacz']."' class='admin-icons'/>";
	$zapisz = "<div style='text-align:center;margin-top:10px;margin-bottom:10px;'><input type='submit' class='button' name='zapisz' value='".$locale['zapisz']."' /></div>";
	
opentable($locale['admin']);
echo"<div style='text-align: center;' class='admin-message center'>".$locale['admin']."</div>";
        echo "<form name='kmfu_form' method='post' action='".FUSION_SELF.$aidlink."'>\n";
		echo "<table cellpadding='0' cellspacing='0' width='100%' style='margin-top: 10px;' class='tbl-border center'>\n<tr>\n";
		echo "<td  class='tbl' width='50%' align='left'>".$locale['019']."</td>\n";
		echo "<td  align='left' class='tbl'><input name='ile' type='number' min='1' max='255' style='width: 70px' value='".$kmfu_ustawienia['ile']."' class='textbox'>\n";
		echo "</input>* Max. 255";
		echo "</td></tr><tr>\n";
		echo "<td  class='tbl' width='50%' align='left'>".$locale['023']."</td>\n";
		echo "<td  align='left' class='tbl'><select name='slider' class='textbox'>\n";
		echo "<option value='1'".($kmfu_ustawienia['slider'] == "1" ? " selected='selected'" : "").">".$locale['wlacz']."</option>\n";
		echo "<option value='0'".($kmfu_ustawienia['slider'] == "0" ? " selected='selected'" : "").">".$locale['wylacz']."</option>\n";
		echo "</select>";
		echo ($kmfu_ustawienia['slider'] == 1 ? $wlacz : $wylacz);
		echo "</td></tr><tr>\n";
		echo"</table>\n";
		echo $zapisz;
		echo "</form>\n";
closetable();

require_once THEMES."templates/footer.php";
?>
<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: version_check.php
| Author: krystian1988
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
add_to_head("<style type='text/css'>
.status, .info, .error, .valid {
position: relative;
margin: 6px 0 0;
padding: 8px;
border-radius: 8px;
font-size: 12px;
cursor: pointer;
text-align: center;
}
div.status div, div.info div, div.error div, div.valid div {margin:0 65px 0 10px;}
div.status a, div.info a, div.error a, div.valid a {font-weight:normal;text-decoration:underline;}
.status-right, .info-right, .error-right, .valid-right {width:55px;position:absolute;top:0;right:-6px;}
.status-right a, .info-right a, .error-right a, .valid-right a {cursor:pointer;}
div.status {background:url(img/messages-sprite.png) no-repeat 0 -70px;color:#e79300;}
div.status a {color:#e79300;}
div.info {background:url(img/messages-sprite.png) no-repeat 0 -105px;color:#2e74b2;}
div.info a {color:#2e74b2;}
div.error	{background: url(img/messages-sprite.png) no-repeat 0 -35px;color:#ce2700;}
div.error a {color:#ce2700;}
div.valid {background: url(img/messages-sprite.png) no-repeat 0 0;color: #6da827;}
div.valid a {color:#6da827;}</style>");
opentable($locale['ver_000']);
echo "<div align='center'>";
if(ini_get('allow_url_fopen') != false){ 
	if ($df_ddi_version = file_get_contents("http://www.krismods-fusion.pl/updates/download_center_panel.txt")) {
		if (preg_match("/^[0-9a-z\.]+$/", $df_ddi_version)) {
		    $in_version = dbarray(dbquery("SELECT inf_version FROM ".DB_INFUSIONS." WHERE inf_folder = 'download_center_panel'"));	
            $inf_version = $in_version['inf_version'];
		    if (version_compare($df_ddi_version, $inf_version, ">")) {
			echo "<div class='info'>".$locale['ver_001']."</div>
			".$locale['ver_002'].$inf_version."<br />
			".$locale['ver_003'].$df_ddi_version."<br />
			".$locale['ver_004']."";
		} else {
			echo "<div class='valid'>".$locale['ver_005']."</div>
			".$locale['ver_006'].$inf_version."<br />
			".$locale['ver_007']; }
		} else {
			echo "<div class='error'>".$locale['ver_008']."</div>\n
			<div class='tbl1' style='display:none;'>".$locale['ver_009']."</div>"; }
	    } else {
		    echo "<div class='error'>".$locale['ver_008']."</div></b>\n
		    <div class='tbl1' style='display:none;'>".$locale['ver_009']."</div>"; }
        } else {
	        echo "<div class='error'>".$locale['ver_008']."</div>\n
		    <div class='tbl1' style='display:none;'>".$locale['ver_010']."</div>"; }
            echo "</div>\n";
closetable();

?>

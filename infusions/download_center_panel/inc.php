<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Plik: inc.php
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
if (!defined("IN_FUSION")) { die("Access Denied"); }
function autor() {
$www = "http://www.krismods-fusion.pl";
$copy = "Copyright KriS Mods Fusion 2014";
echo "<div align='right'><a href='".$www."' target='_blank' title='".$copy."'>©</a></div>";
}

?>
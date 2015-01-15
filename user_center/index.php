<?php
require_once "../maincore.php";
require_once CLASSES."UserFields.class.php";
require_once CLASSES."UserFieldsInput.class.php";
include LOCALE.LOCALESET."members.php";
include LOCALE.LOCALESET."user_fields.php";
require_once THEMES."templates/header.php";
if (iMEMBER){

add_to_title(" - Konto Użytkownika");
opentable($userdata['user_name']." - Twoje Konto");
$podglad = "<img src='".UC."img/edytuj.png' border='0' >";
$podglad1 = "<img src='".UC."img/wiadomosci.png' border='0' >";
$podglad2 = "<img src='".UC."img/uzytkownicy.png' border='0' >";
$podglad3 = "<img src='".UC."img/wyloguj.png' border='0' >";
$podglad4 = "";
$podglad5 = "";
$podglad6 = "";
$podglad7 = "";
include_once INFUSIONS."shoutbox_panel/infusion_db.php";
echo "<table cellpadding='0' cellspacing='4' style='padding-top: 6px;' width='100%' align='center'><tr>";
if ($userdata['user_avatar'] && file_exists(IMAGES."avatars/".$userdata['user_avatar']) && $userdata['user_status']!=6 && $userdata['user_status']!=5) {
			echo "<td class='tbl1' style='width: 20%;' align='center' valign='top'><img src='".IMAGES."avatars/".$userdata['user_avatar']."' /></td>\n";
		} else {
			echo "<td class='tbl1' style='width: 20%;' align='center' valign='top'><img src='".IMAGES."avatars/noavatar100.png'  /></td>\n";
		}
		
		echo "<td class='tbl1' style='width: 60%;' align='left' valign='top'>
		<b>Komentarzy:</b> ".number_format(dbcount("(comment_id)", DB_COMMENTS, "comment_name='".$userdata['user_id']."'"))."<br />
		<b>Wpisów w SB:</b> ".number_format(dbcount("(shout_id)", DB_SHOUTBOX, "shout_name='".$userdata['user_id']."'"))."</br />
		<b>Postów:</b> ".number_format($userdata['user_posts'])."</br />
		<b>Twoje IP:</b> ".USER_IP."<br />
		<b>Data Rejestracji:</b> ".showdate("longdate", $userdata['user_joined'])."<br />
		<b>Twoja ostatnia wizyta:</b> ".showdate("longdate", $userdata['user_lastvisit'])."
		</td>";
echo "<td class='tbl1' style='width: 20%;' align='center' valign='top'><br /><a href='".BASEDIR."index.php?logout=yes'>".$podglad3."<br /><b>Wyloguj</b></a></td>";
		echo "</tr></table>";
		
//pierwsza tabela na 4
//echo "<table cellpadding='0' cellspacing='4' style='padding-top: 6px;' width='100%' align='center'><tr>";
//echo "<td class='tbl1' style='width: 10%;' align='center' valign='top'><a href='#'>".$podglad."<br /><b>Edytuj Profil</b></a></td>";
//echo "<td class='tbl2' style='width: 10%;' align='center' valign='top'><a href='#'>".$podglad1."<br /><b>Wiadomości</b></a></td>";
//echo "<td class='tbl1' style='width: 10%;' align='center' valign='top'><a href='#'>".$podglad2."<br /><b>Lista Użytkowników</b></a></td>";
//echo "<td class='tbl2' style='width: 10%;' align='center' valign='top'><a href='#'>".$podglad3."<br /><b>Wyloguj</b></a></td>";
//echo "</tr></table>";

//zakładki tabs

 echo '<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">
<script>
$(function() {
$( "#tabs" ).tabs();
});
</script>
</head>
<body>
<div id="tabs">
<ul>
<li><a href="#tematy">Obserwowane Tematy</a></li>
<li><a href="#lista">Lista Użytkowników</a></li>
</ul>
<div id="tematy">';
if (isset($_GET['delete']) && isnum($_GET['delete']) && dbcount("(thread_id)", DB_THREAD_NOTIFY, "thread_id='".$_GET['delete']."' AND notify_user='".$userdata['user_id']."'")) {
	$result = dbquery("DELETE FROM ".DB_THREAD_NOTIFY." WHERE thread_id=".$_GET['delete']." AND notify_user=".$userdata['user_id']);
	redirect(FUSION_SELF);
}

if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }

//echo "<div class='info'>".$locale['global_056']."</div>";

$result = dbquery(
	"SELECT tn.thread_id FROM ".DB_THREAD_NOTIFY." tn
	INNER JOIN ".DB_THREADS." tt ON tn.thread_id = tt.thread_id
	INNER JOIN ".DB_FORUMS." tf ON tt.forum_id = tf.forum_id
	WHERE tn.notify_user=".$userdata['user_id']." AND ".groupaccess('forum_access')." AND tt.thread_hidden='0'"
);
$rows = dbrows($result);

if ($rows) {
	$result = dbquery("
		SELECT tf.forum_access, tn.thread_id, tn.notify_datestamp, tn.notify_user,
		tt.thread_subject, tt.forum_id, tt.thread_lastpost, tt.thread_lastuser, tt.thread_postcount,
		tu.user_id AS user_id1, tu.user_name AS user_name1, tu.user_status AS user_status1, 
		tu2.user_id AS user_id2, tu2.user_name AS user_name2, tu2.user_status AS user_status2
		FROM ".DB_THREAD_NOTIFY." tn
		INNER JOIN ".DB_THREADS." tt ON tn.thread_id = tt.thread_id
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id = tf.forum_id
		LEFT JOIN ".DB_USERS." tu ON tt.thread_author = tu.user_id
		LEFT JOIN ".DB_USERS." tu2 ON tt.thread_lastuser = tu2.user_id
		INNER JOIN ".DB_POSTS." tp ON tt.thread_id = tp.thread_id
		WHERE tn.notify_user=".$userdata['user_id']." AND ".groupaccess('forum_access')." AND tt.thread_hidden='0'
		GROUP BY tn.thread_id
		ORDER BY tn.notify_datestamp DESC
		LIMIT ".$_GET['rowstart'].",10
	");
	echo "<table class='tbl-border' cellpadding='0' cellspacing='1' width='100%'>\n<tr>\n";
	echo "<td class='tbl2'><strong>".$locale['global_044']."</strong></td>\n";
	echo "<td class='tbl2' style='text-align:center;white-space:nowrap'><strong>".$locale['global_050']."</strong></td>\n";
	echo "<td class='tbl2' style='text-align:center;white-space:nowrap'><strong>".$locale['global_047']."</strong></td>\n";
	echo "<td class='tbl2' style='text-align:center;white-space:nowrap'><strong>".$locale['global_046']."</strong></td>\n";
	echo "<td class='tbl2' style='text-align:center;white-space:nowrap'><strong>".$locale['global_057']."</strong></td>\n";	
	echo "</tr>\n";
	$i = 0;
	while ($data = dbarray($result)) {
		$row_color = ($i % 2 == 0 ? "tbl1" : "tbl2");
		echo "<tr>\n<td class='".$row_color."'><a href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."'>".$data['thread_subject']."</a></td>\n";
		echo "<td class='".$row_color."' style='text-align:center;white-space:nowrap'>".profile_link($data['user_id1'], $data['user_name1'], $data['user_status1'])."</td>\n";
		echo "<td class='".$row_color."' style='text-align:center;white-space:nowrap'>".profile_link($data['user_id2'], $data['user_name2'], $data['user_status2'])."<br />
		".showdate("forumdate", $data['thread_lastpost'])."</td>\n";
		echo "<td class='".$row_color."' style='text-align:center;white-space:nowrap'>".($data['thread_postcount']-1)."</td>\n";
		echo "<td class='".$row_color."' style='text-align:center;white-space:nowrap'><a href='".FUSION_SELF."?delete=".$data['thread_id']."' onclick=\"return confirm('".$locale['global_060']."');\">".$locale['global_058']."</a></td>\n";
		echo "</tr>\n";
		$i++;
	}
	echo "</table>\n";
	
	echo "<div align='center' style='margin-top:5px;'>".makePageNav($_GET['rowstart'],10,$rows,3,FUSION_SELF."?")."</div>\n";
} else {
	echo "<div class='valid'>".$locale['global_059']."</div>";
	
}
echo '</div>
<div id="lista">';
if (iMEMBER) {
	if (!isset($_GET['sortby']) || !ctype_alnum($_GET['sortby'])) { $_GET['sortby'] = "all"; }
	$orderby = ($_GET['sortby'] == "all" ? "" : " AND user_name LIKE '".stripinput($_GET['sortby'])."%'");
	$result = dbquery("SELECT user_id FROM ".DB_USERS." WHERE user_status='0'".$orderby);
	$rows = dbrows($result);
	if (!isset($_GET['rowstart']) || !isnum($_GET['rowstart'])) { $_GET['rowstart'] = 0; }
	if ($rows) {
		$i = 0;
		echo "<table cellpadding='0' cellspacing='0' width='100%'>\n<tr>\n";
		echo "<td class='tbl2'><strong>".$locale['401']."</strong></td>\n";
		echo "<td class='tbl2'><strong>".$locale['405']."</strong></td>\n";
		echo "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><strong>".$locale['402']."</strong></td>\n";
		echo "</tr>\n";
		$result = dbquery("SELECT user_id, user_name, user_status, user_level, user_groups FROM ".DB_USERS." WHERE user_status='0'".$orderby." ORDER BY user_level DESC, user_name LIMIT ".$_GET['rowstart'].",20");
		while ($data = dbarray($result)) {
			$cell_color = ($i % 2 == 0 ? "tbl1" : "tbl2"); $i++;
			echo "<tr>\n<td class='$cell_color'>\n".profile_link($data['user_id'], $data['user_name'], $data['user_status'])."</td>\n";
			$groups = "";
			$user_groups = explode(".", $data['user_groups']);
			$j = 0;
			foreach ($user_groups as $key => $value) {
				if ($value) {
					$groups .= "<a href='profile.php?group_id=".$value."'>".getgroupname($value)."</a>".($j < count($user_groups)-1 ? ", " : "");
				}
				$j++;
			}
			echo "<td class='$cell_color'>\n".($groups ? $groups : ($data['user_level'] == 103 ? $locale['407'] : $locale['406']))."</td>\n";
			echo "<td align='center' width='1%' class='$cell_color' style='white-space:nowrap'>".getuserlevel($data['user_level'])."</td>\n</tr>";
		}
		echo "</table>\n"; 
	} else {
		echo "<div style='text-align:center'><br />\n".$locale['403'].$_GET['sortby']."<br /><br />\n</div>\n";
	}
	$search = array(
		"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R",
		"S","T","U","V","W","X","Y","Z","0","1","2","3","4","5","6","7","8","9"
	);
	echo "<hr />\n<table cellpadding='0' cellspacing='1' class='tbl-border center'>\n<tr>\n";
	echo "<td rowspan='2' class='tbl2'><a href='".FUSION_SELF."?sortby=all'>".$locale['404']."</a></td>";
	for ($i = 0; $i < 36 != ""; $i++) {
		echo "<td align='center' class='tbl1'><div class='small'><a href='".FUSION_SELF."?sortby=".$search[$i]."'>".$search[$i]."</a></div></td>";
		echo ($i == 17 ? "<td rowspan='2' class='tbl2'><a href='".FUSION_SELF."?sortby=all'>".$locale['404']."</a></td>\n</tr>\n<tr>\n" : "\n");
	}
	echo "</tr>\n</table>\n";
} else {
	redirect("index.php");
}

if ($rows > 20) { echo "<div align='center' style='margin-top:5px;'>".makepagenav($_GET['rowstart'], 20, $rows, 3, FUSION_SELF."?sortby=".$_GET['sortby']."&amp;")."</div>\n"; }
echo '</div>
</div>';






//koniec zakładek


closetable();
} else {
add_to_title(" - UPSSSSSS");
opentable("UPSSSSSS");
echo "<div class='error'>Aby wejść do Konta Użytkownika proszę się <a href='".BASEDIR."login.php'>zalogować</a></div>";
closetable();
}
require_once THEMES."templates/footer.php";
?>
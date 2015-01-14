<?php
/* 
| Main RSS construction file
| W0rst RSS by PolarFox aka Psc | unlogic.info | Build - see below 
*/
define('THIS_BUILD','3');
require_once '../maincore.php';

//settings
$trim_to = 0; //trim description to X symbols? 0 - disabled 

//===minicore
//filter
$_GET['feed'] = isset($_GET['feed'])?stripinput(basename($_GET['feed'])):'';

function shortstr($desc,$trim_to=0) {
	if (($trim_to != 0) && (strlen($desc) > $trim_to)) { $desc = substr($desc, 0, ($trim_to-3)).'...'; }
	return $desc;
}

if(!function_exists('add_to_head')) { //dummy - rss can't parse head
	function add_to_head($any) {unset($any);return true;}
}
function rssDate($time=0){
	$time = ($time==0 ? time() : $time);
	return date(DATE_RSS, $time);
}
function set_fpath($pathto) {global $settings;
	if(strstr($pathto, 'http://')||strstr($pathto, 'ftp://')) {return $pathto;} //already ready
	return $settings['siteurl'].str_replace('../', '', $pathto);
}
function fix_intxt($text) { //slowly preg...
	$text = str_replace(array('href="/','href=\'/','src="/','src=\'/',']]>'), 
				array('href="','href=\'','src="','src=\'',''), $text); //remove doubleslash + ]]>
	$text = preg_replace('#href=(["\'])(.*?)(["\'])#sie', "stripslashes('href=\\1'.set_fpath('\\2').'\\3')", $text);
	$text = preg_replace('#src=(["\'])(.*?)(["\'])#sie', "stripslashes('src=\\1'.set_fpath('\\2').'\\3')", $text);
	return $text;
}
function rss_p_head($title) { //removed (after <lang>) <pubDate>'.rssDate().'</pubDate> <ttl>360</ttl>
global $settings,$locale;
$title = stripslashes(htmlspecialchars($title.' | '.$settings['sitename']));
$desc = stripslashes(htmlspecialchars($settings['description']));
	return '<?xml version="1.0" encoding="'.$locale['charset'].'"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
  <atom:link href="'.$settings['siteurl'].'rss/'.(!empty($_GET['feed'])?'?feed='.$_GET['feed']:'').'" rel="self" type="application/rss+xml" />
    <title>'.$title.'</title>
		<image>
		<url>'.$settings['siteurl'].$settings['sitebanner'].'</url>
		<title>'.$title.'</title>
		<link>'.$settings['siteurl'].'</link>
		</image>
    <link>'.$settings['siteurl'].'</link>
    <description>'.$desc.'</description>
    <language>'.$locale['xml_lang'].'-'.$locale['xml_lang'].'</language>	
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
    <generator>'.$settings['siteurl'].' - unLogic RSS creation kit (b. '.THIS_BUILD.')</generator>
	<copyright>'.$settings['siteurl'].'</copyright>';
}
function rss_p_item ($title,$link,$desc,$pubdate,$guid,$category='',$comments='') {global $settings,$trim_to;
$link = set_fpath($link);
$desc = fix_intxt($desc);
$title= !empty($title)?htmlspecialchars(stripslashes($title)):'unnamed_'.$guid;
$category = (!empty($category)? '<category>'.htmlspecialchars(stripslashes($category)).'</category>':'');//)
$pubdate = rssDate($pubdate);

return '<item>
      <title>'.shortstr($title,100).'</title>
	  <guid isPermaLink="false">'.$guid.'</guid>
      <link>'.$link.'</link>
      <description><![CDATA['.shortstr($desc,$trim_to).']]></description>
      <pubDate>'.$pubdate.'</pubDate>
	  '.$category.'	
	  <comments>'.set_fpath($comments).'</comments>
    </item>';
}
function rss_p_noitem() {
	return rss_p_item ('No items yet','','This RSS feed is empty.','no_item',time(),'No Items','');
}

function rss_p_footer() {
	return '</channel>
		</rss>';
}

//to client
$ready_rss = array('news','comments','forum','articles','photogallery','downloads','weblinks');

if(!empty($_GET['feed']) && in_array($_GET['feed'],$ready_rss)) { $inc_rsspage = $_GET['feed']; }
else { $inc_rsspage = $ready_rss[0]; }

header('Content-Type: application/rss+xml; charset='.$locale['charset']);
include_once(BASEDIR.'rss/'.$inc_rsspage.'.php');

//footer inc
if (ob_get_length() !== FALSE){ob_end_flush();}
mysql_close($db_connect);
?>
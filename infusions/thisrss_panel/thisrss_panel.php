<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright © 2002 - 2008 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: thisrss_panel.php
| Author: PolarFox aka Psc | Build: 5 | unlogic.info
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined('IN_FUSION')) { die('Access Denied'); }
///+news cats

$t = INFUSIONS.'thisrss_panel/locale/';$i = $settings['locale'].'.php';
include (file_exists($t.$i) ? $t.$i : $t.'English.php');

//=== config
$thisrss_c['silent_mode'] = false;//visible or not
$thisrss_c['rss_smicon'] = '<img src="'.INFUSIONS.'thisrss_panel/feed-icon-12x12.png" alt="RSS" border="0" />';
$thisrss_c['rss_hr'] = '<hr class="side"/>';
$thisrss_c['allow_links'] = array(//allow? these links
'n'=>true,//news
'c'=>false,//comments
'ct'=>false,//comments - cat
'cc'=>false,//comments - branch
'f'=>true,//forum
'ff'=>true,//forum + forum
'ft'=>true,//forum + thread
'a'=>false,//articles
'p'=>false,//photos
'd'=>true,//downloads
'w'=>true,//weblinks
);
//=== end

//internal data - careful!
$thisrss_c['data_clinks'] = array ( 
	'news.php' => 'N|readmore',
	'viewpage.php' => 'C|page_id',
	'articles.php' => 'A|article_id',
	'photogallery.php' => 'P|photo_id',
	'videos.php' => 'V|view');	
$thisrss_c['data_flinks'] = array ( 
	'viewforum.php' => 'forum_id',
	'viewthread.php' => 'thread_id');			 
$thisrss_c['data_staticlinks'] = array(
	array('',$locale['trss_news'],'n'),
	array('feed=comments',$locale['trss_comm'],'c'),
	array('feed=forum',$locale['trss_forum'],'f'),
	array('feed=articles',$locale['trss_art'],'a'),
	array('feed=photogallery',$locale['trss_photo'],'p'),
	array('feed=downloads',$locale['trss_down'],'d'),
	array('feed=weblinks',$locale['trss_link'],'w')
	);

//init
$thisrss_c['temp_head'] = '';$thisrss_c['temp_panel'] = '';

if (!defined('trssMAINPATH')) {
	define('trssMAINPATH',BASEDIR.'rss/');
	
	function html_rsslink($link,$title,$type=1,$check=false,$class='side'){//type : 1 = a , 2 = link
		global $thisrss_c,$locale;
		
		if($check && isset($thisrss_c['allow_links'][$check]) && !$thisrss_c['allow_links'][$check]){
			return false;}//hide the link
		if($class!=''){
			$class = ' class="'.$class.'"';}
		if($link!=''){
			$link='?'.$link;}		
		$type = (int)$type;
		
		switch ($type) {
			case 2:
				return '<link rel="alternate" type="application/rss+xml" href="'.trssMAINPATH.$link.'" title="'.$title.'" />';
				break;
			case 1:
			default:
				return '<a type="application/rss+xml" '.$class.' href="'.trssMAINPATH.str_replace('&','&amp;',$link).'" title="'.$title.'">'.$thisrss_c['rss_smicon'].' '.$title.'</a>';
		}
	}	
}

//extended comments
if(array_key_exists(FUSION_SELF,$thisrss_c['data_clinks'])) {
	$cl_data = explode('|',$thisrss_c['data_clinks'][FUSION_SELF]);
	if(isset($_GET[$cl_data[1]])) {
		$thisrss_c['temp_head'][] = html_rsslink('feed=comments&c='.$cl_data[0].'&n='.stripinput($_GET[$cl_data[1]]),$locale['trss_comm'].': '.$locale['trss_tcomm'],2,'ct');
		$thisrss_c['temp_head'][] = html_rsslink('feed=comments&c='.$cl_data[0],$locale['trss_comm'].': '.$locale['trss_ccomm'],2,'cc');
		if(!$thisrss_c['silent_mode']){
			$thisrss_c['temp_panel'][] = html_rsslink('feed=comments&c='.$cl_data[0].'&n='.stripinput($_GET[$cl_data[1]]),$locale['trss_comm'].': '.$locale['trss_tcomm'],1,'ct');
			$thisrss_c['temp_panel'][] = html_rsslink('feed=comments&c='.$cl_data[0],$locale['trss_comm'].': '.$locale['trss_ccomm'],1,'cc');
			if($thisrss_c['allow_links']['ct']||$thisrss_c['allow_links']['cc']){
				$thisrss_c['temp_panel'][] = $thisrss_c['rss_hr'];}
		}
	}
}

//extended posts
if(array_key_exists(FUSION_SELF,$thisrss_c['data_flinks'])) {
	if(isset($_GET[$thisrss_c['data_flinks'][FUSION_SELF]]) && isnum($_GET[$thisrss_c['data_flinks'][FUSION_SELF]])) {
		if($thisrss_c['data_flinks'][FUSION_SELF] == 'forum_id'){
			$thisrss_c['temp_head'][] = html_rsslink('feed=forum&f='.stripinput($_GET['forum_id']),$locale['trss_forum'].': '.$locale['trss_fforum'],2,'ff');}
		else if($thisrss_c['data_flinks'][FUSION_SELF] == 'thread_id'){
			$thisrss_c['temp_head'][] = html_rsslink('feed=forum&t='.stripinput($_GET['thread_id']),$locale['trss_forum'].': '.$locale['trss_tforum'],2,'ft');}
		if(!$thisrss_c['silent_mode']){
			if($thisrss_c['data_flinks'][FUSION_SELF] == 'forum_id'){
				$thisrss_c['temp_panel'][] = html_rsslink('feed=forum&f='.stripinput($_GET['forum_id']),$locale['trss_forum'].': '.$locale['trss_fforum'],1,'ff');}
			else if($thisrss_c['data_flinks'][FUSION_SELF] == 'thread_id'){
				$thisrss_c['temp_panel'][] = html_rsslink('feed=forum&t='.stripinput($_GET['thread_id']),$locale['trss_forum'].': '.$locale['trss_tforum'],1,'ft');}
			if($thisrss_c['allow_links']['ff']||$thisrss_c['allow_links']['ft']){
				$thisrss_c['temp_panel'][] = $thisrss_c['rss_hr'];}
		}
	}
}

for ($i = 1; $i <= 2; $i++) {//link + a href
	$t = ($i==2?'temp_head':'temp_panel');//2 link ; 1 a
	
	$ti = (count($thisrss_c['data_staticlinks']) - 1);//a lot of links (*2)
	for($ii = 0; $ii <= $ti ; $ii++ ){	
		$thisrss_c[$t][] = html_rsslink($thisrss_c['data_staticlinks'][$ii][0],$thisrss_c['data_staticlinks'][$ii][1],$i,$thisrss_c['data_staticlinks'][$ii][2]);
	}
}

//output
add_to_head(implode('',$thisrss_c['temp_head']));

if(!$thisrss_c['silent_mode']){
	openside($locale['trss_title']);
	
	foreach ($thisrss_c['temp_panel'] as &$link) {
		if($link){
			echo $link.
				(!strstr($link, '<hr')?'<br/>':'');}
	}
	
	closeside();
}
?>
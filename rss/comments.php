<?php
//build: 1+
if (!defined('IN_FUSION')) { header('Location: index.php');exit; }

$comm_title = 'Komentarze';
$items_per_page = 30;
$allow_get = true;

if( isset($_GET['c']) ) { $comm_cat = stripinput($_GET['c']);} 
else {$comm_cat = '';}
if( isset($_GET['n']) && isNum($_GET['n']) ) { $comm_itid = $_GET['n'];} 
else {$comm_itid = '';}

if(!$allow_get) {$comm_cat=''; $comm_itid='';}

echo rss_p_head($comm_title);

$clinks_arr = array ( 'N' => 'news.php?readmore=',
					 'C' => 'viewpage.php?page_id=',
					 'A' => 'articles.php?article_id=',
					 'P' => 'photogallery.php?photo_id=',
					 'V' => 'infusions/video_infusions/videos.php?view=',
					 'r' => 'infusions/roadmap/roadmap.php?view=');

$ctinc_arr = array ( 'N' => 'news.news_subject.news_id',
					 'C' => 'custom_pages.page_title.page_id',
					 'A' => 'articles.article_subject.article_id',
					 'P' => 'photos.photo_title.photo_id');	

$dbq_ok = false;
if(isset($_GET['c'])&&!isset($_GET['n'])&&isset($ctinc_arr[$_GET['c']])) {
$comm_dbq = explode('.',stripinput($ctinc_arr[$_GET['c']])); // db_table.title_row.to_link_row
if($comm_dbq[0]!='' && $comm_dbq[1]!='' && $comm_dbq[2]!='' ) {$dbq_ok = true;}
} 

$sql = dbquery(
		"SELECT tcm.comment_type, tcm.comment_name, tcm.comment_message, tcm.comment_id, tcm.comment_item_id,  tcm.comment_datestamp, tcu.user_name, tcu.user_status
		".($dbq_ok?',tex.'.$comm_dbq[1].' as cthis_title':'')." FROM ".DB_COMMENTS." tcm
		LEFT JOIN ".DB_USERS." tcu ON tcm.comment_name=tcu.user_id
		".($dbq_ok?'LEFT JOIN '.DB_PREFIX.$comm_dbq[0].' tex ON tcm.comment_item_id=tex.'.$comm_dbq[2]:'')."
		WHERE comment_hidden='0'".($comm_cat!=''?" AND comment_type='$comm_cat'":'').($comm_itid!=''&&$comm_cat!=''?" AND comment_item_id='$comm_itid'":'')."
		ORDER BY comment_datestamp DESC LIMIT 0,$items_per_page");
		
if(dbrows($sql)) {
while($data = dbarray($sql)) {
$path_to = (isset($clinks_arr[$data['comment_type']])?$clinks_arr[$data['comment_type']]:'?UNKNOWN_'.$data['comment_type'].'=');		
echo rss_p_item (	(isset($data['cthis_title'])?$data['cthis_title']:(isset($data['user_name'])?$data['user_name']:$data['comment_name'])),
					   BASEDIR.$path_to.$data['comment_item_id'],
					   (isset($data['cthis_title'])?(isset($data['user_name'])?'<p><strong>- '.profile_link($data['comment_name'],$data['user_name'],$data['user_status']):$data['comment_name']).'</strong></p>':'').
							nl2br(parseubb(parsesmileys($data['comment_message']))),
					   $data['comment_datestamp'],
					   'c_'.$data['comment_id'],
					   $data['comment_type'],
					   '');
}
}
else {  echo rss_p_noitem(); }

echo rss_p_footer();
?>

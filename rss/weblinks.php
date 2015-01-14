<?php
//build: 2
if (!defined('IN_FUSION')) { header('Location: index.php');exit; }

echo rss_p_head('Linki');

$items_per_page = 10;

	$result = dbquery(
		"SELECT tw.weblink_name, tw.weblink_id, tw.weblink_description, tw.weblink_datestamp,twc.weblink_cat_id, twc.weblink_cat_name FROM ".DB_WEBLINKS." tw
		INNER JOIN ".DB_WEBLINK_CATS." twc ON tw.weblink_cat=twc.weblink_cat_id
		WHERE ".groupaccess('weblink_cat_access')."
		ORDER BY weblink_datestamp DESC LIMIT 0,$items_per_page");	 
	
if (dbrows($result)) {
while ($data = dbarray($result)) {
echo rss_p_item ($data['weblink_name'],
					   BASEDIR.'weblinks.php?cat_id='.$data['weblink_cat_id'].'&amp;weblink_id='.$data['weblink_id'],
					   '<p><strong>- <a href="'.BASEDIR.'weblinks.php?cat_id='.$data['weblink_cat_id'].'">'.$data['weblink_cat_name'].'</a></strong></p>'.
							($data['weblink_description']?nl2br(stripslashes($data['weblink_description'])):''),
					   $data['weblink_datestamp'],
					   'w_'.$data['weblink_id'],
					   $data['weblink_cat_name'],
					   '');
}

}
else { echo rss_p_noitem(); }
		
echo rss_p_footer();
?>

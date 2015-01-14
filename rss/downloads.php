<?php
//build: 2
if (!defined('IN_FUSION')) { header('Location: index.php');exit; }

$items_per_page = 15;

echo rss_p_head('Pobieralnia');

	$result = dbquery(
		"SELECT td.download_title, td.download_filesize, td.download_id, td.download_description, td.download_datestamp,tdc.download_cat_id, tdc.download_cat_name FROM ".DB_DOWNLOADS." td
		INNER JOIN ".DB_DOWNLOAD_CATS." tdc ON td.download_cat=tdc.download_cat_id
		WHERE ".groupaccess('download_cat_access')."
		ORDER BY download_datestamp DESC LIMIT 0,$items_per_page");	 
if (dbrows($result)) {
while ($data = dbarray($result)) {
echo rss_p_item (	$data['download_title'].($data['download_filesize']?' ('.$data['download_filesize'].')':''),
					   BASEDIR.'downloads.php?cat_id='.$data['download_cat_id'].'&amp;download_id='.$data['download_id'],
					   '<p><strong>- <a href="'.BASEDIR.'downloads.php?cat_id='.$data['download_cat_id'].'">'.$data['download_cat_name'].'</a></strong></p>'.
							($data['download_description']?nl2br(stripslashes($data['download_description'])):''),
					   $data['download_datestamp'],
					   'd_'.$data['download_id'],
					   $data['download_cat_name'],
					   '');
}
		
}
else { echo rss_p_noitem(); }
		
echo rss_p_footer();
?>

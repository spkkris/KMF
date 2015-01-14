<?php
//build: 2
if (!defined('IN_FUSION')) { header('Location: index.php');exit; }

define('SAFEMODE', @ini_get("safe_mode") ? true : false);
function getalbpath($toid) {
return PHOTOS.(!SAFEMODE ? 'album_'.$toid.'/' : '');
}

echo rss_p_head('Foto');

$items_per_page = 15;

$result = dbquery(
		"SELECT tp.photo_thumb1, tp.photo_thumb2, tp.photo_filename, tp.photo_title, tp.photo_id, tp.photo_description, tp.photo_datestamp, tp.photo_allow_comments, ta.album_title, ta.album_id
		FROM ".DB_PHOTOS." tp
		INNER JOIN ".DB_PHOTO_ALBUMS." ta USING (album_id)
		WHERE ".groupaccess('album_access')."
		ORDER BY photo_datestamp DESC LIMIT 0,$items_per_page");
		
if (dbrows($result)) {
while ($data = dbarray($result)) {
$imgsrcthis = getalbpath($data['album_id']).($data['photo_thumb1']?$data['photo_thumb1']:($data['photo_thumb2']?$data['photo_thumb2']:$data['photo_filename']));
	
echo rss_p_item ($data['photo_title'],
					   BASEDIR.'photogallery.php?photo_id='.$data['photo_id'],
					   '<p><strong>- <a href="'.BASEDIR.'photogallery.php?album_id='.$data['album_id'].'">'.$data['album_title'].'</a></strong></p>'.
							'<p><img src="'.$imgsrcthis.'" alt="'.$data['photo_filename'].'" /></p>'.
							nl2br(parseubb($data['photo_description'])),
					   $data['photo_datestamp'],
					   'p_'.$data['photo_id'],
					   $data['album_title'],
					   ($data['photo_allow_comments'] ? BASEDIR.'photogallery.php?photo_id='.$data['photo_id'].'#comments':''));
}
		
}
else { echo rss_p_noitem(); }
		
echo rss_p_footer();
?>

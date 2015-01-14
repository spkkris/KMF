<?php
//build: 2
if (!defined('IN_FUSION')) { header('Location: index.php');exit; }

echo rss_p_head($locale['global_077']);

$items_per_page = $settings['newsperpage'];

		$result = dbquery(
			"SELECT tn.news_subject, tn.news_id, tn.news_breaks, tn.news_news, tn.news_datestamp, tn.news_allow_comments,tn.news_image_t2, tc.news_cat_id, tc.news_cat_name FROM ".DB_NEWS." tn
			LEFT JOIN ".DB_USERS." tu ON tn.news_name=tu.user_id
			LEFT JOIN ".DB_NEWS_CATS." tc ON tn.news_cat=tc.news_cat_id
			WHERE ".groupaccess('news_visibility')." AND (news_start='0'||news_start<=".time().") AND (news_end='0'||news_end>=".time().") AND news_draft='0'
			ORDER BY news_sticky DESC, news_datestamp DESC LIMIT 0,$items_per_page");

if (dbrows($result)) {
while ($data = dbarray($result)) {
echo rss_p_item (	$data['news_subject'],
					   BASEDIR.'news.php?readmore='.$data['news_id'],
					   (!empty($data['news_cat_id'])?'<p><strong>- <a href="'.BASEDIR.'news_cats.php?cat_id='.$data['news_cat_id'].'">'.$data['news_cat_name'].'</a></strong></p>':'').
						   ($data['news_image_t2']?'<p><img src="'.IMAGES_N_T.$data['news_image_t2'].'" /></p>':'').
						   parseubb($data['news_breaks'] == 'y' ? nl2br(stripslashes($data['news_news'])) : stripslashes($data['news_news'])),
					   $data['news_datestamp'],
					   'n_'.$data['news_id'],
					   $data['news_cat_name'],
					   ($data['news_allow_comments'] ? BASEDIR.'news.php?readmore='.$data['news_id'].'#comments':''));
}

}
else { echo rss_p_noitem(); }
		
echo rss_p_footer();
?>
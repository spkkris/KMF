<?php
//build: 1+
if (!defined('IN_FUSION')) { header('Location: index.php');exit; }

echo rss_p_head($locale['global_030']);

$items_per_page = 15;

	$result = dbquery(
		"SELECT ta.article_id,ta.article_subject, ta.article_allow_comments, ta.article_breaks, ta.article_snippet, ta.article_datestamp ,tac.article_cat_name, tac.article_cat_id FROM ".DB_ARTICLES." ta
		INNER JOIN ".DB_ARTICLE_CATS." tac ON ta.article_cat=tac.article_cat_id
		WHERE ".groupaccess('article_cat_access')." AND article_draft='0'
		ORDER BY article_datestamp DESC LIMIT 0,$items_per_page");   
if (dbrows($result)) {
while ($data = dbarray($result)) {
echo rss_p_item ($data['article_subject'],
					   BASEDIR.'articles.php?article_id='.$data['article_id'],
					   '<p><strong>- <a href="'.BASEDIR.'articles.php?cat_id='.$data['article_cat_id'].'">'.$data['article_cat_name'].'</a></strong></p>'.
							parseubb($data['article_breaks'] == 'y' ? nl2br(stripslashes($data['article_snippet'])) : stripslashes($data['article_snippet'])),
					   $data['article_datestamp'],
					   'a_'.$data['article_id'],
					   $data['article_cat_name'],
					   ($data['article_allow_comments'] ? BASEDIR.'articles.php?article_id='.$data['article_id'].'#comments':''));
}
		
}
else { echo rss_p_noitem(); }
		
echo rss_p_footer();
?>
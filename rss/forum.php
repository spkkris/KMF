<?php
//build: 3
if (!defined('IN_FUSION')) { header('Location: index.php');exit; }


//settings
$allow_get = true;
$bbcodes_allow = true; //bbcodes allowed? true - yes, false - no
$max_posts = $settings['numofthreads'];
$max_threads = $settings['numofthreads'];

if( isset($_GET['f']) && isNum($_GET['f']) ) { $set_forumid = $_GET['f'];} 
else {$set_forumid = '';}
if( isset($_GET['t']) && isNum($_GET['t']) ) { $set_threadid = $_GET['t'];} 
else {$set_threadid = '';}

if(!$allow_get) {$set_forumid='';$set_threadid = '';}

function postparse($text) { global $bbcodes_allow;
	if(!$bbcodes_allow) {$text = preg_replace("[\[(.*?)\]]", '', $text);}	//$text = preg_replace("<\<(.*?)\>>", "", $text);
	else { $text = parseubb($text); }
	return nl2br($text);
}

if($set_threadid!=''){//posts of this thread
	echo rss_p_head($locale['global_043']);

	$result = dbquery(
		"SELECT p.thread_id, p.post_id, p.post_message, p.post_smileys, p.post_datestamp,
		u.user_name,
		tt.thread_subject
		FROM ".DB_POSTS." p
		INNER JOIN ".DB_THREADS." tt ON p.thread_id=tt.thread_id
		INNER JOIN ".DB_FORUMS." tf ON p.forum_id=tf.forum_id
		LEFT JOIN ".DB_USERS." u ON p.post_author=u.user_id
		WHERE ".groupaccess('tf.forum_access')." AND p.thread_id='".$set_threadid."' ".(iADMIN?'':' AND post_hidden=\'0\'')."
		ORDER BY post_datestamp LIMIT 0,".$max_posts);
	
	if (dbrows($result)) {
		while ($data = dbarray($result)) {
		
			echo rss_p_item ($data['user_name'],
				FORUM.'viewthread.php?thread_id='.$data['thread_id'].'&amp;pid='.$data['post_id'].'#post_'.$data['post_id'],
				postparse($data['post_smileys']?parsesmileys($data['post_message']):$data['post_message']),
				$data['post_datestamp'],
				'f_'.$data['thread_id'].$data['post_id'],
				$data['thread_subject'],
				'');	
		
		}
	}
	else {  echo rss_p_noitem(); }
	
	echo rss_p_footer();
}
else {//whole forum + current forum
	echo rss_p_head($locale['global_040']);

	$result = dbquery(
		"SELECT tt.thread_id, tt.thread_subject, tt.thread_views, tt.thread_lastuser, tt.thread_lastpost,
		tt.thread_poll, tf.forum_id, tf.forum_name, tf.forum_access, tt.thread_lastpostid, tt.thread_postcount, tu.user_id, tu.user_name, tu.user_status, tp.post_message, tp.post_smileys
		FROM ".DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id=tf.forum_id
		INNER JOIN ".DB_USERS." tu ON tt.thread_lastuser=tu.user_id
		INNER JOIN ".DB_POSTS." tp ON tt.thread_lastpost=tp.post_datestamp
		WHERE ".groupaccess('tf.forum_access')." ".(iADMIN?'':' AND post_hidden=\'0\'')." ".($set_forumid!=''?' AND tt.forum_id=\''.$set_forumid.'\'':'')."
		ORDER BY tt.thread_lastpost DESC LIMIT 0,".$max_threads);


	if (dbrows($result)) {
		while ($data = dbarray($result)) {
			$thread_poll = ($data['thread_poll']?'['.$locale['global_051'].'] ':'');

			echo rss_p_item ($thread_poll.$data['thread_subject'],
					 FORUM.'viewthread.php?thread_id='.$data['thread_id'].'&amp;pid='.$data['thread_lastpostid'].'#post_'.$data['thread_lastpostid'],
					 '<p>'.
					 ($set_forumid==''?'<strong>- '.$locale['global_048'].': <a href="'.FORUM.'viewforum.php?forum_id='.$data['forum_id'].'">'.$data['forum_name'].'</a></strong> ; ':'').
					 $locale['global_045'].': <b>'.$data['thread_views'].'</b> '.$locale['global_046'].': <b>'.($data['thread_postcount']-1).'</b> '.$locale['global_050'].': '.profile_link($data['thread_lastuser'],$data['user_name'],$data['user_status']).'</p>'.
						postparse($data['post_smileys']?parsesmileys($data['post_message']):$data['post_message']),
					 $data['thread_lastpost'],
					 'f_'.$data['thread_id'].$data['thread_lastpostid'],
					 $data['forum_name'],
					 '');
		}
	}
	else {  echo rss_p_noitem(); }


	echo rss_p_footer();
}
?>
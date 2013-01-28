<?php 
/*
Plugin Name: LiveJournal Userpics
Plugin URI: http://a-bishop.com/wordpress/
Description: This plugin shows LiveJournal userpics (used with OpenID).
Version: 0.5
Author: Alexander Bishop
Author URI: http://a-bishop.com/

CHANGES
2008-10-10 Initial release
*/
function get_userpic($comment, $size) {
	$author = $comment->comment_author_url;
	if(ereg("livejournal", $author)){
		include_once(ABSPATH . WPINC . '/rss.php');
		$url = $author.'data/rss';
		$rss = fetch_rss($url);
		$userpic = $rss->image['url'];
		return '<img src="'.$userpic.'" class="userpic" height="'.$size.'">';
	}
	else {
		$avatar = get_avatar($comment, $size);
		return $avatar;
	}
}
?>
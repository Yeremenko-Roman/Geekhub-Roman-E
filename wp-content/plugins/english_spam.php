<?php
/*
Plugin Name: Aprove only russian comments
Plugin URI: http://blogclient.ru
Description: Kill all comments without russian text
Author: Vladimir Yushko
Version: 1.0
Author URI: http://blogclient.ru/
*/ 

function KillUnrussianComment($commentdata) {
$Content = @iconv('utf-8', 'windows-1251//ignore', $commentdata['comment_content']);
if (!preg_match('/[à-ÿÀ-ß]{1,}/', $Content))  {
	wp_die( __('Sorry, comments are closed for this item.') );
}
return $commentdata;
}

add_action('preprocess_comment', 'KillUnrussianComment');

?>
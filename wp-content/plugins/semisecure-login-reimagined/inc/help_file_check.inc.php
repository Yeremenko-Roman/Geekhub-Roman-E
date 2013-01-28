<?php
	// this assumes that the wp-content folder is in the default location
	$root = dirname(dirname(dirname(dirname(dirname(__FILE__)))));

	// if we aren't already within WordPress
	if (! defined('ABSPATH') ) {
		if (file_exists($root.'/wp-load.php')) // WP 2.6
			require_once($root.'/wp-load.php');
		else if (file_exists($root.'/wp-config.php')) // Prior to WP 2.6
			require_once($root.'/wp-config.php');
		else
			$allow_semisecure_help_access = true; // in this case, just allow access
	}

	if (!isset($allow_semisecure_help_access) && function_exists('current_user_can')) {
		// check to see that the current user has the same privileges
		//  as those that are required for the settings page
		if (current_user_can('manage_options'))
			$allow_semisecure_help_access = true;
	}

	require_once(dirname(dirname(__FILE__)).'/classes/SemisecureLoginReimagined.php');
	$text_domain = SemisecureLoginReimagined::text_domain();

	// if we were able to access the WP core
	//  then load the plugin's text-domain for translations
	if (function_exists('load_plugin_textdomain') && function_exists('plugin_basename') && defined('PLUGINDIR'))
		load_plugin_textdomain($text_domain, PLUGINDIR.'/'.plugin_basename(dirname(dirname(__FILE__))).'/languages', plugin_basename(dirname(dirname(__FILE__))).'/languages');

	// if we weren't able to access the WP core
	//  then make sure the basic translation functions are available
	//   even if they won't actually translate anything
	if (!function_exists('__')) {
		function __($string, $text_domain='') {
			return $string;
		}
	}
	if (!function_exists('_e')) {
		function _e($string, $text_domain='') {
			echo $string;
		}
	}

	if (!isset($allow_semisecure_help_access) OR $allow_semisecure_help_access !== true) {
		if (function_exists('wp_die'))
			wp_die(__('Sorry, you don\'t have permission to access this page. You must be logged in and granted the "manage_options" capability to view this page.', $text_domain));
		else
			die(__('Sorry, you don\'t have permission to access this page. You must be logged in and granted the "manage_options" capability to view this page.', $text_domain));
	}
?>
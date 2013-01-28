<?php
/*
Plugin Name: Replace WP-Version
Plugin URI: http://bueltge.de/wordpress-version-verschleiern-plugin/602/
Description: Replace the WP-version with a random string &lt; WP 2.4 and eliminate WP-version &gt; WP 2.4
Author: Frank Bueltge
Version: 1.0
License: GPL
Author URI: http://bueltge.de/
*/

function fb_replace_wp_version() {

	if ( !is_admin() ) {
		global $wp_version;
		
		// random value
		$v = intval( rand(0, 9999) );
		
		if ( function_exists('the_generator') ) {
			// eliminate version for wordpress >= 2.4
			add_filter( 'the_generator', create_function('$a', "return null;") );
			// add_filter( 'wp_generator_type', create_function( '$a', "return null;" ) );
			
			// for $wp_version and db_version
			$wp_version = $v;
		} else {
			// for wordpress < 2.4
			add_filter( "bloginfo_rss('version')", create_function('$a', "return $v;") );
			
			// for rdf and rss v0.92
			$wp_version = $v;
		}
	}

}

if ( function_exists('add_action') ) {
	add_action('init', fb_replace_wp_version, 1);
}
?>
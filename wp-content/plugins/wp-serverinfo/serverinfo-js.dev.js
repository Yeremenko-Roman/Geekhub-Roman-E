/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.8 Plugin: WP-ServerInfo 1.50								|
|	Copyright (c) 2009 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://lesterchan.net															|
|																							|
|	File Information:																	|
|	- Server Info JavaScript File													|
|	- wp-content/plugins/wp-serverinfo/serverinfo-js.js					|
|																							|
+----------------------------------------------------------------+
*/


// Display General Overview
function toggle_general() {
	jQuery('#GeneralOverview').show();
	jQuery('#PHPinfo').hide();
	jQuery('#MYSQLinfo').hide();
}

// Display PHP Information
function toggle_php() {
	jQuery('#GeneralOverview').hide();
	jQuery('#PHPinfo').show();
	jQuery('#MYSQLinfo').hide();
}

// Display MYSQL Information
function toggle_mysql() {
	jQuery('#GeneralOverview').hide();
	jQuery('#PHPinfo').hide();
	jQuery('#MYSQLinfo').show();
}
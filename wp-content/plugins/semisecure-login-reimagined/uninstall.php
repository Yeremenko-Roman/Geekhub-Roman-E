<?php
// WP2.7 uninstall.php file
if(defined('ABSPATH') && defined('WP_UNINSTALL_PLUGIN') && function_exists('delete_option')) {
	delete_option('semisecurelogin_reimagined_encrypt_admin');
	delete_option('semisecurelogin_reimagined_encrypt_login');
	delete_option('semisecurelogin_reimagined_rsa_keys');
	delete_option('semisecurelogin_reimagined_secretkey_algo');
	delete_option('semisecurelogin_reimagined_more_settings');
}
?>
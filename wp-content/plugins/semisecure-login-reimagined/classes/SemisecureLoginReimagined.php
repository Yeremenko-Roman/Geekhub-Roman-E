<?php
if (!class_exists('SemisecureLoginReimagined')) {
	require_once(dirname(dirname(__FILE__)).'/inc/compatibility_defines.inc.php');
	require_once(dirname(__FILE__).'/SemisecureLoginReimagined_AdvancedOptions.php');
	/**
	* The main class for this plugin
	* Creating an instance of this class takes place in plugin.php
	* This way the class can be defined without being instantiated when including this file
	*/
	class SemisecureLoginReimagined extends SemisecureLoginReimagined_AdvancedOptions {

		// class constants
		var $uuid = 'f0466efd887740a4be0d67e2a548431a';
		var $nonce_session_key = 'f0466efd887740a4be0d67e2a548431a_nonce'; //semisecure_login_nonce
		var $text_domain = 'semisecure-login-reimagined'; //f0466efd887740a4be0d67e2a548431a_text_domain

		var $text_domain_loaded = false;
		var $force_alt_keypair_generation_method = FALSE; // used to be in the AdvancedOptions file
		var $debug_info = array();

		/**
		 * Grrr... PHP4-style constructor... conforming to WordPress minimum PHP requirements (PHP4 is dead!)
		 */
		function SemisecureLoginReimagined() {
			// Plugin Hooks
			// checking that the plugin hook functions exist so that when accessing this file directly it doesn't throw a fatal error (and possibly give away info about your server)
			if (function_exists('register_activation_hook'))
				register_activation_hook(dirname(dirname(__FILE__)).'/plugin.php', array(&$this, 'semisecure_activate'));
			if (function_exists('add_action')) {
				add_action('init', array(&$this, 'wp_init'));
				add_action('admin_menu', array(&$this, 'add_pages'));

				// Do we want to encrypt the password on the wp-login.php page?
				if ($this->get_option('encrypt_login')) {
					add_action('login_head', array(&$this, 'login_head'));
					add_action('login_form', array(&$this, 'login_form'), $this->login_form_semisecure_message_position);
				}

				// Do we also want to encrypt the password(s) on the user administration pages?
				//  (wp-admin/profile.php, wp-admin/user-edit.php, and wp-admin/user-new.php)
				if ($this->get_option('encrypt_admin')) {
					add_action('admin_print_scripts', array(&$this, 'admin_print_scripts'));
					add_action('admin_head', array(&$this, 'admin_head'));
				}
			}
			if (function_exists('add_filter')) {
				add_filter('plugin_action_links_'.plugin_basename(dirname(dirname(__FILE__)).'/plugin.php'), array(&$this, 'plugin_action_links'));
				add_filter('plugin_row_meta', array(&$this, 'plugin_row_meta'), 10, 2);
				// add ozh admin drop down icon
				add_filter('ozh_adminmenu_icon_semisecureloginreimagined', array(&$this, 'ozh_adminmenu_icon'));
			}
		}

		/**
		 * Load the WP textdomain for this plugin (for translations)
		 */
		function load_textdomain() {
			if ($this->text_domain_loaded) return;

			load_plugin_textdomain($this->text_domain, PLUGINDIR.'/'.plugin_basename(dirname(dirname(__FILE__))).'/languages', plugin_basename(dirname(dirname(__FILE__))).'/languages');
			$this->text_domain_loaded = true;
		}

		/**
		 * Plugin Hook (init)
		 * Call session_start before sending any headers
		 * This fixes an issue where sometimes you would have to reload the page to properly start the session
		 */
		function wp_init() {
			$nonce_method = $this->get_option('nonce_method');
			
			// don't start the session if nonces are disabled
			if ($nonce_method != 'disable') {
				@session_start();

				// create the login nonce if it hasn't been initialized
				if ($this->empty_string($_SESSION[$this->nonce_session_key]))
					$_SESSION[$this->nonce_session_key] = $this->generate_nonce();

				$nonce = $_SESSION[$this->nonce_session_key];
			}
			else {
				$nonce = '';
			}

			// loop thru and decrypt any passwords as needed
			// update $_POST
			$expire_nonce = false;
			$uuid_length_plus_two = strlen($this->uuid) + 2;
			foreach($_POST as $key => $val) {
				if (strlen($key) > $uuid_length_plus_two && substr($key, ($uuid_length_plus_two * -1)) == '__' . $this->uuid) {
					$orig_key = substr($key, 0, strlen($key) - $uuid_length_plus_two);
					if (isset($_POST[$orig_key])) {
						if ($this->overwrite_post_key_value OR preg_match('/^[*]+$/', $_POST[$orig_key])) {
							// WP forces GPC data to be escaped
							// strip the slashes to make sure we're dealing with the data
							$new_val = stripslashes_deep( array($val) );
							$new_val = $new_val[0];

							$new_val = $this->decrypt_rsa_password($new_val, $nonce);

							// add slashes back to $new_val to be used in $_POST
							// this just makes sure it's the same as the rest of the GPC data within WordPress
							$new_val = add_magic_quotes( array($new_val) );
							$_POST[$orig_key] =  $new_val[0];
						}
					}
					else {
						$new_val = stripslashes_deep( array($val) );
						$new_val = $new_val[0];
						$new_val = $this->decrypt_rsa_password($new_val, $nonce);
						$new_val = add_magic_quotes( array($new_val) );
						$_POST[$orig_key] =  $new_val[0];
					}

					$expire_nonce = true;
				}
			}

			if ($expire_nonce && $nonce_method != 'disable') {
				// expire the login nonce if we just used the previous value
				$prev_nonce = $_SESSION[$this->nonce_session_key];
				$_SESSION[$this->nonce_session_key] = $this->generate_nonce();
				if ($_SESSION[$this->nonce_session_key] == $prev_nonce) {
					// there's a very small chance that the newly generated nonce value will equal the previous nonce
					// if that's the case we'll try one more time to generate a new nonce
					// we won't try looping forever though
					$_SESSION[$this->nonce_session_key] = $this->generate_nonce();
				}
			}
		}

		/**
		 * Plugin Hook (admin_print_scripts)
		 */
		function admin_print_scripts() {
			global $pagenow;
			switch ($pagenow) {
				case 'profile.php':
				case 'user-edit.php':
				case 'user-new.php':
					SemisecureLoginReimagined::enqueue_js();
					break;
			}
		}

		/**
		 * Plugin Hook (admin_head)
		 */
		function admin_head() {
			global $pagenow;
			switch ($pagenow) {
				case 'profile.php':
				case 'user-edit.php':
				case 'user-new.php':
					$this->load_textdomain();
					require(dirname(dirname(__FILE__)).'/inc/admin_head.inc.php');
					break;
			}
		}

		/**
		 * Decrypt a hex/base64/RSA encoded password
		 *
		 * @param string $password The base64 RSA/RC4/AES encoded password
		 * @param string $nonce The current nonce to verify against
		 * @return string Returns the decrypted password or the original encrypted password (if the private-key doesn't exist or the nonce was incorrect)
		 */
		function decrypt_rsa_password($password, $nonce) {
			$keys_array = $this->get_option('rsa_keys');
			$privkey = $keys_array['privatekey'];

			if (is_string($privkey) && !empty($privkey)) {
				if ( count($array = explode('|', $password)) === 2 ) {
					$encrypted_secret_key = $array[0];
					$encrypted_password = $array[1];

					$success = @openssl_private_decrypt(base64_decode($encrypted_secret_key), &$secret_key, $privkey, OPENSSL_PKCS1_PADDING);
					if ($success) {
						$secret_key_algo = $this->secret_key_algo();
						require_once(dirname(__FILE__).'/crypto-js/Cryptojs_util.php');
						//require_once(dirname(__FILE__).'/crypto-js/Cryptojs_Binary.php');
						require_once(dirname(__FILE__).'/crypto-js/Cryptojs_UTF8.php');
						require_once(dirname(__FILE__).'/crypto-js/Cryptojs.php');
						if ($secret_key_algo == 'aes-cbc') {
							require_once(dirname(__FILE__).'/crypto-js/Cryptojs_AES.php');
							$temp = Cryptojs_AES::decrypt($encrypted_password, $secret_key, array('mode' => 'cbc'));
						}
						else if ($secret_key_algo == 'aes-ofb') {
							require_once(dirname(__FILE__).'/crypto-js/Cryptojs_AES.php');
							$temp = Cryptojs_AES::decrypt($encrypted_password, $secret_key, array('mode' => 'ofb'));
						}
						else if ($secret_key_algo == 'rabbit') {
							require_once(dirname(__FILE__).'/crypto-js/Cryptojs_Rabbit.php');
							$temp = Cryptojs_Rabbit::decrypt($encrypted_password, $secret_key);
						}
						else { // marc4
							require_once(dirname(__FILE__).'/crypto-js/Cryptojs_MARC4.php');
							$temp = Cryptojs_MARC4::decrypt($encrypted_password, $secret_key);
						}

						$nonce_length = strlen($nonce);
						if (substr($temp, 0, $nonce_length) === $nonce) {
							$pad_length = hexdec(substr($temp, $nonce_length, 2));
							$pass = substr($temp, $nonce_length + 2 + $pad_length); // pass has to be at the end because it might be UTF-8, which PHP doesn't count correctly
							return $pass;
						}
					}

				}
			}
			return NULL;
		}

		/**
		 * Plugin Hook (register_activation_hook)
		 * Generates and stores a unique RSA key when the plugin is first activated
		 */
		function semisecure_activate() {
			if (!extension_loaded('openssl') OR !function_exists('openssl_private_decrypt')) {
				$this->load_textdomain();
				deactivate_plugins( dirname(dirname(__FILE__)).'/plugin.php' );
				wp_die(sprintf(__('SemisecureLoginReimagined has not been activated!<br /><br />OpenSSL doesn\'t appear to be available. This plugin relies on OpenSSL and won\'t work until it\'s been installed.<br /><br />Click <a href="%s">here</a> to return to the plugins page.', $this->text_domain), 'plugins.php'));
			}

			// check if the option already exists in the database
			if ($this->get_option('rsa_keys') === FALSE) {
				// create an initial keypair
				$successful = $this->store_new_rsa_keypair(1024, 10001);
			}

			if ($this->get_option('encrypt_login') === FALSE) {
				// activate the login encryption option when first activating the plugin
				$this->update_option('encrypt_login', TRUE);
			}

			if (isset($successful) && $successful === FALSE) {
				$this->load_textdomain();
				wp_die(sprintf(__('SemisecureLoginReimagined has been activated but the keypair was not successfully generated.<br /><br />Please visit the <a href="%s">settings</a> page to generate an RSA keypair.<br /><br />Click <a href="%s">here</a> to return to the plugins page.', $this->text_domain), 'options-general.php?page=semisecureloginreimagined', 'plugins.php?'.$_SERVER['QUERY_STRING']));
			}
			
			$blog_charset = get_option('blog_charset');
			$blog_charset = trim(strtoupper($blog_charset));
			if ($blog_charset != 'UTF-8') {
				wp_die(sprintf(__('SemisecureLoginReimagined has been activated but the blog\'s character encoding is set to %s.<br /><br />This plugin is tested to work with UTF-8.<br /><br />Click <a href="%s">here</a> to change the character encoding.<br /><br />Click <a href="%s">here</a> to return to the plugins page.', $this->text_domain), htmlspecialchars($blog_charset), 'options-reading.php', 'plugins.php?'.$_SERVER['QUERY_STRING']));
			}
		}

		/**
		 * Intermediate function to generate and store a new RSA keypair
		 * The javascript encryption is slightly faster when using 3 as the public exponent
		 *
		 * @param int $numbits Should be either 512, 1024, 2048, or 3072
		 * @param int $publicexponent Should be either 10001 or 3
		 * @return bool Returns true or false depending on if the new keypair was successfully generated and saved
		 */
		function store_new_rsa_keypair($numbits = 1024, $publicexponent = 10001) {
			// make sure we don't accidentally insert a duplicate keypair into the DB
			$this->fix_rsa_keypair_serialization();

			if ($this->generate_keys_via_alt_method()) {
				require_once(dirname(__FILE__).'/SemisecureLoginReimagined_RsaKeys_Alt.php');
				$rsakeys = new SemisecureLoginReimagined_RsaKeys_Alt($numbits);
				$this->debug_info[] = sprintf(__('The alternative method is using: %s', $this->text_domain), $this->get_openssl_version());
				$debug_info = sprintf(__('OpenSSL might not have been <a href="%s" target="_blank">installed correctly</a>.', $this->text_domain), 'http://www.php.net/manual/en/openssl.installation.php');
			}
			else {
				require_once(dirname(__FILE__).'/SemisecureLoginReimagined_RsaKeys.php');
				$rsakeys = new SemisecureLoginReimagined_RsaKeys($numbits, $publicexponent);
			}
			$keys_array = $rsakeys->get_keys_array();

			$successful = (is_array($keys_array) && is_string($keys_array['modulus']) && !empty($keys_array['modulus']) && is_int($keys_array['publicexponent']) && is_string($keys_array['publickey']) && !empty($keys_array['publickey']) && is_string($keys_array['privatekey']) && !empty($keys_array['privatekey']) && is_int($keys_array['numbits']));
			if ($successful) {
				$keys_array['publickey'] = $this->convert_to_php_eol($keys_array['publickey']);
				$keys_array['privatekey'] = $this->convert_to_php_eol($keys_array['privatekey']);

				// add/update the array to the database (WordPress takes care of serializing the array)
				$this->update_option('rsa_keys', $keys_array);
			}
			else if( isset($debug_info) ) {
				$this->debug_info[] = $debug_info;
			}

			return $successful;
		}

		/**
		 * @return string The OpenSSL version
		 */
		function get_openssl_version() {
			if ( defined('OPENSSL_VERSION_TEXT') ) { // since PHP 5.2.0
				return OPENSSL_VERSION_TEXT;
			}
			else {
				// taken from: http://us.php.net/manual/en/function.phpinfo.php
				ob_start();
				phpinfo(INFO_MODULES);
				$s = ob_get_contents();
				ob_end_clean();
				$s = strip_tags($s,'<h2><th><td>');
				$s = preg_replace('/<th[^>]*>([^<]+)<\/th>/',"<info>\\1</info>",$s);
				$s = preg_replace('/<td[^>]*>([^<]+)<\/td>/',"<info>\\1</info>",$s);
				$vTmp = preg_split('/(<h2[^>]*>[^<]+<\/h2>)/',$s,-1,PREG_SPLIT_DELIM_CAPTURE);
				$vModules = array();
				for ($i=1;$i<count($vTmp);$i++) {
					if (preg_match('/<h2[^>]*>([^<]+)<\/h2>/',$vTmp[$i],$vMat)) {
						$vName = trim($vMat[1]);
						$vTmp2 = explode("\n",$vTmp[$i+1]);
						foreach ($vTmp2 AS $vOne) {
							$vPat = '<info>([^<]+)<\/info>';
							$vPat3 = "/$vPat\s*$vPat\s*$vPat/";
							$vPat2 = "/$vPat\s*$vPat/";
							if (preg_match($vPat3,$vOne,$vMat)) { // 3cols
								$vModules[$vName][trim($vMat[1])] = array(trim($vMat[2]),trim($vMat[3]));
							} elseif (preg_match($vPat2,$vOne,$vMat)) { // 2cols
								$vModules[$vName][trim($vMat[1])] = trim($vMat[2]);
							}
						}
					}
				}

				if (isset($vModules['openssl']['OpenSSL Version'])) {
					return $vModules['openssl']['OpenSSL Version'];
				}
				
				$version = '';
				if (isset($vModules['openssl']['OpenSSL Library Version'])) {
					$version = $vModules['openssl']['OpenSSL Library Version'] . ' ' . __('(Library Version)', $this->text_domain);
				}
				if (isset($vModules['openssl']['OpenSSL Header Version'])) {
					if (!empty($version))
						$version .= '; ' . $vModules['openssl']['OpenSSL Header Version'] . ' ' . __('(Header Version)', $this->text_domain);
					else
						$version = $vModules['openssl']['OpenSSL Header Version'] . ' ' . __('(Header Version)', $this->text_domain);
				}
				return $version;
			}
		}

		/**
		 * Plugin Hook (login_head)
		 * JavaScript to be included in the login page header
		 */
		function login_head() {
			$this->load_textdomain();
			require(dirname(dirname(__FILE__)).'/inc/login_head.inc.php');
		}

		/**
		 * Plugin Hook (login_form)
		 */
		function login_form() {
			$this->load_textdomain();
			require(dirname(dirname(__FILE__)).'/inc/login_form.inc.php');
		}

		/**
		 * Plugin Hook (admin_menu)
		 * Add the settings page
		 */
		function add_pages() {
			$this->load_textdomain();
			add_options_page(sprintf(__('Semisecure Login Reimagined %s', $this->text_domain), $this->version()), __('Semisecure Login', $this->text_domain), 'manage_options', 'semisecureloginreimagined', array(&$this, 'options_page'));
		}

		/**
		 * Plugin Hook (add_options_page)
		 * Display the settings page
		 */
		function options_page() {
			$this->load_textdomain();
			require(dirname(dirname(__FILE__)).'/inc/options_page.inc.php');
		}

		/**
		 * @param string $in (for this plugin, $in == 'semisecureloginreimagined')
		 * @return string The icon location
		 */
		function ozh_adminmenu_icon($in) {
			$images = array( 'key.png', 'lock.png' );
			$rand = wp_rand(0, count($images) - 1);
			return plugins_url( plugin_basename(dirname(dirname(__FILE__))).'/images/'.$images[$rand]);
		}

		/**
		 * Plugin Hook (plugin_action_links)
		 * Add a settings link on the plugins page
		 * 
		 * @param array $links
		 * @return array Updated links
		 */
		function plugin_action_links($links) {
			if (current_user_can('manage_options')) {
				$this->load_textdomain();
				$link = '<a href="options-general.php?page=semisecureloginreimagined">' . __('Settings', $this->text_domain) . '</a>';
				//array_unshift(&$links, $link);
				$links[] = $link;
			}
			return $links;
		}

		/**
		 * Plugin Hook (plugin_row_meta)
		 * This hook requires WP 2.8
		 *
		 * @param array $links
		 * @param string $file
		 * @return array updated links
		 */
		function plugin_row_meta($links, $file) {
			if ($file == plugin_basename(dirname(dirname(__FILE__)).'/plugin.php')) {
				$this->load_textdomain();
				$links[] = '<a href="http://wordpress.org/extend/plugins/semisecure-login-reimagined/">' . __('Visit WP.org plugin site', $this->text_domain) . '</a>';
			}
			return $links;
		}

		/**
		 * Generates a random nonce (15 characters in length)
		 *
		 * @return string Returns a nonce
		 */
		function generate_nonce() {
			$length = 15;
			$array = $this->rand_chars();
			$array = str_shuffle($array);
			$max = strlen($array) - 1;
			$nonce = '';
			for ($i = 0; $i < $length; $i++) {
				$nonce .= $array[wp_rand(0, $max)];
			}
			return $nonce;
		}

		/**
		 * Lets us know which method of RSA key generation we should use
		 *
		 * @return bool
		 */
		function generate_keys_via_alt_method() {
			if ( $this->force_alt_keypair_generation_method ) {
				$this->debug_info[] = __('The alternative keypair generation method is being used because the user forced this method.', $this->text_domain);
				return true;
			}
			if ( ini_get('safe_mode') == 1 || strtolower(ini_get('safe_mode')) == 'on' ) {
				$this->debug_info[] = __('The alternative keypair generation method is being used because safe_mode is enabled.', $this->text_domain);
				return true;
			}
			if ( !function_exists('shell_exec') OR !function_exists('proc_open') OR !function_exists('proc_close') ) {
				$this->debug_info[] = __('The alternative keypair generation method is being used because the PHP execution functions have been disabled.', $this->text_domain);
				return true;
			}
			
			$temp = $this->test_openssl_exec();
			if ( $this->trimmed_empty( $temp ) ) {
				$this->debug_info[] = __('The alternative keypair generation method is being used because openssl doesn\'t appear to be in the location specified in the AdvancedOptions fiile. The default location assumes that the openssl executable is in the system path.', $this->text_domain);
				return true;
			}

			$this->debug_info[] = __('The default keypair generation method is being used.', $this->text_domain);
			$this->debug_info[] = sprintf(__('The default method is using: %s', $this->text_domain), $temp);
			return false;
		}
		
		/**
		 * Test that the openssl executable is available
		 *
		 * @return string Returns the OpenSSL version or NULL/empty
		 */
		function test_openssl_exec() {
			// get STDOUT + STDERR
			//$ver = @shell_exec("$this->openssl_location version 2>&1");

			// only get STDOUT
			$ver = @shell_exec("$this->openssl_location version");

			return $ver;
		}

		/**
		 * Try to fix a keypair that was created via the JavaScript method where
		 *   is_serialized()===TRUE and @unserialize()===FALSE
		 * This can happen if the count was wrong for the publickey and privatekey elements
		 *   because the count was based on the wrong newline string (CRLF vs CR vs LF, etc.)
		 * Whey doesn't is_serialized just check for @unserialize()===FALSE ??
		 *
		 * @return bool Returns TRUE if the the option was updated; otherwise FALSE
		 */
		function fix_rsa_keypair_serialization() {
			global $wpdb;

			// can't use get_option here because it might return FALSE from unserialize()
			$alloptions = wp_load_alloptions(); // grab the option from cache
			if (isset($alloptions['semisecurelogin_reimagined_rsa_keys'])) {
				$keypair = $alloptions['semisecurelogin_reimagined_rsa_keys'];
			}
			else { // if the option isn't in cache then try to grab it directly from the DB
				$row = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_name = 'semisecurelogin_reimagined_rsa_keys' LIMIT 1" );
				if ( is_object($row) )
					$keypair = $row->option_value;
			}

			if ( isset($keypair) ) {
				if (is_serialized($keypair) && @unserialize($keypair) === FALSE) {
					// checking this way acknowledges that the array elements could be entered in any order
					$pattern = "/(a:5:\{)([^\{\}'\\\\]+)(\})/";
					if (preg_match($pattern, $keypair, $match)) {
						$pieces = explode(';', $match[2]);
						if (count($pieces) == 11 && $this->trimmed_empty($pieces[10])) {
							$parts[] = $pieces[0].';'.$pieces[1].';';
							$parts[] = $pieces[2].';'.$pieces[3].';';
							$parts[] = $pieces[4].';'.$pieces[5].';';
							$parts[] = $pieces[6].';'.$pieces[7].';';
							$parts[] = $pieces[8].';'.$pieces[9].';';
							$patterns[] = '/s:7:"numbits";i:[0-9]+;/';                       // int
							$patterns[] = '/s:14:"publicexponent";i:[0-9]+;/';               // int
							$patterns[] = '/s:7:"modulus";s:[0-9]+:"[A-Za-z0-9]+";/';        // string
							$patterns[] = '/(s:10:"privatekey";s:)([0-9]+)(:")([^"]+)(";)/'; // string
							$patterns[] = '/(s:9:"publickey";s:)([0-9]+)(:")([^"]+)(";)/';   // string
							for ($i = 0; $i < count($parts); $i++) {
								for ($j = 0; $j < count($patterns); $j++) {
									if ($patterns[$j] !== true && preg_match($patterns[$j], $parts[$i], $m)) {
										$patterns[$j] = true;
										if (count($m) == 6) {
											$m[4] = $this->convert_to_php_eol($m[4]);
											$m[2] = strlen($m[4]); // re-count the length
											$parts[$i] = $m[1].$m[2].$m[3].$m[4].$m[5];
										}
										else { // count($m) == 1
											$parts[$i] = $m[0]; // probably not needed
										}
										break; // break 1
									}
								}
							}

							$update_option_value = true;
							foreach ($patterns as $val) {
								if ($val !== true) {
									$update_option_value = false;
									break;
								}
							}

							if ($update_option_value) {
								$all_parts = '';
								foreach ($parts as $val) {
									$all_parts .= $val;
								}
								$corrected = $match[1].$all_parts.$match[3];
								// can't use update_option here
								//   it uses get_option internally which will return FALSE
								//   this will cause a duplicate row to be created in the DB
								// instead of updating manually we could use delete_option and add_option
								//   but that will increase the auto-incremented ID on the options table
								//   it's an option to consider for another day
								$wpdb->query( "UPDATE $wpdb->options SET option_value = '$corrected' WHERE option_name = 'semisecurelogin_reimagined_rsa_keys' LIMIT 1" );
								wp_cache_flush(); // clear the cache
								return true;
							}
						}
					}

					// if we weren't able to update the value in the DB then just delete the row
					//   WordPress already thinks the row doesn't exist even though it does
					//   this can lead to duplicate rows being inserted into the DB
					delete_option('semisecurelogin_reimagined_rsa_keys');
				}
			}
			return false;
		}

		/**
		 * Makes sure the publickey and privatekey are stored with PHP_EOL as the newline character
		 * OpenSSL seems to be able to deal with both CRLF and LF on Linux
		 *   This function is making the assumption that using PHP_EOL will work in all cases
		 *
		 * @return bool Returns TRUE if the option was updated; otherwise FALSE
		 */
		function consolidate_rsa_keypair_newlines() {
			$keys_array = $this->get_option('rsa_keys');
			if (is_array($keys_array)) {
				$orig_publickey = $keys_array['publickey'];
				$orig_privatekey = $keys_array['privatekey'];
				$keys_array['publickey'] = $this->convert_to_php_eol($keys_array['publickey']);
				$keys_array['privatekey'] = $this->convert_to_php_eol($keys_array['privatekey']);
				if ($keys_array['publickey'] != $orig_publickey OR $keys_array['privatekey'] != $orig_privatekey) {
					$this->update_option('rsa_keys', $keys_array);
					return true;
				}
			}
			return false;
		}

		/**
		 * If PCRE/UTF8 is available then replace all unicode newline characters with PHP_EOL
		 * Otherwise just replace the standard ASCII/ISO-8859-1 newline characters with PHP_EOL
		 * (Can be used statically)
		 *
		 * @param string $string
		 * @return mixed Returns the string with the newlines converted to PHP_EOL if the input is a string; if the input is NOT a string then just return the input untouched
		 */
		function convert_to_php_eol($string) {
			if (is_string($string)) {
				// In PHP, \n is guaranteed to be LF (0x0A) and \r is guaranteed to be CR (0x0D)
				//   PHP_EOL will return the system-dependent newline character(s)
				$string = str_replace("\r\n", "\n", $string);  // change CRLF to LF
				$string = str_replace("\r", "\n", $string);    // change CR by itself to LF
				if ( @preg_match('/\p{L}/u', 'a') ) { // if PCRE has been compiled with unicode support
					// http://en.wikipedia.org/wiki/Newline#Unicode
					//$string = preg_replace('/\x{000D}\x{000A}/u', "\n", $string); // change CRLF to LF
					//$string = preg_replace('/\x{000D}/u', "\n", $string);         // change CR to LF
					$string = preg_replace('/\x{0085}/u', "\n", $string);           // change NEL to LF
					$string = preg_replace('/\x{000C}/u', "\n", $string);           // change FF to LF
					$string = preg_replace('/\x{2028}/u', "\n", $string);           // change LS to LF
					$string = preg_replace('/\x{2029}/u', "\n", $string);           // change PS to LF
				}
				$string = str_replace("\n", PHP_EOL, $string); // finally, change LF to PHP_EOL
			}
			return $string;
		}
		
		/**
		 * PHP does not support unsigned integers
		 * (Can be used statically)
		 *
		 * 32-bit PHP
		 *   PHP_INT_SIZE === 4
		 *   PHP_INT_MAX  === 2147483647
		 *
		 * 64-bit PHP
		 *   PHP_INT_SIZE === 8
		 *   PHP_INT_MAX  === 9223372036854775807
		 *
		 * These constants are available since PHP 4.4.0 and PHP 5.0.5
		 *
		 * @return bool
		 */
		function is_64bit_int() {
			//$tmp_int = (int)4294967295;
			$tmp_int = (int)9223372036854775807;
			return ($tmp_int > 0);
		}

		/**
		 * This function also considers strings filled with only whitespace to be empty
		 * (Can be used statically)
		 *
		 * @param mixed $var
		 * @return bool
		 */
		function trimmed_empty($var) {
			if (is_string($var))
				$var = trim($var);
			return empty($var);
		}

		/**
		 * This function doesn't consider "0" to be empty
		 * (Can be used statically)
		 *
		 * @param mixed $string
		 * @return bool
		 */
		function empty_string($string) {
			if (is_string($string)) {
				if (strlen($string) <= 0)
					return true;
				else
					return false;
			}
			return empty($string);
		}

		/**
		 * (Can be used statically)
		 *
		 * @return string Returns the current SemisecureLoginReimagined text_domain
		 */
		function text_domain() {
			$vars = get_class_vars(__CLASS__);
			return $vars[__FUNCTION__];
		}

		/**
		 * Retrieve the option from the database
		 * (can be used statically)
		 *
		 * @param string $shortname
		 * @return variable
		 */
		function get_option($shortname) {
			switch($shortname) {
				case 'encrypt_admin':
					return get_option('semisecurelogin_reimagined_encrypt_admin');
				case 'encrypt_login':
					return get_option('semisecurelogin_reimagined_encrypt_login');
				case 'rsa_keys':
					return get_option('semisecurelogin_reimagined_rsa_keys');
				case 'secretkey_algo':
					$temp = get_option('semisecurelogin_reimagined_secretkey_algo');
					if ( ($temp == 'rabbit' && !SemisecureLoginReimagined::is_64bit_int()) || !in_array($temp, array('marc4', 'aes-cbc', 'aes-ofb', 'rabbit')) )
						$temp = 'marc4'; // make sure the plugin still works
					return $temp;
				case 'nonce_method':
					$temp = get_option('semisecurelogin_reimagined_more_settings');
					return (in_array($temp['nonce_method'], array('direct', 'async', 'disable'))) ? $temp['nonce_method'] : 'direct';
			}
		}

		/**
		 * Add/Update the option in the database
		 * (can be used statically)
		 *
		 * @param string $shortname
		 * @param variable $value
		 * @return variable
		 */
		function update_option($shortname, $value) {
			switch($shortname) {
				case 'encrypt_admin':
					update_option('semisecurelogin_reimagined_encrypt_admin', $value);    // bool
					break;
				case 'encrypt_login':
					update_option('semisecurelogin_reimagined_encrypt_login', $value);    // bool
					break;
				case 'rsa_keys':
					update_option('semisecurelogin_reimagined_rsa_keys', $value);         // array
					break;
				case 'secretkey_algo':
					if (in_array($value, array('marc4', 'aes-cbc', 'aes-ofb', 'rabbit')));
						update_option('semisecurelogin_reimagined_secretkey_algo', $value); // string
					break;
				case 'nonce_method':
					$temp = get_option('semisecurelogin_reimagined_more_settings');
					if (!is_array($temp)) $temp = array(); // in case the option hasn't been set yet
					if (in_array($value, array('direct', 'async', 'disable'))) {
						$temp['nonce_method'] = $value;
						update_option('semisecurelogin_reimagined_more_settings', $temp);   // array
					}
					break;
			}
		}


		/****************** START STATIC INTEGRATION METHODS ******************/
		/*** Can't use TRUE static functions for PHP4-compatibility reasons ***/
		/***************** For use with 3rd-party integration *****************/

		/** @return string Returns the SemisecureLoginReimagined uuid */
		function uuid() {
			$vars = get_class_vars(__CLASS__);
			return $vars[__FUNCTION__];
		}

		/** @return string Returns the current SemisecureLoginReimagined version */
		function version() {
			if ( !function_exists( 'get_plugins' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			$plugins = get_plugins('/'.plugin_basename(dirname(dirname(__FILE__))));
			return $plugins['plugin.php']['Version'];
		}

		/** @return string Returns the modulus from the RSA keypair */
		function public_n() {
			$keys_array = SemisecureLoginReimagined::get_option('rsa_keys');
			return $keys_array['modulus'];
		}

		/** @return int Returns the public-exponent from the RSA keypair */
		function public_e() {
			$keys_array = SemisecureLoginReimagined::get_option('rsa_keys');
			return $keys_array['publicexponent'];
		}

		/** @return bool Returns true or false depending on if the RSA keypair has been stored successfully */
		function is_rsa_key_ok() {
			$keys_array = SemisecureLoginReimagined::get_option('rsa_keys');
			// Technically, 'publickey' is stored but is never used for anything
			return (is_array($keys_array) && is_string($keys_array['modulus']) && !empty($keys_array['modulus']) && is_int($keys_array['publicexponent']) && is_string($keys_array['publickey']) && !empty($keys_array['publickey']) && is_string($keys_array['privatekey']) && !empty($keys_array['privatekey']) && is_int($keys_array['numbits']));
		}

		/** @return bool Returns true or false depending on if PHP has been compiled with openssl support */
		function is_openssl_avail() {
			return (extension_loaded('openssl') && function_exists('openssl_private_decrypt'));
		}

		/**
		 * @param bool $auto_print Automatically call wp_print_scripts? (useful for certain pages that don't automatically call this function)
		 */
		function enqueue_js($auto_print=false) {
			wp_enqueue_script('jquery'); // make sure jQuery is available

			$plugin_url = plugins_url( plugin_basename(dirname(dirname(__FILE__))) );
			wp_enqueue_script('jsbn-jsbn', $plugin_url.'/js/jsbn/jsbn.min.js', false, '1.1');
			wp_enqueue_script('jsbn-prng4', $plugin_url.'/js/jsbn/prng4.min.js', false, '1.1');
			wp_enqueue_script('jsbn-rng', $plugin_url.'/js/jsbn/rng.min.js', array('jsbn-prng4'), '1.1');
			wp_enqueue_script('jsbn-rsa', $plugin_url.'/js/jsbn/rsa.min.js', array('jsbn-jsbn', 'jsbn-rng'), '1.1');
			wp_enqueue_script('jsbn-base64', $plugin_url.'/js/jsbn/base64.min.js', false, '1.1');

			wp_enqueue_script('cryptojs-util', $plugin_url.'/js/crypto-js/crypto-min.js', false, '2.0.0');
			wp_enqueue_script('cryptojs-sha1', $plugin_url.'/js/crypto-js/sha1-min.js', array('cryptojs-util'), '2.0.0');
			wp_enqueue_script('cryptojs-hmac', $plugin_url.'/js/crypto-js/hmac-min.js', array('cryptojs-util'), '2.0.0');
			wp_enqueue_script('cryptojs-pbkdf2', $plugin_url.'/js/crypto-js/pbkdf2-min.js', array('cryptojs-util', 'cryptojs-hmac'), '2.0.0'); // uses sha1 by default, but it's not a 100% dependency

			$dependencies = array('jquery', 'jsbn-rsa', 'jsbn-base64', 'cryptojs-sha1');

			$secret_key_algo = SemisecureLoginReimagined::secret_key_algo();
			if ($secret_key_algo == 'aes-cbc') {
				wp_enqueue_script('cryptojs-cbc', $plugin_url.'/js/crypto-js/cbc-min.js', array('cryptojs-util'), '2.0.0');
				wp_enqueue_script('cryptojs-aes', $plugin_url.'/js/crypto-js/aes-min.js', array('cryptojs-util', 'cryptojs-pbkdf2'), '2.0.0');
				$dependencies[] = 'cryptojs-cbc';
				$dependencies[] = 'cryptojs-aes';
			}
			else if ($secret_key_algo == 'aes-ofb') {
				wp_enqueue_script('cryptojs-ofb', $plugin_url.'/js/crypto-js/ofb-min.js', array('cryptojs-util'), '2.0.0');
				wp_enqueue_script('cryptojs-aes', $plugin_url.'/js/crypto-js/aes-min.js', array('cryptojs-util', 'cryptojs-pbkdf2'), '2.0.0');
				$dependencies[] = 'cryptojs-ofb';
				$dependencies[] = 'cryptojs-aes';
			}
			else if ($secret_key_algo == 'rabbit') {
				wp_enqueue_script('cryptojs-rabbit', $plugin_url.'/js/crypto-js/rabbit-min.js', array('cryptojs-util', 'cryptojs-pbkdf2'), '2.0.0');
				$dependencies[] = 'cryptojs-rabbit';
			}
			else { // marc4
				wp_enqueue_script('cryptojs-marc4', $plugin_url.'/js/crypto-js/marc4-min.js', array('cryptojs-util', 'cryptojs-pbkdf2'), '2.0.0');
				$dependencies[] = 'cryptojs-marc4';
			}

			wp_enqueue_script('semisecure-encrypt', $plugin_url.'/js/semisecure.js', $dependencies, '1.0.4');

			if ($auto_print) {
				$scripts = array('semisecure-encrypt');
				wp_print_scripts( $scripts );
			}
		}

		/** @return string Returns the nonce session key */
		function nonce_session_key() {
			$vars = get_class_vars(__CLASS__);
			return $vars[__FUNCTION__];
		}

		/** @return string Returns the current nonce */
		function nonce() {
			$nonce_method = SemisecureLoginReimagined::get_option('nonce_method');
			if ($nonce_method == 'disable')
				return '';

			$vars = get_class_vars(__CLASS__);
			return $_SESSION[$vars['nonce_session_key']];
		}

		/**
		 * If you want to override the admin setting then you can use ::nonce() or ::nonce_js_url() directly
		 *
		 * @param bool $js_var A variable to pass through to ::nonce_js_url()
		 * @return string The nonce or an Ajax URL where the nonce can be grabbed from
		 */
		function nonce_js($js_var=false) {
			$nonce_method = SemisecureLoginReimagined::get_option('nonce_method');
			if ($nonce_method == 'async')
				return SemisecureLoginReimagined::nonce_js_url($js_var);
			else
				return SemisecureLoginReimagined::nonce();
		}

		/**
		 * @param bool $js_var If false then just get the nonce, if true then load the nonce into the following JavaScript variable: SemisecureLoginReimagined_nonce
		 * @return string The URL to the nonce JavaScript
		 */
		function nonce_js_url($js_var=false) {
			$plugin_url = plugins_url( plugin_basename(dirname(dirname(__FILE__))) );
			$plugin_url .= '/js/nonce.php';
			$nonce_method = SemisecureLoginReimagined::get_option('nonce_method');
			if ($js_var && $nonce_method == 'disable')
				$plugin_url .= '?disable=true&js=true';
			else if ($js_var && $nonce_method != 'disable')
				$plugin_url .= '?js=true';
			else if (!$js_var && $nonce_method == 'disable')
				$plugin_url .= '?disable=true';
			return $plugin_url;
		}

		/**
		 * @return int Max number of characters that the RSA key can encode
		 */
		function max_rand_chars() {
			$keys_array = SemisecureLoginReimagined::get_option('rsa_keys');
			$numbits = $keys_array['numbits'];
			switch($numbits) {
				case 3072:
					return 373; // 3072 - 88 (PKCS#1) = 2984; 2984 / 8-bit byte = 373
				case 2048:
					return 245; // 2048 - 88 (PKCS#1) = 1960; 1960 / 8-bit byte = 245
				case 1024:
					return 117; // 1024 - 88 (PKCS#1) = 936; 936 / 8-bit byte = 117
				default: // case 512
					return 53; // 512 - 88 (PKCS#1) = 424; 424 / 8-bit byte = 53
			}
		}

		/**
		 * @return string Which random characters to use
		 */
		function rand_chars() {
			return '!#$%&()*+,-0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_abcdefghijklmnopqrstuvwxyz{}~'; // not using the following ASCII characters: "'`\|/
		}
		
		/**
		 * @return string Get which secret key algorithm to use
		 */
		function secret_key_algo() {
			return SemisecureLoginReimagined::get_option('secretkey_algo');
		}

		/******************* END STATIC INTEGRATION METHODS *******************/

	}
}
?>
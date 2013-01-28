<?php
if (!class_exists('SemisecureLoginReimagined_AdvancedOptions')) {
	/**
	* This class includes advanced editable options that aren't available in the admin interface
	* Don't edit these options unless you know what you're doing!
	* Keep in mind that when upgrading this plugin, this file will be overwritten, and you will need to "re-make" your changes to the following advanced options
	* Future versions of this file might contain additional options, and choosing NOT to update this file may break the plugin
	*/
	class SemisecureLoginReimagined_AdvancedOptions {

		/** ******************************* START EDITING!!! ******************************** **/

		/**
		 * If you're using the default keypair generation method, and for some reason the path to openssl isn't included in your server's system path
		 *   (which means that you can't call out directly to openssl)
		 *   then you can include the full path to openssl here
		 * For example: '/usr/bin/openssl' or 'C:\\OpenSSL\\bin\\openssl'
		 * This is a string (the default value is 'openssl')
		 */
		var $openssl_location = 'openssl';

		/**
		 * If the POST key is already set, do we want to overwrite the value with the decrypted RSA value?
		 * For example:
		 *   $_POST['pwd'] is already set
		 *   $_POST['pwd__uuid'] is also set
		 *     Should we overwrite 'pwd' with the decrypted value of 'pwd__uuid'?
		 *     The one exception is if 'pwd' contains all asterisks (*)
		 *       In this case, we'll always overwrite 'pwd' with 'pwd__uuid'
		 * Allowed values: TRUE or FALSE (the default value is FALSE)
		 */
		var $overwrite_post_key_value = FALSE;

		/**
		 * This option is helpful if you're using a 2nd (or 3rd, etc.) plugin that also outputs "something" below the login form
		 * For example, if you're using the WP-OpenID plugin, then the login form will display both the "Semisecure" message and the OpenID textbox below the login form
		 *   This option will explicitly let you choose if the "Semisecure" message is displayed before or after the OpenID textbox
		 * A smaller number will cause the "Semisecure" message to be displayed earlier (before "something else"), and a larger number will cause the message to be displayed later (after "something else")
		 *   This number is relative to that "something else"
		 * This is an integer, and the default value is 10 (which corresponds to WordPress' default value for plugin hooks)
		 */
		var $login_form_semisecure_message_position = 10;

		/** ******************************** STOP EDITING!!! ******************************** **/

	}
}
?>
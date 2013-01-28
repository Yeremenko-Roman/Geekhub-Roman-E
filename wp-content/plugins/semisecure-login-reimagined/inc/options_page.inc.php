<?php defined('ABSPATH') OR die();

$plugin_url = plugins_url( plugin_basename(dirname(dirname(__FILE__))) );

$sub = $_GET['sub'];
if ($sub == 'misc')
	$class_misc = 'class="current"';
else if ($sub == 'integration')
	$class_integration = 'class="current"';
else if ($sub == 'uninstall')
	$class_uninstall = 'class="current"';
else {
	$class_keypair = 'class="current"';
	$sub = 'keypair';
}

$blog_charset = get_option('blog_charset');
$blog_charset = trim(strtoupper($blog_charset));
if ($blog_charset != 'UTF-8') {
	print '<div class="error"><p><strong>' . sprintf(__('This plugin is tested to work with UTF-8. Your <a href="%s">blog settings</a> are currently using the following character encoding: %s', $this->text_domain), 'options-reading.php', htmlspecialchars($blog_charset)) . '</strong></p></div>';
}

if (!extension_loaded('openssl')) {
	print '<div class="error"><p><strong>' . __('OpenSSL doesn\'t appear to be installed. This plugin relies on OpenSSL and won\'t work until it\'s been installed.', $this->text_domain) . '</strong></p></div>';
}
else if (!function_exists('openssl_private_decrypt')) {
	print '<div class="error"><p><strong>' . __('The <code>openssl_private_decrypt</code> function appears to be disabled. This plugin won\'t work until it\'s been enabled. Check the <code>disable_functions</code> setting in your php.ini file.', $this->text_domain) . '</strong></p></div>';
}

// WP forces GPC to be escaped, but nothing here should fall into that scenario
// generate and save a new keypair
if (isset($_POST['rsa_numbits']) && !empty($_POST['rsa_numbits']) && is_numeric($_POST['rsa_numbits'])) {
	if (current_user_can('manage_options')) {
		check_admin_referer('generate_semisecure-login-reimagined');
		if ($_POST['force_alt_method'] == 'yes')
			$this->force_alt_keypair_generation_method = TRUE;
		if ($_POST['show_debug_info'] == 'yes')
			$show_debug_info = true;
		$rsa_numbits = $_POST['rsa_numbits'];
		$rsa_publicexponent = is_numeric($_POST['rsa_publicexponent']) ? $_POST['rsa_publicexponent'] : 10001;
		$successful = $this->store_new_rsa_keypair($rsa_numbits, $rsa_publicexponent);
		if ($successful) {
			print '<div id="message" class="updated fade"><p><strong>' . __('The new keypair was successfully generated and saved.', $this->text_domain) . '</strong>';
		}
		else {
			print '<div id="message" class="error"><p><strong>' . __('The keypair could not be created!', $this->text_domain) . '</strong>';
		}
		if ($show_debug_info) {
			print '<ul>';
			foreach ($this->debug_info as $val) {
				print '<li>' . $val . '</li>';
			}
			print '</ul>';
		}
		print '</p></div>';
		}
		else {
			print '<div id="message" class="error"><p><strong>' . __('Ooops... looks like you don\'t have the correct privileges to make this change.', $this->text_domain) . '</strong></p></div>';
		}
}
// miscellaneous settings
else if ($_POST['where_to_encrypt'] == 'postback') {
	if (current_user_can('manage_options')) {
		check_admin_referer('misc_semisecure-login-reimagined');
		$this->update_option('encrypt_admin', isset($_POST['do_admin_encrypt']));
		$this->update_option('encrypt_login', isset($_POST['do_login_encrypt']));
		$this->update_option('secretkey_algo', $_POST['secret_key_algo']);
		$this->update_option('nonce_method', $_POST['nonce_method']);
		print '<div id="message" class="updated fade"><p><strong>' . __('The settings were successfully updated.', $this->text_domain) . '</strong></p></div>';
	}
	else {
		print '<div id="message" class="error"><p><strong>' . __('Ooops... looks like you don\'t have the correct privileges to make this change.', $this->text_domain) . '</strong></p></div>';
	}
}
// uninstall
else if ($_POST['uninstall_semisecure_login_reimagined'] == 'yes') {
	if (current_user_can('manage_options')) {
		check_admin_referer('uninstall_semisecure-login-reimagined');
		$file = plugin_basename(dirname(dirname(__FILE__)).'/plugin.php');
		define('WP_UNINSTALL_PLUGIN', $file);
		include(WP_PLUGIN_DIR . '/' . dirname($file) . '/uninstall.php');
		$deactivate_url = 'plugins.php?action=deactivate&amp;plugin='.urlencode($file);
		$deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_'.$file);
		print '<div id="message" class="updated fade"><p><strong>' . sprintf(__('<a href="%s">Click here</a> to finish the uninstallation and this plugin will be automatically deactivated.', $this->text_domain), $deactivate_url) . '</strong></p></div>';
	}
	else {
		print '<div id="message" class="error"><p><strong>' . __('Ooops... looks like you don\'t have the correct privileges to make this change.', $this->text_domain) . '</strong></p></div>';
	}
}

$do_encrypt_admin = $this->get_option('encrypt_admin');
$do_encrypt_login = $this->get_option('encrypt_login');
$secret_key_algo = $this->get_option('secretkey_algo');
$nonce_method = $this->get_option('nonce_method');

$is_64bit_int = $this->is_64bit_int();

$keys_array = $this->get_option('rsa_keys');
if ($keys_array === FALSE OR !is_array($keys_array)) {
	// attempt to fix a line-ending/count issue if the keypair was generated via the JavaScript method
	if ($this->fix_rsa_keypair_serialization()) {
		$keys_array = $this->get_option('rsa_keys');
		print '<div id="message" class="updated fade"><p><strong>' . __('The keypair was in a bad state. This has been automatically fixed.') . '</strong></p></div>';
	}
}
// make sure the keypair newlines are set as PHP_EOL
if ($this->consolidate_rsa_keypair_newlines()) {
	$keys_array = $this->get_option('rsa_keys');
	// don't print out a message in this case
}
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div>
<h2><?php _e('Semisecure Login Reimagined', $this->text_domain); ?></h2>
<p>
	<?php _e('This plugin uses a combination of public-key (RSA) and secret-key (MARC4, Rabbit, or AES) encryption to encrypt your password on the client-side before transmission. A nonce is used to help prevent replay attacks. This provides a &quot;semisecure&quot; login environment. For full security you should use an SSL certificate.', $this->text_domain); ?>
</p>
<ul class="subsubsub">
  <li><a <?php echo $class_keypair; ?> href="?page=semisecureloginreimagined"><?php _e('Keypair Settings', $this->text_domain); ?></a> | </li>
  <li><a <?php echo $class_misc; ?> href="?page=semisecureloginreimagined&amp;sub=misc"><?php _e('Misc Settings', $this->text_domain); ?></a> | </li>
  <li><a <?php echo $class_integration; ?> href="?page=semisecureloginreimagined&amp;sub=integration"><?php _e('Integration', $this->text_domain); ?></a> | </li>
  <li><a <?php echo $class_uninstall; ?> href="?page=semisecureloginreimagined&amp;sub=uninstall"><?php _e('Uninstall', $this->text_domain); ?></a></li>
</ul>
<div style="clear:both;"></div>

<?php if ($sub == 'keypair') : ?>

<p>
<?php
// check if a keypair already exists
if (SemisecureLoginReimagined::is_rsa_key_ok()) {
?>
	<?php printf(__('The current key is <strong>%d</strong> bits in length, and the public-exponent is <strong>%s</strong>', $this->text_domain), $keys_array['numbits'], '0x' . $keys_array['publicexponent']); ?>
	<table border="0">
		<tr valign="top">
			<td><?php _e('Modulus:', $this->text_domain); ?></td>
			<td><pre><?php echo wordwrap($keys_array['modulus'], 64, "\n", true) ?></pre></td>
		</tr>
	</table>
<?php
} else {
?>
	<span style="color:red;"><strong><?php _e('WARNING:', $this->text_domain); ?></strong><br /><?php _e('The RSA keypair could not be found! Please remedy the situation by generating a new keypair below.', $this->text_domain); ?></span>
<?php
}
?>
</p>
<p>
	<?php _e('<em>&quot;RSA claims that 1024-bit keys are likely to become crackable some time between 2006 and 2010 and that 2048-bit keys are sufficient until 2030. An RSA key length of 3072 bits should be used if security is required beyond 2030.&quot;</em> -<a href="http://en.wikipedia.org/wiki/Key_size#Asymmetric_algorithm_key_lengths" target="_blank">Wikipedia</a>', $this->text_domain); ?>
</p>
<h3><?php _e('Generate New Keypair', $this->text_domain); ?></h3>
<p>
	<?php _e('Use the following options to generate &amp; store a new RSA keypair.', $this->text_domain); ?>
</p>
<p>
	<?php _e('<em>A lower number of bits requires less processing power, but also limits the amount of data it can encode. More bits corresponds to both better encryption and higher processing load.</em>', $this->text_domain); ?>
</p>
<form method="post" action="">
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Number of bits', $this->text_domain); ?></th>
			<td>
				<select name="rsa_numbits">
					<option value="512"><?php _e('512 bits', $this->text_domain); ?></option>
					<option value="1024" selected="selected"><?php _e('1024 bits', $this->text_domain); ?></option>
					<option value="2048"><?php _e('2048 bits', $this->text_domain); ?></option>
					<option value="3072"><?php _e('3072 bits', $this->text_domain); ?></option>
				</select>
				<br/><?php _e('1024 bits is currently recommended based on the tradeoff between performance and security.', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Public-exponent', $this->text_domain); ?></th>
			<td>
				<select name="rsa_publicexponent">
					<option value="3"><?php _e('3 (0x3)', $this->text_domain); ?></option>
					<option value="10001" selected="selected"><?php _e('F4 (0x10001)', $this->text_domain); ?></option>
				</select>
				<br /><?php _e('OpenSSL defaults to F4; 0x3 has slightly faster encryption times<br /><em>Note: the alternative keypair generation method will not respect this value</em>', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<hr style="text-align:center;width:50%;" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Force alt method', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="force_alt_method" value="yes" />  <?php _e('Yes', $this->text_domain); ?> </label>
				<br /><?php _e('There are two keypair generation methods. The default method makes a direct call against OpenSSL on the server. The alternative method uses PHP\'s built-in OpenSSL functions. The alternative method will automatically be used if: PHP\'s execution functions are disabled, safe mode is enabled, or OpenSSL is not in the location specified in the advanced options file. You can also force the alternative method using this option.', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Show debug info', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="show_debug_info" value="yes" /> <?php _e('Yes', $this->text_domain); ?> </label>
				<br /><?php _e('If the keypair is not generating correctly, then you can use this option to find out why.', $this->text_domain); ?>
			</td>
		</tr>
	</table>
	<p class="submit">
		<?php wp_nonce_field('generate_semisecure-login-reimagined'); ?>
		<input type="submit" name="Submit" value="<?php _e('Generate Key &raquo;', $this->text_domain); ?>" />
	</p>
</form>
<p>
	<?php printf(__('Click <a href="%s" target="_blank">here</a> to create a new RSA keypair via JavaScript in your browser.', $this->text_domain), $plugin_url.'/help/generate_key.php'); ?>
</p>

<?php elseif ($sub == 'misc') : ?>

<form method="post" action="">
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('User login', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="do_login_encrypt" value="yes" <?php if ($do_encrypt_login) { echo 'checked="checked"'; } ?> /> <?php _e('Encrypt passwords when logging in?', $this->text_domain); ?> </label>
				<br/><?php _e('This includes the wp-login.php page as well as any plugins that implement the login_head and login_form hooks.', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('User administration', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="do_admin_encrypt" value="yes" <?php if ($do_encrypt_admin) { echo 'checked="checked"'; } ?> /> <?php _e('Encrypt passwords when managing users?', $this->text_domain); ?> </label>
				<br/><?php _e('This includes changing or setting a password when: editing your own user (wp-admin/profile.php), editing another user (wp-admin/user-edit.php), and creating a new user (wp-admin/user-new.php).', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Secret-key algorithm', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="secret_key_algo" value="marc4" <?php if ($secret_key_algo == 'marc4') echo 'checked="checked"'; ?> /> <?php _e('MARC4', $this->text_domain); ?> </label>
				<label><input type="radio" name="secret_key_algo" value="rabbit" <?php if ($secret_key_algo == 'rabbit') echo 'checked="checked"'; if (!$is_64bit_int) echo ' disabled="disabled"'; ?> /> <?php if(!$is_64bit_int) echo '<del>'; _e('Rabbit', $this->text_domain); if(!$is_64bit_int) echo '</del>'; ?> </label>
				<label><input type="radio" name="secret_key_algo" value="aes-cbc" <?php if ($secret_key_algo == 'aes-cbc') echo 'checked="checked"'; ?> /> <?php _e('AES (CBC)', $this->text_domain); ?> </label>
				<label><input type="radio" name="secret_key_algo" value="aes-ofb" <?php if ($secret_key_algo == 'aes-ofb') echo 'checked="checked"'; ?> /> <?php _e('AES (OFB)', $this->text_domain); ?> </label>
				<br/><?php _e('MARC4 (Modified Allegedly RC4) is based on RC4, a widely-used stream cipher. This modified version corrects certain weaknesses found in RC4. Rabbit is a high-performance stream cipher and a finalist in the eSTREAM portfolio. The Advanced Encryption Standard (AES) is a block cipher, and an encryption standard adopted by the U.S. government. CBC (cipher-block chaining) is one of the most common block cipher modes. OFB (output feedback) is a mode that turns a block cipher into a synchronous stream cipher.<br /><em>Note: Rabbit currently requires 64-bit PHP, and will not be selectable if you\'re running 32-bit PHP.</em>', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Nonce', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="nonce_method" value="direct" <?php if ($nonce_method == 'direct') echo 'checked="checked"'; ?> /> <?php _e('Print directly', $this->text_domain); ?> </label>
				<label><input type="radio" name="nonce_method" value="async" <?php if ($nonce_method == 'async') echo 'checked="checked"'; ?> /> <?php _e('Async (Ajax)', $this->text_domain); ?> </label>
				<label><input type="radio" name="nonce_method" value="disable" <?php if ($nonce_method == 'disable') echo 'checked="checked"'; ?> /> <?php _e('Disable', $this->text_domain); ?> </label>
				<br/><?php _e('<em>Print directly</em> means that the nonce is passed directly from PHP to JavaScript. The <em>asynchronous</em> option will use Ajax to dynamically retrieve the current nonce value. This might be necessary if you\'re using a caching plugin. You can also choose to <em>disable</em> nonce support. Nonces are used to protect against login replays. This plugin doesn\'t protect against session hijacking, so nonces aren\'t strictly needed.', $this->text_domain); ?>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input type="hidden" name="where_to_encrypt" value="postback" />
		<?php wp_nonce_field('misc_semisecure-login-reimagined'); ?>
		<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;', $this->text_domain); ?>" />
	</p>
</form>

<?php elseif ($sub == 'integration') : ?>

<p>
  <?php _e('By default, this plugin will encrypt your password on the <code>wp-login.php</code> page. Optionally, it can also encrypt your password on the <code>wp-admin/profile.php</code>, <code>wp-admin/user-edit.php</code>, and <code>wp-admin/user-new.php</code> pages. Here\'s a quick guide showing how you can integrate with this plugin to encrypt passwords on other pages too (most likely via a plugin).', $this->text_domain); ?>
</p>
<p>
  <?php _e('<em>Note: v3 of this plugin has made some (breaking) changes from the v2 integration</em>', $this->text_domain); ?>
</p>
<p>
  <?php _e('For starters, let\'s assume you have a form like the following (this only works for forms where <code>method="post"</code>):', $this->text_domain); ?>
</p>
<pre style="border:1px solid #000;margin:15px;overflow:auto;padding:5px;color:#000;background-color:#F9F9F9;">
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;form</span> <span style="color: #000066;">id</span>=<span style="color: #ff0000;">&quot;loginform&quot;</span> <span style="color: #000066;">method</span>=<span style="color: #ff0000;">&quot;post&quot;</span> <span style="color: #000066;">action</span>=<span style="color: #ff0000;">&quot;&quot;</span><span style="color: #000000; font-weight: bold;">&gt;</span></span>
  <span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;input</span> <span style="color: #000066;">type</span>=<span style="color: #ff0000;">&quot;password&quot;</span> <span style="color: #000066;">id</span>=<span style="color: #ff0000;">&quot;user_pwd&quot;</span> <span style="color: #000066;">name</span>=<span style="color: #ff0000;">&quot;pwd&quot;</span> <span style="color: #000000; font-weight: bold;">/&gt;</span></span>
  <span style="color: #808080; font-style: italic;">&lt;!-- <?php _e('Other form elements', $this->text_domain); ?> --&gt;</span>
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;/form<span style="color: #000000; font-weight: bold;">&gt;</span></span></span>
</pre>
<p>
  <?php _e('The basics of encrypting are as follows:', $this->text_domain); ?>
</p>
<pre style="border:1px solid #000;margin:15px;overflow:auto;padding:5px;color:#000;background-color:#F9F9F9;">
<span style="color: #000000; font-weight: bold;">&lt;?php</span>
  <span style="color: #666666; font-style: italic;">// <?php _e('Make sure that all the external JavaScript is available', $this->text_domain); ?></span>
  <span style="color: #666666; font-style: italic;">// <?php _e('Optionally, pass TRUE into this method if the page doesn\'t automatically call wp_print_scripts', $this->text_domain); ?></span>
  SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">enqueue_js</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
<span style="color: #000000; font-weight: bold;">?&gt;</span>
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;script</span> <span style="color: #000066;">type</span>=<span style="color: #ff0000;">&quot;text/javascript&quot;</span><span style="color: #000000; font-weight: bold;">&gt;</span></span>
jQuery<span style="color: #009900;">&#40;</span>document<span style="color: #009900;">&#41;</span>.<span style="color: #660066;">ready</span><span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">function</span><span style="color: #009900;">&#40;</span>$<span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
  <span style="color: #006600; font-style: italic;">// <?php _e('Bind to the form\'s submit event', $this->text_domain); ?></span>
  $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'form#loginform'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">submit</span><span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">function</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
    <span style="color: #006600; font-style: italic;">// <?php _e('Collect the password(s) and form name(s)', $this->text_domain); ?></span>
    <span style="color: #003366; font-weight: bold;">var</span> password <span style="color: #339933;">=</span> $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'#user_pwd'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">val</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> <span style="color: #000066;">name</span> <span style="color: #339933;">=</span> <span style="color: #3366CC;">'pwd'</span><span style="color: #339933;">;</span>

    <span style="color: #006600; font-style: italic;">// <?php _e('Pass the needed PHP values over to the JavaScript side', $this->text_domain); ?></span>
    <span style="color: #003366; font-weight: bold;">var</span> public_n <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">public_n</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> public_e <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">public_e</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> uuid <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">uuid</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> nonce_js <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">nonce_js</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> max_rand_chars <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">max_rand_chars</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> rand_chars <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> <span style="color: #990000;">addslashes</span><span style="color: #009900;">&#40;</span>SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">rand_chars</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>
    <span style="color: #003366; font-weight: bold;">var</span> secret_key_algo <span style="color: #339933;">=</span> <span style="color: #3366CC;">'<span style="color: #000000; font-weight: bold;">&lt;?php</span> <span style="color: #b1b100;">echo</span> SemisecureLoginReimagined<span style="color: #339933;">::</span><span style="color: #004000;">secret_key_algo</span><span style="color: #009900;">&#40;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span> <span style="color: #000000; font-weight: bold;">?&gt;</span>'</span><span style="color: #339933;">;</span>

    <span style="color: #006600; font-style: italic;">// <?php _e('Encrypt the password(s)', $this->text_domain); ?></span>
    <span style="color: #006600; font-style: italic;">// <?php _e('This function will return an Array on success or FALSE on failure', $this->text_domain); ?></span>
    <span style="color: #003366; font-weight: bold;">var</span> arr <span style="color: #339933;">=</span> SemisecureLoginReimagined.<span style="color: #660066;">encrypt</span><span style="color: #009900;">&#40;</span>password<span style="color: #339933;">,</span> <span style="color: #000066;">name</span><span style="color: #339933;">,</span> nonce_js<span style="color: #339933;">,</span> public_n<span style="color: #339933;">,</span> public_e<span style="color: #339933;">,</span> uuid<span style="color: #339933;">,</span> secret_key_algo<span style="color: #339933;">,</span> rand_chars<span style="color: #339933;">,</span> max_rand_chars<span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>

    <span style="color: #000066; font-weight: bold;">if</span> <span style="color: #009900;">&#40;</span>arr<span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
      <span style="color: #006600; font-style: italic;">// <?php _e('Loop through the array and append the controls to the form', $this->text_domain); ?></span>
      <span style="color: #000066; font-weight: bold;">for</span> <span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">var</span> i <span style="color: #339933;">=</span> <span style="color: #CC0000;">0</span><span style="color: #339933;">;</span> i <span style="color: #339933;">&lt;</span> arr.<span style="color: #660066;">length</span><span style="color: #339933;">;</span> i<span style="color: #339933;">++</span><span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span>
        $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'form#loginform'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">append</span><span style="color: #009900;">&#40;</span>arr<span style="color: #009900;">&#91;</span>i<span style="color: #009900;">&#93;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
      <span style="color: #009900;">&#125;</span>

      <span style="color: #006600; font-style: italic;">// <?php _e('Finally, don\'t submit the plain-text password(s)', $this->text_domain); ?></span>
      <span style="color: #006600; font-style: italic;">// <?php _e('One option is to submit asterisks in place of the actual password', $this->text_domain); ?></span>
      <span style="color: #003366; font-weight: bold;">var</span> temp <span style="color: #339933;">=</span> <span style="color: #3366CC;">''</span><span style="color: #339933;">;</span>
      <span style="color: #000066; font-weight: bold;">for</span> <span style="color: #009900;">&#40;</span><span style="color: #003366; font-weight: bold;">var</span> i <span style="color: #339933;">=</span> <span style="color: #CC0000;">0</span><span style="color: #339933;">;</span> i <span style="color: #339933;">&lt;</span> password.<span style="color: #660066;">length</span><span style="color: #339933;">;</span> i<span style="color: #339933;">++</span><span style="color: #009900;">&#41;</span> <span style="color: #009900;">&#123;</span> temp <span style="color: #339933;">+=</span> <span style="color: #3366CC;">'*'</span><span style="color: #339933;">;</span> <span style="color: #009900;">&#125;</span>
      $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'#user_pwd'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">val</span><span style="color: #009900;">&#40;</span>temp<span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
      <span style="color: #006600; font-style: italic;">// <?php _e('Another option is to disable the control(s) with the plain-text password(s) altogether', $this->text_domain); ?></span>
      $<span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'#user_pwd'</span><span style="color: #009900;">&#41;</span>.<span style="color: #660066;">attr</span><span style="color: #009900;">&#40;</span><span style="color: #3366CC;">'disabled'</span><span style="color: #339933;">,</span> <span style="color: #3366CC;">'true'</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
    <span style="color: #009900;">&#125;</span>
  <span style="color: #009900;">&#125;</span><span style="color: #009900;">&#41;</span><span style="color: #339933;">;</span>
<span style="color: #009900;">&#125;</span><span style="color: #009900;">&#41;</span>
<span style="color: #009900;"><span style="color: #000000; font-weight: bold;">&lt;/script<span style="color: #000000; font-weight: bold;">&gt;</span></span></span>
</pre>
<p>
  <?php _e('After WordPress\' <code>init</code> hook has run, <code>$_POST[\'pwd\']</code> will contain the decrypted password.', $this->text_domain); ?>
</p>
<p>
  <?php _e('It\'s possible to pass multiple <em>passwords</em> and <em>names</em> into the <code>SemisecureLoginReimagined.encrypt</code> function. Instead of passing a single string, you can pass an array of strings. Just make sure that the password values match up with the names by keeping the elements in the same respecitve order.', $this->text_domain); ?>
</p>
<p>
  <?php _e('When creating your own integration, you might want to verify that this plugin has been activated. You can do that by using PHP\'s <code>method_exists</code> function. You can also compare the current version of this plugin by calling the <code>SemisecureLoginReimagined::version()</code> method.', $this->text_domain); ?>
</p>
<p>
  <?php _e('Two helper functions are available: <code>SemisecureLoginReimagined::is_rsa_key_ok()</code> and <code>SemisecureLoginReimagined::is_openssl_avail()</code>. These can be used to show an appropriate message to the user (both need to be true for this plugin to function properly).', $this->text_domain); ?>
</p>
<p>
  <?php _e('For best results, it\'s recommended to call <code>rng_seed_time</code> on each <em>keypress</em> or <em>mouse click</em>.', $this->text_domain); ?>
</p>
<p>
   <?php _e('For some complete examples, check out <code>inc/login_head.inc.php</code> and <code>inc/admin_head.inc.php</code> included with this plugin.', $this->text_domain); ?>
</p>

<?php elseif ($sub == 'uninstall') : ?>

<p>
	<?php _e('Deactivating this plugin does not remove any data that may have been created, such as the RSA keypair. To remove this extra data, you can uninstall this plugin here or delete it through the admin interface.', $this->text_domain); ?>
</p>
<p style="color:red;">
	<strong><?php _e('WARNING:', $this->text_domain); ?></strong><br /><?php _e('Once uninstalled, this cannot be undone. Make sure this is what you want to do. Optionally, you should backup your WordPress database first.', $this->text_domain); ?>
</p>
<p>
	<?php _e('Do you want to remove the following option(s) from the database?', $this->text_domain); ?>
</p>
<table class="widefat">
<thead>
	<tr>
		<th><?php _e('WordPress Options', $this->text_domain); ?></th>
	</tr>
</thead>
	<tr>
		<td valign="top">
			<ol>
			<li>semisecurelogin_reimagined_encrypt_admin</li>
			<li>semisecurelogin_reimagined_encrypt_login</li>
			<li>semisecurelogin_reimagined_more_settings</li>
			<li>semisecurelogin_reimagined_rsa_keys</li>
			<li>semisecurelogin_reimagined_secretkey_algo</li>
			</ol>
		</td>
	</tr>
</table>
<form method="post" action="">
<p style="text-align: center;">
	<label><input type="checkbox" name="uninstall_semisecure_login_reimagined" value="yes" /> <?php _e('Yes', $this->text_domain); ?> </label><br /><br />
	<?php wp_nonce_field('uninstall_semisecure-login-reimagined'); ?>
	<input class="button" type="submit" name="Submit" value="<?php _e('UNINSTALL &raquo;', $this->text_domain); ?>" />
</p>
</form>

<?php endif; ?>
</div>
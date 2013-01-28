<?php require_once(dirname(dirname(__FILE__)).'/inc/help_file_check.inc.php'); ?>
<html>
<head>
	<title><?php _e('Help: SemisecureLoginReimagined Integration Details', $text_domain); ?></title>
	<style type="text/css">
	body {
		background-color: #F9F9F9;
		color: #000;
	}
	pre {
		margin: 15px;
		padding: 5px;
		overflow: auto;
	}
	pre, p span {
		background-color: #E6E6E6;
		color: #000066;
	}
	</style>
</head>
<body>

<h2><?php _e('SemisecureLoginReimagined Integration Details', $text_domain); ?></h2>

<p>
	<?php _e('By default, this plugin will encrypt your password on the <span>wp-login.php</span> page. Optionally, it can also encrypt your password on the <span>wp-admin/profile.php</span>, <span>wp-admin/user-edit.php</span>, and <span>wp-admin/user-new.php</span> pages. Here\'s a quick guide showing how you can integrate with this plugin to encrypt passwords on other pages too (most likely via a plugin).', $text_domain); ?>
</p>

<p>
	<?php _e('Assume you have a form that submits a password like the following (this only works for forms where <span>method="post"</span>).', $text_domain); ?>
</p>
<pre>
&lt;form id="loginform" method="post" action=""&gt;
	&lt;input type="password" id="pwd" name="pwd" value="" /&gt;
	&lt;!-- <?php _e('Other form elements', $text_domain); ?> --&gt;
&lt;/form&gt;
</pre>

<hr/>

<p>
	<?php _e('You\'ll probably want to check to see if the plugin has been activated. This could be as simple as checking that the <span>uuid</span> method exists. (You could just check to see if the class exists, but v1.x doesn\'t have integration support.)', $text_domain); ?>
</p>
<pre>
&lt;?php
	if (method_exists('SemisecureLoginReimagined', 'uuid')) {
		// <?php echo __('put code here', $text_domain) . "\n"; ?>
	}
?&gt;
</pre>

<hr/>

<p>
	<?php printf(__('Use the following to include the <a href="%s" target="_blank">jsbn</a> JavaScript files which are needed for client-side RSA encryption. (<span>jsbn.js</span>, <span>prng4.js</span>, <span>rng.js</span>, <span>rsa.js</span>, and <span>base64.js</span>)', $text_domain), 'http://www-cs-students.stanford.edu/~tjw/jsbn/'); ?>
</p>
<pre>
&lt;?php
	// <?php echo __('prints out through wp_enqueue_script/wp_print_scripts', $text_domain) . "\n"; ?>
	// <?php echo __('takes two optional boolean values', $text_domain) . "\n"; ?>
	//   <?php echo __('(1) if you want to include base64.js (defaults to TRUE)', $text_domain) . "\n"; ?>
	//   <?php echo __('(2) if you want this function to call wp_print_scripts itself (defaults to FALSE)', $text_domain) . "\n"; ?>
	SemisecureLoginReimagined::enqueue_jsbn_js();
?&gt;
</pre>

<hr/>

<p>
	<?php _e('The JavaScript that follows should be bound to the form\'s submit event.', $text_domain); ?>
</p>
<p>
	<?php _e('Here\'s one example:', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	document.getElementById('loginform').setAttribute('onsubmit', 'javascript:return encryptPwd();');
	function encryptPwd() {
		// <?php echo __('put JavaScript code here', $text_domain) . "\n"; ?>
	}
&lt;/script&gt;
</pre>
<p>
	<?php _e('And here\'s another if you\'re using jQuery:', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	$('form#loginform').submit(function() {
		// <?php echo __('put JavaScript code here', $text_domain) . "\n"; ?>
	});
&lt;/script&gt;
</pre>

<hr/>

<p>
	<?php _e('Pass the RSA public-key through to your JavaScript code.', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	var public_n = '&lt;?php echo SemisecureLoginReimagined::public_n(); ?&gt;';
	var public_e = '&lt;?php echo SemisecureLoginReimagined::public_e(); ?&gt;';
&lt;/script&gt;
</pre>

<hr/>

<p>
	<?php _e('Setup the JavaScript RSAKey for encryption.', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	var rsa = new RSAKey();
	rsa.setPublic(public_n, public_e);
&lt;/script&gt;
</pre>

<hr/>

<p>
	<?php _e('Encrypt the nonce along with the password.', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	var password = document.getElementById('pwd').value;
	var res = rsa.encrypt('&lt;?php echo addslashes(SemisecureLoginReimagined::nonce()); ?&gt;' + password);
&lt;/script&gt;
</pre>

<hr/>

<p>
	<?php _e('Create a hidden form element and append it to the form. This new element\'s name should be the same as the original (in this case <span>pwd</span>) plus a double underscore plus the SemisecureLoginReimagined uuid. You can pass the encrypted password as either hex or base64 encoded data (<span>hex2b64</span> comes from <span>base64.js</span>).', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	var rsaPwd = document.createElement('input');
	rsaPwd.setAttribute('type', 'hidden');
	rsaPwd.setAttribute('name', 'pwd__&lt;?php echo SemisecureLoginReimagined::uuid(); ?&gt;';
	//rsaPwd.value = res; // <?php echo __('res is a hex string') . "\n"; ?>
	rsaPwd.value = hex2b64(res); // <?php echo __('convert the hex string to base64 (this is not the same as base64-ing the hex string!)') . "\n"; ?>
	document.getElementById('loginform').appendChild(rsaPwd);
&lt;/script&gt;
</pre>

<hr/>

<p>
	<?php _e('Finally, DON\'T submit the plain-text password! There\'s a couple different options here.', $text_domain); ?>
</p>
<p>
	<?php _e('You can either replace the contents of the password field', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	var temp = '';
	for (var i = 0; i &lt; document.getElementById('pwd').length; i++) { temp += '*'; }
	document.getElementById('pwd').value = temp;
&lt;/script&gt;
</pre>
<p>
	<?php _e('and/or you can choose to NOT submit the password field at all.', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	document.getElementById('pwd').disabled = true;
&lt;/script&gt;
</pre>

<hr/>

<p>
	<?php _e('On the server-side, once WordPress has run the <span>init</span> hook, the following code will contain the decrypted value.', $text_domain); ?>
</p>
<pre>
&lt;?php
	echo $_POST['pwd'];
?&gt;
</pre>

<hr/>

<p>
	<?php _e('Other considerations:', $text_domain); ?>
</p>
<p>
	<?php _e('You\'ll probably want to include a message that SemisecureLoginReimagined is enabled.', $text_domain); ?>
</p>
<pre>
&lt;p id="semisecure-message"&gt;
	<?php echo __('Semisecure Login is enabled.', $text_domain) . "\n"; ?>
&lt;/p&gt;
</pre>

<p>
	<?php _e('Here\'s a couple functions to let you know if the RSA key is okay and if openssl is available (both of which are required for this plugin to work). These could be used to change the message that the user sees.', $text_domain); ?>
</p>
<pre>
&lt;?php
	if (SemisecureLoginReimagined::is_rsa_key_ok() &amp;&amp; SemisecureLoginReimagined::is_openssl_avail()) {
		// <?php echo __('Semisecure Login is enabled', $text_domain) . "\n"; ?>
	}
	else {
		// <?php echo __('Semisecure Login is NOT enabled', $text_domain) . "\n"; ?>
	}
?&gt;
</pre>

<p>
	<?php _e('The JavaScript random number generator being used here recommends calling <span>rng_seed_time</span> on each keypress or mouse click.', $text_domain); ?>
</p>
<p>
	<?php _e('Here\'s one example:', $text_domain); ?>
</p>
<pre>
&lt;body onclick="rng_seed_time();" onkeypress="rng_seed_time();"&gt;
</pre>
<p>
	<?php _e('And here\'s another if you\'re using jQuery:', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	$(window).click(rng_seed_time);
	$(window).keypress(rng_seed_time);
&lt;/script&gt;
</pre>

<p>
	<?php _e('Finally, you can get the current version of this plugin with the following code:', $text_domain); ?>
</p>
<pre>
&lt;?php
	echo SemisecureLoginReimagined::version();
?&gt;
</pre>

<hr/>

<p>
	<?php _e('For some complete examples, check out <span>inc/login_head.inc.php</span> and <span>inc/admin_head.inc.php</span> included with this plugin.', $text_domain); ?>
</p>

<hr/>
<hr/>

<p>
	<?php _e('As of v2.0.1 the nonce can now be loaded in dynamically. This might be necessary if you\'re using a caching plugin.', $text_domain); ?>
</p>
<p>
	<?php _e('Here\'s two jQuery examples:', $text_domain); ?>
</p>
<pre>
&lt;script type="text/javascript"&gt;
	var nonce = '';
	$.ajax({
		url: '&lt;?php echo SemisecureLoginReimagined::nonce_js(); ?&gt;',
		cache: false,
		async: false,
		success: function(data) {
			nonce = data;
		}
	});
&lt;/script&gt;
</pre>
<pre>
&lt;script type="text/javascript"&gt;
	var nonce = '';
	$.getScript('&lt;?php echo SemisecureLoginReimagined::nonce_js(true); ?&gt;', function() {
		nonce = SemisecureLoginReimagined_nonce;
	});
&lt;/script&gt;
</pre>

</body>
</html>
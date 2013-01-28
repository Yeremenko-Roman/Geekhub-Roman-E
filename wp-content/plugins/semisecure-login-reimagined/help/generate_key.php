<?php
	require_once(dirname(dirname(__FILE__)).'/inc/help_file_check.inc.php');
	if (!isset($table_prefix)) $table_prefix = 'wp_'; // if we weren't able to access the WP core ($wpdb->prefix)
?>
<html>
<head>

<title><?php _e('Help: Generate RSA Keypair (SemisecureLoginReimagined)', $text_domain); ?></title>

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
pre, p span, textarea, input.text {
	background-color: #E6E6E6;
	color: #000066;
}
</style>

<script type="text/javascript" src="../js/php/serialize.min.js"></script>
<script type="text/javascript" src="../js/jsbn/jsbn.min.js"></script>
<script type="text/javascript" src="../js/jsbn/jsbn2.min.js"></script>
<script type="text/javascript" src="../js/jsbn/prng4.min.js"></script>
<script type="text/javascript" src="../js/jsbn/rng.min.js"></script>
<script type="text/javascript" src="../js/jsbn/rsa.min.js"></script>
<script type="text/javascript" src="../js/jsbn/rsa2.min.js"></script>
<script type="text/javascript" src="../js/jsbn/base64.min.js"></script>
<script type="text/javascript">
//<![CDATA[
function get_privatekey(numbits, pubexp, n, d, p, q, dmp1, dmq1, coeff) {
	var privatekey = '020100';
	if (numbits == '512') {
		/* modulus */
		privatekey += '0241'; /* 02 means that what follows is an integer; 41(hex) == 64 length (x2 == 128) */
		if (n.length == 128) /* else 130 */
			privatekey += '00';
		privatekey += n;

		/* public exponent */
		if (pubexp == '3')
			privatekey += '0201' + '03';
		else /* pubexp == '10001' */
			privatekey += '0203' + '010001';

		/* private exponent */
		privatekey += '0241';
		if (d.length == 128) /* else 130 */
			privatekey += '00';
		privatekey += d;

		/* prime1 */
		privatekey += '0221';
		if (p.length == 64) /* else 66 */
			privatekey += '00';
		privatekey += p;

		/* prime2 */
		privatekey += '0221';
		if (q.length == 64) /* else 66 */
			privatekey += '00';
		privatekey += q;

		/* exp1 */
		privatekey += '0221';
		if (dmp1.length == 64) /* else 66 */
			privatekey += '00';
		privatekey += dmp1;

		/* exp2 */
		privatekey += '0221';
		if (dmq1.length == 64) /* else 66 */
			privatekey += '00';
		privatekey += dmq1;

		/* coeff */
		privatekey += '0221';
		if (coeff.length == 64) /* else 66 */
			privatekey += '00';
		privatekey += coeff;
	}
	else { /* numbits == '1024' */
		/* modulus */
		privatekey += '028181'; /* the length should be 81(hex), openssl seems to always encode this as 8181 though */
		if (n.length == 256) /* else 258 */
			privatekey += '00';
		privatekey += n;

		/* public exponent */
		if (pubexp == '3')
			privatekey += '0201' + '03';
		else /* pubexp == '10001' */
			privatekey += '0203' + '010001';

		/* private exponent */
		privatekey += '028181'; /* the length should be 81(hex), openssl seems to always encode this as 8181 though */
		if (d.length == 256) /* else 258 */
			privatekey += '00';
		privatekey += d;

		/* prime1 */
		privatekey += '0241';
		if (p.length == 128) /* else 130 */
			privatekey += '00';
		privatekey += p;

		/* prime2 */
		privatekey += '0241';
		if (q.length == 128) /* else 130 */
			privatekey += '00';
		privatekey += q;

		/* exp1 */
		privatekey += '0241';
		if (dmp1.length == 128) /* else 130 */
			privatekey += '00';
		privatekey += dmp1;

		/* exp2 */
		privatekey += '0241';
		if (dmq1.length == 128) /* else 130 */
			privatekey += '00';
		privatekey += dmq1;

		/* coeff */
		privatekey += '0241';
		if (coeff.length == 128) /* else 130 */
			privatekey += '00';
		privatekey += coeff;
	}

	var privlength = (privatekey.length / 2).toString(16).toUpperCase();
	if (privlength.length % 2 != 0)
		privlength = '0' + privlength;
	privatekey = '3082' + privlength + privatekey;

	return privatekey;
}
function get_publickey(numbits, pubexp, n) {
	var publickey = '00' + n;
	var publiclength = (publickey.length / 2).toString(16).toUpperCase();
	if (publiclength.length % 2 != 0)
		publiclength = '0' + publiclength;

	if (numbits == '512') {
		publickey = '02' + publiclength + publickey;

		if (pubexp == '3')
			publickey += '0201' + '03';
		else /* pubexp == '10001' */
			publickey += '0203' + '010001';

		publiclength = (publickey.length / 2).toString(16).toUpperCase();
		if (publiclength.length % 2 != 0)
			publiclength = '0' + publiclength;
		publickey = '30' + publiclength + publickey;

		publickey = '00' + publickey;

		publiclength = (publickey.length / 2).toString(16).toUpperCase();
		if (publiclength.length % 2 != 0)
			publiclength = '0' + publiclength;
		publickey = '03' + publiclength + publickey;

		publickey = '300D06092A864886F70D0101010500' + publickey;

		publiclength = (publickey.length / 2).toString(16).toUpperCase();
		if (publiclength.length % 2 != 0)
			publiclength = '0' + publiclength;
		publickey = '30' + publiclength + publickey;
	}
	else { /* numbits == '1024' */
		publickey = '0281' + publiclength + publickey;

		if (pubexp == '3')
			publickey += '0201' + '03';
		else /* pubexp == '10001' */
			publickey += '0203' + '010001';

		publiclength = (publickey.length / 2).toString(16).toUpperCase();
		if (publiclength.length % 2 != 0)
			publiclength = '0' + publiclength;
		publickey = '3081' + publiclength + publickey;

		publickey = '00' + publickey;

		publiclength = (publickey.length / 2).toString(16).toUpperCase();
		if (publiclength.length % 2 != 0)
			publiclength = '0' + publiclength;
		publickey = '0381' + publiclength + publickey;

		publickey = '300D06092A864886F70D0101010500' + publickey;

		publiclength = (publickey.length / 2).toString(16).toUpperCase();
		if (publiclength.length % 2 != 0)
			publiclength = '0' + publiclength;
		publickey = '3081' + publiclength + publickey;
	}

	return publickey;
}
function generate_key() {
	var numbits = document.getElementById('numbits').value;
	var pubexp = document.getElementById('pubexp').value;
	var prefix = document.getElementById('prefix').value;

	var rsa = new RSAKey();
	rsa.generate(numbits, pubexp); /* this is where the script will freeze up */

	var n = (rsa.n.toString(16).length % 2 == 0) ? rsa.n.toString(16).toUpperCase() : '0' + rsa.n.toString(16).toUpperCase();
	var d = (rsa.d.toString(16).length % 2 == 0) ? rsa.d.toString(16).toUpperCase() : '0' + rsa.d.toString(16).toUpperCase();
	var p = (rsa.p.toString(16).length % 2 == 0) ? rsa.p.toString(16).toUpperCase() : '0' + rsa.p.toString(16).toUpperCase();
	var q = (rsa.q.toString(16).length % 2 == 0) ? rsa.q.toString(16).toUpperCase() : '0' + rsa.q.toString(16).toUpperCase();
	var dmp1 = (rsa.dmp1.toString(16).length % 2 == 0) ? rsa.dmp1.toString(16).toUpperCase() : '0' + rsa.dmp1.toString(16).toUpperCase();
	var dmq1 = (rsa.dmq1.toString(16).length % 2 == 0) ? rsa.dmq1.toString(16).toUpperCase() : '0' + rsa.dmq1.toString(16).toUpperCase();
	var coeff = (rsa.coeff.toString(16).length % 2 == 0) ? rsa.coeff.toString(16).toUpperCase() : '0' + rsa.coeff.toString(16).toUpperCase();

	var privatekey = get_privatekey(numbits, pubexp, n, d, p, q, dmp1, dmq1, coeff);
	privatekey = hex2b64(privatekey);
	privatekey = linebrk(privatekey, 64);
	privatekey = '-----BEGIN RSA PRIVATE KEY-----\n' + privatekey + '\n-----END RSA PRIVATE KEY-----';

	var publickey = get_publickey(numbits, pubexp, n);
	publickey = hex2b64(publickey);
	publickey = linebrk(publickey, 64);
	publickey = '-----BEGIN PUBLIC KEY-----\n' + publickey + '\n-----END PUBLIC KEY-----';

	var sqlarray = new Array();
	sqlarray['numbits'] = parseInt(numbits);
	sqlarray['publicexponent'] = parseInt(pubexp);
	sqlarray['modulus'] = n;
	sqlarray['privatekey'] = privatekey;
	sqlarray['publickey'] = publickey;

	var serialized = serialize(sqlarray);

	document.getElementById('priv').value = privatekey;
	document.getElementById('pub').value = publickey;
	document.getElementById('table_name').value = prefix + "options";
	document.getElementById('option_name').value = "semisecurelogin_reimagined_rsa_keys";
	document.getElementById('option_value').value = serialized;
	document.getElementById('select').value = "SELECT * FROM " + prefix + "options WHERE option_name = 'semisecurelogin_reimagined_rsa_keys'"
	document.getElementById('insert').value = "INSERT INTO " + prefix + "options (option_name, option_value)\nVALUES ('semisecurelogin_reimagined_rsa_keys', '" + serialized + "')";
	document.getElementById('update').value = "UPDATE " + prefix + "options\nSET option_value = '" + serialized + "'\nWHERE option_name = 'semisecurelogin_reimagined_rsa_keys'";

	document.getElementById('modulus').value = linebrk(n, 64);
	document.getElementById('public_exponent').value = '0' + pubexp;
	document.getElementById('private_exponent').value = linebrk(d, 64);
	document.getElementById('prime1').value = linebrk(p, 64);
	document.getElementById('prime2').value = linebrk(q, 64);
	document.getElementById('exp1').value = linebrk(dmp1, 64);
	document.getElementById('exp2').value = linebrk(dmq1, 64);
	document.getElementById('coeff').value = linebrk(coeff, 64);
}
function toggle_keypair_components() {
	var block = document.getElementById('keypair_components');
	if (block.style.display == 'block') {
		block.style.display = 'none';
		document.getElementById('toggle_header').innerHTML = '[+]';
	}
	else { /* 'none' */
		block.style.display = 'block';
		document.getElementById('toggle_header').innerHTML = '[-]';
	}
}
//]]>
</script>

</head>
<body onclick="rng_seed_time();" onkeypress="rng_seed_time();">

<h2><?php _e('Generate RSA Keypair', $text_domain); ?></h2>

<p>
	<?php _e('If you\'re unable to generate a keypair on the settings page (for whatever reason) then you can attempt to generate a keypair directly in your browser here. For this to be considered <span>secure</span> you\'ll need to be able to insert data into your database securely (it\'s not a good idea to pass your private key over http://).', $text_domain); ?>
</p>
<p>
	<?php _e('For example, even if you don\'t have an SSL certificate for your website, your host might allow you to connect securely (via https://) to cPanel and phpMyAdmin.', $text_domain); ?>
</p>
<p>
	<?php _e('The number of bits here is limited to 512 or 1024 because generating an RSA keypair in JavaScript is very CPU intensive. Your browser might prompt you to stop or continue the script if it\'s been running too long. Choose continue to finish generating the key.', $text_domain); ?>
</p>
<p>
	<?php _e('For the generated SQL to be correct, make sure to set your WordPress table prefix as defined in <span>wp-config.php</span>', $text_domain); ?>
</p>
<p>
	<?php _e('After manually inserting/updating the keypair in the database, make sure to re-load the plugin\'s settings page! This will verify that the keypair is OK, and will automatically try to repair it if there was a problem.', $text_domain); ?>
</p>

<hr />

<table border="0">
	<tr>
		<td><?php _e('Number of bits', $text_domain); ?></td>
		<td>
			<select id="numbits">
				<option value="512" selected="selected"><?php _e('512 bits', $text_domain); ?></option>
				<option value="1024"><?php _e('1024 bits', $text_domain); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php _e('Public-exponent', $text_domain); ?></td>
		<td>
			<select id="pubexp">
				<option value="3"><?php _e('3 (0x3)', $text_domain); ?></option>
				<option value="10001" selected="selected"><?php _e('F4 (0x10001)', $text_domain); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?php _e('Table prefix', $text_domain); ?></td>
		<td>
			<input type="text" class="text" id="prefix" style="width:100px;" value="<?php echo htmlspecialchars($table_prefix, ENT_QUOTES); ?>" />
		</td>
	</tr>
</table>

<p>
	<input type="button" id="generate" value="<?php _e('Generate Key &raquo;', $text_domain); ?>" onclick="generate_key()" />
</p>

<hr />

<h3><?php _e('RSA Key', $text_domain); ?></h3>
<table border="0" style="width:100%;">
	<tr>
		<td><textarea id="priv" style="width:100%;height:200px;" readonly="readonly"></textarea></td>
		<td><textarea id="pub" style="width:100%;height:200px;" readonly="readonly"></textarea></td>
	</tr>
</table>

<hr />

<h3><?php _e('What needs to be inserted into the database', $text_domain); ?></h3>
<table border="0" style="width:100%;">
	<tr>
		<th valign="top"><?php _e('Table name', $text_domain); ?></th>
		<td valign="top"><input type="text" class="text" style="width:100%;" id="table_name" value="" readonly="readonly" /></td>
	</tr>
	<tr>
		<th valign="top">option_name</th>
		<td valign="top"><input type="text" class="text" style="width:100%;" id="option_name" value="" readonly="readonly" /></td>
	</tr>
	<tr>
		<th valign="top">option_value</th>
		<td valign="top"><textarea id="option_value" style="width:100%;height:250px;" readonly="readonly"></textarea></td>
	</tr>
</table>

<hr />

<p>
	<?php _e('You can use the following SQL query to determine if you need to INSERT a new row or UPDATE the existing row.', $text_domain); ?>
</p>
<input style="width:100%;" type="text" id="select" value="" class="text" readonly="readonly" />

<p>
	<?php _e('If the previous query returned no rows then you can use the following to INSERT a new row.', $text_domain); ?>
</p>
<textarea style="width:100%;height:250px;" id="insert" readonly="readonly"></textarea>

<p>
	<?php _e('If the previous query returned one row then you can use the following to UPDATE it.', $text_domain); ?>
</p>
<textarea style="width:100%;height:250px;" id="update" readonly="readonly"></textarea>

<p>
	<?php _e('If the previous query returned more than one row then you have problems! In this case you should probably delete all but one of the returned rows, and then you can update the remaining one.', $text_domain); ?>
</p>

<hr />

<h3><?php _e('Toggle keypair components', $text_domain); ?> <a href="#" onclick="javascript:toggle_keypair_components();return false;"><span id="toggle_header">[+]</span></a></h3>

<div id="keypair_components" style="display:none;">

<p>
	<?php _e('Modulus (hex)', $text_domain); ?>
	<br /><textarea id="modulus" style="width:50%;height:75px;" readonly="readonly"></textarea>
</p>

<p>
	<?php _e('Public exponent (hex)', $text_domain); ?>
	<br /><input type="text" class="text" id="public_exponent" style="width:50%;" value="" readonly="readonly" />
</p>

<p>
	<?php _e('Private exponent (hex)', $text_domain); ?>
	<br /><textarea id="private_exponent" style="width:50%;height:75px;" readonly="readonly"></textarea>
</p>

<p>
	<?php _e('Prime1 (hex)', $text_domain); ?>
	<br /><textarea id="prime1" style="width:50%;height:50px;" readonly="readonly"></textarea>
</p>

<p>
	<?php _e('Prime2 (hex)', $text_domain); ?>
	<br /><textarea id="prime2" style="width:50%;height:50px;" readonly="readonly" /></textarea>
</p>

<p>
	<?php _e('Exponent1 (hex)', $text_domain); ?>
	<br /><textarea id="exp1" style="width:50%;height:50px;" value="" readonly="readonly"></textarea>
</p>

<p>
	<?php _e('Exponent2 (hex)', $text_domain); ?>
	<br /><textarea id="exp2" style="width:50%;height:50px;" readonly="readonly"></textarea>
</p>

<p>
	<?php _e('Coefficient (hex)', $text_domain); ?>
	<br /><textarea id="coeff" style="width:50%;height:50px;" readonly="readonly"></textarea>
</p>

</div>

</body>
</html>
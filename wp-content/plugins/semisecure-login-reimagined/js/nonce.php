<?php

if (isset($_GET['disable']))
	$nonce = '';
else {
	@session_start();
	require_once(dirname(dirname(__FILE__)).'/classes/SemisecureLoginReimagined.php');
	$nonce_session_key = SemisecureLoginReimagined::nonce_session_key();
	$nonce = $_SESSION[$nonce_session_key];
}

// load the nonce into a JavaScript variable if "js" is set on the query-string
if (isset($_GET['js'])) :
?>
var SemisecureLoginReimagined_nonce = '<?php echo addslashes($nonce); ?>';
<?php
else :

// otherwise just return the nonce directly
echo $nonce;

endif;
?>
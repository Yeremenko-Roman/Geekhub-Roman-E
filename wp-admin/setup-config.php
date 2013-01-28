<?php
/**
 * Retrieves and creates the wp-config.php file.
 *
 * The permissions for the base directory must allow for writing files in order
 * for the wp-config.php to be created using this page.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * We are installing.
 *
 * @package WordPress
 */
define('WP_INSTALLING', true);

/**#@+
 * These three defines are required to allow us to use require_wp_db() to load
 * the database class while being wp-content/db.php aware.
 * @ignore
 */
define('ABSPATH', dirname(dirname(__FILE__)).'/');
define('WPINC', 'wp-includes');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
/**#@-*/

require_once(ABSPATH . WPINC . '/compat.php');
require_once(ABSPATH . WPINC . '/functions.php');
require_once(ABSPATH . WPINC . '/classes.php');

if (!file_exists(ABSPATH . 'wp-config-sample.php'))
	wp_die('Извините, мне нужен файл wp-config-sample.php чтобы начать работу. Пожалуйста, перезагрузите этот файл из вашего дистрибутива WordPress.');

$configFile = file(ABSPATH . 'wp-config-sample.php');

if ( !is_writable(ABSPATH))
	wp_die("Извините, я не могу записать в директорию. Вы должны сделать изменения прав (например chmod 777 , chmod 775 или chmod 755 в зависимости от конфигурации сервера) в вашей директории WordPress или создайте ваш wp-config.php вручную. Не забывайте в любом случае про изменения дефолтных параметров ключей безопасности.");

// Check if wp-config.php has been created
if (file_exists(ABSPATH . 'wp-config.php'))
	wp_die("<p>Файл 'wp-config.php' уже существует. Если вам нужно сбросить любой из пунктов конфигурации в этом файле, удалите его для начала.  Вы можете попытаться <a href='install.php'>установить сейчас</a>.</p>");

// Check if wp-config.php exists above the root directory but is not part of another install
if (file_exists(ABSPATH . '../wp-config.php') && ! file_exists(ABSPATH . '../wp-settings.php'))
	wp_die("<p>Файл 'wp-config.php' уже существует одним уровнем выше вашей инсталляции WordPress. Если вам нужно сбросить любой из пунктов конфигурацию в этом файле, удалите его для начала.  Вы можете попытаться <a href='install.php'>установить сейчас</a>.</p>");

if (isset($_GET['step']))
	$step = $_GET['step'];
else
	$step = 0;

/**
 * Display setup wp-config.php file header.
 *
 * @ignore
 * @since 2.3.0
 * @package WordPress
 * @subpackage Installer_WP_Config
 */
function display_header() {
	header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>WordPress &rsaquo; Setup Configuration File</title>
<link rel="stylesheet" href="css/install.css" type="text/css" />

</head>
<body>
<h1 id="logo"><img alt="WordPress" src="images/wordpress-logo.png" /></h1>
<?php
}//end function display_header();

switch($step) {
	case 0:
		display_header();
?>

<p>Добро пожаловать в WordPress. Перед тем как мы начнем, нам нужно заполнить некоторую информацию о базе данных. Вам необходимо знать следующие вещи, прежде чем продолжить.</p>
<ol>
	<li>Имя базы данных</li>
	<li>Имя пользователя базы данных</li>
	<li>пароль базы данных</li>
	<li>Хост базы данных</li>
	<li>Префикс таблицы (если вы хотите запускать более чем один WordPress в единой базе данных) </li>
</ol>
<p><strong>Если по каким либо причинам автоматическое создание невозможно, не беспокойтесь. Всю информацию о базе данных можно заполнить в конфигурационном файле. Вы можете также просто открыть <code>wp-config-sample.php</code> в текстовом редакторе, корректно работающим с UTF-8 (например UnicEdit, Notepad++ ), заполнить вашу информацию, и сохранить файл как <code>wp-config.php</code>. </strong></p>
<p>По всей вероятности, эти данные были поставлены Вам вашим хостером. Если вы не имеете этой информации, тогда вам нужно связаться с ними прежде чем вы сможете продолжить. Если вы уже готовы,&hellip;</p>

<p class="step"><a href="setup-config.php?step=1" class="button">Поехали!</a></p>
<?php
	break;

	case 1:
		display_header();
	?>
<form method="post" action="setup-config.php?step=2">
	<p>В поля ниже вы должны ввести данные для подключения к базе данных. Если вы не разбираетесь в этом, обратитесь к вашему хостеру. </p>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="dbname">Имя базы данных</label></th>
			<td><input name="dbname" id="dbname" type="text" size="25" value="wordpress" /></td>
			<td>Имя базы данных, которую вы хотите использовать для работы с WP. </td>
		</tr>
		<tr>
			<th scope="row"><label for="uname">Имя пользователя</label></th>
			<td><input name="uname" id="uname" type="text" size="25" value="username" /></td>
			<td>Ваше имя пользователя MySQL</td>
		</tr>
		<tr>
			<th scope="row"><label for="pwd">Пароль</label></th>
			<td><input name="pwd" id="pwd" type="text" size="25" value="password" /></td>
			<td>...и MySQL пароль.</td>
		</tr>
		<tr>
			<th scope="row"><label for="dbhost">Сервер базы данных</label></th>
			<td><input name="dbhost" id="dbhost" type="text" size="25" value="localhost" /></td>
			<td>в 99% случаях вам не нужно менять это значение. (уточните у вашего хостера! Бывают варианты mysql , mysqlserver либо IP адрес сервера и т.д.)</td>
		</tr>
		<tr>
			<th scope="row"><label for="prefix">Префикс таблиц</label></th>
			<td><input name="prefix" id="prefix" type="text" id="prefix" value="wp_" size="25" /></td>
			<td>Если вы хотите запустить несколько инсталляций WordPress в единой базе данных, измените это.</td>
		</tr>
	</table>
	<p class="step"><input name="submit" type="submit" value="Отправить" class="button" /></p>
</form>
<?php
	break;

	case 2:
	$dbname  = trim($_POST['dbname']);
	$uname   = trim($_POST['uname']);
	$passwrd = trim($_POST['pwd']);
	$dbhost  = trim($_POST['dbhost']);
	$prefix  = trim($_POST['prefix']);
	if (empty($prefix)) $prefix = 'wp_';

	// Test the db connection.
	/**#@+
	 * @ignore
	 */
	define('DB_NAME', $dbname);
	define('DB_USER', $uname);
	define('DB_PASSWORD', $passwrd);
	define('DB_HOST', $dbhost);
	/**#@-*/

	// We'll fail here if the values are no good.
	require_wp_db();
	if ( !empty($wpdb->error) )
		wp_die($wpdb->error->get_error_message());

	$handle = fopen(ABSPATH . 'wp-config.php', 'w');

	foreach ($configFile as $line_num => $line) {
		switch (substr($line,0,16)) {
			case "define('DB_NAME'":
				fwrite($handle, str_replace("putyourdbnamehere", $dbname, $line));
				break;
			case "define('DB_USER'":
				fwrite($handle, str_replace("'usernamehere'", "'$uname'", $line));
				break;
			case "define('DB_PASSW":
				fwrite($handle, str_replace("'yourpasswordhere'", "'$passwrd'", $line));
				break;
			case "define('DB_HOST'":
				fwrite($handle, str_replace("localhost", $dbhost, $line));
				break;
			case '$table_prefix  =':
				fwrite($handle, str_replace('wp_', $prefix, $line));
				break;
			default:
				fwrite($handle, $line);
		}
	}
	fclose($handle);
	chmod(ABSPATH . 'wp-config.php', 0666);

	display_header();
?>
<p>Все в порядке! Вы выполнили эту части установки. WordPress теперь может общаться с базой данных. Если вы готовы, время приступить к </p>

<p class="step"><a href="install.php" class="button">Запуску инсталляции</a></p>
<?php
	break;
}
?>
</body>
</html>

<?php
/**
 * Базовая конфигурация WordPress.
 *
 * ВНИМАНИЕ: используйте для редактирования этого файла только правильные редакторы!!! Прочитайте README.HTML !!!
 *
 * Этот файл содержит следующие конфигурации: Настройки MySQL, Префикс таблиц,
 * Секретные ключи, Язык WordPress, и ABSPATH. Вы можете найти больше информации
 * посетив страницу {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} в Кодексе. Вы можете получить настройки MySQL у вашего хостера.
 *
 * Этот файл используется мастером создания wp-config.php во время инсталляции
 * Вам не нужно использовать сайт - просто скопируйте этот файл под именем
 *  "wp-config.php" и заполните значнения.
 *
 * @package WordPress
 */

// ** Настройки MySQL - Вы можете получить их у вашего хостера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'putyourdbnamehere');

/** MySQL имя пользователя */
define('DB_USER', 'usernamehere');

/** MySQL пароль базы данных */
define('DB_PASSWORD', 'yourpasswordhere');

/** MySQL сервер - иногда требуется изменять это значение. например на Мастерхосте */
define('DB_HOST', 'localhost');

/** Кодировка базы данных, используемая при создании таблиц. */
define('DB_CHARSET', 'utf8');

/** Сопоставление базы данных. НЕ ИЗМЕНЯЙТЕ ЭТО ЗНАЧЕНИЕ. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи аутентификации.
 *
 * Измените их на уникальные фразы! Каждая фраза должна быть разной. Желательно с использованием латинских строчных и прописных букв, цифр, спецсимоволов.
 * Или просто сгенерируйте, открыв вот эту ссылку в браузере {@link https://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service} . Затем скопируйте полученные строки ниже, заменив те что были до этого
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'izmenite eto na unikalnuyu frazu');
define('SECURE_AUTH_KEY', 'izmenite eto na unikalnuyu frazu');
define('LOGGED_IN_KEY', 'izmenite eto na unikalnuyu frazu');
define('NONCE_KEY', 'izmenite eto na unikalnuyu frazu');
/**#@-*/

/**
 * Префикс таблиц WordPress в базе данных.
 *
 * Вы можете иметь несколько инсталляций WordPress в одной базе, но поставив в каждой различные префиксы
 * Только цифры, латинские буквы и символ подчеркивания!
 */
$table_prefix  = 'wp_';

/**
 * Язык WordPress. Если не указан никакой, то будет английский!
 *
 * По умолчанию для локализации предлагается такой вариант: define ('WPLANG', 'ru_RU');
 * но вы можете существенно снизить нагрузку на ваш блог, раскомментировав строку:  if (strpos($_SERVER['REQUEST_URI'], 'wp-admin')) define ('WPLANG', 'ru_RU'); else define ('WPLANG', 'ru_RU_lite');
 * и закомментировав строку define ('WPLANG', 'ru_RU');
 * тогда в админке вы будете использовать полный файл перевода, а для лицевой части блога облегченный файл перевода.
 * в среднем это снижает потребление памяти на 3 мегабайта и ускоряется генерация страницы. Также может понадобиться для тех плагинов,
 * которые что-либо выводят на лицевую часть блога создать файлы с переводами по технологии: скопировать файл, например wptuner-ru_RU.mo в wptuner-ru_RU_lite.mo
 * тоже самое нужно сделать и если ваша Тема локализована через внешний файл перевода. Более подробно на http://lecactus.ru/2008/11/15/3110/
 */

define ('WPLANG', 'ru_RU');
// if (strpos($_SERVER['REQUEST_URI'], 'wp-admin')) define ('WPLANG', 'ru_RU'); else define ('WPLANG', 'ru_RU_lite');



// define( 'AUTOSAVE_INTERVAL', 60 ); //Измените здесь интервал автосохранения в секундах (по умолчанию задано значение 60секунд, вы можете переопределить его здесь)
// define('WP_POST_REVISIONS', 5); // управление количеством ревизий. 0-отключает их вообще, любой другое число указывает максимальное количество ревизий каждой записи. вы можете переопределять также эти параметры через плагины из дистрибутива rc-revmngr и Revision Control

/* Ниже представлены необязательные параметры. Если вы не знаете для чего они - НЕ ТРОГАЙТЕ. Это только для очень продвинутых пользователей */
/* Чтобы включить параметр - раскомментируйте нужную строку и отредактируйте.  Подробная информация есть в Кодексе */

// define('USER_COOKIE', 'wordpressuser_' . COOKIEHASH);
// define('PASS_COOKIE', 'wordpresspass_' . COOKIEHASH);
// define('AUTH_COOKIE', 'wordpress_' . COOKIEHASH);
// define('SECURE_AUTH_COOKIE', 'wordpress_sec_' . COOKIEHASH);
// define('LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH);
// define('TEST_COOKIE', 'wordpress_test_cookie');
// define('COOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('home') . '/' ) );
// define('SITECOOKIEPATH', preg_replace('|https?://[^/]+|i', '', get_option('siteurl') . '/' ) );
// define( 'ADMIN_COOKIE_PATH', SITECOOKIEPATH . 'wp-admin' );
// define( 'PLUGINS_COOKIE_PATH', preg_replace('|https?://[^/]+|i', '', WP_PLUGIN_URL)  );
// define('COOKIE_DOMAIN', false);
// define('FORCE_SSL_ADMIN', false);
// define('FORCE_SSL_LOGIN', false);


/* Это все! Ничего особенно сложного нет. Дальше ничего не редактируйте */

/** Абсолютный путь к директории Wordpress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Устанавливает переменные и подключаемые файлы WordPress. */
require_once(ABSPATH . 'wp-settings.php');
?>

<?php

$hyper_labels['wp_cache_not_enabled'] = "Система кэширования wordPress не включена. Пожалуйста, активируйте ее добавлением указанной ниже строки в wp-config.php. Спасибо!";
$hyper_labels['configuration'] = "Конфигурация";
$hyper_labels['activate'] = "Активировать кэш?";
$hyper_labels['timeout'] = "Время жизни кэшированных страниц";
$hyper_labels['timeout_desc'] = "минут (установите в 0, чтобы они оставались постоянно)";
$hyper_labels['count'] = "Всего кэшированных страниц (кэшированные редиректы также считаются)";
$hyper_labels['save'] = "Сохранить";
//$hyper_labels['store'] = "Store pages as";
//$hyper_labels['folder'] = "Cache folder";
$hyper_labels['gzip'] = "Gzip сжатие";
$hyper_labels['gzip_desc'] = "Отправлять сжатые в gzip страницы для поддерживаемых браузеров";
$hyper_labels['clear'] = "Очистить кэш";
$hyper_labels['compress_html'] = "Оптимизировать HTML";
$hyper_labels['compress_html_desc'] = "Попытаться оптимизировать HTML удаляя неиспользуемые пробелы. Не делайте этого, если вы используете теги &lt;pre&gt; в ваших записях";
$hyper_labels['redirects'] = "Кэшировать WP редиректы";
$hyper_labels['redirects_desc'] = "Могут быть проблемы с некоторыми конфигурациями. Попробуйте.";
$hyper_labels['mobile'] = "Детектировать и кэшировать страницы для мобильных устройств";
$hyper_labels['clean_interval'] = "Автоочистка каждые";
$hyper_labels['clean_interval_desc'] = "минут (установите в 0 чтобы отключить)";
$hyper_labels['not_activated'] = "Hyper Cache не установлен корректно: некоторые файлы или директории не созданы. Проверьте что на директорию wp-content установлены правильные права (777) и удалите файл advanced-cache.php в ней. Деактивируйте и переактивируйте плагин повторно.";
$hyper_labels['expire_type'] = "Как очищать кэш";
$hyper_labels['expire_type_desc'] = "<b>нет</b>: кэш не будет удаляться при событиях (комментарии, новые записи, и т.п.)<br />";
$hyper_labels['expire_type_desc'] .= "<b>одиночные страницы</b>: кэшированные страницы, связанные с измененной записью (в редакторе или при добавлении комментария) и главная страница сайта. Новые опубликованные записи делают недействительным весь кэш.<br />";
$hyper_labels['expire_type_desc'] .= "<b>одиночные страницы. ограниченно</b>: как и 'одиночные страницы' но без очистки всего кэша при добавлении новых записей.<br />";
$hyper_labels['expire_type_desc'] .= "<b>все</b>: все кэшированные страницы (блог всегда обновленный)<br />";
$hyper_labels['expire_type_desc'] .= "Помните: когда вы используете 'одиночные страницы. ограниченно', новая запись будет появляться на главной странице, но не на страницах рубрик и меток. Если вы используете виджет/функцию 'последние записи' в сайдбаре, там не будет обновлена информация.";
$hyper_labels['advanced_options'] = "Расширенные настройки";
$hyper_labels['reject'] = "исключить URI";
$hyper_labels['reject_desc'] = "Один на строку. Когда URI (eg. /video/my-new-performance) начинается с одной из этих строк, то он не будет кэширован.";

$hyper_labels['home'] = "Не кэшировать главную страницу";
$hyper_labels['home_desc'] = "Если вы включите эту опцию, главная страница и все остальные страницы (1,2,3 и т.д.) старых записей не будут кэшироваться.";

$hyper_labels['feed'] = "Кэшировать rss-ленты?";
$hyper_labels['feed_desc'] = "Лучше этого не делать, чтобы иметь в RSS всегда свежий контент";
// New from version 2.2.4
$hyper_labels['urls_analysis'] = "URL-ы сос строкой запроса";
$hyper_labels['urls_analysis_desc'] = "URL-ы с параметрами URL-ов, такими как www.satollo.com?param=value.";
$hyper_labels['urls_analysis_desc'] .= "Hyper Cache создает имена кэшированных страниц используя полный URL, с его параметрами.";
$hyper_labels['urls_analysis_desc'] .= "При использовании пермалинков, параметры URL-ов игнорируются WordPress чтобы избежать кэширования URL с некорректными параметрами и не создавать идентичные страницы в кэше, забивающие дисковое пространство.";
$hyper_labels['urls_analysis_desc'] .= "Исключение: параметр 's' используется WordPress для встроенного поиска.";
$hyper_labels['urls_analysis_desc'] .= "Если вы отключили кэшировани URL-ов с парметрами, результаты поиска не будут кэшироваться.";
$hyper_labels['urls_analysis_desc'] .= "Другие плагины могут использовать параметры, кэширование этих URL_ов может повысить производительность";
$hyper_labels['urls_analysis_desc'] .= "I cannot give you a final solution... BUT if you have the permalink disabled the cache will work only with this option enabled.";

$hyper_labels['urls_analysis_default'] = "НЕ кэшировать URL-ы с параметрами";
$hyper_labels['urls_analysis_full'] = "Кэшировать все URL-ы";
// To be implemented
//$hyper_labels['urls_analysis_removeqs'] = "Удалить строку запроса и переадресовать";

$hyper_labels['storage'] = "Хранилище";
$hyper_labels['storage_nogzencode_desc'] = "у вас не установлено расширение zlib, оставьте настройку по умолчанию!";

$hyper_labels['gzip_nogzencode_desc'] = "нет функции 'gzencode', возможно в вашем PHP не подключено расширение zlib.";

// New from version 2.2.5
$hyper_labels['reject_agents'] = "User agents to reject";
$hyper_labels['reject_agents_desc'] = "A 'one per line' list of user agents that, when matched, makes to skip the caching process.";
$hyper_labels['mobile_agents'] = "Mobile user agents";
$hyper_labels['mobile_agents_desc'] = "A 'one per line' list of user agents to identify mobile devices.";
$hyper_labels['_notranslation'] = "Do not use translations for this configuration panel";


$hyper_labels['cron_key'] = "Cron action key";
$hyper_labels['cron_key_desc'] = "A complicate to guess key to start actions from cron calls (no single or double quotes, no spaces)";


?>

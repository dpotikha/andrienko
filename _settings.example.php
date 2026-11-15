<?php
// Template for _settings.php (gitignored; the real file lives only on the server and holds the DB credentials).
// Constants required by the gady.com.ua CMS build (branch cms-upgrade).
define("LOCALE", "ru"); // admin UI language: ru, ua, en, de
$connect_params = array("url" => "mysql://localhost:3306/DB_NAME",
	"params" => array("user" => "DB_USER", "password" => "DB_PASSWORD"));
$SETUP_LANGUAGES       = array('ru');
$SETUP_LANGUAGES3      = array('rus');
$SETUP_LANGUAGES_TITLE = array('Рус');
define("DEFAULT_LANGUAGE", 'ru');
define("DB_CHARSET", "utf8");
define("SITE_CHARSET", "utf-8");
define("ADMIN_CHARSET", "utf-8");
define("MOD_REWRITE", true);
define("USE_LANG", false);
define("USE_VIRTUAL_LANG", false);
define("USE_SAFE_EMAIL", true);
define("USE_SKIP", false);
define("SITE_COMMENTS_PER_PAGE", 20);
define("ADMIN_COMMENTS_PER_PAGE", 50);
define("MAX_COMMENT_SYMBOLS", "10000");
define("MAX_COMMENTS_LEVEL", 3);
define("MANAGERS_CAN_MOVE", true);
define("MANAGERS_CAN_CLONE", false);

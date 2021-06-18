<?php

/* General */
define('_SOLD_STATUS','5');
define('_SALE_STATUS','1');
define('_BOUGHT_OUT_STATUS','6');

define('_SHOW_ERRORS', true);

/* Directories */
define("DIR_ROOT", dirname(dirname(__FILE__)));
define("DIR_APP", DIR_ROOT . '/app');	
define("DIR_CORE", DIR_ROOT . '/core');	
define("DIR_PUBLIC", DIR_ROOT . '/public');
define("DIR_STATIC", DIR_PUBLIC  . '/static');	

define("WWW_STATIC", '/static');	
define("WWW_JS", WWW_STATIC  . '/js');		
define("WWW_CSS", WWW_STATIC  . '/css');	

define("_MODELS", "\\App\\Models\\");
define("_CONTROLLERS", "\\App\\Controllers\\");
define("_VIEWS", "\\App\\Views\\");	

/* Database Config */
define('_DB_HOST','localhost');
define('_DB_NAME','retroseller');
define('_DB_USER','root');
define('_DB_PASSWORD','');
define('_DB_PORT','3306');

/* CLI Mode */
if(php_sapi_name() !== 'cli'){
	define("_URL", ( empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}
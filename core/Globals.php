<?php

use \Core\Services\NotificationService as Notifications;
use \Core\Services\SessionService as Session;
use \Core\Services\UpdateService as Update;
use \Core\Services\PluginService as Plugins;
use \Core\Services\EntityService as Entities;

use Doctrine\ORM\Query\ResultSetMapping;


/**
 * Config
 */
 
if(!defined('STDIN') ) {
	define("_URL_ROOT", (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://") . $_SERVER['HTTP_HOST']); 
}

/* Directories */
define("DIR_ROOT", dirname(dirname(__FILE__)));
define("DIR_APP", DIR_ROOT . '/app');	
define("DIR_CORE", DIR_ROOT . '/core');	
define("DIR_PUBLIC", DIR_ROOT . '/');
define("DIR_STATIC", DIR_PUBLIC  . '/static');	
define("DIR_PROXIES", DIR_CORE  . '/Proxies');
define("DIR_VENDOR", DIR_ROOT . '/vendor');

define("DIR_VIEWS", DIR_APP . '/Views');	
define("DIR_CONTROLLERS", DIR_APP . '/Controllers');	
define("DIR_MODELS", DIR_APP . '/Models');	

define("DIR_PLUGINS", DIR_APP . '/Plugins');	

define("WWW_STATIC", '/static');	
define("WWW_JS", WWW_STATIC  . '/js');		
define("WWW_CSS", WWW_STATIC  . '/css');	
define("WWW_IMG", WWW_STATIC  . '/img');	

/* Name Spaces */
define("_MODELS", "\\App\\Models\\");
define("_CONTROLLERS", "\\App\\Controllers\\");
define("_VIEWS", "\\App\\Views\\");	

define("_CONFIG_FILE",DIR_APP . '/Config.php');

/* Global Image Sizes */
define("_IMAGE_SIZES",[
	"thumbnail" => ["width" => 300, "height" => 300],
	"small" => ["width" => 600, "height" => 600],	
	"medium" => ["width" => 1000, "height" => 1000],	
	"large" => ["width" => 1500, "height" => 1500],
	]
);

/* Purchase Statuses */
define("_PURCHASE_STATUSES", array(
	'FOR_SALE' => array('id' => 1, 'name' => 'For Sale'),
	'SOLD' => array('id' => 2, 'name' => 'Sold'),
	'BOUGHT_OUT' => array('id' => 3, 'name' => 'Bought Out'),
	'WRITTEN_OFF' => array('id' => 4, 'name' => 'Written Off'),
	'REQUIRES_REPAIR' => array('id' => 5, 'name' => 'Requires Repair'),
	'HELD' => array('id' => 6, 'name' => 'Held'),
	)
);

/* Sale Statuses */
define("_SALE_STATUSES", array(
	'PENDING' => array('id' => 1, 'name' => 'Pending-Payment'),
	'PAID' => array('id' => 2, 'name' => 'Paid'),
	'CANCELLED' => array('id' => 3, 'name' => 'Cancelled'),
	'DISPATCHED' => array('id' => 4, 'name' => 'Dispatched')		
	)
);

/* General */
define('_SOLD_STATUS','5');
define('_SALE_STATUS','1');
define('_BOUGHT_OUT_STATUS','6');

if(file_exists(_CONFIG_FILE)){
	define("_IS_SETUP", true);
}else{
	define("_IS_SETUP", false);	
}

/* CLI Mode */
if(php_sapi_name() !== 'cli'){
	define("_URL", ( empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
}

if(file_exists(dirname(dirname(__FILE__)) . "/app/Config.php")){
	require(dirname(dirname(__FILE__)) . "/app/Config.php");
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 86400');   
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
	
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}


/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * First Launch
 */
 

if(!_IS_SETUP){
	
	if($_SERVER['REQUEST_URI'] != "/setup"){
		header('Location: /setup');
		die();
	}
	return false;
}


function getMetadata($key){

	$meta = Entities::findBy("metadata",["key" => $key]);

	if($meta){
		return $meta[0]->getValue();
	}else{
		return null;
	}

}

function setMetadata($key, $value){

	if(Entities::findBy("metadata",["key" => $key])){

		$meta = Entities::findBy("metadata",["key" => $key])[0];
		$meta->setValue($value);
	}else{

		$meta = new \App\Models\Metadata();
		$meta->setValue($value);
		$meta->setKey($key);
	}

	Entities::persist($meta);
	Entities::flush();

}

function dbCheck(){

	try {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$conn = new \mysqli(_DB_HOST, _DB_USER, _DB_PASSWORD);
	} 
	catch (mysqli_sql_exception $e) {
		die('Unable to connect to MySQL Database ' . _DB_USER . '@' . _DB_NAME . ' on ' . _DB_HOST);	
	}

}

Entities::load();
Plugins::load();

if(php_sapi_name() !== 'cli'){
	dbCheck();
}


Session::start();

if(Update::hasNewVersion()){
	Notifications::addNotification("New Update Available: " .  Update::remoteVersion(),"/update","globe-europe");
}

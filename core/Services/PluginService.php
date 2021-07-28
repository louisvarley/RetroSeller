<?php

use App\Config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

namespace Core\Services;

class PluginService{
	
	private static $plugins = [];
	
	public static function load(){
	
		$directories = glob(DIR_PLUGINS . '/*' , GLOB_ONLYDIR);

		foreach($directories as $directory){
			self::$plugins[] = new \Core\Classes\Plugin($directory);
		}
	}
		
	public static function list(){

		return self::$plugins;
	}		
}

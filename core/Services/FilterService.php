<?php

use App\Config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

namespace Core\Services;

class FilterService{
	
	
	private static $filters = [];
	
	
	public static function add($id, $closure){
		
		self::$filters[$id][] = $closure;
		
	}
	
	public static function action($id, $var, $arg = null){
	
		if(array_key_exists($id, self::$filters)){
			
			foreach(self::$filters[$id] as $filter){
				$var = call_user_func($filter, $var, $arg);			
			}
			
		}
		
		return $var;
		
		
	}

		
}

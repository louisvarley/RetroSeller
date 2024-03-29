<?php

namespace App\Plugins\DatabaseManager;

use \Core\Services\FilterService as Filter;
use \Core\Services\EntityService as Entities;

class DatabaseManager
{
	public static $title = "Database Manager";
	public static $description = "For exporting and importing your database across RetroSeller Instances";
	
	
	public static function init(){
				
		Filter::add("nav_menu", function($nav){


			$nav['configuration']['subitems'][] = ['type' => 'divider'];			
			$nav['configuration']['subitems'][] = ['type' => 'item', 'title' => 'Export Database', 'link' => '/database/export'];	
			
			return $nav;
			
		});		
		
		
	}
	
}
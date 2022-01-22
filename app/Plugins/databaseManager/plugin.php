<?php

namespace App\Plugins\databaseManager;

use \Core\Services\FilterService as Filter;
use \Core\Services\EntityService as Entities;

class databaseManager
{
	public static $title = "Database Manager";
	public static $description = "For exporting and importing your database across RetroSeller Instances";
	
	
	public static function init(){
				
		Filter::add("nav_menu", function($nav){


			$nav['configuration']['subitems'][] = ['type' => 'divider'];			
			$nav['configuration']['subitems'][] = ['type' => 'item', 'title' => 'Export Database', 'link' => '/database/export'];
			$nav['configuration']['subitems'][] = ['type' => 'item', 'title' => 'Import Database', 'link' => '/database/import'];		
			
			return $nav;
			
		});		
		
		
	}
	
}
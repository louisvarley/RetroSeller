<?php

namespace App\Plugins\eBayImport;

use \Core\Services\FilterService as Filter;

class eBayImport
{
	public static $title = "eBay Intergration";
	public static $description = "allows for connecting eBay accounts to RetroSeller and auto downloading of Sales";
	
	
	public static function init(){
				
		Filter::add("nav_menu", function($nav){

			$nav['sales']['subitems'][] = ['type' => 'divider'];
			$nav['sales']['subitems'][] = ['type' => 'item', 'title' => 'eBay Auctions', 'link' => '/auction/selling'];
			
			$nav['configuration']['subitems'][] = ['type' => 'divider'];			
			$nav['configuration']['subitems'][] = ['type' => 'item', 'title' => 'eBay Intergrations', 'link' => '/integration/list'];
			$nav['configuration']['subitems'][] = ['type' => 'item', 'title' => 'Import eBay Sales', 'link' => '/import/ebayImports'];		
			
			return $nav;
			
		});		
		
	}
	
}
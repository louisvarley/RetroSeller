<?php

namespace App\Plugins\eBayImport;

use \Core\Services\FilterService as Filter;
use \Core\Services\EntityService as Entities;
use \App\Plugins\eBayImport\Services\EbayService as eBay;

class eBayImport
{
	public static $title = "eBay Intergration";
	public static $description = "allows for connecting eBay accounts to RetroSeller and auto downloading of Sales";
	
	
	public static function init(){
				
		Filter::add("nav_menu", function($nav){

			$nav['sales']['subitems'][] = ['type' => 'divider'];
			$nav['sales']['subitems'][] = ['type' => 'item', 'title' => 'eBay Auctions', 'link' => '/auction/selling'];
			
			$nav['configuration']['subitems'][] = ['type' => 'divider'];			
			$nav['configuration']['subitems'][] = ['type' => 'item', 'title' => 'eBay Integrations', 'link' => '/integration/list'];
			$nav['configuration']['subitems'][] = ['type' => 'item', 'title' => 'Import eBay Sales', 'link' => '/import/ebayImports'];		
			
			return $nav;
			
		});		
		
		
		Filter::add("invoice_customer", function($customer_details, $sale_id){
			
			$sale = Entities::findEntity("sale", $sale_id);
			$id = $sale->geteBayOrderId();
			
			$results = [];
			
			/* Check all Intergrations */
			foreach(Entities::findAll("integration") as $integration){
				$results[] = eBay::withIntegration($integration->getId())::getOrder($id);
			}
			
			if($results){
				$order = end($results)[0];	
			}			
			
			if($order){
	
				$customer_details = array(
					$order->ShippingAddress->Name,
					$order->ShippingAddress->Street1,
					$order->ShippingAddress->Street2,
					$order->ShippingAddress->CityName,
					$order->ShippingAddress->StateOrProvince,
					$order->ShippingAddress->PostalCode,
					$order->ShippingAddress->Country,					
				);

			}
			
			return $customer_details;
		
		});
		
	}
	
}
<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Ebay extends \App\Controllers\Api
{
	
	protected function getSalesGetAction(){

		try{
			
			$result = ["new_sales" => 0, "updated_sales" => 0, "updated_purchases" => 0, "log" => array()];
		
			foreach(findAll("Integration") as $integration){
				
				$result['updated_purchases'] = $result['updated_purchases'] + ebayService($integration->getId())->updatePurchasesWithAuctions();
			}
			
		
			foreach(findAll("Integration") as $integration){
				
				$r = ebayService($integration->getId())->CreateSalesFromOrders();
				$result['new_sales'] = $result['new_sales'] + $r['imports'];
				$result['updated_sales'] = $result['updated_sales'] + $r['updates'];	
				
				$result['log'] = array_merge($result['log'], $r['log']);
				
			}

			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => "Completed", 'result' => $result]);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['message' => $e->getMessage()]);
		}
	}

}

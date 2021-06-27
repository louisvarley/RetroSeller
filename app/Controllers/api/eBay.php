<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class eBayApi extends \App\Controllers\Api\ApiController
{
	
	protected function getSalesGetAction(){

		try{
			$imports = 0;
			
			foreach(findAll("Integration") as $integration){
				
				$imports = $imports + ebayService($integration->getId())->CreateSalesFromOrders();
			}
			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => "Imported $imports new sales"]);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);
		}
	}

}

<?php

namespace App\Plugins\eBayImport\Controllers\Api;


use \Core\View;
use \App\Models\Purchase;
use \Core\Services\entityService as Entities;

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
		
			foreach(Entities::findAll("Integration") as $integration){
				
				$result['updated_purchases'] = $result['updated_purchases'] + $integration->eBay()::updatePurchasesWithAuctions();
			}
			
		
			foreach(Entities::findAll("Integration") as $integration){
				
				$r = $integration->eBay()::CreateSalesFromOrders();
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
	
	protected function refreshTokensGetAction(){

		try{
			
			$log = [];
		
			foreach(Entities::findAll("Integration") as $integration){
				
				$response = $integration->refreshToken();
				
				
				if($response->getStatusCode() !== 200){
					$log[] = ['error' => $response->error, 'error_description' => $response->error_description];
				}else{
					
					$log[] = ['response' => 'Integration ' . $integration->getId() . ' was refreshed successfully'];
				}

			}
			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => "Completed", 'response' => $log]);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['message' => $e->getMessage()]);
		}
	}	

}

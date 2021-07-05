<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Sales extends \App\Controllers\Api
{
	
	protected function saleSetStatusGetAction(){

		try{

			$sale = findEntity("sale",$this->get['saleId']);
			$status = findEntity("saleStatus",$this->get['saleStatusId']);
			
			$sale->setStatus($status);
			
			entityService()->flush();

			return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Status Changed']);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);
		}
	}


}

<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;
use \Core\Services\entityService as Entities;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Sales extends \App\Controllers\Api
{
	
	protected function saleSetStatusGetAction(){

		try{

			$sale = Entities::findEntity("sale",$this->get['saleId']);
			$status = Entities::findEntity("saleStatus",$this->get['saleStatusId']);
			
			$sale->setStatus($status);
			
			Entities::flush();

			return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Status Changed']);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);
		}
	}
	
	protected function saleCalculateFeesGetAction(){
		
		
		try{

			$saleVendor = Entities::findEntity("saleVendor",$this->get['saleVendorId']);
			$paymentVendor = Entities::findEntity("paymentVendor",$this->get['paymentVendorId']);
			$amount = $this->get['amount'];
			
			$feeAmount = round($saleVendor->calculateFee($amount) + $paymentVendor->calculateFee($amount),2);
			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => $feeAmount]);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);
		}
		
	}


}

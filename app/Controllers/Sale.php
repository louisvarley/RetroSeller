<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Sale extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Sales", "description" => "Sales are when 1 or more purchases has been sold"];		

	public function getEntity($id = 0){
		
		return array(
			$this->route_params['controller'] => findEntity($this->route_params['controller'], $id),
			"saleVendors" => createOptionSet('SaleVendor', 'id','name'),
			"paymentVendors" => createOptionSet('PaymentVendor', 'id','name'),		
			"accounts" => createOptionSet('Account', 'id','name'),					
			"purchases" => createOptionSet('purchase', 'id',['id','name','date']),			
		);	
	} 

	public function updateEntity($id, $data){
		
		$sale = findEntity($this->route_params['controller'], $id);
		$saleVendor = findEntity("SaleVendor", $data['sale']['sale_vendor_id']);
		$paymentVendor = findEntity("PaymentVendor", $data['sale']['payment_vendor_id']);
		
		$purchases = findBy("Purchase", ['sale' => $sale]);
		
		$soldStatus = findEntity("PurchaseStatus", _PURCHASE_STATUSES['SOLD']['id']);	
		$forSaleStatus = findEntity("PurchaseStatus", _PURCHASE_STATUSES['FOR_SALE']['id']);			
		
		foreach($purchases as $purchase){
			$purchase->setSale(null);
			$purchase->setStatus($forSaleStatus);
			entityManager()->persist($purchase);
		}

		foreach($data['sale']['purchases'] as $purchase_id){
			$purchase = findEntity("Purchase", $purchase_id);
			$purchase->setSale($sale);
			$purchase->setStatus($soldStatus);
		}
		
		$sale->getAccounts()->clear();	
		foreach($data['sale']['accounts'] as $account_id){
			$sale->getAccounts()->add(findEntity("Account", $account_id));
		}				
		
		$sale->setEbayItemNo($data['sale']['ebay_item_no']);
		$sale->setGrossAmount($data['sale']['gross_amount']);		
		$sale->setPostageCost($data['sale']['postage_cost']);		
		$sale->setFeeCost($data['sale']['fee_cost']);		
		
		$sale->setPaymentVendor($paymentVendor);		
		$sale->setSaleVendor($saleVendor);

		$sale->setDate(date_create_from_format('d/m/Y', $data['sale']['date']));
		
		entityManager()->persist($sale);
		entityManager()->flush();
		
	}
	
	public function insertEntity($data){
		
		$saleVendor = findEntity("SaleVendor", $data['sale']['sale_vendor_id']);
		$paymentVendor = findEntity("PaymentVendor", $data['sale']['payment_vendor_id']);
		
		$sale = new \App\Models\Sale();
		$soldStatus = findEntity("PurchaseStatus", _PURCHASE_STATUSES['SOLD']['id']);	
		$purchases = findBy("Purchase", ['sale' => $sale]);
		
		foreach($purchases as $purchase){
			$purchase->setSale(null);
			entityManager()->persist($purchase);
		}

		foreach($data['sale']['purchases'] as $purchase_id){
			$purchase = findEntity("Purchase", $purchase_id);
			$purchase->setSale($sale);
			$purchase->setStatus($soldStatus);
		}
		
		$sale->getAccounts()->clear();	
		foreach($data['sale']['accounts'] as $account_id){
			$sale->getAccounts()->add(findEntity("Account", $account_id));
		}				
		
		$sale->setEbayItemNo($data['sale']['ebay_item_no']);
		$sale->setGrossAmount($data['sale']['gross_amount']);		
		$sale->setPostageCost($data['sale']['postage_cost']);	

		$saleVendorFee = (($saleVendor->getPercentageFee() / 100) * $data['sale']['gross_amount']) + $saleVendor->getFixedFee();
		$paymentVendorFee = (($paymentVendor->getPercentageFee() / 100) * $data['sale']['gross_amount']) + $paymentVendor->getFixedFee();
		
		$sale->setFeeCost($saleVendorFee + $paymentVendorFee);		
		
		$sale->setPaymentVendor($paymentVendor);		
		$sale->setSaleVendor($saleVendor);

		$sale->setDate(date_create_from_format('d/m/Y', $data['sale']['date']));	
		
		entityManager()->persist($sale);
		entityManager()->flush();
		
		return $sale->getId();
		
	}	
	
}

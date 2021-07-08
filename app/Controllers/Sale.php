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
			"saleStatuses" => createOptionSet('SaleStatus', 'id','name'),
			"paymentVendors" => createOptionSet('PaymentVendor', 'id','name'),		
			"accounts" => createOptionSet('Account', 'id','name'),					
			"purchases" => ($id > 0 ? createOptionSet('purchase', 'id',['id','name','date']) : createOptionSet('purchase', 'id',['id','name','date'], ['status' => ['comparison' => '=', 'match' => \App\Models\PurchaseStatus::ForSale()->getId()]])),			
		);	
	} 

	public function updateEntity($id, $data){
		
		$sale = findEntity($this->route_params['controller'], $id);
		$saleVendor = findEntity("SaleVendor", $data['sale']['sale_vendor_id']);
		$paymentVendor = findEntity("PaymentVendor", $data['sale']['payment_vendor_id']);
		$purchases = findBy("Purchase", ['sale' => $sale]);
		
		$sale->setStatus(findEntity("SaleStatus", $data['sale']['status']));	
		
		foreach($purchases as $purchase){
			$purchase->setSale(null);
			$purchase->setStatus(\app\Models\PurchaseStatus::ForSale());
			entityService()->persist($purchase);
		}

		foreach($data['sale']['purchases'] as $purchase_id){
			$purchase = findEntity("Purchase", $purchase_id);
			$purchase->setSale($sale);
			if($sale->isPaid()){
				$purchase->setStatus(\app\Models\PurchaseStatus::Sold());
			}
			
			if($sale->isCancelled()){
				$purchase->setStatus(\app\Models\PurchaseStatus::ForSale());
			}			
			
		}
		
		$sale->getAccounts()->clear();	
		foreach($data['sale']['accounts'] as $account_id){
			$sale->getAccounts()->add(findEntity("Account", $account_id));
		}				
		
		$sale->setGrossAmount($data['sale']['gross_amount']);		
		$sale->setPostageCost($data['sale']['postage_cost']);
		$sale->setPostageAmount($data['sale']['postage_amount']);	
		$sale->setFeeCost($data['sale']['fee_cost']);		
		$sale->setPaymentVendor($paymentVendor);		
		$sale->setSaleVendor($saleVendor);
		$sale->setDate(date_create_from_format('d/m/Y', $data['sale']['date']));

		
		entityService()->persist($sale);
		
		if(isset($data['note']) &&  $data['note'] != ""){

			$note = new \App\Models\SaleNote();
			$note->setSale($sale);
			$note->setNote($data['note']);
			$note->setDate(new \DateTime('now'));
			$note->setUser();
			entityService()->persist($note);

		}

		entityService()->flush();
		
	}
	
	public function insertEntity($data){
		
		$saleVendor = findEntity("SaleVendor", $data['sale']['sale_vendor_id']);
		$paymentVendor = findEntity("PaymentVendor", $data['sale']['payment_vendor_id']);	
		$sale = new \App\Models\Sale();
		
		$sale->setStatus(findEntity("SaleStatus", $data['sale']['status']));	
		
		$purchases = findBy("Purchase", ['sale' => $sale]);
		
		foreach($purchases as $purchase){
			$purchase->setSale(null);
			entityService()->persist($purchase);
		}

		foreach($data['sale']['purchases'] as $purchase_id){
			$purchase = findEntity("Purchase", $purchase_id);
			$purchase->setSale($sale);
			
			if($sale->isComplete()){
				$purchase->setStatus(\app\Models\PurchaseStatus::Sold());
			}		
			
		}
		
		$sale->getAccounts()->clear();	
		foreach($data['sale']['accounts'] as $account_id){
			$sale->getAccounts()->add(findEntity("Account", $account_id));
		}				
		
		$sale->setGrossAmount($data['sale']['gross_amount']);		
		$sale->setPostageCost($data['sale']['postage_cost']);	
		$sale->setPostageAmount($data['sale']['postage_amount']);
		
		$saleVendorFee = $saleVendor->calculateFee($data['sale']['gross_amount']);
		$paymentVendorFee = $paymentVendor->calculateFee($data['sale']['gross_amount']);
		
		$sale->setFeeCost($saleVendorFee + $paymentVendorFee);		
		
		$sale->setPaymentVendor($paymentVendor);		
		$sale->setSaleVendor($saleVendor);

		$sale->setDate(date_create_from_format('d/m/Y', $data['sale']['date']));	
		
		entityService()->persist($sale);
		entityService()->flush();
		
		return $sale->getId();
		
	}	
	
    /**
     * When the list action is called
     *
     * @return void
     */
	public function listAction(){

		$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : "id";
		$order = isset($_GET['orderby']) ? $_GET['order'] : "desc";		
		
		$this->render($this->route_params['controller'] . '/list.html', 
			array("entities" => findAll($this->route_params['controller'], $orderBy, $order), 'saleStatuses' => findAll("saleStatus"))
			
		);

	}	

	
}

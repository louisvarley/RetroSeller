<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use \Core\Services\EntityService as Entities;
use \Core\Services\EmailService as Emailer;

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
			$this->route_params['controller'] => Entities::findEntity($this->route_params['controller'], $id),
			"saleVendors" => Entities::createOptionSet('SaleVendor', 'id','name'),
			"saleStatuses" => Entities::createOptionSet('SaleStatus', 'id','name'),
			"paymentVendors" => Entities::createOptionSet('PaymentVendor', 'id','name'),		
			"accounts" => Entities::createOptionSet('Account', 'id','name'),					
			"purchases" => ($id > 0 ? Entities::createOptionSet('purchase', 'id',['id','name','date']) : Entities::createOptionSet('purchase', 'id',['id','name','date'], ['status' => ['comparison' => '=', 'match' => \App\Models\PurchaseStatus::ForSale()->getId()]])),			
		);	
	} 

	public function updateEntity($id, $data){
		
		$sale = Entities::findEntity($this->route_params['controller'], $id);
		$saleVendor = Entities::findEntity("SaleVendor", $data['sale']['sale_vendor_id']);
		$paymentVendor = Entities::findEntity("PaymentVendor", $data['sale']['payment_vendor_id']);
		$purchases = Entities::findBy("Purchase", ['sale' => $sale]);
		
		$sale->setStatus(Entities::findEntity("SaleStatus", $data['sale']['status']));	
		
		foreach($purchases as $purchase){
			$purchase->setSale(null);
			$purchase->setStatus(\app\Models\PurchaseStatus::ForSale());
			Entities::persist($purchase);
		}

		foreach($data['sale']['purchases'] as $purchase_id){
			$purchase = Entities::findEntity("Purchase", $purchase_id);
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
			$sale->getAccounts()->add(Entities::findEntity("Account", $account_id));
		}				
		
		$sale->setGrossAmount($data['sale']['gross_amount']);		
		$sale->setPostageCost($data['sale']['postage_cost']);
		$sale->setPostageAmount($data['sale']['postage_amount']);	
		$sale->setFeeCost($data['sale']['fee_cost']);		
		$sale->setPaymentVendor($paymentVendor);		
		$sale->setSaleVendor($saleVendor);
		$sale->setDate(date_create_from_format('d/m/Y', $data['sale']['date']));
		$sale->seteBayOrderId($data['sale']['ebay_order_id']);
		
		Entities::persist($sale);
		
		if(isset($data['note']) &&  $data['note'] != ""){

			$note = new \App\Models\SaleNote();
			$note->setSale($sale);
			$note->setNote($data['note']);
			$note->setDate(new \DateTime('now'));
			$note->setUser();
			Entities::persist($note);

		}
	
		Entities::flush();
		
	}
	
	public function insertEntity($data){
		
		$saleVendor = Entities::findEntity("SaleVendor", $data['sale']['sale_vendor_id']);
		$paymentVendor = Entities::findEntity("PaymentVendor", $data['sale']['payment_vendor_id']);	
		$sale = new \App\Models\Sale();
		
		$sale->setStatus(Entities::findEntity("SaleStatus", $data['sale']['status']));	
		
		$purchases = Entities::findBy("Purchase", ['sale' => $sale]);
		
		foreach($purchases as $purchase){
			$purchase->setSale(null);
			Entities::persist($purchase);
		}

		foreach($data['sale']['purchases'] as $purchase_id){
			$purchase = Entities::findEntity("Purchase", $purchase_id);
			$purchase->setSale($sale);
			
			if($sale->isPaid()){
				$purchase->setStatus(\app\Models\PurchaseStatus::Sold());
			}		
			
		}
		
		$sale->getAccounts()->clear();	
		foreach($data['sale']['accounts'] as $account_id){
			$sale->getAccounts()->add(Entities::findEntity("Account", $account_id));
		}				
		
		$sale->setGrossAmount($data['sale']['gross_amount']);		
		$sale->setPostageCost($data['sale']['postage_cost']);	
		$sale->setPostageAmount($data['sale']['postage_amount']);
		$sale->seteBayOrderId($data['sale']['ebay_order_id']);

		$saleVendorFee = $saleVendor->calculateFee($data['sale']['gross_amount']);
		$paymentVendorFee = $paymentVendor->calculateFee($data['sale']['gross_amount']);
		
		$sale->setFeeCost($saleVendorFee + $paymentVendorFee);		
		
		$sale->setPaymentVendor($paymentVendor);		
		$sale->setSaleVendor($saleVendor);

		$sale->setDate(date_create_from_format('d/m/Y', $data['sale']['date']));	
		
		
		foreach(Entities::findAll("user") as $user){
			Emailer::sendTemplate("new_sale", $user->getEmail(),"New Sale",['link' => _URL_ROOT . '/sale/edit/' . $sale->getId(), 'items' => $sale->getPurchasesString(), 'vendor' => $sale->getSaleVendor()->getName(), 'amount' => $sale->getGrossAmount(), 'profit' => round($sale->getProfitAmount(),2)]);	
		}
				
		
		Entities::persist($sale);
		Entities::flush();
		
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
			array("entities" => Entities::findAll($this->route_params['controller'], $orderBy, $order), 'saleStatuses' => Entities::findAll("saleStatus"))
			
		);

	}	

	
}

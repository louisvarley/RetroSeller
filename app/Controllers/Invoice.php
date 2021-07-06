<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Invoice extends \Core\Controller
{
	
	public $page_data = ["title" => "Invoice", "description" => "Generate an Invoice"];		

	public function indexAction($id = 0){
		
		if($this->isPOST()){
			
			$this->generate();
			
		}else{
			
		
			$this->render('Invoice/index.html', array(
				"accounts" => createOptionSet('Account', 'id','name'),	
			));
		
		}
		
	} 


	public function invPad($id){
		return 'INV' . str_pad($id, 5, "0", STR_PAD_LEFT ); 
	}
	
	public function generate(){
		
		$sale = findEntity("sale", $this->route_params['id']);
		$account = findEntity("account", $this->post['invoice']['account']);		
			

		$invoice = new \Konekt\PdfInvoice\InvoicePrinter("A4","£");

		/* handles using a tmpfile to get blob, cant use base64 here */
		if($account->getLogo()){
			
			$tmpHandle = tmpfile();
			$tmpUri = stream_get_meta_data($tmpHandle)['uri'];
			rename($tmpUri, $tmpUri .= '.png');
			fwrite($tmpHandle, base64_decode($account->getLogo()->getbase64()));
			fseek($tmpHandle, 0);
			$invoice->setLogo($tmpUri); 
		
		}
		
		$invoice->setColor($account->getColor());      // pdf color scheme
		$invoice->setType("Invoice");    // Invoice Type
		$invoice->setReference($this->invPad($sale->getId()));   // Reference
		$invoice->setDate(date_format($sale->getDate(),"M jS Y"));   //Billing Date
		
		if($account->getBusinessAddress()){		
		
			$invoice->setFrom(array($account->getBusinessName(),
			$account->getBusinessAddress()->getLine1(),
			$account->getBusinessAddress()->getLine2(),
			$account->getBusinessAddress()->getcity(),
			$account->getBusinessAddress()->getState(),
			$account->getBusinessAddress()->getPostalcode(),
			\Core\Classes\Countries::get($account->getBusinessAddress()->getCountry())));
		}
		
		
		//$invoice->setTo(array("Purchaser Name","Sample Company Name","128 AA Juanita Ave","Glendora , CA 91740"));

		$total = 0;
		foreach($sale->getPurchases() as $purchase){
			$total = $total + $purchase->getValuation();
			$invoice->addItem($purchase->getName(),$purchase->getDescription(),1,0,$purchase->getValuation(),0,$purchase->getValuation());
		}

		$invoice->addTotal("Discount",$total - $sale->getGrossAmount());
		$invoice->addTotal("Shipping",$sale->getPostageAmount());

		$invoice->addTotal("Total Due", $sale->getPostageAmount() + $sale->getGrossAmount());

		$invoice->addBadge($sale->getStatus()->getName());

		$invoice->addTitle("Important Notice");

		$invoice->addParagraph("Purchases are only dispatched once payment has been made in full.");
		
		$invoice->addParagraph("All purchases have a 14 day warranty from date this invoice was issued. This excludes products described as faulty, incomplete or damaged.");
		
		$invoice->addTitle("Payment Methods");
		
		if($account->getAccountNumber()){
			$invoice->addParagraph("BACS: Account Number: " . $account->getAccountNumber() . ', Sort Code: ' . $account->getAccountSortCode() . ', Reference: ' . $this->invPad($sale->getId()));		
		}
		
		if($account->getPayPalEmailAddress()){
			$invoice->addParagraph("PayPal: " . $account->getPayPalEmailAddress());		
		}
		
		$invoice->setFooternote("Generated By RetroSeller");

		$invoice->render('INV-' . $sale->getId() . '.pdf','I'); 
		
		/* If we used a tmp logo, clean up */
		if($account->getLogo()){
			unlink($tmpUri);
		}
			
	}

	
	
}

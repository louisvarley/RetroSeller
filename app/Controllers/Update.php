<?php

namespace App\Controllers;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Update extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
		/* Everything here should handle any updating needed by checking and adding, processing and removing etc */
		
		foreach(_PURCHASE_STATUSES as $purchaseStatus){
			if(count(findBy("PurchaseStatus",["name" => $purchaseStatus['name']])) == 0){
				$status = new \App\Models\PurchaseStatus();
				$status->setname($purchaseStatus['name']);
				entityManager()->persist($status);
			}
		}
		
		foreach(_SALE_STATUSES as $saleStatus){
			
			if(count(findBy("SaleStatus",["name" => $saleStatus['name']])) == 0){
				$status = new \App\Models\SaleStatus();
				$status->setname($saleStatus['name']);
				entityManager()->persist($status);
			}
		}
		entityManager()->flush();
		toastManager()->throwSuccess("Ready to Rock and Roll...", "You are fully Updated");	
		
		if(authenticationManager()->loggedIn()){
			header('Location: /');
		}else{
			header('Location: /login');
		}
		
		
		
    }
	
	
	
	
}

<?php

namespace App\Controllers;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

	protected $authentication = true;

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
		$forSaleStatus = findEntity("PurchaseStatus", _SALE_STATUS);	

		$sales = findAll("Sale");
		$accounts = findAll("Account");
		$purchases = findBy("Purchase", ["status" => $forSaleStatus ]);
		
		$profit = 0;
		$valuation = 0;
		
		foreach($sales as $sale){			
			$profit = $profit + $sale->getProfitAmount();
		}
		
		foreach($purchases as $purchase){			
			$valuation = $valuation + $purchase->getValuation();
		}		
		
		$dashboard_data = array(
			"accounts" => $accounts,
			"sales" => $sales,			
			"purchases" => $purchases,
			"profit" => $profit,
			"valuation" => $valuation,
			
		);

        View::renderTemplate('Home/index.html', $dashboard_data);
    }
}

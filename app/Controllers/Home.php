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
		$forSaleStatus = findEntity("PurchaseStatus", _PURCHASE_STATUSES['FOR_SALE']['id']);	

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
		
		
		/* Dataset of Sales Last 7 Days */
		$date = date('Y-m-d h:i:s', strtotime("-7 days"));

		$salesSumed = entityManager()->getRepository(_MODELS . 'Sale')
					->createQueryBuilder('e')
					->select('e.date, sum(e.gross_amount) as amount')
					->where('e.date BETWEEN :n7days AND :today ')
					->setParameter('today', date('Y-m-d h:i:s'))
					->setParameter('n7days', $date)
					->groupby('e.date')
					->getQuery()
					->getArrayResult();		
		
		
		$now = new \DateTime( "6 days ago", new \DateTimeZone('America/New_York'));
		$interval = new \DateInterval( 'P1D'); // 1 Day interval
		$period = new \DatePeriod( $now, $interval, 6); // 7 Days
		
		$salesData = array();
		
		foreach( $period as $day) {
			$key = $day->format('D');
			$salesData[$key] = 0;
		}
		
		foreach($salesSumed as $salesSum){
			$salesData[$salesSum['date']->format('D')] = $salesData[$salesSum['date']->format('D')] + $salesSum['amount'];
		}
		

		
		$dashboard_data = array(
			"accounts" => $accounts,
			"sales" => $sales,	
			"salesData" => $salesData,			
			"purchases" => $purchases,
			"profit" => $profit,
			"valuation" => $valuation,
			
		);


		$this->render('Home/index.html', $dashboard_data);	
    }
}

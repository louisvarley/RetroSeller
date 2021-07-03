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
		
		
		$profitThisWeek = 0;
		$profitLastWeek = 0;
		$profitAllTime = 0;
		
		$salesThisWeek = 0;
		$salesLastWeek = 0;
		$salesAllTime = 0;
		
		/* We need some specific dates for reporting */
		
		$weekDayNow = date('N', time()) - 1;
		
		$today = (new \DateTime('now'));		
		$thisMonday = ((new \DateTime('now'))->modify('-' . $weekDayNow . ' days'));
		
		$lastSunday = ((new \DateTime('now'))->modify('-' . ($weekDayNow+1) . ' days'));
		$lastMonday = ((new \DateTime('now'))->modify('-' . ($weekDayNow+7) . ' days'));
		
		date_time_set($lastMonday, 00, 00);
		date_time_set($thisMonday, 00, 00);	
		date_time_set($lastSunday, 23, 59);			

		foreach($sales as $sale){			
			$profitAllTime = $profitAllTime + $sale->getProfitAmount();
			$salesAllTime++;
			
			if($sale->getDate() >= $lastMonday && $sale->getDate() <= $lastSunday){
				$profitLastWeek = $profitLastWeek + $sale->getProfitAmount();
				$salesLastWeek++;
			}
			
			if($sale->getDate() >= $thisMonday && $sale->getDate() <= $today){
				$profitThisWeek = $profitThisWeek + $sale->getProfitAmount();
				$salesThisWeek++;
			}			
			
		}
		
		foreach($purchases as $purchase){			
			$valuation = $valuation + $purchase->getValuation();
		}		
		
		
		/* Dataset of Sales Last 7 Days */
		$date = date('Y-m-d h:i:s', strtotime("-30 days"));

		$salesSumed = entityManager()->getRepository(_MODELS . 'Sale')
					->createQueryBuilder('e')
					->select('e.date, sum(e.gross_amount) as gross, sum(e.fee_cost) as fee, sum(e.postage_cost) as postage')
					->where('e.date BETWEEN :n7days AND :today ')
					->setParameter('today', date('Y-m-d h:i:s'))
					->setParameter('n7days', $date)
					->groupby('e.date')
					->getQuery()
					->getArrayResult();		
		
		
		$now = new \DateTime( "29 days ago");
		$interval = new \DateInterval( 'P1D'); // 1 Day interval
		$period = new \DatePeriod( $now, $interval, 29); // 7 Days
		
		$salesDaily = array();
		
		foreach( $period as $day) {
			$key = $day->format('d');
			$salesDaily[$key]['gross'] = 0;
			$salesDaily[$key]['net'] = 0;			
			
		}
		
		foreach($salesSumed as $salesSum){

			$salesDaily[$salesSum['date']->format('d')]['gross'] = $salesDaily[$salesSum['date']->format('d')]['gross'] + $salesSum['gross'];
			$salesDaily[$salesSum['date']->format('d')]['net'] = $salesDaily[$salesSum['date']->format('d')]['net'] + $salesSum['gross'] - ($salesSum['postage'] + $salesSum['fee']);			
		}
		
		$dashboard_data = array(
			"accounts" => $accounts, // All Accounts
			"sales" => $sales,	// All Sales
			"latestSales" => array_slice($sales, -10),
			"salesDaily" => $salesDaily, // Daily Sales Last 30 Days	
			"purchases" => $purchases, // All Purchases
			"profitAllTime" => $profitAllTime,
			"profitLastWeek" => $profitLastWeek,
			"profitThisWeek" => $profitThisWeek,
			"salesAllTime" => $salesAllTime,
			"salesLastWeek" => $salesLastWeek,
			"salesThisWeek" => $salesThisWeek,			
			"valuation" => $valuation,
		);


		$this->render('Home/index.html', $dashboard_data);	
    }
}

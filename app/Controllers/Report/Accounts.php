<?php

namespace App\Controllers\Report;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Accounts extends \App\Controllers\Report
{
	protected $authentication = true;

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {

		


    }
		

	public function statementAction(){
		
		if(empty($this->post['account_id'])){
			
			
			$this->render('Report/account_statement.html', array("accounts" => createOptionSet('Account', 'id','name')));
			return ;
			
		}

		$account = findEntity("account", $this->post['account_id']);

		header('Content-disposition: attachment; filename="' . $account->getName() . ' Statement' . '.xlsx"');
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		$spreadsheet->getActiveSheet()->setCellValue('A1','Date');
		$spreadsheet->getActiveSheet()->setCellValue('B1','Amount');
		$spreadsheet->getActiveSheet()->setCellValue('C1','Balance');
		$spreadsheet->getActiveSheet()->setCellValue('D1','Description');	
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);			
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);	
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(35);	
		

		
		$balance = 0;
		
		$arr = [];
		
		$x = 1;
		foreach($account->getSales() as $sale){
			
			if($sale->isComplete()){
			
				foreach($sale->getPurchases() as $purchase){
						foreach($purchase->getExpenses() as $expense){
							if($expense->getAccount()->getId() == $this->getId()){
								if($purchase->getBuyOut() == null){
									array_push($arr, [
									'date' => $sale->getDate(),
									'type' => 'Expense Payment',
									'description' => $expense->getName(),
									'amount' => $expense->getAmount() / $expense->getPurchases()->count()
									]);
								}
							}
						}
				}
				
				array_push($arr, [
				'date' => $sale->getDate(),
				'type' => "Sale Profit",
				'description' => $sale->getName(),
				'amount' => $sale->getProfitAmount() / $sale->getAccounts()->count()
				]);
				
			}

		}



		usort($array, function($a, $b){
			
			$t1 = strtotime($a['datetime']);
			$t2 = strtotime($b['datetime']);
			return $t1 - $t2;
		});
			
			foreach($arr as $entry){
				
				$x++;
			
			$spreadsheet->getActiveSheet()->setCellValue('A' . $x, $entry['date']);
			$spreadsheet->getActiveSheet()->setCellValue('B' . $x, $entry['type']);
			$spreadsheet->getActiveSheet()->setCellValue('C' . $x, $entry['description']);				
			$spreadsheet->getActiveSheet()->setCellValue('D' . $x, $entry['amount']);		

		}
	
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
	
}

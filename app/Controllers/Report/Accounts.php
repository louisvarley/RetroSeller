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
		
		$x = 1;
		foreach($account->getSales() as $sale){
			
			if($sale->isComplete()){
				$x++;

				$balance = $balance + $sale->getProfitAmount() / $sale->getAccounts()->count();
				$amount = $sale->getProfitAmount() / $sale->getAccounts()->count();
				
				$spreadsheet->getActiveSheet()->setCellValue('A' . $x, $sale->getDate());
				$spreadsheet->getActiveSheet()->setCellValue('B' . $x, $amount);
				$spreadsheet->getActiveSheet()->setCellValue('C' . $x, $balance);				
				$spreadsheet->getActiveSheet()->setCellValue('D' . $x, "Sale");		

			}
		}
		
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
	
}

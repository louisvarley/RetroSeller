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
		$spreadsheet->getActiveSheet()->setCellValue('B1','Type');
		$spreadsheet->getActiveSheet()->setCellValue('C1','In/Out');		
		$spreadsheet->getActiveSheet()->setCellValue('D1','Description');
		$spreadsheet->getActiveSheet()->setCellValue('E1','Amount');	
		$spreadsheet->getActiveSheet()->setCellValue('F1','Balance');
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);			
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);	
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);	
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);			
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);

		$spreadsheet->getActiveSheet()->getStyle("D")->applyFromArray([

			'numberFormat' => [
				'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00
			]
		]);

		$spreadsheet->getActiveSheet()->getStyle("E")->applyFromArray([

			'numberFormat' => [
				'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00
			]
		]);
		
		$transactions = $account->getTransactions();
			
		$x = 1;		
			
		foreach($transactions as $transaction){
			
			$x++;

			$spreadsheet->getActiveSheet()->setCellValue('A' . $x, $transaction['date']);
			$spreadsheet->getActiveSheet()->setCellValue('B' . $x, $transaction['type']);
			$spreadsheet->getActiveSheet()->setCellValue('C' . $x, $transaction['direction']);			
			$spreadsheet->getActiveSheet()->setCellValue('D' . $x, $transaction['description']);				
			$spreadsheet->getActiveSheet()->setCellValue('E' . $x, $transaction['amount']);		
			$spreadsheet->getActiveSheet()->setCellValue('F' . $x, round($transaction['balance'],2));	
		}
	
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
	
}

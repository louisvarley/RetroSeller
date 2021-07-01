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
		$spreadsheet->getActiveSheet()->setCellValue('C1','Description');
		$spreadsheet->getActiveSheet()->setCellValue('D1','Amount');	
		$spreadsheet->getActiveSheet()->setCellValue('E1','Balance');
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);			
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);	
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(35);	
		

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
			
		foreach($transactions as $traasaction){
			
			$x++;

			$spreadsheet->getActiveSheet()->setCellValue('A' . $x, $traasaction['date']);
			$spreadsheet->getActiveSheet()->setCellValue('B' . $x, $traasaction['type']);
			$spreadsheet->getActiveSheet()->setCellValue('C' . $x, $traasaction['description']);				
			$spreadsheet->getActiveSheet()->setCellValue('D' . $x, $traasaction['amount']);		
			$spreadsheet->getActiveSheet()->setCellValue('E' . $x, $traasaction['balance']);	
		}
	
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
	
}

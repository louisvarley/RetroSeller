<?php

namespace App\Controllers\Report;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Sales extends \App\Controllers\Report
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
		
		header('Content-disposition: attachment; filename="Sales Statement.xlsx"');
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		$spreadsheet->getActiveSheet()->setCellValue('A1','Sale ID');
		$spreadsheet->getActiveSheet()->setCellValue('B1','Date');
		$spreadsheet->getActiveSheet()->setCellValue('C1','Type');
		$spreadsheet->getActiveSheet()->setCellValue('D1','Description');
		$spreadsheet->getActiveSheet()->setCellValue('E1','Amount');	
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);		
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);			
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);	
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);	
		

		$spreadsheet->getActiveSheet()->getStyle("E")->applyFromArray([

			'numberFormat' => [
				'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00
			]
		]);

		$sales = findAll("sale");
		
		$transactions = [];
		
		foreach($sales as $sale){
			
			
			if($sale->isComplete()){
				
				
				/* Add Gross Amount */
				array_push($transactions, [
					'id' => $sale->getId(),				
					'date' => $sale->getDate(),
					'type' => 'SALE_GROSS',
					'description' => $sale->getPurchasesString(),
					'amount' => $sale->getGrossAmount()
				]);
				
				/* Add Postage Cost */
				array_push($transactions, [
					'id' => $sale->getId(),				
					'date' => $sale->getDate(),
					'type' => 'SALE_POSTAGE_COST',
					'description' => "Postage Cost",
					'amount' => 0 - $sale->getPostageCost()
				]);				
				
				/* Add Fee Cost */
				array_push($transactions, [
					'id' => $sale->getId(),
					'date' => $sale->getDate(),
					'type' => 'SALE_FEE_COST',
					'description' => 'Fees from ' . $sale->getPaymentVendor()->getName() . ' and ' . $sale->getSaleVendor()->getName(),
					'amount' => 0 - $sale->getFeeCost()
				]);				

				foreach($sale->getPurchases() as $purchase){

					foreach($purchase->getExpenses() as $expense){
						
						/* Add Expenses */
						array_push($transactions, [
							'id' => $sale->getId(),						
							'date' => $sale->getDate(),
							'type' => 'EXPENSE_PAYOUT',
							'description' => 'Expense for ' . $expense->getName() . ' paid to ' . $expense->getAccount()->getName(),
							'amount' => 0 - $expense->getAmount() / $expense->getPurchases()->count()
						]);
						
					}
				
				}
				
				foreach($sale->getAccounts() as $account){
				
					/* Add Profit From This Sale */
					array_push($transactions, [
					'id' => $sale->getId(),					
					'date' => $sale->getDate(),
					'type' => "PROFIT_PAY_OUT",
					'description' => "Profit Share for " . $account->getName(),
					'amount' => 0 - ($sale->getProfitAmount() / $sale->getAccounts()->count())
					]);
				
				}
				
			}
			
		}
			
		$x = 1;		
			
		foreach($transactions as $transaction){
			
			$x++;

			$spreadsheet->getActiveSheet()->setCellValue('A' . $x, $transaction['id']);
			$spreadsheet->getActiveSheet()->setCellValue('B' . $x, $transaction['date']);
			$spreadsheet->getActiveSheet()->setCellValue('C' . $x, $transaction['type']);
			$spreadsheet->getActiveSheet()->setCellValue('D' . $x, $transaction['description']);				
			$spreadsheet->getActiveSheet()->setCellValue('E' . $x, $transaction['amount']);			
		}
	
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
	
}

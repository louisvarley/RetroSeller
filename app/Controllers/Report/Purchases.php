<?php

namespace App\Controllers\Report;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Purchases extends \App\Controllers\Report
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
		

	public function UnderValuedAction(){
		
		header('Content-disposition: attachment; filename="Sales Statement.xlsx"');
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		$spreadsheet->getActiveSheet()->setCellValue('A1','Purchase ID');
		$spreadsheet->getActiveSheet()->setCellValue('B1','Type');		
		$spreadsheet->getActiveSheet()->setCellValue('C1','Valuation');
		$spreadsheet->getActiveSheet()->setCellValue('D1','Current Spend');
		$spreadsheet->getActiveSheet()->setCellValue('E1','Profit / Loss');
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);		
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);	
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);	
		
		$spreadsheet->getActiveSheet()->getStyle("C")->applyFromArray([

			'numberFormat' => [
				'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00
			]
		]);
		
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

		$purchases = findAll("purchase");
		
		$transactions = [];
		
		foreach($purchases as $purchase){
			
			
			if($purchase->getSale() == null && 
				$purchase->getStatus() == \app\Models\PurchaseStatus::ForSale() &&
				$purchase->getTotalSpend() > 0 &&
				$purchase->getValuation() > 0
				){
				
				/* Lose Money just from Spend */
				if($purchase->getTotalSpend() > $purchase->getValuation()){
				
					/* Add Gross Amount */
					array_push($transactions, [
						'type' => 'LOSS',
						'id' => $purchase->getId(),				
						'valuation' => $purchase->getValuation(),
						'spend' => $purchase->getTotalSpend(),
						'profit' => $purchase->getValuation() - $purchase->getTotalSpend(),
					]);
				
				}
				/* Gain is less than 10%  */				
				elseif(($purchase->getValuation() / ($purchase->getTotalSpend() / 100)) < 10 ){
					
					array_push($transactions, [
						'type' => 'LOW_PROFIT',
						'id' => $purchase->getId(),				
						'valuation' => $purchase->getValuation(),
						'spend' => $purchase->getTotalSpend(),
						'profit' => $purchase->getValuation() - $purchase->getTotalSpend(),
					]);

				}
				
			}
			
		}
			
		$x = 1;		
			
		foreach($transactions as $traasaction){
			
			$x++;

			$spreadsheet->getActiveSheet()->setCellValue('A' . $x, $traasaction['id']);
			$spreadsheet->getActiveSheet()->setCellValue('B' . $x, $traasaction['type']);
			$spreadsheet->getActiveSheet()->setCellValue('C' . $x, $traasaction['valuation']);
			$spreadsheet->getActiveSheet()->setCellValue('D' . $x, $traasaction['spend']);				
			$spreadsheet->getActiveSheet()->setCellValue('E' . $x, $traasaction['profit']);			
		}
	
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
	
}

<?php

namespace App\Controllers\Report;

use \Core\View;
use \Core\Services\EntityService as Entities;

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
		
		header('Content-disposition: attachment; filename="Undervalued Purchases Report.xlsx"');
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		
		$ebaySaleVendor = Entities::findEntity("saleVendor", getMetadata("ebay_sale_vendor_id"));
		$ebayPaymentVendor = Entities::findEntity("paymentVendor", getMetadata("ebay_payment_vendor_id"));		
		
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		$spreadsheet->getActiveSheet()->setCellValue('A1','Type');
		$spreadsheet->getActiveSheet()->setCellValue('B1','Id');
		$spreadsheet->getActiveSheet()->setCellValue('C1','Name');		
		$spreadsheet->getActiveSheet()->setCellValue('D1','Valuation');
		$spreadsheet->getActiveSheet()->setCellValue('E1','Current Spend');
		$spreadsheet->getActiveSheet()->setCellValue('F1','eBay Fees');		
		$spreadsheet->getActiveSheet()->setCellValue('G1','Profit / Loss');
		$spreadsheet->getActiveSheet()->setCellValue('H1','eBay Profit / Loss');		
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);	
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(12);			
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);		
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);	
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12);	
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);	
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(12);			
		
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

		$spreadsheet->getActiveSheet()->getStyle("F")->applyFromArray([

			'numberFormat' => [
				'formatCode' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00
			]
		]);		

		$purchases = Entities::findAll("purchase");
		
		$transactions = [];
		
		foreach($purchases as $purchase){
			
			$ebayFee = $ebaySaleVendor->calculateFee($purchase->getValuation()) + $ebayPaymentVendor->calculateFee($purchase->getValuation());
			
			if($purchase->getSale() == null && 
				$purchase->getStatus() == \app\Models\PurchaseStatus::ForSale() &&
				$purchase->getTotalSpend() > 0 &&
				$purchase->getValuation() > 0
				){
				
				/* Spend is Greater than Valuation */
				if($purchase->getTotalSpend() > $purchase->getValuation()){
				
					/* Add Gross Amount */
					array_push($transactions, [
						'type' => 'LOSS',
						'id' => $purchase->getId(),	
						'name' => $purchase->getname(),						
						'valuation' => $purchase->getValuation(),
						'spend' => $purchase->getTotalSpend(),
						'fees' => $ebayFee,
						'profit' => $purchase->getValuation() - $purchase->getTotalSpend(),
						'profit_ebay' => $purchase->getValuation() - ($ebayFee + $purchase->getTotalSpend()),							
					]);
				
				}
				/* Profit Gained is less than the Undervalued Percentage */				
				elseif((($purchase->getTotalSpend() / 100) * getMetadata("undervalued_percentage"))  > ($purchase->getValuation() - $purchase->getTotalSpend())){
					
					array_push($transactions, [
						'type' => 'LOW_PROFIT',
						'id' => $purchase->getId(),	
						'name' => $purchase->getname(),
						'valuation' => $purchase->getValuation(),
						'spend' => $purchase->getTotalSpend(),
						'fees' => $ebayFee,
						'profit' => $purchase->getValuation() - $purchase->getTotalSpend(),
						'profit_ebay' => $purchase->getValuation() - ($ebayFee + $purchase->getTotalSpend()),							
					]);

				}
				
				/* Total Spend Plus eBay fee is greater than Valuation */
				elseif(($purchase->getTotalSpend() + $ebayFee) > $purchase->getValuation()){
					
					array_push($transactions, [
						'type' => 'EBAY_LOSS',
						'id' => $purchase->getId(),	
						'name' => $purchase->getname(),
						'valuation' => $purchase->getValuation(),
						'spend' => $purchase->getTotalSpend(),
						'fees' => $ebayFee,						
						'profit' => $purchase->getValuation() - $purchase->getTotalSpend(),
						'profit_ebay' => $purchase->getValuation() - ($ebayFee + $purchase->getTotalSpend()),							
					]);

				}
				
				/* Profit gained is less than UnderValued Percentage when spend includes eBay Fees */
				elseif(((($ebayFee + $purchase->getTotalSpend()) / 100) * getMetadata("undervalued_percentage"))  > ($purchase->getValuation() - $purchase->getTotalSpend())){				
					
					array_push($transactions, [
						'type' => 'EBAY_LOW_PROFIT',
						'id' => $purchase->getId(),	
						'name' => $purchase->getname(),
						'valuation' => $purchase->getValuation(),
						'spend' => $purchase->getTotalSpend(),
						'fees' => $ebayFee,						
						'profit' => $purchase->getValuation() - $purchase->getTotalSpend(),
						'profit_ebay' => $purchase->getValuation() - ($ebayFee + $purchase->getTotalSpend()),						
					]);

				}				
				
				
			}
			
		}
			
		$x = 1;		
			
		foreach($transactions as $transaction){
			
			$x++;

			$spreadsheet->getActiveSheet()->setCellValue('A' . $x, $transaction['type']);
			$spreadsheet->getActiveSheet()->setCellValue('B' . $x, $transaction['id']);
			$spreadsheet->getActiveSheet()->setCellValue('C' . $x, $transaction['name']);
			$spreadsheet->getActiveSheet()->setCellValue('D' . $x, $transaction['valuation']);
			$spreadsheet->getActiveSheet()->setCellValue('E' . $x, $transaction['spend']);	
			$spreadsheet->getActiveSheet()->setCellValue('F' . $x, $transaction['fees']);				
			$spreadsheet->getActiveSheet()->setCellValue('G' . $x, $transaction['profit']);	
			$spreadsheet->getActiveSheet()->setCellValue('H' . $x, $transaction['profit_ebay']);				
		}
	
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
	
}

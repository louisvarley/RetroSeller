<?php

namespace App\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Invoice extends \Core\Controller
{
	
	public $page_data = ["title" => "Invoice", "description" => "Generate an Invoice"];		

	public function indexAction($id = 0){
		
		if($this->isPOST()){
			
			$this->generate();
			
		}else{
			
		
			$this->render('Invoice/index.html', array(
				"accounts" => createOptionSet('Account', 'id','name'),	
			));
		
		}
		
	} 

	
	public function generate(){
		
		//header('Content-disposition: attachment; filename=purchase_import_template.xlsx');
		//header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		
		define("POUND",chr(163));
		
		
		
		$sale = findEntity("sale", $this->route_params['id']);
		$account = findEntity("account", $this->post['invoice']['account']);
		
		$pdf = new \FPDF('P','mm','A4');

		$pdf->AddPage();
		/*output the result*/
		
		
		$pdf->SetFont('Arial','B',180);
		$pdf->SetTextColor(242,231,230);
		$pdf->Text(35,190,$sale->getStatus()->getName());	
		$pdf->SetTextColor(0,0,0);

		$pdf->setFillColor(215,215,217);

		/*set font to arial, bold, 14pt*/
		$pdf->SetFont('Arial','B',24);
		
		
		$pdf->SetFont('Arial','B',24);
        $pdf->Cell(60);
		
		$pdf->Cell(0, 10, "Invoice", 0, true, 'R');

        $pdf->Ln(10);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(70);

        $pdf->Ln(10);		
		
		$pdf->Cell(71 ,10,'',0,0);
		$pdf->Cell(59 ,10,'',0,1);

		$pdf->SetFont('Arial','B',24);
		$pdf->Cell(71 ,5,$account->GetBusinessName(),0,0);
		$pdf->Cell(50 ,10,'',0,1);
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(25 ,5,'Sale ID:',0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(34 ,5, $sale->getId() ,0,1);

		$pdf->Cell(50 ,0,'',0,1);

		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(25 ,5,'Invoice Date:',0,0);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(34 ,5, date('d-m-Y'),0,1);
		 
		$pdf->SetFont('Arial','B',15);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(189 ,10,'',0,1);

		$pdf->SetFont('Arial','B',10);
		/*Heading Of the table*/
		$pdf->Cell(90 ,6,'Description',1,0,'C', true);
		$pdf->Cell(23 ,6,'Qty',1,0,'C', true);
		$pdf->Cell(30 ,6,'Unit Price',1,0,'C', true);
		$pdf->Cell(20 ,6,'Tax',1,0,'C', true);
		$pdf->Cell(25 ,6,'Total',1,1,'C', true);/*end of line*/
		/*Heading Of the table end*/
		$pdf->SetFont('Arial','',10);
			$total = 0;
			foreach($sale->getPurchases() as $purchase){
				$total = $total + $purchase->getValuation();
				$pdf->Cell(90 ,6,$purchase->getName(),1,0);
				$pdf->Cell(23 ,6,'1',1,0,'R');
				$pdf->Cell(30 ,6,POUND . sprintf('%.2f',$purchase->getValuation()),1,0,'R');
				$pdf->Cell(20 ,6,POUND . sprintf('%.2f',0),1,0,'R');
				$pdf->Cell(25 ,6,POUND . sprintf('%.2f',$purchase->getValuation()),1,1,'R');
			}
				
				
		$pdf->Cell(118 ,6,'',0,0);

		$pdf->Cell(25 ,6,'Discounts',0,0);
		$pdf->Cell(45 ,6,POUND . sprintf('%.2f',$total - $sale->getGrossAmount()) ,1,1,'R');				
				
				
		$pdf->Cell(118 ,6,'',0,0);
		$pdf->Cell(25 ,6,'Sub Total',0,0);
		$pdf->Cell(45 ,6,POUND . sprintf('%.2f',$sale->getGrossAmount()) ,1,1,'R');

		$pdf->Cell(118 ,6,'',0,0);
		$pdf->Cell(25 ,6,'P&P',0,0);
		$pdf->Cell(45 ,6,POUND . sprintf('%.2f',$sale->getPostageAmount()),1,1,'R');
		
		$pdf->Cell(118 ,6,'',0,0);
		$pdf->Cell(25 ,6,'Total',0,0);
		$pdf->Cell(45 ,6,POUND . sprintf('%.2f',$sale->getGrossAmount() + $sale->getPostageAmount()),1,1,'R');		
		
		$pdf->Cell(0 ,6,'',0,0);
		
		$pdf->Cell(50 ,5,'',0,1);

		$pdf->SetFont('Arial','B',10);

		$pdf->Cell(100 ,6,'Payment Options',0,0,'L');
		$pdf->Cell(50 ,6,'',0,1);		
		$pdf->SetFont('Arial','B',7);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(15 ,6,'BACS',0,0,'L');
		$pdf->SetFont('Arial','',7);
				
		$pdf->Cell(50 ,5,'',0,1);
			
		$pdf->Cell(70 ,6,"Account Name: " . $account->getBusinessName(),0,0,'L');	
		$pdf->Cell(50 ,5,'',0,1);
		$pdf->Cell(70 ,6,"Account Number: " . $account->getAccountNumber(),0,0,'L');	
		$pdf->Cell(50 ,5,'',0,1);	
		$pdf->Cell(70 ,6,"Sortcode: " . $account->getAccountSortCode() ,0,0,'L');	
			
		$pdf->Cell(50 ,5,'',0,1);
		$pdf->SetFont('Arial','B',7);		
		$pdf->Cell(15 ,6,'PayPal',0,0,'L');
		$pdf->SetFont('Arial','',7);		
		$pdf->Cell(50 ,5,'',0,1);
		
		$pdf->Cell(70 ,6,$account->getPayPalEmailAddress(),0,0,'L');	
		$pdf->SetFont('Arial','B',10);			
		
		$pdf->SetY(5);
    // Arial italic 8
    $pdf->SetFont('Arial','I',8);
    // Page number
    $pdf->Cell(0,10,'Prepared by: Your Name');
		
		
		$pdf->Output();	
		
				

		
	}

	
	
}

<?php

namespace App\Controllers;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Import extends \Core\Controller
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
	
	public function eBayOrdersAction(){
		
		$imports = 0;
		
		foreach(findAll("Intergration") as $intergration){
			
			$imports = $imports + ebayService($intergration->getId())->CreateSalesFromOrders();
		}
			
		toastManager()->throwSuccess("Saved...", "Imported " . $imports . " New Sales");
		header('Location: /');

		
	}
	
	public function purchaseAction()
	{
		
		if(isset($_GET['download'])){
			
			$this->purchaseTemplate();
		}
	
		if(isset($_FILES['imported'])){
			if ($_FILES['imported']['error'] == UPLOAD_ERR_OK               //checks for errors
				  && is_uploaded_file($_FILES['imported']['tmp_name'])) { //checks that file is uploaded
				$this->processPurchaseData(file_get_contents($_FILES['imported']['tmp_name']));
			}
		}

		View::renderTemplate('Import/purchase.html');
	}
	
	public function processPurchaseData($data){
		
		
		
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
		$spreadsheet = $reader->load($_FILES['imported']['tmp_name']);
	

		$worksheet = $spreadsheet->getActiveSheet();
		$rows = $worksheet->toArray();
		
		$count = 0;

		foreach($rows as $key => $value) {
			
			if($value[0] == "") break;
			
			if($key > 0){
				$count++;
			
				$purchaseVendor = findBy("PurchaseVendor", ["name" => $value[4]])[0];
				$purchaseStatus = findBy("PurchaseStatus", ["name" => $value[3]])[0];
				$purchaseCategory = findBy("PurchaseCategory", ["path" => $value[2]])[0];
	
				$purchase = new \App\Models\Purchase();
				$purchase->setName($value[0]);
				$purchase->setDescription($value[6]);
				$purchase->setPurchaseVendor($purchaseVendor);
				$purchase->setStatus($purchaseStatus);
				$purchase->setDate(date_create_from_format('d/m/Y', $value[5]));	
				$purchase->setValuation($value[1]);
				$purchase->setCategory($purchaseCategory);	

				entityManager()->persist($purchase);
							
			}
		};
		
	
		entityManager()->flush();
		
		toastManager()->throwSuccess("Saved...", "Imported " . $count . " New Purchases");
		
		

	}
	
	public function purchaseTemplate(){
		
		
		header('Content-disposition: attachment; filename=purchase_import_template.xlsx');
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		
		$spreadsheet->getActiveSheet()->setCellValue('A1','Title');
		$spreadsheet->getActiveSheet()->setCellValue('B1','Valuation');
		$spreadsheet->getActiveSheet()->setCellValue('C1','Category');
		$spreadsheet->getActiveSheet()->setCellValue('D1','Status');	
		$spreadsheet->getActiveSheet()->setCellValue('E1','Purchase Vendor');	
		$spreadsheet->getActiveSheet()->setCellValue('F1','Date');
		$spreadsheet->getActiveSheet()->setCellValue('G1','Description');
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);			
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);	
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);	
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);	
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);			
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(50);		

		$vendors = FindAll("PurchaseVendor");
		$statuses = Findall("PurchaseStatus");
		$categories = FindAll("PurchaseCategory");
		
		
		$x = 0;
		foreach($categories as $category){
			$x++;
			$spreadsheet->getActiveSheet()->setCellValue('X' . $x,$category->getPath());
		}
		
		$y = 0;
		foreach($statuses as $status){
			$y++;
			$spreadsheet->getActiveSheet()->setCellValue('Y' . $y,$status->getName());			
		}			
		
		$z = 0;
		foreach($vendors as $vendor){
			$z++;
			$spreadsheet->getActiveSheet()->setCellValue('Z' . $z,$vendor->getName());			
		}	
				

		$validation = $spreadsheet->getActiveSheet()->getCell('C2')->getDataValidation();
		$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
		$validation->setFormula1('Worksheet!$X$1:$X$' . $x);
		$validation->setAllowBlank(true);		
		$validation->setShowDropDown(true);
		
		$validation = $spreadsheet->getActiveSheet()->getCell('D2')->getDataValidation();
		$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);	
		$validation->setFormula1('Worksheet!$Y$1:$Y$' . $y);		
		$validation->setAllowBlank(true);		
		$validation->setShowDropDown(true);		
		
		$validation = $spreadsheet->getActiveSheet()->getCell('E2')->getDataValidation();
		$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);	
		$validation->setFormula1('Worksheet!ZX$1:$Z$' . $z);
		$validation->setAllowBlank(true);		
		$validation->setShowDropDown(true);			
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
}

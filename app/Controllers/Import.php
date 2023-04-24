<?php

namespace App\Controllers;

use \Core\View;
use \Core\Services\ToastService as Toast;
use \Core\Services\EntityService as Entities;
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
		
	/* Updating or Importing new Sales */
	public function ebayImportsAction(){
		
		
		$result = ["new_sales" => 0, "updated_sales" => 0, "updated_purchases" => 0];
	
		foreach(Entities::findAll("Integration") as $integration){
			
			$result['updated_purchases'] = $result['updated_purchases'] + $integration->eBay()::updatePurchasesWithAuctions();
		}
		
	
		foreach(Entities::findAll("Integration") as $integration){
			
			$r = $integration->eBay()::CreateSalesFromOrders();
			$result['new_sales'] = $result['new_sales'] + $r['imports'];
			$result['updated_sales'] = $result['updated_sales'] + $r['updates'];			
		}
			
		toast::throwSuccess("Saved...", "Imported " . $result['new_sales'] . " New Sales from eBay");
		toast::throwSuccess("Saved...", "Updated " . $result['updated_sales'] . " Sales from eBay");
		toast::throwSuccess("Saved...", "Updated " . $result['updated_purchases'] . " Purchases from eBay");		
		header('Location: /');

		
	}
	
	/* Uploading an Excel of Purchases */
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
			
				$purchaseVendor = Entities::findBy("PurchaseVendor", ["name" => $value[4]])[0];
				$purchaseStatus = Entities::findBy("PurchaseStatus", ["name" => $value[3]])[0];
				$purchaseCategory = Entities::findBy("PurchaseCategory", ["path" => $value[2]])[0];
	
				$purchase = new \App\Models\Purchase();
				$purchase->setName($value[0]);
				$purchase->setDescription($value[6]);
				$purchase->setPurchaseVendor($purchaseVendor);
				$purchase->setStatus($purchaseStatus);
				$purchase->setDate(date_create_from_format('d/m/Y', $value[5]));	
				$purchase->setValuation($value[1]);
				$purchase->setCategory($purchaseCategory);	

				Entities::persist($purchase);
				
				if(!empty($value[7]) && !empty($value[8])){
					
					$account = Entities::findBy("Account", ["name" => $value[7]])[0];
			
					$expense = new \App\Models\Expense();
					$expense->setName("Initial Purchase");
					$expense->setAmount($value[8]);
					$expense->setDate(date_create_from_format('d/m/Y', date("d/m/Y")));		
					$expense->setAccount($account);
					$expense->getPurchases()->add($purchase);
					Entities::persist($expense);
				}
			
			}
		};
		
	
		Entities::flush();
		
		toast::throwSuccess("Saved...", "Imported " . $count . " New Purchases");
		
		

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
		$spreadsheet->getActiveSheet()->setCellValue('H1','Expense Account');
		$spreadsheet->getActiveSheet()->setCellValue('I1','Expense Amount');		
		
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(25);		
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);			
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);	
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);	
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);	
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);			
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(50);		
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);		
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);				

		$vendors = Entities::findAll("PurchaseVendor");
		$statuses = Entities::findall("PurchaseStatus");
		$categories = Entities::findAll("PurchaseCategory");
		$accounts = Entities::findAll("Account");
		
		
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
		
		$w = 0;
		foreach($accounts as $account){
			$w++;
			$spreadsheet->getActiveSheet()->setCellValue('W' . $w,$account->getName());			
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
		$validation->setFormula1('Worksheet!Z$1:$Z$' . $z);
		$validation->setAllowBlank(true);		
		$validation->setShowDropDown(true);			
		
		$validation = $spreadsheet->getActiveSheet()->getCell('H2')->getDataValidation();
		$validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);	
		$validation->setFormula1('Worksheet!W$1:$W$' . $w);
		$validation->setAllowBlank(true);		
		$validation->setShowDropDown(true);		
		
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
		ob_end_clean();
		$writer->save('php://output');
		
		die();
		
	}
}

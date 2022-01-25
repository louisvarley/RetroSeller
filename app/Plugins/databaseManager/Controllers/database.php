<?php

namespace App\Plugins\DatabaseManager\Controllers;

use \Core\View;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use \Core\Services\ToastService as Toast;

use \App\Plugins\eBayImport\Services\EbayService as eBay;
use \Core\Services\EntityService as Entities;


/**
 * Home controller
 *
 * PHP version 7.0
 */
 

class Database extends \App\Controllers\ManagerController
{
	
	public $page_data = ["title" => "Database Manager", "description" => "Manage Database"];	
	
	/* Authenticate an Integration */
	public function exportAction(){
		$date = new \DateTime();
		
		header("Content-type: application/sql");
		header("Cache-Control: no-store, no-cache");
		header('Content-Disposition: attachment; filename="RetroSeller-Backup-' . $date->format("y-m-d h:i:s") . '.sql"');
		
		echo Entities::MySqlDump();
		return;
		
	}
	
	/* Authenticate an Integration */
	public function importAction(){

		if(isset($_FILES['database'])){
			if ($_FILES['database']['error']['backup'] == UPLOAD_ERR_OK){              //checks for errors
			
					$import = Entities::MySqlImport($_FILES['database']['tmp_name']['backup']);
			
					if($import){
						
						Toast::throwError("Saved...", $import);
					}else{
						
						Toast::throwSuccess("Saved...", "Imported...");
					}
					
			}else{
				Toast::throwError("Saved...", "Imported...");
			}
		
		}
		
		$this->render('Database/import.html');
		
	}	
	
}

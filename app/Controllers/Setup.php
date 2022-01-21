<?php

namespace App\Controllers;

use \Core\View;

use \Core\Services\ToastService as Toast;
use \Core\Services\UpdateService as Updater;
use \Core\Services\EntityService as Entities;
use \Core\Services\AuthenticationService as Authentication;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Setup extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction(){
		
		
		
		/* Config File Exists, Update */
		if(_IS_SETUP){
			
			if(Authentication::loggedIn()){
				$this->update();
			}
			
			header('Location: /');
			
		}else{
			
			if($this->isPOST()){			 
				$this->install();
			}
		
			View::renderTemplate('Setup/index.html');
			
		}
				
		
	}
	
	public function install(){
		

		try {
					
			$connection = @fsockopen($this->post['db_host'], $this->post['db_port']);

		} catch (mysqli_sql_exception $e) {
			toast::throwError("Error...", "MySQL Connection failed: Host Server not Found");
			return;			
		}

		if(!is_resource($connection)){
			toast::throwError("Error...", "MySQL Connection failed: Host Server not Found");
			return;
		}
	
		try {
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$conn = new \mysqli($this->post['db_host'], $this->post['db_user'], $this->post['db_password']);
		} catch (\mysqli_sql_exception $e) {
			toast::throwError("Error...", ("MySQL Connection failed, please check your details and try again"));
			return;			
		}

		$query = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='" . $this->post['db_name'] . "'");
		$row = $query->fetch_object();


		if ($row == null) {	
			$conn->close();
			toast::throwError("Error...", ("Database '" . $this->post['db_name'] . "' not found or user has no permission"));
			return;
		}
		
		$conn->close();

		/* Delete Old Config */
		if(file_exists(_CONFIG_FILE))
		unlink(_CONFIG_FILE);

		/* Build New Config */
		$config = "<?php			

/* Database Config */
define('_DB_HOST','" . $this->post['db_host'] . "');
define('_DB_NAME','" . $this->post['db_name'] . "');
define('_DB_USER','" . $this->post['db_user'] . "');
define('_DB_PASSWORD','" . $this->post['db_password'] . "');
define('_DB_PORT','" . $this->post['db_port'] . "');
define('_DB_DUMPER','mysqldump');";
			
		file_put_contents(_CONFIG_FILE, $config);

		require(_CONFIG_FILE);
		
		schemaGenerate();
		
	}
	
	public function update(){
		
		
		/* Everything here should handle any updating needed by checking and adding, processing and removing etc */
		
		foreach(_PURCHASE_STATUSES as $purchaseStatus){
			
			$pStatus = Entities::findEntity("purchaseStatus", $purchaseStatus['id']);
			
			if($pStatus){
				$pStatus->setName($purchaseStatus['name']);			
			}else{
				$pStatus = new \App\Models\PurchaseStatus();
				$pStatus->setName($purchaseStatus['name']);
			}
			
			Entities::persist($pStatus);
		}
		
		foreach(_SALE_STATUSES as $saleStatus){
			
			$sStatus = Entities::findEntity("saleStatus", $saleStatus['id']);
			
			if($sStatus){
				$sStatus->setName($saleStatus['name']);			
			}else{
				$sStatus = new \App\Models\SaleStatus();
				$sStatus->setName($saleStatus['name']);
			}
			
			Entities::persist($sStatus);	

		}
		
		Entities::flush();
		toast::throwSuccess("Ready to Rock and Roll...", 	"Updated to Version " .  Updater::currentVersion());	
		
		header('Location: /');
				
	}
	
}

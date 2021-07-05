<?php

namespace App\Controllers;

use \Core\View;

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
		
		define("_CONFIG_FILE",DIR_APP . '/Config.php');
		
		/* Config File Exists, Update */
		if(file_exists(_CONFIG_FILE)){
			
			if(authenticationService()->loggedIn()){
				$this->update();
			}
			
		}else{
			
			if($this->isPOST()){			 
				$this->install();
			}
		
			View::renderTemplate('Setup/index.html');
			
		}
				
		header('Location: /');
	}
	
	public function install(){
		
		
		$connection = @fsockopen($this->post['db_host'], $this->post['db_port']);

		if(!is_resource($connection)){
			toastService()->throwError("Error...", ("MySQL Connection failed: Host Server not Found"));
			View::renderTemplate('Setup/index.html');
		}
	
	
		try {
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			$conn = new \mysqli($this->post['db_host'], $this->post['db_user'], $this->post['db_password']);
		} catch (mysqli_sql_exception $e) {
			toastService()->throwError("Error...", ("MySQL Connection failed: " . $conn->connect_error));
			View::renderTemplate('Setup/index.html');
			return;			
		}

		$query = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='" . $this->post['db_name'] . "'");
		$row = $query->fetch_object();


		if ($row == null) {	
			$conn->close();
			toastService()->throwError("Error...", ("Database '" . $this->post['db_name'] . "' not found or user has no permission"));
			View::renderTemplate('Setup/index.html');
			return;
		}
		
		$conn->close();

		/* Delete Old Config */
		if(file_exists(_CONFIG_FILE))
		unlink(_CONFIG_FILE);

		/* Build New Config */
		$config = "<?php			

define('_FIRST_LAUNCH','FALSE');

/* Database Config */
define('_DB_HOST','" . $this->post['db_host'] . "');
define('_DB_NAME','" . $this->post['db_name'] . "');
define('_DB_USER','" . $this->post['db_user'] . "');
define('_DB_PASSWORD','" . $this->post['db_password'] . "');
define('_DB_PORT','" . $this->post['db_port'] . "');
define('_DB_DUMPER','mysqldump');";
			
		file_put_contents(_CONFIG_FILE, $config);

		require(_CONFIG_FILE);
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(EntityService());
		$classes = EntityService()->getMetadataFactory()->getAllMetadata();
		$schemaTool->createSchema($classes);					
						

		$proxyFactory = EntityService()->getProxyFactory();
		$metadatas = EntityService()->getMetadataFactory()->getAllMetadata();
		$proxyFactory->generateProxyClasses($metadatas, DIR_PROXIES);
		

		$user = new \App\Models\User();
		$user->setEmail($this->post['user_email']);	
		$user->setPassword($this->post['user_password']);
		EntityService()->persist($user);
		
		
		foreach(_PURCHASE_STATUSES as $purchaseStatus){
			
			$status = new \App\Models\PurchaseStatus();
			$status->setname($purchaseStatus['name']);
			EntityService()->persist($status);
		}
		
		foreach(_SALE_STATUSES as $saleStatus){
			

			$status = new \App\Models\SaleStatus();
			$status->setname($saleStatus['name']);
			EntityService()->persist($status);
			
		}
		
		EntityService()->flush();
		
		toastService()->throwSuccess("Ready to Rock and Roll...", "You are setup and ready to go");
		header('Location: /login');
		
		
	}
	
	public function update(){
		
		
		/* Everything here should handle any updating needed by checking and adding, processing and removing etc */
		
		foreach(_PURCHASE_STATUSES as $purchaseStatus){
			
			$pStatus = findEntity("purchaseStatus", $purchaseStatus['id']);
			
			if($pStatus){
				$pStatus->setName($purchaseStatus['name']);			
			}else{
				$pStatus = new \App\Models\PurchaseStatus();
				$pStatus->setName($purchaseStatus['name']);
			}
			
			EntityService()->persist($pStatus);
		}
		
		foreach(_SALE_STATUSES as $saleStatus){
			
			$sStatus = findEntity("saleStatus", $saleStatus['id']);
			
			if($sStatus){
				$sStatus->setName($saleStatus['name']);			
			}else{
				$sStatus = new \App\Models\SaleStatus();
				$sStatus->setName($saleStatus['name']);
			}
			
			EntityService()->persist($sStatus);	

		}
		
		EntityService()->flush();
		toastService()->throwSuccess("Ready to Rock and Roll...", "You are fully Updated");	
		
		header('Location: /');
				
	}
	
}

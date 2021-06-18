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
    public function indexAction()
    {
		
		define("_CONFIG_FILE",DIR_APP . '/Config.php');
		
		if(authenticationManager()->loggedIn()){
			
			require(DIR_ROOT . 'dump.php');
			$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(entityManager());
			$classes = entityManager()->getMetadataFactory()->getAllMetadata();
			$schemaTool->updateSchema($classes);	


			header('Location: /');
			
		}
		
		if($this->isPOST()){

			/* Delete Old Config */
			if(file_exists(_CONFIG_FILE))
			unlink(_CONFIG_FILE);

			/* Build New Config */
			$config = "
<?php			

/* General */

define('_FIRST_LAUNCH','FALSE');
define('_SOLD_STATUS','5');
define('_SALE_STATUS','1');
define('_BOUGHT_OUT_STATUS','6');




/* Database Config */
define('_DB_HOST','" . $this->post['db_host'] . "');
define('_DB_NAME','" . $this->post['db_name'] . "');
define('_DB_USER','" . $this->post['db_user'] . "');
define('_DB_PASSWORD','" . $this->post['db_password'] . "');
define('_DB_PORT','" . $this->post['db_port'] . "');
define('_DB_DUMPER','C:\wamp64\bin\mysql\mysql8.0.22\bin\mysqldump.exe');		

?>";
			
		file_put_contents(_CONFIG_FILE, $config);

		require(_CONFIG_FILE);
		
		/* Create Schema */
		$conn = new \mysqli(_DB_HOST, _DB_USER, _DB_PASSWORD);
		if ($conn->connect_error) {
		  toastManager()->throwError("Error...", ("MySQL Connection failed: " . $conn->connect_error));
		  View::renderTemplate('Setup/index.html');
		  return;
		}

		// Create database
		$sql = "CREATE DATABASE IF NOT EXISTS " . _DB_NAME;
		if ($conn->query($sql) === TRUE) {
					

			$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(entityManager());
			$classes = entityManager()->getMetadataFactory()->getAllMetadata();
			$schemaTool->createSchema($classes);					
							

			$proxyFactory = entityManager()->getProxyFactory();
			$metadatas = entityManager()->getMetadataFactory()->getAllMetadata();
			$proxyFactory->generateProxyClasses($metadatas, DIR_PROXIES);
			
		} else {
			toastManager()->throwError("Error...", ("Error creating database: " . $conn->error));
			View::renderTemplate('Setup/index.html');
			return;
		}

		$conn->close();
		

		$user = new \App\Models\User();
		$user->setEmail($this->post['user_email']);	
		$user->setPassword($this->post['user_password']);
		entityManager()->persist($user);
		entityManager()->flush();
		
		toastManager()->throwSuccess("Ready to Rock and Roll...", "You are setup and ready to go");
		header('Location: /login');
		
		}

        View::renderTemplate('Setup/index.html');
    }
}

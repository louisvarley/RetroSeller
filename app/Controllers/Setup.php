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
			
			require(DIR_ROOT . '/dump.php');
			$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(entityManager());
			$classes = entityManager()->getMetadataFactory()->getAllMetadata();
			$schemaTool->updateSchema($classes);	


			header('Location: /');
			
		}
		
		if($this->isPOST()){

			sessionManager()->start();

			$conn = new \mysqli($this->post['db_host'], $this->post['db_user'], $this->post['db_password']);
			if ($conn->connect_error) {
				toastManager()->throwError("Error...", ("MySQL Connection failed: " . $conn->connect_error));
				View::renderTemplate('Setup/index.html');
				return;				
			}
	
			$query = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='" . $this->post['db_name'] . "'");
			$row = $query->fetch_object();


			if ($row == null) {	
				$conn->close();
				toastManager()->throwError("Error...", ("Database '" . $this->post['db_name'] . "' not found or user has no permission"));
				View::renderTemplate('Setup/index.html');
				return;
			}
			
			$conn->close();

			/* Delete Old Config */
			if(file_exists(_CONFIG_FILE))
			unlink(_CONFIG_FILE);

			/* Build New Config */
			$config = "
<?php			

define('_FIRST_LAUNCH','FALSE');

/* Database Config */
define('_DB_HOST','" . $this->post['db_host'] . "');
define('_DB_NAME','" . $this->post['db_name'] . "');
define('_DB_USER','" . $this->post['db_user'] . "');
define('_DB_PASSWORD','" . $this->post['db_password'] . "');
define('_DB_PORT','" . $this->post['db_port'] . "');
define('_DB_DUMPER','mysqldump');		

?>";
			
		file_put_contents(_CONFIG_FILE, $config);

		require(_CONFIG_FILE);
		
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(entityManager());
		$classes = entityManager()->getMetadataFactory()->getAllMetadata();
		$schemaTool->createSchema($classes);					
						

		$proxyFactory = entityManager()->getProxyFactory();
		$metadatas = entityManager()->getMetadataFactory()->getAllMetadata();
		$proxyFactory->generateProxyClasses($metadatas, DIR_PROXIES);
		

		$user = new \App\Models\User();
		$user->setEmail($this->post['user_email']);	
		$user->setPassword($this->post['user_password']);
		entityManager()->persist($user);
		
		
		foreach(_PURCHASE_STATUSES as $purchaseStatus){
			
			$status = new \App\Models\PurchaseStatus();
			$status->setname($purchaseStatus['name']);
			entityManager()->persist($status);
		}
		
		
		entityManager()->flush();
		
		
		$sql = '	
				
			CREATE OR REPLACE VIEW rs_purchase_categories_vw AS
			SELECT   id,
					 name,
					 parent_id,
					 path,
					 depth,
					 color
			FROM     (
							SELECT id,
								   name,
								   parent_id,
								   name  AS path,
								   color AS color,
								   0     AS depth
							FROM   rs_purchase_categories
							WHERE  parent_id IS NULL
							UNION ALL
							SELECT    t2.id                                     AS id,
									  t2.name                                   AS name,
									  t1.id                                     AS parent_id,
												concat(t1.name, " > ", t2.name) AS path,
									  t2.color                                  AS color,
									  1                                         AS depth
							FROM      rs_purchase_categories t1
							LEFT JOIN rs_purchase_categories t2
							ON        t2.parent_id = t1.id
							WHERE     t1.parent_id IS NULL
							UNION ALL
							SELECT    t3.id                                                     AS id,
									  t3.name                                                   AS name,
									  t2.id                                                     AS parent_id,
												concat(t1.name, " > ", t2.name, " > ", t3.name) AS path,
									  t3.color                                                  AS color,
									  2                                                         AS depth
							FROM      rs_purchase_categories t2
							LEFT JOIN rs_purchase_categories t3
							ON        t3.parent_id = t2.id
							LEFT JOIN rs_purchase_categories t1
							ON        t2.parent_id = t1.id
							WHERE     t2.parent_id IS NOT NULL
							AND       t3.id IS NOT NULL ) a
			ORDER BY a.path,
					 a.name ASC ';
						
		$connection = entityManager()->getConnection();
		$statement = $connection->prepare($sql);
		$statement->execute();		 		
	
		toastManager()->throwSuccess("Ready to Rock and Roll...", "You are setup and ready to go");
		header('Location: /login');
		
		}

        View::renderTemplate('Setup/index.html');
    }
}

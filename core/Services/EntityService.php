<?php

namespace Core\Services;

use App\Config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Core\Services\PluginService as Plugins;
use Ifsnop\Mysqldump as IMysqldump;

class EntityService{
	
	public static $entityManager;
	public static $dbParams;

	public static function load($devMode = false){


		$evm = new \Doctrine\Common\EventManager();

		/* Doctrine */
		$paths = array(DIR_APP . "/Models");
		
		/* Load all Models from all plugins for schema + Proxy generation */
		foreach(plugins::list() as $plugin){
	
			if(count($plugin->models) > 0){
				$paths[] = $plugin->directory . '/Models';
			}
			
		}
		
		/* The Connection Configuration */
		self::$dbParams = array(
			'driver'   => 'pdo_mysql',
			'user'     => _DB_USER,
			'password' => _DB_PASSWORD,
			'dbname'   => _DB_NAME,
			'host' 	   => _DB_HOST,
			'port'	   => _DB_PORT,
		);
		
		$cacheDir = DIR_PROXIES;
		if (!is_dir($cacheDir)) {
			mkdir($cacheDir);
		}
		
		$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $devMode, $cacheDir, null, false);
		self::$entityManager = \Doctrine\ORM\EntityManager::create(self::$dbParams, $config, $evm);
		
		
		
				
	}
	
	public static function Manager(){
		
		return self::$entityManager;
	}
	
	public static function em(){
		
		return self::$entityManager;
	}
	
	public static function findByNot( $model, array $criteria, array $orderBy = null, $limit = null, $offset = null ){
		
		$model = ucfirst($model);	
		$entity = _MODELS . $model;
		
        $qb = self::Manager()->createQueryBuilder();
        $expr = self::Manager()->getExpressionBuilder();

        $qb->select( 'entity' )
            ->from( $entity, 'entity'  );

        foreach ( $criteria as $field => $value ) {
                $qb->andWhere( $expr->neq( 'entity.' . $field, $value ) );
        }

        if ( $orderBy ) {

            foreach ( $orderBy as $field => $order ) {

                $qb->addOrderBy( 'entity.' . $field, $order );
            }
        }

        if ( $limit )
            $qb->setMaxResults( $limit );

        if ( $offset )
            $qb->setFirstResult( $offset );

        return $qb->getQuery()
            ->getResult();
    }	
	
	/* Find a Single Entity by ID */
	public static function findEntity($model, $id){
		$model = ucfirst($model);	
		return self::Manager()->find(_MODELS . $model, $id);	
	}

	/* Find Multiple Entities By a Matching Criteria */
	public static function findBy($model, $criteria, $orderBy = null, $limit = null, $offset = null){
		$model = ucfirst($model);
		return self::Manager()->getRepository(_MODELS . $model)->findBy($criteria, $orderBy, $limit, $offset);	
	}

	/* Find All Entities */
	public static function findAll($model, $orderBy = null, $order = "ASC" ){
		$model = ucfirst($model);	
		
		if(!empty($orderBy)){
			return self::Manager()->getRepository(_MODELS . $model)->findBy([], [$orderBy => $order]);
		}else{
			return self::Manager()->getRepository(_MODELS . $model)->findAll();	
		}
		
	}	
	
	/* Create a Query from Scratch */
	public static function createQuery($query){	
		return self::Manager()->createQuery($query);
	}

	/* Create Query Builder From Scratch */
	public static function createQueryBuilder($fields = null){	
		return self::Manager()->createQueryBuilder($fields);
	}

	/* Create a Named Query */
	public static function createdNamedQuery($model, $namedQuery){
		return self::Manager()->getRepository(_MODELS . $model)->createNamedQuery($namedQuery)->getResult();	
	}

	/* Create an Optionset */
	public static function createOptionSet($model, $valueField, $textField, $criteria = null){
		
		$qb = self::Manager()->createQueryBuilder($model);
		$qb->from(_MODELS . $model, "u");
		$qb->addSelect("u" . '.' . $valueField . ' AS value');

		if(!is_array($textField)){
			$qb->addSelect("u" . '.' . $textField . ' AS text');
			$qb->orderBy("u" . '.' . $textField, 'ASC');
		}else{
			
			/* handles turning multiple columns into a JSON array for select2 to display */
			$c = "";
			foreach($textField as $field){		
				$c = $c. "'\"" . $field . "\":\"',". "u" . '.' . $field . "," . "'\",',";	
			}

			$c = rtrim($c,",'") . "'";
			$c = "CONCAT('{'," . $c . ",'}') as text";	
		
			$qb->addSelect($c);
			$qb->orderBy("u" . '.' . $textField[0], 'ASC');

		}
		
		if($criteria != null){
		
			foreach($criteria as $key => $value){
				$qb->Where('u.' . $key . ' ' . $value['comparison'] . ' :value');
				$qb->setParameter('value', $value['match']);
				
			}

		}
			
		$query = $qb->getQuery();
		
		$res = $query->getResult();
		
		return $res;
	}	
			
	public static function persist($entity){

		return self::em()->persist($entity);

	}		
	
	public static function flush(){
		
		return self::em()->flush();
	}
	
	public static function remove($entity){
		
		return self::em()->remove($entity);
	}
	
	/* mysqli db connection */
	public static function dbConnect(){
		
		self::load();
		
		if(!self::dbServerExists()) throw new Exception('Unable to Connect to MySQL Server Host');
		
		$mysqli = new \mysqli(self::$dbParams['host'], self::$dbParams['user'], self::$dbParams['password']);
		return $mysqli;

		
	}
	

	/* Is the Proxies Folder Empty? */
	public static function proxiesExist(){
		

		$handle = opendir(DIR_PROXIES);
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				closedir($handle);
				return true;
			}
		}
		closedir($handle);
		return false;

	}
	
	/* Generate Proxies */
	public static function generateProxies(){
		
		self::load();
		
		$proxyFactory = self::em()->getProxyFactory();
		$metadatas = self::em()->getMetadataFactory()->getAllMetadata();
		$proxyFactory->generateProxyClasses($metadatas, DIR_PROXIES);
		
	}
	
	/* Generate Static Data, such as status */
	public static function generateStaticData(){
		
		
		foreach(_PURCHASE_STATUSES as $purchaseStatus){
			
			$pStatus = self::findEntity("purchaseStatus", $purchaseStatus['id']);
			
			if($pStatus){
				$pStatus->setName($purchaseStatus['name']);			
			}else{
				$pStatus = new \App\Models\PurchaseStatus();
				$pStatus->setName($purchaseStatus['name']);
			}
			
			self::persist($pStatus);
			self::flush();
		}
		
		foreach(_SALE_STATUSES as $saleStatus){
			
			$sStatus = self::findEntity("saleStatus", $saleStatus['id']);
			
			if($sStatus){
				$sStatus->setName($saleStatus['name']);			
			}else{
				$sStatus = new \App\Models\SaleStatus();
				$sStatus->setName($saleStatus['name']);
			}
			
			self::persist($sStatus);
			self::flush();			

		}
		
	}

	/* Is DB Server Alive? */
	public static function dbServerExists(){
		
		self::load();
		
		try {
		
			$connection = @fsockopen(self::$dbParams['host'], self::$dbParams['port']);
		}
		catch(\mysqli_sql_exception $e){

			return false;			
		}

		if(!is_resource($connection)){
			return false;
		}
		
		return true;
		
	}

	/* Does the Database Exist? */
	public static function dbExists(){
		
		$mysqli = self::dbConnect();
		
		try{
			
			$mysqli->select_db(self::$dbParams['dbname']);
			return true;
		}
		catch(\mysqli_sql_exception $e){
			
			return false;
		}
		
	}

	/* Create the Database */
	public static function createDatabase(){
		
		if(!self::dbExists()){
			$conn = self::dbConnect();
			$conn->query("CREATE DATABASE " . self::$dbParams['dbname']);
		}
	}

	/* Generates the Database Schema */
	public static function generateSchema(){
		
		self::load();
		
		/* Generates Schema */
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(self::em());
		$classes = self::em()->getMetadataFactory()->getAllMetadata();
		$schemaTool->updateSchema($classes);					
		
		self::flush();			
			
	}
	
	/* Creates a user if no other users exist */
	public static function initialUserCheck(){
		
		if(!self::findAll("user")){
		
			$user = new \App\Models\User();
			$user->setEmail(_ADMIN_USER);	
			$user->setPassword(_ADMIN_PASSWORD);
					
			self::persist($user);
			self::flush();
		
		}
		
	}
	
	public static function MySqlDump(){
		
		self::load();		
		
		$tmpSql = "dump.sql";
		
		try {
			$dump = new IMysqldump\Mysqldump('mysql:host=localhost;dbname=' . self::$dbParams['dbname'], self::$dbParams['user'],self::$dbParams['password']);
			$dump->start($tmpSql);
		} catch (\Exception $e) {
			echo 'mysqldump-php error: ' . $e->getMessage();
		}
		
		$raw = file_get_contents($tmpSql);
		
		return ($raw);
		
	}
	
	public static function MySqlImport($filename){
		
	
		self::load();	
		
		// Connect to MySQL server
		//$link = mysqli_connect($mysql_host, $mysql_username, $mysql_password, $mysql_database); or die('Error connecting to MySQL server: ' . mysqli_connect_error());

		$link = self::dbConnect();

		mysqli_query($link, "DROP DATABASE " . self::$dbParams['dbname'] . ";");
		mysqli_query($link, "CREATE DATABASE " . self::$dbParams['dbname'] . ";");		
		mysqli_query($link, "USE " . self::$dbParams['dbname'] . ";");

		// Temporary variable, used to store current query
		$templine = '';
		
		$contents = (file_get_contents($filename));
		
		file_put_contents($filename,$contents);
		
		$handle = fopen($filename , 'r');
		if ($handle) {
			while (!feof($handle)) { // Loop through each line
				$line = trim(fgets($handle));
				// Skip it if it's a comment
				if (substr($line, 0, 2) == '--' || $line == '') {
					continue;
				}

				// Add this line to the current segment
				$templine .= $line;

				// If it has a semicolon at the end, it's the end of the query
				if (substr(trim($line), -1, 1) == ';') {
					// Perform the query
					try{
					mysqli_query($link, $templine) or print('Error performing query "' . $templine . '":' . mysqli_error($link) . PHP_EOL);
					}
					catch(\mysqli_sql_exception $e){
						return $e->getMessage();
					}
					// Reset temp variable to empty
					$templine = '';
				}
			}
			fclose($handle);
		}
		return;
		
	}
	
}


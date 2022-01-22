<?php

namespace Core\Services;

use App\Config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Core\Services\PluginService as Plugins;

class EntityService{
	
	public static $entityManager;

	public static function load($devMode = false){


		$evm = new \Doctrine\Common\EventManager();

		/* Doctrine */
		$paths = array(DIR_APP . "/Models");
		
		/* Load all Models from all plugins */
		foreach(plugins::list() as $plugin){
			
			if(count($plugin->models) > 0){
				$path[] = $plugin->directory . '/models';
			}
			
		}
		
		/* The Connection Configuration */
		$dbParams = array(
			'driver'   => 'pdo_mysql',
			'user'     => _DB_USER,
			'password' => _DB_PASSWORD,
			'dbname'   => _DB_NAME,
			'host' 	   => _DB_HOST,
		);
		
		$cacheDir = DIR_PROXIES;
		if (!is_dir($cacheDir)) {
			mkdir($cacheDir);
		}
		
		$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $devMode, $cacheDir, null, false);
		self::$entityManager = \Doctrine\ORM\EntityManager::create($dbParams, $config, $evm);
		
		
		
				
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

	public static function generateSchema(){
		
		Entities::load();
	
		$schemaTool = new \Doctrine\ORM\Tools\SchemaTool(Entities::em());
		$classes = Entities::em()->getMetadataFactory()->getAllMetadata();
		$schemaTool->createSchema($classes);					

		$proxyFactory = Entities::em()-getProxyFactory();
		$metadatas = Entities::em()-getMetadataFactory()->getAllMetadata();
		$proxyFactory->generateProxyClasses($metadatas, DIR_PROXIES);
		
		foreach(_PURCHASE_STATUSES as $purchaseStatus){
			
			$status = new \App\Models\PurchaseStatus();
			$status->setname($purchaseStatus['name']);
			Entities::persist($status);
		}
		
		foreach(_SALE_STATUSES as $saleStatus){
		
			$status = new \App\Models\SaleStatus();
			$status->setname($saleStatus['name']);
			Entities::persist($status);
		}
		
		Entities::flush();
			
		
	}
	
	public static function initialUserCheck(){
		
		if(!Entities::findAll("user")){
		
			$user = new \App\Models\User();
			$user->setEmail(_ADMIN_USER);	
			$user->setPassword(_ADMIN_PASSWORD);
			Entities::persist($user);
		
		}
		
	}
}


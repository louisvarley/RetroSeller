<?php

use App\Config;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

namespace Core\Services;

class EntityService{
	
	public $EntityManager;
	
	protected static $instance = null;
	
	/**
	 * 
	 * @return CLASS INSTANCE
	 */ 
    public static function instance() {

        if ( null == static::$instance ) {
            static::$instance = new static();
        }

        return static::$instance;
    }	
	
	public function __construct($devMode = false){


		$evm = new \Doctrine\Common\EventManager();

		/* Doctrine */
		$paths = array(DIR_APP . "/Models");

		/* The Connection Configuration */
		$dbParams = array(
			'driver'   => 'pdo_mysql',
			'user'     => _DB_USER,
			'password' => _DB_PASSWORD,
			'dbname'   => _DB_NAME,
		);
		
		$cacheDir = DIR_PROXIES;
		if (!is_dir($cacheDir)) {
			mkdir($cacheDir);
		}
		
		$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $devMode, $cacheDir, null, false);
		$this->EntityManager = \Doctrine\ORM\EntityManager::create($dbParams, $config, $evm);
		
		
		
				
	}
	
	public function Manager(){
		
		return $this->EntityManager;
	}
	
	public function findByNot( $entity, array $criteria, array $orderBy = null, $limit = null, $offset = null )
    {
        $qb = $this->Manager()->createQueryBuilder();
        $expr = $this->Manager()->getExpressionBuilder();

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
		
}

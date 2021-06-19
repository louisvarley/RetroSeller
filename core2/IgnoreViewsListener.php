<?php

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs as GenerateSchemaEventArgs;
use Doctrine\ORM\EntityManagerInterface;
 
namespace Core;
 
/**
 * IgnoreViewsListener class, purely stops any updates or creations of schemer from including any where table is actually a view
 */


class IgnoreViewsListener
{
    private $ignoredTables = [
        'rs_purchase_categories_view',
    ];
 
    // or depending on context and what you need
 
    private $ignoredEntities = [
        _MODELS . "PurchaseCategoryView",
    ];
 
    /**
     * Remove ignored tables /entities from Schema
     * 
     * @param GenerateSchemaEventArgs $args
     */
    public function postGenerateSchema(\Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs $args)
    {
        $schema = $args->getSchema();
        $em = $args->getEntityManager();
 
        $ignoredTables = $this->ignoredTables;

        $ignoredTables = [];

        foreach ($schema->getTableNames() as $tableName) {
 
			if(strpos($tableName, "_vw") !== false){
				  $schema->dropTable($tableName);
			}
        }
    }
}


 
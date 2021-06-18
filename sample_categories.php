<?php // create_product.php <name>

require __DIR__ . '/Core/Globals.php';


use \Core\EntityManager;
use \App\Models\Purchase;
use \App\Models\Expense;
use \App\Models\PurchaseVendor;
use \App\Models\SaleVendor;
use \App\Models\PaymentVendor;
use \App\Models\Account;

$category = new \App\Models\PurchaseCategory();
$category->setname("Sega Megadrive");
$category->setColor("");
entityManager()->persist($category);

$category = new \App\Models\PurchaseCategory();
$category->setname("Sega Mastersystem");
$category->setColor("");
entityManager()->persist($category);

$category = new \App\Models\PurchaseCategory();
$category->setname("Sega Mastersystem");
$category->setColor("");
entityManager()->persist($category);

entityManager()->flush();


echo "Created Categories";

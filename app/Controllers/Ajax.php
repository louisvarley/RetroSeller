<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Purchase;
use Doctrine\DataTables;
/**
 * Home controller
 *
 * PHP version 7.0
 */
class Ajax extends \Core\Controller
{

	protected $authentication = true;

	protected function before()
	{
		//header('Content-type: application/json');
	}

	protected function indexAction(){


		if(isset($this->get['action'])){

			$this->get['action'] = ucfirst($this->get['action']);

			if($this->isPOST()){
				call_user_func(array(__CLASS__, 'post' . $this->get['action']), $this->get, $this->post);
			}

			if($this->isGET()){
				call_user_func(array(__CLASS__, 'get' . $this->get['action']), $this->get);
			}

			if($this->isPUT()){
				call_user_func(array(__CLASS__, 'put' . $this->get['action']), $this->get, $this->post);
			}		

			if($this->isDELETE()){
				call_user_func(array(__CLASS__, 'delete' . $this->get['action']), $this->get, $this->post);
			}			

		}

	}


	protected function getHelloAction($data){

		echo "hello";
		die();

	}


	protected function postPurchaseImage($data){

		$imageLocation = ($_FILES['image']['tmp_name']);
		$imageData = file_get_contents($imageLocation);
		$imageBase64 = base64_encode($imageData);

		$purchase = findEntity("purchase",$data['purchaseId']);

		$image = new \App\Models\Blob();
		$image->setData($imageBase64);
		entityManager()->persist($image);

		$purchase->getImages()->add($image);

		entityManager()->flush();

	}


	protected function deletePurchaseImage($data){


		$purchase = findEntity("purchase", $data['purchaseId']);
		$image = findEntity("blob", $data['blobId']);

		$purchase->getImages()->removeElement($image);

		entityManager()->remove($image);
		entityManager()->flush();

	}

}

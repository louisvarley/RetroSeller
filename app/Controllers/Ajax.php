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
		header('Content-type: application/json');
	}

	protected function indexAction(){


		if(isset($this->get['action'])){

			$this->get['action'] = ucfirst($this->get['action']);

			$method = strtolower($this->requestMethod()) . $this->get['action'];

			if(!method_exists(__CLASS__, strtolower($this->requestMethod()) . $this->get['action'])){

				$response = new \Core\Classes\AjaxResponse(500, 0, "Unknown Ajax Action " . $method);
				echo $response->asJson();

			}else{

				if($this->isPOST()){
					call_user_func(array(__CLASS__, $method), $this->get, $this->post);
				}

				if($this->isGET()){
					call_user_func(array(__CLASS__, $method), $this->get);
				}

				if($this->isPUT()){
					call_user_func(array(__CLASS__, $method), $this->get, $this->put);
				}		

				if($this->isDELETE()){
					call_user_func(array(__CLASS__, $method), $this->get);
				}	

			}		

		}

	}


	protected function getHello($data){

		$response = new \Core\Classes\AjaxResponse(200, 0, ['message' => 'Hello World']);
		echo $response->asJson();

	}


	protected function postPurchaseImage($data){

		try{

			$imageLocation = ($_FILES['image']['tmp_name']);
			$imageData = file_get_contents($imageLocation);
			$imageBase64 = base64_encode($imageData);

			$purchase = findEntity("purchase",$data['purchaseId']);

			$image = new \App\Models\Blob();
			$image->setData($imageBase64);
			entityManager()->persist($image);

			$purchase->getImages()->add($image);

			entityManager()->flush();

			$response = new \Core\Classes\AjaxResponse(200, 0, ['blobId' => $image->getId(), 'message' => 'Image Saved']);
			echo $response->asJson();
	
		}
		catch (Exception $e) {
			$response = new \Core\Classes\AjaxResponse(500, 0, ['error' => $e->getMessage()]);
			echo $response->asJson();

		}
	}


	protected function deletePurchaseImage($data){

		try{

			$purchase = findEntity("purchase", $data['purchaseId']);
			$image = findEntity("blob", $data['blobId']);

			$purchase->getImages()->removeElement($image);

			entityManager()->remove($image);
			entityManager()->flush();

			
			$response = new \Core\Classes\AjaxResponse(200, 0, ['message' => 'Image deleted']);
			echo $response->asJson();

		}
		catch (Exception $e) {
			$response = new \Core\Classes\AjaxResponse(500, 0, ['error' => $e->getMessage()]);
			echo $response->asJson();

		}
	}

}

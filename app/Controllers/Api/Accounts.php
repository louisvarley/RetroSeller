<?php

namespace App\Controllers\Api;

use \Core\View;
use \App\Models\Purchase;
use \Core\Services\EntityService as Entities;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Accounts extends \App\Controllers\Api
{
	
	protected function accountLogoPostAction(){

		try{


			$imageLocation = ($_FILES['images']['tmp_name'][0]);
			$imageData = file_get_contents($imageLocation);
			$imageBase64 = base64_encode($imageData);

			$account = Entities::findEntity("account",$this->get['accountId']);

			$image = new \App\Models\Blob();
			$image->setData($imageBase64);
			Entities::persist($image);

			$account->setLogo($image);

			Entities::flush();

			return new \Core\Classes\ApiResponse(200, 0, ['blobId' => $image->getId(), 'message' => 'Image Saved']);
	
		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);
		}
	}


	protected function accountLogoDeleteAction(){

		try{

			$account = Entities::findEntity("account", $this->get['accountId']);
			$image = Entities::findEntity("blob", $this->get['blobId']);

			Entities::remove($account->getLogo());
			$account->setLogo(null);
			
			Entities::flush();
			
			return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Image deleted']);

		}
		catch (Exception $e) {
			return new \Core\Classes\ApiResponse(500, 0, ['error' => $e->getMessage()]);

		}
	}


	protected function accountLogoRotateGetAction(){
		

		
		$blobId = $this->get['blobId'];
		$image = Entities::findEntity("blob", $blobId);
		$image->rotate();
	
		return new \Core\Classes\ApiResponse(200, 0, ['message' => 'Image Saved']);
		
	}


}

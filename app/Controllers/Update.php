<?php

namespace App\Controllers;

use \Core\View;
use \Core\Services\ToastService as Toast;
use \Core\Services\UpdateService as Updater;
use \Core\Services\EntityService as Entities;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Update extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
		
		View::renderTemplate('Update/update.html', ['version' => ['updatable' => Updater::hasNewVersion(), 'current' => Updater::currentVersion(), 'remote' => Updater::remoteVersion()]]);
		
    }
	
	public function installAction(){
		
		$current = Updater::currentVersion();
				
		Entities::generateStaticData();	
		
		$new = Updater::remoteVersion();

		if($current != $new){

			$ln = Updater::update();
			
			$current = Updater::currentVersion();

			if($current == $new){
				
				toast::throwSuccess("Updated Successfully", "Updated to Version $current");
				header("location:" . "/");		
			
			}else{
			
				toast::throwError("Update Failed", "Run .update.sh to manually update");
				
			}
					
		}
		
		header("location:" . "/");	

	}
	
}

<?php

namespace App\Controllers;

use \Core\View;
use \Core\Services\ToastService as Toast;
use \Core\Services\UpdateService as Update

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
		
		View::renderTemplate('Update/update.html', ['version' => ['updatable' => Update::hasNewVersion(), 'current' => Update::currentVersion(), 'remote' => Update::remoteVersion()]]);
		
    }
	
	public function installAction(){
		
		$current = Update::currentVersion();
		
		shell_exec('cd ' . DIR_ROOT);
		shell_exec('./.update.sh');
		
		$new = Update::currentVersion();
		
		if($current != $new){
			header("location:" . "/setup");		
		}else{
			toast::throwError("Update Failed", "Update failed to pull correctly, run .update.sh to manually update");
			header("location:" . "/");			
		}


		
	}
	
}

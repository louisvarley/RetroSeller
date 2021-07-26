<?php

namespace App\Controllers;

use \Core\View;

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
		
		View::renderTemplate('Update/update.html', ['version' => ['current' => updateService()->currentVersion(), 'remote' => updateService()->remoteVersion()]]);
		
    }
	
	public function installAction(){
		
		$current = updateService()->currentVersion();
		
		shell_exec('cd ' . DIR_ROOT);
		shell_exec('/.update.sh');
		
		$new = updateService()->currentVersion();
		
		if($current != $new){
			header("location:" . "/setup");		
		}else{
			toastService()->throwError("Update Failed", "Update failed to pull correctly, run .update.sh to manually update");
			header("location:" . "/");			
		}


		
	}
	
}

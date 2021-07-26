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
		
		shell_exec('cd ' . DIR_ROOT);
		shell_exec('git checkout ' . DIR_ROOT);
		shell_exec('git fetch');
		shell_exec('git pull');
		shell_exec('chmod +x ' . DIR_ROOT . '/.update.sh');
		shell_exec('chmod +x ' . DIR_ROOT . '/.composer.sh');

		header("location:" . "/setup");
		
	}
	
}

<?php

namespace Core\Classes;

/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class Plugin
{

	public $name;
	public $fullName;
	public $title;
	
	public $directory;
	
	public $models = [];
	public $controllers = [];
	

	public function __construct($directory){
		
		if(file_exists($directory . '/' . 'plugin.php')){
			require($directory . '/' . 'plugin.php');
		}
	

		$this->directory = $directory;
		$this->name = basename($directory);

		$this->fullName = "\App\Plugins\\" . $this->name . "\\" . $this->name;

		$this->title = $this->fullName::$title;
		
		/* Call Init Method if defined */
		if(method_exists($this->fullName, "init")){
			$this->fullName::init();
		}

		if(file_exists($directory . '/Models')){
			$this->findModels();
		}

		if(file_exists($directory . '/Controllers')){
			$this->findControllers();
		}

		
	}
	
	public function setDirectory($directory){	
		$this->directory = $directory;
	}
	
	public function getDirectory(){
		return $this->directory;
	}
	
	public function setName($name){
		$this->name = $name;
	}
	
	public function getName($name){
		return $this->name();
	}
	
	public function findModels(){
		
		$models = glob($this->directory . '/Models/*.{php}' , GLOB_BRACE);
		
		foreach($models as $model){
			require($model); // Import Model
			$this->models[] = new \Core\Classes\PluginModel($model, $this->directory);
		}
		
	}
	
	public function findControllers(){
		

		$dir = new \RecursiveDirectoryIterator($this->directory . '/Controllers');
		$ite = new \RecursiveIteratorIterator($dir);
		$files = new \RegexIterator($ite, '/.*\.php/', \RegexIterator::GET_MATCH);
		$controllers = array();
		foreach($files as $file) {
			$controllers = array_merge($controllers, $file);
		}

		foreach($controllers as $controller){

			require($controller); // Import Controller
			$this->controllers[] = new \Core\Classes\PluginController($controller, $this->directory);
		}
		
	}
	
}
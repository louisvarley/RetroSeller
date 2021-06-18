<?php

use App\Config;

namespace Core;

class JobManager{
	
	protected static $instance = null;
	
	public $actions;
	/**
	 * 
	 * @return CLASS INSTANCE
	 */ 
    public static function instance() {

        if ( null == static::$instance ) {
            static::$instance = new static();
        }

        return static::$instance;
    }		
	
	public function __construct(){
		
		$this->actions = [];
		
	}
	
	public function add($action){		
		array_push($this->actions, $action);
	}
	
	public function execute(){
		

		foreach($this->actions as $action){
			$action();
		}
		
	}

}
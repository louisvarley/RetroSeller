<?php

namespace Core\Services;

class UpdateService{
	
	protected static $instance = null;
		
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
	
	public function currentVersion(){
		
		$version = file_get_contents(DIR_ROOT . '/build');
		$version = substr($version,0,40);
		
		return $version;
	}
	
	public function remoteVersion(){

		$opts = [
				'http' => [
						'method' => 'GET',
						'header' => [
								'User-Agent: RetroSeller'
						]
				]
		];

		$context = stream_context_create($opts);
		
		$build = file_get_contents("https://raw.githubusercontent.com/louisvarley/RetroSeller/main/build?flush_cache=True", false, $context);

		return $build;
						
	}
	
	public function hasNewVersion(){
		
		
		if($this->currentVersion() != $this->remoteVersion()){
			
			return true;
		}
		
	}
}
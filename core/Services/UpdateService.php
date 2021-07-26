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
		
		$version = file_get_contents(DIR_ROOT . '/.git/FETCH_HEAD');
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
		
		$commits = json_decode(file_get_contents("https://api.github.com/repos/louisvarley/RetroSeller/commits", false, $context));

		$current_commit = $commits[1]->sha;
		
		return $current_commit;
				
		
	}
	
	public function hasNewVersion(){
		
		
		if($this->currentVersion() != $this->remoteVersion()){
			
			return true;
		}
		
	}
}
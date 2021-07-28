<?php

namespace Core\Services;

class UpdateService{
	

	public static function currentVersion(){
		
		$version = file_get_contents(DIR_ROOT . '/build');
		$version = substr($version,0,40);
		
		return $version;
	}
	
	public static function remoteVersion(){

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
	
	public static function hasNewVersion(){
		
		
		if(self::currentVersion() != self::remoteVersion()){
			
			return true;
		}
		
	}
}
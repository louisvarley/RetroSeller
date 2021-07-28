<?php

namespace Core\Classes;

/**
 * Error and exception handler
 *
 * PHP version 7.0
 */
class PluginController
{

	public $alias;
	public $name;
	public $fullPath;
	
	public function __construct($fullPath, $pluginRoot){
		
		$this->fullPath = $fullPath;

		$relativePath = str_replace(DIR_ROOT, '', $this->fullPath);

		$this->alias = str_replace($pluginRoot, '', $this->fullPath);
		$this->alias = str_replace('/controllers', '/Controllers', $this->alias);
		$this->alias = str_replace('/', '\\', $this->alias);			
		$this->alias = '\App' . $this->alias;
		$this->alias = str_replace('.php', '', $this->alias);	

		$this->name = str_replace(DIR_ROOT, "", $this->fullPath);
		$this->name = str_replace('.php', '', $this->name);		
		$this->name = str_replace('app/', 'App/', $this->name);
		$this->name = str_replace('/controllers', '/Controllers', $this->name);		
		$this->name = str_replace('/', '\\', $this->name);		

		/* Alias this Controller into app */
		class_alias($this->name, $this->alias);

	}

}
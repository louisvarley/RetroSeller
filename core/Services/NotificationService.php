<?php

use App\Config;


namespace Core\Services;

class NotificationService
{

	protected static $instance = null;
	
	public $notifications  = array();
	
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

	public function fetch(){
		

		$sales = findBy("sale", ["status" => findEntity('saleStatus', _SALE_STATUSES['PAID']['id'])]);
		
		foreach($sales as $sale){
			$this->notifications[] = ['title' => 'Order ' . $sale->getId() . ' ready for dispatch', 'link' => 'https://www.google.com', 'icon' => 'truck'];
		}
		
		return $this->notifications;
		
	}
	
	public function addNotification($title, $link, $icon){
		
		
		$this->notifications[] = ['title' => $title, 'link' => $link, 'icon' => $icon];
	}
}


<?php

use App\Config;


namespace Core\Services;

class NotificationService
{

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

	public function fetch(){
		
		$notifications = array();
		
		$sales = findBy("sale", ["status" => findEntity('saleStatus', _SALE_STATUSES['PAID']['id'])]);
		
		foreach($sales as $sale){
			$notifications[] = ['title' => 'Order ' . $sale->getId() . ' ready for dispatch', 'link' => 'https://www.google.com', 'icon' => 'truck'];
		}
		
		return $notifications;
		
	}
}


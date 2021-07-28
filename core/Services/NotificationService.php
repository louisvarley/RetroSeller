<?php

namespace Core\Services;

use App\Config;
use \Core\Services\EntityService as Entities;

class NotificationService
{

	public static $notifications = [];

	public static function fetch(){
		

		$sales = Entities::findBy("sale", ["status" => Entities::findEntity('saleStatus', _SALE_STATUSES['PAID']['id'])]);
		
		foreach($sales as $sale){
			self::$notifications[] = ['title' => 'Order ' . $sale->getId() . ' ready for dispatch', 'link' => 'https://www.google.com', 'icon' => 'truck'];
		}
		
		return self::$notifications;
		
	}
	
	public static function addNotification($title, $link, $icon){
		
		self::$notifications[] = ['title' => $title, 'link' => $link, 'icon' => $icon];
	}
}


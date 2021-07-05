<?php

namespace Core;

class TwigFilters {

	public static function Apply($twig){

		$twig->addFilter( new \Twig\TwigFilter('rag', function ($ogFloat) {
				
				$num = floatval($ogFloat);
				
				if($num < 0){
					return "rag-red";
				}elseif($num ==0){
					return "rag-orange";
				}else{
					return "rag-green";
				}
				
		}));	

		$twig->addFilter( new \Twig\TwigFilter('as_thumbnail', function ($image) {
			return '<div class="image-thumbnail"><img src="' . $image . '"></div>';
		}));

		$twig->addFilter( new \Twig\TwigFilter('item_links', function ($customSKUs) {
				
			$str = "";

			foreach(explode(',',$customSKUs) as $customSKU){				
				if(findEntity("purchase", $customSKU)){
					$purchase = findEntity("purchase",$customSKU);
					$str .= '<a href="/purchase/edit/' . $purchase->getId() . '">' . $purchase->getName() . '</a>';
				}
			}

			return $str;
			
		}));


		$twig->addFilter( new \Twig\TwigFilter('format_duration', function ($dts) {
			
			$dt = new \DateTime();
			$dt->add(new \DateInterval($dts));
			$interval = $dt->diff(new \DateTime());

			return  $interval->format('%d Day %H Hour %I Minutes');
		}));
		
		return $twig;
			
	}
		
}
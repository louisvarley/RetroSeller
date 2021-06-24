<?php

use App\Config;

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;


namespace Core\Services;

class ebayService{
	
	public $ebay;
	
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

	public function config(){

		return [

			'credentials' => [
				'devId' => getMetadata("ebayDevId"),
				'appId' => getMetadata("ebayAppId"),
				'certId' => getMetadata("ebayCertId"),
			],
			'authToken' => getMetadata("ebayAuthToken"),
			'oauthUserToken' => getMetadata("ebayUserToken"),
			'ruName' => getMetadata("ebayRuName")
		];

	}


	public function service(){

		return new \DTS\eBaySDK\Trading\Services\TradingService([
			'credentials' => $this->config()['credentials'],
			'siteId'      => \DTS\eBaySDK\Constants\SiteIds::GB
		]);
	}


	public function getMyActiveAuctions($pageNum = 1){

		$request = new \DTS\eBaySDK\Trading\Types\GetMyeBaySellingRequestType();
		$request->RequesterCredentials = new \DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType();

		$request->RequesterCredentials->eBayAuthToken = $this->config()['oauthUserToken']; //$config['production']['oauthUserToken'];
			
		$request->ActiveList = new \DTS\eBaySDK\Trading\Types\ItemListCustomizationType();
		$request->ActiveList->Include = true;
		$request->ActiveList->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
		$request->ActiveList->Pagination->EntriesPerPage = 10;
		$request->ActiveList->Sort = \DTS\eBaySDK\Trading\Enums\ItemSortTypeCodeType::C_CURRENT_PRICE_DESCENDING;

		$request->ActiveList->Pagination->PageNumber = $pageNum;

		/**
		 * Send the request.
		 */
		$response = $this->service()->getMyeBaySelling($request);

		if (isset($response->Errors)) {
			foreach ($response->Errors as $error) {
				printf(
					"%s: %s\n%s\n\n",
					$error->SeverityCode === \DTS\eBaySDK\Trading\Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
					$error->ShortMessage,
					$error->LongMessage
				);
			}
		}

		if ($response->Ack !== 'Failure' && isset($response->ActiveList)) {
			return $response->ActiveList;
		}

	}

}


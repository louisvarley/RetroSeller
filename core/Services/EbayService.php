<?php

use App\Config;

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;

namespace Core\Services;

class EbayService
{

	/* Holds all Instances */
    protected static $instance;

	public $integrationId = 0;

    /**
     *
     * @return CLASS INSTANCE
     */
    public static function instance($integrationId){

        if (!isset(static::$instance)) {
            static::$instance = new static($integrationId);
        }

        return static::$instance;
    }

    /**
     *
     * @return VOID
     */
	public function __construct($integrationId){
		
		$this->integrationId = $integrationId;
	}

    /**
     *
     * @return Integration Entity
     */
	public function integration(){
		
		return findEntity("integration", $this->integrationId);
		
	}

    /**
     *
     * @return Array
     */
    public function config(){

		return [

			'credentials' => [
				'devId' => $this->integration()->getDevId(),
				'appId' => $this->integration()->getAppId(),
				'certId' => $this->integration()->getCertId(),
			],
			'ruName' => $this->integration()->getRuName(),
		];
		
    }

    /**
     *
     * @return FulfillmentService Instance
     */
	public function fulfillmentService(){

		 return new \DTS\eBaySDK\Fulfillment\Services\FulfillmentService([
			'authorization' => $this->integration()->getAccessToken(),
            'siteId' => \DTS\eBaySDK\Constants\SiteIds::GB				
        ]);		
	}
	
    /**
     *
     * @return AnalyticsService Instance
     */
	public function analyticsService(){

        return new \DTS\eBaySDK\Analytics\Services\AnalyticsService([
			'authorization' => $this->integration()->getAccessToken()
        ]);		
	}

    /**
     *
     * @return IdentityService Instance
     */
	public function accountService(){

        return new \DTS\eBaySDK\Account\Services\AccountService([
			'authorization' => $this->integration()->getAccessToken()
        ]);		
	}	
	
    /**
     *
     * @return Trading Service Instance
     */
    public function tradingService(){

		return new \DTS\eBaySDK\Trading\Services\TradingService([
            'credentials' => $this->config()['credentials'],		
			'authorization' => $this->integration()->getAccessToken(),
            'siteId' => \DTS\eBaySDK\Constants\SiteIds::GB			
        ]);	
    }

    /**
     *
     * @return oAuth Service Instance
     */	
    public function oAuthService(){
		 return new \DTS\eBaySDK\OAuth\Services\OAuthService([
			'credentials' => $this->config()['credentials'],
			'ruName'      => $this->config()['ruName'],
			'sandbox'     => false
		]);		
    }
	
    /**
     * Splits a string of SKUs into an Array of SKUs
     * @return array
     */	
	public function SplitSKU($skus){
		
		$return = '';
		
		foreach(explode(",",$skus) as $sku){
			$return .= ',' . explode("_",$sku)[0];
		}
		
		$return = ltrim($return,",");
		
		return explode(",",$return);
		
	}	

	/**
     * Return the Authentication Url for this Integration
     * @return String
     */	
    public function authUrl($state){

        return $this->oAuthService()->redirectUrlForUser([
            'state' => $state,
            'scope' => [
				'https://api.ebay.com/oauth/api_scope',
				'https://api.ebay.com/oauth/api_scope/sell.marketing.readonly',
				'https://api.ebay.com/oauth/api_scope/sell.marketing',
				'https://api.ebay.com/oauth/api_scope/sell.inventory.readonly',
				'https://api.ebay.com/oauth/api_scope/sell.inventory',
				'https://api.ebay.com/oauth/api_scope/sell.account.readonly',
				'https://api.ebay.com/oauth/api_scope/sell.account',
				'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly',
				'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
				'https://api.ebay.com/oauth/api_scope/sell.analytics.readonly',
				'https://api.ebay.com/oauth/api_scope/sell.finances',
				'https://api.ebay.com/oauth/api_scope/sell.payment.dispute',
				'https://api.ebay.com/oauth/api_scope/commerce.identity.readonly',	
            ]
        ]);

    }

	/**
     * Return Response for UserToken Request
     * @return UserTokenRestResponse Instance
     */	
	public function getUserToken($code){
		
		
		$request = new \DTS\eBaySDK\OAuth\Types\GetUserTokenRestRequest();
		$request->code = $code;

		return $this->oAuthService()->getUserToken($request);
	
	}
	
	public function userTokenIsValid(){

		$response = $this->accountService()->getAccountPrivileges();
		
		if($response->getStatusCode() !== 200){
			
			return false;
			
		} else {

			return true;
			
		}
		
	}
	
	/**
     * Return Response for Refresh UserToken
     * @return RefreshUserTokenRestResponse Instance
     */	
	public function refreshToken(){

		$response =  $this->oAuthService()->refreshUserToken(new \DTS\eBaySDK\OAuth\Types\RefreshUserTokenRestRequest([
		'refresh_token' => $this->integration()->getRefreshToken(),
		'scope' => [
					'https://api.ebay.com/oauth/api_scope',
					'https://api.ebay.com/oauth/api_scope/sell.marketing.readonly',
					'https://api.ebay.com/oauth/api_scope/sell.marketing',
					'https://api.ebay.com/oauth/api_scope/sell.inventory.readonly',
					'https://api.ebay.com/oauth/api_scope/sell.inventory',
					'https://api.ebay.com/oauth/api_scope/sell.account.readonly',
					'https://api.ebay.com/oauth/api_scope/sell.account',
					'https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly',
					'https://api.ebay.com/oauth/api_scope/sell.fulfillment',
					'https://api.ebay.com/oauth/api_scope/sell.analytics.readonly',
					'https://api.ebay.com/oauth/api_scope/sell.finances',
					'https://api.ebay.com/oauth/api_scope/sell.payment.dispute',
					'https://api.ebay.com/oauth/api_scope/commerce.identity.readonly',	
				]
		]));

		return $response;

	}
		


    /**
     * Single eBay Order By OrderID
     * @return Order
     */	
	public function getOrder($orderId){

        $pst = new \DateTimeZone('Europe/London');
        $createTimeFrom = new \DateTime("-5 days");
        $createTimeTo = new \DateTime("-0 hours");

        $request = new \DTS\eBaySDK\Trading\Types\GetOrdersRequestType();

        $request->DetailLevel[] = "ReturnAll";
        $request->IncludeFinalValueFee = true;
        $request->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
        $request->Pagination->EntriesPerPage = 50;
        $request->Pagination->PageNumber = 1;
        $request->SortingOrder = \DTS\eBaySDK\Trading\Enums\SortOrderCodeType::C_DESCENDING;
        $request->OrderIDArray = new \DTS\eBaySDK\Trading\Types\OrderIDArrayType();
        $request->OrderIDArray->OrderID[] = $orderId;

        $response = $this->tradingService()->getOrders($request);

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


        if ($response->Ack !== 'Failure' && isset($response->OrderArray->Order)) {
            return $response->OrderArray->Order;
        }

        return null;
    }

    /**
     * Instance of Active Auctions
     * @return Order
     */	
    public function getMyActiveAuctions($pageNum = 1){

        $request = new \DTS\eBaySDK\Trading\Types\GetMyeBaySellingRequestType();

        $request->ActiveList = new \DTS\eBaySDK\Trading\Types\ItemListCustomizationType();
        $request->ActiveList->Include = true;
        $request->ActiveList->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
        $request->ActiveList->Pagination->EntriesPerPage = 50;
        $request->ActiveList->Sort = \DTS\eBaySDK\Trading\Enums\ItemSortTypeCodeType::C_CURRENT_PRICE_DESCENDING;

        $request->ActiveList->Pagination->PageNumber = $pageNum;

        /**
         * Send the request.
         */
        $response = $this->tradingService()->getMyeBaySelling($request);

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

        return null;

    }

	/**
     * Return a single eBay Item by ItemId
     * @return Item
     */	
	public function getItem($itemId){
		
		
		$request = new \DTS\eBaySDK\Trading\Types\GetItemRequestType();

        $request->RequesterCredentials = new \DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType();

        $request->ItemID = $itemId;

        $response = $this->tradingService()->getItem($request);

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

        if ($response->Ack !== 'Failure' && isset($response->Item)) {
            return $response->Item;
        }



    }

    /**
     * Return all active orders
     * @return Instance of ActiveOrders
     */		
    public function getMyOrdersRest()
    {

		$request = new \DTS\eBaySDK\Fulfillment\Types\GetOrdersRestRequest();
		
		$time = strtotime("-5 day");
		$tMicro = sprintf("%03d",($time - floor($time)) * 1000);
		$tUtc = gmdate('Y-m-d\TH:i:s.', $time).$tMicro.'Z';
		$request->filter = "creationdate:%5B" . $tUtc . "511Z..%5D";
		
		$response = $this->fulfillmentService()->getOrders($request);
		
		if($response->getStatusCode() !== 200){
			
			return $response;
			
		} else {

			return $response->orders;
			
		}
        return null;

    }

    /**
     * Updates all purchases with their respective eBay Item ID using SKUs
     * @return int # of purchases updated
     */		
	public function updatePurchasesWithAuctions()
	{

		return 0;

		$updates = 0;

        foreach ($this->getMyActiveAuctions()->ItemArray->Item as $activeAuction) {

            foreach (explode(",", $activeAuction->SKU) as $sku) {

                $purchase = findEntity("purchase", $sku);

                if ($purchase) {
                    $purchase->seteBayItemId($activeAuction->ItemID);
                    entityService()->persist($purchase);
					$updates++;
                    entityService()->flush();
                }
            }
        }

		return $updates;
		
	}




    /**
     * Takes all orders and creates / updates sales
     * @return array[imports, updated, log] 
     */	
    public function CreateSalesFromOrders(){
		
        $imports = 0;
		$updates = 0;
		$result = [];

		/* Save Vendors we will use later */
		$ebaySaleVendor = findEntity("saleVendor", getMetadata("ebay_sale_vendor_id"));
		$ebayPaymentVendor = findEntity("paymentVendor", getMetadata("ebay_payment_vendor_id"));
		
        /* Start By Looping all our orders */
        foreach ($this->getMyOrdersRest() as $order) {

			/* These are any purchases this order fulfilled */
			$fulfilledPurchaseIds = [];
			
			/* GET all SKUs for this line */
			foreach($order->lineItems as $lineItem){
				
				/* All SKUs in this Line Split and Cleaned */
				$lineSkus = $this->SplitSKU($lineItem->sku);
				
				if(empty($lineSkus)) continue;
				
				$fulfilled = 0;
				
				/* Get the Item Itself */
				$item = $this->getItem($lineItem->legacyItemId);
						
				foreach($lineSkus as $sku){
					
					/* Find the Quantity Avaliable for Sale quantitys of more than 1, imply each SKU is one of the quantity, otherwise, all SKUs are included*/
					
					if(!empty($item->Variations)){
					
						foreach($item->Variations->Variation as $variation){
							
							foreach($this->SplitSKU($variation->SKU) as $variationSku){
								
								if($variationSku == $sku){
									$quantity = $variation->Quantity;
								}
							}
						}
					
					}else{
						
						$quantity = $item->Quantity;
						
					}
			
					/* Find a purchase for this given SKU */
					$purchase = findEntity("purchase", $sku);

					/* If we found a Matched Purchase */
					if($purchase){
							
						/* If Listing was a multi-quantity listing we have to do some fulfilment counting */
						if($quantity > 1){

							/* Stop when we have fulfilled the correct quantity */
							if($fulfilled == $lineItem->quantity) continue;

							/* If this is a new sale and purchase has no sale  */
							if($purchase->getSale() == null){

								/* add purchase ID to fulfilled purchases */
								$fulfilledPurchaseIds[] = $purchase->getId();
								$fulfilled++;
														
							/* If it was an existing sale, add anyway */
							}

						/* Otherwise we can just safely add it */
						}else{

							$fulfilledPurchaseIds[] = $purchase->getId();

						}							

					}
				}
			}

			/* Are we dealing with an existing sale? */
			$sales = findBy("sale", ["ebay_order_id" => $order->orderId]);
			
			if(!empty($sales)){
				$sale = $sales[0];
			}
			
			/* We didnt match purchases above using SKUs but we do have a matched sale */
			if(empty($fulfilledPurchaseIds) && !empty($sale)){
				
				foreach($sale->getPurchases() as $purchase){
					$fulfilledPurchaseIds[] = $purchase->getId();
				}
			}
			
			/* If at this point, we fulfilled nothing, then continue */
			if(empty($fulfilledPurchaseIds) && empty($sale)){
				$result[] = [
					"type" => "error", 
					"error" => "No Fulfillable Purchases Found",
					"saleId" => null, 
					"orderId" => $order->orderId,
					"lineSkus" => $lineSkus
				];
				continue;
			}
			
			
			/* Found the sale by its orderId */
			if(!empty($sales)){
				
				$sale = $sales[0];

			/* Try and find the sale by SKU Instead */
			}else{
				
				/* Try and find the sale by one of it's SKUs */
				foreach($fulfilledPurchaseIds as $purchaseId){
					
					$purchase = findEntity("purchase", $purchaseId);
					
					if(!empty($purchase) && !empty($purchase->getSale())){
						$sale = $purchase->getSale();
					}
				}
			}
			
			/* If we still did not match, then its a New Sale */
			if(empty($sale)){
				$sale = new \App\Models\Sale();
			}
			
			/* Sale is either now an existing or new sale */
			
			if($order->orderFulfillmentStatus == "FULFILLED"){
				$sale->setStatus(\app\Models\SaleStatus::Dispatched());
			}
			elseif($order->orderPaymentStatus == "PAID"){
				$sale->setStatus(\app\Models\SaleStatus::Paid());
			}
			elseif($order->orderPaymentStatus == "FAILED"){
				$sale->setStatus(\app\Models\SaleStatus::Cancelled());
			}
			elseif($order->orderPaymentStatus == "FULLY_REFUNDED"){
				$sale->setStatus(\app\Models\SaleStatus::Cancelled());
			}				
			else{
				$sale->setStatus(\app\Models\SaleStatus::UnPaid());
			}						

			/* Clear all purchases */
			$sale->getPurchases()->clear();

			/* Attach Purchases to the Sale */
			foreach($fulfilledPurchaseIds as $purchaseId){
				
				$purchase = findEntity("purchase", $purchaseId);
				
				if($purchase){

					$purchase->setSale($sale);
					
					if($order->orderPaymentStatus == "PAID"){
						$purchase->setStatus(\app\Models\PurchaseStatus::Sold());
					}
					elseif($order->orderPaymentStatus == "FULLY_REFUNDED"){
						$purchase->setStatus(\app\Models\PurchaseStatus::ForSale());
					}
					elseif($order->orderPaymentStatus == "FAILED"){
						$purchase->setStatus(\app\Models\PurchaseStatus::ForSale());
					}
					
					$sale->getPurchases()->add($purchase);
				
				}
			}

			/* We didnt find any SKUs to Purchases so Bail */
			if($sale->getPurchases()->count() == 0){
				$result[] = [
					"type" => "error", 
					"error" => "Sale had no purchases attached",
					"saleId" => null, 
					"orderId" => $order->orderId,
					"lineSkus" => $lineSkus
					];
				continue;
			}
			
			/* Total Fees */
			//$sale->setFeeCost(0);

			/* Gross Amount (Without Postage */
			if(null == $order->pricingSummary->priceSubtotal->convertedFromValue){
				$sale->setGrossAmount($order->pricingSummary->priceSubtotal->value);
			}else{
				$sale->setGrossAmount($order->pricingSummary->priceSubtotal->convertedFromValue);
			}
			
			/* Postage Amount */
			if(null == $order->pricingSummary->deliveryCost->convertedFromValue){
				$sale->setPostageAmount($order->pricingSummary->deliveryCost->value);
			}else{
				$sale->setPostageAmount($order->pricingSummary->deliveryCost->convertedFromValue);
			}			

			/* Postage Cost */
			//$sale->setPostageCost($order->pricingSummary->deliveryCost->convertedFromValue);
			
			/* Order ID */
			$sale->seteBayOrderId($order->orderId);
			
			/* Created Date */
			$sale->setDate(new \DateTime($order->creationDate)); 
			
			/* Vendors */
			$sale->setSaleVendor($ebaySaleVendor);
			$sale->setPaymentVendor($ebayPaymentVendor);
			
			

			if($sale->getId()) $updates++;
			if(empty($sale->getId())) $imports++;		

			if($sale->getId()) $result[] = [
				"type" => "updated", 
				"saleId" => $sale->getId(), 
				"orderId" => $order->orderId, 
				"purchases" => $fulfilledPurchaseIds, 
				"title" => $sale->getPurchasesString(),				
				"grossAmount" => $sale->getGrossAmount(),
				"postageAmount" => $sale->getPostageAmount(),
				"postageCost" => $sale->getPostageCost()
			];
			
			if(empty($sale->getId())) $result[] = [
				"type" => "new", 
				"saleId" => null, 
				"orderId" => $order->orderId, 
				"purchases" => $fulfilledPurchaseIds, 
				"title" => $sale->getPurchasesString(),
				"grossAmount" => $sale->getGrossAmount(),
				"postageAmount" => $sale->getPostageAmount(),
				"postageCost" => $sale->getPostageCost()
			];
			
			entityService()->persist($sale);
			entityService()->flush();
			
			unset($sale);

		}
		

        return ["imports" => $imports, "updates" => $updates, "log" => $result];

    }

}


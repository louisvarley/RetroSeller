<?php

use App\Config;

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;


namespace Core\Services;

class eBayService
{

    protected static $instance = [];


	public $integrationId = 0;

    /**
     *
     * @return CLASS INSTANCE
     */
    public static function instance($integrationId)
    {

        if (!isset(static::$instance[$integrationId])) {
            static::$instance[$integrationId] = new static($integrationId);
        }

        return static::$instance[$integrationId];
    }

	public function __construct($integrationId){
		
		$this->integrationId = $integrationId;
	}

    public function config()
    {


		if(findEntity("integration", $this->integrationId)){
			$integration = findEntity("integration", $this->integrationId);

			return [

				'credentials' => [
					'devId' => $integration->getDevId(),
					'appId' => $integration->getAppId(),
					'certId' => $integration->getCertId(),
				],
				'oauthUserToken' => $integration->getAuthToken(),
			];
			
		}


    }


    public function service()
    {

        return new \DTS\eBaySDK\Trading\Services\TradingService([
            'credentials' => $this->config()['credentials'],
            'siteId' => \DTS\eBaySDK\Constants\SiteIds::GB
        ]);
    }


    public function getMyActiveAuctions($pageNum = 1)
    {

        $request = new \DTS\eBaySDK\Trading\Types\GetMyeBaySellingRequestType();
        $request->RequesterCredentials = new \DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType();

        $request->RequesterCredentials->eBayAuthToken = $this->config()['oauthUserToken']; //$config['production']['oauthUserToken'];

        $request->ActiveList = new \DTS\eBaySDK\Trading\Types\ItemListCustomizationType();
        $request->ActiveList->Include = true;
        $request->ActiveList->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
        $request->ActiveList->Pagination->EntriesPerPage = 50;
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

        return null;

    }

    public function getSaleRecord()
    {


        $request = new \DTS\eBaySDK\Trading\Types\GetSellingManagerSoldListingsRequestType();
        $request->RequesterCredentials = new \DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType();
        $request->RequesterCredentials->eBayAuthToken = $this->config()['oauthUserToken'];
        $response = $this->service()->getSellingManagerSoldListings($request);

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

        return $response->SaleRecord;
    }


	public function getItem($itemId){
		
		
		$request = new \DTS\eBaySDK\Trading\Types\GetItemRequestType();

        $request->RequesterCredentials = new \DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType();
        $request->RequesterCredentials->eBayAuthToken = $this->config()['oauthUserToken'];

        $request->ItemID = $itemId;

        $response = $this->service()->getItem($request);

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

    public function getMyOrders($pageNum = 1)
    {

        $pst = new \DateTimeZone('Europe/London');
        $createTimeFrom = new \DateTime("-5 days");
        $createTimeTo = new \DateTime("-0 hours");

        $request = new \DTS\eBaySDK\Trading\Types\GetOrdersRequestType();

        $request->RequesterCredentials = new \DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType();
        $request->RequesterCredentials->eBayAuthToken = $this->config()['oauthUserToken'];

        $request->CreateTimeFrom = $createTimeFrom;
        $request->CreateTimeTo = $createTimeTo;
        $request->DetailLevel[] = "ReturnAll";
        $request->IncludeFinalValueFee = true;
        $request->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
        $request->Pagination->EntriesPerPage = 50;
        $request->Pagination->PageNumber = $pageNum;
        $request->SortingOrder = \DTS\eBaySDK\Trading\Enums\SortOrderCodeType::C_DESCENDING;


        $response = $this->service()->getOrders($request);

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

        if ($response->Ack !== 'Failure' && isset($response->OrderArray)) {
            return $response->OrderArray;
        }

        return null;

    }


	public function updatePurchasesWithAuctions()
	{
		
		$updates = 0;
		
		/* Loop active auctions, set the item ID to each purchase */
        foreach ($this->getMyActiveAuctions()->ItemArray->Item as $activeAuction) {

            foreach (explode(",", $activeAuction->SKU) as $sku) {

                $purchase = findEntity("purchase", $sku);

                if ($purchase) {
                    $purchase->seteBayItemId($activeAuction->ItemID);
                    entityManager()->persist($purchase);
					$updates++;
                    entityManager()->flush();
                }
            }
        }
		
		return $updates;
		
	}

	/*
	
	1. Loop all orders (service checks last 5 days)
	2. Loop all transactions in these orders
	3. Get Transaction Value and SKU/S from this transaction
	4. If Multiple SKUs and qty for sale > 1 then pick only SKUs for quantity purchased with no sale already
	5. If Single SKU or qty for sale == 1 then just get all SKUs
	6. Check if a sale exists (for updating instead)
	7. Create a sale, attach purchases, fees and gross amount
	*/
	
    public function CreateSalesFromOrders()
    {

        $imports = 0;
		$updates = 0;

        /* Now Loop for any sales that need creating */
        foreach ($this->getMyOrders()->Order as $order) {

            $finalValueFee = 0;
            $skus = "";
			
			
            foreach ($order->TransactionArray->Transaction as $transaction) {

                $finalValueFee = $finalValueFee + $transaction->FinalValueFee->value;
				$qty = $transaction->QuantityPurchased;
				$fulfilled = 0; 
				$item = $this->getItem($transaction->Item->ItemID);

				/* If Quantity Available was more than 1 we will only pick SKUs up to the quantity purchased from SKUs without a SALE*/
				if($item->Quantity > 1){
					
					foreach(explode(",",$transaction->Item->SKU) as $sku){
						 $purchase = findEntity("purchase", $sku);
						 if($purchase){
							 if(empty($purchase->getSale())){
								 if($fulfilled == $qty) continue;
								 $fulfilled++;
								 $skus .= ',' . $sku;
							 }
						 }
					}
					
				}else{
					$skus .= ',' . $transaction->Item->SKU;
				}
            }

            $skuArray = explode(",",rtrim(ltrim($skus,","),","));

            $sale = findBy("sale", ["ebay_order_id" => $order->OrderID]);
			
			/* if still empty try find by SKUs */
			if(empty($sale)){
				foreach($skuArray as $sku){
				$purchase = findEntity("purchase",$sku);					
					if(!empty($purchase)){
						if(!empty($purchase->getSale())){
							$sale = $purchase->getSale();
							continue;
						}
					}
				}
			}

            if(empty($sale)){

                $sale = new \App\Models\Sale();
				
				if($order->OrderStatus == "Completed"){
					$sale->setStatus(\app\Models\SaleStatus::Paid());
				}
				elseif($order->OrderStatus == "Cancelled"){
					$sale->setStatus(\app\Models\SaleStatus::Cancelled());
				}
				else{
					$sale->setStatus(\app\Models\SaleStatus::UnPaid());
				}						

                foreach($skuArray as $sku){
                    $purchase = findEntity("purchase", $sku);
                    if($purchase) {
                        $purchase->setSale($sale);
						$sale->getPurchases()->add($purchase);
                    }
					
                }

				/* We didnt find any SKUs to Purchases so Bail */
                if($sale->getPurchases()->count() == 0) continue;
				
                $sale->setFeeCost($finalValueFee);
                $sale->setGrossAmount($order->AmountPaid->value);
                $sale->seteBayOrderId($order->OrderID);
                $sale->setPostageCost(0);
                $sale->setDate($order->CreatedTime);

                entityManager()->persist($sale);
                entityManager()->flush();
				
                $imports++;
				
            }else{
				
				$sale = $sale[0];
				
				if($order->ShippedTime){
					$sale->setStatus(\app\Models\SaleStatus::Dispatched());
				}
				elseif($order->OrderStatus == "Completed"){
					$sale->setStatus(\app\Models\SaleStatus::Paid());
				}
				elseif($order->OrderStatus == "Cancelled"){
					$sale->setStatus(\app\Models\SaleStatus::Cancelled());
				}
				else{
					$sale->setStatus(\app\Models\SaleStatus::Pending());
				}					

                $sale->setGrossAmount($order->AmountPaid->value);
                $sale->seteBayOrderId($order->OrderID);
                $sale->setDate($order->CreatedTime);
				entityManager()->persist($sale);
                entityManager()->flush();
				
				$updates++;
			}



        }

        return ["imports" => $imports, "updates" => $updates];

    }

}


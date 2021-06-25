<?php

use App\Config;

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;


namespace Core\Services;

class ebayService
{

    public $ebay;

    protected static $instance = null;


    /**
     *
     * @return CLASS INSTANCE
     */
    public static function instance()
    {

        if (null == static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function config()
    {

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


    public function CreateAuctionsFromActiveAuctions()
    {

        /* Loop active auctions, set the item ID for each purchase */
        foreach ($this->getMyActiveAuctions()->ItemArray->Item as $activeAuction) {

            foreach (explode(",", $activeAuction->SKU) as $sku) {

                $purchase = findEntity("purchase", $sku);

                if ($purchase) {
                    $purchase->seteBayItemId($activeAuction->ItemID);
                    entityManager()->persist($purchase);
                    entityManager()->flush();
                }
            }
        }

        /* Now Loop for any sales that need creating */
        foreach (eBayService()->getMyOrders()->Order as $order) {

            $finalValueFee = 0;
            $skus = "";

            foreach ($order->TransactionArray->Transaction as $transaction) {

                $finalValueFee = $finalValueFee + $transaction->FinalValueFee->value;
                $skus .= ',' . $transaction->Item->SKU;

            }

            $skuArray = explode(",",rtrim(ltrim($skus,","),","));

            $sale = findBy("sale", ["ebay_order_id" => $order->OrderID]);

            if(empty($sale)){

                $sale = new \App\Models\Sale();

                foreach($skuArray as $sku){
                    $purchase = findEntity("purchase", $sku);
                    if($purchase) $sale->getPurchases()->add($purchase);
                }

                if($sale->getPurchases()->count() == 0) continue;

                $sale->setFeeCost($finalValueFee);
                $sale->setGrossAmount($order->AmountPaid->value);
                $sale->seteBayOrderId($order->OrderID);
                $sale->setPostageCost(0);
                $sale->setDate(new \Datetime('NOW'));

                entityManager()->persist($sale);
                entityManager()->flush();

            }


        }

    }

}


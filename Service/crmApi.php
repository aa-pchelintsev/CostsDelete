<?php

namespace costsDelete;

use RetailCrm\Api\Factory\SimpleClientFactory;
use RetailCrm\Api\Exception\Client\BuilderException;
use RetailCrm\Api\Model\Request\Orders\OrdersRequest;
use RetailCrm\Api\Model\Filter\Orders\OrderFilter;
use RetailCrm\Api\Model\Request\Costs\CostsRequest;
use RetailCrm\Api\Model\Filter\Costs\CostFilter;
use RetailCrm\Api\Model\Request\Costs\CostsDeleteRequest;



class crmApi
{

    private \RetailCrm\Api\Client $client;

    public function __construct($url, $apiKey, $logger)
    {
        try {
            $this->client = SimpleClientFactory::createClient($url, $apiKey);
        } catch (BuilderException $e) {
            $logger->logError($e);
            exit;
        }
    }

    public function findCostsByOrderNumber($orderResponse): \RetailCrm\Api\Model\Response\Costs\CostsResponse
    {
        $orderNumber = $orderResponse->number;
        $costsRequest = new CostsRequest();
        $costsRequest->filter = new CostFilter();
        $costsRequest->filter->orderNumber = $orderNumber;
        return $this->client->costs->list($costsRequest);
    }

    public function findOrderByInnerId(string $urlLine): \RetailCrm\Api\Model\Entity\Orders\Order
    {
        $re = '/\/(\d+)\//';
        $id = preg_split($re, $urlLine, -1, PREG_SPLIT_DELIM_CAPTURE)[1];
        $orderRequest = new OrdersRequest();
        $orderRequest->filter = new OrderFilter();
        $orderRequest->filter->ids = [$id];
        return $this->client->orders->list($orderRequest)->orders[0];
    }

    public function deleteCostInOrder($idsDeleteArray): \RetailCrm\Api\Model\Response\Costs\CostsDeleteResponse
    {
        $costDeleteRequest = new CostsDeleteRequest();
        $costDeleteRequest->ids = json_encode($idsDeleteArray);
        return $this->client->costs->costsDelete($costDeleteRequest);
    }


}
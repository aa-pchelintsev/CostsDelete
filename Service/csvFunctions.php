<?php

namespace costsDelete;


class csvFunctions
{
    private $api;
    private $logger;

    public function __construct($api, $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function processCSVFile($filename): void
    {
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $lineNumber = 1;
            while (($data = fgetcsv($handle)) !== FALSE) {
                $urlStr = $data[0];
                if (!empty($urlStr)) {
                    $this->processCosts($urlStr);
                } else {
                    $this->logger->logResult->info("Артефакт. В строке $lineNumber. Не было обнаружено ссылки на заказ");
                }
                $lineNumber++;
            }
            fclose($handle);
        }
    }

    private function processCosts($urlLine): void
    {
        $orderResponse = $this->api->findOrderByInnerId($urlLine);
        $costsResponse = $this->api->findCostsByOrderNumber($orderResponse);
        $idsToDelete = [];
        $counter = 0;
        $order = $orderResponse->number;
        if (!empty($costsResponse->costs)) {
            foreach ($costsResponse->costs as $index => $cost) {
                if ($cost->costItem == "marketplace-commission" and $cost->comment == "Комиссия OZON за продажу товаров") {
                    $counter++;
                    if ($index > 0) {
                        $idsToDelete[] = $cost->id;
                        $idsStr = implode(",", $idsToDelete);
                    }
                }
            }
            if (!empty($idsToDelete)) {
                    $this->api->deleteCostInOrder($idsToDelete);
                    $this->logger->logSuccess("Успех. В заказе $order в строке $urlLine расходы с id $idsStr были успешно удалены");
            }
            elseif ($counter == 1) {
                {
                    $this->logger->logSuccess("Без изменений. В заказе $order в строке $urlLine расход с id $cost->id всего один");
                }
            }
            else {
                $this->logger->logSuccess("Не найден. В заказе $order в строке $urlLine расход для удаления не найден");
            }
        }
    }
}
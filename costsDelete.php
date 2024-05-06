<?php

require_once "vendor/autoload.php";
require_once "Service/csvFunctions.php";

use costsDelete\crmApi;
use costsDelete\customLogger;
use costsDelete\csvFunctions;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$url = $_ENV["API_URL"];
$apiKey = $_ENV["API_KEY"];

$logger = new customLogger();
$api = new crmApi($url, $apiKey, $logger);


$csvFunctions = new csvFunctions($api, $logger);
$csvFunctions->processCSVFile("ordersPchelintsev-staff.csv");

$logger->logResult->close();
$logger->logError->close();
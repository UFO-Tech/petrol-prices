<?php

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Ufo\PetrolPrices\FuelPriceService;
use Ufo\PetrolPrices\PetrolVendors\AutoRiaStations;
use Ufo\PetrolPrices\Providers\AutoRiaFuelPriceProvider;

require_once __DIR__.'/../vendor/autoload.php';

$httpClient = HttpClient::create();
$cache = new FilesystemAdapter(directory: __DIR__ . '/../var/cache');
$provider = new AutoRiaFuelPriceProvider($httpClient);
$fuelPriceService = new FuelPriceService($provider, $cache);
$provider->setVendor(AutoRiaStations::UKRNAFTA);
try {
    $prices = $fuelPriceService->getFuelPrices();

    var_dump($prices);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
# Fuel Price Service

## Description

`Fuel Price Service` is a PHP library for fetching and caching up-to-date fuel prices from various providers.  
It supports PSR-compliant interfaces for better integration into modern PHP projects.

## Features

- Fetch fuel prices from providers via API.
- Automatic caching with customizable TTL.
- Easy addition of new data providers through interfaces.
- Exception handling for errors during data retrieval.

## Installation

Install the library via Composer:

```bash
composer require ufo/petrol-prices
```

## Usage

### Basic Example

```php
use Ufo\PetrolPrices\FuelPriceService;
use Ufo\PetrolPrices\Providers\AutoRiaFuelPriceProvider;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;

$httpClient = HttpClient::create();
$cache = new FilesystemAdapter();
$provider = new AutoRiaFuelPriceProvider($httpClient);

$service = new FuelPriceService($provider, $cache);

try {
    $fuelPrices = $service->getFuelPrices();
    echo "Date: {$fuelPrices->date}\n";
    echo "A-95 Premium: {$fuelPrices->a95Plus} UAH\n";
    echo "A-95: {$fuelPrices->a95} UAH\n";
    echo "A-92: {$fuelPrices->a92} UAH\n";
    echo "Diesel: {$fuelPrices->diesel} UAH\n";
    echo "Gas: {$fuelPrices->gas} UAH\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Usage Without Caching

```php
$service = new FuelPriceService($provider);
$fuelPrices = $service->getFuelPrices();
```

### Usage with Custom TTL

```php
$service = new FuelPriceService($provider, $cache, cacheTtl: 7200); // Cache for 2 hours
$fuelPrices = $service->getFuelPrices();
```

## Extending

To add a new provider, implement the `IFuelPriceProvider` interface:

```php
namespace Ufo\PetrolPrices\Interfaces;

use Ufo\PetrolPrices\DTO\FuelPrice;

interface IFuelPriceProvider
{
    public function getFuelPrices(): FuelPrice;
}
```

## Testing

Run tests using PHPUnit:

```bash
vendor/bin/phpunit tests/
```

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

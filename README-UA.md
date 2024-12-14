# Fuel Price Service

## Опис

`Fuel Price Service` – це PHP-бібліотека для отримання та кешування актуальних цін на пальне від різних постачальників.
Вона підтримує використання PSR-сумісних інтерфейсів для кращої інтеграції в сучасні PHP-проекти.

## Можливості

- Отримання цін на пальне від постачальників через API.
- Автоматичне кешування результатів з можливістю налаштування TTL.
- Легке додавання нових постачальників даних завдяки інтерфейсам.
- Винятки для обробки помилок під час отримання даних.

## Встановлення

Встановіть бібліотеку через Composer:

```bash
composer require ufo/petrol-prices
```

## Використання

### Базовий приклад

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
    echo "Дата: {$fuelPrices->date}\n";
    echo "А-95 преміум: {$fuelPrices->a95Plus} грн\n";
    echo "А-95: {$fuelPrices->a95} грн\n";
    echo "А-92: {$fuelPrices->a92} грн\n";
    echo "Дизель: {$fuelPrices->diesel} грн\n";
    echo "Газ: {$fuelPrices->gas} грн\n";
} catch (\Exception $e) {
    echo "Помилка: " . $e->getMessage();
}
```

### Використання без кешування

```php
$service = new FuelPriceService($provider);
$fuelPrices = $service->getFuelPrices();
```

### Використання з кастомним TTL

```php
$service = new FuelPriceService($provider, $cache, cacheTtl: 7200); // Кеш на 2 години
$fuelPrices = $service->getFuelPrices();
```

## Розширення

Для додавання нового постачальника реалізуйте інтерфейс `IFuelPriceProvider`:

```php
namespace Ufo\PetrolPrices\Interfaces;

use Ufo\PetrolPrices\DTO\FuelPrice;

interface IFuelPriceProvider
{
    public function getFuelPrices(): FuelPrice;
}
```

## Тестування

Запустіть тести через PHPUnit:

```bash
vendor/bin/phpunit tests/
```

## Ліцензія

Цей проект розповсюджується за ліцензією MIT. Деталі дивіться у файлі [LICENSE](LICENSE).

<?php

namespace Tests\Ufo\PetrolPrices;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Ufo\PetrolPrices\FuelPriceService;
use Ufo\PetrolPrices\Interfaces\IFuelPriceProvider;
use Ufo\PetrolPrices\DTO\FuelPrice;

class FuelPriceServiceTest extends TestCase
{
    public function testGetFuelPricesWithCache(): void
    {
        $mockProvider = $this->createMock(IFuelPriceProvider::class);
        $mockProvider->expects($this->once())
                     ->method('getFuelPrices')
                     ->willReturn(new FuelPrice('2024-12-14', 50.5, 49.5, 48.0, 47.0, 30.0));

        $mockCache = $this->createMock(CacheInterface::class);
        $mockCache->expects($this->once())
                  ->method('get')
                  ->with('fuel_prices')
                  ->willReturnCallback(function ($key, $callback) {
                      $item = $this->createMock(ItemInterface::class);
                      $item->expects($this->once())
                           ->method('expiresAfter')
                           ->with(3600);

                      return $callback($item);
                  });

        $service = new FuelPriceService($mockProvider, $mockCache);
        $fuelPrices = $service->getFuelPrices();

        $this->assertInstanceOf(FuelPrice::class, $fuelPrices);
        $this->assertEquals('2024-12-14', $fuelPrices->date);
        $this->assertEquals(50.5, $fuelPrices->a95Plus);
    }

    public function testGetFuelPricesWithoutCache(): void
    {
        $mockProvider = $this->createMock(IFuelPriceProvider::class);
        $mockProvider->expects($this->once())
                     ->method('getFuelPrices')
                     ->willReturn(new FuelPrice('2024-12-14', 50.5, 49.5, 48.0, 47.0, 30.0));

        $service = new FuelPriceService($mockProvider, null);
        $fuelPrices = $service->getFuelPrices();

        $this->assertInstanceOf(FuelPrice::class, $fuelPrices);
        $this->assertEquals('2024-12-14', $fuelPrices->date);
        $this->assertEquals(50.5, $fuelPrices->a95Plus);
    }
}
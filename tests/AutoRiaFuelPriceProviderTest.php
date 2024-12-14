<?php

namespace Tests\Ufo\PetrolPrices;

use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Ufo\PetrolPrices\Providers\AutoRiaFuelPriceProvider;
use Ufo\PetrolPrices\DTO\FuelPrice;
use Ufo\PetrolPrices\Exceptions\FuelPriceException;

class AutoRiaFuelPriceProviderTest extends TestCase
{
    public function testGetFuelPrices(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
                     ->method('getContent')
                     ->willReturn(json_encode([
                         'buckets' => [
                             [
                                 'key_as_string' => '2024-12-14',
                                 'a95pf' => ['avg' => 50.5],
                                 'a95f' => ['avg' => 49.5],
                                 'a92f' => ['avg' => 48.0],
                                 'dtf' => ['avg' => 47.0],
                                 'gazf' => ['avg' => 30.0],
                             ]
                         ]
                     ]));

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
                       ->method('request')
                       ->with('GET', $this->anything())
                       ->willReturn($mockResponse);

        $provider = new AutoRiaFuelPriceProvider($mockHttpClient);
        $fuelPrices = $provider->getFuelPrices();

        $this->assertInstanceOf(FuelPrice::class, $fuelPrices);
        $this->assertEquals('2024-12-14', $fuelPrices->date);
        $this->assertEquals(50.5, $fuelPrices->a95Plus);
    }

    public function testGetFuelPricesThrowsExceptionOnInvalidResponse(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
                     ->method('getContent')
                     ->willReturn('{}');

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
                       ->method('request')
                       ->with('GET', $this->anything())
                       ->willReturn($mockResponse);

        $provider = new AutoRiaFuelPriceProvider($mockHttpClient);

        $this->expectException(FuelPriceException::class);
        $provider->getFuelPrices();
    }
}
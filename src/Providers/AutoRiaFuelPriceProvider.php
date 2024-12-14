<?php

namespace Ufo\PetrolPrices\Providers;

use DateMalformedStringException;
use DateTime;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Ufo\PetrolPrices\DTO\FuelPrice;
use Ufo\PetrolPrices\Exceptions\FuelPriceException;
use Ufo\PetrolPrices\Interfaces\IFuelPriceProvider;
use Ufo\PetrolPrices\PetrolVendors\AutoRiaStations;

use function array_merge;
use function current;
use function json_decode;

class AutoRiaFuelPriceProvider implements IFuelPriceProvider
{
    private const string URL = 'https://auto.ria.com/content/news/templetify/fuel_price_page/';

    private const array QUERY = [
        'refuel' => null,
        'langId' => 4,
        'size' => 1,
    ];

    protected ?AutoRiaStations $vendor = null;

    public function __construct(
        protected HttpClientInterface $httpClient
    ) {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws FuelPriceException
     * @throws DateMalformedStringException
     */
    public function getFuelPrices(): FuelPrice
    {
        $params = self::QUERY;
        if ($this->vendor) {
            $params['refuel'] = $this->vendor->value;
        }

        $response = $this->httpClient->request(
            'GET',
            self::URL,
            ['query' => $params]
        );
        $content = json_decode($response->getContent(), true) ?? throw new FuelPriceException('Can`t get API data');
        $prices = current($content['buckets'] ?? [null]) ??  throw new FuelPriceException('Response not have prices');

        $date = new DateTime($prices['key_as_string']);

        return new FuelPrice(
            $date->format('Y-m-d'),
            $prices['a95pf']['avg'],
            $prices['a95f']['avg'],
            $prices['a92f']['avg'],
            $prices['dtf']['avg'],
            $prices['gazf']['avg'],
        );
    }

    public function setVendor(AutoRiaStations $vendor): static
    {
        $this->vendor = $vendor;
        return $this;
    }

    public function unsetVendor(): static
    {
        $this->vendor = null;
        return $this;
    }



}
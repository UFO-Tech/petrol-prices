<?php

namespace Ufo\PetrolPrices;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Ufo\PetrolPrices\DTO\FuelPrice;
use Ufo\PetrolPrices\Interfaces\IFuelPriceProvider;

class FuelPriceService
{
    public function __construct(
        protected IFuelPriceProvider $provider,
        protected ?CacheInterface $cache = null,
        protected int $cacheTtl = 3600,
    ) {}

    /**
     * Get cached fuel prices.
     *
     * @throws InvalidArgumentException
     */
    public function getFuelPrices(): FuelPrice
    {
        if (!$this->cache) {
            return $this->provider->getFuelPrices();
        }

        return $this->cache->get("fuel_prices", function (ItemInterface $item) {
            $item->expiresAfter($this->cacheTtl);
            return $this->provider->getFuelPrices();
        });
    }
}
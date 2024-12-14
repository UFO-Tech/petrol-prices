<?php

namespace Ufo\PetrolPrices\Interfaces;

use Ufo\PetrolPrices\DTO\FuelPrice;

interface IFuelPriceProvider
{
    /**
     * Get fuel prices for all available types.
     *
     * @return FuelPrice
     */
    public function getFuelPrices(): FuelPrice;
}
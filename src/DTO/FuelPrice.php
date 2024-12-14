<?php
namespace Ufo\PetrolPrices\DTO;


readonly class FuelPrice
{
    public function __construct(
        public string $date,
        public float $a95Plus,
        public float $a95,
        public float $a92,
        public float $diesel,
        public float $gas,
    ) {}
}
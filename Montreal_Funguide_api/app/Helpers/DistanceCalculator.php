<?php

namespace App\API;

use Frostybee\Geobee\Calculator;
use Slim\Exception\HttpBadRequestException;

class DistanceCalculator
{
    private Calculator $calculator;

    public function __construct()
    {
        $this->calculator = new Calculator();
    }

    public function getDistance(array $start, array $end): array
    {
        if (
            !isset($start['lat'], $start['lng'], $end['lat'], $end['lng'])
        ) {
            throw new \Exception("Invalid coordinates");
        }

        return [
            'value' => $this->calculator
                ->calculate($start['lat'], $start['lng'], $end['lat'], $end['lng'])
                ->to('km', 2),
            'unit' => 'km'
        ];
    }
}

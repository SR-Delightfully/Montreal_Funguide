<?php

namespace App\API;

class UnitConverter
{
    private static array $ingredientDensity = [
        'water' => 1.000,
        'flour' => 0.530,
        'milk' => 1.030,
        'sugar' => 0.845,
        'salt' => 1.217,
        'honey' => 1.420,
        'butter' => 0.959,
        'oil' => 0.946,
        'rice' => 0.760,
        'oats' => 0.338
    ];

    private static array $unitsVolume = [
        'ml' => 0.001,
        'l' => 1,
        'gal' => 3.8,
        'oz' => 0.0296,
        'cup' => 0.237
    ];

    private static array $unitsMass = [
        'g' => 0.001,
        'kg' => 1,
        'lb' => 0.45,
        'oz' => 0.0283,
    ];

    public static function convertVolumeToMass(float $volume, string $ingredient, string $fromUnit, string $toUnit): float
    {
        if (!isset(self::$ingredientDensity[$ingredient])) {
            throw new \Exception("Unknown ingredient density");
        }

        $density = self::$ingredientDensity[$ingredient];

        $volumeInLiters = $volume * (self::$unitsVolume[$fromUnit] ?? 1);
        $massInKg = $volumeInLiters * $density;

        return $massInKg / (self::$unitsMass[$toUnit] ?? 1);
    }

    public static function convertMassToVolume(float $mass, string $ingredient, string $fromUnit, string $toUnit): float
    {
        if (!isset(self::$ingredientDensity[$ingredient])) {
            throw new \Exception("Unknown ingredient density");
        }

        $density = self::$ingredientDensity[$ingredient];

        $massInKg = $mass * (self::$unitsMass[$fromUnit] ?? 1);
        $volumeInLiters = $massInKg / $density;

        return $volumeInLiters / (self::$unitsVolume[$toUnit] ?? 1);
    }
}

<?php

declare(strict_types=1);

require_once __DIR__ . "/../../vendor/autoload.php";

use App\Helpers\Core\PDOService;
use App\Exceptions\DataImportException;

$configFn = require __DIR__ . "/../../config/env.php";
$config = $configFn([]);

try {
    $pdoService = new PDOService($config['db']);
    $pdo = $pdoService->getPDO();
} catch (Throwable $e) {
    die("DB CONNECTION FAILED: " . $e->getMessage());
}

$apiKey = $config['api']['ninjas_key'] ?? null;

if (!$apiKey) {
    throw new DataImportException(null, "Missing API key");
}

$stmt = $pdo->prepare("SELECT ingredient_id, ingredient_name FROM ingredient");
$stmt->execute();
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nutritionStmt = $pdo->prepare("
    INSERT INTO calories (
        ingredient_id,
        calories,
        protein,
        fat,
        carbohydrates
    ) VALUES (
        :ingredient_id,
        :calories,
        :protein,
        :fat,
        :carbs
    )
");

foreach ($ingredients as $ingredient) {

    $url = "https://api.api-ninjas.com/v1/nutrition?query=" . urlencode($ingredient['ingredient_name']);

    $context = stream_context_create([
        "http" => [
            "header" => "X-Api-Key: " . $apiKey . "\r\n"
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) continue;

    $data = json_decode($response, true);

    if (!isset($data[0])) continue;

    $nutr = $data[0];

    $nutritionStmt->execute([
        ':ingredient_id' => $ingredient['ingredient_id'],
        ':calories' => $nutr['calories'] ?? null,
        ':protein' => $nutr['protein_g'] ?? null,
        ':fat' => $nutr['fat_total_g'] ?? null,
        ':carbs' => $nutr['carbohydrates_total_g'] ?? null
    ]);
}

echo "Nutrition enrichment completed.";
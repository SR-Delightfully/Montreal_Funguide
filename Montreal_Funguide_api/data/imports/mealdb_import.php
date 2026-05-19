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

$response = file_get_contents("https://www.themealdb.com/api/json/v1/1/search.php?s=");

if ($response === false) {
    throw new DataImportException(null, "Failed to connect to MealDB API");
}

$data = json_decode($response, true);

if (!isset($data["meals"])) {
    throw new DataImportException(null, "Invalid MealDB response");
}

$stmt = $pdo->prepare("
    INSERT INTO recipe (
        recipe_name,
        recipe_source,
        recipe_instructions,
        recipe_thumbnail
    ) VALUES (
        :name,
        'MealDB',
        :instructions,
        :thumbnail
    )
");

foreach ($data["meals"] as $meal) {
    $stmt->execute([
        ':name' => $meal['strMeal'] ?? 'Unknown',
        ':instructions' => $meal['strInstructions'] ?? null,
        ':thumbnail' => $meal['strMealThumb'] ?? null
    ]);
}

echo "MealDB import completed successfully.";
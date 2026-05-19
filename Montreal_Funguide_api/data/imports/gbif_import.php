<?php

declare(strict_types=1);

require_once __DIR__ . "/../../vendor/autoload.php";

use App\Helpers\Core\PDOService;

$configFn = require __DIR__ . "/../../config/env.php";
$config = $configFn([]);

try {
    $pdo = (new PDOService($config['db']))->getPDO();
} catch (Throwable $e) {
    die("DB CONNECTION FAILED: " . $e->getMessage());
}

$response = file_get_contents("https://api.gbif.org/v1/occurrence/search?taxon_key=5&limit=20");

$data = json_decode($response, true);

$stmt = $pdo->prepare("
    INSERT INTO species (
        species_name,
        species_family,
        species_genus,
        species_gbif_id
    ) VALUES (
        :name,
        :family,
        :genus,
        :gbif_id
    )
");

foreach ($data["results"] as $item) {
    $stmt->execute([
        ':name' => $item['species'] ?? $item['scientificName'] ?? 'Unknown',
        ':family' => $item['family'] ?? null,
        ':genus' => $item['genus'] ?? null,
        ':gbif_id' => $item['taxonKey'] ?? null
    ]);
}

echo "GBIF import completed successfully.";
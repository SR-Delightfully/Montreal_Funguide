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

$addresses = [
    "Mount Royal Montreal",
    "Old Port Montreal",
    "Montreal Botanical Garden"
];

$stmt = $pdo->prepare("
    INSERT INTO location (
        location_name,
        location_lat,
        location_long,
        location_borough,
        location_type
    ) VALUES (
        :name,
        :lat,
        :lng,
        :borough,
        'park'
    )
");

foreach ($addresses as $address) {

    $url = "https://api.api-ninjas.com/v1/geocoding?address=" . urlencode($address);

    $context = stream_context_create([
        "http" => [
            "header" => "X-Api-Key: " . $config['api']['ninjas_key'] . "\r\n"
        ]
    ]);

    $response = file_get_contents($url, false, $context);

    if ($response === false) continue;

    $data = json_decode($response, true);

    if (!isset($data[0])) continue;

    $result = $data[0];

    $stmt->execute([
        ':name' => $address,
        ':lat' => $result['latitude'] ?? null,
        ':lng' => $result['longitude'] ?? null,
        ':borough' => null
    ]);
}

echo "Geocoding import completed successfully.";
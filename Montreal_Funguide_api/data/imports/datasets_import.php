<?php

declare(strict_types=1);

echo "Starting full data import...\n\n";

function runScript(string $path): void
{
    echo "Running: $path\n";

    $php = PHP_BINARY;

    // FIX: Windows-safe quoting (DO NOT use escapeshellarg here)
    $cmd = '"' . $php . '" "' . $path . '"';

    $output = [];
    $resultCode = 0;

    exec($cmd, $output, $resultCode);

    echo implode("\n", $output) . "\n";

    if ($resultCode !== 0) {
        echo "[ERROR]: $path\n";
        exit(1);
    }

    echo "[SUCCESS]: $path\n\n";
}

$base = __DIR__;

runScript($base . "/gbif_import.php");
runScript($base . "/geocode_import.php");
runScript($base . "/mealdb_import.php");
runScript($base . "/nutrition_import.php");

echo "ALL IMPORTS COMPLETED SUCCESSFULLY\n";
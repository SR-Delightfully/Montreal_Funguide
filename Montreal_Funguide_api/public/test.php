<?php

try {

    $pdo = new PDO(
        "mysql:host=127.0.0.1;dbname=montreal_funguide_db",
        "root",
        ""
    );

    echo "CONNECTED";

} catch (PDOException $e) {

    echo $e->getMessage();
}
<?php
$c = json_decode(file_get_contents(__DIR__ . '/database/data/countries_extended.json'), true);
echo json_encode([
    'region' => $c[0]['region'] ?? null,
    'area' => $c[0]['area'] ?? null,
    'languages' => $c[0]['languages'] ?? null
]);

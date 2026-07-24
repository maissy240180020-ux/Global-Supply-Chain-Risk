<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$missing = ["Antarctica", "French Southern and Antarctic Lands", "Bermuda", "Northern Cyprus", "Falkland Islands", "Greenland", "French Guiana", "Kosovo", "New Caledonia", "Puerto Rico", "Western Sahara", "Solomon Islands", "Somaliland", "West Bank", "Taiwan"];

foreach ($missing as $name) {
    // Generate a pseudo-random country code
    $code = strtoupper(substr($name, 0, 2)); 
    // Fix specific codes if needed to avoid duplicate
    if ($name == "Kosovo") $code = "XK";
    if ($name == "Taiwan") $code = "TW";

    $riskScore = rand(15, 75);
    $riskLevel = 'Low';
    if ($riskScore > 60) {
        $riskLevel = 'High';
    } elseif ($riskScore > 35) {
        $riskLevel = 'Medium';
    }

    App\Models\Country::updateOrCreate(
        ['country_name' => $name],
        [
            'country_code' => $code,
            'flag' => 'https://flagcdn.com/w320/' . strtolower($code) . '.png',
            'capital' => '-',
            'currency' => 'USD',
            'population' => rand(100000, 5000000),
            'latitude' => 0.0,
            'longitude' => 0.0,
            'gdp' => rand(1000000000, 50000000000),
            'inflation' => 0,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'temperature' => rand(10, 35),
            'weather' => 'Cerah',
        ]
    );
}

echo "Berhasil menambahkan " . count($missing) . " negara/wilayah baru.\n";

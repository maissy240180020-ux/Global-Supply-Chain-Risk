<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$res = Illuminate\Support\Facades\Http::withoutVerifying()->get('https://restcountries.com/v3.1/alpha/ID');
echo json_encode($res->json());

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = App\Models\Country::pluck('country_name')->map(fn($n) => strtolower($n))->toArray();
$j = json_decode(file_get_contents(public_path('geojson/world.geojson')), true);
$g = array_map(fn($f) => strtolower($f['properties']['name']), $j['features']);

$aliases = [
    'the bahamas' => 'bahamas',
    'united states of america' => 'united states',
    'united republic of tanzania' => 'tanzania',
    'republic of serbia' => 'serbia',
    'russian federation' => 'russia',
    'democratic republic of the congo' => 'congo',
    'republic of the congo' => 'congo',
    'ivory coast' => 'côte d\'ivoire',
    'côte d\'ivoire' => 'ivory coast',
    'south korea' => 'korea, south',
    'north korea' => 'korea, north',
    'vietnam' => 'viet nam',
    'macedonia' => 'north macedonia'
];

$missing = [];
foreach($g as $name) {
    $mapped = $aliases[$name] ?? $name;
    if(!in_array($mapped, $db)) {
        $missing[] = $name;
    }
}
echo json_encode(array_values($missing));

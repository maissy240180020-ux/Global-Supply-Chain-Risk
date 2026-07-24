<?php
$c = json_decode(file_get_contents(__DIR__ . '/database/data/world_countries.json'), true);
echo json_encode(array_keys($c[0]));

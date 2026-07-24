<?php
$d = base64_decode('CBMiM2h0dHBzOi8vd3d3LmJsb29tYmVyZy5jb20vbmV3cy9hcnRpY2xlcy8yMDI2LTA3LTE4');
$p = strpos($d, 'http');
$u = substr($d, $p);
$clean = preg_replace('/[^a-zA-Z0-9_\-\.\/\?&\+=#%~:]/', '', $u);
var_dump($clean);

<?php
include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . './indelude.php';

use \EdwinLuijten\Houston\Houston;

$_SESSION = [];

Houston::init([
    'handler' => 'file',
    'file_log_location' => __DIR__ . '/..',
]);

new Tes();
new Exceptio();
echo 'hello world';
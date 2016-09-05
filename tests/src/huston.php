<?php
include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . './indelude.php';

use \EdwinLuijten\Houston\Houston;

$_SESSION = [];

Houston::init([
    'handler' => 'file',
    'file_log_location' => __DIR__ . '/..',
]);
trigger_error('deprecated error', E_USER_DEPRECATED);
trigger_error('error', E_USER_ERROR);
trigger_error('notice', E_USER_NOTICE);
trigger_error('warning', E_USER_WARNING);

throw new Exception('thrown exception');
new Tes();

echo 'hello world';
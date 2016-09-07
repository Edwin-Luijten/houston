<?php
include_once __DIR__ . '/../../vendor/autoload.php';
include_once __DIR__ . './indelude.php';

use \EdwinLuijten\Houston\Houston;

$_SESSION = [];

Houston::init([
    'file_log_location' => __DIR__ . '/../houston/houston.problem',
]);
trigger_error('deprecated error', E_USER_DEPRECATED);
trigger_error('error', E_USER_ERROR);
trigger_error('notice', E_USER_NOTICE);
trigger_error('warning', E_USER_WARNING);

throw new Exception('thrown exception' . "\n");
new Tes();

echo 'hello world';
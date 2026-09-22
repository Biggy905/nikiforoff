<?php

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Http\Request;

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());

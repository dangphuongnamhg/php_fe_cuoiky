<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$r = Illuminate\Support\Facades\Http::get('http://127.0.0.1:8001/api/pitches', ['status' => 'active']);
var_dump($r->successful());
var_dump($r->json('data.data'));

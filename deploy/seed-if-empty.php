<?php

use App\Models\Offer;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Console\Kernel;

$backendPath = dirname(__DIR__);

require $backendPath.'/vendor/autoload.php';
$app = require $backendPath.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

if (Offer::query()->count() < 4) {
    $app->make(DatabaseSeeder::class)->run();
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fks = DB::select("SELECT CONSTRAINT_NAME, COLUMN_NAME FROM information_schema.key_column_usage WHERE TABLE_NAME='menu' AND CONSTRAINT_SCHEMA='dapur_aisyah' AND REFERENCED_TABLE_NAME IS NOT NULL");
print_r($fks);

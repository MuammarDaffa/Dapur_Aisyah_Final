<?php
$f = 'e:/TA/Dapur_Aisyah/database/seeders/LayananKateringSeeder.php';
$c = file_get_contents($f);
$c = preg_replace('/, \'available_days\' => \[[^\]]*\]/', '', $c);
file_put_contents($f, $c);

<?php

date_default_timezone_set('Asia/Bangkok');

// allow CLI only
if (php_sapi_name() !== 'cli') {
    exit('Forbidden');
}

// bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$date = date('Y-m-d_H-i-s');

// path
$basePath = __DIR__ . '/storage/app/dbonly';
@mkdir($basePath, 0755, true);

// ✅ db config (ถูกต้อง)
$dbHost = config('database.connections.mysql.host');
$dbName = config('database.connections.mysql.database');
$dbUser = config('database.connections.mysql.username');
$dbPass = config('database.connections.mysql.password');

// mysqldump path
$mysqldump = 'E:\\xampp\\mysql\\bin\\mysqldump.exe';

$dbFile = "$basePath/dbonly_$date.sql";

$command = "\"$mysqldump\" -h $dbHost -u $dbUser -p$dbPass $dbName > \"$dbFile\"";

exec($command, $output, $result);

if ($result !== 0) {
    echo "❌ DB backup failed\n";
    print_r($output);
    exit;
}

echo "✔ DB ONLY backup completed: $date\n";

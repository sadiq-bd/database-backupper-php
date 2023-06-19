<?php
use Sadiq\DB_Backup;
require __DIR__ . '/Sadiq/DB_Backup.php';

$dbBackup = new DB_Backup(
    user: 'root',
    password: '4616' 
);
$dbBackup->setBackupDir(__DIR__ . '/backup');

if ($dbBackup->createBackup('db')) {
    echo 'backup success!';
}

// if ($dbBackup->pushBackup('db2')) {
//     echo 'push to database success!';
// }

echo PHP_EOL;

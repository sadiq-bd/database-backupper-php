<p align="left">
  <img src="https://api.sadiq.workers.dev/app/github/repo/database-backupper-php/views" alt="Repo views" />
</p>

# Database Backupper

A simple PHP utility for backing up and restoring MySQL databases.

## Features

- Create database backups easily
- Restore backups to any database
- Set custom backup directory
- Lightweight and easy to use

## Requirements

- PHP 8.0 or higher
- MySQL/MariaDB
- Composer (for autoloading, if using outside of example)

## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/sadiq-bd/database-backupper-php.git
   cd database-backupper-php
   ```

2. **(Optional) Install dependencies using Composer:**

   If the project grows or you want to use autoloading, run:

   ```bash
   composer install
   ```

## Usage

### Create a Backup

Edit `backup.php` to configure your database credentials and backup directory:

```php
use Sadiq\DB_Backup;
require __DIR__ . '/Sadiq/DB_Backup.php';

$dbBackup = new DB_Backup(
    user: 'root',
    password: 'your_password'
);
$dbBackup->setBackupDir(__DIR__ . '/backup');

if ($dbBackup->createBackup('your_database_name')) {
    echo 'backup success!';
}
```

Then run:

```bash
php backup.php
```

### Restore a Backup

Uncomment and use the `pushBackup` method in `backup.php`:

```php
// if ($dbBackup->pushBackup('your_database_name')) {
//     echo 'push to database success!';
// }
```

## Directory Structure

```
database-backupper-php/
├── Sadiq/
│   └── DB_Backup.php
├── backup.php
└── README.md
```

## License

MIT License

## Author

[Sadiq](https://github.com/sadiq-bd)

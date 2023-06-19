<?php
namespace Sadiq;
use PDO;
use PDOException;

/**
 * @author Sadiq <sadiq.developer.bd@gmail.com>
 */

class DB_Backup {

    private $db_type = 'mysql';
    private $db_host = '';
    private $db_port = 0;
    private $db_user = '';
    private $db_password = '';
    private $db_charset = '';

    private $db_name = '';

    private $pdo;

    private $backup_dir = '';

    public function __construct(
        $host = 'localhost', 
        $port = 3306, 
        $user = '', 
        $password = '', 
        $charset = 'utf8mb4'
    ) {
        $this->db_host = $host;
        $this->db_port = $port;
        $this->db_user = $user;
        $this->db_password = $password;
        $this->db_charset = $charset;
    }

    public function setBackupDir($dir) {
        if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
            mkdir($dir);
        }
        $this->backup_dir = $dir;
    }

    public function connectDB(string $dbname) {

        $this->db_name = $dbname;

        $dsn = sprintf(
            "%s:host=%s;port=%d;dbname=%s;charset=%s",
            $this->db_type, 
            $this->db_host, 
            $this->db_port, 
            $dbname, 
            $this->db_charset
        );

        $this->pdo = new PDO($dsn, $this->db_user, $this->db_password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        return $this;
    }

    public function listTables() {
        try {
            $query = "SELECT table_name FROM information_schema.tables WHERE table_schema = :dbname";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam('dbname', $this->db_name);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            exit('Fatal Error: ' . $e->getMessage());
        }
    }

    public function listTableColumns(string $table) {
        try {
            $query = "SELECT column_name FROM information_schema.columns WHERE table_schema = :dbname AND table_name = :tblname";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam('dbname', $this->db_name);
            $stmt->bindParam('tblname', $table);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            exit('Fatal Error: ' . $e->getMessage());
        }
    }

    public function listTableRows(string $table) {
        try {
            $query = "SELECT * FROM " . $table;
            $query = $this->pdo->query($query);
            return $query->fetchAll(PDO::FETCH_NUM);
        } catch (PDOException $e) {
            exit('Fatal Error: ' . $e->getMessage());
        }
    }


    public function createBackup(string $db_name) {
        $backup = array();
        $db = $this->connectDB($db_name);
        $tables = $db->listTables();
        foreach ($tables as $i => $tbl) {
            $columns = $this->listTableColumns($tbl);
            $backup[0] = $columns;

            $rows = $this->listTableRows($tbl);
            $backup[1] = $rows;

            $fname = rtrim($this->backup_dir, '/') . '/' . $tbl . '.backup';
            $file = fopen($fname, 'w+');
            fwrite($file, json_encode($backup));
            fclose($file);
        }

        return true;
    }

    public function pushBackup(string $dbname) {
        $this->connectDB($dbname);
        $backup = array();
        $bakFiles = glob(rtrim($this->backup_dir, '/') . '/' . '*' . '.backup');
        foreach ($bakFiles as $fname) {
            $file = file_get_contents($fname);
            $table = pathinfo($fname, PATHINFO_FILENAME);
            $backup = json_decode($file);
            try {
                $query1 = "INSERT INTO " . $table . ' (' . implode(',', $backup[0]) . ') VALUES (' . rtrim(str_repeat('?,', count($backup[0])), ',') . ')'; 
                $stmt = $this->pdo->prepare($query1);
                foreach ($backup[1] as $data) {
                    $stmt->execute($data);
                }
            } catch (PDOException $e) {
                exit('Fatal Error: ' . $e->getMessage());
            }
        }

        return true;
    }

}


<?php
/**
 * Database.php
 * Wrapper PDO. SEMUA query menggunakan Prepared Statements (anti SQL Injection)
 * -> memenuhi syarat "Keamanan Fundamental: Prepared Statements".
 */
class Database
{
    private $pdo;
    private $stmt;

    public function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Koneksi database gagal: ' . $e->getMessage());
        }
    }

    // Siapkan query
    public function query($sql)
    {
        $this->stmt = $this->pdo->prepare($sql);
        return $this;
    }

    // Bind parameter (otomatis tentukan tipe)
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):  $type = PDO::PARAM_INT;  break;
                case is_bool($value): $type = PDO::PARAM_BOOL; break;
                case is_null($value): $type = PDO::PARAM_NULL; break;
                default:              $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    public function execute()  { return $this->stmt->execute(); }
    public function single()   { $this->execute(); return $this->stmt->fetch(); }
    public function resultSet(){ $this->execute(); return $this->stmt->fetchAll(); }
    public function rowCount() { return $this->stmt->rowCount(); }
    public function lastId()   { return $this->pdo->lastInsertId(); }
}

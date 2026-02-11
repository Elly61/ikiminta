<?php
/**
 * Database Connection Class
 * Handles PDO connection to MySQL database
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $config;

    private function __construct() {
        $db_config = require_once __DIR__ . "/database.php";
        $this->config = $db_config;
        $this->connect();
    }

    /**
     * Get database instance (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to database
     */
    private function connect() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config["host"],
                $this->config["port"],
                $this->config["database"],
                $this->config["charset"]
            );

            $this->pdo = new PDO(
                $dsn,
                $this->config["username"],
                $this->config["password"],
                $this->config["options"]
            );

        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Check error logs.");
        }
    }

    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->pdo;
    }

    /**
     * Execute SELECT query
     */
    public function select($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("SELECT Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute SELECT query (single row)
     */
    public function selectOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("SELECT ONE Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute INSERT query
     */
    public function insert($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("INSERT Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute UPDATE query
     */
    public function update($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("UPDATE Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute DELETE query
     */
    public function delete($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("DELETE Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute raw query
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Start transaction
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->pdo->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->pdo->rollBack();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }

    /**
     * Close connection
     */
    public function close() {
        $this->pdo = null;
    }
}
?>
<?php
/**
 * Database Connection Class
 * Handles PDO connection to MySQL or PostgreSQL database
 * Supports Render's DATABASE_URL and individual env vars
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $config;
    private $driver = 'mysql'; // 'mysql' or 'pgsql'

    private function __construct() {
        // Check for Render's DATABASE_URL first (PostgreSQL)
        $databaseUrl = getenv('DATABASE_URL');
        
        if ($databaseUrl && $databaseUrl !== '') {
            // Parse DATABASE_URL (format: postgres://user:pass@host:port/dbname)
            $parsed = parse_url($databaseUrl);
            $this->driver = 'pgsql';
            $this->config = [
                "host" => $parsed['host'] ?? 'localhost',
                "username" => $parsed['user'] ?? 'root',
                "password" => $parsed['pass'] ?? '',
                "database" => ltrim($parsed['path'] ?? '/ikiminta', '/'),
                "port" => (int)($parsed['port'] ?? 5432),
                "charset" => "utf8",
                "options" => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            ];
        } else {
            // Load from individual environment variables (MySQL/local)
            $env = function($key, $default = null) {
                $val = getenv($key);
                return $val !== false && $val !== '' ? $val : $default;
            };

            $this->driver = $env("IK_DB_DRIVER", "mysql");
            $this->config = [
                "host" => $env("IK_DB_HOST", "localhost"),
                "username" => $env("IK_DB_USER", "root"),
                "password" => $env("IK_DB_PASS", ""),
                "database" => $env("IK_DB_NAME", "ikiminta"),
                "port" => (int)$env("IK_DB_PORT", $this->driver === 'pgsql' ? 5432 : 3306),
                "charset" => $env("IK_DB_CHARSET", "utf8"),
                "collation" => $env("IK_DB_COLLATION", "utf8_unicode_ci"),
                "prefix" => $env("IK_DB_PREFIX", ""),
                "options" => [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            ];
        }
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
     * Get the database driver being used
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * Connect to database
     */
    private function connect() {
        try {
            if ($this->driver === 'pgsql') {
                $dsn = sprintf(
                    "pgsql:host=%s;port=%s;dbname=%s",
                    $this->config["host"],
                    $this->config["port"],
                    $this->config["database"]
                );
            } else {
                $dsn = sprintf(
                    "mysql:host=%s;port=%s;dbname=%s;charset=%s",
                    $this->config["host"],
                    $this->config["port"],
                    $this->config["database"],
                    $this->config["charset"]
                );
            }

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
    public function insert($table, $data) {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array_values($data));
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("INSERT Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute UPDATE query
     */
    public function update($table, $data, $where = '', $whereParams = []) {
        try {
            $setParts = [];
            foreach (array_keys($data) as $column) {
                $setParts[] = "$column = ?";
            }
            $setClause = implode(', ', $setParts);
            $sql = "UPDATE $table SET $setClause";
            if (!empty($where)) {
                $sql .= " WHERE $where";
            }
            $stmt = $this->pdo->prepare($sql);
            $params = array_merge(array_values($data), $whereParams);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("UPDATE Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Execute DELETE query
     */
    public function delete($table, $where = '', $whereParams = []) {
        try {
            $sql = "DELETE FROM $table";
            if (!empty($where)) {
                $sql .= " WHERE $where";
            }
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($whereParams);
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
     * Count records
     */
    public function count($table, $where = '', $params = []) {
        try {
            $sql = "SELECT COUNT(*) as count FROM $table";
            if (!empty($where)) {
                $sql .= " WHERE $where";
            }
            $result = $this->selectOne($sql, $params);
            return $result ? (int)$result['count'] : 0;
        } catch (PDOException $e) {
            error_log("COUNT Error: " . $e->getMessage());
            return 0;
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
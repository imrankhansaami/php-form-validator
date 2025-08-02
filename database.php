<?php
require_once 'config.php';

/**
 * Database Handler Class
 * Handles database connections and operations
 */
class Database {
    private $connection;
    private static $instance = null;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get database instance (singleton pattern)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Create the users table if it doesn't exist
     */
    public function createUsersTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                phone VARCHAR(20),
                password_hash VARCHAR(255) NOT NULL,
                age INT NOT NULL,
                country VARCHAR(10) NOT NULL,
                message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                ip_address VARCHAR(45),
                INDEX idx_email (email),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        try {
            $this->connection->exec($sql);
            return true;
        } catch (PDOException $e) {
            error_log("Failed to create users table: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Insert user data into database
     */
    public function insertUser($data, $password) {
        $sql = "
            INSERT INTO users (name, email, phone, password_hash, age, country, message, ip_address)
            VALUES (:name, :email, :phone, :password_hash, :age, :country, :message, :ip_address)
        ";
        
        try {
            $stmt = $this->connection->prepare($sql);
            
            $result = $stmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'] ?: null,
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':age' => (int)$data['age'],
                ':country' => $data['country'],
                ':message' => $data['message'] ?: null,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);
            
            return $result ? $this->connection->lastInsertId() : false;
            
        } catch (PDOException $e) {
            // Check if it's a duplicate email error
            if ($e->getCode() == 23000) {
                throw new Exception("Email address already exists");
            }
            
            error_log("Database insert error: " . $e->getMessage());
            throw new Exception("Failed to save user data");
        }
    }
    
    /**
     * Check if email already exists
     */
    public function emailExists($email) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            error_log("Email check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users with pagination
     */
    public function getUsers($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT id, name, email, phone, age, country, created_at 
            FROM users 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ";
        
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get users error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total user count
     */
    public function getUserCount() {
        $sql = "SELECT COUNT(*) FROM users";
        
        try {
            $stmt = $this->connection->query($sql);
            return $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            error_log("Get user count error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update user data
     */
    public function updateUser($id, $data) {
        $sql = "
            UPDATE users 
            SET name = :name, email = :email, phone = :phone, 
                age = :age, country = :country, message = :message
            WHERE id = :id
        ";
        
        try {
            $stmt = $this->connection->prepare($sql);
            
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':phone' => $data['phone'] ?: null,
                ':age' => (int)$data['age'],
                ':country' => $data['country'],
                ':message' => $data['message'] ?: null
            ]);
            
        } catch (PDOException $e) {
            error_log("Update user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete user
     */
    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([':id' => $id]);
            
        } catch (PDOException $e) {
            error_log("Delete user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log activity to database
     */
    public function logActivity($message, $identifier = '', $level = 'info') {
        // Create activity_logs table if it doesn't exist
        $this->createActivityLogsTable();
        
        $sql = "
            INSERT INTO activity_logs (message, identifier, level, ip_address, user_agent)
            VALUES (:message, :identifier, :level, :ip_address, :user_agent)
        ";
        
        try {
            $stmt = $this->connection->prepare($sql);
            
            return $stmt->execute([
                ':message' => $message,
                ':identifier' => $identifier,
                ':level' => $level,
                ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
            
        } catch (PDOException $e) {
            error_log("Log activity error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create activity logs table
     */
    private function createActivityLogsTable() {
        $sql = "
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                message TEXT NOT NULL,
                identifier VARCHAR(255),
                level ENUM('info', 'warning', 'error') DEFAULT 'info',
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_created_at (created_at),
                INDEX idx_level (level)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        try {
            $this->connection->exec($sql);
        } catch (PDOException $e) {
            error_log("Failed to create activity_logs table: " . $e->getMessage());
        }
    }
    
    /**
     * Close database connection
     */
    public function close() {
        $this->connection = null;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {}
}
?>
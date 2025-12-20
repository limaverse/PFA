<?php
// C:\xampp\htdocs\PFA\transpori\dashuser\database.php
session_start();

class Database {
    private $host = 'localhost';
    private $dbname = 'transpori';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}

$db = new Database();
$conn = $db->connect();
// IMPORTANT: Only create demo user if we're in dashboard AND user is not logged in
// AND this is specifically for dashboard access
$is_dashboard = strpos($_SERVER['PHP_SELF'], 'dashuser') !== false;

if ($is_dashboard && !isset($_SESSION['user_id'])) {
    // Check if default demo member exists
    $stmt = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM members WHERE email = ?");
    $stmt->execute(['alice@example.com']);
    $member = $stmt->fetch();
    
    if ($member) {
        // Use existing demo member
        $_SESSION['user_id'] = $member['id'];
        $_SESSION['user_name'] = $member['name'];
        $_SESSION['user_email'] = 'alice@example.com';
        $_SESSION['is_demo_user'] = true; // Mark as demo user
    } else {
        // Create a new demo member
        try {
            $stmt = $conn->prepare("
                INSERT INTO members (email, password_hash, first_name, last_name, is_verified, is_active) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            // Default password: password123 (hashed)
            $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
            $stmt->execute([
                'alice@example.com',
                $hashed_password,
                'Alice',
                'Johnson',
                true,
                true
            ]);
            
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['user_name'] = 'Alice Johnson';
            $_SESSION['user_email'] = 'alice@example.com';
            $_SESSION['is_demo_user'] = true; // Mark as demo user
            
        } catch (PDOException $e) {
            // If insert fails, try to get any existing member
            $stmt = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) as name, email FROM members LIMIT 1");
            $member = $stmt->fetch();
            
            if ($member) {
                $_SESSION['user_id'] = $member['id'];
                $_SESSION['user_name'] = $member['name'];
                $_SESSION['user_email'] = $member['email'];
                $_SESSION['is_demo_user'] = true;
            } else {
                // Last resort: disable foreign key checks for this session
                $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
                $_SESSION['user_id'] = 1;
                $_SESSION['user_name'] = 'Demo User';
                $_SESSION['user_email'] = 'demo@example.com';
                $_SESSION['is_demo_user'] = true;
            }
        }
    }
}

// Function to ensure member exists (call before any INSERT)
function ensureMemberExists($conn, $member_id) {
    $stmt = $conn->prepare("SELECT id FROM members WHERE id = ?");
    $stmt->execute([$member_id]);
    
    if ($stmt->rowCount() == 0) {
        // Create a dummy member if doesn't exist
        $stmt = $conn->prepare("
            INSERT INTO members (id, email, password_hash, first_name, last_name, is_verified, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE id = id
        ");
        
        $hashed_password = password_hash('temp123', PASSWORD_DEFAULT);
        $stmt->execute([
            $member_id,
            'user' . $member_id . '@example.com',
            $hashed_password,
            'User',
            $member_id,
            true,
            true
        ]);
    }
}
?>
<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);


// echo json_encode(['test' => 'PHP is working', 'timestamp' => date('Y-m-d H:i:s')]);
// exit;

// Comment out everything else for now to test basic PHP functionality

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Cache-busting headers
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    require_once 'config/database.php';
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database config error: ' . $e->getMessage()]);
    exit;
}

class AuthAPI {
    private $conn;

    public function __construct() {
        try {
            $database = new Database();
            $this->conn = $database->getConnection();
        } catch (Exception $e) {
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public function testConnection() {
        try {
            // Test database connection
            $query = "SELECT COUNT(*) as count FROM admin_users";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return [
                'success' => true, 
                'message' => 'Database connection successful',
                'user_count' => $result['count']
            ];
        } catch (Exception $e) {
            return [
                'success' => false, 
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT id, username, password_hash, full_name, role, is_active 
                     FROM admin_users 
                     WHERE username = :username AND is_active = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch();

                if ($password == $user['password_hash']) {
                    // Create session
                    $session_id = bin2hex(random_bytes(32));
                    $expires_at = date('Y-m-d H:i:s', time() + 7200); // 2 hours
                    
                    $session_query = "INSERT INTO admin_sessions (id, user_id, ip_address, user_agent, expires_at) 
                                    VALUES (:session_id, :user_id, :ip_address, :user_agent, :expires_at)";
                    
                    $session_stmt = $this->conn->prepare($session_query);
                    $session_stmt->bindParam(':session_id', $session_id);
                    $session_stmt->bindParam(':user_id', $user['id']);
                    $session_stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
                    $session_stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
                    $session_stmt->bindParam(':expires_at', $expires_at);
                    $session_stmt->execute();

                    // Update last login
                    $update_query = "UPDATE admin_users SET last_login = NOW() WHERE id = :user_id";
                    $update_stmt = $this->conn->prepare($update_query);
                    $update_stmt->bindParam(':user_id', $user['id']);
                    $update_stmt->execute();

                    return [
                        'success' => true,
                        'session_id' => $session_id,
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'full_name' => $user['full_name'],
                            'role' => $user['role']
                        ]
                    ];
                }
            }
            
            return ['success' => false, 'message' => 'Invalid credentials'];
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }

    public function validateSession($session_id) {
        try {
            $query = "SELECT s.user_id, u.username, u.full_name, u.role 
                     FROM admin_sessions s 
                     JOIN admin_users u ON s.user_id = u.id 
                     WHERE s.id = :session_id AND s.expires_at > NOW() AND u.is_active = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                // Update last activity
                $update_query = "UPDATE admin_sessions SET last_activity = NOW() WHERE id = :session_id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(':session_id', $session_id);
                $update_stmt->execute();

                return ['success' => true, 'user' => $stmt->fetch()];
            }
            
            return ['success' => false, 'message' => 'Invalid session'];
        } catch (Exception $e) {
            error_log("Session validation error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Session validation failed'];
        }
    }

    public function logout($session_id) {
        try {
            $query = "DELETE FROM admin_sessions WHERE id = :session_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':session_id', $session_id);
            $stmt->execute();

            return ['success' => true, 'message' => 'Logged out successfully'];
        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Logout failed'];
        }
    }
}

// Handle requests
try {
    $auth = new AuthAPI();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['action'])) {
            switch ($input['action']) {
                case 'test':
                    echo json_encode($auth->testConnection());
                    break;
                case 'login':
                    echo json_encode($auth->login($input['username'], $input['password']));
                    break;
                case 'validate':
                    echo json_encode($auth->validateSession($input['session_id']));
                    break;
                case 'logout':
                    echo json_encode($auth->logout($input['session_id']));
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No action specified']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}


?>
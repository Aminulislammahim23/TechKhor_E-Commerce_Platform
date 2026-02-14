<?php
class LoginController {
    private $connection;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    /**
     * Handle user login authentication
     * @return array Contains success status, errors, and user data
     */
    public function handleLogin() {
        $errors = [];
        
        // Get form data
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validate input
        $errors = array_merge($errors, $this->validateInput($email, $password));
        
        // If validation passes, authenticate user
        if (empty($errors)) {
            $errors = array_merge($errors, $this->authenticateUser($email, $password, $remember));
        }
        
        return [
            'success' => empty($errors),
            'errors' => $errors,
            'formData' => ['email' => $email]
        ];
    }
    
    /**
     * Validate login input data
     * @param string $email
     * @param string $password
     * @return array Validation errors
     */
    private function validateInput($email, $password) {
        $errors = [];
        
        // Email validation
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } elseif (strlen($email) > 100) {
            $errors[] = "Email is too long";
        }
        
        // Password validation
        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) > 128) {
            $errors[] = "Password is too long";
        }
        
        return $errors;
    }
    
    /**
     * Authenticate user credentials
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return array Authentication errors
     */
    private function authenticateUser($email, $password, $remember) {
        $errors = [];
        
        // Prepare and execute query
        $stmt = $this->connection->prepare("SELECT user_id, name, email, role, password, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check account status
            $errors = array_merge($errors, $this->validateAccountStatus($user));
            
            // Verify password if account is active
            if (empty($errors) && password_verify($password, $user['password'])) {
                // Login successful - set session and handle remember me
                $this->processSuccessfulLogin($user, $remember);
            } elseif (empty($errors)) {
                // Password incorrect
                $errors[] = "Invalid email or password";
                // Log failed attempt
                $this->logFailedLoginAttempt($email);
            }
        } else {
            // No user found
            $errors[] = "Invalid email or password";
            $this->logFailedLoginAttempt($email);
        }
        
        $stmt->close();
        return $errors;
    }
    
    /**
     * Validate user account status
     * @param array $user
     * @return array Status validation errors
     */
    private function validateAccountStatus($user) {
        $errors = [];
        
        switch ($user['status']) {
            case 'inactive':
                $errors[] = "Your account is not active. Please contact administrator.";
                break;
            case 'blocked':
                $errors[] = "Your account has been blocked. Please contact administrator.";
                break;
            case 'active':
                // Account is active, no errors
                break;
            default:
                $errors[] = "Account status unknown. Please contact administrator.";
                break;
        }
        
        return $errors;
    }
    
    /**
     * Process successful login
     * @param array $user
     * @param bool $remember
     */
    private function processSuccessfulLogin($user, $remember) {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user'] = [
            'user_id' => $user['user_id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        // Handle "Remember Me" functionality (simplified for now)
        // Skip remember me functionality since required columns don't exist
        if ($remember) {
            // Log that remember me was requested but not implemented
            error_log("Remember me requested for user ID: " . $user['user_id'] . " but not implemented due to missing database columns");
        }
        
        // Log successful login
        $this->logSuccessfulLogin($user['user_id'], $user['email']);
        
        // Update last login timestamp (skip for now)
        // $this->updateLastLogin($user['user_id']);
    }
    
    /**
     * Set remember me cookie
     * @param int $userId
     */
    private function setRememberMeCookie($userId) {
        // Skip implementation due to missing database columns
        /*
        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        
        // Store token in database
        $this->storeRememberToken($userId, $hashedToken);
        
        // Set cookie (expires in 30 days)
        setcookie('remember_token', $userId . ':' . $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
        */
    }
    
    /**
     * Store remember me token in database
     * @param int $userId
     * @param string $hashedToken
     */
    private function storeRememberToken($userId, $hashedToken) {
        $stmt = $this->connection->prepare("UPDATE users SET remember_token = ?, remember_token_expires = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE user_id = ?");
        $stmt->bind_param("si", $hashedToken, $userId);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Log successful login attempt
     * @param int $userId
     * @param string $email
     */
    private function logSuccessfulLogin($userId, $email) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        error_log("Successful login: User ID={$userId}, Email={$email}, IP={$ipAddress}");
        
        // Store in login logs table (if exists)
        $this->storeLoginLog($userId, 'success', $ipAddress, $userAgent);
    }
    
    /**
     * Log failed login attempt
     * @param string $email
     */
    private function logFailedLoginAttempt($email) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        error_log("Failed login attempt: Email={$email}, IP={$ipAddress}");
        
        // Store in login logs table (if exists)
        $this->storeLoginLog(null, 'failed', $ipAddress, $userAgent, $email);
    }
    
    /**
     * Store login attempt in logs
     * @param int|null $userId
     * @param string $status
     * @param string $ipAddress
     * @param string $userAgent
     * @param string|null $email
     */
    private function storeLoginLog($userId, $status, $ipAddress, $userAgent, $email = null) {
        // Check if login_logs table exists
        $tableExists = $this->connection->query("SHOW TABLES LIKE 'login_logs'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $this->connection->prepare("INSERT INTO login_logs (user_id, email, status, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("issss", $userId, $email, $status, $ipAddress, $userAgent);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    /**
     * Update user's last login timestamp
     * @param int $userId
     */
    private function updateLastLogin($userId) {
        $stmt = $this->connection->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Handle logout functionality
     */
    public function handleLogout() {
        // Clear remember me cookie if exists
        if (isset($_COOKIE['remember_token'])) {
            $this->clearRememberToken();
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        // Destroy session
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Redirect to login page
        $this->redirectAfterLogout();
    }
    
    /**
     * Clear remember me token from database
     */
    private function clearRememberToken() {
        if (isset($_COOKIE['remember_token'])) {
            $tokenParts = explode(':', $_COOKIE['remember_token']);
            if (count($tokenParts) === 2) {
                $userId = $tokenParts[0];
                $stmt = $this->connection->prepare("UPDATE users SET remember_token = NULL, remember_token_expires = NULL WHERE user_id = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    
    /**
     * Redirect after logout
     */
    private function redirectAfterLogout() {
        header('Location: ?page=login');
        exit();
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn() {
        // Check session
        if (isset($_SESSION['user_id']) && isset($_SESSION['user'])) {
            return true;
        }
        
        // Check remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            return $this->validateRememberToken($_COOKIE['remember_token']);
        }
        
        return false;
    }
    
    /**
     * Validate remember me token
     * @param string $token
     * @return bool
     */
    private function validateRememberToken($token) {
        $tokenParts = explode(':', $token);
        if (count($tokenParts) !== 2) {
            return false;
        }
        
        $userId = $tokenParts[0];
        $tokenValue = $tokenParts[1];
        
        $stmt = $this->connection->prepare("SELECT user_id, name, email, role, remember_token, remember_token_expires FROM users WHERE user_id = ? AND remember_token_expires > NOW()");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($tokenValue, $user['remember_token'])) {
                // Re-create session
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                
                $stmt->close();
                return true;
            }
        }
        
        $stmt->close();
        return false;
    }
    
    /**
     * Check if user has specific role
     * @param string $requiredRole
     * @return bool
     */
    public function hasRole($requiredRole) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $requiredRole;
    }
    
    /**
     * Get current user data
     * @return array|null
     */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return $_SESSION['user'];
        }
        return null;
    }
    
    /**
     * Redirect user based on their role
     */
    public function redirectByRole() {
        if (!$this->isLoggedIn()) {
            $this->redirectAfterLogout();
            return;
        }
        
        $user = $this->getCurrentUser();
        if ($user && $user['role'] === 'admin') {
            header('Location: ?page=admin');
        } else {
            header('Location: ?page=home');
        }
        exit();
    }
    
    /**
     * Check for brute force attempts
     * @param string $email
     * @return bool
     */
    public function isBruteForceAttempt($email) {
        // Check if login_logs table exists
        $tableExists = $this->connection->query("SHOW TABLES LIKE 'login_logs'");
        if (!$tableExists || $tableExists->num_rows === 0) {
            return false;
        }
        
        // Check for 5 failed attempts in last 15 minutes
        $stmt = $this->connection->prepare("SELECT COUNT(*) as attempt_count FROM login_logs WHERE email = ? AND status = 'failed' AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['attempt_count'] >= 5;
    }
}
?>
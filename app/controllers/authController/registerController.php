<?php
class RegisterController {
    private $connection;
    
    public function __construct($connection) {
        $this->connection = $connection;
    }
    
    public function handleRegistration() {
        $errors = [];
        
        // Get form data
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $newsletter = isset($_POST['newsletter']);
        $terms = isset($_POST['terms']);
        
        // Validate input data
        $errors = array_merge($errors, $this->validateInput($firstName, $lastName, $email, $password, $confirmPassword, $phone, $terms));
        
        // If validation passes, process registration
        if (empty($errors)) {
            $errors = array_merge($errors, $this->processRegistration($firstName, $lastName, $email, $password, $phone, $newsletter));
        }
        
        return [
            'success' => empty($errors),
            'errors' => $errors,
            'formData' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'phone' => $phone
            ]
        ];
    }
    
    private function validateInput($firstName, $lastName, $email, $password, $confirmPassword, $phone, $terms) {
        $errors = [];
        
        // Required field validation
        if (empty($firstName)) {
            $errors[] = "First name is required";
        } elseif (strlen($firstName) > 100) {
            $errors[] = "First name is too long (maximum 100 characters)";
        } elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $firstName)) {
            $errors[] = "First name contains invalid characters";
        }
        
        if (empty($lastName)) {
            $errors[] = "Last name is required";
        } elseif (strlen($lastName) > 100) {
            $errors[] = "Last name is too long (maximum 100 characters)";
        } elseif (!preg_match('/^[a-zA-Z\s\-\'\.]+$/', $lastName)) {
            $errors[] = "Last name contains invalid characters";
        }
        
        if (empty($email)) {
            $errors[] = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format";
        } elseif (strlen($email) > 100) {
            $errors[] = "Email is too long (maximum 100 characters)";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        } elseif (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        } elseif (strlen($password) > 128) {
            $errors[] = "Password is too long (maximum 128 characters)";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character";
        }
        
        if (empty($confirmPassword)) {
            $errors[] = "Please confirm your password";
        } elseif ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match";
        }
        
        if (!empty($phone)) {
            // Remove spaces and common formatting characters
            $cleanPhone = preg_replace('/[\s\-\(\)]+/', '', $phone);
            if (!preg_match('/^[\d\+\(\)\s\-]{10,20}$/', $phone)) {
                $errors[] = "Invalid phone number format (10-20 digits)";
            } elseif (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 15) {
                $errors[] = "Phone number must be between 10 and 15 digits";
            }
        }
        
        if (!$terms) {
            $errors[] = "You must agree to the Terms & Conditions";
        }
        
        // Additional security validations
        $errors = array_merge($errors, $this->validateSecurity($firstName, $lastName, $email, $password));
        
        return $errors;
    }
    
    private function validateSecurity($firstName, $lastName, $email, $password) {
        $errors = [];
        
        // Check for common password patterns
        $commonPasswords = ['password', '123456', 'qwerty', 'admin123', 'welcome'];
        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = "Please choose a stronger password";
        }
        
        // Check if password is too similar to email/username
        $nameParts = explode(' ', strtolower($firstName . ' ' . $lastName));
        foreach ($nameParts as $part) {
            if (strlen($part) > 2 && stripos($password, $part) !== false) {
                $errors[] = "Password should not contain your name";
            }
        }
        
        if (stripos($password, strtolower($email)) !== false) {
            $errors[] = "Password should not contain your email";
        }
        
        return $errors;
    }
    
    private function processRegistration($firstName, $lastName, $email, $password, $phone, $newsletter) {
        $errors = [];
        
        // Check if email already exists
        $stmt = $this->connection->prepare("SELECT user_id, status FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $existingUser = $result->fetch_assoc();
            if ($existingUser['status'] === 'active') {
                $errors[] = "Email address is already registered";
            } elseif ($existingUser['status'] === 'inactive') {
                $errors[] = "This email was previously registered but the account is inactive. Please contact support.";
            } elseif ($existingUser['status'] === 'blocked') {
                $errors[] = "This email address has been blocked. Please contact support.";
            }
        } else {
            // Rate limiting - check recent registrations from same IP
            $errors = array_merge($errors, $this->checkRateLimit());
            
            if (empty($errors)) {
                // Insert new user
                $errors = array_merge($errors, $this->createUser($firstName, $lastName, $email, $password, $phone, $newsletter));
            }
        }
        
        $stmt->close();
        return $errors;
    }
    
    private function checkRateLimit() {
        $errors = [];
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $timeLimit = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM users WHERE created_at > ? AND SUBSTRING_INDEX(password, ':', 1) = ?");
        $stmt->bind_param("ss", $timeLimit, $ipAddress);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 5) {
            $errors[] = "Too many registration attempts. Please try again later.";
        }
        
        $stmt->close();
        return $errors;
    }
    
    private function createUser($firstName, $lastName, $email, $password, $phone, $newsletter) {
        $errors = [];
        
        // Insert new user (default role is 'customer')
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $name = $firstName . ' ' . $lastName;
        $role = 'customer';
        $status = 'active';
        $avatar = 'default.png';
        
        // Add IP address to password hash for rate limiting (not for security)
        $passwordWithIP = $hashedPassword . ':' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        
        $stmt = $this->connection->prepare("INSERT INTO users (name, email, phone, password, role, status, avatar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $phone, $passwordWithIP, $role, $status, $avatar);
        
        if ($stmt->execute()) {
            // Get the newly created user ID
            $userId = $this->connection->insert_id;
            
            // Start session and log in the user automatically
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['user'] = [
                'user_id' => $userId,
                'name' => $name,
                'email' => $email,
                'role' => $role
            ];
            
            // Log registration activity
            error_log("New user registered: ID={$userId}, Email={$email}, IP=" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            
        } else {
            $errors[] = "Registration failed. Please try again. Error: " . $stmt->error;
        }
        
        $stmt->close();
        return $errors;
    }
    
    public function redirectAfterSuccess() {
        header('Location: ?page=home&registration=success');
        exit();
    }
    
    public function redirectAfterFailure() {
        header('Location: ?page=register');
        exit();
    }
}
?>
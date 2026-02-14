<?php
class UserModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    /**
     * Create a new user
     */
    public function createUser($name, $email, $phone, $password, $role = 'customer') {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $phone, $hashedPassword, $role);

        if ($stmt->execute()) {
            return $this->connection->insert_id;
        }
        
        return false;
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        $sql = "SELECT user_id, name, email, phone, role, avatar, status, created_at FROM users WHERE user_id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $sql = "SELECT user_id, name, email, phone, role, password, avatar, status, created_at FROM users WHERE email = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    /**
     * Update user information
     */
    public function updateUser($userId, $name = null, $email = null, $phone = null, $avatar = null) {
        $fields = [];
        $params = [];
        $paramTypes = '';

        if ($name !== null) {
            $fields[] = "name = ?";
            $params[] = $name;
            $paramTypes .= 's';
        }

        if ($email !== null) {
            $fields[] = "email = ?";
            $params[] = $email;
            $paramTypes .= 's';
        }

        if ($phone !== null) {
            $fields[] = "phone = ?";
            $params[] = $phone;
            $paramTypes .= 's';
        }

        if ($avatar !== null) {
            $fields[] = "avatar = ?";
            $params[] = $avatar;
            $paramTypes .= 's';
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $userId;
        $paramTypes .= 'i';

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param($paramTypes, ...$params);

        return $stmt->execute();
    }

    /**
     * Update user password
     */
    public function updateUserPassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $userId);

        return $stmt->execute();
    }

    /**
     * Update user status
     */
    public function updateUserStatus($userId, $status) {
        $validStatuses = ['active', 'inactive', 'blocked'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $sql = "UPDATE users SET status = ? WHERE user_id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("si", $status, $userId);

        return $stmt->execute();
    }

    /**
     * Update user role
     */
    public function updateUserRole($userId, $role) {
        $validRoles = ['admin', 'employee', 'customer'];
        if (!in_array($role, $validRoles)) {
            return false;
        }

        $sql = "UPDATE users SET role = ? WHERE user_id = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("si", $role, $userId);

        return $stmt->execute();
    }

    /**
     * Get all users with pagination
     */
    public function getAllUsers($limit = 10, $offset = 0) {
        $sql = "SELECT user_id, name, email, phone, role, avatar, status, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get users count by role
     */
    public function getUsersCountByRole($role = null) {
        if ($role) {
            $sql = "SELECT COUNT(*) as count FROM users WHERE role = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("s", $role);
        } else {
            $sql = "SELECT COUNT(*) as count FROM users";
            $stmt = $this->connection->prepare($sql);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'];
    }

    /**
     * Get users count by status
     */
    public function getUsersCountByStatus($status) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE status = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'];
    }

    /**
     * Delete user (soft delete by setting status to inactive)
     */
    public function deleteUser($userId) {
        return $this->updateUserStatus($userId, 'inactive');
    }

    /**
     * Authenticate user
     */
    public function authenticateUser($email, $password) {
        $user = $this->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Return user data without password
            unset($user['password']);
            return $user;
        }

        return false;
    }

    /**
     * Check if email already exists (excluding current user)
     */
    public function emailExists($email, $excludeUserId = null) {
        if ($excludeUserId) {
            $sql = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("si", $email, $excludeUserId);
        } else {
            $sql = "SELECT user_id FROM users WHERE email = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->bind_param("s", $email);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
}
?>
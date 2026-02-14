<?php
// Profile Controller - Handle admin profile updates

class ProfileController {
    
    /**
     * Update admin profile including avatar upload
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }
        
        $userId = $_SESSION['user']['user_id'] ?? 0;
        
        if (!$userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $response = ['success' => true, 'message' => ''];
        
        // Handle avatar upload
        $avatarPath = $this->handleAvatarUpload($userId);
        
        // Update profile in database
        $result = $this->saveProfileToDatabase($userId, $avatarPath);
        
        return $result;
    }
    
    /**
     * Handle avatar file upload
     */
    private function handleAvatarUpload($userId) {
        if (!isset($_FILES['admin_avatar']) || $_FILES['admin_avatar']['error'] !== UPLOAD_ERR_OK) {
            return null; // No new avatar uploaded
        }
        
        $file = $_FILES['admin_avatar'];
        
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, WEBP allowed.'];
        }
        
        // Validate file size (2MB max)
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'message' => 'File size must be less than 2MB.'];
        }
        
        // Create avatars directory if not exists
        $uploadDir = './app/assets/images/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Delete old avatar if exists and not default
            $this->deleteOldAvatar($userId);
            return $newFilename;
        }
        
        return null;
    }
    
    /**
     * Delete old avatar file
     */
    private function deleteOldAvatar($userId) {
        include './app/models/db.php';
        $con = getConnection();
        
        $stmt = mysqli_prepare($con, "SELECT avatar FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $oldAvatar = $row['avatar'];
            if (!empty($oldAvatar) && $oldAvatar !== 'default.png') {
                $oldPath = './app/assets/images/avatars/' . $oldAvatar;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
        }
        
        mysqli_stmt_close($stmt);
        mysqli_close($con);
    }
    
    /**
     * Save profile data to database
     */
    private function saveProfileToDatabase($userId, $avatarFilename = null) {
        include './app/models/db.php';
        $con = getConnection();
        
        // Get current user data first
        $fetchStmt = mysqli_prepare($con, "SELECT name, email, phone, avatar FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($fetchStmt, "i", $userId);
        mysqli_stmt_execute($fetchStmt);
        $fetchResult = mysqli_stmt_get_result($fetchStmt);
        
        if (!$row = mysqli_fetch_assoc($fetchResult)) {
            mysqli_stmt_close($fetchStmt);
            mysqli_close($con);
            return ['success' => false, 'message' => 'User not found'];
        }
        
        // Store current values
        $currentName = $row['name'];
        $currentEmail = $row['email'];
        $currentPhone = $row['phone'];
        $currentAvatar = $row['avatar'];
        mysqli_stmt_close($fetchStmt);
        
        // Get new values from form
        $newName = trim($_POST['name'] ?? '');
        $newEmail = trim($_POST['email'] ?? '');
        $newPhone = trim($_POST['phone'] ?? '');
        
        // Use new values if provided, otherwise keep current values
        $name = !empty($newName) ? $newName : $currentName;
        $email = !empty($newEmail) ? $newEmail : $currentEmail;
        $phone = $newPhone; // Allow empty phone to be cleared
        
        // Validate required fields
        if (empty($name)) {
            return ['success' => false, 'message' => 'Name is required'];
        }
        
        if (empty($email)) {
            return ['success' => false, 'message' => 'Email is required'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Check if email already exists for other users (only if email changed)
        if ($email !== $currentEmail) {
            $checkStmt = mysqli_prepare($con, "SELECT user_id FROM users WHERE email = ? AND user_id != ?");
            mysqli_stmt_bind_param($checkStmt, "si", $email, $userId);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);
            
            if (mysqli_num_rows($checkResult) > 0) {
                mysqli_stmt_close($checkStmt);
                mysqli_close($con);
                return ['success' => false, 'message' => 'Email already exists for another user'];
            }
            mysqli_stmt_close($checkStmt);
        }
        
        // Use new avatar if uploaded, otherwise keep current
        $finalAvatar = $avatarFilename ? $avatarFilename : $currentAvatar;
        
        // Build update query
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, avatar = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $phone, $finalAvatar, $userId);
        
        if (mysqli_stmt_execute($stmt)) {
            // Update session data with the saved values
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['avatar'] = $finalAvatar;
            
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            return ['success' => true, 'message' => 'Profile updated successfully!'];
        } else {
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            return ['success' => false, 'message' => 'Database error: ' . $error];
        }
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Invalid request method'];
        }
        
        $userId = $_SESSION['user']['user_id'] ?? 0;
        
        if (!$userId) {
            return ['success' => false, 'message' => 'User not logged in'];
        }
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate passwords
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'All password fields are required'];
        }
        
        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => 'New passwords do not match'];
        }
        
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
        }
        
        // Verify current password
        include './app/models/db.php';
        $con = getConnection();
        
        $stmt = mysqli_prepare($con, "SELECT password FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            return ['success' => false, 'message' => 'User not found'];
        }
        
        if (!password_verify($currentPassword, $row['password'])) {
            mysqli_stmt_close($stmt);
            mysqli_close($con);
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        mysqli_stmt_close($stmt);
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = mysqli_prepare($con, "UPDATE users SET password = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($updateStmt, "si", $hashedPassword, $userId);
        
        if (mysqli_stmt_execute($updateStmt)) {
            mysqli_stmt_close($updateStmt);
            mysqli_close($con);
            return ['success' => true, 'message' => 'Password changed successfully!'];
        } else {
            $error = mysqli_stmt_error($updateStmt);
            mysqli_stmt_close($updateStmt);
            mysqli_close($con);
            return ['success' => false, 'message' => 'Error changing password: ' . $error];
        }
    }
}

// Handle profile update request
if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    header('Content-Type: application/json');
    
    $profileController = new ProfileController();
    $result = $profileController->updateProfile();
    
    echo json_encode($result);
    exit;
}

// Handle password change request
if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
    header('Content-Type: application/json');
    
    $profileController = new ProfileController();
    $result = $profileController->changePassword();
    
    echo json_encode($result);
    exit;
}
?>
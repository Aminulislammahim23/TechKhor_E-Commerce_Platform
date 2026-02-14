<?php 
require_once './app/views/layouts/header.php';

// Initialize variables (will be set by PageController if there are errors)
$errors = $errors ?? [];
$formData = $formData ?? ['email' => ''];
?>

<div class="auth-wrapper">
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-logo">🔐</div>
            <h1>Welcome Back</h1>
            <p>Sign in to your TechKhor account</p>
        </div>
        
        <form class="auth-form" id="loginForm" method="post" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($formData['email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                    <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                </div>
            </div>
            
            <div class="form-options">
                <label class="checkbox-container">
                    <input type="checkbox" name="remember">
                    <span class="checkmark"></span>
                    Remember me
                </label>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>
            
            <button type="submit" class="btn-primary auth-btn">Sign In</button>
        </form>
        
        <?php if (!empty($errors)): ?>
            <div class="form-group" style="text-align: center;">
                <?php foreach ($errors as $error): ?>
                    <div class="error-message" style="color: #e74c3c; background-color: #fdeded; padding: 10px; border-radius: 4px; margin: 5px 0; border: 1px solid #f5c6cb;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="auth-divider">
            <span>or</span>
        </div>
        
        <div class="social-login">
            <button class="social-btn google-btn">
                <span class="social-icon">🔍</span>
                Continue with Google
            </button>
            <button class="social-btn facebook-btn">
                <span class="social-icon">f</span>
                Continue with Facebook
            </button>
        </div>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="?page=register">Create Account</a></p>
            <p class="help-link"><a href="#">Need help signing in?</a></p>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = field.nextElementSibling;
    if (field.type === 'password') {
        field.type = 'text';
        toggle.textContent = '🙈';
    } else {
        field.type = 'password';
        toggle.textContent = '👁️';
    }
}
</script>

</div>
</div>

<?php require_once './app/views/layouts/footer.php'; ?>
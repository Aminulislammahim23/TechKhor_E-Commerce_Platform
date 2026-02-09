<?php require_once './app/views/layouts/header.php'; ?>

<div class="auth-wrapper">
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand-logo">🚀</div>
            <h1>Create Account</h1>
            <p>Join TechKhor today and unlock amazing deals!</p>
        </div>
        
        <form class="auth-form" id="registerForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" required placeholder="Enter your first name">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" required placeholder="Enter your last name">
                </div>
            </div>
            
            <div class="form-group">
                <label for="regEmail">Email Address</label>
                <input type="email" id="regEmail" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="regPassword">Password</label>
                <div class="password-field">
                    <input type="password" id="regPassword" name="password" required placeholder="Create a password">
                    <span class="toggle-password" onclick="togglePassword('regPassword')">👁️</span>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="strength-meter">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="strength-text" id="strengthText">Password strength</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <div class="password-field">
                    <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Confirm your password">
                    <span class="toggle-password" onclick="togglePassword('confirmPassword')">👁️</span>
                </div>
                <div class="password-match" id="passwordMatch"></div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number">
            </div>
            
            <div class="form-options">
                <label class="checkbox-container">
                    <input type="checkbox" name="newsletter" checked>
                    <span class="checkmark"></span>
                    Subscribe to newsletter
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" name="terms" required>
                    <span class="checkmark"></span>
                    I agree to <a href="#" class="terms-link">Terms & Conditions</a>
                </label>
            </div>
            
            <button type="submit" class="btn-primary auth-btn">Create Account</button>
        </form>
        
        <div class="auth-divider">
            <span>or</span>
        </div>
        
        <div class="social-login">
            <button class="social-btn google-btn">
                <span class="social-icon">🔍</span>
                Sign up with Google
            </button>
            <button class="social-btn facebook-btn">
                <span class="social-icon">f</span>
                Sign up with Facebook
            </button>
        </div>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="?page=login">Sign In</a></p>
        </div>
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

document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const password = document.getElementById('regPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validate passwords match
    if (password !== confirmPassword) {
        showNotification('Passwords do not match!', 'error');
        return;
    }
    
    // Here you would typically send AJAX request to register endpoint
    showNotification('Registration functionality would be implemented here', 'info');
});
</script>

</div>
</div>

<?php require_once './app/views/layouts/footer.php'; ?>

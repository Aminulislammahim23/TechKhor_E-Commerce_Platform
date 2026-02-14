// Register.js - JavaScript functionality for registration form

// Password toggle functionality
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

// Enhanced password strength checker
function checkPasswordStrength(password) {
    const strength = {
        score: 0,
        feedback: []
    };
    
    // Length check
    if (password.length >= 8) {
        strength.score += 1;
    } else {
        strength.feedback.push('Use at least 8 characters');
    }
    
    // Complexity checks
    if (/[a-z]/.test(password)) strength.score += 1;
    else strength.feedback.push('Add lowercase letters');
    
    if (/[A-Z]/.test(password)) strength.score += 1;
    else strength.feedback.push('Add uppercase letters');
    
    if (/[0-9]/.test(password)) strength.score += 1;
    else strength.feedback.push('Add numbers');
    
    if (/[@$!%*?&]/.test(password)) strength.score += 1;
    else strength.feedback.push('Add special characters (@$!%*?&)');
    
    // Common patterns check
    if (password.length > 0) {
        const commonPatterns = ['password', '123456', 'qwerty', 'admin'];
        for (let pattern of commonPatterns) {
            if (password.toLowerCase().includes(pattern)) {
                strength.score = Math.max(0, strength.score - 1);
                strength.feedback.push('Avoid common patterns like "password" or "123456"');
                break;
            }
        }
    }
    
    return strength;
}

// Initialize all event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Password strength validation
    const passwordField = document.getElementById('regPassword');
    if (passwordField) {
        passwordField.addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = checkPasswordStrength(password);
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthFill.style.width = '0%';
                strengthText.textContent = 'Password strength';
                strengthText.style.color = '#666';
                return;
            }
            
            // Update strength meter
            const percentage = (strength.score / 5) * 100;
            strengthFill.style.width = percentage + '%';
            
            // Set color based on strength
            if (strength.score <= 2) {
                strengthFill.style.backgroundColor = '#e74c3c'; // Red
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#e74c3c';
            } else if (strength.score <= 3) {
                strengthFill.style.backgroundColor = '#f39c12'; // Orange
                strengthText.textContent = 'Medium';
                strengthText.style.color = '#f39c12';
            } else if (strength.score <= 4) {
                strengthFill.style.backgroundColor = '#3498db'; // Blue
                strengthText.textContent = 'Good';
                strengthText.style.color = '#3498db';
            } else {
                strengthFill.style.backgroundColor = '#2ecc71'; // Green
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#2ecc71';
            }
        });
    }
    
    // Password confirmation validation
    const confirmPasswordField = document.getElementById('confirmPassword');
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function(e) {
            const password = document.getElementById('regPassword').value;
            const confirmPassword = e.target.value;
            const passwordMatch = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                passwordMatch.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                passwordMatch.textContent = '✓ Passwords match';
                passwordMatch.style.color = '#2ecc71';
            } else {
                passwordMatch.textContent = '✗ Passwords do not match';
                passwordMatch.style.color = '#e74c3c';
            }
        });
    }
    
    // Email format validation feedback
    const emailField = document.getElementById('regEmail');
    if (emailField) {
        emailField.addEventListener('blur', function(e) {
            const email = e.target.value.trim();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert('Please enter a valid email address');
                e.target.focus();
            }
        });
    }
    
    // Form submission validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('regEmail').value.trim();
            const password = document.getElementById('regPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            // Validate required fields
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }
            
            // Validate password requirements
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return false;
            }
            
            if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/.test(password)) {
                e.preventDefault();
                alert('Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&)');
                return false;
            }
            
            // Validate password match
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
            
            // Validate terms acceptance
            if (!terms) {
                e.preventDefault();
                alert('You must agree to the Terms & Conditions');
                return false;
            }
            
            // Additional checks for email format
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return false;
            }
            
            // Allow form submission
            return true;
        });
    }
});

// Export functions for global access if needed
window.togglePassword = togglePassword;
window.checkPasswordStrength = checkPasswordStrength;
document.addEventListener('DOMContentLoaded', function() {
   
    initPasswordToggle();
    initPasswordStrength();
    initFormValidation();
    initLoadingStates();
});


function initPasswordToggle() {
    
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = field.nextElementSibling || field.parentElement.nextElementSibling;
    
    if (field.type === 'password') {
        field.type = 'text';
        if (toggle) toggle.textContent = '🙈';
    } else {
        field.type = 'password';
        if (toggle) toggle.textContent = '👁️';
    }
}


function initPasswordStrength() {
    const passwordField = document.getElementById('regPassword');
    const strengthMeter = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    const strengthContainer = document.getElementById('passwordStrength');
    
    if (!passwordField || !strengthMeter) return;
    
    passwordField.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        
        strengthMeter.style.width = strength.percentage + '%';
        strengthText.textContent = strength.text;
        
        
        strengthContainer.className = 'password-strength strength-' + strength.level.toLowerCase();
    });
}

function calculatePasswordStrength(password) {
    let score = 0;
    let feedback = [];
    
    
    if (password.length >= 8) score += 25;
    else feedback.push('Use at least 8 characters');
    
    
    if (/[a-z]/.test(password)) score += 15;
    else feedback.push('Add lowercase letters');
    
   
    if (/[A-Z]/.test(password)) score += 15;
    else feedback.push('Add uppercase letters');
    
    
    if (/\d/.test(password)) score += 20;
    else feedback.push('Add numbers');
    
    
    if (/[^A-Za-z0-9]/.test(password)) score += 25;
    else feedback.push('Add special characters');
    
    
    let level, text;
    if (score < 40) {
        level = 'Weak';
        text = 'Weak password';
    } else if (score < 70) {
        level = 'Medium';
        text = 'Medium strength';
    } else {
        level = 'Strong';
        text = 'Strong password';
    }
    
    return {
        score: score,
        percentage: score,
        level: level,
        text: text,
        feedback: feedback
    };
}


function initPasswordMatch() {
    const passwordField = document.getElementById('regPassword');
    const confirmPasswordField = document.getElementById('confirmPassword');
    const matchIndicator = document.getElementById('passwordMatch');
    
    if (!passwordField || !confirmPasswordField || !matchIndicator) return;
    
    function checkPasswordMatch() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;
        
        if (confirmPassword === '') {
            matchIndicator.textContent = '';
            matchIndicator.className = 'password-match';
            return;
        }
        
        if (password === confirmPassword) {
            matchIndicator.textContent = '✓ Passwords match';
            matchIndicator.className = 'password-match match-valid';
        } else {
            matchIndicator.textContent = '✗ Passwords do not match';
            matchIndicator.className = 'password-match match-invalid';
        }
    }
    
    confirmPasswordField.addEventListener('input', checkPasswordMatch);
    passwordField.addEventListener('input', checkPasswordMatch);
}


function initFormValidation() {
   
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            validateLoginForm(this);
        });
    }
    
    
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            validateRegisterForm(this);
        });
    }
    
    
    initPasswordMatch();
}

function validateLoginForm(form) {
    const email = form.querySelector('#email').value.trim();
    const password = form.querySelector('#password').value;
    
  
    if (!isValidEmail(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    
   
    if (password.length < 6) {
        showNotification('Password must be at least 6 characters long', 'error');
        return false;
    }
    
   
    simulateLogin(form);
    return true;
}

function validateRegisterForm(form) {
    const firstName = form.querySelector('#firstName').value.trim();
    const lastName = form.querySelector('#lastName').value.trim();
    const email = form.querySelector('#regEmail').value.trim();
    const password = form.querySelector('#regPassword').value;
    const confirmPassword = form.querySelector('#confirmPassword').value;
    const phone = form.querySelector('#phone').value.trim();
    const termsAccepted = form.querySelector('input[name="terms"]').checked;
    
    
    if (!firstName || !lastName) {
        showNotification('Please enter your full name', 'error');
        return false;
    }
    
    
    if (!isValidEmail(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    
    
    if (password.length < 8) {
        showNotification('Password must be at least 8 characters long', 'error');
        return false;
    }
    
    
    const strength = calculatePasswordStrength(password);
    if (strength.score < 40) {
        showNotification('Please use a stronger password', 'error');
        return false;
    }
    
   
    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return false;
    }
    
    
    if (!termsAccepted) {
        showNotification('Please accept the Terms & Conditions', 'error');
        return false;
    }
    
    
    if (phone && !isValidPhone(phone)) {
        showNotification('Please enter a valid phone number', 'error');
        return false;
    }
    
    
    simulateRegistration(form);
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}


function initLoadingStates() {
    
}

function simulateLogin(form) {
    const submitButton = form.querySelector('.auth-btn');
    const originalText = submitButton.innerHTML;
    
    
    submitButton.innerHTML = 'Signing in...';
    submitButton.disabled = true;
    submitButton.classList.add('btn-loading');
    
    
    setTimeout(() => {
        
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        submitButton.classList.remove('btn-loading');
        
        
        showNotification('✅ Login successful! Welcome back!', 'success');
        
        
    }, 2000);
}

function simulateRegistration(form) {
    const submitButton = form.querySelector('.auth-btn');
    const originalText = submitButton.innerHTML;
    
    
    submitButton.innerHTML = 'Creating account...';
    submitButton.disabled = true;
    submitButton.classList.add('btn-loading');
    
    
    setTimeout(() => {
        
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        submitButton.classList.remove('btn-loading');
        
        
        showNotification('🎉 Account created successfully! Welcome to TechKhor!', 'success');
        
        
    }, 2500);
}


function showNotification(message, type = 'info') {
    
    const existingNotifications = document.querySelectorAll('.auth-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    
    const notification = document.createElement('div');
    notification.className = `auth-notification notification-${type}`;
    notification.textContent = message;
    
    
    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '15px 25px',
        borderRadius: '8px',
        color: 'white',
        fontWeight: '600',
        zIndex: '9999',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease',
        maxWidth: '350px'
    });
    
    
    switch(type) {
        case 'success':
            notification.style.backgroundColor = '#28a745';
            break;
        case 'error':
            notification.style.backgroundColor = '#dc3545';
            break;
        case 'warning':
            notification.style.backgroundColor = '#ffc107';
            notification.style.color = '#212529';
            break;
        case 'info':
        default:
            notification.style.backgroundColor = '#17a2b8';
            break;
    }
    
    
    document.body.appendChild(notification);
    
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}


document.querySelectorAll('.social-btn').forEach(button => {
    button.addEventListener('click', function() {
        const platform = this.classList.contains('google-btn') ? 'Google' : 'Facebook';
        showNotification(`Connecting to ${platform}...`, 'info');
        
        
        setTimeout(() => {
            showNotification(`✅ Connected with ${platform} successfully!`, 'success');
        }, 1500);
    });
});


document.querySelector('.forgot-password')?.addEventListener('click', function(e) {
    e.preventDefault();
    showNotification('Password reset link sent to your email!', 'info');
});
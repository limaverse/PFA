// javascript.js - Authentication page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const toggleButtons = document.querySelectorAll('.toggle-btn');
    const authForms = document.querySelectorAll('.auth-form');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tab = this.dataset.tab;
            
            // Update active button
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding form
            authForms.forEach(form => {
                form.classList.remove('active');
                if (form.id === tab + 'Form') {
                    form.classList.add('active');
                }
            });
            
            // Update URL parameter
            updateUrlParam('tab', tab);
        });
    });
    
    // Password strength indicator
    const passwordInput = document.getElementById('signupPassword');
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            
            // Update strength bar
            strengthBar.style.width = strength.percentage + '%';
            strengthBar.className = 'strength-fill ' + strength.class;
            
            // Update text
            strengthText.textContent = strength.text;
            strengthText.className = 'strength-text ' + strength.class;
        });
    }
    
    // Form validation
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            } else {
                showLoading(this.querySelector('.auth-submit-btn'));
            }
        });
    }
    
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            if (!validateSignupForm()) {
                e.preventDefault();
            } else {
                showLoading(this.querySelector('.auth-submit-btn'));
            }
        });
        
        // Confirm password validation
        const confirmPassword = document.getElementById('confirmPassword');
        if (confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                validatePasswordMatch();
            });
        }
    }
    
    // Social login buttons
    const socialButtons = document.querySelectorAll('.social-btn');
    socialButtons.forEach(button => {
        button.addEventListener('click', function() {
            const provider = this.classList.contains('google') ? 'Google' : 'Facebook';
            alert(provider + ' login would be implemented in a real application');
        });
    });
    
    // Forgot password
    const forgotPassword = document.querySelector('.forgot-password');
    if (forgotPassword) {
        forgotPassword.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Password reset functionality would be implemented here');
        });
    }
    
    // Check URL parameter for tab
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam && (tabParam === 'login' || tabParam === 'signup')) {
        const tabButton = document.querySelector(`[data-tab="${tabParam}"]`);
        if (tabButton) {
            tabButton.click();
        }
    }
});

// Update URL parameter
function updateUrlParam(key, value) {
    const url = new URL(window.location);
    url.searchParams.set(key, value);
    window.history.replaceState({}, '', url);
}

// Check password strength
function checkPasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    const strengthLevels = [
        { class: 'weak', text: 'Weak', percentage: 25 },
        { class: 'fair', text: 'Fair', percentage: 50 },
        { class: 'good', text: 'Good', percentage: 75 },
        { class: 'strong', text: 'Strong', percentage: 100 }
    ];
    
    return strengthLevels[Math.min(score, strengthLevels.length - 1)];
}

// Form validation functions
function validateLoginForm() {
    let isValid = true;
    
    // Clear previous errors
    clearErrors('login');
    
    // Email validation
    const email = document.getElementById('loginEmail').value.trim();
    if (!email) {
        showError('loginEmailError', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('loginEmailError', 'Please enter a valid email');
        isValid = false;
    }
    
    // Password validation
    const password = document.getElementById('loginPassword').value;
    if (!password) {
        showError('loginPasswordError', 'Password is required');
        isValid = false;
    } else if (password.length < 6) {
        showError('loginPasswordError', 'Password must be at least 6 characters');
        isValid = false;
    }
    
    return isValid;
}

function validateSignupForm() {
    let isValid = true;
    
    // Clear previous errors
    clearErrors('signup');
    
    // First name validation
    const firstName = document.getElementById('firstName').value.trim();
    if (!firstName) {
        showError('firstNameError', 'First name is required');
        isValid = false;
    }
    
    // Last name validation
    const lastName = document.getElementById('lastName').value.trim();
    if (!lastName) {
        showError('lastNameError', 'Last name is required');
        isValid = false;
    }
    
    // Email validation
    const email = document.getElementById('signupEmail').value.trim();
    if (!email) {
        showError('signupEmailError', 'Email is required');
        isValid = false;
    } else if (!isValidEmail(email)) {
        showError('signupEmailError', 'Please enter a valid email');
        isValid = false;
    }
    
    // Phone validation
    const phone = document.getElementById('phone').value.trim();
    if (!phone) {
        showError('phoneError', 'Phone number is required');
        isValid = false;
    }
    
    // Password validation
    const password = document.getElementById('signupPassword').value;
    if (!password) {
        showError('signupPasswordError', 'Password is required');
        isValid = false;
    } else if (password.length < 6) {
        showError('signupPasswordError', 'Password must be at least 6 characters');
        isValid = false;
    }
    
    // Confirm password validation
    if (!validatePasswordMatch()) {
        isValid = false;
    }
    
    // Terms agreement
    const agreeTerms = document.getElementById('agreeTerms');
    if (!agreeTerms.checked) {
        alert('You must agree to the Terms of Service and Privacy Policy');
        isValid = false;
    }
    
    return isValid;
}

function validatePasswordMatch() {
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorElement = document.getElementById('confirmPasswordError');
    
    if (password !== confirmPassword) {
        showError('confirmPasswordError', 'Passwords do not match');
        return false;
    } else {
        clearError('confirmPasswordError');
        return true;
    }
}

// Helper functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showError(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = message;
        element.style.display = 'block';
    }
}

function clearError(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = '';
        element.style.display = 'none';
    }
}

function clearErrors(formType) {
    const errorElements = document.querySelectorAll(`#${formType}Form .error-message`);
    errorElements.forEach(element => {
        element.textContent = '';
        element.style.display = 'none';
    });
}

function showLoading(button) {
    const btnText = button.querySelector('.btn-text');
    const btnLoader = button.querySelector('.btn-loader');
    
    if (btnText && btnLoader) {
        btnText.style.opacity = '0.5';
        btnLoader.style.display = 'inline-block';
        button.disabled = true;
    }
}
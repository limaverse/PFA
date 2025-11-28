// Handle URL parameters for login/signup tabs
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab === 'login' || tab === 'signup') {
        setTimeout(() => {
            const targetButton = document.querySelector(`[data-tab="${tab}"]`);
            if (targetButton) targetButton.click();
        }, 100);
    }
});
// DOM Elements
const loginForm = document.getElementById('loginForm');
const signupForm = document.getElementById('signupForm');
const toggleButtons = document.querySelectorAll('.toggle-btn');
const authForms = document.querySelectorAll('.auth-form');
const successModal = document.getElementById('successModal');
const errorModal = document.getElementById('errorModal');
const modalClose = document.getElementById('modalClose');
const errorModalClose = document.getElementById('errorModalClose');
const backToHome = document.getElementById('backToHome');

// Password strength elements
const signupPassword = document.getElementById('signupPassword');
const passwordStrength = document.getElementById('passwordStrength');
const passwordStrengthText = document.getElementById('passwordStrengthText');

// User storage (in a real app, this would be a backend API)
const users = JSON.parse(localStorage.getItem('transpori_users')) || [];

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeAuth();
    setupEventListeners();
});

function initializeAuth() {
    // Check if user is already logged in
    const currentUser = localStorage.getItem('transpori_current_user');
    if (currentUser) {
        console.log('User already logged in:', JSON.parse(currentUser));
    }
}

function setupEventListeners() {
    // Toggle between login and signup forms
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update active button
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show target form
            authForms.forEach(form => {
                form.classList.remove('active');
                if (form.id === `${targetTab}Form`) {
                    form.classList.add('active');
                }
            });
        });
    });

    // Login form submission
    loginForm.addEventListener('submit', handleLogin);

    // Signup form submission
    signupForm.addEventListener('submit', handleSignup);

    // Password strength indicator
    signupPassword.addEventListener('input', updatePasswordStrength);

    // Modal close buttons
    modalClose.addEventListener('click', () => {
        successModal.classList.remove('active');
    });

    errorModalClose.addEventListener('click', () => {
        errorModal.classList.remove('active');
    });

    // Back to home button
    backToHome.addEventListener('click', (e) => {
        e.preventDefault();
        alert('Redirecting to home page...');
        // In a real app: window.location.href = 'index.html';
    });

    // Close modals when clicking outside
    [successModal, errorModal].forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    // Social login buttons
    document.querySelectorAll('.social-btn').forEach(button => {
        button.addEventListener('click', handleSocialLogin);
    });

    // Forgot password link
    document.querySelector('.forgot-password').addEventListener('click', (e) => {
        e.preventDefault();
        showErrorModal('Password Reset', 'Password reset functionality will be available soon!');
    });

    // Terms links
    document.querySelectorAll('.terms-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            showErrorModal('Terms & Privacy', 'Terms of Service and Privacy Policy will be available soon!');
        });
    });
}

// Form validation functions
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^\+?[\d\s-()]{10,}$/;
    return phoneRegex.test(phone);
}

function validatePassword(password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    return passwordRegex.test(password);
}

function updatePasswordStrength() {
    const password = signupPassword.value;
    let strength = 0;
    let text = 'Weak';
    let color = '#ef4444';

    if (password.length >= 8) strength += 25;
    if (/[a-z]/.test(password)) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;

    if (strength >= 75) {
        text = 'Strong';
        color = '#10b981';
    } else if (strength >= 50) {
        text = 'Good';
        color = '#f59e0b';
    }

    passwordStrength.style.width = `${strength}%`;
    passwordStrength.style.background = color;
    passwordStrengthText.textContent = text;
    passwordStrengthText.style.color = color;
}

function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    errorElement.textContent = message;
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(element => {
        element.textContent = '';
    });
}

function setLoading(button, isLoading) {
    if (isLoading) {
        button.classList.add('loading');
        button.disabled = true;
    } else {
        button.classList.remove('loading');
        button.disabled = false;
    }
}

// Form handlers
async function handleLogin(e) {
    e.preventDefault();
    clearErrors();

    const submitButton = loginForm.querySelector('.auth-submit-btn');
    setLoading(submitButton, true);

    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const rememberMe = document.getElementById('rememberMe').checked;

    let isValid = true;

    // Validate email
    if (!validateEmail(email)) {
        showError('loginEmailError', 'Please enter a valid email address');
        isValid = false;
    }

    // Validate password
    if (password.length < 6) {
        showError('loginPasswordError', 'Password must be at least 6 characters');
        isValid = false;
    }

    if (!isValid) {
        setLoading(submitButton, false);
        return;
    }

    // Simulate API call
    try {
        await simulateAPICall(1000);
        
        // Check if user exists
        const user = users.find(u => u.email === email && u.password === password);
        
        if (user) {
            // Login successful
            localStorage.setItem('transpori_current_user', JSON.stringify({
                id: user.id,
                email: user.email,
                firstName: user.firstName,
                lastName: user.lastName
            }));

            if (rememberMe) {
                localStorage.setItem('transpori_remember_me', 'true');
            }

            showSuccessModal('Login Successful', 'Welcome back to Transpori!');
        } else {
            showErrorModal('Login Failed', 'Invalid email or password. Please try again.');
        }
    } catch (error) {
        showErrorModal('Login Error', 'Something went wrong. Please try again.');
    } finally {
        setLoading(submitButton, false);
    }
}

async function handleSignup(e) {
    e.preventDefault();
    clearErrors();

    const submitButton = signupForm.querySelector('.auth-submit-btn');
    setLoading(submitButton, true);

    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('signupEmail').value;
    const phone = document.getElementById('phone').value;
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const agreeTerms = document.getElementById('agreeTerms').checked;

    let isValid = true;

    // Validate first name
    if (firstName.length < 2) {
        showError('firstNameError', 'First name must be at least 2 characters');
        isValid = false;
    }

    // Validate last name
    if (lastName.length < 2) {
        showError('lastNameError', 'Last name must be at least 2 characters');
        isValid = false;
    }

    // Validate email
    if (!validateEmail(email)) {
        showError('signupEmailError', 'Please enter a valid email address');
        isValid = false;
    } else if (users.find(u => u.email === email)) {
        showError('signupEmailError', 'Email already exists');
        isValid = false;
    }

    // Validate phone
    if (!validatePhone(phone)) {
        showError('phoneError', 'Please enter a valid phone number');
        isValid = false;
    }

    // Validate password
    if (!validatePassword(password)) {
        showError('signupPasswordError', 'Password must be at least 8 characters with uppercase, lowercase, and numbers');
        isValid = false;
    }

    // Validate confirm password
    if (password !== confirmPassword) {
        showError('confirmPasswordError', 'Passwords do not match');
        isValid = false;
    }

    // Validate terms agreement
    if (!agreeTerms) {
        showError('confirmPasswordError', 'You must agree to the terms and conditions');
        isValid = false;
    }

    if (!isValid) {
        setLoading(submitButton, false);
        return;
    }

    // Simulate API call
    try {
        await simulateAPICall(1500);
        
        // Create new user
        const newUser = {
            id: Date.now().toString(),
            firstName,
            lastName,
            email,
            phone,
            password, // In real app, this would be hashed
            createdAt: new Date().toISOString()
        };

        users.push(newUser);
        localStorage.setItem('transpori_users', JSON.stringify(users));

        // Auto-login after signup
        localStorage.setItem('transpori_current_user', JSON.stringify({
            id: newUser.id,
            email: newUser.email,
            firstName: newUser.firstName,
            lastName: newUser.lastName
        }));

        showSuccessModal('Account Created', 'Welcome to Transpori! Your account has been created successfully.');
        
        // Clear form
        signupForm.reset();
        updatePasswordStrength();
        
    } catch (error) {
        showErrorModal('Signup Error', 'Something went wrong. Please try again.');
    } finally {
        setLoading(submitButton, false);
    }
}

function handleSocialLogin(e) {
    const platform = e.target.closest('.social-btn').classList[1];
    showErrorModal('Coming Soon', `${platform.charAt(0).toUpperCase() + platform.slice(1)} login will be available soon!`);
}

// Modal functions
function showSuccessModal(title, message) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    successModal.classList.add('active');
}

function showErrorModal(title, message) {
    document.getElementById('errorModalTitle').textContent = title;
    document.getElementById('errorModalMessage').textContent = message;
    errorModal.classList.add('active');
}

// Utility function to simulate API calls
function simulateAPICall(duration) {
    return new Promise((resolve) => {
        setTimeout(resolve, duration);
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape key closes modals
    if (e.key === 'Escape') {
        successModal.classList.remove('active');
        errorModal.classList.remove('active');
    }
    
    // Tab key switches between forms
    if (e.key === 'Tab' && e.ctrlKey) {
        e.preventDefault();
        const activeTab = document.querySelector('.toggle-btn.active').getAttribute('data-tab');
        const targetTab = activeTab === 'login' ? 'signup' : 'login';
        
        document.querySelector(`[data-tab="${targetTab}"]`).click();
    }
});

// Form input enhancements
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
});

// Demo user for testing
function createDemoUser() {
    const demoUser = {
        id: 'demo123',
        firstName: 'Demo',
        lastName: 'User',
        email: 'demo@transpori.com',
        phone: '+1234567890',
        password: 'Demo123',
        createdAt: new Date().toISOString()
    };
    
    // Only add demo user if it doesn't exist
    if (!users.find(u => u.email === demoUser.email)) {
        users.push(demoUser);
        localStorage.setItem('transpori_users', JSON.stringify(users));
        console.log('Demo user created:', demoUser);
    }
}



// Create demo user on page load
createDemoUser();
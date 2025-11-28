<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transpori";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection - but don't die if it fails for demo
if ($conn->connect_error) {
    // Continue without database for demo
    $db_connected = false;
} else {
    $db_connected = true;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    // Simple demo responses - remove this when database is ready
    switch ($action) {
        case 'register':
            $response = ["success" => true, "message" => "Registration successful (Demo)"];
            break;
            
        case 'login':
            $response = ["success" => true, "message" => "Login successful (Demo)"];
            break;
            
        default:
            $response = ["success" => false, "message" => "Invalid action"];
    }
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Authentication</title>
    <link rel="stylesheet" href="transpori/login...signup/css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Fallback styles in case CSS file is not found */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 400px;
            max-width: 90%;
        }
        .toggle-btn {
            padding: 10px;
            border: none;
            background: #f8f9fa;
            cursor: pointer;
            flex: 1;
        }
        .toggle-btn.active {
            background: #007bff;
            color: white;
        }
        .auth-form {
            display: none;
        }
        .auth-form.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Background Animation -->
        <div class="background-animation">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo">
                    <i class="fas fa-shield-alt"></i>
                    <span>Transpori</span>
                </div>
                <p class="tagline">Safe Transportation for Everyone</p>
            </div>

            <!-- Toggle Buttons -->
            <div class="auth-toggle">
                <button class="toggle-btn active" data-tab="login" type="button">Login</button>
                <button class="toggle-btn" data-tab="signup" type="button">Sign Up</button>
            </div>

            <!-- Login Form -->
            <form id="loginForm" class="auth-form active">
                <div class="form-group">
                    <label for="loginEmail">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" id="loginEmail" name="email" required>
                    <div class="error-message" id="loginEmailError"></div>
                </div>

                <div class="form-group">
                    <label for="loginPassword">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <input type="password" id="loginPassword" name="password" required>
                    <div class="error-message" id="loginPasswordError"></div>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" id="rememberMe">
                        <span class="checkmark"></span>
                        Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span class="btn-text">Login</span>
                    <div class="btn-loader">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </button>

                <div class="social-login">
                    <p>Or continue with</p>
                    <div class="social-buttons">
                        <button type="button" class="social-btn google">
                            <i class="fab fa-google"></i>
                            Google
                        </button>
                        <button type="button" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </button>
                    </div>
                </div>
            </form>

            <!-- Signup Form -->
            <form id="signupForm" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">
                            <i class="fas fa-user"></i>
                            First Name
                        </label>
                        <input type="text" id="firstName" name="firstName" required>
                        <div class="error-message" id="firstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">
                            <i class="fas fa-user"></i>
                            Last Name
                        </label>
                        <input type="text" id="lastName" name="lastName" required>
                        <div class="error-message" id="lastNameError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="signupEmail">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" id="signupEmail" name="email" required>
                    <div class="error-message" id="signupEmailError"></div>
                </div>

                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i>
                        Phone Number
                        </label>
                    <input type="tel" id="phone" name="phone" required>
                    <div class="error-message" id="phoneError"></div>
                </div>

                <div class="form-group">
                    <label for="signupPassword">
                        <i class="fas fa-lock"></i>
                        Password
                    </label>
                    <input type="password" id="signupPassword" name="password" required>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="passwordStrength"></div>
                        </div>
                        <span class="strength-text" id="passwordStrengthText">Weak</span>
                    </div>
                    <div class="error-message" id="signupPasswordError"></div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">
                        <i class="fas fa-lock"></i>
                        Confirm Password
                    </label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <div class="error-message" id="confirmPasswordError"></div>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" id="agreeTerms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="auth-submit-btn">
                    <span class="btn-text">Create Account</span>
                    <div class="btn-loader">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </button>
            </form>

            <!-- Back to Home -->
            <!-- Back to Home -->
<div class="back-home">
    <a href="homepage.php" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>
</div>
        </div>

        <!-- Success Modal -->
        <div id="successModal" class="modal">
            <div class="modal-content">
                <div class="modal-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 id="modalTitle">Success!</h3>
                <p id="modalMessage">Your account has been created successfully.</p>
                <button id="modalClose" class="modal-btn">Continue</button>
            </div>
        </div>

        <!-- Error Modal -->
        <div id="errorModal" class="modal">
            <div class="modal-content">
                <div class="modal-icon error">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3 id="errorModalTitle">Error!</h3>
                <p id="errorModalMessage">Something went wrong. Please try again.</p>
                <button id="errorModalClose" class="modal-btn">Try Again</button>
            </div>
        </div>
    </div>

    <script>
    // Basic JavaScript to make the page work
    document.addEventListener('DOMContentLoaded', function() {
        console.log('JavaScript loaded!');
        
        // Tab switching
        const toggleBtns = document.querySelectorAll('.toggle-btn');
        const authForms = document.querySelectorAll('.auth-form');
        
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all
                toggleBtns.forEach(b => b.classList.remove('active'));
                authForms.forEach(form => form.classList.remove('active'));
                
                // Add active to clicked
                this.classList.add('active');
                const tab = this.getAttribute('data-tab');
                document.getElementById(tab + 'Form').classList.add('active');
            });
        });
        
        // Form submissions
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Login functionality would work here!');
        });
        
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Signup functionality would work here!');
        });
        
        // Modal functionality
        document.getElementById('modalClose').addEventListener('click', function() {
            document.getElementById('successModal').style.display = 'none';
        });
        
        document.getElementById('errorModalClose').addEventListener('click', function() {
            document.getElementById('errorModal').style.display = 'none';
        });
        
        // Test modals
        // document.getElementById('successModal').style.display = 'flex'; // Uncomment to test
    });
    </script>
    
    <!-- Your external JavaScript -->
    <script src="transpori/login...signup/javascript.js"></script>
</body>
</html>
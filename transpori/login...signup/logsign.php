<?php
// logsign.php - Login/Signup page
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Database connection
require_once '../dashuser/database.php';

$error_message = '';
$success_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'login') {
            // Login process
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Special admin login
            if ($email === 'admin@transpori.tn' && $password === 'admintranspori') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_email'] = $email;
                header('Location: ../dashadmin/dashboard.php');
                exit;
            }
            
            // Regular user login
            try {
                $stmt = $conn->prepare("SELECT id, email, password_hash, first_name, last_name FROM members WHERE email = ? AND is_active = TRUE");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['is_logged_in'] = true;

                    // Update last login
                    $stmt = $conn->prepare("UPDATE members SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    
                   header('Location: /PFA/transpori/dashuser/index.php');
exit();
                } else {
                    $error_message = "Invalid email or password";
                }
            } catch (PDOException $e) {
                $error_message = "Login error: " . $e->getMessage();
            }
            
        } elseif ($_POST['action'] === 'signup') {
            // Signup process
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validation
            if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
                $error_message = "All fields are required";
            } elseif ($password !== $confirm_password) {
                $error_message = "Passwords do not match";
            } elseif (strlen($password) < 6) {
                $error_message = "Password must be at least 6 characters";
            } else {
                try {
                    // Check if email already exists
                    $stmt = $conn->prepare("SELECT id FROM members WHERE email = ?");
                    $stmt->execute([$email]);
                    
                    if ($stmt->rowCount() > 0) {
                        $error_message = "Email already registered";
                    } else {
                        // Create new user
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt = $conn->prepare("
                            INSERT INTO members (email, password_hash, first_name, last_name, phone, is_verified, is_active, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ");
                        
                        $stmt->execute([
                            $email,
                            $hashed_password,
                            $first_name,
                            $last_name,
                            $phone,
                            false, // Not verified by default
                            true   // Active by default
                        ]);
                        
                        $user_id = $conn->lastInsertId();
                        
                        // Auto-login after signup
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                        
                        $success_message = "Account created successfully!";
                        
                        // Redirect after 2 seconds
                        header("refresh:2;url=../dashuser/index.php");
                    }
                } catch (PDOException $e) {
                    $error_message = "Signup error: " . $e->getMessage();
                }
            }
        }
    }
}

// Check current tab from URL parameter
$current_tab = isset($_GET['tab']) && ($_GET['tab'] === 'login' || $_GET['tab'] === 'signup') ? $_GET['tab'] : 'login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Authentication</title>
    <link rel="stylesheet" href="css/css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

            <!-- Error/Success Messages -->
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                    <p class="redirect-message">Redirecting to dashboard...</p>
                </div>
            <?php endif; ?>

            <!-- Toggle Buttons -->
            <div class="auth-toggle">
                <button class="toggle-btn <?php echo $current_tab === 'login' ? 'active' : ''; ?>" data-tab="login">Login</button>
                <button class="toggle-btn <?php echo $current_tab === 'signup' ? 'active' : ''; ?>" data-tab="signup">Sign Up</button>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" class="auth-form <?php echo $current_tab === 'login' ? 'active' : ''; ?>">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="loginEmail">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" id="loginEmail" name="email" required 
                           value="<?php echo isset($_POST['email']) && $_POST['action'] === 'login' ? htmlspecialchars($_POST['email']) : ''; ?>">
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
                        <input type="checkbox" id="rememberMe" name="remember">
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
            <form id="signupForm" method="POST" class="auth-form <?php echo $current_tab === 'signup' ? 'active' : ''; ?>">
                <input type="hidden" name="action" value="signup">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">
                            <i class="fas fa-user"></i>
                            First Name
                        </label>
                        <input type="text" id="firstName" name="first_name" required 
                               value="<?php echo isset($_POST['first_name']) && $_POST['action'] === 'signup' ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                        <div class="error-message" id="firstNameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="lastName">
                            <i class="fas fa-user"></i>
                            Last Name
                        </label>
                        <input type="text" id="lastName" name="last_name" required
                               value="<?php echo isset($_POST['last_name']) && $_POST['action'] === 'signup' ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                        <div class="error-message" id="lastNameError"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="signupEmail">
                        <i class="fas fa-envelope"></i>
                        Email Address
                    </label>
                    <input type="email" id="signupEmail" name="email" required
                           value="<?php echo isset($_POST['email']) && $_POST['action'] === 'signup' ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <div class="error-message" id="signupEmailError"></div>
                </div>

                <div class="form-group">
                    <label for="phone">
                        <i class="fas fa-phone"></i>
                        Phone Number
                    </label>
                    <input type="tel" id="phone" name="phone" required
                           value="<?php echo isset($_POST['phone']) && $_POST['action'] === 'signup' ? htmlspecialchars($_POST['phone']) : ''; ?>">
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
                    <input type="password" id="confirmPassword" name="confirm_password" required>
                    <div class="error-message" id="confirmPasswordError"></div>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" id="agreeTerms" name="agree_terms" required>
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
            <div class="back-home">
                <a href="../home/transpori.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <script src="javascript/javascript.js"></script>
</body>
</html>
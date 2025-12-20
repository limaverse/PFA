<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Safe Transportation Reporting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Load appropriate CSS based on page -->
    <?php if (strpos($_SERVER['PHP_SELF'], 'dashuser') !== false): ?>
        <!-- Dashboard CSS -->
        <link rel="stylesheet" href="/PFA/transpori/dashuser/css/style.css">
    <?php else: ?>
        <!-- Main Page CSS -->
        <link rel="stylesheet" href="/PFA/transpori/home/css/transpori css.css">
    <?php endif; ?>
</head>
<body <?php echo (strpos($_SERVER['PHP_SELF'], 'dashuser') !== false) ? 'class="dashboard-page"' : ''; ?>>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="<?php echo $is_logged_in ? '/PFA/transpori/dashuser/index.php' : '/PFA/transpori/home/transpori.php'; ?>" class="logo">
                    <i class="fas fa-shield-alt"></i>
                    Transpori
                </a>
                <ul class="nav-links">
                    <?php if ($is_logged_in): ?>
                        <!-- Logged In Navigation -->
                        <li><a href="/PFA/transpori/dashuser/index.php" class="nav-link">Home</a></li>
                        <li><a href="/PFA/transpori/dashuser/my-experiences.php" class="nav-link">My Experiences</a></li>
                        <li><a href="/PFA/transpori/dashuser/share.php" class="nav-link">Share Experience</a></li>
                        <li><a href="/PFA/transpori/dashuser/tips.php" class="nav-link">Safety Tips</a></li>
                        <li><a href="/PFA/transpori/dashuser/emergency.php" class="nav-link">Emergency Contacts</a></li>
                    <?php else: ?>
                        <!-- Logged Out Navigation (Main Page) -->
                        <li><a href="#home" class="nav-link scroll-link">Home</a></li>
                        <li><a href="#about" class="nav-link scroll-link">About</a></li>
                        <li><a href="#services" class="nav-link scroll-link">Services</a></li>
                        <li><a href="#articles" class="nav-link scroll-link">Resources</a></li>
                        <li><a href="#contact" class="nav-link scroll-link">Contact</a></li>
                    <?php endif; ?>
                </ul>
                <div class="auth-buttons">
                    <?php if ($is_logged_in): ?>
                        <!-- Profile Dropdown for logged in users -->
                        <div class="profile-dropdown">
                            <a href="#" class="profile-btn">
                                <div class="profile-avatar">
                                    <?php echo substr($user_name, 0, 2); ?>
                                </div>
                                <span><?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <div class="profile-dropdown-content">
                                <a href="/PFA/transpori/dashuser/index.php">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                                <a href="#">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a href="/PFA/transpori/dashuser/logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Login/Signup buttons for logged out users -->
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=login" class="btn btn-outline">Login</a>
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn">Sign Up</a>
                    <?php endif; ?>
                </div>
                <button class="hamburger">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        const authButtons = document.querySelector('.auth-buttons');
        
        if (hamburger) {
            hamburger.addEventListener('click', function(e) {
                e.stopPropagation();
                navLinks.classList.toggle('mobile-active');
                authButtons.classList.toggle('mobile-active');
            });
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.navbar')) {
                navLinks.classList.remove('mobile-active');
                authButtons.classList.remove('mobile-active');
            }
        });
        
        // Close mobile menu when clicking a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                navLinks.classList.remove('mobile-active');
                authButtons.classList.remove('mobile-active');
            });
        });
    });
    </script>
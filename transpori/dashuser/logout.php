<?php
// C:\xampp\htdocs\PFA\transpori\dashuser\logout.php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page instead of homepage
header('Location: ../login...signup/logsign.php?tab=login');
exit;
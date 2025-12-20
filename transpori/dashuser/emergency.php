<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /PFA/transpori/login...signup/logsign.php?tab=login');
    exit();
}

$user_name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Emergency Contacts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/PFA/transpori/home/css/transpori css.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard-page">
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-shield-alt"></i>
                    Transpori
                </a>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="my-experiences.php" class="nav-link">My Experiences</a></li>
                    <li><a href="share.php" class="nav-link">Share Experience</a></li>
                    <li><a href="tips.php" class="nav-link">Safety Tips</a></li>
                    <li><a href="emergency.php" class="nav-link">Emergency Contacts</a></li>
                </ul>
                <div class="auth-buttons">
                    <a href="#" class="profile-btn">
                        <div class="profile-avatar">
                            <?php echo substr($user_name, 0, 2); ?>
                        </div>
                        <span><?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                    </a>
                    <a href="logout.php" class="btn btn-outline">Logout</a>
                </div>
                <button class="hamburger">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <main class="main-content">
        
        <div class="dashboard-header">
            <div>
                <h1 class="text-3xl font-extrabold">Emergency Contacts</h1>
                <p class="text-text-secondary">Important numbers for your safety</p>
            </div>
        </div>
        
        <div class="glass-card mb-6">
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-red-400"></i>
                    Emergency Services
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">Police Emergency</h4>
                                <span class="inline-block px-2 py-1 bg-red-500/20 text-red-400 text-xs font-bold rounded mt-1">EMERGENCY</span>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('197')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">197</div>
                        
                        <p class="text-sm text-text-secondary">Immediate police assistance</p>
                    </div>
                    
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">Ambulance</h4>
                                <span class="inline-block px-2 py-1 bg-red-500/20 text-red-400 text-xs font-bold rounded mt-1">EMERGENCY</span>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('190')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">190</div>
                        
                        <p class="text-sm text-text-secondary">Medical emergencies</p>
                    </div>
                    
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">Civil Protection (Fire)</h4>
                                <span class="inline-block px-2 py-1 bg-red-500/20 text-red-400 text-xs font-bold rounded mt-1">EMERGENCY</span>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('198')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">198</div>
                        
                        <p class="text-sm text-text-secondary">Fire and rescue services</p>
                    </div>
                </div>
            </div>
            
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-shield-alt mr-2 text-green-400"></i>
                    Green Line (Safety Reports)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">Anti-Corruption Line</h4>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('1899')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">1899</div>
                        
                        <p class="text-sm text-text-secondary">Report corruption cases</p>
                    </div>
                    
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">Transport Safety Line</h4>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('1899')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">1899</div>
                        
                        <p class="text-sm text-text-secondary">Transport safety reports</p>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-bus mr-2 text-blue-400"></i>
                    Transport Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">SNCFT Information</h4>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('30 100')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">30 100</div>
                        
                        <p class="text-sm text-text-secondary">Train information and inquiries</p>
                    </div>
                    
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">TGM Information</h4>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('71 447 000')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">71 447 000</div>
                        
                        <p class="text-sm text-text-secondary">TGM line information</p>
                    </div>
                    
                    <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h4 class="font-bold text-lg">Light Metro Information</h4>
                            </div>
                            <button class="text-primary hover:text-primary-dark transition" 
                                    onclick="copyToClipboard('71 234 000')"
                                    title="Copy number">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                        
                        <div class="text-2xl font-bold text-primary mb-3">71 234 000</div>
                        
                        <p class="text-sm text-text-secondary">Metro light information</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Copied to clipboard: ' + text);
        }, function(err) {
            alert('Failed to copy: ' + err);
        });
    }
    </script>
    
    <script src="/PFA/transpori/home/javascript/transpori javascript.js"></script>
    <script src="javascript/main.js"></script>
</body>
</html>
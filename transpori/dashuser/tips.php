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
    <title>Transpori | Safety Tips</title>
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
                <h1 class="text-3xl font-extrabold">Safety Tips</h1>
                <p class="text-text-secondary">Stay safe while using public transportation</p>
            </div>
        </div>
        
        <div class="glass-card mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="flex flex-col p-4 rounded-lg bg-glass-bg border border-glass-border hover:border-primary transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4">
                            <i class="fas fa-eye text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-lg">Stay Alert</h4>
                    </div>
                    <p class="text-text-secondary flex-grow">Always be aware of your surroundings when using public transport.</p>
                </div>
                
                <div class="flex flex-col p-4 rounded-lg bg-glass-bg border border-glass-border hover:border-primary transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4">
                            <i class="fas fa-briefcase text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-lg">Secure Belongings</h4>
                    </div>
                    <p class="text-text-secondary flex-grow">Keep your bags closed and in front of you where you can see them.</p>
                </div>
                
                <div class="flex flex-col p-4 rounded-lg bg-glass-bg border border-glass-border hover:border-primary transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4">
                            <i class="fas fa-door-open text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-lg">Emergency Exits</h4>
                    </div>
                    <p class="text-text-secondary flex-grow">Note the location of emergency exits when boarding.</p>
                </div>
                
                <div class="flex flex-col p-4 rounded-lg bg-glass-bg border border-glass-border hover:border-primary transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-lg">Travel in Groups</h4>
                    </div>
                    <p class="text-text-secondary flex-grow">When possible, travel with friends or during busy hours.</p>
                </div>
                
                <div class="flex flex-col p-4 rounded-lg bg-glass-bg border border-glass-border hover:border-primary transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4">
                            <i class="fas fa-mobile-alt text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-lg">Keep Phone Charged</h4>
                    </div>
                    <p class="text-text-secondary flex-grow">Ensure your phone is charged before traveling.</p>
                </div>
                
                <div class="flex flex-col p-4 rounded-lg bg-glass-bg border border-glass-border hover:border-primary transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-primary to-secondary flex items-center justify-center mr-4">
                            <i class="fas fa-map-marked-alt text-white text-xl"></i>
                        </div>
                        <h4 class="font-bold text-lg">Know Your Route</h4>
                    </div>
                    <p class="text-text-secondary flex-grow">Plan your route in advance and share it with someone.</p>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="glass-card">
                <h3 class="text-xl font-bold mb-4">Before You Travel</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Plan your route in advance using reliable apps</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Share your itinerary with family or friends</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Check service updates and potential delays</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Save emergency contacts in your phone</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Charge your phone and power bank</span>
                    </li>
                </ul>
            </div>
            
            <div class="glass-card">
                <h3 class="text-xl font-bold mb-4">While Traveling</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Sit near the driver or in well-lit areas</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Keep valuables secured and out of sight</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Trust your instincts - if something feels wrong, move</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Note emergency exits and equipment locations</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-success mt-1 mr-3"></i>
                        <span class="text-text-secondary">Report suspicious activity to authorities</span>
                    </li>
                </ul>
            </div>
        </div>

    </main>

    <script src="/PFA/transpori/home/javascript/transpori javascript.js"></script>
    <script src="javascript/main.js"></script>
</body>
</html>
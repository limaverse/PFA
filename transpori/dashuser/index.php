<?php
// C:\xampp\htdocs\PFA\transpori\dashuser\index.php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PFA/transpori/login...signup/logsign.php?tab=login');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// GET USER'S STATISTICS - Only approved experiences count
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_experiences,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_experiences,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_experiences,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_experiences
    FROM experiences 
    WHERE member_id = ?
");
$stmt->execute([$user_id]);
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// GET PUBLIC EXPERIENCES FROM DATABASE - Only approved ones
$stmt = $conn->prepare("
    SELECT e.*, 
           m.first_name, 
           m.last_name,
           c.name as category_name
    FROM experiences e
    JOIN members m ON e.member_id = m.id
    LEFT JOIN categories c ON e.category_id = c.id
    WHERE e.is_public = TRUE 
    AND e.status = 'approved'
    ORDER BY e.created_at DESC
    LIMIT 10
");
$stmt->execute();
$public_experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// GET USER'S PENDING EXPERIENCES COUNT
$stmt = $conn->prepare("
    SELECT COUNT(*) as pending_count 
    FROM experiences 
    WHERE member_id = ? AND status = 'pending'
");
$stmt->execute([$user_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$pending_count = $result['pending_count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Dashboard</title>
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
                <h1 class="text-3xl font-extrabold">Welcome back, <?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?>!</h1>
                <p class="text-text-secondary">Recent experiences from our community</p>
                <?php if ($pending_count > 0): ?>
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-500/20 text-yellow-400">
                        <i class="fas fa-clock mr-1"></i>
                        You have <?php echo $pending_count; ?> experience(s) awaiting approval
                    </span>
                </div>
                <?php endif; ?>
            </div>
            <button class="btn" onclick="window.location.href='share.php'">
                <i class="fas fa-plus mr-2"></i> Share Experience
            </button>
        </div>
        
        <!-- Quick Stats -->
        <div class="stats-grid grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card stat-card">
                <div class="stat-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['total_experiences'] ?? 0; ?></div>
                    <div class="stat-label">Total Experiences</div>
                </div>
            </div>
            
            <div class="glass-card stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #06b6d4);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['approved_experiences'] ?? 0; ?></div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            
            <div class="glass-card stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['pending_experiences'] ?? 0; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            
            <div class="glass-card stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['rejected_experiences'] ?? 0; ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </div>

        <!-- Community Experiences -->
        <div class="section-title mb-6">
            <h2>Recent Community Experiences</h2>
            <small class="text-text-secondary">All posts are approved by moderators</small>
        </div>
        
        <div class="space-y-6 mb-8">
            <?php if (empty($public_experiences)): ?>
                <div class="post-card text-center py-8">
                    <i class="fas fa-edit text-4xl text-text-secondary mb-4"></i>
                    <h3 class="text-xl font-bold text-text mb-2">No approved experiences yet</h3>
                    <p class="text-text-secondary mb-6">Be the first to share an experience!</p>
                    <button class="btn" onclick="window.location.href='share.php'">
                        <i class="fas fa-plus mr-2"></i> Share Experience
                    </button>
                </div>
            <?php else: ?>
                <?php foreach ($public_experiences as $experience): ?>
                    <div class="post-card">
                        <div class="post-header">
                            <div class="post-avatar">
                                <?php 
                                $initials = '';
                                if (!empty($experience['first_name'])) {
                                    $initials = substr($experience['first_name'], 0, 1);
                                }
                                if (!empty($experience['last_name'])) {
                                    $initials .= substr($experience['last_name'], 0, 1);
                                }
                                echo $initials ?: 'UU';
                                ?>
                            </div>
                            <div>
                                <div class="font-bold">
                                    <?php 
                                    $name = '';
                                    if (!empty($experience['first_name'])) {
                                        $name = $experience['first_name'];
                                    }
                                    if (!empty($experience['last_name'])) {
                                        $name .= ' ' . $experience['last_name'];
                                    }
                                    echo htmlspecialchars($name ?: 'Unknown User');
                                    ?>
                                </div>
                                <div class="text-sm text-text-secondary">
                                    <?php echo date('M d, Y', strtotime($experience['created_at'])); ?>
                                    <?php if (!empty($experience['category_name'])): ?>
                                        • <?php echo htmlspecialchars($experience['category_name']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="status-badge status-<?php echo htmlspecialchars($experience['experience_type'] ?? 'positive'); ?>">
                                <?php echo htmlspecialchars($experience['experience_type'] ?? 'positive'); ?>
                            </span>
                        </div>
                        
                        <div class="post-content">
                            <h4 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($experience['title']); ?></h4>
                            <p><?php echo nl2br(htmlspecialchars(substr($experience['content'], 0, 200))); ?>...</p>
                            <?php if (!empty($experience['location'])): ?>
                            <div class="mt-2 text-sm text-text-secondary">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?php echo htmlspecialchars($experience['location']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="post-actions">
                            <button class="action-btn like-btn" data-id="<?php echo $experience['id']; ?>">
                                <i class="fas fa-thumbs-up"></i>
                                <span><?php echo $experience['likes_count'] ?? 0; ?> likes</span>
                            </button>
                            <button class="action-btn">
                                <i class="fas fa-comment"></i>
                                <span><?php echo $experience['comments_count'] ?? 0; ?> comments</span>
                            </button>
                            <button class="action-btn ml-auto" onclick="window.location.href='view-experience.php?id=<?php echo $experience['id']; ?>'">
                                <i class="fas fa-eye"></i>
                                <span>View Details</span>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="glass-card text-center">
            <a href="my-experiences.php" class="btn btn-outline">
                <i class="fas fa-history mr-2"></i> View All Your Experiences
            </a>
        </div>

    </main>

    <script>
    // AJAX for likes
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const experienceId = this.getAttribute('data-id');
            const likeBtn = this;
            
            fetch('ajax/like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: experienceId,
                    type: 'experience'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const span = likeBtn.querySelector('span');
                    span.textContent = data.newCount + ' likes';
                    
                    // Toggle liked state
                    const icon = likeBtn.querySelector('i');
                    if (icon.classList.contains('text-primary')) {
                        icon.classList.remove('text-primary');
                    } else {
                        icon.classList.add('text-primary');
                    }
                }
            });
        });
    });
    </script>
    
    <script src="/PFA/transpori/home/javascript/transpori javascript.js"></script>
    <script src="javascript/main.js"></script>
</body>
</html>
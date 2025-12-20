<?php
// C:\xampp\htdocs\PFA\transpori\dashadmin\dashboard.php
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../auth.php');
    exit;
}

// Include the user database connection
require_once __DIR__ . '/../dashuser/database.php';

// Fetch data for dashboard
try {
    $reports_count = (int)$conn->query("SELECT COUNT(*) FROM reports")->fetchColumn();
    $pending_reports = (int)$conn->query("SELECT COUNT(*) FROM reports WHERE status = 'pending'")->fetchColumn();
    $active_users = (int)$conn->query("SELECT COUNT(*) FROM members WHERE is_active = TRUE")->fetchColumn();
    $user_posts = (int)$conn->query("SELECT COUNT(*) FROM experiences")->fetchColumn();
    $pending_experiences_count = (int)$conn->query("SELECT COUNT(*) FROM experiences WHERE status = 'pending'")->fetchColumn();

    $recent_reports = $conn->query("SELECT * FROM reports ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $unverified_users = $conn->query("SELECT * FROM members WHERE is_verified = FALSE LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $emergency_contacts = $conn->query("SELECT * FROM emergency_contacts ORDER BY category, name")->fetchAll(PDO::FETCH_ASSOC);
    $users_by_status = $conn->query("SELECT status, COUNT(*) as count FROM members GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reports_count = $pending_reports = $active_users = $user_posts = $pending_experiences_count = 0;
    $recent_reports = $unverified_users = $categories = $emergency_contacts = $users_by_status = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="dashcss.css">
</head>
<body>
    
    <!-- Sidebar Toggle Button for Mobile -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <a href="dashboard.php" class="sidebar-logo">
            <i class="fas fa-shield-alt"></i>
            Admin Control Panel
        </a>
        
        <nav class="sidebar-nav flex-grow">
            
            <a href="dashboard.php" class="active" data-page="dashboard">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>

            <!-- System Management -->
            <div class="sidebar-section-title">System Management</div>
            <a href="users.php" data-page="users">
                <i class="fas fa-users-cog w-5"></i> User Management
            </a>
            <a href="system_admin.php" data-page="system-admin">
                <i class="fas fa-cogs w-5"></i> System Administration
            </a>
            
            <!-- Content & Reports -->
            <div class="sidebar-section-title">Content & Safety</div>
            <a href="reports.php" data-page="reports">
                <i class="fas fa-exclamation-triangle w-5"></i> Report Management
            </a>
            <a href="moderation.php" data-page="moderation">
                <i class="fas fa-th-list w-5"></i> Content Moderation
            </a>
            <a href="content_creation.php" data-page="content-creation">
                <i class="fas fa-pen-fancy w-5"></i> Content Creation
            </a>
            <a href="categories.php" data-page="categories">
                <i class="fas fa-tags w-5"></i> Categories Management
            </a>

            <!-- Public Services -->
            <div class="sidebar-section-title">Public Services</div>
            <a href="emergency_contacts.php" data-page="emergency-contacts">
                <i class="fas fa-headset w-5"></i> Emergency Contacts
            </a>
            <a href="events.php" data-page="events">
                <i class="fas fa-calendar-alt w-5"></i> Events Management
            </a>

            <!-- Analytics -->
            <div class="sidebar-section-title">Analytics</div>
            <a href="stats.php" data-page="stats">
                <a href="moderation.php" data-page="moderation">
                <i class="fas fa-chart-bar w-5"></i> Statistics & Analytics
            </a>
            
        </nav>
        
        <!-- Logout Button -->
        <div class="mt-auto pt-4 border-t border-white/10">
            <a href="../auth.php?logout=true" class="sidebar-nav-item text-red-400 hover:text-red-300">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h1 class="text-3xl font-extrabold">Welcome back, Admin!</h1>
            <div class="relative">
                <button id="profile-menu-btn" class="flex items-center gap-3 p-2 rounded-full bg-white/10 hover:bg-white/20 transition">
                    <img src="https://placehold.co/40x40/8b5cf6/ffffff?text=AD" alt="Admin" class="w-10 h-10 rounded-full border-2 border-secondary">
                    <span class="hidden sm:inline font-semibold">
                        <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Administrator'; ?>
                    </span>
                </button>
            </div>
        </div>

        <!-- Dashboard Stats Cards -->
        <div class="stats-grid mb-8">
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Total Reports</h3>
                    <i class="fas fa-exclamation-triangle text-2xl text-orange-400"></i>
                </div>
                <p class="text-3xl font-bold mb-2"><?php echo $reports_count; ?></p>
                <p class="text-sm opacity-80">Safety incidents reported</p>
            </div>
            
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Pending Reports</h3>
                    <i class="fas fa-clock text-2xl text-yellow-400"></i>
                </div>
                <p class="text-3xl font-bold mb-2"><?php echo $pending_reports; ?></p>
                <p class="text-sm opacity-80">Awaiting review</p>
            </div>
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Pending Experiences</h3>
                    <i class="fas fa-clock text-2xl text-yellow-400"></i>
                </div>
                <p class="text-3xl font-bold mb-2"><?php echo $pending_experiences_count; ?></p>
                <p class="text-sm opacity-80">Awaiting moderation</p>
                <a href="moderation.php" class="btn btn-sm mt-4 w-full">Review Now</a>
            </div>
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Active Users</h3>
                    <i class="fas fa-users text-2xl text-green-400"></i>
                </div>
                <p class="text-3xl font-bold mb-2"><?php echo $active_users; ?></p>
                <p class="text-sm opacity-80">Registered members</p>
            </div>
            <div class="glass-card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">Pending Experiences</h3>
        <i class="fas fa-clock text-2xl text-yellow-400"></i>
    </div>
    <p class="text-3xl font-bold mb-2"><?php echo $pending_experiences_count; ?></p>
    <p class="text-sm opacity-80">Awaiting moderation</p>
    <a href="moderation.php" class="btn btn-sm mt-4 w-full">Review Now</a>
</div>
            <div class="glass-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">User Posts</h3>
                    <i class="fas fa-comments text-2xl text-blue-400"></i>
                </div>
                <p class="text-3xl font-bold mb-2"><?php echo $user_posts; ?></p>
                <p class="text-sm opacity-80">Experiences shared</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Reports -->
            <div class="glass-card">
                <h2 class="text-xl font-bold text-primary mb-4">Recent Reports</h2>
                <div class="space-y-3">
                    <?php foreach(array_slice($recent_reports, 0, 5) as $report): ?>
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <div class="font-semibold"><?php echo htmlspecialchars($report['type']); ?></div>
                            <div class="text-sm text-white/70"><?php echo htmlspecialchars($report['location']); ?></div>
                        </div>
                        <span class="status-badge status-<?php echo $report['status']; ?>">
                            <?php echo ucfirst($report['status']); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="reports.php" class="btn btn-outline w-full mt-4">View All Reports</a>
            </div>

            <!-- Unverified Users -->
            <div class="glass-card">
                <h2 class="text-xl font-bold text-primary mb-4">Unverified Users</h2>
                <div class="space-y-3">
                    <?php foreach($unverified_users as $user): ?>
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                        <div>
                            <div class="font-semibold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                            <div class="text-sm text-white/70"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        <a href="users.php?verify=<?php echo $user['id']; ?>" class="btn btn-sm bg-success hover:bg-success/80">
                            Verify
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="users.php" class="btn btn-outline w-full mt-4">Manage All Users</a>
            </div>
        </div>

    </main>

    <!-- Link to external JavaScript -->
    <script src="dashscript.js"></script>
    <script>
    // Sidebar toggle functionality
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    
    // Auto-close sidebar on mobile when clicking outside
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        
        if (window.innerWidth <= 1024 && 
            !sidebar.contains(event.target) && 
            !sidebarToggle.contains(event.target) &&
            sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    });
    </script>

</body>
</html>
<?php
// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../auth.php');
    exit;
}

require_once __DIR__ . '/../dashuser/database.php';

// Handle user verification
if (isset($_GET['verify'])) {
    $user_id = $_GET['verify'];
    
    try {
        $stmt = $conn->prepare("UPDATE members SET is_verified = TRUE WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        
        $_SESSION['message'] = "User verified successfully";
        $_SESSION['message_type'] = "success";
    } catch(Exception $e) {
        $_SESSION['message'] = "Error verifying user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: users.php");
    exit;
}

// Handle user status toggle
if (isset($_GET['toggle_status'])) {
    $user_id = $_GET['user_id'];
    
    try {
        // Get current status
        $stmt = $conn->prepare("SELECT is_active FROM members WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            $new_status = $user['is_active'] ? FALSE : TRUE;
            $stmt = $conn->prepare("UPDATE members SET is_active = :status WHERE id = :id");
            $stmt->execute([':status' => $new_status, ':id' => $user_id]);
            
            $status_text = $new_status ? "activated" : "deactivated";
            $_SESSION['message'] = "User $status_text successfully";
            $_SESSION['message_type'] = "success";
        }
    } catch(Exception $e) {
        $_SESSION['message'] = "Error updating user status: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: users.php");
    exit;
}

// Handle delete user
if (isset($_GET['delete'])) {
    $user_id = $_GET['user_id'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM members WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        
        $_SESSION['message'] = "User deleted successfully";
        $_SESSION['message_type'] = "success";
    } catch(Exception $e) {
        $_SESSION['message'] = "Error deleting user: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: users.php");
    exit;
}

// Get all users
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT * FROM members WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($status_filter)) {
    if ($status_filter == 'active') {
        $query .= " AND is_active = TRUE";
    } elseif ($status_filter == 'inactive') {
        $query .= " AND is_active = FALSE";
    } elseif ($status_filter == 'verified') {
        $query .= " AND is_verified = TRUE";
    } elseif ($status_filter == 'unverified') {
        $query .= " AND is_verified = FALSE";
    }
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Transpori Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            
            <a href="dashboard.php" class="" data-page="dashboard">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>

            <!-- System Management -->
            <div class="sidebar-section-title">System Management</div>
            <a href="users.php" class="active" data-page="users">
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
        
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="text-3xl font-extrabold">User Management</h1>
            <div class="flex items-center gap-4">
                <span class="text-white/70"><?php echo count($users); ?> users</span>
                <a href="?export=csv" class="btn btn-sm">
                    <i class="fas fa-download mr-2"></i> Export
                </a>
            </div>
        </div>

        <!-- Message Display -->
        <?php if(isset($_SESSION['message'])): ?>
        <div class="glass-card p-4 mb-6 <?php echo $_SESSION['message_type'] == 'success' ? 'bg-green-500/20' : 'bg-red-500/20'; ?>">
            <div class="flex justify-between items-center">
                <span><?php echo $_SESSION['message']; ?></span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-white/70 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        endif; 
        ?>

        <!-- Filters -->
        <div class="glass-card p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-2">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           class="admin-input" placeholder="Search users...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-2">Filter</label>
                    <select name="status" class="admin-select">
                        <option value="">All Users</option>
                        <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        <option value="verified" <?php echo $status_filter == 'verified' ? 'selected' : ''; ?>>Verified</option>
                        <option value="unverified" <?php echo $status_filter == 'unverified' ? 'selected' : ''; ?>>Unverified</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn flex-1">Filter</button>
                    <a href="users.php" class="btn btn-outline">Clear</a>
                </div>
            </form>
        </div>

        <!-- User Statistics -->
        <?php
        $active_count = $conn->query("SELECT COUNT(*) FROM members WHERE is_active = TRUE")->fetchColumn();
        $verified_count = $conn->query("SELECT COUNT(*) FROM members WHERE is_verified = TRUE")->fetchColumn();
        $total_count = $conn->query("SELECT COUNT(*) FROM members")->fetchColumn();
        ?>
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-green-500 mb-1"><?php echo $active_count; ?></div>
                <div class="text-sm text-white/70">Active Users</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-blue-500 mb-1"><?php echo $verified_count; ?></div>
                <div class="text-sm text-white/70">Verified</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-primary mb-1"><?php echo $total_count; ?></div>
                <div class="text-sm text-white/70">Total Users</div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glass-card p-0">
            <div class="overflow-x-auto">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td class="font-mono"><?php echo $user['id']; ?></td>
                            <td>
                                <div class="font-semibold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                <div class="text-xs text-white/50">ID: <?php echo $user['id']; ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if($user['is_active']): ?>
                                <span class="status-badge status-active">Active</span>
                                <?php else: ?>
                                <span class="status-badge status-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user['is_verified']): ?>
                                <span class="status-badge status-verified">Verified</span>
                                <?php else: ?>
                                <span class="status-badge status-unverified">Unverified</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <?php if(!$user['is_verified']): ?>
                                    <a href="users.php?verify=<?php echo $user['id']; ?>" 
                                       class="text-success hover:text-green-400" 
                                       title="Verify">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="users.php?toggle_status=1&user_id=<?php echo $user['id']; ?>"
                                       class="text-primary hover:text-primary-dark"
                                       title="<?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                        <i class="fas fa-power-off"></i>
                                    </a>
                                    
                                    <a href="users.php?delete=1&user_id=<?php echo $user['id']; ?>" 
                                       onclick="return confirm('Delete user <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>?')"
                                       class="text-red-500 hover:text-red-700"
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    
                                    <button onclick="showUserDetails(<?php echo $user['id']; ?>)"
                                            class="text-blue-400 hover:text-blue-300"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-white/50">
                                <i class="fas fa-users text-3xl mb-2"></i>
                                <div>No users found</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- User Details Modal -->
    <div id="user-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="glass-card max-w-2xl w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-primary" id="user-modal-title">User Details</h3>
                <button onclick="closeUserModal()" class="text-white/80 hover:text-white">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="user-modal-body">
                <!-- Dynamic content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="dashscript.js"></script>
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    
    function showUserDetails(userId) {
        // In a real app, you would fetch user details via AJAX
        document.getElementById('user-modal-title').textContent = `User #${userId} Details`;
        document.getElementById('user-modal-body').innerHTML = `
            <div class="space-y-4">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-primary"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold">User Name</h4>
                        <p class="text-white/70">user@example.com</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Member Since</div>
                        <div class="font-semibold"><?php echo date('M d, Y'); ?></div>
                    </div>
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Reports Submitted</div>
                        <div class="font-semibold">5</div>
                    </div>
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Experiences Shared</div>
                        <div class="font-semibold">3</div>
                    </div>
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Last Active</div>
                        <div class="font-semibold">Today</div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button onclick="closeUserModal()" class="btn btn-outline">Close</button>
                    <button class="btn bg-primary">Reset Password</button>
                </div>
            </div>
        `;
        document.getElementById('user-modal').style.display = 'flex';
    }
    
    function closeUserModal() {
        document.getElementById('user-modal').style.display = 'none';
    }
    </script>

</body>
</html>
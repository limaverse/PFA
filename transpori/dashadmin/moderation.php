<?php
// C:\xampp\htdocs\PFA\transpori\dashadmin\moderation.php
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

// Handle experience approval/rejection (support GET and POST)
if (isset($_REQUEST['action']) && isset($_REQUEST['id'])) {
    $experience_id = (int)$_REQUEST['id'];
    $action = $_REQUEST['action'];
    $admin_id = $_SESSION['admin_id'] ?? 1;

    try {
        if ($action === 'approve') {
            $stmt = $conn->prepare("
                UPDATE experiences 
                SET status = 'approved', 
                    admin_reviewed_at = NOW(), 
                    admin_reviewed_by = ?
                WHERE id = ?
            ");
            $stmt->execute([$admin_id, $experience_id]);

            $_SESSION['message'] = "Experience #$experience_id approved successfully";
            $_SESSION['message_type'] = "success";

        } elseif ($action === 'reject') {
            $rejection_reason = $_POST['rejection_reason'] ?? ($_REQUEST['rejection_reason'] ?? 'Content violates community guidelines');

            $stmt = $conn->prepare("
                UPDATE experiences 
                SET status = 'rejected', 
                    admin_reviewed_at = NOW(), 
                    admin_reviewed_by = ?,
                    rejection_reason = ?
                WHERE id = ?
            ");
            $stmt->execute([$admin_id, $rejection_reason, $experience_id]);

            $_SESSION['message'] = "Experience #$experience_id rejected";
            $_SESSION['message_type'] = "success";

        } elseif ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM experiences WHERE id = ?");
            $stmt->execute([$experience_id]);

            $_SESSION['message'] = "Experience #$experience_id deleted";
            $_SESSION['message_type'] = "success";
        }

        header("Location: moderation.php");
        exit;

    } catch(Exception $e) {
        $_SESSION['message'] = "Error: " . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = "error";
        header("Location: moderation.php");
        exit;
    }
}

// Get all experiences for moderation
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';

$query = "SELECT e.*, 
                 CONCAT(m.first_name, ' ', m.last_name) as user_name,
                 m.email as user_email,
                 c.name as category_name
          FROM experiences e 
          LEFT JOIN members m ON e.member_id = m.id
          LEFT JOIN categories c ON e.category_id = c.id
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (e.title LIKE :search OR e.content LIKE :search OR m.first_name LIKE :search OR m.last_name LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($status_filter)) {
    $query .= " AND e.status = :status";
    $params[':status'] = $status_filter;
}

$query .= " ORDER BY e.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$experiences = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Get counts (use PDO fetchColumn)
$pending_count = (int)$conn->query("SELECT COUNT(*) FROM experiences WHERE status = 'pending'")->fetchColumn();
$approved_count = (int)$conn->query("SELECT COUNT(*) FROM experiences WHERE status = 'approved'")->fetchColumn();
$rejected_count = (int)$conn->query("SELECT COUNT(*) FROM experiences WHERE status = 'rejected'")->fetchColumn();
$total_count = (int)$conn->query("SELECT COUNT(*) FROM experiences")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Moderation | Transpori Admin</title>
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
            
            <a href="dashboard.php" data-page="dashboard">
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
            <a href="moderation.php" class="active" data-page="moderation">
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
            <h1 class="text-3xl font-extrabold">Content Moderation</h1>
            <div class="flex items-center gap-4">
                <span class="text-white/70"><?php echo count($experiences); ?> experiences</span>
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
                           class="admin-input" placeholder="Search experiences...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-2">Status</label>
                    <select name="status" class="admin-select">
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending Review</option>
                        <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status_filter == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn flex-1">Filter</button>
                    <a href="moderation.php" class="btn btn-outline">Clear</a>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-primary mb-1"><?php echo $total_count; ?></div>
                <div class="text-sm text-white/70">Total Experiences</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-yellow-500 mb-1"><?php echo $pending_count; ?></div>
                <div class="text-sm text-white/70">Pending Review</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-green-500 mb-1"><?php echo $approved_count; ?></div>
                <div class="text-sm text-white/70">Approved</div>
            </div>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold text-red-500 mb-1"><?php echo $rejected_count; ?></div>
                <div class="text-sm text-white/70">Rejected</div>
            </div>
        </div>

        <!-- Experiences Table -->
        <div class="glass-card p-0">
            <div class="overflow-x-auto">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>User</th>
                            <th>Category</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($experiences as $exp): ?>
                        <tr>
                            <td class="font-mono">#<?php echo $exp['id']; ?></td>
                            <td>
                                <div class="font-semibold"><?php echo htmlspecialchars(substr($exp['title'], 0, 50)); ?><?php echo strlen($exp['title']) > 50 ? '...' : ''; ?></div>
                                <div class="text-xs text-white/50">
                                    <?php echo htmlspecialchars($exp['transport_type']); ?> • 
                                    <?php echo htmlspecialchars($exp['experience_type']); ?>
                                </div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($exp['user_name'] ?: 'User #' . $exp['member_id']); ?></div>
                                <div class="text-xs text-white/50"><?php echo htmlspecialchars($exp['user_email']); ?></div>
                            </td>
                            <td><?php echo htmlspecialchars($exp['category_name'] ?: 'Uncategorized'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($exp['created_at'])); ?></td>
                            <td>
                                <?php if($exp['status'] == 'pending'): ?>
                                <span class="status-badge bg-yellow-500/20 text-yellow-400">Pending</span>
                                <?php elseif($exp['status'] == 'approved'): ?>
                                <span class="status-badge bg-green-500/20 text-green-400">Approved</span>
                                <?php elseif($exp['status'] == 'rejected'): ?>
                                <span class="status-badge bg-red-500/20 text-red-400">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <button onclick="viewExperience(<?php echo $exp['id']; ?>)" 
                                            class="text-primary hover:text-primary-dark"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <?php if($exp['status'] == 'pending'): ?>
                                    <button onclick="approveExperience(<?php echo $exp['id']; ?>)" 
                                            class="text-success hover:text-green-400"
                                            title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="showRejectForm(<?php echo $exp['id']; ?>)" 
                                            class="text-danger hover:text-red-400"
                                            title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="deleteExperience(<?php echo $exp['id']; ?>)" 
                                            class="text-red-500 hover:text-red-700"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($experiences)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-white/50">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <div>No experiences found</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Experience Details Modal -->
    <div id="experience-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="glass-card max-w-3xl w-full max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-primary" id="exp-modal-title">Experience Details</h3>
                <button onclick="closeExperienceModal()" class="text-white/80 hover:text-white">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="experience-modal-body">
                <!-- Dynamic content loaded via AJAX -->
            </div>
        </div>
    </div>

    <!-- Reject Experience Modal -->
    <div id="reject-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="glass-card max-w-md w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-primary">Reject Experience</h3>
                <button onclick="closeRejectModal()" class="text-white/80 hover:text-white">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="reject-form" method="POST" action="moderation.php">
                <input type="hidden" name="id" id="reject-exp-id">
                <input type="hidden" name="action" value="reject">
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-white/70 mb-2">Rejection Reason</label>
                    <textarea name="rejection_reason" class="admin-input h-32" 
                              placeholder="Provide a reason for rejection (this will be shown to the user)..."
                              required></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()" class="btn btn-outline flex-1">Cancel</button>
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 flex-1">
                        <i class="fas fa-times mr-2"></i> Reject Experience
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="dashscript.js"></script>
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }
    
    function viewExperience(id) {
        fetch(`../dashuser/ajax/get_experience.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const exp = data.experience;
                    document.getElementById('exp-modal-title').textContent = `Experience #${exp.id}`;
                    
                    let statusBadge = '';
                    if (exp.status === 'pending') {
                        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-500/20 text-yellow-400 mb-4">Pending Review</span>';
                    } else if (exp.status === 'approved') {
                        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-500/20 text-green-400 mb-4">Approved</span>';
                    } else if (exp.status === 'rejected') {
                        statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-500/20 text-red-400 mb-4">Rejected</span>';
                    }
                    
                    document.getElementById('experience-modal-body').innerHTML = `
                        <div class="space-y-4">
                            ${statusBadge}
                            
                            <div>
                                <div class="text-sm text-white/50 mb-1">Title</div>
                                <div class="text-lg font-bold">${exp.title}</div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white/5 p-3 rounded">
                                    <div class="text-sm text-white/50">Author</div>
                                    <div class="font-semibold">${exp.author_name || 'Unknown User'}</div>
                                </div>
                                <div class="bg-white/5 p-3 rounded">
                                    <div class="text-sm text-white/50">Category</div>
                                    <div class="font-semibold">${exp.category_name || 'Uncategorized'}</div>
                                </div>
                                <div class="bg-white/5 p-3 rounded">
                                    <div class="text-sm text-white/50">Transport Type</div>
                                    <div class="font-semibold">${exp.transport_type}</div>
                                </div>
                                <div class="bg-white/5 p-3 rounded">
                                    <div class="text-sm text-white/50">Experience Type</div>
                                    <div class="font-semibold">${exp.experience_type}</div>
                                </div>
                            </div>
                            
                            ${exp.location ? `
                            <div>
                                <div class="text-sm text-white/50 mb-1">Location</div>
                                <div class="font-semibold">${exp.location}</div>
                            </div>` : ''}
                            
                            <div>
                                <div class="text-sm text-white/50 mb-2">Content</div>
                                <div class="bg-white/5 p-4 rounded whitespace-pre-line">${exp.content}</div>
                            </div>
                            
                            <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                                <button onclick="closeExperienceModal()" class="btn btn-outline">Close</button>
                                ${exp.status === 'pending' ? `
                                <button onclick="approveExperience(${exp.id})" class="btn bg-green-500 hover:bg-green-600">
                                    <i class="fas fa-check mr-2"></i> Approve
                                </button>
                                <button onclick="showRejectForm(${exp.id})" class="btn bg-red-500 hover:bg-red-600">
                                    <i class="fas fa-times mr-2"></i> Reject
                                </button>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    document.getElementById('experience-modal').style.display = 'flex';
                }
            });
    }
    
    function closeExperienceModal() {
        document.getElementById('experience-modal').style.display = 'none';
    }
    
    function approveExperience(id) {
        if (confirm('Approve this experience?')) {
            window.location.href = `moderation.php?action=approve&id=${id}`;
        }
    }
    
    function showRejectForm(id) {
        document.getElementById('reject-exp-id').value = id;
        document.getElementById('reject-modal').style.display = 'flex';
        closeExperienceModal();
    }
    
    function closeRejectModal() {
        document.getElementById('reject-modal').style.display = 'none';
    }
    
    function deleteExperience(id) {
        if (confirm('Delete this experience? This action cannot be undone.')) {
            window.location.href = `moderation.php?action=delete&id=${id}`;
        }
    }
    </script>

</body>
</html>
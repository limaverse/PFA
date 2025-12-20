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

// Handle report status update
if (isset($_GET['update_status'])) {
    $report_id = $_GET['report_id'];
    $status = $_GET['status'];
    
    try {
        $stmt = $conn->prepare("UPDATE reports SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $report_id]);
        
        $_SESSION['message'] = "Report #$report_id status updated to $status";
        $_SESSION['message_type'] = "success";
    } catch(Exception $e) {
        $_SESSION['message'] = "Error updating report: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: reports.php");
    exit;
}

// Handle delete report
if (isset($_GET['delete'])) {
    $report_id = $_GET['report_id'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM reports WHERE id = :id");
        $stmt->execute([':id' => $report_id]);
        
        $_SESSION['message'] = "Report #$report_id deleted successfully";
        $_SESSION['message_type'] = "success";
    } catch(Exception $e) {
        $_SESSION['message'] = "Error deleting report: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: reports.php");
    exit;
}

// Get all reports
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT r.*, CONCAT(m.first_name, ' ', m.last_name) as user_name 
          FROM reports r 
          LEFT JOIN members m ON r.member_id = m.id 
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (r.type LIKE :search OR r.location LIKE :search OR r.description LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($status_filter)) {
    $query .= " AND r.status = :status";
    $params[':status'] = $status_filter;
}

$query .= " ORDER BY r.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get reports count by status
$status_counts = $conn->query("SELECT status, COUNT(*) as count FROM reports GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Management | Transpori Admin</title>
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

    <!-- Sidebar Navigation (Same as dashboard.php) -->
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
            <a href="users.php" data-page="users">
                <i class="fas fa-users-cog w-5"></i> User Management
            </a>
            <a href="system_admin.php" data-page="system-admin">
                <i class="fas fa-cogs w-5"></i> System Administration
            </a>
            
            <!-- Content & Reports -->
            <div class="sidebar-section-title">Content & Safety</div>
            <a href="reports.php" class="active" data-page="reports">
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
            <h1 class="text-3xl font-extrabold">Report Management</h1>
            <div class="flex items-center gap-4">
                <span class="text-white/70"><?php echo count($reports); ?> reports</span>
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
                           class="admin-input" placeholder="Search reports...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-2">Status</label>
                    <select name="status" class="admin-select">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="accepted" <?php echo $status_filter == 'accepted' ? 'selected' : ''; ?>>Accepted</option>
                        <option value="refused" <?php echo $status_filter == 'refused' ? 'selected' : ''; ?>>Refused</option>
                        <option value="resolved" <?php echo $status_filter == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn flex-1">Filter</button>
                    <a href="reports.php" class="btn btn-outline">Clear</a>
                </div>
            </form>
        </div>

        <!-- Status Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <?php 
            $status_colors = [
                'pending' => 'bg-yellow-500',
                'accepted' => 'bg-green-500', 
                'refused' => 'bg-red-500',
                'resolved' => 'bg-blue-500'
            ];
            
            foreach($status_counts as $stat): 
                $color = $status_colors[$stat['status']] ?? 'bg-gray-500';
            ?>
            <div class="glass-card p-4 text-center">
                <div class="text-2xl font-bold <?php echo $color; ?> text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-2">
                    <?php echo $stat['count']; ?>
                </div>
                <div class="text-sm font-medium"><?php echo ucfirst($stat['status']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Reports Table -->
        <div class="glass-card p-0">
            <div class="overflow-x-auto">
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Reported By</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reports as $report): ?>
                        <tr>
                            <td class="font-mono">#<?php echo $report['id']; ?></td>
                            <td><?php echo htmlspecialchars($report['type']); ?></td>
                            <td><?php echo htmlspecialchars($report['location']); ?></td>
                            <td>
                                <?php 
                                if($report['anonymous']) {
                                    echo 'Anonymous';
                                } else {
                                    echo htmlspecialchars($report['user_name'] ?: 'User #' . $report['member_id']);
                                }
                                ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($report['created_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $report['status']; ?>">
                                    <?php echo ucfirst($report['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <button onclick="showReportDetails(<?php echo $report['id']; ?>)" 
                                            class="text-primary hover:text-primary-dark">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="reports.php?update_status=1&report_id=<?php echo $report['id']; ?>&status=accepted"
                                       class="text-success hover:text-green-400">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="reports.php?update_status=1&report_id=<?php echo $report['id']; ?>&status=refused"
                                       class="text-danger hover:text-red-400">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    <a href="reports.php?delete=1&report_id=<?php echo $report['id']; ?>" 
                                       onclick="return confirm('Delete report #<?php echo $report['id']; ?>?')"
                                       class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($reports)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-white/50">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <div>No reports found</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination would go here -->
        </div>

    </main>

    <!-- Report Details Modal -->
    <div id="report-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="glass-card max-w-3xl w-full max-h-[80vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-primary" id="report-modal-title">Report Details</h3>
                <button onclick="closeReportModal()" class="text-white/80 hover:text-white">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="report-modal-body">
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
    
    function showReportDetails(reportId) {
        // In a real app, you would fetch report details via AJAX
        // For now, we'll show a placeholder
        document.getElementById('report-modal-title').textContent = `Report #${reportId} Details`;
        document.getElementById('report-modal-body').innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Status</div>
                        <div class="font-semibold">Pending</div>
                    </div>
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Type</div>
                        <div class="font-semibold">Harassment</div>
                    </div>
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Location</div>
                        <div class="font-semibold">Bus Station</div>
                    </div>
                    <div class="bg-white/5 p-3 rounded">
                        <div class="text-sm text-white/50">Date Reported</div>
                        <div class="font-semibold"><?php echo date('M d, Y'); ?></div>
                    </div>
                </div>
                
                <div>
                    <div class="text-sm text-white/50 mb-2">Description</div>
                    <div class="bg-white/5 p-4 rounded">
                        This is a detailed description of the reported incident. The user reported inappropriate behavior on the bus route 45.
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                    <button onclick="closeReportModal()" class="btn btn-outline">Close</button>
                    <button class="btn bg-success">Accept Report</button>
                    <button class="btn bg-danger">Refuse Report</button>
                </div>
            </div>
        `;
        document.getElementById('report-modal').style.display = 'flex';
    }
    
    function closeReportModal() {
        document.getElementById('report-modal').style.display = 'none';
    }
    </script>

</body>
</html>
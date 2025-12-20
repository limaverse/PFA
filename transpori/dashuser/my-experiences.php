<?php
// C:\xampp\htdocs\PFA\transpori\dashuser\my-experiences.php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PFA/transpori/login...signup/logsign.php?tab=login');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';
$success_message = '';
$error_message = '';

// GET USER'S EXPERIENCES FROM DATABASE
try {
    $stmt = $conn->prepare("
        SELECT e.*, 
               c.name as category_name,
               CASE 
                   WHEN e.status = 'pending' THEN 'Pending Review'
                   WHEN e.status = 'approved' THEN 'Approved'
                   WHEN e.status = 'rejected' THEN 'Rejected'
                   ELSE 'Unknown'
               END as status_text,
               CASE e.status
                   WHEN 'pending' THEN 'bg-yellow-500/20 text-yellow-400'
                   WHEN 'approved' THEN 'bg-green-500/20 text-green-400'
                   WHEN 'rejected' THEN 'bg-red-500/20 text-red-400'
                   ELSE 'bg-gray-500/20 text-gray-400'
               END as status_class
        FROM experiences e 
        LEFT JOIN categories c ON e.category_id = c.id 
        WHERE e.member_id = ? 
        ORDER BY e.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $user_experiences = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    die("Error fetching experiences: " . htmlspecialchars($e->getMessage()));
}

// COUNT STATS
$total_experiences = count($user_experiences);
$approved_experiences = count(array_filter($user_experiences, fn($exp) => ($exp['status'] ?? '') == 'approved'));
$pending_experiences = count(array_filter($user_experiences, fn($exp) => ($exp['status'] ?? '') == 'pending'));
$rejected_experiences = count(array_filter($user_experiences, fn($exp) => ($exp['status'] ?? '') == 'rejected'));

// Handle delete request
if (isset($_GET['delete'])) {
    $experience_id = (int)$_GET['delete'];
    
    // Verify ownership
    $stmt = $conn->prepare("SELECT member_id FROM experiences WHERE id = ?");
    $stmt->execute([$experience_id]);
    $exp = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($exp && $exp['member_id'] == $user_id) {
        $stmt = $conn->prepare("DELETE FROM experiences WHERE id = ?");
        $stmt->execute([$experience_id]);
        
        // Redirect to refresh
        header('Location: my-experiences.php?deleted=1');
        exit();
    } else {
        $error_message = "Unable to delete experience.";
    }
}

if (isset($_GET['deleted'])) {
    $success_message = "Experience deleted successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | My Experiences</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/PFA/transpori/home/css/transpori css.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard-page">
<header>
    <div class="container">
        <nav class="navbar">
            <a href="index.php" class="logo">
                <i class="fas fa-shield-alt"></i> Transpori
            </a>
            <div class="auth-buttons">
                <a href="#" class="profile-btn">
                    <div class="profile-avatar"><?php echo htmlspecialchars(substr($user_name,0,2)); ?></div>
                    <span><?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                </a>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            </div>
        </nav>
    </div>
</header>

<main class="main-content">
    <div class="dashboard-header">
        <div>
            <h1 class="text-3xl font-extrabold">My Experiences</h1>
            <p class="text-text-secondary">Manage and track your shared experiences</p>
        </div>
        <button class="btn" onclick="window.location.href='share.php'">
            <i class="fas fa-plus mr-2"></i> New Experience
        </button>
    </div>

    <?php if ($success_message): ?>
    <div class="glass-card bg-green-500/10 border-green-500/20 mb-6">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-400 mr-3"></i>
            <div class="text-green-300"><?php echo htmlspecialchars($success_message); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="glass-card bg-red-500/10 border-red-500/20 mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
            <div class="text-red-300"><?php echo htmlspecialchars($error_message); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="glass-card mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 rounded-lg bg-glass-bg border border-glass-border">
                <div class="text-sm text-text-secondary">Total</div>
                <div class="text-2xl font-bold"><?php echo $total_experiences; ?></div>
            </div>
            <div class="text-center p-4 rounded-lg bg-glass-bg border border-glass-border">
                <div class="text-sm text-text-secondary">Approved</div>
                <div class="text-2xl font-bold"><?php echo $approved_experiences; ?></div>
            </div>
            <div class="text-center p-4 rounded-lg bg-glass-bg border border-glass-border">
                <div class="text-sm text-text-secondary">Pending</div>
                <div class="text-2xl font-bold"><?php echo $pending_experiences; ?></div>
            </div>
            <div class="text-center p-4 rounded-lg bg-glass-bg border border-glass-border">
                <div class="text-sm text-text-secondary">Rejected</div>
                <div class="text-2xl font-bold"><?php echo $rejected_experiences; ?></div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <?php if (empty($user_experiences)): ?>
            <div class="glass-card p-6 text-center text-text-secondary">
                You haven't shared any experiences yet. <a href="share.php" class="text-primary">Share one now</a>.
            </div>
        <?php else: ?>
            <?php foreach ($user_experiences as $experience): ?>
                <div class="glass-card p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-bold"><?php echo htmlspecialchars($experience['title'] ?? 'Untitled'); ?></div>
                            <div class="text-sm text-text-secondary">
                                <?php echo date('M d, Y', strtotime($experience['created_at'] ?? 'now')); ?>
                                <?php if (!empty($experience['category_name'])): ?> • <?php echo htmlspecialchars($experience['category_name']); ?><?php endif; ?>
                            </div>
                        </div>
                        <div class="<?php echo htmlspecialchars($experience['status_class'] ?? 'bg-gray-500/20 text-gray-400'); ?> px-3 py-1 rounded-full text-sm">
                            <?php echo htmlspecialchars($experience['status_text'] ?? ucfirst($experience['status'] ?? 'unknown')); ?>
                        </div>
                    </div>

                    <div class="mt-3 text-sm text-text-secondary">
                        <?php echo nl2br(htmlspecialchars(substr($experience['content'] ?? '', 0, 300))); ?><?php echo (strlen($experience['content'] ?? '') > 300) ? '...' : ''; ?>
                        <?php if (!empty($experience['location'])): ?>
                        <div class="mt-2 text-sm">
                            <i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($experience['location']); ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-4 flex gap-3">
                        <a href="view-experience.php?id=<?php echo $experience['id']; ?>" class="btn btn-outline">View</a>
                        <a href="share.php?edit=<?php echo $experience['id']; ?>" class="btn">Edit</a>
                        <a href="?delete=<?php echo $experience['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this experience?');">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
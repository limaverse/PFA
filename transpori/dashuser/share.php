<?php
// C:\xampp\htdocs\PFA\transpori\dashuser\share.php
session_start();
require_once 'database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PFA/transpori/login...signup/logsign.php?tab=login');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Get categories for dropdown
try {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE type = 'experience' AND is_active = TRUE ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $error_message = "Could not load categories: " . htmlspecialchars($e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $transport_type = $_POST['transport_type'] ?? 'other';
    $experience_type = $_POST['experience_type'] ?? 'positive';
    $is_public = isset($_POST['is_public']) ? 1 : 0;
    $location = trim($_POST['location'] ?? '');
    
    if ($title === '' || $content === '' || $category_id <= 0) {
        $error_message = "Please fill in all required fields!";
    } else {
        // verify selected category exists
        $cat_ok = false;
        foreach ($categories as $c) {
            if ((int)$c['id'] === $category_id) { $cat_ok = true; break; }
        }
        if (! $cat_ok) {
            $error_message = "Invalid category selected.";
        } else {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO experiences 
                    (member_id, category_id, title, content, location, transport_type, experience_type, is_public, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
                ");
                
                $stmt->execute([
                    $user_id,
                    $category_id,
                    $title,
                    $content,
                    $location,
                    $transport_type,
                    $experience_type,
                    $is_public
                ]);
                
                // redirect to avoid duplicate form submission
                header('Location: share.php?submitted=1');
                exit();
                
            } catch (PDOException $e) {
                $error_message = "Error: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}

if (isset($_GET['submitted'])) {
    $success_message = "Experience submitted successfully! It will be reviewed by moderators before appearing publicly.";
}

$user_name = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Share Experience</title>
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
            <a href="index.php" class="logo"><i class="fas fa-shield-alt"></i> Transpori</a>
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
            <h1 class="text-3xl font-extrabold">Share Experience</h1>
            <p class="text-text-secondary">Help others by sharing your transportation journey</p>
            <div class="mt-2 glass-card p-3 bg-yellow-500/10 border-yellow-500/20">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-400 mr-3"></i>
                    <div class="text-sm text-yellow-300">
                        Experiences are reviewed before appearing publicly.
                    </div>
                </div>
            </div>
        </div>
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

    <div class="glass-card p-6">
        <form method="POST" id="experience-form">
            <div class="form-group mb-6">
                <label class="block text-text-secondary mb-2">Title *</label>
                <input type="text" name="title" class="form-input" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" placeholder="Brief title" required>
            </div>

            <div class="form-group mb-6">
                <label class="block text-text-secondary mb-2">Category *</label>
                <select name="category_id" class="form-input" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="form-group">
                    <label class="block text-text-secondary mb-2">Transport Type</label>
                    <select name="transport_type" class="form-input">
                        <option value="bus" <?php echo ($_POST['transport_type'] ?? '') == 'bus' ? 'selected' : ''; ?>>Bus</option>
                        <option value="train" <?php echo ($_POST['transport_type'] ?? '') == 'train' ? 'selected' : ''; ?>>Train</option>
                        <option value="metro" <?php echo ($_POST['transport_type'] ?? '') == 'metro' ? 'selected' : ''; ?>>Metro</option>
                        <option value="taxi" <?php echo ($_POST['transport_type'] ?? '') == 'taxi' ? 'selected' : ''; ?>>Taxi</option>
                        <option value="other" <?php echo ($_POST['transport_type'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="block text-text-secondary mb-2">Experience Type</label>
                    <select name="experience_type" class="form-input">
                        <option value="positive" <?php echo ($_POST['experience_type'] ?? 'positive') == 'positive' ? 'selected' : ''; ?>>Positive</option>
                        <option value="negative" <?php echo ($_POST['experience_type'] ?? '') == 'negative' ? 'selected' : ''; ?>>Negative</option>
                        <option value="suggestion" <?php echo ($_POST['experience_type'] ?? '') == 'suggestion' ? 'selected' : ''; ?>>Suggestion</option>
                    </select>
                </div>
            </div>

            <div class="form-group mb-6">
                <label class="block text-text-secondary mb-2">Location (Optional)</label>
                <input type="text" name="location" class="form-input" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" placeholder="e.g., Downtown Station">
            </div>

            <div class="form-group mb-6">
                <label class="block text-text-secondary mb-2">Your Experience *</label>
                <textarea name="content" class="form-textarea" rows="6" required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
            </div>

            <div class="form-group mb-6">
                <label class="flex items-center text-text-secondary">
                    <input type="checkbox" name="is_public" class="mr-2" <?php echo isset($_POST['is_public']) ? 'checked' : ''; ?>>
                    <span>Make this experience public</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="button" class="btn btn-outline flex-1" onclick="window.location.href='index.php'">Cancel</button>
                <button type="submit" class="btn flex-1"><i class="fas fa-paper-plane mr-2"></i> Submit for Review</button>
            </div>
        </form>
    </div>
</main>
</body>
</html>
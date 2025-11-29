<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Link to external CSS and javascript -->
<link rel="stylesheet" href="dashcss.css">
<script src="dashscript.js"></script>
</head>
<body>
    
    <!-- Sidebar Toggle Button for Mobile -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <a href="#dashboard" class="sidebar-logo">
            <i class="fas fa-shield-alt"></i>
            Admin Control Panel
        </a>
        
        <nav class="sidebar-nav flex-grow">
            
            <a href="#dashboard" class="active" data-page="dashboard">
                <i class="fas fa-chart-line w-5"></i> Dashboard
            </a>

            <!-- System Management -->
            <div class="sidebar-section-title">System Management</div>
            <a href="#users" data-page="users">
                <i class="fas fa-users-cog w-5"></i> User Management
            </a>
            <a href="#system-admin" data-page="system-admin">
                <i class="fas fa-cogs w-5"></i> System Administration
            </a>
            
            <!-- Content & Reports -->
            <div class="sidebar-section-title">Content & Safety</div>
            <a href="#reports" data-page="reports">
                <i class="fas fa-exclamation-triangle w-5"></i> Report Management
            </a>
            <a href="#moderation" data-page="moderation">
                <i class="fas fa-th-list w-5"></i> Content Moderation
            </a>
            <a href="#content-creation" data-page="content-creation">
                <i class="fas fa-pen-fancy w-5"></i> Content Creation
            </a>
            <a href="#categories" data-page="categories">
                <i class="fas fa-tags w-5"></i> Categories Management
            </a>

            <!-- Public Services -->
            <div class="sidebar-section-title">Public Services</div>
            <a href="#emergency-contacts" data-page="emergency-contacts">
                <i class="fas fa-headset w-5"></i> Emergency Contacts
            </a>
            <a href="#events" data-page="events">
                <i class="fas fa-calendar-alt w-5"></i> Events Management
            </a>

            <!-- Analytics -->
            <div class="sidebar-section-title">Analytics</div>
            <a href="#stats" data-page="stats">
                <i class="fas fa-chart-bar w-5"></i> Statistics & Analytics
            </a>
            
        </nav>
        
        <!-- Logout Button -->
        <div class="mt-auto pt-4 border-t border-white/10">
            <a href="C:\xampp\htdocs\PFA\transpori\login...signup\log sign.html" class="sidebar-nav-item text-red-400 hover:text-red-300">
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
                    <span class="hidden sm:inline font-semibold">Jane Doe</span>
                </button>
            </div>
        </div>

        <!-- Dynamic Content Area -->
        <div id="page-content">
            <!-- Content will be rendered here by JavaScript -->
        </div>

    </main>

    <!-- Modal for details/actions -->
    <div id="detail-modal" class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="glass-card max-w-2xl w-full transform transition-all scale-95 duration-300" id="modal-content-container">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-primary" id="modal-title">Details</h3>
                <button class="text-white/80 hover:text-white" onclick="closeModal()">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="modal-body" class="text-white/80 space-y-3">
                <!-- Dynamic content goes here -->
            </div>
            <div id="modal-actions" class="mt-6 flex justify-end gap-3">
                <!-- Action buttons -->
            </div>
        </div>
    </div>

</body>
</html>
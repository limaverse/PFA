// --- Mock Data ---
let incidentReports = [
    { id: 1001, type: 'Harassment', location: 'Bus', date: '2025-11-25', status: 'pending', user: 'Alice J.', anonymous: false, description: 'Aggressive behavior towards a female passenger near the back of the bus.' },
    { id: 1002, type: 'Condition', location: 'Metro', date: '2025-11-24', status: 'accepted', user: 'Bob S.', anonymous: false, description: 'Broken door lock on a coach. Photos attached.' },
    { id: 1003, type: 'Theft', location: 'TGM', date: '2025-11-23', status: 'refused', user: 'Anon', anonymous: true, description: 'Witnessed ticket agent accepting cash without issuing a ticket.' },
    { id: 1004, type: 'Complaint', location: 'Bus', date: '2025-11-22', status: 'pending', user: 'Eve P.', anonymous: false, description: 'Driver using phone while driving and speeding.' },
    { id: 1005, type: 'Maintenance', location: 'Metro', date: '2025-11-21', status: 'accepted', user: 'Charlie B.', anonymous: false, description: 'Air conditioning not working in carriage 3.' },
    { id: 1006, type: 'Harassment', location: 'TGM', date: '2025-11-20', status: 'pending', user: 'Anon', anonymous: true, description: 'Verbal harassment reported by passenger.' },
    { id: 1007, type: 'Condition', location: 'Bus', date: '2025-11-19', status: 'accepted', user: 'David L.', anonymous: false, description: 'Seat belts damaged in multiple seats.' },
    { id: 1008, type: 'Theft', location: 'Metro', date: '2025-11-18', status: 'pending', user: 'Anon', anonymous: true, description: 'Personal belongings stolen from bag.' },
    { id: 1009, type: 'Complaint', location: 'Bus', date: '2025-11-17', status: 'refused', user: 'Frank M.', anonymous: false, description: 'Driver refused to stop at requested station.' },
    { id: 1010, type: 'Maintenance', location: 'TGM', date: '2025-11-16', status: 'accepted', user: 'Grace K.', anonymous: false, description: 'Lights flickering in carriage 2.' },
    { id: 1011, type: 'Harassment', location: 'Metro', date: '2025-11-15', status: 'pending', user: 'Anon', anonymous: true, description: 'Unwanted physical contact reported.' },
    { id: 1012, type: 'Condition', location: 'Bus', date: '2025-11-14', status: 'accepted', user: 'Henry N.', anonymous: false, description: 'Brakes making unusual noise.' },
    { id: 1013, type: 'Theft', location: 'TGM', date: '2025-11-13', status: 'refused', user: 'Anon', anonymous: true, description: 'Phone stolen from pocket.' },
    { id: 1014, type: 'Complaint', location: 'Metro', date: '2025-11-12', status: 'pending', user: 'Ivy O.', anonymous: false, description: 'Overcrowding during peak hours.' },
    { id: 1015, type: 'Maintenance', location: 'Bus', date: '2025-11-11', status: 'accepted', user: 'Jack P.', anonymous: false, description: 'Wipers not working properly.' }
];

let users = [
    { id: 201, name: 'Alice Johnson', email: 'alice@transpori.com', status: 'active', verified: true, posts: 5 },
    { id: 202, name: 'Bob Smith', email: 'bob@transpori.com', status: 'active', verified: false, posts: 1 },
    { id: 203, name: 'Charlie Brown', email: 'charlie@transpori.com', status: 'inactive', verified: true, posts: 0 },
    { id: 204, name: 'Diana Prince', email: 'diana@transpori.com', status: 'active', verified: true, posts: 12 },
    { id: 205, name: 'Edward Wilson', email: 'edward@transpori.com', status: 'active', verified: false, posts: 3 }
];

let categories = [
    { id: 1, type: 'Report', name: 'Harassment', enabled: true },
    { id: 2, type: 'Report', name: 'Condition', enabled: true },
    { id: 3, type: 'Report', name: 'Theft', enabled: true },
    { id: 4, type: 'Report', name: 'Complaint', enabled: true },
    { id: 5, type: 'Report', name: 'Maintenance', enabled: true }
];

let emergencyContacts = [
    { id: 1, name: 'Tunisian Police Emergency', number: '197', category: 'emergency', description: 'Immediate police assistance.' },
    { id: 2, name: 'Transtu Support Line', number: '71900000', category: 'transport', description: 'Transtu operator support.' },
    { id: 3, name: 'Green Line Safety', number: '80101010', category: 'green_line', description: 'Dedicated line for safety reports.' },
    { id: 4, name: 'National Guard', number: '71717171', category: 'emergency', description: 'National security emergencies.' },
    { id: 5, name: 'Civil Protection', number: '198', category: 'emergency', description: 'Fire and rescue services.' }
];

// Global state for simplicity
let currentPage = 'dashboard';

// --- Layout & Navigation Functions ---

document.addEventListener('DOMContentLoaded', () => {
    // Initial render
    renderPage(currentPage);
    setupSidebarNav();
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

function setupSidebarNav() {
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.sidebar-nav a').forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            const newPage = link.dataset.page;
            if (newPage) {
                currentPage = newPage;
                renderPage(newPage);
                // Close sidebar on mobile after navigation
                if (window.innerWidth <= 1024) {
                    toggleSidebar();
                }
            }
        });
    });
}

function renderPage(page) {
    const contentDiv = document.getElementById('page-content');
    contentDiv.innerHTML = '';
    
    switch (page) {
        case 'dashboard':
            renderDashboard(contentDiv);
            break;
        case 'reports':
            renderReportManagement(contentDiv);
            break;
        case 'users':
            renderUserManagement(contentDiv);
            break;
        case 'moderation':
            renderContentModeration(contentDiv);
            break;
        case 'emergency-contacts':
            renderEmergencyContacts(contentDiv);
            break;
        case 'categories':
            renderCategoriesManagement(contentDiv);
            break;
        case 'stats':
            renderStatisticsAnalytics(contentDiv);
            break;
        case 'events':
            renderEventsManagement(contentDiv);
            break;
        case 'system-admin':
            renderSystemAdministration(contentDiv);
            break;
        case 'content-creation':
            renderContentCreation(contentDiv);
            break;
        default:
            renderDashboard(contentDiv);
    }
}

// --- Helper Functions ---

function createStatCard(label, value, iconClass, gradient) {
    return `
        <div class="glass-card stat-card hover:translate-y-0">
            <div class="stat-icon" style="background: ${gradient};">
                <i class="${iconClass}"></i>
            </div>
            <div>
                <div class="stat-value">${value}</div>
                <div class="stat-label">${label}</div>
            </div>
        </div>
    `;
}

function getStatusBadge(status) {
    const normalizedStatus = status.toLowerCase().replace(/\s/g, '-');
    return `<span class="status-badge status-${normalizedStatus}">${status}</span>`;
}

// --- Page Rendering Functions ---

function renderDashboard(target) {
    const pendingReports = incidentReports.filter(r => r.status === 'pending').length;
    const activeUsers = users.filter(u => u.status === 'active').length;

    target.innerHTML = `
        <div class="section-title"><h2>Dashboard & Key Metrics</h2></div>
        <div class="stats-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            ${createStatCard('Total Reports', incidentReports.length, 'fas fa-shield-alt', 'linear-gradient(135deg, var(--primary), var(--secondary))')}
            ${createStatCard('Pending Reports', pendingReports, 'fas fa-exclamation-triangle', 'linear-gradient(135deg, #facc15, #f59e0b)')}
            ${createStatCard('Active Users', activeUsers, 'fas fa-users', 'linear-gradient(135deg, var(--accent), #4ade80)')}
            ${createStatCard('User Posts', users.reduce((sum, user) => sum + user.posts, 0), 'fas fa-comment', 'linear-gradient(135deg, #ef4444, #f97316)')}
        </div>
        
        <div class="section-title mt-4"><h2>Report Management: Pending Review</h2></div>
        <div class="glass-card p-0 mb-8">
            ${createReportTable(incidentReports.filter(r => r.status === 'pending').slice(0, 5))}
        </div>
        
        <div class="section-title mt-4"><h2>User Management: Unverified Profiles</h2></div>
        <div class="glass-card p-0">
            ${createUserTable(users.filter(u => !u.verified))}
        </div>
    `;
}

function createReportTable(reports) {
    const rows = reports.map(report => {
        return `
            <tr onclick="showReportDetails(${report.id})" class="cursor-pointer">
                <td class="text-sm font-mono">${report.id}</td>
                <td>${report.type}</td>
                <td>${report.location}</td>
                <td>${getStatusBadge(report.status)}</td>
                <td>${report.anonymous ? 'Yes' : report.user}</td>
                <td>
                    <button class="text-success hover:text-green-400 text-lg mr-2" title="Accept"><i class="fas fa-check"></i></button>
                    <button class="text-danger hover:text-red-400 text-lg" title="Refuse"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;
    }).join('');
    
    return `
        <table class="glass-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>User</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${reports.length > 0 ? rows : '<tr><td colspan="6" class="text-center text-white/50">No reports in this category.</td></tr>'}
            </tbody>
        </table>
    `;
}

function createUserTable(usersList) {
    const rows = usersList.map(user => {
        return `
            <tr onclick="showUserDetails(${user.id})" class="cursor-pointer">
                <td class="text-sm font-mono">${user.id}</td>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td>${getStatusBadge(user.status)}</td>
                <td>${getStatusBadge(user.verified ? 'Verified' : 'Unverified')}</td>
                <td>
                    <button class="text-success hover:text-green-400 text-lg mr-2" title="Verify"><i class="fas fa-check"></i></button>
                    <button class="text-danger hover:text-red-400 text-lg" title="Deactivate"><i class="fas fa-times"></i></button>
                </td>
            </tr>
        `;
    }).join('');
    
    return `
        <table class="glass-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${usersList.length > 0 ? rows : '<tr><td colspan="6" class="text-center text-white/50">No users found in this list.</td></tr>'}
            </tbody>
        </table>
    `;
}

// Report Management Page
function renderReportManagement(target) {
    target.innerHTML = `
        <div class="flex justify-between items-center mb-6">
            <div class="section-title m-0"><h2>Report Management (${incidentReports.length})</h2></div>
            <button class="btn hover:shadow-lg hover:shadow-primary/40"><i class="fas fa-file-alt mr-2"></i> View Anonymous Reports</button>
        </div>
        <div class="glass-card p-0">
            ${createReportTable(incidentReports)}
        </div>
    `;
}

// User Management Page
function renderUserManagement(target) {
     target.innerHTML = `
        <div class="flex justify-between items-center mb-6">
            <div class="section-title m-0"><h2>User Management (${users.length})</h2></div>
        </div>
        <div class="glass-card p-0">
            ${createUserTable(users)}
        </div>
        <div class="mt-6 flex justify-end">
            <button class="btn hover:shadow-lg hover:shadow-primary/40"><i class="fas fa-user-plus mr-2"></i> Add Admin/Staff</button>
        </div>
    `;
}

// Content Moderation Page
function renderContentModeration(target) {
    target.innerHTML = `
        <div class="section-title"><h2>Content Moderation</h2></div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Shared Experiences -->
            <div class="glass-card">
                <h3 class="text-xl font-bold text-secondary mb-4">Shared Experiences & Comments</h3>
                <ul class="space-y-3 text-white/80">
                    <li><i class="fas fa-circle text-xs text-accent mr-2"></i> 2 new experience submissions pending review.</li>
                    <li><i class="fas fa-circle text-xs text-accent mr-2"></i> 14 comments flagged for inappropriate content.</li>
                </ul>
                <button class="btn mt-4 bg-white/10 hover:bg-white/20" style="background: var(--glass-bg);">Go to Moderation Queue <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
            <!-- Article Publications -->
            <div class="glass-card">
                <h3 class="text-xl font-bold text-secondary mb-4">Article Publications</h3>
                <ul class="space-y-3 text-white/80">
                    <li><i class="fas fa-circle text-xs text-accent mr-2"></i> 1 draft article ready for approval/rejection.</li>
                    <li><i class="fas fa-circle text-xs text-accent mr-2"></i> 3 articles currently published and active.</li>
                </ul>
                <button class="btn mt-4 bg-white/10 hover:bg-white/20" style="background: var(--glass-bg);">Manage Articles <i class="fas fa-arrow-right ml-2"></i></button>
            </div>
        </div>
    `;
}

// Emergency Contacts Management Page
function renderEmergencyContacts(target) {
    target.innerHTML = `
        <div class="flex justify-between items-center mb-6">
            <div class="section-title m-0"><h2>Emergency Contacts Management</h2></div>
            <button class="btn hover:shadow-lg hover:shadow-primary/40"><i class="fas fa-phone-plus mr-2"></i> Add New Contact</button>
        </div>
        <div class="glass-card p-0">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${emergencyContacts.map(c => `
                        <tr>
                            <td>${c.name}</td>
                            <td><span class="font-mono">${c.number}</span></td>
                            <td>${c.category}</td>
                            <td class="text-sm text-white/70">${c.description}</td>
                            <td>
                                <button class="text-primary hover:text-primary-dark text-lg mr-2" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="text-danger hover:text-red-700 text-lg" title="Delete"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Categories Management Page
function renderCategoriesManagement(target) {
    target.innerHTML = `
        <div class="flex justify-between items-center mb-6">
            <div class="section-title m-0"><h2>Categories Management (${categories.length})</h2></div>
            <button class="btn hover:shadow-lg hover:shadow-primary/40"><i class="fas fa-plus mr-2"></i> Create New Category</button>
        </div>
        <div class="glass-card p-0">
            <table class="glass-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Applies To</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${categories.map(c => `
                        <tr>
                            <td>${c.id}</td>
                            <td>${c.name}</td>
                            <td>${c.type}</td>
                            <td>${getStatusBadge(c.enabled ? 'Enabled' : 'Disabled')}</td>
                            <td>
                                <button class="text-primary hover:text-primary-dark text-lg mr-2" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="text-danger hover:text-red-700 text-lg" title="${c.enabled ? 'Disable' : 'Enable'}"><i class="fas fa-toggle-on"></i></button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Statistics & Analytics Page
function renderStatisticsAnalytics(target) {
    // Calculate data for transportation categories
    const busReports = incidentReports.filter(r => r.location === 'Bus').length;
    const metroReports = incidentReports.filter(r => r.location === 'Metro').length;
    const tgmReports = incidentReports.filter(r => r.location === 'TGM').length;
    
    const harassmentReports = incidentReports.filter(r => r.type === 'Harassment').length;
    const conditionReports = incidentReports.filter(r => r.type === 'Condition').length;
    const theftReports = incidentReports.filter(r => r.type === 'Theft').length;
    const complaintReports = incidentReports.filter(r => r.type === 'Complaint').length;
    const maintenanceReports = incidentReports.filter(r => r.type === 'Maintenance').length;

    target.innerHTML = `
        <div class="section-title"><h2>Statistics & Analytics</h2></div>
        <div class="glass-card mb-6">
            <h3 class="text-xl font-bold text-secondary mb-4">Reports by Transportation Type</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-3xl font-bold text-primary mb-2">${busReports}</div>
                    <div class="text-white/70">Bus Reports</div>
                </div>
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-3xl font-bold text-accent mb-2">${metroReports}</div>
                    <div class="text-white/70">Metro Reports</div>
                </div>
                <div class="bg-white/5 p-4 rounded-lg">
                    <div class="text-3xl font-bold text-secondary mb-2">${tgmReports}</div>
                    <div class="text-white/70">TGM Reports</div>
                </div>
            </div>
        </div>
        
        <div class="glass-card mb-6">
            <h3 class="text-xl font-bold text-secondary mb-4">Reports by Category</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white/5 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-primary mb-1">${harassmentReports}</div>
                    <div class="text-sm text-white/70">Harassment</div>
                </div>
                <div class="bg-white/5 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-accent mb-1">${conditionReports}</div>
                    <div class="text-sm text-white/70">Condition</div>
                </div>
                <div class="bg-white/5 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-warning mb-1">${theftReports}</div>
                    <div class="text-sm text-white/70">Theft</div>
                </div>
                <div class="bg-white/5 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-danger mb-1">${complaintReports}</div>
                    <div class="text-sm text-white/70">Complaint</div>
                </div>
                <div class="bg-white/5 p-3 rounded-lg text-center">
                    <div class="text-xl font-bold text-success mb-1">${maintenanceReports}</div>
                    <div class="text-sm text-white/70">Maintenance</div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            ${createStatCard('User Posts', users.reduce((sum, user) => sum + user.posts, 0), 'fas fa-comment', 'linear-gradient(135deg, #f0abfc, #a78bfa)')}
            <div class="glass-card flex flex-col justify-center">
                <h3 class="text-lg font-bold text-white mb-2">Generate Safety Report</h3>
                <p class="text-sm text-white/70">Create a comprehensive PDF for compliance.</p>
                <button class="btn btn-sm mt-3 bg-accent hover:shadow-lg hover:shadow-accent/40"><i class="fas fa-file-pdf mr-2"></i> Generate</button>
            </div>
        </div>
    `;
}

// Events Management Page
function renderEventsManagement(target) {
     target.innerHTML = `
        <div class="flex justify-between items-center mb-6">
            <div class="section-title m-0"><h2>Events Management</h2></div>
            <button class="btn hover:shadow-lg hover:shadow-primary/40"><i class="fas fa-calendar-plus mr-2"></i> Publish New Event</button>
        </div>
        <div class="glass-card">
            <h3 class="text-xl font-bold text-secondary mb-4">Upcoming Events</h3>
            <div class="space-y-4">
                <div class="bg-white/5 p-4 rounded-lg border border-white/10 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-lg text-primary">Public Safety Awareness Day</p>
                        <p class="text-sm text-white/70">2026-01-15 | Tunis City Center</p>
                        <p class="text-xs text-accent">54 Registrations</p>
                    </div>
                    <button class="text-primary hover:text-primary-dark text-lg" title="Edit"><i class="fas fa-edit"></i></button>
                </div>
                <div class="bg-white/5 p-4 rounded-lg border border-white/10 flex justify-between items-center">
                    <div>
                        <p class="font-bold text-lg text-primary">Digital Reporting Workshop</p>
                        <p class="text-sm text-white/70">2026-02-01 | Online Webinar</p>
                        <p class="text-xs text-accent">120 Registrations</p>
                    </div>
                    <button class="text-primary hover:text-primary-dark text-lg" title="Edit"><i class="fas fa-edit"></i></button>
                </div>
            </div>
        </div>
    `;
}

// System Administration Page
function renderSystemAdministration(target) {
    target.innerHTML = `
        <div class="section-title"><h2>System Administration</h2></div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Admin Accounts & Permissions -->
            <div class="glass-card">
                <h3 class="text-xl font-bold text-secondary mb-4">Admin Accounts & Permissions</h3>
                <p class="text-white/70 mb-4">Manage access levels for administrative staff.</p>
                <button class="btn bg-white/10 hover:bg-white/20" style="background: var(--glass-bg);">Manage Admin Roles <i class="fas fa-lock ml-2"></i></button>
            </div>
            <!-- System Settings & Maintenance -->
            <div class="glass-card">
                <h3 class="text-xl font-bold text-secondary mb-4">Maintenance & Backup</h3>
                <p class="text-white/70 mb-4">Ensure data integrity and system configuration.</p>
                <div class="flex flex-col gap-3">
                    <button class="btn bg-white/10 hover:bg-white/20" style="background: var(--glass-bg);"><i class="fas fa-database mr-2"></i> Initiate Database Backup</button>
                </div>
            </div>
        </div>
    `;
}

// Content Creation Page
function renderContentCreation(target) {
    target.innerHTML = `
        <div class="section-title"><h2>Content Creation</h2></div>
        <div class="glass-card">
            <h3 class="text-xl font-bold text-secondary mb-4">Write & Publish New Article</h3>
            <label class="block mb-3">
                <span class="text-white/70">Article Title</span>
                <input type="text" class="admin-input" placeholder="e.g., 5 Ways to Stay Safe on the Metro"/>
            </label>
            <label class="block mb-3">
                <span class="text-white/70">Category</span>
                <select class="admin-select">
                    <option>Safety Tips</option>
                    <option>Transport News</option>
                    <option>System Updates</option>
                </select>
            </label>
            <label class="block mb-3">
                <span class="text-white/70">Content (Markdown Supported)</span>
                <textarea class="admin-textarea" rows="10" placeholder="Start writing your safety article here..."></textarea>
            </label>
            <div class="flex justify-end gap-3 mt-4">
                <button class="btn bg-white/10 hover:bg-white/20" style="background: var(--glass-bg);"><i class="fas fa-drafting-pencil mr-2"></i> Save Draft</button>
                <button class="btn"><i class="fas fa-upload mr-2"></i> Publish Article</button>
            </div>
        </div>
    `;
}

// --- Modal/Detail Functions ---

function showReportDetails(id) {
    const report = incidentReports.find(r => r.id === id);
    if (!report) return;
    
    const modal = document.getElementById('detail-modal');
    document.getElementById('modal-title').textContent = `Incident Report #${report.id}`;
    
    document.getElementById('modal-body').innerHTML = `
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div><span class="text-white/50">Type:</span> <span class="font-semibold text-white">${report.type}</span></div>
            <div><span class="text-white/50">Location:</span> <span class="font-semibold text-white">${report.location}</span></div>
            <div><span class="text-white/50">Date Filed:</span> <span class="font-semibold text-white">${report.date}</span></div>
            <div><span class="text-white/50">Reported By:</span> <span class="font-semibold text-white">${report.anonymous ? 'Anonymous' : report.user}</span></div>
        </div>
        
        <h4 class="font-bold text-primary mt-4">Description</h4>
        <div class="p-3 rounded-lg bg-white/5 text-sm">${report.description}</div>
        
        <label class="block mt-4">
            <span class="text-white/70">Change Status</span>
            <select id="update-status-select" class="admin-select" style="color:var(--text);">
                <option value="pending" ${report.status === 'pending' ? 'selected' : ''}>Pending</option>
                <option value="accepted" ${report.status === 'accepted' ? 'selected' : ''}>Accepted</option>
                <option value="refused" ${report.status === 'refused' ? 'selected' : ''}>Refused</option>
            </select>
        </label>
    `;
    
    document.getElementById('modal-actions').innerHTML = `
        <button class="btn bg-white/10 hover:bg-white/20" style="background: var(--glass-bg);" onclick="closeModal()">Close</button>
        <button class="btn" onclick="updateReportChanges(${report.id})"><i class="fas fa-save mr-2"></i> Save and Update</button>
    `;
    
    openModal();
}

function showUserDetails(id) {
    const user = users.find(u => u.id === id);
    if (!user) return;
    
    document.getElementById('modal-title').textContent = `User Profile: ${user.name}`;
    
    document.getElementById('modal-body').innerHTML = `
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div><span class="text-white/50">ID:</span> <span class="font-mono text-white">${user.id}</span></div>
            <div><span class="text-white/50">Email:</span> <span class="font-semibold text-white">${user.email}</span></div>
            <div><span class="text-white/50">Posts:</span> <span class="font-semibold text-primary">${user.posts}</span></div>
        </div>
        
        <h4 class="font-bold text-primary mt-4">Account Status</h4>
        <label class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10">
            <span class="text-white/70">Account Active / Deactive</span>
            <input type="checkbox" id="user-active-toggle" class="form-checkbox h-5 w-5 text-primary border-white/50 bg-white/10 rounded-full" ${user.status === 'active' ? 'checked' : ''} />
        </label>
        <label class="flex items-center justify-between p-3 rounded-lg bg-white/5 border border-white/10 mt-3">
            <span class="text-white/70">Profile Verified Status</span>
            <input type="checkbox" id="user-verified-toggle" class="form-checkbox h-5 w-5 text-success border-white/50 bg-white/10 rounded-full" ${user.verified ? 'checked' : ''} />
        </label>
    `;
    
    document.getElementById('modal-actions').innerHTML = `
        <button class="btn btn-outline bg-transparent border-danger text-danger hover:bg-danger/10" onclick="resetUserPassword(${user.id})"><i class="fas fa-key mr-2"></i> Reset Password</button>
        <button class="btn" onclick="updateUserChanges(${user.id})"><i class="fas fa-save mr-2"></i> Save User Changes</button>
    `;
    
    openModal();
}

function openModal() {
    const modal = document.getElementById('detail-modal');
    modal.style.display = 'flex';
    setTimeout(() => {
        document.getElementById('modal-content-container').classList.remove('scale-95');
        document.getElementById('modal-content-container').classList.add('scale-100');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('detail-modal');
    document.getElementById('modal-content-container').classList.remove('scale-100');
    document.getElementById('modal-content-container').classList.add('scale-95');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Mock update functions
function updateReportChanges(id) {
    const newStatus = document.getElementById('update-status-select').value;
    const reportIndex = incidentReports.findIndex(r => r.id === id);
    
    if (reportIndex !== -1) {
        incidentReports[reportIndex].status = newStatus;
        console.log(`Report ${id} updated: Status=${newStatus}`);
        closeModal();
        renderPage(currentPage); 
    }
}

function updateUserChanges(id) {
    const isActive = document.getElementById('user-active-toggle').checked;
    const isVerified = document.getElementById('user-verified-toggle').checked;
    const userIndex = users.findIndex(u => u.id === id);
    
    if (userIndex !== -1) {
        users[userIndex].status = isActive ? 'active' : 'inactive';
        users[userIndex].verified = isVerified;
        console.log(`User ${id} updated: Active=${isActive}, Verified=${isVerified}`);
        closeModal();
        renderPage(currentPage); 
    }
}

function resetUserPassword(id) {
    console.log(`Password reset initiated for user ${id}.`);
    console.log('Password reset link has been sent to the user (Mock Action).');
}

// Responsive sidebar logic
if (window.innerWidth > 1024) {
     document.getElementById('sidebar').classList.add('active');
}
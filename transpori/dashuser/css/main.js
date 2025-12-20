// C:\xampp\htdocs\PFA\transpori\dashuser\javascript\main.js
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar
    if (window.innerWidth <= 1024) {
        document.getElementById('sidebar')?.classList.remove('active');
    } else {
        document.getElementById('sidebar')?.classList.add('active');
    }
    
    // Setup like buttons
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const type = this.dataset.type;
            const countSpan = this.querySelector('.like-count');
            
            fetch('ajax/like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    countSpan.textContent = data.newCount;
                    this.classList.add('liked');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    
    // Close modals on outside click
    document.addEventListener('click', function(e) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });
});

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

function showExperienceDetails(id) {
    const modal = document.getElementById('detail-modal');
    const modalBody = document.getElementById('modal-body');
    
    modalBody.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-3xl text-primary mb-4"></i>
            <p class="text-text-secondary">Loading experience details...</p>
        </div>
    `;
    
    modal.classList.add('active');
    
    fetch(`ajax/get_experience.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const exp = data.experience;
                modalBody.innerHTML = `
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-primary">${exp.title}</h2>
                        <button class="text-text-secondary hover:text-text" onclick="closeDetailModal()">
                            <i class="fas fa-times text-2xl"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center text-white font-bold mr-4">
                                ${exp.author_initials}
                            </div>
                            <div>
                                <div class="font-bold text-lg">${exp.author_name}</div>
                                <div class="text-text-secondary">${exp.date_formatted}</div>
                            </div>
                            <span class="status-badge ml-auto">
                                ${exp.experience_type}
                            </span>
                        </div>
                        
                        <div class="p-4 rounded-lg bg-glass-bg border border-glass-border">
                            <p class="text-text whitespace-pre-line">${exp.content}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 rounded-lg bg-primary/10 border border-primary/20">
                                <div class="text-2xl font-bold text-primary">${exp.likes_count}</div>
                                <div class="text-sm text-text-secondary">Likes</div>
                            </div>
                            <div class="text-center p-3 rounded-lg bg-secondary/10 border border-secondary/20">
                                <div class="text-2xl font-bold text-secondary">${exp.comments_count}</div>
                                <div class="text-sm text-text-secondary">Comments</div>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <button class="btn btn-outline flex-1" onclick="likeExperience(${exp.id})">
                                <i class="fas fa-thumbs-up mr-2"></i> Like Experience
                            </button>
                            <button class="btn flex-1" onclick="closeDetailModal()">
                                Close
                            </button>
                        </div>
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-circle text-3xl text-red-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-text mb-2">Error</h3>
                        <p class="text-text-secondary mb-6">Could not load experience details.</p>
                        <button class="btn btn-outline" onclick="closeDetailModal()">
                            Close
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            modalBody.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-3xl text-red-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-text mb-2">Network Error</h3>
                    <p class="text-text-secondary mb-6">Please check your connection and try again.</p>
                    <button class="btn btn-outline" onclick="closeDetailModal()">
                        Close
                    </button>
                </div>
            `;
        });
}

function closeDetailModal() {
    document.getElementById('detail-modal').classList.remove('active');
}

function likeExperience(id) {
    fetch('ajax/like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: id,
            type: 'experience'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likeCount = document.querySelector('.modal-content .text-primary.text-2xl');
            if (likeCount) {
                likeCount.textContent = data.newCount;
            }
            alert('Experience liked!');
        }
    })
    .catch(error => console.error('Error:', error));
}

window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth > 1024) {
        sidebar?.classList.add('active');
    } else {
        sidebar?.classList.remove('active');
    }
});
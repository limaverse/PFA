// Dashboard specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Like buttons functionality
    document.querySelectorAll('.action-btn').forEach(button => {
        if (button.querySelector('.fa-thumbs-up')) {
            button.addEventListener('click', function() {
                const likeCount = this.querySelector('span');
                if (likeCount) {
                    const currentCount = parseInt(likeCount.textContent);
                    likeCount.textContent = currentCount + 1;
                    this.style.color = '#3b82f6';
                }
            });
        }
    });
    
    // View details buttons
    document.querySelectorAll('.action-btn').forEach(button => {
        if (button.querySelector('.fa-eye')) {
            button.addEventListener('click', function() {
                alert('Experience details would open here in a real app.');
            });
        }
    });
    
    // Delete buttons
    document.querySelectorAll('.btn-outline.text-red-400').forEach(button => {
        button.addEventListener('click', function(e) {
            if (confirm('Are you sure you want to delete this experience?')) {
                const postCard = this.closest('.post-card');
                if (postCard) {
                    postCard.style.opacity = '0.5';
                    setTimeout(() => {
                        postCard.remove();
                        alert('Experience deleted successfully!');
                    }, 300);
                }
            }
        });
    });
});
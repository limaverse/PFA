// Mobile Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');
const authButtons = document.querySelector('.auth-buttons');

hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    authButtons.classList.toggle('active');
});

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-links a, .auth-buttons a').forEach(link => {
    link.addEventListener('click', () => {
        navLinks.classList.remove('active');
        authButtons.classList.remove('active');
    });
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if(targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if(targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 70,
                behavior: 'smooth'
            });
        }
    });
});

// Simple form submission handler
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Thank you for your submission! In a real application, this would be processed.');
        this.reset();
    });
});

// Add animation to elements when they come into view
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.service-card, .emergency-card, .article-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
    observer.observe(card);
});

// Emergency contact quick dial functionality
document.querySelectorAll('.emergency-card').forEach(card => {
    card.addEventListener('click', function() {
        const serviceName = this.querySelector('h3').textContent;
        const number = this.querySelector('.emergency-number').textContent;
        console.log(`Dialing emergency service: ${serviceName} - ${number}`);
        
        // Show a confirmation dialog
        const confirmed = confirm(`Do you want to call ${serviceName} at ${number}?`);
        if (confirmed) {
            // In a real app, this would initiate a phone call
            // For web, we can't actually make calls, but we can log it
            console.log(`Initiating call to ${number}`);
            // You could also redirect to tel: link on mobile devices
            // window.location.href = `tel:${number}`;
        }
    });
});

// Add loading animation to buttons
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        // Add a small loading effect
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        
        setTimeout(() => {
            this.innerHTML = originalText;
        }, 1000);
    });
});

// Newsletter subscription handler
const newsletterForm = document.querySelector('.footer-column form');
if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = this.querySelector('input[type="email"]').value;
        
        if (email) {
            // Simulate API call
            setTimeout(() => {
                alert('Thank you for subscribing to our newsletter!');
                this.reset();
            }, 500);
        }
    });
}

// Add scroll to top functionality
const scrollToTop = document.createElement('button');
scrollToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
scrollToTop.style.position = 'fixed';
scrollToTop.style.bottom = '20px';
scrollToTop.style.right = '20px';
scrollToTop.style.width = '50px';
scrollToTop.style.height = '50px';
scrollToTop.style.borderRadius = '50%';
scrollToTop.style.background = 'linear-gradient(135deg, var(--primary), var(--secondary))';
scrollToTop.style.color = 'white';
scrollToTop.style.border = 'none';
scrollToTop.style.cursor = 'pointer';
scrollToTop.style.zIndex = '1000';
scrollToTop.style.opacity = '0';
scrollToTop.style.transition = 'opacity 0.3s ease';
scrollToTop.classList.add('btn');

scrollToTop.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

document.body.appendChild(scrollToTop);

// Show/hide scroll to top button based on scroll position
window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollToTop.style.opacity = '1';
    } else {
        scrollToTop.style.opacity = '0';
    }
});

// Add keyboard navigation support
document.addEventListener('keydown', (e) => {
    // ESC key closes mobile menu
    if (e.key === 'Escape' && navLinks.classList.contains('active')) {
        navLinks.classList.remove('active');
        authButtons.classList.remove('active');
    }
    
    // Tab key navigation for accessibility
    if (e.key === 'Tab') {
        // Ensure focus remains within modal/mobile menu when open
        if (navLinks.classList.contains('active')) {
            const focusableElements = navLinks.querySelectorAll('a, button, [tabindex]:not([tabindex="-1"])');
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey && document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            } else if (!e.shiftKey && document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
});
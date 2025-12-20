<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "transpori";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection - but don't die if it fails for demo
if ($conn->connect_error) {
    // Continue without database for demo
    $db_connected = false;
} else {
    $db_connected = true;
}

// Get emergency contacts from database or use defaults
$emergency_contacts = [];
if ($db_connected) {
    $sql = "SELECT name, phone_number, description, category FROM emergency_contacts WHERE is_active = TRUE";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $emergency_contacts[] = $row;
        }
    }
}

// If no contacts from database, use default ones
if (empty($emergency_contacts)) {
    $emergency_contacts = [
        ['name' => 'Police Secours', 'phone_number' => '197', 'description' => 'Police d\'urgence', 'category' => 'emergency'],
        ['name' => 'Protection Civile', 'phone_number' => '198', 'description' => 'Pompiers et secours d\'urgence', 'category' => 'emergency'],
        ['name' => 'Anti-Corruption Line', 'phone_number' => '1899', 'description' => 'Signalement de corruption', 'category' => 'green_line'],
        ['name' => 'Garde Nationale', 'phone_number' => '198', 'description' => 'Garde nationale pour la sécurité', 'category' => 'emergency']
    ];
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['contact_submit'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $inquiry_type = $_POST['inquiry_type'];
        $message = $_POST['message'];
        
        $contact_success = true;
    }
    
    if (isset($_POST['newsletter_submit'])) {
        $newsletter_email = $_POST['newsletter_email'];
        
        if ($db_connected) {
            $sql = "INSERT INTO newsletter_subscriptions (email) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $newsletter_email);
            
            if ($stmt->execute()) {
                $newsletter_success = true;
            } else {
                $newsletter_error = true;
            }
            $stmt->close();
        } else {
            $newsletter_success = true; // For demo purposes
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Safe Transportation Reporting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/homepagecss.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">
                    <i class="fas fa-shield-alt"></i>
                    Transpori
                </a>
                <ul class="nav-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#articles">Resources</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
                <div class="auth-buttons">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
                        <a href="logout.php" class="btn">Logout</a>
                    <?php else: ?>
                        <a href="auth.php?tab=login" class="btn btn-outline">Login</a>
                        <a href="auth.php?tab=signup" class="btn">Sign Up</a>
                    <?php endif; ?>
                </div>
                <button class="hamburger">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Safe Transportation for <span style="color: var(--primary);">Everyone</span></h1>
                <p>Transpori is a community-driven platform for reporting safety incidents, sharing experiences, and accessing resources to make public transportation safer in Tunisia.</p>
                <div class="hero-btns">
                    <a href="#services" class="btn">Report an Incident</a>
                    <a href="#articles" class="btn btn-outline">View Resources</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Contacts Section -->
    <section class="emergency-contacts">
        <div class="container">
            <div class="section-title">
                <h2>Emergency Contacts</h2>
            </div>
            <div class="emergency-grid">
                <?php foreach($emergency_contacts as $contact): ?>
                <div class="emergency-card">
                    <div class="emergency-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($contact['name']); ?></h3>
                    <div class="emergency-number"><?php echo htmlspecialchars($contact['phone_number']); ?></div>
                    <p><?php echo htmlspecialchars($contact['description']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="section-title">
                <h2>About Transpori</h2>
            </div>
            <div class="about-content">
                <div class="about-text">
                    <h2>Making Transportation Safer Together</h2>
                    <p>Transpori is a safe transportation reporting system designed to empower citizens, improve safety standards, and create accountability in Tunisia's public transportation system.</p>
                    <p>Our platform allows users to report incidents, share experiences, access emergency contacts, and stay informed about transportation safety through educational resources and community engagement.</p>
                    <a href="#contact" class="btn">Join Our Community</a>
                </div>
                <div class="about-image">
                    <div class="about-image-placeholder">
                        <i class="fas fa-bus"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services">
        <div class="container">
            <div class="section-title">
                <h2>Our Services</h2>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>Incident Reporting</h3>
                    <p>Report safety incidents and harassment cases in public transportation with our secure, anonymous system.</p>
                    <a href="#contact" class="btn">Report Now</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Share Experiences</h3>
                    <p>Share your positive and negative transportation experiences to help others and drive improvements.</p>
                    <a href="#contact" class="btn">Share Experience</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Safety Statistics</h3>
                    <p>View statistics and trends about transportation safety issues to stay informed about current challenges.</p>
                    <a href="#contact" class="btn">View Statistics</a>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Community Events</h3>
                    <p>Participate in community events and awareness campaigns to promote transportation safety.</p>
                    <a href="#contact" class="btn">View Events</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Articles Section -->
    <section id="articles" class="articles">
        <div class="container">
            <div class="section-title">
                <h2>Safety Resources</h2>
            </div>
            <div class="articles-grid">
                <div class="article-card">
                    <div class="article-image">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="article-content">
                        <h3>Your Rights in Public Transportation</h3>
                        <p>Learn about your legal rights and protections when using public transportation in Tunisia.</p>
                        <div class="article-meta">
                            <span><i class="far fa-calendar"></i> May 15, 2023</span>
                            <span><i class="far fa-eye"></i> 1.2K views</span>
                        </div>
                        <a href="#contact" class="btn btn-outline">Read More</a>
                    </div>
                </div>
                <div class="article-card">
                    <div class="article-image">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="article-content">
                        <h3>Community Safety Initiatives</h3>
                        <p>Discover how community-led initiatives are making transportation safer for everyone.</p>
                        <div class="article-meta">
                            <span><i class="far fa-calendar"></i> April 28, 2023</span>
                            <span><i class="far fa-eye"></i> 987 views</span>
                        </div>
                        <a href="#contact" class="btn btn-outline">Read More</a>
                    </div>
                </div>
                <div class="article-card">
                    <div class="article-image">
                        <i class="fas fa-traffic-light"></i>
                    </div>
                    <div class="article-content">
                        <h3>Transportation Safety Updates</h3>
                        <p>Stay informed about the latest safety measures and updates in Tunisia's transport system.</p>
                        <div class="article-meta">
                            <span><i class="far fa-calendar"></i> June 3, 2023</span>
                            <span><i class="far fa-eye"></i> 1.5K views</span>
                        </div>
                        <a href="#contact" class="btn btn-outline">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <div class="container">
            <div class="section-title">
                <h2>Contact Us</h2>
            </div>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h3>Location</h3>
                            <p>Tunis, Tunisia</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h3>Email</h3>
                            <p>contact@transpori.tn</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h3>Phone</h3>
                            <p>+216 12 345 678</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3>Working Hours</h3>
                            <p>Monday - Friday: 9:00 - 17:00</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <?php if(isset($contact_success)): ?>
                        <div class="alert alert-success">
                            Thank you for your message! We'll get back to you soon.
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="email" name="email" placeholder="Your Email" required>
                        <select name="inquiry_type" required>
                            <option value="" disabled selected>Select Inquiry Type</option>
                            <option value="report">Report an Incident</option>
                            <option value="experience">Share an Experience</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="general">General Inquiry</option>
                        </select>
                        <textarea name="message" placeholder="Your Message" required></textarea>
                        <button type="submit" name="contact_submit" class="btn">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Transpori</h3>
                    <p>Making public transportation safer through community reporting, education, and advocacy.</p>
                </div>
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Report Incident</a></li>
                        <li><a href="#articles">Resources</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Services</h3>
                    <ul class="footer-links">
                        <li><a href="#services">Incident Reporting</a></li>
                        <li><a href="#services">Experience Sharing</a></li>
                        <li><a href="#services">Safety Statistics</a></li>
                        <li><a href="#services">Educational Resources</a></li>
                        <li><a href="#services">Community Events</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for safety updates and transportation news.</p>
                    <?php if(isset($newsletter_success)): ?>
                        <div class="alert alert-success">
                            Thank you for subscribing!
                        </div>
                    <?php elseif(isset($newsletter_error)): ?>
                        <div class="alert alert-error">
                            There was an error with your subscription. Please try again.
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="email" name="newsletter_email" placeholder="Your Email" required>
                        <button type="submit" name="newsletter_submit" class="btn">Subscribe</button>
                    </form>
                </div>
            </div>
            <p class="copyright">&copy; 2025 Transpori. All Rights Reserved. | Safe Transportation Reporting System</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
<?php
if (isset($conn) && $db_connected) {
    $conn->close();
}
?>
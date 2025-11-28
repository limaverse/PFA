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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text: #1e293b;
            --text-light: #64748b;
            --text-secondary: #475569;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --border: #e2e8f0;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--text);
            line-height: 1.6;
            background-color: var(--bg-white);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: var(--bg-white);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .logo i {
            font-size: 1.8rem;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            color: white;
            padding: 150px 0 100px;
            margin-top: 70px;
        }

        .hero-content {
            max-width: 700px;
            text-align: center;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #e0e7ff;
        }

        .hero-btns {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Section Styles */
        section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--text);
            margin-bottom: 15px;
        }

        /* Emergency Contacts */
        .emergency-contacts {
            background: var(--bg-light);
        }

        .emergency-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .emergency-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .emergency-card:hover {
            transform: translateY(-5px);
        }

        .emergency-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 1.8rem;
        }

        .emergency-card h3 {
            margin-bottom: 10px;
            color: var(--text);
        }

        .emergency-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* About Section */
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .about-text h2 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            color: var(--text);
        }

        .about-text p {
            margin-bottom: 20px;
            color: var(--text-light);
        }

        .about-image {
            display: flex;
            justify-content: center;
        }

        .about-image-placeholder {
            width: 100%;
            height: 400px;
            background-color: #dbeafe;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
        }

        .about-image i {
            font-size: 5rem;
            color: var(--primary);
        }

        /* Services Section */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .service-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }

        .service-card h3 {
            margin-bottom: 15px;
            color: var(--text);
        }

        .service-card p {
            margin-bottom: 20px;
            color: var(--text-light);
        }

        /* Articles Section */
        .articles {
            background: var(--bg-light);
        }

        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .article-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .article-card:hover {
            transform: translateY(-5px);
        }

        .article-image {
            height: 200px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .article-content {
            padding: 25px;
        }

        .article-content h3 {
            margin-bottom: 15px;
            color: var(--text);
        }

        .article-content p {
            margin-bottom: 20px;
            color: var(--text-light);
        }

        .article-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        /* Contact Section */
        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .contact-item {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }

        .contact-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .contact-item h3 {
            margin-bottom: 5px;
            color: var(--text);
        }

        .contact-item p {
            color: var(--text-light);
        }

        .contact-form form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-form input,
        .contact-form select,
        .contact-form textarea {
            padding: 15px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .contact-form input:focus,
        .contact-form select:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .contact-form textarea {
            min-height: 150px;
            resize: vertical;
        }

        /* Footer */
        footer {
            background: #1e293b;
            color: white;
            padding: 60px 0 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-column h3 {
            margin-bottom: 20px;
            color: white;
        }

        .footer-column p {
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-column form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #334155;
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Success/Error Messages */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links, .auth-buttons {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .about-content,
            .contact-content {
                grid-template-columns: 1fr;
            }

            .hero-btns {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 250px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .hero {
                padding: 120px 0 80px;
            }

            .hero h1 {
                font-size: 2rem;
            }

            section {
                padding: 60px 0;
            }

            .section-title h2 {
                font-size: 2rem;
            }
        }
    </style>
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

    <script>
        // Mobile menu toggle
        document.querySelector('.hamburger').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            const authButtons = document.querySelector('.auth-buttons');
            
            if (navLinks.style.display === 'flex') {
                navLinks.style.display = 'none';
                authButtons.style.display = 'none';
            } else {
                navLinks.style.display = 'flex';
                authButtons.style.display = 'flex';
                
                if (window.innerWidth <= 768) {
                    navLinks.style.flexDirection = 'column';
                    authButtons.style.flexDirection = 'column';
                    navLinks.style.position = 'absolute';
                    navLinks.style.top = '100%';
                    navLinks.style.left = '0';
                    navLinks.style.right = '0';
                    navLinks.style.background = 'white';
                    navLinks.style.padding = '20px';
                    navLinks.style.boxShadow = '0 5px 10px rgba(0,0,0,0.1)';
                    
                    authButtons.style.position = 'absolute';
                    authButtons.style.top = 'calc(100% + 180px)';
                    authButtons.style.left = '0';
                    authButtons.style.right = '0';
                    authButtons.style.background = 'white';
                    authButtons.style.padding = '20px';
                    authButtons.style.boxShadow = '0 5px 10px rgba(0,0,0,0.1)';
                }
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
<?php
if (isset($conn) && $db_connected) {
    $conn->close();
}
?>
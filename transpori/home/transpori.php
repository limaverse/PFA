<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Safe Transportation Reporting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/PFA/transpori/home/css/transpori css.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="<?php echo $is_logged_in ? '/PFA/transpori/dashuser/index.php' : '#home'; ?>" class="logo">
                    <i class="fas fa-shield-alt"></i>
                    Transpori
                </a>
                <ul class="nav-links">
                    <?php if ($is_logged_in): ?>
                        <!-- Logged In Navigation -->
                        <li><a href="/PFA/transpori/dashuser/index.php" class="nav-link">Home</a></li>
                        <li><a href="/PFA/transpori/dashuser/my-experiences.php" class="nav-link">My Experiences</a></li>
                        <li><a href="/PFA/transpori/dashuser/share.php" class="nav-link">Share Experience</a></li>
                        <li><a href="/PFA/transpori/dashuser/tips.php" class="nav-link">Safety Tips</a></li>
                        <li><a href="/PFA/transpori/dashuser/emergency.php" class="nav-link">Emergency Contacts</a></li>
                    <?php else: ?>
                        <!-- Logged Out Navigation (Main Page) -->
                        <li><a href="#home" class="nav-link scroll-link">Home</a></li>
                        <li><a href="#about" class="nav-link scroll-link">About</a></li>
                        <li><a href="#services" class="nav-link scroll-link">Services</a></li>
                        <li><a href="#articles" class="nav-link scroll-link">Resources</a></li>
                        <li><a href="#contact" class="nav-link scroll-link">Contact</a></li>
                    <?php endif; ?>
                </ul>
                <div class="auth-buttons">
                    <?php if ($is_logged_in): ?>
                        <!-- Profile and Logout buttons -->
                        <a href="#" class="profile-btn">
                            <div class="profile-avatar">
                                <?php echo substr($user_name, 0, 2); ?>
                            </div>
                            <span><?php echo htmlspecialchars(explode(' ', $user_name)[0]); ?></span>
                        </a>
                        <a href="/PFA/transpori/dashuser/logout.php" class="btn btn-outline">Logout</a>
                    <?php else: ?>
                        <!-- Login/Signup buttons -->
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=login" class="btn btn-outline">Login</a>
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn">Sign Up</a>
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
                    <?php if ($is_logged_in): ?>
                        <a href="/PFA/transpori/dashuser/share.php" class="btn">Share Experience</a>
                        <a href="/PFA/transpori/dashuser/tips.php" class="btn btn-outline">View Safety Tips</a>
                    <?php else: ?>
                        <a href="#services" class="btn scroll-link">Report an Incident</a>
                        <a href="#articles" class="btn btn-outline scroll-link">View Resources</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Contacts -->
    <section class="emergency-contacts">
        <div class="container">
            <div class="section-title">
                <h2>Emergency Contacts</h2>
            </div>
            <div class="emergency-grid">
                <div class="emergency-card">
                    <div class="emergency-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h3>Police Secours</h3>
                    <div class="emergency-number">197</div>
                </div>
                <div class="emergency-card">
                    <div class="emergency-icon">
                        <i class="fas fa-fire-extinguisher"></i>
                    </div>
                    <h3>Protection Civile</h3>
                    <div class="emergency-number">198</div>
                </div>
                <div class="emergency-card">
                    <div class="emergency-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Anti-Corruption Line</h3>
                    <div class="emergency-number">1899</div>
                </div>
                <div class="emergency-card">
                    <div class="emergency-icon">
                        <i class="fas fa-ambulance"></i>
                    </div>
                    <h3>Garde Nationale</h3>
                    <div class="emergency-number">198</div>
                </div>
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
                    <?php if (!$is_logged_in): ?>
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn">Join Our Community</a>
                    <?php endif; ?>
                </div>
                <div class="about-image">
                    <div class="placeholder-image">
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
                    <?php if ($is_logged_in): ?>
                        <a href="/PFA/transpori/dashuser/share.php" class="btn">Report Now</a>
                    <?php else: ?>
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn">Report Now</a>
                    <?php endif; ?>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Share Experiences</h3>
                    <p>Share your positive and negative transportation experiences to help others and drive improvements.</p>
                    <?php if ($is_logged_in): ?>
                        <a href="/PFA/transpori/dashuser/share.php" class="btn">Share Experience</a>
                    <?php else: ?>
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn">Share Experience</a>
                    <?php endif; ?>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Safety Statistics</h3>
                    <p>View statistics and trends about transportation safety issues to stay informed about current challenges.</p>
                    <?php if ($is_logged_in): ?>
                        <a href="/PFA/transpori/dashuser/index.php" class="btn">View Statistics</a>
                    <?php else: ?>
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn">View Statistics</a>
                    <?php endif; ?>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Community Events</h3>
                    <p>Participate in community events and awareness campaigns to promote transportation safety.</p>
                    <?php if ($is_logged_in): ?>
                        <a href="/PFA/transpori/dashuser/index.php" class="btn">View Events</a>
                    <?php else: ?>
                        <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn">View Events</a>
                    <?php endif; ?>
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
                        <?php if ($is_logged_in): ?>
                            <a href="/PFA/transpori/dashuser/tips.php" class="btn btn-outline">Read More</a>
                        <?php else: ?>
                            <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn btn-outline">Read More</a>
                        <?php endif; ?>
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
                        <?php if ($is_logged_in): ?>
                            <a href="/PFA/transpori/dashuser/tips.php" class="btn btn-outline">Read More</a>
                        <?php else: ?>
                            <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn btn-outline">Read More</a>
                        <?php endif; ?>
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
                        <?php if ($is_logged_in): ?>
                            <a href="/PFA/transpori/dashuser/tips.php" class="btn btn-outline">Read More</a>
                        <?php else: ?>
                            <a href="/PFA/transpori/login...signup/logsign.php?tab=signup" class="btn btn-outline">Read More</a>
                        <?php endif; ?>
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
                    <form>
                        <input type="text" placeholder="Your Name" required>
                        <input type="email" placeholder="Your Email" required>
                        <select required>
                            <option value="" disabled selected>Select Inquiry Type</option>
                            <option value="report">Report an Incident</option>
                            <option value="experience">Share an Experience</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="general">General Inquiry</option>
                        </select>
                        <textarea placeholder="Your Message" required></textarea>
                        <button type="submit" class="btn">Send Message</button>
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
                        <li><a href="#home" class="scroll-link">Home</a></li>
                        <li><a href="#about" class="scroll-link">About Us</a></li>
                        <li><a href="#services" class="scroll-link">Report Incident</a></li>
                        <li><a href="#articles" class="scroll-link">Resources</a></li>
                        <li><a href="#contact" class="scroll-link">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Services</h3>
                    <ul class="footer-links">
                        <li><a href="#services" class="scroll-link">Incident Reporting</a></li>
                        <li><a href="#services" class="scroll-link">Experience Sharing</a></li>
                        <li><a href="#services" class="scroll-link">Safety Statistics</a></li>
                        <li><a href="#articles" class="scroll-link">Educational Resources</a></li>
                        <li><a href="#services" class="scroll-link">Community Events</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for safety updates and transportation news.</p>
                    <form>
                        <input type="email" placeholder="Your Email" style="width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 5px; border: none;">
                        <button type="submit" class="btn" style="width: 100%;">Subscribe</button>
                    </form>
                </div>
            </div>
            <p class="copyright">&copy; 2025 Transpori. All Rights Reserved. | Safe Transportation Reporting System</p>
        </div>
    </footer>

    <script src="/PFA/transpori/home/javascript/transpori javascript.js"></script>
</body>
</html>
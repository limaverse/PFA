<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transpori | Safe Transportation Reporting System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="transpori css.css">
</head>
<body>
    <?php include 'components/header.php'; ?>
    
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

    <?php include 'components/emergency_contacts.php'; ?>
    <?php include 'components/about.php'; ?>
    <?php include 'components/services.php'; ?>
    <?php include 'components/articles.php'; ?>
    <?php include 'components/contact.php'; ?>
    <?php include 'components/footer.php'; ?>

    <script src="transpori javascript.js"></script>
</body>
</html>
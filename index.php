<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Smart Inventory</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        window.embeddedChatbotConfig = {
            chatbotId: "bErTeqv1TeRenV0eT7_ji",
            domain: "www.chatbase.co"
        }
    </script>
    <script src="https://www.chatbase.co/embed.min.js" chatbotId="bErTeqv1TeRenV0eT7_ji" domain="www.chatbase.co" defer></script>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">
        <img src="logo.png" alt="Smart Inventory Management Logo" style="height: 40px; margin-right: 20px;">
        SMARTECH INVENTORY
    </a>

    <div class="collapse navbar-collapse justify-content-end">
        <div>
            <a href="login.php" class="btn btn-outline-light">Login</a>
            <a href="register.php" class="btn btn-primary">Register</a>
        </div>
    </div>
</nav>

<div class="hero-section">
    <h1>Welcome to SmarTech Inventory Management System</h1>
    <p>Manage your resources, streamline operations, and connect seamlessly with others. Our system is designed to empower admins and users alike for a smoother, more efficient experience.</p>
    <a href="register.php" class="btn btn-primary">Get Started</a>
    <a href="#about" class="btn btn-outline-light">Learn More</a>
</div>

<div id="about" class="about-section">
    <h2>About SmarTech Inventory</h2>
    <p>Smart Inventory Management is a cutting-edge solution designed to streamline and optimize how businesses track, manage, and replenish their inventory. By leveraging advanced technologies like AI, IoT, and real-time analytics, it ensures that your stock levels are always accurate and your supply chain operates efficiently.</p>
</div>

<div class="features">
    <h3>Our Key Features</h3>
    <div class="feature-box">
        <i class="fas fa-chart-line"></i>
        <p>Real-Time Inventory Tracking</p>
    </div>
    <div class="feature-box">
        <i class="fas fa-boxes"></i>
        <p>Seamless Stock Management</p>
    </div>
    <div class="feature-box">
        <i class="fas fa-cogs"></i>
        <p>Automation & AI Tools</p>
    </div>
</div>

<div class="footer">
    <p>&copy; <?= date("Y"); ?> SmarTech Inventory Management System. All Rights Reserved.</p>
</div>

</body>
</html>

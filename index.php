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
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #005f6a, #009688);
            color: #fff;
        }
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000; 
            background-color: #004d4f;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s ease;
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .hero-section {
            text-align: center;
            padding: 100px 20px;
            animation: fadeIn 2s ease-in-out;
        }
        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
        }
        .hero-section p {
            font-size: 1.2rem;
            margin: 20px 0;
            line-height: 1.5;
        }
        .btn-primary, .btn-outline-light {
            font-size: 1.2rem;
            padding: 10px 30px;
            margin: 10px;
            border-radius: 25px;
        }
        .about-section {
            background-color: #fff;
            color: #333;
            padding: 60px 20px;
            text-align: center;
            animation: slideIn 1.5s ease-in-out;
        }
        .about-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .about-section p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .features {
            background-color: #005f6a;
            padding: 40px 20px;
            text-align: center;
        }
        .features h3 {
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 700;
        }
        .feature-box {
            display: inline-block;
            width: 30%;
            margin: 10px;
            text-align: center;
        }
        .feature-box i {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 10px;
        }
        .feature-box p {
            font-size: 1rem;
            line-height: 1.5;
        }
        .footer {
            background-color: #004d4f;
            color: #ddd;
            text-align: center;
            padding: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
<a class="navbar-brand" href="#">
  <img src="logo.png" alt="Smart Inventory Management Logo" style="height: 50px; margin-right: 30px;">
  SMARTECH INVENTORY MANAGEMENT
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

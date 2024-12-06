<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// User session data
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

// Profile picture logic
$uploadDir = 'uploads/profile_pictures/';
$placeholderImg = 'https://via.placeholder.com/40';
$profilePicture = $uploadDir . 'profile_' . $userId . '.jpg';

if (!file_exists($profilePicture)) {
    $profilePicture = $placeholderImg;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/header.css">
</head>
<body>
    <header class="dashboard-header">
    <div class="logo">
    <a href="#">
        <img src="logo.png" alt="Smart Inventory Logo" class="logo-img">
        Smart Inventory
    </a>
</div>

        <nav class="nav-links">
    <a href="Dashboard.php"><i class="fas fa-tachometer-alt"></i></a>
    <a href="view_products.php"><i class="fas fa-box"></i></a>
    <a href="view_orders.php"><i class="fas fa-box-open"></i></a>
    <a href="supplier_management.php"><i class="fas fa-users"></i></a>
    <a href="notifications.php"><i class="fas fa-bell"></i></a>
    <a href="report.php"><i class="fas fa-chart-line"></i></a>
</nav>

        <div class="header-right">
            <form class="search-bar" action="search.php" method="GET">
                <input type="text" name="search" id="search" placeholder="Search a product">
                <button type="submit">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
            <div class="profile-menu">
                <a href="profile.php" class="profile-btn">
                    <img src="<?php echo $profilePicture; ?>" alt="Profile Picture" class="profile-pic">
                </a>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>
</body>
</html>

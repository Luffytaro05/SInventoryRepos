<?php
session_start();
include('db_connect.php');

if (isset($_SESSION['username'])) {
    header("Location: Dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Store user data in session
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: Dashboard.php");
                exit();
            } else {
                $error_message = "Incorrect password. Please try again.";
            }
        } else {
            $error_message = "User not found. Please register first.";
        }
    }
        
        $stmt->close();
        $conn->close();
    }    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<div class="container d-flex">
    <div class="branding-section col-8">
    <h1>
  <img src="logo.png" alt="SMARTECH Logo" style="height: 50px; vertical-align: middle;">
  SMARTECH INVENTORY MANAGEMENT
</h1>

        <p>A SMARTECH Inventory Management System is an advanced, technology-driven solution designed to enhance the tracking, storage, and management of inventory within a business. It incorporates technologies such as IoT (Internet of Things), RFID (Radio Frequency Identification), and AI (Artificial Intelligence) to offer real-time updates, automate routine tasks like reordering, and analyze historical data for demand forecasting. This system centralizes inventory data, making it accessible through user-friendly dashboards and mobile apps, and supports integration with various sales channels, ensuring consistency across platforms. With features like automated stock tracking, barcode and QR code scanning, and advanced security protocols, businesses can achieve higher efficiency, reduce errors, and better manage stock levels to minimize costs and prevent stockouts. The result is not only improved operational accuracy but also enhanced customer satisfaction through better product availability and streamlined processes. Overall, a SMART Inventory Management System provides data-driven insights that enable businesses to make informed decisions, adapt to trends, and scale operations effectively, making it an essential tool for modern supply chain and inventory management.</p>
    </div>

    <div class="form-section col-4">
       <h2>Login</h2>
    <?php if (!empty($error_message)) : ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
        <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
    </form>
    </div>
</div>
    

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
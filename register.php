<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {

        $user_username = $_POST['username'];
        $user_email = $_POST['email'];
        $user_password = $_POST['password'];

        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $user_username, $user_email, $hashed_password);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $conn->error;
        }
    } else {
        echo "Please fill in all required fields.";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
<div>
<div class="container d-flex">
    <div class="branding-section col-8">
    <h1>
  <img src="logo.png" alt="SMARTECH Logo" style="height: 50px; vertical-align: middle;">
  SMARTECH INVENTORY MANAGEMENT
</h1>
        <p>A SMARTECH Inventory Management System is an advanced, technology-driven solution designed to enhance the tracking, storage, and management of inventory within a business. It incorporates technologies such as IoT (Internet of Things), RFID (Radio Frequency Identification), and AI (Artificial Intelligence) to offer real-time updates, automate routine tasks like reordering, and analyze historical data for demand forecasting. This system centralizes inventory data, making it accessible through user-friendly dashboards and mobile apps, and supports integration with various sales channels, ensuring consistency across platforms. With features like automated stock tracking, barcode and QR code scanning, and advanced security protocols, businesses can achieve higher efficiency, reduce errors, and better manage stock levels to minimize costs and prevent stockouts. The result is not only improved operational accuracy but also enhanced customer satisfaction through better product availability and streamlined processes. Overall, a SMART Inventory Management System provides data-driven insights that enable businesses to make informed decisions, adapt to trends, and scale operations effectively, making it an essential tool for modern supply chain and inventory management.</p>
    </div>

    
    <div class="form-section col-4">
        <h2>Create Account</h2>
        <form id="registerForm" method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <p>Already have an account? <a href="login.php" class="text-light">Login here!</a></p>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
document.getElementById("registerForm").addEventListener("submit", function (event) {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (password !== confirmPassword) {
        event.preventDefault();
        alert("Passwords do not match");
    }
});
</script>
</body>
</html>
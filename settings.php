<?php
session_start();
include 'includes/header.php';

// Include the database connection file
require 'db.php'; // Ensure this file contains the $pdo variable initialization

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userEmail = $_SESSION['user_email'];
$notificationsEnabled = $_SESSION['notifications_enabled'] ?? true;
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newEmail = trim($_POST['email']);
    $newPassword = trim($_POST['password']);
    $notifications = isset($_POST['notifications']) ? true : false;

    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $message = "Settings updated successfully!";
        $_SESSION['user_email'] = $userEmail = $newEmail;
        $_SESSION['notifications_enabled'] = $notificationsEnabled = $notifications;

        try {
            // Update user details in the database
            $stmt = $pdo->prepare("UPDATE users SET email = :email, notifications = :notifications WHERE id = :id");
            $stmt->execute([
                'email' => $newEmail,
                'notifications' => $notifications ? 1 : 0,
                'id' => $userId,
            ]);

            if (!empty($newPassword)) {
                // Save hashed password to the database
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
                $stmt->execute([
                    'password' => $hashedPassword,
                    'id' => $userId,
                ]);
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/settings.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>User Dashboard</h2>
            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </aside>

        <main class="main">
            <div class="form-container">
                <h2>Account Settings</h2>

                <?php if ($message): ?>
                    <div class="message <?php echo strpos($message, 'success') !== false ? 'success' : 'error'; ?>">
                        <?php echo $message; ?>
                        <i class="fas fa-times" onclick="this.parentElement.style.display='none'"></i>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required placeholder="Enter your email">

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter a new password">

                    <label for="notifications">
                        <input type="checkbox" id="notifications" name="notifications" <?php echo $notificationsEnabled ? 'checked' : ''; ?>>
                        Enable notifications
                    </label>

                    <button type="submit">Save Changes</button>
                </form>

                <div class="progress-bar">
                    <span style="width: 75%; background: #4caf50;"></span>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>
</html>

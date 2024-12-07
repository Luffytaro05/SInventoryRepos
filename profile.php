<?php
// Include the header
include 'includes/header.php';

// Include database connection file
include 'db_connect.php'; // Ensure this file sets up the $conn variable

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Get user data from session
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'];

// Profile picture directory
$uploadDir = 'uploads/profile_pictures/';
$placeholderImg = 'https://via.placeholder.com/150';
$profilePicture = $uploadDir . 'profile_' . $userId . '.jpg';

if (!file_exists($profilePicture)) {
    $profilePicture = $placeholderImg;
}

// Initialize $userBio
$userBio = "No bio available.";

// Handle profile update actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update bio
    if (isset($_POST['update_bio'])) {
        $newBio = trim($_POST['bio']);
        if (!empty($newBio)) {
            $newBio = htmlspecialchars($newBio);

            // Update the bio in the database
            $updateSql = "UPDATE users SET bio = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);

            if ($stmt) {
                $stmt->bind_param("si", $newBio, $userId);
                if ($stmt->execute()) {
                    $message = "Bio updated successfully!";
                    $userBio = $newBio; // Update the variable to show changes immediately

                    // Log the activity
                    $activitySql = "INSERT INTO activities (user_id, activity_text) VALUES (?, ?)";
                    $activityStmt = $conn->prepare($activitySql);
                    if ($activityStmt) {
                        $activityText = "Updated bio";
                        $activityStmt->bind_param("is", $userId, $activityText);
                        $activityStmt->execute();
                        $activityStmt->close();
                    }
                } else {
                    $message = "Error updating bio.";
                }
                $stmt->close();
            } else {
                die("Error preparing statement: " . $conn->error);
            }
        } else {
            $message = "Bio cannot be empty.";
        }
    }

    // Handle profile picture upload
    if (isset($_FILES['profile_picture'])) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $filePath = $uploadDir . 'profile_' . $userId . '.jpg';

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $profilePicture = $filePath;
            $message = "Profile picture updated successfully!";

            // Log the activity
            $activitySql = "INSERT INTO activities (user_id, activity_text) VALUES (?, ?)";
            $activityStmt = $conn->prepare($activitySql);
            if ($activityStmt) {
                $activityText = "Updated profile picture";
                $activityStmt->bind_param("is", $userId, $activityText);
                $activityStmt->execute();
                $activityStmt->close();
            }
        } else {
            $message = "Error uploading profile picture.";
        }
    }
}

// Fetch the current bio from the database
$sql = "SELECT bio FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($userBio);
    if ($stmt->fetch()) {
        // Successfully fetched bio
    }
    $stmt->close();
} else {
    die("Error preparing statement: " . $conn->error);
}

// Fetch recent activities from the database
$recentActivities = [];
$activitySql = "SELECT activity_text, activity_date FROM activities WHERE user_id = ? ORDER BY activity_date DESC LIMIT 5";
$activityStmt = $conn->prepare($activitySql);

if ($activityStmt) {
    $activityStmt->bind_param("i", $userId);
    $activityStmt->execute();
    $activityResult = $activityStmt->get_result();

    while ($activity = $activityResult->fetch_assoc()) {
        $recentActivities[] = $activity['activity_text'] . " (" . $activity['activity_date'] . ")";
    }
    $activityStmt->close();
} else {
    die("Error preparing statement: " . $conn->error);
}
$suppliersCount = 0; // Replace with your query to count suppliers
$customersCount = 0; // Replace with your query to count customers

// Query to count suppliers
$suppliersQuery = "SELECT COUNT(*) FROM suppliers WHERE id = ?";
$stmt = $conn->prepare($suppliersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($suppliersCount);
$stmt->fetch();
$stmt->close();

// Query to count customers
$customersQuery = "SELECT COUNT(*) FROM orders WHERE id = ?";
$stmt = $conn->prepare($customersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($customersCount);
$stmt->fetch();
$stmt->close();
// Include the header
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>User Dashboard</h2>
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </aside>

        <main class="profile-section">
            <section class="profile-header">
            <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
                <h1><?php echo htmlspecialchars($userName); ?></h1>
                <p><?php echo htmlspecialchars($userEmail); ?></p>
                <p class="user-bio"><?php echo nl2br(htmlspecialchars($userBio)); ?></p>

                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <textarea name="bio" placeholder="Update your bio..." required><?php echo htmlspecialchars($userBio); ?></textarea>
                    <input type="file" name="profile_picture" accept="image/*">
                    <button type="submit" name="update_bio">Save Changes</button>
                </form>
            </section>

            <section class="user-stats">
    <div class="stat-card">
        <h3>Suppliers</h3>
        <p><?php echo htmlspecialchars($suppliersCount); ?></p>
    </div>
    <div class="stat-card">
        <h3>Customers</h3>
        <p><?php echo htmlspecialchars($customersCount); ?></p>
    </div>
</section>

            <section class="recent-activities">
                <h2>Recent Activities</h2>
                <?php if (!empty($recentActivities)): ?>
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="activity-card">
                            <?php echo htmlspecialchars($activity); ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recent activities found.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>

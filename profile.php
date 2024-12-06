<?php include 'includes/header.php'; ?>

<?php
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

// Bio and activity placeholders
$userBio = "Passionate developer and designer.";
$recentActivities = [
    "Uploaded a new profile picture.",
    "Updated bio information.",
    "Joined the 'Tech Innovators' team.",
    "Completed the 'Advanced PHP' course.",
];

// Handle profile update actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_bio'])) {
        $newBio = trim($_POST['bio']);
        if (!empty($newBio)) {
            $userBio = htmlspecialchars($newBio);
            $message = "Bio updated successfully!";
        } else {
            $message = "Bio cannot be empty.";
        }
    }

    if (isset($_FILES['profile_picture'])) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $filePath = $uploadDir . 'profile_' . $userId . '.jpg';

        if (move_uploaded_file($fileTmpPath, $filePath)) {
            $profilePicture = $filePath;
            $message = "Profile picture updated successfully!";
        } else {
            $message = "Error uploading profile picture.";
        }
    }
}

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
                <img src="<?php echo $profilePicture; ?>" alt="Profile Picture">
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
                    <h3>Followers</h3>
                    <p>120</p>
                </div>
                <div class="stat-card">
                    <h3>Following</h3>
                    <p>80</p>
                </div>
                <div class="stat-card">
                    <h3>Posts</h3>
                    <p>45</p>
                </div>
            </section>

            <section class="recent-activities">
                <h2>Recent Activities</h2>
                <?php foreach ($recentActivities as $activity): ?>
                    <div class="activity-card">
                        <?php echo htmlspecialchars($activity); ?>
                    </div>
                <?php endforeach; ?>
            </section>
        </main>
    </div>
</body>
</html>

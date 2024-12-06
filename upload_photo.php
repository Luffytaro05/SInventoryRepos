<?php
$uploadDir = 'uploads/profile_pictures/';
$userId = 1; 
$targetFile = $uploadDir . 'profile_' . $userId . '.jpg';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['profile-photo']) && $_FILES['profile-photo']['error'] == 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['profile-photo']['type'], $allowedTypes)) {
        if (move_uploaded_file($_FILES['profile-photo']['tmp_name'], $targetFile)) {
            echo "Profile picture updated successfully.";
        } else {
            echo "Error saving the file.";
        }
    } else {
        echo "Invalid file type.";
    }
} else {
    echo "No file uploaded.";
}
?>


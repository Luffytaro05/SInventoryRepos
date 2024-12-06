<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];

    $conn = new mysqli('localhost', 'root', '', 'smart_inventory');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "UPDATE users SET username='$username', email='$email', bio='$bio' WHERE id=1"; 
    if ($conn->query($sql) === TRUE) {
        echo "Profile updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?>

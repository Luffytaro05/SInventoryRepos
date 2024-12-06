<?php
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_name = $_POST['supplier_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    if (empty($supplier_name) || empty($contact) || empty($email) || empty($address)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO suppliers (supplier_name, contact, email, address, created_at) VALUES (?, ?, ?, ?, NOW())");
    $result = $stmt->execute([$supplier_name, $contact, $email, $address]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Supplier added successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add supplier. Please try again.']);
    }
}
?>





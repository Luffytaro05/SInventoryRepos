<?php
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $supplier_name = $_POST['supplier_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    if (empty($id) || empty($supplier_name) || empty($contact) || empty($email) || empty($address)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE suppliers SET supplier_name = ?, contact = ?, email = ?, address = ? WHERE id = ?");
    $result = $stmt->execute([$supplier_name, $contact, $email, $address, $id]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Supplier updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update supplier. Please try again.']);
    }
}
?>








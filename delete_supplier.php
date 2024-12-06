<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if (empty($id)) {
        echo json_encode(['status' => 'error', 'message' => 'Supplier ID is required.']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
    $result = $stmt->execute([$id]);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Supplier deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete supplier. Please try again.']);
    }
}
?>





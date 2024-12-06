<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['product_id'];
    $restockQuantity = $_POST['restock_quantity'];

    try {
        $stmt = $pdo->prepare("UPDATE products SET stock = stock + :quantity WHERE id = :id");
        $stmt->execute(['quantity' => $restockQuantity, 'id' => $productId]);
        header('Location: notifications.php?success=Product restocked successfully.');
        exit();
    } catch (PDOException $e) {
        header('Location: notifications.php?error=Failed to restock product.');
        exit();
    }
} else {
    header('Location: notifications.php');
    exit();
}
?>

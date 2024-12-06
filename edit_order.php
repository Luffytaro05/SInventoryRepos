<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'];
    $customerName = $_POST['customer_name'];
    $productId = $_POST['product_id'];
    $quantity = $_POST['stock'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE orders SET customer_name = :customer_name, product_id = :product_id, stock = :stock, status = :status WHERE id = :id");
    $stmt->execute([
        ':customer_name' => $customerName,
        ':product_id' => $productId,
        ':stock' => $quantity,
        ':status' => $status,
        ':id' => $orderId
    ]);

    header('Location: view_orders.php');
    exit;
}
?>

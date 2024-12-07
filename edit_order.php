<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['order_id'];
    $customerName = $_POST['customer_name'];
    $productId = $_POST['product_id'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];
    $paymentMethod = $_POST['payment_method'];

    // Retrieve the price of the selected product
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = :product_id LIMIT 1");
    $stmt->execute([':product_id' => $productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $price = $product['price'] ?? 0; // Default to 0 if no price is found

    // Update the order with the price
    $stmt = $pdo->prepare("UPDATE orders SET product_id = :product_id, customer_name = :customer_name, stock = :stock, price = :price, status = :status, payment_method = :payment_method WHERE id = :id");
    $stmt->bindParam(':product_id', $productId);
    $stmt->bindParam(':customer_name', $customerName);
    $stmt->bindParam(':stock', $stock);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':payment_method', $paymentMethod);
    $stmt->bindParam(':id', $orderId);
    $stmt->execute();

    header('Location: view_orders.php');
}
<?php
include 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customerName = $_POST['customer_name'];
    $productId = $_POST['product_id'];
    $quantityOrdered = $_POST['stock'];
    $paymentMethod = $_POST['payment_method'];

    $pdo->beginTransaction();

    try {
        // Retrieve product details, including price
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :product_id LIMIT 1");
        $stmt->execute([':product_id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentStock = $product['quantity'] ?? $product['stock'] ?? null;
        $productPrice = $product['price'] ?? 0; // Get the price, default to 0 if not available

        if ($currentStock !== null && $currentStock >= $quantityOrdered) {
            $column = isset($product['quantity']) ? "quantity" : "stock";
            $stmt = $pdo->prepare("UPDATE products SET $column = $column - :quantity WHERE id = :product_id");
            $stmt->execute([':quantity' => $quantityOrdered, ':product_id' => $productId]);

            // Insert the order including price
            $stmt = $pdo->prepare("INSERT INTO orders (customer_name, product_id, stock, price, status, payment_method, created_at) VALUES (:customer_name, :product_id, :stock, :price, 'Pending', :payment_method, NOW())");
            $stmt->execute([
                ':customer_name' => $customerName,
                ':product_id' => $productId,
                ':stock' => $quantityOrdered,
                ':price' => $productPrice,
                ':payment_method' => $paymentMethod
            ]);

            $pdo->commit();

            $success = "Order placed successfully and stock updated!";
            header("Location: view_orders.php?success=" . urlencode($success));
            exit;
        } else {
            $pdo->rollBack();
            $error = "Insufficient stock for this order.";
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Error placing order: " . $e->getMessage();
    }
}

// Fetch products for the form dropdown
$products = $pdo->query("SELECT id, product_name FROM products")->fetchAll(PDO::FETCH_ASSOC);
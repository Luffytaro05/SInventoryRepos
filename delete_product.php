<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $check_orders_sql = "SELECT COUNT(*) FROM orders WHERE product_id = :product_id";
    $stmt_check = $pdo->prepare($check_orders_sql);
    $stmt_check->execute(['product_id' => $product_id]);
    $order_count = $stmt_check->fetchColumn();

    if ($order_count > 0) {
        header('Location: view_products.php?status=error&message=Cannot delete product with active orders');
        exit();
    } else {
        $delete_sql = "DELETE FROM products WHERE id = :id";
        $stmt_delete = $pdo->prepare($delete_sql);
        if ($stmt_delete->execute(['id' => $product_id])) {
            header('Location: view_products.php?status=deleted');
            exit();
        } else {
            echo "Error deleting product.";
        }
    }
}



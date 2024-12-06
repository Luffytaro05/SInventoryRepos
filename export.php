<?php
include 'db.php';

try {
    // Prepare and execute the query to fetch products
    $stmt = $pdo->query("
        SELECT p.id, p.product_name, p.price, p.quantity, s.supplier_name
        FROM products p
        LEFT JOIN suppliers s ON p.supplier_id = s.id
        ORDER BY p.product_name
    ");

    // Fetch products as an associative array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        header("Location: Dashboard.php?error=No data available to export.");
        exit();
    }

    $filename = "product_stock_report_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');

    fputcsv($output, array_keys($products[0]));

    foreach ($products as $product) {
        fputcsv($output, $product);
    }

    fclose($output);
    exit();
} catch (PDOException $e) {
    header("Location: Dashboard.php?error=Failed to export data: " . $e->getMessage());
    exit();
}


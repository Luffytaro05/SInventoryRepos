<?php
// Include database connection
include 'db.php';

// Set headers to download the CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="orders_export.csv"');

// Open a file in write mode to output CSV data
$output = fopen('php://output', 'w');

// Output CSV headers
$headers = ['Order ID', 'Product Name', 'Price', 'Customer Name', 'Quantity', 'Status', 'Payment Method', 'Order Date'];
fputcsv($output, $headers);

// Fetch orders from the database
$stmt = $pdo->prepare("
    SELECT o.id, p.product_name, p.price, o.customer_name, o.stock, o.status, o.payment_method, o.created_at 
    FROM orders o 
    JOIN products p ON o.product_id = p.id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output data rows as CSV
foreach ($orders as $order) {
    $row = [
        $order['id'],
        $order['product_name'],
        number_format($order['price'], 2),
        $order['customer_name'],
        $order['stock'],
        $order['status'],
        $order['payment_method'],
        $order['created_at']
    ];
    fputcsv($output, $row);
}

// Close the file
fclose($output);
exit();

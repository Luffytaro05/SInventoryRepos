<?php
include 'db.php';

try {
    $ordersStmt = $pdo->query("
        SELECT o.id AS Order_ID, o.customer_name AS Customer_Name, 
               p.product_name AS Product_Name, o.stock AS Quantity_Ordered, 
               p.stock AS Current_Stock, o.status AS Order_Status, 
               o.created_at AS Order_Date
        FROM orders o
        JOIN products p ON o.product_id = p.id
        ORDER BY o.created_at DESC
    ");
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($orders)) {
        header("Location: report.php?error=No data available to export.");
        exit();
    }

    $filename = "order_stock_report_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $output = fopen('php://output', 'w');

    fputcsv($output, array_keys($orders[0]));

    foreach ($orders as $order) {
        fputcsv($output, $order);
    }

    fclose($output);
    exit();
} catch (PDOException $e) {
    header("Location: report.php?error=Failed to export data: " . $e->getMessage());
    exit();
}

<?php
include 'db.php';

// Get the order ID from the URL parameter
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    die("Invalid order ID.");
}

// Fetch order details from the database
$stmt = $pdo->prepare("SELECT o.id, o.customer_name, o.stock, o.status, o.payment_method, o.created_at, p.product_name, p.price
                        FROM orders o
                        JOIN products p ON o.product_id = p.id
                        WHERE o.id = :id");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5; /* Light gray background for better contrast */
    margin: 0;
    padding: 20px;
}

.receipt {
    max-width: 600px;
    margin: auto;
    padding: 30px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
    border-top: 5px solid #007bff;
    overflow: hidden;
    transition: box-shadow 0.3s ease-in-out, transform 0.3s ease-in-out;
}

.receipt:hover {
    box-shadow: 0 8px 35px rgba(0, 0, 0, 0.15);
    transform: translateY(-3px);
}

.receipt-header {
    text-align: center;
    margin-bottom: 25px;
    border-bottom: 2px solid #f1f1f1;
    padding-bottom: 15px;
}

.receipt-header h2 {
    margin: 0;
    color: #007bff;
    font-size: 30px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.receipt-header p {
    margin: 5px 0 15px;
    color: #555;
    font-size: 14px;
}

.receipt-details {
    margin-bottom: 25px;
    line-height: 1.7;
    color: #333;
    font-size: 16px;
}

.receipt-details p {
    margin: 10px 0;
}

.receipt-details strong {
    color: #007bff;
    font-weight: 600;
}

.receipt-summary {
    margin-top: 20px;
    border-top: 2px dashed #ddd;
    padding-top: 15px;
    background: #f9f9f9;
    border-radius: 8px;
}

.receipt-summary p {
    font-weight: 500;
    font-size: 15px;
    color: #333;
}

.receipt-footer {
    text-align: center;
    margin-top: 30px;
}

.print-btn, .email-btn {
    background-color: #007bff;
    color: #ffffff;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    margin: 0 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: inline-block;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.print-btn:hover, .email-btn:hover {
    background-color: #0056b3;
    transform: scale(1.05);
}

.email-btn {
    background-color: #28a745;
    border-radius: 8px;
}

.email-btn:hover {
    background-color: #218838;
}

.print-btn:focus, .email-btn:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.5);
}

@media print {
    body {
        margin: 0;
        padding: 0;
    }
    .print-btn, .email-btn {
        display: none;
    }
}

    </style>
</head>
<body>

<div class="receipt">
    <div class="receipt-header">
        <h2>Order Receipt</h2>
        <p>Order ID: <?= htmlspecialchars($order['id']) ?></p>
    </div>

    <div class="receipt-details">
        <p><strong>Customer Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Product Name:</strong> <?= htmlspecialchars($order['product_name']) ?></p>
        <p><strong>Price:</strong> $<?= number_format($order['price'], 2) ?></p>
        <p><strong>Quantity:</strong> <?= htmlspecialchars($order['stock']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
        <p><strong>Order Date:</strong> <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></p>
    </div>

    <div class="receipt-footer">
        <button class="btn btn-primary print-btn" onclick="window.print()">Print Receipt</button>
    </div>
</div>

</body>
</html>

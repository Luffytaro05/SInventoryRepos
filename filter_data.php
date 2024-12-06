<?php
// Include the database connection
include 'includes/db_connection.php'; 

// Get the start and end date from the request
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// Prepare the SQL query
$query = "SELECT * FROM orders WHERE 1=1";
$params = [];

// Add date filters if provided
if ($startDate && $endDate) {
    $query .= " AND order_date BETWEEN :startDate AND :endDate";
    $params[':startDate'] = $startDate;
    $params[':endDate'] = $endDate;
} elseif ($startDate) {
    $query .= " AND order_date >= :startDate";
    $params[':startDate'] = $startDate;
} elseif ($endDate) {
    $query .= " AND order_date <= :endDate";
}

// Execute the query
$stmt = $pdo->prepare($query);
$stmt->execute($params);

// Fetch the results and display them
if ($stmt->rowCount() > 0) {
    echo "<table class='table'>";
    echo "<thead><tr><th>Order ID</th><th>Customer Name</th><th>Product</th><th>Quantity</th><th>Order Date</th></tr></thead>";
    echo "<tbody>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$row['order_id']}</td>";
        echo "<td>{$row['customer_name']}</td>";
        echo "<td>{$row['product_name']}</td>";
        echo "<td>{$row['quantity']}</td>";
        echo "<td>{$row['order_date']}</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No results found for the selected date range.</p>";
}
?>

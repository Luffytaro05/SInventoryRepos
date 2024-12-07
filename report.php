<?php
include 'db_connect.php'; 
include 'includes/header.php'; 
include 'db.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}
// Get user data from session
$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

$error = '';
$success = '';

try {
    $ordersStmt = $pdo->query("
        SELECT o.id, o.customer_name, o.stock AS quantity_ordered, o.status, o.created_at,
               p.product_name, p.stock AS current_stock
        FROM orders o
        JOIN products p ON o.product_id = p.id
        ORDER BY o.created_at DESC
    ");
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error generating report: " . $e->getMessage();
}

if (isset($_GET['delete_order_id'])) {
    $orderId = $_GET['delete_order_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
        $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $success = "Order deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting order: " . $e->getMessage();
    }
}
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order and Stock Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/report.css">
</head>
<body>

<div class="container">
    <h2>Order and Stock Report</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product Name</th>
                    <th>Quantity Ordered</th>
                    <th>Current Stock</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']); ?></td>
                            <td><?= htmlspecialchars($order['customer_name']); ?></td>
                            <td><?= htmlspecialchars($order['product_name']); ?></td>
                            <td><?= htmlspecialchars($order['quantity_ordered']); ?></td>
                            <td><?= htmlspecialchars($order['current_stock']); ?></td>
                            <td>
                                <span class="badge badge-<?= $order['status'] == 'Completed' ? 'success' : ($order['status'] == 'Cancelled' ? 'danger' : 'warning'); ?>">
                                    <?= htmlspecialchars($order['status']); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($order['created_at']); ?></td>
                            <td>
                                <form method="post" action="delete_order.php" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No data available</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <form action="export_report.php" method="post">
        <button type="submit" class="btn-export">Export to CSV</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<?php include 'includes/footer.php'; ?>
</html>


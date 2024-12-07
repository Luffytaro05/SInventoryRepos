<?php
include 'includes/header.php';
include 'db_connect.php';
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

$lowStockThreshold = 10; 
$lowStockNotifications = [];

$stmt = $pdo->prepare("SELECT id, product_name, stock FROM products WHERE stock < :threshold");
$stmt->execute(['threshold' => $lowStockThreshold]);
$lowStockNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$newOrdersCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE order_date >= CURDATE()")->fetchColumn();

if (isset($_GET['success'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
} elseif (isset($_GET['error'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
}
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Smart Inventory Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/notifications.css">
</head>
<body>
    <div class="notifications-container">
        <h1 class="text-center">Notifications</h1>

        <div class="card">
            <div class="card-header">
                <h2>Low Stock Notifications</h2>
            </div>
            <div class="card-body">
                <?php if (count($lowStockNotifications) > 0): ?>
                    <div class="list-group">
                        <?php foreach ($lowStockNotifications as $product): ?>
                            <div class="notification-item list-group-item list-group-item-warning">
                                <i class="fas fa-exclamation-triangle alert-icon"></i>
                                <div>
                                    <strong><?= htmlspecialchars($product['product_name']) ?></strong>: 
                                    Only <?= $product['stock'] ?> left in stock!
                                </div>
                                <button class="btn btn-sm btn-primary float-right" onclick="showRestockModal(<?= $product['id'] ?>, <?= $product['stock'] ?>)">Restock</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-success">All products are sufficiently stocked.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>General Notifications</h2>
            </div>
            <div class="card-body">
                <p>You have <strong><?= $newOrdersCount ?></strong> new orders today.</p>
            </div>
        </div>

        <div id="restockModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Restock Product</h5>
                        <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="restockForm" action="restock_product.php" method="POST">
                            <input type="hidden" name="product_id" id="product_id">
                            <div class="form-group">
                                <label for="restock_quantity">Quantity to Add:</label>
                                <input type="number" class="form-control" name="restock_quantity" id="restock_quantity" required>
                            </div>
                            <button type="submit" class="btn btn-success">Restock</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="notification-alert">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle alert-icon"></i>
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle alert-icon"></i>
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function showRestockModal(productId, currentQuantity) {
            document.getElementById('product_id').value = productId;
            document.getElementById('restock_quantity').value = ''; 
            document.getElementById('restockModal').style.display = "block";
            $('.modal').modal('show'); 
        }

        function closeModal() {
            document.getElementById('restockModal').style.display = 'none';
            $('.modal').modal('hide');
        }

        <?php if (count($lowStockNotifications) > 0): ?>
            alert('Attention: You have products with low stock or out-of-stock conditions. Please restock.');
        <?php endif; ?>
    </script>
</body>
</html>
<?php include 'includes/footer.php'; ?>
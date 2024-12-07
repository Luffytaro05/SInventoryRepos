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

// Fetch orders with pagination support
$perPage = 10; // Number of orders per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$ordersStmt = $pdo->prepare("
    SELECT o.id, o.product_id, p.product_name AS product_name, p.price AS product_price, o.customer_name, o.stock, o.status, o.payment_method, o.created_at 
    FROM orders o 
    JOIN products p ON o.product_id = p.id
    ORDER BY o.created_at DESC
    LIMIT :limit OFFSET :offset
");
$ordersStmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
$ordersStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$ordersStmt->execute();
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

$productsStmt = $pdo->query("SELECT id, product_name, stock, price FROM products");
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get total number of orders for pagination
$totalOrdersStmt = $pdo->query("SELECT COUNT(*) FROM orders");
$totalOrders = $totalOrdersStmt->fetchColumn();
$totalPages = ceil($totalOrders / $perPage);
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/view_orders.css">
</head>
<body>

<div class="container">
    <h2>Order Management</h2>

    <!-- Filter Form -->
    <div class="filter-form mb-4">
        <form id="filterForm" class="form-inline">
            <label for="statusFilter" class="mr-2">Status:</label>
            <select class="form-control mb-2 mr-sm-2" id="statusFilter">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <label for="productFilter" class="mr-2">Product:</label>
            <input type="text" class="form-control mb-2 mr-sm-2" id="productFilter" placeholder="Product Name">

            <label for="dateFilter" class="mr-2">Date:</label>
            <input type="date" class="form-control mb-2 mr-sm-2" id="dateFilter">

            <button type="button" class="btn btn-primary mb-2" onclick="filterTable()">Filter</button>
        </form>
    </div>

    <!-- Message Display -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Operation successful!</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">An error occurred. Please try again.</div>
    <?php endif; ?>

    <!-- Download CSV Button -->
    <a href="export_orders.php" class="btn btn-info mb-3">Download Orders CSV</a>

    <!-- Add Order Button -->
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addOrderModal">Add Order</button>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="orderTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Customer Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr data-status="<?= htmlspecialchars($order['status']) ?>" data-product="<?= htmlspecialchars($order['product_name']) ?>" data-date="<?= htmlspecialchars($order['created_at']) ?>">
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['product_name']) ?></td>
                            <td>$<?= number_format($order['product_price'], 2) ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= htmlspecialchars($order['stock']) ?></td>
                            <td>
                                <?php if ($order['status'] == 'Pending'): ?>
                                    <span class="btn-status status-pending">Pending</span>
                                <?php elseif ($order['status'] == 'Completed'): ?>
                                    <span class="btn-status status-completed">Completed</span>
                                <?php else: ?>
                                    <span class="btn-status status-cancelled">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editOrderModal-<?= $order['id'] ?>">Edit</button>
                                <a href="delete_order.php?id=<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                                <a href="print_receipt.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm" target="_blank">Print Receipt</a>
                            </td>
                        </tr>

                        <!-- Edit Order Modal -->
                        <div class="modal fade" id="editOrderModal-<?= $order['id'] ?>" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="edit_order.php">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editOrderModalLabel">Edit Order</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <div class="form-group">
                                                <label for="customer_name">Customer Name:</label>
                                                <input type="text" class="form-control" name="customer_name" value="<?= htmlspecialchars($order['customer_name']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="product_id">Product:</label>
                                                <select class="form-control" name="product_id" required>
                                                    <?php foreach ($products as $product): ?>
                                                        <option value="<?= $product['id'] ?>" <?= (isset($order['product_id']) && $product['id'] == $order['product_id']) ? 'selected' : '' ?>>
                                                            <?= $product['product_name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="stock">Quantity:</label>
                                                <input type="number" class="form-control" name="stock" value="<?= htmlspecialchars($order['stock']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status:</label>
                                                <select class="form-control" name="status" required>
                                                    <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                    <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="payment_method">Payment Method:</label>
                                                <select class="form-control" name="payment_method" required>
                                                    <option value="Credit Card" <?= $order['payment_method'] == 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
                                                    <option value="Gcash" <?= $order['payment_method'] == 'Gcash' ? 'selected' : '' ?>>Gcash</option>
                                                    <option value="Bank Transfer" <?= $order['payment_method'] == 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                                    <option value="Cash" <?= $order['payment_method'] == 'Cash' ? 'selected' : '' ?>>Cash</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Add Order Modal -->
    <div class="modal fade" id="addOrderModal" tabindex="-1" aria-labelledby="addOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="add_order.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addOrderModalLabel">Add New Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_name">Customer Name:</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="product_id">Product:</label>
                            <select class="form-control" name="product_id" required>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['id'] ?>"><?= $product['product_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="stock">Quantity:</label>
                            <input type="number" class="form-control" name="stock" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" name="status" required>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method:</label>
                            <select class="form-control" name="payment_method" required>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Gcash">Gcash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Order</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
function filterTable() {
    let statusFilter = $('#statusFilter').val().toLowerCase();
    let productFilter = $('#productFilter').val().toLowerCase();
    let dateFilter = $('#dateFilter').val();

    $('#orderTable tbody tr').each(function() {
        let status = $(this).data('status').toLowerCase();
        let product = $(this).data('product').toLowerCase();
        let date = $(this).data('date');

        if (
            (statusFilter === '' || status.includes(statusFilter)) &&
            (productFilter === '' || product.includes(productFilter)) &&
            (dateFilter === '' || date.includes(dateFilter))
        ) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}
</script>

</body>
<?php include 'includes/footer.php'; ?>
</html>

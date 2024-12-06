<?php
include 'includes/header.php';
include 'db.php';

$ordersStmt = $pdo->query("
    SELECT o.id, o.product_id, p.product_name AS product_name, o.customer_name, o.stock, o.status, o.created_at 
    FROM orders o 
    JOIN products p ON o.product_id = p.id
    ORDER BY o.created_at DESC
");
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

$productsStmt = $pdo->query("SELECT id, product_name, stock FROM products");
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <div class="filter-form">
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

    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addOrderModal">Add Order</button>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="orderTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Customer Name</th>
                    <th>Quantity</th>
                    <th>Status</th>
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
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editOrderModal-<?= $order['id'] ?>">Edit</button>
                                <a href="delete_order.php?id=<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                            </td>
                        </tr>

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
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update Order</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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
                            <option value="">Select a product</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['id'] ?>"><?= $product['product_name'] ?> (Stock: <?= $product['stock'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock">Quantity:</label>
                        <input type="number" class="form-control" name="stock" required min="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Add Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function filterTable() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const productFilter = document.getElementById('productFilter').value.toLowerCase();
        const dateFilter = document.getElementById('dateFilter').value;

        const rows = document.querySelectorAll('#orderTable tbody tr');

        rows.forEach(row => {
            const status = row.getAttribute('data-status').toLowerCase();
            const product = row.getAttribute('data-product').toLowerCase();
            const date = row.getAttribute('data-date');

            if (
                (statusFilter === '' || status.includes(statusFilter)) &&
                (productFilter === '' || product.includes(productFilter)) &&
                (dateFilter === '' || date.includes(dateFilter))
            ) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
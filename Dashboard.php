<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'db.php';

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrdersToday = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$totalSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$username = $_SESSION['user_name'];

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = CURDATE()");
    $count = $stmt->fetchColumn();
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS low_stock_count FROM products WHERE stock < 10");
    $lowStockProducts = $stmt->fetch(PDO::FETCH_ASSOC)['low_stock_count'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS out_of_stock_count FROM products WHERE stock = 0");
    $outOfStockProducts = $stmt->fetch(PDO::FETCH_ASSOC)['out_of_stock_count'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    $stmt = $pdo->query("SELECT id, product_name, stock FROM products WHERE stock > 10");
    $productsToUpdate = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productsToUpdate as $product) {
        $newStock = $product['stock']; 
        $updateStmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
        $updateStmt->execute([':stock' => $newStock, ':id' => $product['id']]);
    }
} catch (PDOException $e) {
    echo "Error updating product stock: " . $e->getMessage();
}

$sql = "SELECT p.product_name, p.stock AS quantity
        FROM products p";
$stmt = $pdo->query($sql);
$productLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
$productsStmt = $pdo->query("SELECT id, product_name, stock FROM products");
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Inventory Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head> 
<body>
    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>WELCOME, <?= htmlspecialchars($username) ?>!!!</h1>
            <p>Here's a quick overview of your inventory today</p>
        </div>

        <button id="darkModeToggle" class="btn btn-dark dark-mode-toggle">Toggle Dark Mode</button>

        <div class="search-bar">
    <input type="text" id="searchInput" class="form-control" placeholder="Search products or orders..." onkeyup="searchFunction()">
</div>
<div id="searchResults" class="search-results"></div>


        <div class="stats-cards">
            <div class="stats-card" data-toggle="tooltip" title="Total number of products in the inventory">
                <h3><i class="fas fa-box"></i> Total Products</h3>
                <p><?= number_format($totalProducts) ?></p>
            </div>
            <div class="stats-card" data-toggle="tooltip" title="Products that are running low on stock">
                <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h3>
                <p><?= number_format($lowStockProducts) ?></p>
            </div>
            <div class="stats-card" data-toggle="tooltip" title="Products that are currently out of stock">
                <h3><i class="fas fa-times-circle"></i> Out of Stock</h3>
                <p><?= number_format($outOfStockProducts) ?></p>
            </div>
            <div class="stats-card" data-toggle="tooltip" title="Total number of orders processed today">
                <h3><i class="fas fa-shopping-cart"></i> Orders Today</h3>
                <p><?= number_format($totalOrdersToday) ?></p>
            </div>
            <div class="stats-card" data-toggle="tooltip" title="Total number of suppliers">
                <h3><i class="fas fa-truck"></i> Suppliers</h3>
                <p><?= $totalSuppliers ?></p>
            </div>
        </div>

        <div class="quick-actions">
            <a href="add_product.php" class="action-btn">+ Add Product</a>
            <a href="#" class="action-btn" data-toggle="modal" data-target="#addOrderModal">+ Create Order</a>
            <a href="supplier_management.php" class="action-btn">Manage Suppliers</a>
            <a href="notifications.php" class="action-btn">View All Notifications</a>
            <a href="export.php" class="action-btn">Export Data</a>
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
                                <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Enter customer name" required>
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
                                <input type="number" class="form-control" id="stock" name="stock" placeholder="Enter quantity" required min="1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Create Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="notifications">
            <h2>Notifications</h2>
            <?php if ($lowStockProducts > 0): ?>
                <p class="notification">⚠️ You have <?= $lowStockProducts ?> products with low stock!</p>
            <?php else: ?>
                <p class="notification">✅ All products are sufficiently stocked.</p>
            <?php endif; ?>
            <?php if ($outOfStockProducts > 0): ?>
                <p class="notification">⚠️ You have <?= $outOfStockProducts ?> products that are out of stock!</p>
            <?php else: ?>
                <p class="notification">✅ All products are sufficiently not out of stock.</p>
            <?php endif; ?>
        </div>

        <div class="chart-section">
    <h2>Stock Levels Overview</h2>
    <div class="chart-container-wrapper">
        <div class="chart-container">
            <canvas id="stockChart"></canvas>
        </div>
        <div class="chart-container">
        <canvas id="productLevelsChart"></canvas>
    </div>
    </div>
</div>


        <script>
            document.getElementById('darkModeToggle').addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                document.querySelectorAll('.stats-card').forEach(function(card) {
                    card.classList.toggle('dark-mode');
                });
                document.querySelectorAll('.quick-actions .action-btn').forEach(function(btn) {
                    btn.classList.toggle('dark-mode');
                });
                document.querySelector('.notifications').classList.toggle('dark-mode');
                document.querySelector('.chart-section').classList.toggle('dark-mode');
            });

            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });

            function searchFunction() {
                let input = document.getElementById('searchInput').value.toLowerCase();
                let items = document.querySelectorAll('.product-item, .order-item');
                items.forEach(item => {
                    if (item.textContent.toLowerCase().includes(input)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            const ctxStock = document.getElementById('stockChart').getContext('2d');
            const stockChart = new Chart(ctxStock, {
                type: 'bar',
                data: {
                    labels: ['In Stock', 'Low Stock', 'Out of Stock'],
                    datasets: [{
                        label: 'Stock Levels',
                        data: [
                            <?= $totalProducts - $lowStockProducts ?>, 
                            <?= $lowStockProducts ?>,
                            <?= $outOfStockProducts ?> 
                        ],
                        backgroundColor: ['#008080', '#f39c12', '#e74c3c'],
                        borderColor: ['#005f6a', '#d0890b', '#c0392b'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    onClick: function(e) {
                        var activePoints = this.getElementsAtEvent(e);
                        if (activePoints.length > 0) {
                            var datasetIndex = activePoints[0].datasetIndex;
                            var index = activePoints[0].index;
                            var label = this.data.labels[index];
                            alert('You clicked on ' + label);
                        }
                    }
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('productLevelsChart').getContext('2d');

    var productNames = <?= json_encode(array_column($productLevels, 'product_name')) ?>; 
    var stockLevels = <?= json_encode(array_column($productLevels, 'quantity')) ?>; 

    var chart = new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: productNames, 
            datasets: [{
                label: 'Stock Level',
                data: stockLevels, 
                backgroundColor: 'rgba(75, 192, 192, 0.2)', 
                borderColor: 'rgba(75, 192, 192, 1)', 
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true, 
                    title: {
                        display: true,
                        text: 'Stock Level' 
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Product Name' 
                    },
                    ticks: {
                        autoSkip: false, 
                        maxRotation: 45, 
                        minRotation: 45
                    }
                }
            }
        }
    });
});

function searchFunction() {
    const searchQuery = document.getElementById('searchInput').value.trim();
    const resultsContainer = document.getElementById('searchResults');

    if (searchQuery === '') {
        resultsContainer.innerHTML = '<p>Please enter a search term.</p>';
        return;
    }

    // Fetch search results from the server
    fetch(`search.php?search=${encodeURIComponent(searchQuery)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            resultsContainer.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching search results:', error);
            resultsContainer.innerHTML = '<p>There was an error fetching the search results. Please try again later.</p>';
        });
}
        </script>
    </div>
</body>
<?php include 'includes/footer.php'; ?>
</html>
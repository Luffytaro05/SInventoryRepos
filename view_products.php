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

$sql = "SELECT p.id, p.product_name, p.price, p.stock AS quantity, s.supplier_name 
        FROM products p 
        LEFT JOIN suppliers s ON p.supplier_id = s.id";
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$lowStockThreshold = 10;

?>
<?php foreach ($products as $product): ?>
    <tr>
        <td><?= htmlspecialchars($product['product_name']); ?></td>
        <td><?= htmlspecialchars($product['quantity']); ?></td>
        
        <td>$<?= isset($product['price']) ? number_format($product['price'], 2) : '0.00'; ?></td>
        
    </tr>
<?php endforeach; ?>

<?php if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
    <div class="error-message">
        <?= htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>
<?php
function renderLowStockSummary($lowStockThreshold) {
    // Ensure the threshold is a valid number
    if (!is_numeric($lowStockThreshold) || $lowStockThreshold <= 0) {
        throw new InvalidArgumentException("Invalid threshold value.");
    }

    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/view_products.css">
</head>
<body>

<div class="container">
    <div class="card p-4 shadow">
        <h2 class="text-center mb-4">Product List</h2>

        <div class="search-bar">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by product name...">
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center">
                Product added successfully!
            </div>
        <?php endif; ?>

        <div class="mb-3 text-right">
            <a href="add_product.php" class="btn btn-custom">
                Add New Product <i class="fas fa-plus"></i>
            </a>
        </div>
        
        <div class="low-stock-summary">
            <p>🔴 Products with low stock (below <?= $lowStockThreshold ?> units) are highlighted.</p>
        </div>
        <?php if (!empty($notifications)): ?>
        <div class="notifications">
            <h2>Notifications</h2>
            <?php foreach ($notifications as $notification): ?>
                <p class="notification"><?= htmlspecialchars($notification); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
            <table class="table table-bordered table-hover" id="productsTable">
                <thead class="thead-dark">
                    <tr>
                        <th onclick="sortTable(0)">ID <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(1)">Product Name <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(2)">Price <i class="fas fa-sort"></i></th>
                        <th onclick="sortTable(3)">Quantity <i class="fas fa-sort"></i></th>
                        <th>Supplier</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= $product['product_name'] ?></td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td><?= $product['quantity'] ?></td>
                                <td><?= isset($product['supplier_name']) ? htmlspecialchars($product['supplier_name']) : 'Unknown'; ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                    <button class="btn btn-warning btn-sm" onclick="showRestockModal(<?= $product['id'] ?>, <?= $product['quantity'] ?>)">Restock</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="no-results">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    

        <div id="restockModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Restock Product</h2>
        <form id="restockForm" method="POST" action="restock_product.php">
            <input type="hidden" name="product_id" id="restock_product_id">
            <label for="restock_quantity">New Stock Quantity:</label>
            <input type="number" id="restock_quantity" name="restock_quantity" required min="1">
            <button type="submit">Restock</button>
        </form>
    </div>
</div>

<script>
function showRestockModal(productId, currentStock) {
    document.getElementById('restock_product_id').value = productId;
    document.getElementById('restock_quantity').value;
    document.getElementById('restockModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('restockModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('restockModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </nav>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        var filter = this.value.toUpperCase();
        var rows = document.querySelectorAll('#productsTable tbody tr');
        rows.forEach(function(row) {
            var productName = row.cells[1].textContent.toUpperCase();
            if (productName.indexOf(filter) > -1) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    function sortTable(columnIndex) {
        var table = document.getElementById('productsTable');
        var rows = Array.from(table.rows).slice(1);
        var isAsc = table.getAttribute('data-sort') === 'asc';
        rows.sort(function(rowA, rowB) {
            var cellA = rowA.cells[columnIndex].textContent.trim();
            var cellB = rowB.cells[columnIndex].textContent.trim();
            return isAsc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });
        rows.forEach(function(row) {
            table.appendChild(row);
        });
        table.setAttribute('data-sort', isAsc ? 'desc' : 'asc');
    }
    document.addEventListener("DOMContentLoaded", () => {
    highlightLowStockRows();

    const tableHeaders = document.querySelectorAll("#productsTable thead th");
    tableHeaders.forEach((header, index) => {
        header.addEventListener("click", () => sortTable(index));
    });
});

function highlightLowStockRows() {
    const rows = document.querySelectorAll("#productsTable tbody tr");
    rows.forEach(row => {
        const quantityCell = row.children[3]; // Assuming Quantity is the 4th column
        const quantity = parseInt(quantityCell.textContent.trim(), 10);
        if (!isNaN(quantity) && quantity < 10) { // Threshold for low stock
            row.classList.add("low-stock-row");
        }
    });
}
</script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
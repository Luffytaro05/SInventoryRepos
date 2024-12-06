<?php
ob_start(); 
include 'includes/header.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $supplier = (int)$_POST['supplier'];

    if (!empty($name) && $price > 0 && $stock > 0 && $supplier > 0) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM suppliers WHERE id = :supplier");
        $stmt->execute([':supplier' => $supplier]);
        $supplierExists = $stmt->fetchColumn();

        if ($supplierExists) {
            $stmt = $pdo->prepare("INSERT INTO products (product_name, price, stock, supplier_id) 
                                   VALUES (:name, :price, :stock, :supplier)");
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':stock' => $stock,
                ':supplier' => $supplier,
            ]);

            header("Location: view_products.php");
            exit();
        } else {
            $error_message = "The specified supplier does not exist.";
        }
    } else {
        $error_message = "Please fill all fields with valid data.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/add_product.css">
</head>
<body>

<div class="container">
    <div class="card">
        <h2 class="text-center mb-4">Add Product</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <form id="addProductForm" action="add_product.php" method="POST">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" placeholder="Enter product price" min="0" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="stock">Quantity</label>
                <input type="number" class="form-control" id="stock" name="stock" placeholder="Enter product quantity" min="1" required>
            </div>
            <div class="form-group">
                <label for="supplier">Supplier ID</label>
                <input type="number" class="form-control" id="supplier" name="supplier" placeholder="Enter supplier ID" min="1" required>
            </div>
            <button type="submit" class="btn btn-custom btn-block">Add Product <i class="fas fa-plus"></i></button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>

<script>
document.getElementById('addProductForm').onsubmit = function(event) {
    const name = document.getElementById('name').value.trim();
    const price = parseFloat(document.getElementById('price').value);
    const stock = parseInt(document.getElementById('stock').value);
    const supplier = parseInt(document.getElementById('supplier').value);

    if (name === '' || price <= 0 || stock <= 0 || supplier <= 0) {
        event.preventDefault();  
        document.querySelector('.alert-danger').style.display = 'block'; 
    } else {
        document.querySelector('.alert-danger').style.display = 'none'; 
    }
};
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
<?php
ob_end_flush(); 
?>
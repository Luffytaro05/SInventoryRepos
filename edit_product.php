<?php
require 'db.php'; 

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $stock = $_POST['stock']; 
        $supplier_id = $_POST['supplier_id'];

        $update_sql = "UPDATE products SET product_name = :product_name, price = :price, stock = :stock, supplier_id = :supplier_id WHERE id = :id";
        $update_stmt = $pdo->prepare($update_sql);

        try {
            $update_stmt->execute([
                'product_name' => $product_name,
                'price' => $price,
                'stock' => $stock,
                'supplier_id' => $supplier_id,
                'id' => $product_id
            ]);
            $success = "Product updated successfully.";
            $product['stock'] = $stock; 
        } catch (PDOException $e) {
            $error = "Error updating product: " . $e->getMessage();
        }
    }
} else {
    $error = "Product ID not specified.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .success-message {
            color: green;
            margin-bottom: 15px;
        }

    </style>
</head>
<body>

<div class="container">
    <h1><i class="fas fa-edit"></i> Edit Product</h1>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error); ?>
        </div>
    <?php elseif (isset($success)): ?>
        <div class="success-message">
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="edit_product.php?id=<?= htmlspecialchars($product['id']); ?>">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($product['product_name'] ?? ''); ?>" required>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price'] ?? ''); ?>" required step="0.01">

        <label for="quantity">quantity:</label>
        <input type="number" id="stock" name="stock" value="<?= htmlspecialchars($product['stock'] ?? ''); ?>" required>

        <label for="supplier_id">Supplier:</label>
        <select id="supplier_id" name="supplier_id" required>
            <?php
            $supplier_sql = "SELECT id, supplier_name FROM suppliers";
            $supplier_stmt = $pdo->prepare($supplier_sql);
            $supplier_stmt->execute();
            $suppliers = $supplier_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($suppliers as $supplier) {
                $selected = ($supplier['id'] == $product['supplier_id']) ? 'selected' : '';
                echo "<option value='{$supplier['id']}' $selected>{$supplier['supplier_name']}</option>";
            }
            ?>
        </select>

        <button type="submit">Update Product</button>
    </form>
</div>

</body>
</html>

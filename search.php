<?php
include 'db.php';

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

$stmt = $pdo->prepare("SELECT * FROM products WHERE product_name LIKE :searchTerm");
$stmt->execute([':searchTerm' => '%' . $searchTerm . '%']);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .dashboard-header {
            background-color: #005f6a;
            color: #fff;
            padding: 20px;
            text-align: center;
        }
        .search-results {
            padding: 30px;
        }
        .search-results h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .product-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 300px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .product-img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .product-item h3 {
            font-size: 1.2rem;
            color: #005f6a;
            margin: 10px 0;
        }
        .product-item p {
            color: #555;
            margin: 5px 0;
        }
        .no-results {
            text-align: center;
            color: #666;
            margin-top: 50px;
        }
        .back-btn {
            display: block;
            margin: 20px auto;
            text-align: center;
            padding: 10px 20px;
            background-color: #005f6a;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
        }
        .back-btn:hover {
            background-color: #007b83;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <h1>Smart Inventory Search</h1>
    </header>

    <div class="search-results">
        <h1>Search Results</h1>
        <?php if (count($products) > 0): ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <h3><?= htmlspecialchars($product['product_name']) ?></h3>
                        <p><strong>Price:</strong> $<?= number_format($product['price'], 2) ?></p>
                        <p><strong>Stock:</strong> <?= $product['stock'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <p>No products found for your search term <strong>"<?= htmlspecialchars($searchTerm) ?>"</strong>.</p>
                <a href="index.php" class="back-btn">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

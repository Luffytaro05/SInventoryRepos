<?php
include 'db.php';

$orderId = $_GET['id'] ?? null;

if ($orderId) {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = :id");
    $stmt->execute([':id' => $orderId]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);

        header("Location: report.php?success=Order deleted successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: report.php?error=Error deleting order: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: report.php?error=Invalid request");
    header('Location: view_orders.php');
    exit();
}

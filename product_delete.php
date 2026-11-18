<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM OrderItems WHERE ProductID = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            set_flash('error', 'Cannot delete this product: it appears in existing orders. Remove those order items first.');
        } else {
            $pdo->prepare("DELETE FROM Inventory WHERE ProductID = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM Products WHERE ProductID = ?")->execute([$id]);
            set_flash('success', 'Product deleted.');
        }
    } catch (PDOException $e) {
        set_flash('error', 'Could not delete product: ' . $e->getMessage());
    }
}

header('Location: products.php');
exit;

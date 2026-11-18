<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM Payments WHERE OrderID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM OrderItems WHERE OrderID = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM Orders WHERE OrderID = ?")->execute([$id]);
        $pdo->commit();
        set_flash('success', 'Order deleted.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_flash('error', 'Could not delete order: ' . $e->getMessage());
    }
}

header('Location: orders.php');
exit;

<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        $pdo->beginTransaction();

        // Remove dependent records first (payments -> order items -> orders) then the customer
        $stmt = $pdo->prepare("SELECT OrderID FROM Orders WHERE CustomerID = ?");
        $stmt->execute([$id]);
        $orderIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($orderIds) {
            $in = implode(',', array_fill(0, count($orderIds), '?'));
            $pdo->prepare("DELETE FROM Payments WHERE OrderID IN ($in)")->execute($orderIds);
            $pdo->prepare("DELETE FROM OrderItems WHERE OrderID IN ($in)")->execute($orderIds);
            $pdo->prepare("DELETE FROM Orders WHERE OrderID IN ($in)")->execute($orderIds);
        }

        $pdo->prepare("DELETE FROM Customers WHERE CustomerID = ?")->execute([$id]);

        $pdo->commit();
        set_flash('success', 'Customer and related orders deleted.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        set_flash('error', 'Could not delete customer: ' . $e->getMessage());
    }
}

header('Location: customers.php');
exit;

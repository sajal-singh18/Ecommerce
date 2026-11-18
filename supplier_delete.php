<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Products WHERE SupplierID = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        set_flash('error', 'Cannot delete: products are still linked to this supplier.');
    } else {
        $pdo->prepare("DELETE FROM Suppliers WHERE SupplierID = ?")->execute([$id]);
        set_flash('success', 'Supplier deleted.');
    }
}

header('Location: suppliers.php');
exit;

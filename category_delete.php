<?php
require 'config.php';

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Products WHERE CategoryID = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        set_flash('error', 'Cannot delete: products are still assigned to this category.');
    } else {
        $pdo->prepare("DELETE FROM Categories WHERE CategoryID = ?")->execute([$id]);
        set_flash('success', 'Category deleted.');
    }
}

header('Location: categories.php');
exit;

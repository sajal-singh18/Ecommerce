<?php
// $active is set by each page to highlight the current nav link
$active = $active ?? '';
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? h($pageTitle) . ' · ' : '' ?>EcommerceDB Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark app-navbar sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-shop"></i> EcommerceDB Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?= $active==='dashboard'?'active':'' ?>" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='customers'?'active':'' ?>" href="customers.php"><i class="bi bi-people"></i> Customers</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='products'?'active':'' ?>" href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='categories'?'active':'' ?>" href="categories.php"><i class="bi bi-tags"></i> Categories</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='suppliers'?'active':'' ?>" href="suppliers.php"><i class="bi bi-truck"></i> Suppliers</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='orders'?'active':'' ?>" href="orders.php"><i class="bi bi-cart-check"></i> Orders</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='payments'?'active':'' ?>" href="payments.php"><i class="bi bi-credit-card"></i> Payments</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='inventory'?'active':'' ?>" href="inventory.php"><i class="bi bi-boxes"></i> Inventory</a></li>
        <li class="nav-item"><a class="nav-link <?= $active==='reports'?'active':'' ?>" href="reports.php"><i class="bi bi-graph-up"></i> Reports</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid py-4 px-4">
  <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type']==='error' ? 'danger' : h($flash['type']) ?> alert-dismissible fade show" role="alert">
      <?= h($flash['message']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

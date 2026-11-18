<?php
require 'config.php';
$pageTitle = 'Dashboard';
$active = 'dashboard';

$totalCustomers = $pdo->query("SELECT COUNT(*) FROM Customers")->fetchColumn();
$totalProducts  = $pdo->query("SELECT COUNT(*) FROM Products")->fetchColumn();
$totalOrders    = $pdo->query("SELECT COUNT(*) FROM Orders")->fetchColumn();

$totalRevenue   = $pdo->query("
    SELECT COALESCE(SUM(oi.Quantity * oi.UnitPrice),0)
    FROM OrderItems oi
    JOIN Orders o ON oi.OrderID = o.OrderID
    WHERE o.Status = 'Delivered'
")->fetchColumn();
$lowStockCount  = $pdo->query("SELECT COUNT(*) FROM Products WHERE Stock < 20")->fetchColumn();

$ordersByStatus = $pdo->query("SELECT Status, COUNT(*) AS cnt FROM Orders GROUP BY Status")->fetchAll();

$recentOrders = $pdo->query("
    SELECT o.OrderID, CONCAT(c.FirstName,' ',c.LastName) AS CustomerName, o.OrderDate, o.Status,
           COALESCE(SUM(oi.Quantity * oi.UnitPrice),0) AS OrderTotal
    FROM Orders o
    JOIN Customers c ON o.CustomerID = c.CustomerID
    LEFT JOIN OrderItems oi ON oi.OrderID = o.OrderID
    GROUP BY o.OrderID, c.FirstName, c.LastName, o.OrderDate, o.Status
    ORDER BY o.OrderDate DESC
    LIMIT 8
")->fetchAll();

$lowStockProducts = $pdo->query("
    SELECT ProductID, ProductName, Stock FROM Products WHERE Stock < 20 ORDER BY Stock ASC LIMIT 6
")->fetchAll();

require 'includes/header.php';
?>

<h3 class="page-title mb-4">Dashboard</h3>

<div class="row g-3 mb-4">
  <div class="col-md-3 col-sm-6">
    <div class="card card-stat bg-grad-1 p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-value"><?= number_format($totalCustomers) ?></div>
          <div>Customers</div>
        </div>
        <i class="bi bi-people fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card card-stat bg-grad-2 p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-value"><?= number_format($totalProducts) ?></div>
          <div>Products</div>
        </div>
        <i class="bi bi-box-seam fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card card-stat bg-grad-3 p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-value"><?= number_format($totalOrders) ?></div>
          <div>Orders</div>
        </div>
        <i class="bi bi-cart-check fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="card card-stat bg-grad-4 p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="stat-value"><?= money($totalRevenue) ?></div>
          <div>Revenue (Delivered)</div>
        </div>
        <i class="bi bi-cash-coin fs-1 opacity-75"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="table-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Recent Orders</h5>
        <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>#</th><th>Customer</th><th>Date</th><th>Status</th><th class="text-end">Amount</th></tr>
          </thead>
          <tbody>
          <?php foreach ($recentOrders as $o): ?>
            <tr>
              <td><a href="order_view.php?id=<?= (int)$o['OrderID'] ?>">#<?= (int)$o['OrderID'] ?></a></td>
              <td><?= h($o['CustomerName']) ?></td>
              <td><?= h(date('d M Y', strtotime($o['OrderDate']))) ?></td>
              <td><span class="badge badge-status-<?= h($o['Status']) ?>"><?= h($o['Status']) ?></span></td>
              <td class="text-end"><?= money($o['OrderTotal']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="table-card mb-3">
      <h5 class="mb-3">Orders by Status</h5>
      <?php foreach ($ordersByStatus as $row):
          $pct = $totalOrders > 0 ? round($row['cnt'] / $totalOrders * 100) : 0; ?>
        <div class="mb-2">
          <div class="d-flex justify-content-between small">
            <span><?= h($row['Status']) ?></span>
            <span><?= (int)$row['cnt'] ?> (<?= $pct ?>%)</span>
          </div>
          <div class="progress" style="height:8px;">
            <div class="progress-bar badge-status-<?= h($row['Status']) ?>" style="width: <?= $pct ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="table-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-danger"></i> Low Stock (<?= (int)$lowStockCount ?>)</h5>
        <a href="products.php?stock=low" class="btn btn-sm btn-outline-danger">View All</a>
      </div>
      <ul class="list-group list-group-flush">
        <?php foreach ($lowStockProducts as $p): ?>
          <li class="list-group-item d-flex justify-content-between">
            <span><?= h($p['ProductName']) ?></span>
            <span class="low-stock"><?= (int)$p['Stock'] ?> left</span>
          </li>
        <?php endforeach; ?>
        <?php if (empty($lowStockProducts)): ?>
          <li class="list-group-item text-muted">No low stock products 🎉</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>

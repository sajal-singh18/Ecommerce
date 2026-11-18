<?php
require 'config.php';
$pageTitle = 'Reports';
$active = 'reports';

// Total revenue (Delivered orders only) and total order count
$totalRevenue = $pdo->query("
    SELECT COALESCE(SUM(oi.Quantity * oi.UnitPrice),0)
    FROM OrderItems oi
    JOIN Orders o ON oi.OrderID = o.OrderID
    WHERE o.Status = 'Delivered'
")->fetchColumn();

$totalOrders = $pdo->query("SELECT COUNT(*) FROM Orders")->fetchColumn();

// Revenue by month (Delivered orders)
$monthlyRevenue = $pdo->query("
    SELECT DATE_FORMAT(o.OrderDate, '%Y-%m') AS YearMonth,
           MONTHNAME(o.OrderDate) AS MonthName,
           SUM(oi.Quantity * oi.UnitPrice) AS Revenue
    FROM Orders o
    JOIN OrderItems oi ON oi.OrderID = o.OrderID
    WHERE o.Status = 'Delivered'
    GROUP BY YearMonth, MonthName
    ORDER BY YearMonth
")->fetchAll();

// Top 5 customers by total spend
$topCustomers = $pdo->query("
    SELECT c.CustomerID, CONCAT(c.FirstName,' ',c.LastName) AS Customer,
           SUM(oi.Quantity * oi.UnitPrice) AS TotalSpent,
           COUNT(DISTINCT o.OrderID) AS Orders
    FROM Customers c
    JOIN Orders o ON c.CustomerID = o.CustomerID
    JOIN OrderItems oi ON oi.OrderID = o.OrderID
    GROUP BY c.CustomerID, c.FirstName, c.LastName
    ORDER BY TotalSpent DESC
    LIMIT 5
")->fetchAll();

// Top selling products by revenue
$topProducts = $pdo->query("
    SELECT p.ProductName, SUM(oi.Quantity) AS UnitsSold, SUM(oi.Quantity * oi.UnitPrice) AS Revenue
    FROM Products p
    JOIN OrderItems oi ON p.ProductID = oi.ProductID
    GROUP BY p.ProductName
    ORDER BY Revenue DESC
    LIMIT 10
")->fetchAll();

// Orders by status breakdown
$statusBreakdown = $pdo->query("
    SELECT Status, COUNT(*) AS cnt, SUM(TotalAmount) AS RawTotal
    FROM Orders GROUP BY Status
")->fetchAll();

require 'includes/header.php';

$maxRevenue = 0;
foreach ($monthlyRevenue as $m) $maxRevenue = max($maxRevenue, (float)$m['Revenue']);
$maxProductRevenue = 0;
foreach ($topProducts as $p) $maxProductRevenue = max($maxProductRevenue, (float)$p['Revenue']);
?>

<h3 class="page-title mb-4">Reports</h3>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card card-stat bg-grad-3 p-3">
      <div class="stat-value"><?= money($totalRevenue) ?></div>
      <div>Total Revenue (Delivered Orders)</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-stat bg-grad-1 p-3">
      <div class="stat-value"><?= number_format($totalOrders) ?></div>
      <div>Total Orders Placed</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card card-stat bg-grad-2 p-3">
      <div class="stat-value"><?= count($topCustomers) > 0 ? money($topCustomers[0]['TotalSpent']) : money(0) ?></div>
      <div>Top Customer Spend</div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="table-card mb-3">
      <h5 class="mb-3"><i class="bi bi-bar-chart"></i> Revenue by Month (Delivered Orders)</h5>
      <?php if (empty($monthlyRevenue)): ?>
        <p class="text-muted">No delivered orders yet.</p>
      <?php endif; ?>
      <?php foreach ($monthlyRevenue as $m):
          $pct = $maxRevenue > 0 ? round($m['Revenue'] / $maxRevenue * 100) : 0; ?>
        <div class="mb-2">
          <div class="d-flex justify-content-between small">
            <span><?= h($m['MonthName']) ?></span>
            <span><?= money($m['Revenue']) ?></span>
          </div>
          <div class="progress" style="height:10px;">
            <div class="progress-bar bg-grad-3" style="width: <?= $pct ?>%; background: linear-gradient(135deg,#16a34a,#15803d);"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="table-card">
      <h5 class="mb-3"><i class="bi bi-list-check"></i> Orders by Status</h5>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead class="table-light"><tr><th>Status</th><th class="text-end">Order Count</th></tr></thead>
          <tbody>
          <?php foreach ($statusBreakdown as $s): ?>
            <tr>
              <td><span class="badge badge-status-<?= h($s['Status']) ?>"><?= h($s['Status']) ?></span></td>
              <td class="text-end"><?= (int)$s['cnt'] ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="table-card mb-3">
      <h5 class="mb-3"><i class="bi bi-trophy"></i> Top 5 Customers</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light"><tr><th>Customer</th><th class="text-end">Orders</th><th class="text-end">Total Spent</th></tr></thead>
          <tbody>
          <?php foreach ($topCustomers as $c): ?>
            <tr>
              <td><a href="customer_form.php?id=<?= (int)$c['CustomerID'] ?>"><?= h($c['Customer']) ?></a></td>
              <td class="text-end"><?= (int)$c['Orders'] ?></td>
              <td class="text-end"><?= money($c['TotalSpent']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($topCustomers)): ?>
            <tr><td colspan="3" class="text-center text-muted py-3">No data.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="table-card">
      <h5 class="mb-3"><i class="bi bi-star"></i> Top Selling Products</h5>
      <?php foreach ($topProducts as $p):
          $pct = $maxProductRevenue > 0 ? round($p['Revenue'] / $maxProductRevenue * 100) : 0; ?>
        <div class="mb-2">
          <div class="d-flex justify-content-between small">
            <span><?= h($p['ProductName']) ?> <span class="text-muted">(<?= (int)$p['UnitsSold'] ?> sold)</span></span>
            <span><?= money($p['Revenue']) ?></span>
          </div>
          <div class="progress" style="height:8px;">
            <div class="progress-bar" style="width: <?= $pct ?>%; background: linear-gradient(135deg,#4f46e5,#7c3aed);"></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($topProducts)): ?>
        <p class="text-muted mb-0">No sales data.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>

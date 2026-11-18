<?php
require 'config.php';
$pageTitle = 'Orders';
$active = 'orders';

$status = trim($_GET['status'] ?? '');
$search = trim($_GET['q'] ?? '');
$validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

$sql = "
    SELECT o.OrderID, o.OrderDate, o.Status,
           c.CustomerID, CONCAT(c.FirstName,' ',c.LastName) AS CustomerName,
           COALESCE(SUM(oi.Quantity * oi.UnitPrice),0) AS OrderTotal
    FROM Orders o
    JOIN Customers c ON o.CustomerID = c.CustomerID
    LEFT JOIN OrderItems oi ON oi.OrderID = o.OrderID
    WHERE 1=1
";
$params = [];

if ($status !== '' && in_array($status, $validStatuses, true)) {
    $sql .= " AND o.Status = :status";
    $params['status'] = $status;
}
if ($search !== '') {
    $sql .= " AND (c.FirstName LIKE :q1 OR c.LastName LIKE :q2 OR o.OrderID = :qid)";
    $params['q1'] = "%$search%";
    $params['q2'] = "%$search%";
    $params['qid'] = is_numeric($search) ? (int)$search : -1;
}
$sql .= " GROUP BY o.OrderID, o.OrderDate, o.Status, c.CustomerID, c.FirstName, c.LastName";
$sql .= " ORDER BY o.OrderDate DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

require 'includes/header.php';
?>

<h3 class="page-title mb-4">Orders</h3>

<div class="table-card">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <input type="text" name="q" class="form-control" placeholder="Search by order ID or customer name..." value="<?= h($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">All Statuses</option>
        <?php foreach ($validStatuses as $s): ?>
          <option value="<?= h($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= h($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
      <a href="orders.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= (int)$o['OrderID'] ?></td>
          <td><a href="customer_form.php?id=<?= (int)$o['CustomerID'] ?>"><?= h($o['CustomerName']) ?></a></td>
          <td><?= h(date('d M Y, H:i', strtotime($o['OrderDate']))) ?></td>
          <td><span class="badge badge-status-<?= h($o['Status']) ?>"><?= h($o['Status']) ?></span></td>
          <td class="text-end"><?= money($o['OrderTotal']) ?></td>
          <td class="text-end">
            <a href="order_view.php?id=<?= (int)$o['OrderID'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> View</a>
            <a href="order_delete.php?id=<?= (int)$o['OrderID'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Delete this order and its items/payment?');"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($orders)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No orders found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="text-muted small"><?= count($orders) ?> order(s) found.</div>
</div>

<?php require 'includes/footer.php'; ?>

<?php
require 'config.php';
$pageTitle = 'Payments';
$active = 'payments';

$status = trim($_GET['status'] ?? '');
$validPayStatuses = ['Pending', 'Completed', 'Failed', 'Refunded'];

$sql = "
    SELECT p.*, o.OrderID, CONCAT(c.FirstName,' ',c.LastName) AS CustomerName
    FROM Payments p
    JOIN Orders o ON p.OrderID = o.OrderID
    JOIN Customers c ON o.CustomerID = c.CustomerID
    WHERE 1=1
";
$params = [];
if ($status !== '' && in_array($status, $validPayStatuses, true)) {
    $sql .= " AND p.PaymentStatus = :status";
    $params['status'] = $status;
}
$sql .= " ORDER BY p.PaymentDate DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$payments = $stmt->fetchAll();

require 'includes/header.php';
?>

<h3 class="page-title mb-4">Payments</h3>

<div class="table-card">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">All Statuses</option>
        <?php foreach ($validPayStatuses as $s): ?>
          <option value="<?= h($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= h($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
      <a href="payments.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr><th>Payment</th><th>Order</th><th>Customer</th><th>Method</th><th>Date</th><th class="text-end">Amount</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php foreach ($payments as $p): ?>
        <tr>
          <td>#<?= (int)$p['PaymentID'] ?></td>
          <td><a href="order_view.php?id=<?= (int)$p['OrderID'] ?>">#<?= (int)$p['OrderID'] ?></a></td>
          <td><?= h($p['CustomerName']) ?></td>
          <td><?= h($p['PaymentMethod']) ?></td>
          <td><?= h(date('d M Y, H:i', strtotime($p['PaymentDate']))) ?></td>
          <td class="text-end"><?= money($p['Amount']) ?></td>
          <td><span class="badge badge-pay-<?= h($p['PaymentStatus']) ?>"><?= h($p['PaymentStatus']) ?></span></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($payments)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No payments found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="text-muted small"><?= count($payments) ?> payment(s) found.</div>
</div>

<?php require 'includes/footer.php'; ?>

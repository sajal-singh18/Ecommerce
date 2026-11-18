<?php
require 'config.php';
$pageTitle = 'Order Detail';
$active = 'orders';
$validStatuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
$validPayStatuses = ['Pending', 'Completed', 'Failed', 'Refunded'];

$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT o.*, c.FirstName, c.LastName, c.Email, c.Phone, c.Address, c.City, c.State, c.Country
    FROM Orders o
    JOIN Customers c ON o.CustomerID = c.CustomerID
    WHERE o.OrderID = ?
");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('error', 'Order not found.');
    header('Location: orders.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $newStatus = $_POST['Status'] ?? '';
        if (in_array($newStatus, $validStatuses, true)) {
            $pdo->prepare("UPDATE Orders SET Status=? WHERE OrderID=?")->execute([$newStatus, $id]);
            set_flash('success', 'Order status updated to ' . $newStatus . '.');
            header("Location: order_view.php?id=$id");
            exit;
        } else {
            $errors[] = 'Invalid status selected.';
        }
    } elseif (isset($_POST['update_payment'])) {
        $payStatus = $_POST['PaymentStatus'] ?? '';
        $payId = (int)($_POST['PaymentID'] ?? 0);
        if (in_array($payStatus, $validPayStatuses, true) && $payId > 0) {
            $pdo->prepare("UPDATE Payments SET PaymentStatus=? WHERE PaymentID=?")->execute([$payStatus, $payId]);
            set_flash('success', 'Payment status updated to ' . $payStatus . '.');
            header("Location: order_view.php?id=$id");
            exit;
        } else {
            $errors[] = 'Invalid payment status.';
        }
    }
}

$items = $pdo->prepare("
    SELECT oi.*, p.ProductName, (oi.Quantity * oi.UnitPrice) AS LineTotal
    FROM OrderItems oi
    JOIN Products p ON oi.ProductID = p.ProductID
    WHERE oi.OrderID = ?
    ORDER BY oi.OrderItemID
");
$items->execute([$id]);
$items = $items->fetchAll();

$orderTotal = array_sum(array_column($items, 'LineTotal'));

$payment = $pdo->prepare("SELECT * FROM Payments WHERE OrderID = ? ORDER BY PaymentID DESC LIMIT 1");
$payment->execute([$id]);
$payment = $payment->fetch();

require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="page-title mb-0">Order #<?= (int)$order['OrderID'] ?></h3>
  <a href="orders.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Orders</a>
</div>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><?php foreach ($errors as $e) echo h($e) . '<br>'; ?></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="table-card mb-3">
      <h5 class="mb-3"><i class="bi bi-person"></i> Customer</h5>
      <p class="mb-1"><strong><?= h($order['FirstName'] . ' ' . $order['LastName']) ?></strong></p>
      <p class="mb-1 text-muted"><?= h($order['Email']) ?></p>
      <p class="mb-1 text-muted"><?= h($order['Phone']) ?></p>
      <p class="mb-0 text-muted"><?= h($order['Address']) ?>, <?= h($order['City']) ?>, <?= h($order['State']) ?>, <?= h($order['Country']) ?></p>
    </div>

    <div class="table-card mb-3">
      <h5 class="mb-3"><i class="bi bi-info-circle"></i> Order Info</h5>
      <p class="mb-1"><strong>Date:</strong> <?= h(date('d M Y, H:i', strtotime($order['OrderDate']))) ?></p>
      <p class="mb-1"><strong>Status:</strong> <span class="badge badge-status-<?= h($order['Status']) ?>"><?= h($order['Status']) ?></span></p>
      <p class="mb-0"><strong>Total:</strong> <?= money($orderTotal) ?></p>

      <form method="post" class="mt-3 d-flex gap-2">
        <select name="Status" class="form-select form-select-sm">
          <?php foreach ($validStatuses as $s): ?>
            <option value="<?= h($s) ?>" <?= $order['Status'] === $s ? 'selected' : '' ?>><?= h($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="update_status" value="1" class="btn btn-sm btn-primary">Update</button>
      </form>
    </div>

    <?php if ($payment): ?>
    <div class="table-card">
      <h5 class="mb-3"><i class="bi bi-credit-card"></i> Payment</h5>
      <p class="mb-1"><strong>Method:</strong> <?= h($payment['PaymentMethod']) ?></p>
      <p class="mb-1"><strong>Amount:</strong> <?= money($payment['Amount']) ?></p>
      <p class="mb-1"><strong>Date:</strong> <?= h(date('d M Y, H:i', strtotime($payment['PaymentDate']))) ?></p>
      <p class="mb-1"><strong>Status:</strong> <span class="badge badge-pay-<?= h($payment['PaymentStatus']) ?>"><?= h($payment['PaymentStatus']) ?></span></p>

      <form method="post" class="mt-3 d-flex gap-2">
        <input type="hidden" name="PaymentID" value="<?= (int)$payment['PaymentID'] ?>">
        <select name="PaymentStatus" class="form-select form-select-sm">
          <?php foreach ($validPayStatuses as $s): ?>
            <option value="<?= h($s) ?>" <?= $payment['PaymentStatus'] === $s ? 'selected' : '' ?>><?= h($s) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="update_payment" value="1" class="btn btn-sm btn-primary">Update</button>
      </form>
    </div>
    <?php else: ?>
    <div class="table-card">
      <h5 class="mb-3"><i class="bi bi-credit-card"></i> Payment</h5>
      <p class="text-muted mb-0">No payment recorded for this order.</p>
    </div>
    <?php endif; ?>
  </div>

  <div class="col-lg-8">
    <div class="table-card">
      <h5 class="mb-3"><i class="bi bi-bag"></i> Order Items</h5>
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>Product</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Line Total</th></tr>
          </thead>
          <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= h($it['ProductName']) ?></td>
              <td class="text-end"><?= (int)$it['Quantity'] ?></td>
              <td class="text-end"><?= money($it['UnitPrice']) ?></td>
              <td class="text-end"><?= money($it['LineTotal']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($items)): ?>
            <tr><td colspan="4" class="text-center text-muted py-4">No items in this order.</td></tr>
          <?php endif; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">Order Total</th>
              <th class="text-end"><?= money($orderTotal) ?></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>

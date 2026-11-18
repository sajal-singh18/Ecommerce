<?php
require 'config.php';
$pageTitle = 'Inventory';
$active = 'inventory';

$warehouse = trim($_GET['warehouse'] ?? '');

$sql = "
    SELECT i.*, p.ProductName, p.Price
    FROM Inventory i
    JOIN Products p ON i.ProductID = p.ProductID
    WHERE 1=1
";
$params = [];
if ($warehouse !== '') {
    $sql .= " AND i.Warehouse = :w";
    $params['w'] = $warehouse;
}
$sql .= " ORDER BY i.AvailableStock ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inventory = $stmt->fetchAll();

$warehouses = $pdo->query("SELECT DISTINCT Warehouse FROM Inventory ORDER BY Warehouse")->fetchAll(PDO::FETCH_COLUMN);

require 'includes/header.php';
?>

<h3 class="page-title mb-4">Inventory</h3>

<div class="table-card">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <select name="warehouse" class="form-select">
        <option value="">All Warehouses</option>
        <?php foreach ($warehouses as $w): ?>
          <option value="<?= h($w) ?>" <?= $warehouse === $w ? 'selected' : '' ?>><?= h($w) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filter</button>
      <a href="inventory.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr><th>Product</th><th>Warehouse</th><th class="text-end">Available Stock</th><th>Last Updated</th></tr>
      </thead>
      <tbody>
      <?php foreach ($inventory as $i): ?>
        <tr>
          <td><?= h($i['ProductName']) ?></td>
          <td><?= h($i['Warehouse']) ?></td>
          <td class="text-end <?= $i['AvailableStock'] < 20 ? 'low-stock' : '' ?>"><?= (int)$i['AvailableStock'] ?></td>
          <td><?= h(date('d M Y, H:i', strtotime($i['LastUpdated']))) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($inventory)): ?>
        <tr><td colspan="4" class="text-center text-muted py-4">No inventory records found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'includes/footer.php'; ?>

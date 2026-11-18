<?php
require 'config.php';
$pageTitle = 'Suppliers';
$active = 'suppliers';

$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT s.*, COUNT(p.ProductID) AS ProductCount
        FROM Suppliers s
        LEFT JOIN Products p ON p.SupplierID = s.SupplierID
        WHERE s.SupplierName LIKE :q1 OR s.City LIKE :q2 OR s.Email LIKE :q3
        GROUP BY s.SupplierID
        ORDER BY s.SupplierID DESC
    ");
    $like = "%$search%";
    $stmt->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
} else {
    $stmt = $pdo->query("
        SELECT s.*, COUNT(p.ProductID) AS ProductCount
        FROM Suppliers s
        LEFT JOIN Products p ON p.SupplierID = s.SupplierID
        GROUP BY s.SupplierID
        ORDER BY s.SupplierID DESC
    ");
}
$suppliers = $stmt->fetchAll();

require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="page-title mb-0">Suppliers</h3>
  <a href="supplier_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Supplier</a>
</div>

<div class="table-card">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-6">
      <input type="text" name="q" class="form-control" placeholder="Search by name, city, or email..." value="<?= h($search) ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
      <?php if ($search !== ''): ?><a href="suppliers.php" class="btn btn-outline-secondary">Clear</a><?php endif; ?>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr><th>ID</th><th>Name</th><th>Contact</th><th>Email</th><th>Phone</th><th>City</th><th class="text-end">Products</th><th class="text-end">Actions</th></tr>
      </thead>
      <tbody>
      <?php foreach ($suppliers as $s): ?>
        <tr>
          <td>#<?= (int)$s['SupplierID'] ?></td>
          <td><?= h($s['SupplierName']) ?></td>
          <td><?= h($s['ContactPerson']) ?></td>
          <td><?= h($s['Email']) ?></td>
          <td><?= h($s['Phone']) ?></td>
          <td><?= h($s['City']) ?></td>
          <td class="text-end"><span class="badge bg-secondary"><?= (int)$s['ProductCount'] ?></span></td>
          <td class="text-end">
            <a href="supplier_form.php?id=<?= (int)$s['SupplierID'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
            <a href="supplier_delete.php?id=<?= (int)$s['SupplierID'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Delete this supplier?');"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($suppliers)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No suppliers found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'includes/footer.php'; ?>

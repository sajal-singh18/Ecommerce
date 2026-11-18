<?php
require 'config.php';
$pageTitle = 'Customers';
$active = 'customers';

$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare("
        SELECT * FROM Customers
        WHERE FirstName LIKE ? OR LastName LIKE ? OR Email LIKE ? OR City LIKE ? OR Phone LIKE ?
        ORDER BY CustomerID DESC
    ");
    $like = "%$search%";
    $stmt->execute([$like, $like, $like, $like, $like]);
} else {
    $stmt = $pdo->query("SELECT * FROM Customers ORDER BY CustomerID DESC");
}
$customers = $stmt->fetchAll();

require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="page-title mb-0">Customers</h3>
  <a href="customer_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Customer</a>
</div>

<div class="table-card">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-6">
      <input type="text" name="q" class="form-control" placeholder="Search by name, email, city, or phone..." value="<?= h($search) ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Search</button>
      <?php if ($search !== ''): ?>
        <a href="customers.php" class="btn btn-outline-secondary">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>City</th><th>State</th><th>Country</th><th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($customers as $c): ?>
        <tr>
          <td>#<?= (int)$c['CustomerID'] ?></td>
          <td><?= h($c['FirstName'] . ' ' . $c['LastName']) ?></td>
          <td><?= h($c['Email']) ?></td>
          <td><?= h($c['Phone']) ?></td>
          <td><?= h($c['City']) ?></td>
          <td><?= h($c['State']) ?></td>
          <td><?= h($c['Country']) ?></td>
          <td class="text-end">
            <a href="customer_form.php?id=<?= (int)$c['CustomerID'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
            <a href="customer_delete.php?id=<?= (int)$c['CustomerID'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Delete this customer? This will also delete their orders.');"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($customers)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No customers found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="text-muted small"><?= count($customers) ?> customer(s) found.</div>
</div>

<?php require 'includes/footer.php'; ?>

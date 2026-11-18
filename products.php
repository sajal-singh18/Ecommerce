<?php
require 'config.php';
$pageTitle = 'Products';
$active = 'products';

$search   = trim($_GET['q'] ?? '');
$catId    = (int)($_GET['category'] ?? 0);
$lowStock = ($_GET['stock'] ?? '') === 'low';

$sql = "
    SELECT p.*, c.CategoryName, s.SupplierName
    FROM Products p
    JOIN Categories c ON p.CategoryID = c.CategoryID
    JOIN Suppliers s ON p.SupplierID = s.SupplierID
    WHERE 1=1
";
$params = [];

if ($search !== '') {
    $sql .= " AND p.ProductName LIKE :q";
    $params['q'] = "%$search%";
}
if ($catId > 0) {
    $sql .= " AND p.CategoryID = :cat";
    $params['cat'] = $catId;
}
if ($lowStock) {
    $sql .= " AND p.Stock < 20";
}
$sql .= " ORDER BY p.ProductID DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM Categories ORDER BY CategoryName")->fetchAll();

require 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h3 class="page-title mb-0">Products</h3>
  <a href="product_form.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Product</a>
</div>

<div class="table-card">
  <form class="row g-2 mb-3" method="get">
    <div class="col-md-4">
      <input type="text" name="q" class="form-control" placeholder="Search product name..." value="<?= h($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="category" class="form-select">
        <option value="0">All Categories</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= (int)$c['CategoryID'] ?>" <?= $catId === (int)$c['CategoryID'] ? 'selected' : '' ?>>
            <?= h($c['CategoryName']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" name="stock" value="low" id="lowStockChk" <?= $lowStock ? 'checked' : '' ?>>
        <label class="form-check-label" for="lowStockChk">Low stock only (&lt; 20)</label>
      </div>
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary"><i class="bi bi-search"></i> Filter</button>
      <a href="products.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Name</th><th>Category</th><th>Supplier</th><th class="text-end">Price</th><th class="text-end">Stock</th><th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($products as $p): ?>
        <tr>
          <td>#<?= (int)$p['ProductID'] ?></td>
          <td><?= h($p['ProductName']) ?><div class="text-muted small"><?= h($p['Description']) ?></div></td>
          <td><span class="badge bg-secondary"><?= h($p['CategoryName']) ?></span></td>
          <td><?= h($p['SupplierName']) ?></td>
          <td class="text-end"><?= money($p['Price']) ?></td>
          <td class="text-end <?= $p['Stock'] < 20 ? 'low-stock' : '' ?>"><?= (int)$p['Stock'] ?></td>
          <td class="text-end">
            <a href="product_form.php?id=<?= (int)$p['ProductID'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
            <a href="product_delete.php?id=<?= (int)$p['ProductID'] ?>" class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Delete this product?');"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($products)): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No products found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="text-muted small"><?= count($products) ?> product(s) found.</div>
</div>

<?php require 'includes/footer.php'; ?>

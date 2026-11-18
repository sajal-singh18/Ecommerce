<?php
require 'config.php';
$pageTitle = 'Product Form';
$active = 'products';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEdit = $id !== null;

$product = [
    'ProductName' => '', 'CategoryID' => '', 'SupplierID' => '',
    'Price' => '', 'Stock' => '', 'Description' => ''
];
$errors = [];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE ProductID = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Product not found.');
        header('Location: products.php');
        exit;
    }
    $product = $found;
}

$categories = $pdo->query("SELECT * FROM Categories ORDER BY CategoryName")->fetchAll();
$suppliers  = $pdo->query("SELECT * FROM Suppliers ORDER BY SupplierName")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product['ProductName'] = trim($_POST['ProductName'] ?? '');
    $product['CategoryID']  = (int)($_POST['CategoryID'] ?? 0);
    $product['SupplierID']  = (int)($_POST['SupplierID'] ?? 0);
    $product['Price']       = trim($_POST['Price'] ?? '');
    $product['Stock']       = trim($_POST['Stock'] ?? '');
    $product['Description'] = trim($_POST['Description'] ?? '');

    if ($product['ProductName'] === '') $errors[] = 'Product name is required.';
    if ($product['CategoryID'] <= 0) $errors[] = 'Please select a category.';
    if ($product['SupplierID'] <= 0) $errors[] = 'Please select a supplier.';
    if (!is_numeric($product['Price']) || $product['Price'] < 0) $errors[] = 'Price must be a non-negative number.';
    if (!is_numeric($product['Stock']) || $product['Stock'] < 0) $errors[] = 'Stock must be a non-negative integer.';

    if (empty($errors)) {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare("
                    UPDATE Products SET ProductName=?, CategoryID=?, SupplierID=?, Price=?, Stock=?, Description=?
                    WHERE ProductID=?
                ");
                $stmt->execute([
                    $product['ProductName'], $product['CategoryID'], $product['SupplierID'],
                    $product['Price'], $product['Stock'], $product['Description'], $id
                ]);
                set_flash('success', 'Product updated successfully.');
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO Products (ProductName, CategoryID, SupplierID, Price, Stock, Description)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $product['ProductName'], $product['CategoryID'], $product['SupplierID'],
                    $product['Price'], $product['Stock'], $product['Description']
                ]);
                set_flash('success', 'Product added successfully.');
            }
            header('Location: products.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
    }
}

require 'includes/header.php';
?>

<h3 class="page-title mb-4"><?= $isEdit ? 'Edit Product #' . (int)$id : 'Add New Product' ?></h3>

<div class="table-card" style="max-width:720px;">
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $err): ?><li><?= h($err) ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Product Name *</label>
        <input type="text" name="ProductName" class="form-control" value="<?= h($product['ProductName']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Category *</label>
        <select name="CategoryID" class="form-select" required>
          <option value="">-- Select Category --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= (int)$c['CategoryID'] ?>" <?= (int)$product['CategoryID'] === (int)$c['CategoryID'] ? 'selected' : '' ?>>
              <?= h($c['CategoryName']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Supplier *</label>
        <select name="SupplierID" class="form-select" required>
          <option value="">-- Select Supplier --</option>
          <?php foreach ($suppliers as $s): ?>
            <option value="<?= (int)$s['SupplierID'] ?>" <?= (int)$product['SupplierID'] === (int)$s['SupplierID'] ? 'selected' : '' ?>>
              <?= h($s['SupplierName']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Price (₹) *</label>
        <input type="number" step="0.01" min="0" name="Price" class="form-control" value="<?= h($product['Price']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Stock *</label>
        <input type="number" min="0" name="Stock" class="form-control" value="<?= h($product['Stock']) ?>" required>
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="Description" class="form-control" rows="3"><?= h($product['Description']) ?></textarea>
      </div>
    </div>

    <div class="mt-4 d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update' : 'Save' ?> Product</button>
      <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require 'includes/footer.php'; ?>

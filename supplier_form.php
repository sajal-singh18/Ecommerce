<?php
require 'config.php';
$pageTitle = 'Supplier Form';
$active = 'suppliers';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEdit = $id !== null;

$supplier = ['SupplierName' => '', 'ContactPerson' => '', 'Email' => '', 'Phone' => '', 'City' => '', 'Country' => ''];
$errors = [];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Suppliers WHERE SupplierID = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Supplier not found.');
        header('Location: suppliers.php');
        exit;
    }
    $supplier = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier['SupplierName']  = trim($_POST['SupplierName'] ?? '');
    $supplier['ContactPerson'] = trim($_POST['ContactPerson'] ?? '');
    $supplier['Email']         = trim($_POST['Email'] ?? '');
    $supplier['Phone']         = trim($_POST['Phone'] ?? '');
    $supplier['City']          = trim($_POST['City'] ?? '');
    $supplier['Country']       = trim($_POST['Country'] ?? '');

    if ($supplier['SupplierName'] === '') $errors[] = 'Supplier name is required.';
    if ($supplier['Email'] !== '' && !filter_var($supplier['Email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email must be valid if provided.';
    }

    if (empty($errors)) {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare("
                    UPDATE Suppliers SET SupplierName=?, ContactPerson=?, Email=?, Phone=?, City=?, Country=?
                    WHERE SupplierID=?
                ");
                $stmt->execute([
                    $supplier['SupplierName'], $supplier['ContactPerson'], $supplier['Email'],
                    $supplier['Phone'], $supplier['City'], $supplier['Country'], $id
                ]);
                set_flash('success', 'Supplier updated successfully.');
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO Suppliers (SupplierName, ContactPerson, Email, Phone, City, Country)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $supplier['SupplierName'], $supplier['ContactPerson'], $supplier['Email'],
                    $supplier['Phone'], $supplier['City'], $supplier['Country']
                ]);
                set_flash('success', 'Supplier added successfully.');
            }
            header('Location: suppliers.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = ($e->getCode() == 23000) ? 'That email is already used by another supplier.' : 'Database error: ' . $e->getMessage();
        }
    }
}

require 'includes/header.php';
?>

<h3 class="page-title mb-4"><?= $isEdit ? 'Edit Supplier #' . (int)$id : 'Add New Supplier' ?></h3>

<div class="table-card" style="max-width:720px;">
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= h($err) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Supplier Name *</label>
        <input type="text" name="SupplierName" class="form-control" value="<?= h($supplier['SupplierName']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Contact Person</label>
        <input type="text" name="ContactPerson" class="form-control" value="<?= h($supplier['ContactPerson']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="Email" class="form-control" value="<?= h($supplier['Email']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="Phone" class="form-control" value="<?= h($supplier['Phone']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">City</label>
        <input type="text" name="City" class="form-control" value="<?= h($supplier['City']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Country</label>
        <input type="text" name="Country" class="form-control" value="<?= h($supplier['Country']) ?>">
      </div>
    </div>

    <div class="mt-4 d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update' : 'Save' ?> Supplier</button>
      <a href="suppliers.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require 'includes/footer.php'; ?>

<?php
require 'config.php';
$pageTitle = 'Customer Form';
$active = 'customers';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$isEdit = $id !== null;

$customer = [
    'FirstName' => '', 'LastName' => '', 'Email' => '', 'Phone' => '',
    'Address' => '', 'City' => '', 'State' => '', 'Country' => ''
];
$errors = [];

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Customers WHERE CustomerID = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        set_flash('error', 'Customer not found.');
        header('Location: customers.php');
        exit;
    }
    $customer = $found;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer['FirstName'] = trim($_POST['FirstName'] ?? '');
    $customer['LastName']  = trim($_POST['LastName'] ?? '');
    $customer['Email']     = trim($_POST['Email'] ?? '');
    $customer['Phone']     = trim($_POST['Phone'] ?? '');
    $customer['Address']   = trim($_POST['Address'] ?? '');
    $customer['City']      = trim($_POST['City'] ?? '');
    $customer['State']     = trim($_POST['State'] ?? '');
    $customer['Country']   = trim($_POST['Country'] ?? '');

    if ($customer['FirstName'] === '') $errors[] = 'First name is required.';
    if ($customer['LastName']  === '') $errors[] = 'Last name is required.';
    if ($customer['Email'] === '' || !filter_var($customer['Email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if (empty($errors)) {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare("
                    UPDATE Customers SET FirstName=?, LastName=?, Email=?, Phone=?, Address=?, City=?, State=?, Country=?
                    WHERE CustomerID=?
                ");
                $stmt->execute([
                    $customer['FirstName'], $customer['LastName'], $customer['Email'], $customer['Phone'],
                    $customer['Address'], $customer['City'], $customer['State'], $customer['Country'], $id
                ]);
                set_flash('success', 'Customer updated successfully.');
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO Customers (FirstName, LastName, Email, Phone, Address, City, State, Country)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $customer['FirstName'], $customer['LastName'], $customer['Email'], $customer['Phone'],
                    $customer['Address'], $customer['City'], $customer['State'], $customer['Country']
                ]);
                set_flash('success', 'Customer added successfully.');
            }
            header('Location: customers.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = 'That email or phone number is already in use by another customer.';
            } else {
                $errors[] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

require 'includes/header.php';
?>

<h3 class="page-title mb-4"><?= $isEdit ? 'Edit Customer #' . (int)$id : 'Add New Customer' ?></h3>

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
      <div class="col-md-6">
        <label class="form-label">First Name *</label>
        <input type="text" name="FirstName" class="form-control" value="<?= h($customer['FirstName']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Last Name *</label>
        <input type="text" name="LastName" class="form-control" value="<?= h($customer['LastName']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email *</label>
        <input type="email" name="Email" class="form-control" value="<?= h($customer['Email']) ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="Phone" class="form-control" value="<?= h($customer['Phone']) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Address</label>
        <input type="text" name="Address" class="form-control" value="<?= h($customer['Address']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">City</label>
        <input type="text" name="City" class="form-control" value="<?= h($customer['City']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">State</label>
        <input type="text" name="State" class="form-control" value="<?= h($customer['State']) ?>">
      </div>
      <div class="col-md-4">
        <label class="form-label">Country</label>
        <input type="text" name="Country" class="form-control" value="<?= h($customer['Country']) ?>">
      </div>
    </div>

    <div class="mt-4 d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update' : 'Save' ?> Customer</button>
      <a href="customers.php" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require 'includes/footer.php'; ?>

<?php
require 'config.php';
$pageTitle = 'Categories';
$active = 'categories';

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$editName = '';
$errors = [];

if ($editId) {
    $stmt = $pdo->prepare("SELECT CategoryName FROM Categories WHERE CategoryID = ?");
    $stmt->execute([$editId]);
    $row = $stmt->fetch();
    if ($row) $editName = $row['CategoryName'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['CategoryName'] ?? '');
    $postId = (int)($_POST['CategoryID'] ?? 0);

    if ($name === '') {
        $errors[] = 'Category name is required.';
    } else {
        try {
            if ($postId > 0) {
                $pdo->prepare("UPDATE Categories SET CategoryName=? WHERE CategoryID=?")->execute([$name, $postId]);
                set_flash('success', 'Category updated.');
            } else {
                $pdo->prepare("INSERT INTO Categories (CategoryName) VALUES (?)")->execute([$name]);
                set_flash('success', 'Category added.');
            }
            header('Location: categories.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = ($e->getCode() == 23000) ? 'That category name already exists.' : 'Database error: ' . $e->getMessage();
        }
    }
    $editId = $postId ?: null;
    $editName = $name;
}

$categories = $pdo->query("
    SELECT c.*, COUNT(p.ProductID) AS ProductCount
    FROM Categories c
    LEFT JOIN Products p ON p.CategoryID = c.CategoryID
    GROUP BY c.CategoryID
    ORDER BY c.CategoryName
")->fetchAll();

require 'includes/header.php';
?>

<h3 class="page-title mb-4">Categories</h3>

<div class="row g-3">
  <div class="col-lg-4">
    <div class="table-card">
      <h5 class="mb-3"><?= $editId ? 'Edit Category' : 'Add Category' ?></h5>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php foreach ($errors as $e) echo h($e) . '<br>'; ?></div>
      <?php endif; ?>
      <form method="post">
        <input type="hidden" name="CategoryID" value="<?= (int)($editId ?? 0) ?>">
        <div class="mb-3">
          <label class="form-label">Category Name *</label>
          <input type="text" name="CategoryName" class="form-control" value="<?= h($editName) ?>" required>
        </div>
        <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> <?= $editId ? 'Update' : 'Save' ?></button>
        <?php if ($editId): ?><a href="categories.php" class="btn btn-outline-secondary">Cancel</a><?php endif; ?>
      </form>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="table-card">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>ID</th><th>Name</th><th class="text-end">Products</th><th class="text-end">Actions</th></tr>
          </thead>
          <tbody>
          <?php foreach ($categories as $c): ?>
            <tr>
              <td>#<?= (int)$c['CategoryID'] ?></td>
              <td><?= h($c['CategoryName']) ?></td>
              <td class="text-end"><span class="badge bg-secondary"><?= (int)$c['ProductCount'] ?></span></td>
              <td class="text-end">
                <a href="categories.php?edit=<?= (int)$c['CategoryID'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                <a href="category_delete.php?id=<?= (int)$c['CategoryID'] ?>" class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Delete this category? Products using it must be reassigned first.');"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require 'includes/footer.php'; ?>

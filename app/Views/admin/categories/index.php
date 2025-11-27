<!DOCTYPE html>
<html>

<head>
  <title>Categories</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <h3 class="text-center mb-4">Categories</h3>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="mb-3 d-flex justify-content-between">
          <a href="<?= admin_dashboard() ?>" class="btn btn-secondary">Dashboard</a>
          <a href="<?= admin_category_link(null, 'create') ?>" class="btn btn-primary">Add Category</a>
        </div>

        <table class="table table-bordered table-striped bg-white">
          <thead>
            <tr>
              <th>ID</th>
              <th>English Name</th>
              <th>Slug</th>
              <th>Parent</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $cat): ?>
                <tr>
                  <td><?= $cat['id'] ?></td>
                  <td><?= esc($cat['en_name']) ?></td>
                  <td><?= esc($cat['slug']) ?></td>
                  <td><?= esc($cat['parent_name'] ?: '-') ?></td>
                  <td>
                    <a href="<?= admin_category_link($cat, 'edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="<?= admin_category_link($cat, 'delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    <a href="<?= admin_category_link($cat, 'trans') ?>" class="btn btn-sm btn-success">Translations</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">No categories found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</body>

</html>
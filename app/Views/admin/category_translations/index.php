<!DOCTYPE html>
<html>

<head>
  <title>Translations for category: <?= esc($category['en_name']) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <h3 class="text-center mb-4">Translations for category: "<?= esc($category['en_name']) ?>"</h3>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="mb-3 d-flex justify-content-between">
          <a href="<?= admin_category_link() ?>" class="btn btn-secondary">Categories</a>
          <a href="<?= admin_category_trans_link($category, null, 'create') ?>" class="btn btn-primary">Add Translation</a>
        </div>

        <table class="table table-bordered table-striped bg-white">
          <thead>
            <tr>
              <th>ID</th>
              <th>Language</th>
              <th>Name</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($category_trans) && is_array($category_trans)): ?>
              <?php foreach ($category_trans as $ct): ?>
                <tr>
                  <td><?= $ct['id'] ?></td>
                  <td><?= esc($ct['language_name']) ?></td>
                  <td><?= esc($ct['name']) ?></td>
                  <td>
                    <a href="<?= admin_category_trans_link($category, $ct, 'edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="<?= admin_category_trans_link($category, $ct, 'delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center">No category translations found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>

</body>

</html>
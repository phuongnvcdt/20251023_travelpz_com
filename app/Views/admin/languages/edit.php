<!DOCTYPE html>
<html>

<head>
  <title>Edit Language</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <h3 class="text-center mb-4">Edit Language</h3>

        <div class="mb-3 d-flex justify-content-start">
          <a href="<?= admin_language_link() ?>" class="btn btn-secondary">Languages</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= admin_language_link($language, 'update') ?>">
          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= esc($language['name']) ?>" required>
          </div>

          <div class="mb-3">
            <label for="code" class="form-label">Code</label>
            <input type="text" class="form-control" id="code" name="code" value="<?= esc($language['code']) ?>" required>
          </div>

          <div class="mb-3">
            <label for="locale" class="form-label">Locale</label>
            <input type="text" class="form-control" id="locale" name="locale" value="<?= esc($language['locale']) ?>" required>
          </div>

          <div class="mb-3">
            <label for="a_id" class="form-label">Agoda ID</label>
            <input type="number" class="form-control" id="a_id" name="a_id" value="<?= esc($language['a_id']) ?>">
          </div>

          <div class="mb-3">
            <label for="k_code" class="form-label">Klook Code</label>
            <input type="text" class="form-control" id="k_code" name="k_code" value="<?= esc($language['k_code']) ?>">
          </div>

          <div class="mb-3">
            <label for="kd_code" class="form-label">Kkday Code</label>
            <input type="text" class="form-control" id="kd_code" name="kd_code" value="<?= esc($language['kd_code']) ?>">
          </div>

          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="active" name="active" <?= $language['active'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="active">Active</label>
          </div>

          <button type="submit" class="btn btn-primary w-100">Update</button>
        </form>
      </div>
    </div>
  </div>

</body>

</html>
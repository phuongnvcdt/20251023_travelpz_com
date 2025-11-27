<!DOCTYPE html>
<html>

<head>
  <title>Add translation for location "<?= esc($location['en_name']) ?>"</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h3 class="text-center mb-4">Add translation for location "<?= esc($location['en_name']) ?>"</h3>

        <div class="mb-3 d-flex justify-content-start">
          <a href="<?= admin_location_trans_link($location) ?>" class="btn btn-secondary">Location Translations</a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= admin_location_trans_link($location, null, 'store') ?>">
          <div class="mb-3">
            <label for="language_id" class="form-label">Language</label>
            <select class="form-select" id="language_id" name="language_id">
              <option value="" disabled>-- Select --</option>
              <?php foreach ($languages as $language): ?>
                <option value="<?= $language['id'] ?>"><?= esc($language['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <button type="submit" class="btn btn-primary w-100">Save</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
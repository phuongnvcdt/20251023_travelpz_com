<!DOCTYPE html>
<html>

<head>
  <title>Add Item</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="bg-light">
  <div class="container mt-5">
    <h3 class="text-center mb-4">Add Item</h3>

    <div class="mb-3 d-flex justify-content-start">
      <a href="<?= admin_item_link() ?>" class="btn btn-secondary">Items</a>
    </div>

    <form method="post" action="<?= admin_item_link(null, 'store') ?>">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="en_name" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Source</label>
        <select class="form-select" name="source_id" required>
          <option value="" selected disabled>-- Select --</option>
          <?php foreach ($sources as $s): ?>
            <option value="<?= $s['id'] ?>"><?= esc($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Source Item ID</label>
        <input type="text" class="form-control" name="source_item_id">
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select class="form-select" name="category_id" required>
          <option value="" selected disabled>-- Select --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= esc($c['en_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Sub Categories</label>
        <select class="form-select subcategories-select" name="sub_categories[]" multiple="multiple">
          <?php foreach ($sub_categories as $sc): ?>
            <option value="<?= $sc['id'] ?>">
              <?= esc($sc['en_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Country</label>
        <select class="form-select" name="country_id">
          <option value="" selected disabled>-- Select --</option>
          <?php foreach ($countries as $loc): ?>
            <option value="<?= $loc['id'] ?>"><?= esc($loc['en_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">City</label>
        <select class="form-select" name="city_id">
          <option value="" selected disabled>-- Select --</option>
          <?php foreach ($cities as $loc): ?>
            <option value="<?= $loc['id'] ?>"><?= esc($loc['en_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="en_description"></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="text" class="form-control" name="image">
      </div>

      <div class="mb-3">
        <label class="form-label">Rating</label>
        <input type="number" step="0.01" class="form-control" name="rating">
      </div>

      <div class="mb-3">
        <label class="form-label">Rating Count</label>
        <input type="number" class="form-control" name="rating_count">
      </div>

      <div class="mb-3">
        <label class="form-label">Youtube</label>
        <input type="text" class="form-control" name="youtube_id">
      </div>

      <button type="submit" class="btn btn-primary w-100">Save</button>
    </form>
  </div>

  <script>
    $(document).ready(function() {
      $('.subcategories-select').select2({
        placeholder: "  -- Select --",
        allowClear: true,
        width: '100%'
      });
    });
  </script>
</body>

</html>
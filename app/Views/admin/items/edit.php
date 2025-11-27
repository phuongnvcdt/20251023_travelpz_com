<!DOCTYPE html>
<html>

<head>
  <title>Edit Item</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="bg-light">
  <div class="container mt-5">
    <h3 class="text-center mb-4">Edit Item</h3>

    <div class="mb-3">
      <a href="<?= admin_item_link() ?>" class="btn btn-secondary">Items</a>
    </div>

    <form method="post" action="<?= admin_item_link($item, 'update') ?>">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="en_name" value="<?= esc($item['en_name']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Source</label>
        <select class="form-select" name="source_id" required>
          <?php foreach ($sources as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $s['id'] == $item['source_id'] ? 'selected' : '' ?>><?= esc($s['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Source Item ID</label>
        <input type="text" class="form-control" name="source_item_id" value="<?= esc($item['source_item_id']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select class="form-select" name="category_id" required>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $c['id'] == $item['category_id'] ? 'selected' : '' ?>><?= esc($c['en_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Sub Categories</label>
        <select class="form-select subcategories-select" name="sub_categories[]" multiple="multiple">
          <?php foreach ($sub_categories as $sc): ?>
            <option value="<?= $sc['id'] ?>" <?= in_array($sc['id'], $selected_subs) ? 'selected' : '' ?>>
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
            <option value="<?= $loc['id'] ?>" <?= $loc['id'] == $item['country_id'] ? 'selected' : '' ?>><?= esc($loc['en_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">City</label>
        <select class="form-select" name="city_id">
          <option value="" selected disabled>-- Select --</option>
          <?php foreach ($cities as $loc): ?>
            <option value="<?= $loc['id'] ?>" <?= $loc['id'] == $item['city_id'] ? 'selected' : '' ?>><?= esc($loc['en_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="en_description"><?= esc($item['en_description']) ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="text" class="form-control" name="image" value="<?= esc($item['image']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Rating</label>
        <input type="number" step="0.01" class="form-control" name="rating" value="<?= esc($item['rating']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Rating Count</label>
        <input type="number" class="form-control" name="rating_count" value="<?= esc($item['rating_count']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Youtube</label>
        <input type="text" class="form-control" name="youtube_id" value="<?= esc($item['youtube_id']) ?>">
      </div>

      <button type="submit" class="btn btn-primary w-100">Update</button>
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
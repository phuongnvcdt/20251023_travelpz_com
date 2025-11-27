<!DOCTYPE html>
<html>

<head>
  <title>Items</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
  <div class="container mt-5">
    <h3 class="text-center mb-4">Items</h3>

    <div class="mb-3 d-flex justify-content-between">
      <a href="<?= admin_dashboard() ?>" class="btn btn-secondary">Dashboard</a>
      <a href="<?= admin_item_link(null, 'create') ?>" class="btn btn-primary">Add Item</a>
    </div>

    <table class="table table-bordered table-striped bg-white">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Source</th>
          <th>Category</th>
          <th>Sub Categories</th>
          <th>Country</th>
          <th>City</th>
          <th>Rating</th>
          <th>Rating Count</th>
          <th>Youtube</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($items)): ?>
          <?php foreach ($items as $i): ?>
            <tr>
              <td><?= $i['id'] ?></td>
              <td><?= esc($i['en_name']) ?></td>
              <td><?= esc($i['source_name']) ?></td>
              <td><?= esc($i['category_name']) ?></td>
              <td><?= esc($i['sub_categories']) ?></td>
              <td><?= esc($i['country_name']) ?></td>
              <td><?= esc($i['city_name']) ?></td>
              <td><?= esc($i['rating']) ?></td>
              <td><?= esc($i['rating_count']) ?></td>
              <td><?= esc($i['youtube_id']) ?></td>
              <td>
                <a href="<?= admin_item_link($i, 'edit') ?>" class="btn btn-sm btn-warning m-1">Edit</a>
                <a href="<?= admin_item_link($i, 'delete') ?>" class="btn btn-sm btn-danger m-1" onclick="return confirm('Are you sure?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="text-center"><?= trans('No items found') ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>

</html>
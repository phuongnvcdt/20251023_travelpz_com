<!DOCTYPE html>
<html>
<head>
    <title>Locations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="text-center mb-4">Locations</h3>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="mb-3 d-flex justify-content-between">
                <a href="<?= admin_dashboard() ?>" class="btn btn-secondary">Dashboard</a>
                <a href="<?= admin_location_link(null, 'create') ?>" class="btn btn-primary">Add Location</a>
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
                    <?php if (!empty($locations) && is_array($locations)): ?>
                        <?php foreach ($locations as $loc): ?>
                            <tr>
                                <td><?= $loc['id'] ?></td>
                                <td><?= esc($loc['en_name']) ?></td>
                                <td><?= esc($loc['slug']) ?></td>
                                <td><?= esc($loc['parent_name'] ?: '-') ?></td>
                                <td>
                                    <a href="<?= admin_location_link($loc, 'edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?= admin_location_link($loc, 'delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    <a href="<?= admin_location_link($loc, 'sources') ?>" class="btn btn-sm btn-info">Sources</a>
                                    <a href="<?= admin_location_link($loc, 'trans') ?>" class="btn btn-sm btn-success">Translations</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No locations found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Source ids for location: <?= esc($location['en_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="text-center mb-4">Source ids for location: "<?= esc($location['en_name']) ?>"</h3>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="mb-3 d-flex justify-content-between">
                <a href="<?= admin_location_link() ?>" class="btn btn-secondary">Locations</a>
                <a href="<?= admin_location_source_link($location, null, 'create') ?>" class="btn btn-primary">Add Source ID</a>
            </div>

            <table class="table table-bordered table-striped bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Source</th>
                        <th>ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($location_sources) && is_array($location_sources)): ?>
                        <?php foreach ($location_sources as $ls): ?>
                            <tr>
                                <td><?= $ls['id'] ?></td>
                                <td><?= esc($ls['source_name']) ?></td>
                                <td><?= esc($ls['location_source_id']) ?></td>
                                <td>
                                    <a href="<?= admin_location_source_link($location, $ls, 'edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?= admin_location_source_link($location, $ls, 'delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No location source ids found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>


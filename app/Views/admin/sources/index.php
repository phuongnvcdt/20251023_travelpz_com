<!DOCTYPE html>
<html>
<head>
    <title>Sources</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="text-center mb-4">Sources</h3>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="mb-3 d-flex justify-content-between">
                <a href="<?= admin_dashboard() ?>" class="btn btn-secondary">Dashboard</a>
                <a href="<?= admin_source_link(null, 'create') ?>" class="btn btn-primary">Add Source</a>
            </div>

            <table class="table table-bordered table-striped bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sources) && is_array($sources)): ?>
                        <?php foreach ($sources as $src): ?>
                            <tr>
                                <td><?= $src['id'] ?></td>
                                <td><?= esc($src['name']) ?></td>
                                <td><?= esc($src['slug']) ?></td>
                                <td>
                                    <a href="<?= admin_source_link($src, 'edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?= admin_source_link($src, 'delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No sources found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>

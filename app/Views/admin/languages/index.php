<!DOCTYPE html>
<html>
<head>
    <title>Languages</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="text-center mb-4">Languages</h3>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <div class="mb-3 d-flex justify-content-between">
                <a href="<?= admin_dashboard() ?>" class="btn btn-secondary">Dashboard</a>
                <a href="<?= admin_language_link(null, 'create') ?>" class="btn btn-primary">Add Language</a>
            </div>

            <table class="table table-bordered table-striped bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Locale</th>
                        <th>Agoda ID</th>
                        <th>Klook Code</th>
                        <th>Kkday Code</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($languages) && is_array($languages)): ?>
                        <?php foreach ($languages as $lang): ?>
                            <tr>
                                <td><?= $lang['id'] ?></td>
                                <td><?= esc($lang['name']) ?></td>
                                <td><?= esc($lang['code']) ?></td>
                                <td><?= esc($lang['locale']) ?></td>
                                <td><?= esc($lang['a_id']) ?></td>
                                <td><?= esc($lang['k_code']) ?></td>
                                <td><?= esc($lang['kd_code']) ?></td>
                                <td>
                                    <?= $lang['active'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                                </td>
                                <td>
                                    <a href="<?= admin_language_link($lang, 'edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?= admin_language_link($lang, 'delete') ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No languages found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>

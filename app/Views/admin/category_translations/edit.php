<!DOCTYPE html>
<html>
<head>
    <title>Edit translation for category "<?= esc($category['en_name']) ?>"</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center mb-4">Edit translation for category "<?= esc($category['en_name']) ?>"</h3>

            <div class="mb-3 d-flex justify-content-start">
                <a href="<?= admin_category_trans_link($category) ?>" class="btn btn-secondary">Location Translations</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form method="post" action="<?= admin_category_trans_link($category, $record, 'update') ?>">
                <div class="mb-3">
                    <label for="language_id" class="form-label">Source</label>
                    <select class="form-select" id="language_id" name="language_id" disabled>
                        <option value="" disabled>-- Select --</option>
                        <?php foreach($languages as $language): ?>
                            <option value="<?= $language['id'] ?>" <?= $language['id'] == $record['language_id'] ? 'selected' : '' ?>><?= esc($language['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= esc($record['name']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

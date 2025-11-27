<!DOCTYPE html>
<html>
<head>
    <title>Add Location</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center mb-4">Add Location</h3>

            <div class="mb-3 d-flex justify-content-start">
                <a href="<?= admin_location_link() ?>" class="btn btn-secondary">Locations</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form method="post" action="<?= admin_location_link(null, 'store') ?>">
                <div class="mb-3">
                    <label for="en_name" class="form-label">English Name</label>
                    <input type="text" class="form-control" id="en_name" name="en_name" required>
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Parent Location</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">None</option>
                        <?php foreach($parents as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= esc($p['en_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Save</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

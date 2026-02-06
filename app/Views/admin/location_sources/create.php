<!DOCTYPE html>
<html>
<head>
    <title>Add source id for location "<?= esc($location['en_name']) ?>"</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3 class="text-center mb-4">Add source id for location "<?= esc($location['en_name']) ?>"</h3>

            <div class="mb-3 d-flex justify-content-start">
                <a href="<?= admin_location_source_link($location) ?>" class="btn btn-secondary">Location Sources</a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>

            <form method="post" action="<?= admin_location_source_link($location, null, 'store') ?>">
                <div class="mb-3">
                    <label for="source_id" class="form-label">Source</label>
                    <select class="form-select" id="source_id" name="source_id">
                        <option value="" disabled>-- Select --</option>
                        <?php foreach($sources as $source): ?>
                            <option value="<?= $source['id'] ?>"><?= esc($source['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="location_source_id" class="form-label">ID</label>
                    <input type="text" class="form-control" id="location_source_id" name="location_source_id" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Save</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>

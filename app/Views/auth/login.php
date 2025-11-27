<?= $this->include('layout/header', $base_data) ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <h1 class="text-center mb-4">Login</h1>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('login') ?>">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
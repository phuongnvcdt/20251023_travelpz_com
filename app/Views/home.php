<?= $this->include('layout/header', $base_data) ?>

<section id="main" class="margin-top-30">
  <div class="container mt-5">
    <h1 class="mb-4"><?= $base_data['meta_title'] ?></h1>
    <div class="row">
      <div class="col-xs-12 col-sm-12 col-md-9">
        <?php if (empty($items)) : ?>
          <div class="alert alert-info"><?= trans('No items found') ?></div>
        <?php else: ?>
          <div class="row items-list-row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
            <?php foreach ($items as $index => $item): ?>
              <div class="mb-2">
                <?= view('layout/item_card', ['item' => $item, 'lazy' => $base_data['is_mobile'] && $index > 0]) ?>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="d-flex justify-content-center">
            <?= $pager->links('default', 'custom_full') ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-xs-12 col-sm-12 col-md-3">
        <?= $this->include('layout/sidebar', $sidebar_data) ?>
      </div>
    </div>
  </div>
</section>

<?= $this->include('layout/footer') ?>
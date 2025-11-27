<?= $this->include('layout/header', $base_data) ?>

<div class="container mt-5">
  <?= $this->include('layout/breadcrumb', $breadcrumb_data) ?>
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

      <?php if (!empty($detail['description'])): ?>
        <div class="text-justify border-top py-2">
          <h2 class="mb-3">
            <b><?= $detail['title'] ?? '' ?></b>
          </h2>
          <?= $detail['description'] ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($tags)): ?>
        <div class="d-flex flex-wrap gap-2 border-top py-2">
          <?php foreach ($tags as $tag): ?>
            <a href="<?= $tag['link'] ?>" class="btn btn-outline-secondary btn-sm">
              #<?= htmlspecialchars($tag['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-3">
      <?= $this->include('layout/sidebar', $sidebar_data) ?>
    </div>
  </div>
</div>

<?= $this->include('layout/footer') ?>
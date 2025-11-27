<div class="mt-5">
  <?php foreach ($relate_items as $rl_item): ?>
    <div class="related-items mt-5">
      <div class="related-item-title border-bottom py-1 border-primary mb-1">
        <h2 class="title text-body">
          <a href="<?= esc($rl_item['link']) ?>">
            <?= esc($rl_item['title']) ?>
          </a>
        </h4>
      </div>
      <div class="row related-items-row g-2">
        <?php foreach ($rl_item['items'] as $item): ?>
          <div class="col-6 col-md-4 col-lg-4">
            <?= view('layout/item_card', ['item' => $item, 'lazy' => true]) ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
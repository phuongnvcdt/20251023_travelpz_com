<div class="card card-travel h-100 shadow border-0 rounded-4 p-2">
  <a href="<?= item_link($item) ?>" aria-label="<?= esc($item['en_name'] ?? '') ?>">
    <div class="ratio ratio-16x9">
      <img class="item-img lazy-img card-img-top object-fit-cover rounded-3" src="<?= itemThumbLink($item) ?>" alt="<?= esc($item['trans_name'] ?? $item['en_name'] ?? '') ?>" style="aspect-ratio: 16/9;" fetchpriority="<?= $lazy ? 'auto' : 'high' ?>" <?= $lazy ? 'loading="lazy"' : '' ?>>
      <span class="m-2 translate-middle <?= dotBackground($item) ?> border border-light rounded-circle" style="width: 10px; height: 10px; "></span>
    </div>
  </a>

  <div class="card-body d-flex flex-column">
    <div class="card-title mb-1 text-truncate text-body">
      <a href="<?= item_link($item) ?>" aria-label="<?= esc(item_title($item)) ?>">
        <?= esc(item_title($item)) ?>
      </a>
    </div>

    <!-- Rating -->
    <?= view('layout/rating_star', ['rating' => $item['rating'] ?? 0, 'count' => $item['rating_count'] ?? 0, 'text' => '']) ?>

    <!-- Action Buttons -->
    <div class="post-buttons d-flex justify-content-between align-items-center mt-2">
      <a rel="nofollow" class="book-now text-success" href="<?= item_book_link($item) ?>">
        <i class="fa fa-cart-plus book-now-i"></i> <?= trans('Book Now') ?>
      </a>
      <a href="<?= item_link($item) ?>" class="read-more text-body">
        <?= trans('Read More') ?>
        <span class="visually-hidden"> about <?= esc($item['en_name'] ?? '') ?></span>
        <i class="fa fa-angle-right read-more-i" aria-hidden="true"></i>
      </a>
    </div>
  </div>
</div>
<?= $this->include('layout/header', $base_data) ?>

<div class="container mt-5">
  <?= $this->include('layout/breadcrumb', $breadcrumb_data) ?>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-9 mb-5">
      <!-- Rating -->
      <?= view('layout/rating_star', ['rating' => $item['rating'], 'count' => $item['rating_count'], 'text' => $detail['reviews']['text'] ?? '']) ?>

      <!-- carousel -->
      <?= view('layout/img_carousel', ['images' => $detail['images'] ?? [['url' => item_img_link($item)]]]) ?>

      <div class="item-text text-style text-center mb-3">
        <a rel="nofollow" class="btn btn-primary btn-action" href="<?= item_book_link($item) ?>">
          <i class="fa fa-cart-plus book-now-i"></i>&nbsp;&nbsp;<?= trans('Book Now') ?>
        </a>
      </div>
      <a rel="nofollow" href="<?= item_book_link($item) ?>" class="btn btn-primary booknow" style="display: none;">
        <i class="fa fa-cart-plus book-now-i"></i>&nbsp;&nbsp;<?= trans('Book Now') ?>
      </a>

      <?php if (!empty($detail['address']['full']) || !empty($detail['map'])): ?>
        <?= view('layout/item_location', ['address' => $detail['address']['full'] ?? '', 'map' => $detail['map'] ?? null]) ?>
      <?php endif; ?>

      <div class="text-justify mb-5">
        <?php if (!empty($detail['description'])): ?>
          <?= $detail['description'] ?>
        <?php elseif (!empty($item['en_description'])): ?>
          <?php if (isset($base_data['language']['locale']) && $base_data['language']['locale'] != 'en-US'): ?>
            <p class="fst-italic">
              &lt;&lt; English description &gt;&gt;
            </p>
          <?php endif; ?>
          <?= $item['en_description'] ?>
        <?php endif; ?>
      </div>

      <?php if (!empty($detail['notes'])): ?>
        <?= view('layout/item_features', ['title' => trans('Notes'), 'list' => $detail['notes']]) ?>
      <?php endif; ?>

      <?php if (!empty($detail['features'])): ?>
        <?php foreach ($detail['features'] as $feature): ?>
          <?= view('layout/item_features', ['title' => $feature['title'], 'list' => $feature['list']]) ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (!empty($detail['love_features'])): ?>
        <?= view('layout/item_features', ['title' => $detail['love_features']['title'], 'list' => $detail['love_features']['list']]) ?>
      <?php endif; ?>

      <?php if (!empty($reviews)): ?>
        <div>
          <?php foreach ($reviews as $review): ?>
            <?= view('layout/review_card', ['review' => $review]) ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($item['youtube_id'])): ?>
        <div class="ratio ratio-16x9 mt-5">
          <iframe class="d-block w-100 h-100 object-fit-cover lazyload rounded-3" src="https://www.youtube.com/embed/<?= esc($item['youtube_id']) ?>" title="YouTube video" frameborder="0" allowfullscreen>
          </iframe>
        </div>
      <?php endif; ?>

      <?php if (!empty($relate_items)): ?>
        <?= view('layout/relate_items', ['relate_items' => $relate_items]) ?>
      <?php endif; ?>

      <?php if (!empty($tags)): ?>
        <div class="d-flex flex-wrap gap-2 mt-5 border-top py-2">
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

<a href="#" class="btn btn-secondary scrollup" style="display: block;" aria-label="Top">
  <i class="fa fa-angle-up"></i>
</a>

<?= $this->include('layout/footer', $js_sources) ?>
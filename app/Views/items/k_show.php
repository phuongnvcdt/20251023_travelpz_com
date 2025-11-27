<?= $this->include('layout/header', ['base_data' => $base_data, 'css_sources' => $css_sources]) ?>

<div class="container mt-5">
  <?= $this->include('layout/breadcrumb', $breadcrumb_data) ?>

  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-9 mb-5">

      <!-- Rating -->
      <div class="d-flex" style="width: auto;">
        <div class="rating-stars">
          <?= render_stars($item['rating']) ?>
        </div>
        <div class="rating-number text-primary">
          &nbsp;(<?= short_number($item['rating_count']) ?>)
        </div>
        <p class="text-success">
          &nbsp;&nbsp;
        </p>
      </div>

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

      <?php if (!empty($detail['address']) || !empty($detail['map'])): ?>
        <div class="mb-5">
          <b>
            <?= esc($detail['address']['full'] ?? '') ?>
          </b>
          <?php if (($detail['map']['lat'] ?? null) && ($detail['map']['long'] ?? null)): ?>
            <a rel="nofollow" href="<?= item_map_link($detail['map']) ?>" target="_blank" class="ms-2 text-decoration-none">
              <i class="fa fa-map-marker-alt text-danger"></i>
              <?= esc(trans('Directions')) ?>
            </a>
            <div class="map-container mt-1 ratio ratio-16x9">
              <iframe class="d-block w-100 h-100 object-fit-cover rounded-3"
                frameborder="0"
                style="border:0"
                src="https://www.google.com/maps?q=<?= $detail['map']['lat'] ?>,<?= $detail['map']['long'] ?>&z=15&output=embed"
                allowfullscreen>
              </iframe>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <div class="text-justify mb-5">
        <?= $item['en_description'] ?? null ?>
      </div>

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
        <div class="mt-5">
          <?php foreach ($relate_items as $rl_item): ?>
            <div class="related-items mt-5">
              <div class="related-item-title border-bottom py-1 border-primary mb-1">
                <h4 class="title text-body">
                  <a href="<?= esc($rl_item['link']) ?>">
                    <?= esc($rl_item['title']) ?>
                  </a>
                </h4>
              </div>
              <div class="row related-items-row g-2">
                <?php foreach ($rl_item['items'] as $it): ?>
                  <div class="col-6 col-md-4 col-lg-4">
                    <?= view('layout/item_card', ['item' => $it, 'lazy' => true]) ?>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
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
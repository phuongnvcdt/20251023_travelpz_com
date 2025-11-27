<div class="card review-card shadow border-0 rounded-4 p-2">
  <div class="review-header">
    <img src="<?= esc(htmlspecialchars($review['user']['avatar'] ?? avatarLink())) ?>" alt="avatar" class="avatar">
    <div>
      <div class="username"><?= esc(htmlspecialchars($review['user']['name'] ?? trans('User'))) ?></div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-2">
    <?= view('layout/rating_star', ['rating' => $review['rating'], 'count' => null, 'text' => $review['title'] ?? $review['org_title'] ?? '']) ?>

    <?php if (!empty($review['time'])): ?>
      <div class="time text-muted small">
        <?= $review['time']->format('Y-m-d H:i') ?>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($review['product_name'])): ?>
    <div class="product-name"><?= trans('Product') . ': ' . esc($review['product_name']) ?></div>
  <?php endif; ?>

  <div class="review-body">
    <?= nl2br($review['trans_content'] ?? $review['org_content'] ?? '') ?>
  </div>

  <?php if (!empty($review['images'])): ?>
    <div class="review-images d-flex overflow-auto mt-2 py-2" style="gap: 10px;">
      <?php foreach ($review['images'] as $img): ?>
        <?php if (!empty($img)): ?>
          <img loading="lazy" src="<?= esc(htmlspecialchars($img)) ?>" alt="review image" class="rounded border" style="height: 200px; object-fit: cover; flex: 0 0 auto;">
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
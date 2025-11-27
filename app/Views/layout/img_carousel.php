<div id="itemCarousel" class="carousel slide mb-2" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php foreach ($images as $index => $img): ?>
      <div class="carousel-item ratio ratio-16x9 <?= $index == 0 ? 'active' : '' ?>">
        <?php if ($index == 0): ?>
          <img src="<?= esc($img['url']) ?>" class="d-block w-100 h-100 object-fit-cover lazyload rounded-3" alt="<?= empty($img['title']) ? 'Image ' . ($index + 1) : $img['title'] ?>" fetchpriority="high">
        <?php else: ?>
          <img data-src="<?= esc($img['url']) ?>" class="d-block w-100 h-100 object-fit-cover lazyload rounded-3" alt="<?= empty($img['title']) ? 'Image ' . ($index + 1) : $img['title'] ?>" fetchpriority="auto" loading="lazy">
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#itemCarousel" data-bs-slide="prev" aria-label="Previous Image">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#itemCarousel" data-bs-slide="next" aria-label="Next Image">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>
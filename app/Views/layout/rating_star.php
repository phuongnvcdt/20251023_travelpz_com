<div class="d-flex" style="width: auto;">
  <div class="rating-stars">
    <?= render_stars($rating) ?>
  </div>
  <?php if (!empty($count)): ?>
    <div class="rating-number text-primary">
      &nbsp;(<?= short_number($count) ?>)
    </div>
  <?php endif; ?>
  <p class="text-success">
    &nbsp;&nbsp;<?= esc($text ?? '') ?>
  </p>
</div>
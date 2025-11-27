<div class="mb-5">
  <b>
    <?= esc($address ?? '') ?>
  </b>
  <?php if (!empty($map['lat']) && !empty($map['long'])): ?>
    <a rel="nofollow" href="<?= item_map_link($map) ?>" target="_blank" class="ms-2 text-decoration-none">
      <i class="fa fa-map-marker-alt text-danger"></i>
      <?= esc(trans('Directions')) ?>
    </a>
    <div class="map-container mt-1 ratio ratio-16x9">
      <iframe class="d-block w-100 h-100 object-fit-cover rounded-3"
        frameborder="0"
        style="border:0"
        src="https://www.google.com/maps?q=<?= $map['lat'] ?>,<?= $map['long'] ?>&z=15&output=embed"
        allowfullscreen
        title="Google Maps">
      </iframe>
    </div>
  <?php endif; ?>
</div>
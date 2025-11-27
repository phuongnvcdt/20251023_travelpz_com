<div class="sidebar">
  <div class="col-sm-12 col-xs-12 sidebar-widget">
    <div class="row">
      <div class="widget-title border-bottom py-1 border-primary mb-1">
        <h2 class="title text-body"><?= $sidebar_data['title'] ?></h4>
      </div>
      <div class="col-sm-12 widget-body">
        <div class="row">
          <ul class="widget-list location-list list-unstyled">
            <?php foreach ($sidebar_data['list'] as $item): ?>
              <li class="location-item border-bottom py-1">
                <a class="text-body" href="<?= $item['link'] ?>"><?= $item['trans_name'] ?? $item['en_name'] ?></a> <span class="text-muted">(<?= $item['item_count'] ?>)</span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
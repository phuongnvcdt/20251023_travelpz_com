<div class="sidebar" style="position: sticky; top: 80px;">
  <div class="sidebar-widget">
    <div class="widget-title border-bottom py-1 border-primary mb-1">
      <h2 class="title text-body"><?= $sidebar_data['title'] ?></h2>
    </div>
    <div class="widget-body" id="sidebar-scroll" style="overflow-x: hidden; overflow-y: auto;">
      <ul class="widget-list location-list list-unstyled mb-0">
        <?php foreach ($sidebar_data['list'] as $item): ?>
          <li class="location-item border-bottom py-1">
            <a class="text-body" href="<?= $item['link'] ?>"><?= $item['trans_name'] ?? $item['en_name'] ?></a> <span class="text-muted">(<?= $item['item_count'] ?>)</span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>

<script>
  (function() {
    var sidebarBody = document.getElementById('sidebar-scroll');
    var col3 = sidebarBody.closest('[class*="col-md-3"]');
    if (!col3) return;
    var row = col3.parentElement;
    var col9 = row ? row.querySelector('[class*="col-md-9"]') : null;
    if (!col9 || !row) return;

    // Set ngay để cột không bị giãn từ đầu
    row.style.alignItems = 'flex-start';

    function updateHeight() {
      var titleEl = sidebarBody.previousElementSibling;
      var titleH = titleEl ? titleEl.getBoundingClientRect().height : 0;
      var h = col9.getBoundingClientRect().height;
      if (h > 0) sidebarBody.style.maxHeight = (h - titleH) + 'px';
    }

    // Đo sau khi trang load xong (ảnh, font đã render)
    window.addEventListener('load', updateHeight);
    window.addEventListener('resize', updateHeight);
  })();
</script>
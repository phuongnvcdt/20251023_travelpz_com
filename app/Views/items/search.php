<?= $this->include('layout/header', $base_data) ?>

<div class="container mt-5">

  <h1 class="mb-3"><?= trans('Search') ?></h1>

  <div id="form" method="get" data-url="<?= locale_url('search') ?>">
    <div class="row g-3 mb-1 d-flex align-items-center">
      <div class="col-md-1">
        <label class="form-label"><?= trans('Keyword') ?>:</label>
      </div>

      <div class="col-md-3">
        <input id="tb_keyword" type="text" name="q" class="form-control" placeholder="<?= trans('Keyword') ?>"
          value="<?= esc($keyword) ?>">
      </div>
    </div>

    <div class="row g-3 mb-1 d-flex align-items-center">
      <div class="col-md-1">
        <label class="form-label"><?= trans('Category') ?>:</label>
      </div>

      <div class="col-md-3">
        <select id="sl_cat" name="cat" class="form-select" data-url="<?= locale_url('ajax/categories') ?>">
          <option value=""><?= trans('All Categories') ?></option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= esc($cat['slug']) ?>" <?= $cat['slug'] == $currentCat ? 'selected' : '' ?>>
              <?= esc($cat['trans_name'] ?? $cat['en_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <select id="sl_sub_cat" name="sub_cat" class="form-select" data-empty-value="<?= trans('All Sub Categories') ?>">
          <option value=""><?= trans('All Sub Categories') ?></option>
          <?php foreach ($sub_categories as $cat): ?>
            <option value="<?= esc($cat['slug']) ?>" <?= $cat['slug'] == $currentSubCat ? 'selected' : '' ?>>
              <?= esc($cat['trans_name'] ?? $cat['en_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-1">
      </div>

      <div class="col-md-4 d-grid">
        <button id="btn_search" type="submit" class="btn btn-primary">
          <i class="fa fa-search"></i> <?= trans('Search') ?>
        </button>
      </div>
    </div>

    <div class="row g-3 mb-1 d-flex align-items-center">
      <div class="col-md-1">
        <label class="form-label"><?= trans('Location') ?>:</label>
      </div>

      <div class="col-md-3">
        <select id="sl_country" name="country" class="form-select" data-url="<?= locale_url('ajax/locations') ?>">
          <option value=""><?= trans('All Countries') ?></option>
          <?php foreach ($countries as $loc): ?>
            <option value="<?= esc($loc['slug']) ?>" <?= $loc['slug'] == $currentCountry ? 'selected' : '' ?>>
              <?= esc($loc['trans_name'] ?? $loc['en_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-3">
        <select id="sl_city" name="city" class="form-select" data-empty-value="<?= trans('All Cities') ?>">
          <option value=""><?= trans('All Cities') ?></option>
          <?php foreach ($cities as $loc): ?>
            <option value="<?= esc($loc['slug']) ?>" <?= $loc['slug'] == $currentCity ? 'selected' : '' ?>>
              <?= esc($loc['trans_name'] ?? $loc['en_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <?php if (empty($items)): ?>
    <div class="alert alert-info mt-5"><?= trans('No results found') ?></div>
  <?php else: ?>
    <div class="row items-list-row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-1">
      <?php foreach ($items as $index => $item): ?>
        <div class="mb-2">
          <?= view('layout/item_card', ['item' => $item, 'lazy' => $base_data['is_mobile'] && $index > 0]) ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="d-flex justify-content-center">
      <?= $pager->links('default', 'custom_full') ?>
    </div>
  <?php endif; ?>
</div>

<?= $this->include('layout/footer') ?>
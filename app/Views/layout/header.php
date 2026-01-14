<!DOCTYPE html>
<html lang="<?= config('App')->currentLocale ?>">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- ✅ SEO meta -->
  <title><?= esc($base_data['meta_title']) ?></title>
  <meta name="description" content="<?= esc($base_data['meta_description']) ?>">
  <meta name="keywords" content="<?= esc(implode(', ', $base_data['meta_keywords'])) ?>">
  <meta property="og:locale" content="<?= config('App')->currentLocale ?>">

  <!-- ✅ Open Graph (Facebook, LinkedIn) -->
  <meta property="og:site_name" content="TravelPZ">
  <meta property="og:title" content="<?= esc($base_data['meta_title']) ?>">
  <meta property="og:description" content="<?= esc($base_data['meta_description']) ?>">
  <meta property="og:image" content="<?= esc($base_data['meta_image']) ?>">
  <meta property="og:url" content="<?= clean_first_page_url(current_url(true)) ?>">
  <meta property="og:type" content="website">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">


  <!-- ✅ Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= esc($base_data['meta_title']) ?>">
  <meta name="twitter:description" content="<?= esc($base_data['meta_description']) ?>">
  <meta name="twitter:image" content="<?= esc($base_data['meta_image']) ?>">

  <meta name="robots" content="index, follow">
  <meta name="theme-color" content="#ffffff">
  <meta name="author" content="TravelPZ">

  <!-- ✅ Favicon -->
  <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
  <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/img/favicon-16x16.png') ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/img/favicon-32x32.png') ?>">
  <link rel="icon" type="image/png" sizes="48x48" href="<?= base_url('assets/img/favicon-48x48.png') ?>">

  <!-- Apple iOS -->
  <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/img/apple-touch-icon.png') ?>">

  <!-- Android / Chrome -->
  <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('assets/img/android-chrome-192x192.png') ?>">
  <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('assets/img/android-chrome-512x512.png') ?>">

  <link rel="canonical" href="<?= clean_first_page_url(current_url(true)) ?>">
  <?php if (!empty($base_data['meta_hreflangs'])): ?>
    <?php foreach ($base_data['meta_hreflangs'] as $lang => $url): ?>
      <link rel="alternate" href="<?= clean_first_page_url($url) ?>" hreflang="<?= esc($lang) ?>">
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (isset($base_data['meta_json_ld'])): ?>
    <!-- Google JSON-LD -->
    <script type="application/ld+json">
      <?= json_encode($base_data['meta_json_ld'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>
  <?php endif; ?>

  <?php if (!empty($base_data['ga_id'])): ?>
    <!-- Google tag (gtag.js) -->
    <script defer src="https://www.googletagmanager.com/gtag/js?id=G-<?= $base_data['ga_id'] ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];

      function gtag() {
        dataLayer.push(arguments);
      }
      gtag('js', new Date());

      gtag('config', 'G-<?= $base_data['ga_id'] ?>');
    </script>
  <?php endif; ?>

  <?php if (!empty($base_data['bw_id'])): ?>
    <!-- Bing Webmastertool -->
    <meta name="msvalidate.01" content="<?= $base_data['bw_id'] ?>" />
  <?php endif; ?>

  <?php if (!empty($base_data['cl_id'])): ?>
    <!-- Clarity -->
    <script type="text/javascript">
      (function(c, l, a, r, i, t, y) {
        c[a] = c[a] || function() {
          (c[a].q = c[a].q || []).push(arguments)
        };
        t = l.createElement(r);
        t.async = 1;
        t.src = "https://www.clarity.ms/tag/" + i;
        y = l.getElementsByTagName(r)[0];
        y.parentNode.insertBefore(t, y);
      })(window, document, "clarity", "script", "<?= $base_data['cl_id'] ?>");
    </script>
  <?php endif; ?>

  <?php if (!empty($base_data['nv_id'])): ?>
    <!-- Naver Webmastertool -->
    <meta name="naver-site-verification" content="<?= $base_data['nv_id'] ?>" />
  <?php endif; ?>

  <!-- ✅ Font -->
  <link rel="preload" href="<?= base_url('assets/css/font-awesome/webfonts/fa-solid-900.woff2') ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?= base_url('assets/css/font-awesome/webfonts/fa-brands-400.woff2') ?>" as="font" type="font/woff2" crossorigin>

  <!-- ✅ CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

  <link rel="preload" href="<?= base_url('assets/css/bootstrap/5.3.0/bootstrap.min.css') ?>" as="style" onload="this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap/5.3.0/bootstrap.min.css') ?>">
  </noscript>

  <link rel="preload" href="<?= base_url('assets/css/font-awesome/6.6.0/all.min.css') ?>" as="style" onload="this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="<?= base_url('assets/css/font-awesome/6.6.0/all.min.css') ?>">
  </noscript>

  <?php foreach ($css_sources ?? [] as $src): ?>
    <link rel="stylesheet" href="<?= $src ?>">
  <?php endforeach; ?>
</head>

<body>
  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm py-2">
      <div class="container">

        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="<?= locale_url() ?>">
          <img src="<?= base_url('assets/img/home.webp') ?>" alt="Home" style="width: 180px; height: 50px;">
        </a>

        <!-- Toggle button for mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
          aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar content -->
        <div class="collapse navbar-collapse" id="navbarMain">
          <!-- Left: Category menu -->
          <ul class="navbar-nav me-auto nav-tabs-custom">
            <?php foreach ($base_data['categories'] ?? [] as $cat): ?>
              <li class="nav-item">
                <a class="nav-link <?= ($cat['id'] == ($base_data['category']['id'] ?? '')) ? 'active' : '' ?>"
                  href="<?= category_link($cat) ?>">
                  <?= esc($cat['trans_name'] ?? $cat['en_name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>

          <!-- Right: Actions -->
          <ul class="navbar-nav ms-auto align-items-center">

            <!-- Search -->
            <li class="nav-item me-2">
              <form class="d-flex" role="search" action="<?= search_link() ?>" method="get">
                <input class="form-control form-control-sm me-2" type="search" name="q" placeholder="<?= trans('Search') ?>..." aria-label="Search">
                <button class="btn btn-outline-primary btn-sm" type="submit">🔍</button>
              </form>
            </li>

            <li class="nav-item me-2">
              <div class="d-flex align-items-center">
                <!-- Login -->
                <div>
                  <a class="btn btn-outline-success btn-sm" href="<?= base_url('login') ?>">
                    <?= trans('Login') ?>
                  </a>
                </div>

                <!-- Language -->
                <div class="nav-item dropdown p-2">
                  <a class="nav-link dropdown-toggle text-primary" href="#" id="langDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    🌐 <?= $base_data['language']['name'] ?? '' ?>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                    <?php foreach ($base_data['languages'] ?? [] as $lang): ?>
                      <?php $is_active = $lang['locale'] === $base_data['language']['locale']; ?>
                      <li>
                        <a
                          class="dropdown-item <?= $is_active ? 'active' : '' ?>"
                          <?= !$is_active ? 'href="' . language_link($lang['locale']) . '"' : '' ?>
                          <?= $is_active ? 'aria-disabled="true" style="pointer-events:none;"' : '' ?>>
                          <?= esc($lang['name']) ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              </div>
            </li>

          </ul>
        </div>
      </div>
    </nav>
  </header>

  <main class="container mt-5">
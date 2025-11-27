<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
  <ul class="pagination">
    <?php if ($pager->hasPrevious()) : ?>
      <li>
        <a href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
          <span aria-hidden="true"><?= lang('Pager.first') ?></span>
        </a>
      </li>
      <li>
        <a href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>">
          <span aria-hidden="true"><?= lang('Pager.previous') ?></span>
        </a>
      </li>
    <?php endif ?>

    <?php foreach ($pager->links() as $link) : ?>
      <?php
      $pageNum = (int) $link['title'];
      $url = (string) $link['uri'];
      $rel = '';

      // Nếu là trang 1 thì bỏ query ?page=1
      if ($pageNum === 1) {
        $rel = 'rel="nofollow"';
        // Xóa ?page=1 hoặc &page=1 khỏi URL
        $url = preg_replace('/([?&])page=1(&|$)/', '$1', $url);
        $url = rtrim($url, '?&'); // dọn dấu thừa
        // Nếu sau khi xóa hết query => trở về URL gốc (trang đầu)
        if (!str_contains($url, '?')) {
          $url = strtok($url, '?');
        }
      }
      ?>
      <li <?= $link['active'] ? 'class="active"' : '' ?>>
        <a href="<?= $url ?>" <?= $rel ?>>
          <?= $link['title'] ?>
        </a>
      </li>
    <?php endforeach ?>

    <?php if ($pager->hasNext()) : ?>
      <li>
        <a href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>">
          <span aria-hidden="true"><?= lang('Pager.next') ?></span>
        </a>
      </li>
      <li>
        <a href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">
          <span aria-hidden="true"><?= lang('Pager.last') ?></span>
        </a>
      </li>
    <?php endif ?>
  </ul>
</nav>
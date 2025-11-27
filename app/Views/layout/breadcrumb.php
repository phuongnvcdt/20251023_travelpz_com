<!-- Breadcrumbs -->
<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="<?= locale_url() ?>"><?= trans('Home') ?></a></li>

		<?php foreach ($breadcrumb_data['list'] as $brc): ?>
			<li class="breadcrumb-item">
				<?php if (empty($brc['link'])): ?>
					<?= esc($brc['name']) ?>
				<?php else: ?>
					<a href="<?= $brc['link'] ?>">
						<?= esc($brc['name']) ?>
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>

<h1 class="mb-4">
	<?= $breadcrumb_data['title'] ?>
</h3>
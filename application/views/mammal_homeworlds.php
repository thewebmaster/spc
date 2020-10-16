<div id="body">
	<?php if (!empty($species)): ?>
		<?php foreach ($species as $specie_url): ?>
			<div class='get-species' data-url='<?=$specie_url?>'><img src='<?= ASSETURL ?>/images/ajax-loader.gif' /></div>
		<?php endforeach; ?>
	<?php else: ?>
		<div>No characters found.</div>
	<?php endif; ?>
</div>
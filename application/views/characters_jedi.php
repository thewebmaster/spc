<div id="body">
	<?php if (!empty($characters)): ?>
		<?php foreach ($characters as $character): ?>
			<div class='get-character' data-url='<?=$character?>'><img src='<?= ASSETURL ?>/images/ajax-loader.gif' /></div>
		<?php endforeach; ?>
	<?php else: ?>
		<div>No characters found.</div>
	<?php endif; ?>
</div>
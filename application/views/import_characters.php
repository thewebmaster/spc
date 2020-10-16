<div id="body">
	<?php if (!empty($characters)): ?>
		<?php foreach ($characters as $character): ?>
			<code><?= $character['name'] ?> | <?= json_encode($character) ?></code>
		<?php endforeach; ?>
	<?php else: ?>
		<div>No characters found.</div>
	<?php endif; ?>
</div>
<div id="body">
	<div><?= validation_errors() ?></div>
	<?= form_open('/create-character') ?>
	<div>Name: <?= form_input('name', set_value('name')) ?></div>
	<div>Height: <?= form_input('height', set_value('height')) ?></div>
	<div>Mass: <?= form_input('mass', set_value('mass')) ?></div>
	<div>Hair Colour: <?= form_input('hair_color', set_value('hair_color')) ?></div>
	<div>Birth Year: <?= form_input('birth_year', set_value('birth_year')) ?></div>
	<div>Gender: <?= form_input('gender', set_value('gender')) ?></div>
	<div>Homeworld Name: <?= form_input('homeworld_name', set_value('homeworld_name')) ?></div>
	<div>Species Name: <?= form_input('species_name', set_value('species_name')) ?></div>
	<div><?= form_submit('submit', 'Create Character') ?></div>
	<?= form_close() ?>
</div>


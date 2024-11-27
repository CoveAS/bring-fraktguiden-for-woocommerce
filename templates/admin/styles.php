<?php
/**
 * @var string $skin = 'fresh'
 */
$colors = apply_filters('bring_fraktguiden_skin_colors', match ($skin) {
	default => [
		'#1d2327',
		'#2c3338',
		'#2271b1',
		'#72aee6',
	],
	'light' => [
		'#e5e5e5',
		'#999',
		'#d64e07',
		'#04a4cc',
	],
	'modern' => [
		'#1e1e1e',
		'#1e1e1e',
		'#3858e9',
		'#33f078',
	],
	'blue' => [
		'#096484',
		'#4796b3',
		'#52accc',
		'#74B6CE',
	],
	'coffee' => [
		'#46403c',
		'#59524c',
		'#c7a589',
		'#9ea476',
	],
	'ectoplasm' => [
		'#413256',
		'#523f6d',
		'#a3b745',
		'#d46f15',
	],
	'midnight' => [
		'#25282b',
		'#363b3f',
		'#69a8bb',
		'#e14d43',
	],
	'ocean' => [
		'#627c83',
		'#738e96',
		'#9ebaa0',
		'#aa9d88',
	],
	'sunrise' => [
		'#b43c38',
		'#cf4944',
		'#dd823b',
		'#ccaf0b',
	],
});
?>
<style>
	:root {
		--bfg-c1: <?php echo $colors[0]; ?>;
		--bfg-c2: <?php echo $colors[1]; ?>;
		--bfg-c3: <?php echo $colors[2]; ?>;
		--bfg-c4: <?php echo $colors[3]; ?>;
	}
</style>

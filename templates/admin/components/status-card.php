<?php
/**
 * Status Card Component
 *
 * @var array $args {
 *     @type array  $items Array of ['label' => string, 'value' => string, 'type' => string].
 *     @type string $type  Card type: 'default', 'test', 'trial', 'expired'.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'items' => [],
	'type' => 'default',
]);

$type_classes = [
	'default' => '',
	'test' => 'bfg-subscription--test',
	'trial' => 'bfg-subscription--trial',
	'expired' => 'bfg-subscription--expired',
];

$card_class = 'bfg-subscription';
if (!empty($type_classes[$args['type']])) {
	$card_class .= ' ' . $type_classes[$args['type']];
}
?>
<div class="<?php echo esc_attr($card_class); ?>">
	<?php foreach ($args['items'] as $item): ?>
		<?php
		$item = wp_parse_args($item, [
			'label' => '',
			'value' => '',
			'type' => 'default',
		]);

		$value_type_classes = [
			'default' => '',
			'success' => 'bfg-subscription__value--success',
			'test' => 'bfg-subscription__value--test',
			'trial' => 'bfg-subscription__value--trial',
			'expired' => 'bfg-subscription__value--expired',
		];

		$value_class = 'bfg-subscription__value';
		if (!empty($value_type_classes[$item['type']])) {
			$value_class .= ' ' . $value_type_classes[$item['type']];
		}
		?>
		<div class="bfg-subscription__item">
			<span class="bfg-subscription__label"><?php echo esc_html($item['label']); ?></span>
			<span class="<?php echo esc_attr($value_class); ?>"><?php echo esc_html($item['value']); ?></span>
		</div>
	<?php endforeach; ?>
</div>

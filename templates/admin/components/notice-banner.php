<?php
/**
 * Notice Banner Component
 *
 * @var array $args {
 *     @type string $message The message to display.
 *     @type string $type    Banner type: 'warning', 'info', 'success', 'error'. Default 'warning'.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'message' => '',
	'type' => 'warning',
]);

$icons = [
	'warning' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
	'info' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
	'success' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6666 5L7.49998 14.1667L3.33331 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
	'error' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
];

$icon = $icons[$args['type']] ?? $icons['warning'];
?>
<div class="bfg-notice-banner">
	<span class="bfg-notice-icon">
		<?php echo $icon; ?>
	</span>
	<p><?php echo wp_kses_post($args['message']); ?></p>
</div>

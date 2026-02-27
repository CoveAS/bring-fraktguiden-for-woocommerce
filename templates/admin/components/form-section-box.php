<?php
/**
 * Form Section Box Component
 *
 * Renders a card-style section with header and content area, commonly used in settings forms.
 *
 * @var array $args {
 *     @type string $title         Required. Section title.
 *     @type string $description   Optional description text.
 *     @type string $content       Inner HTML content for the section body.
 *     @type bool   $divider       Whether to show top divider. Default false.
 *     @type bool   $submit_button Whether to include a submit button. Default true.
 *     @type string $submit_text   Custom submit button text. Default 'Save Changes'.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'title' => '',
	'description' => '',
	'content' => '',
	'divider' => false,
	'submit_button' => true,
	'submit_text' => __('Save Changes', 'bring-fraktguiden-for-woocommerce'),
]);

if (!$args['title']) {
	return;
}

$header_class = 'bfg-box__header';
if ($args['divider']) {
	$header_class .= ' bfg-box__header--divider';
}
?>
<div class="bfg-box">
	<div class="<?php echo esc_attr($header_class); ?>">
		<strong><?php echo esc_html($args['title']); ?></strong>
		<?php if ($args['description']): ?>
			<p><?php echo esc_html($args['description']); ?></p>
		<?php endif; ?>
	</div>
	<div class="bfg-box__section">
		<?php echo $args['content']; ?>
		<?php if ($args['submit_button']): ?>
			<?php submit_button($args['submit_text']); ?>
		<?php endif; ?>
	</div>
</div>

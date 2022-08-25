<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

$classes = [];

$classes[] = 'notice';
$classes[] = 'notice-' . $type;
if (
	is_callable(
			$dismissable
	)
	? $dismissable()
	: $dismissable
) {
	$classes[] = 'is-dismissible';
}
$classes[] = 'bring-notice';
$classes[] = 'bring-notice-' . $key;
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-notice_id="<?php echo esc_attr( $key ); ?>">
	<?php foreach ( $messages as $message ) : ?>
		<?php // A message may contain HTML code. ?>
		<p><?php echo $message; // phpcs:ignore ?></p>
	<?php endforeach; ?>
</div>

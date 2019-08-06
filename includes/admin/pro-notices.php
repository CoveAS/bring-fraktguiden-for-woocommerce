<?php
/**
 * This file is part of Bring Fraktguiden for WooCommerce.
 *
 * @package Bring_Fraktguiden
 */

$classes = [];

$classes[] = 'notice';
$classes[] = 'notice-' . $type;
$classes[] = $dismissable ? 'is-dismissible' : '';
$classes[] = 'bring-notice';
$classes[] = 'bring-notice-' . $key;
?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-notice_id="<?php echo esc_attr( $key ); ?>">
	<?php foreach ( $messages as $message ) : ?>
	<p><?php echo esc_html( $message ); ?></p>
	<?php endforeach; ?>
</div>

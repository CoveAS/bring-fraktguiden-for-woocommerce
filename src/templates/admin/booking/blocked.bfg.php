<?php

use BringFraktguidenPro\Booking\Box\BookingBlock;

/**
 * The reason this shop cannot book, shown in place of the form.
 *
 * @var BookingBlock $block
 */
?>

<div class="bfg bfg-booking-box">
	<div class="bfg-notice-banner bfg-booking-notice">
		<?php require dirname(__DIR__) . '/parts/notice-icon.php'; ?>
		<div class="bfg-booking-notice__body">
			<p><?php echo esc_html($block->message); ?></p>
			<p><a class="bfg-btn bfg-btn--secondary bfg-btn--sm" href="<?php echo esc_url($block->url); ?>"><?php echo esc_html($block->link_text); ?></a></p>
		</div>
	</div>
</div>

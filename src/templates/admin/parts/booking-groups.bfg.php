<?php

/**
 * The groups of a bulk booking selection.
 *
 * The modal shows the groups before the worker sends the request, so nobody
 * has to read the result rows to learn which order was left alone.
 *
 * @var array<string, array<int, array{url: string, number: string}>> $booking_groups
 * @var string $booking_settings_url
 * @var bool   $booking_without_bring Whether the shop books an order without a Bring service.
 */

$booking_group_blocks = [
	'book' => [
		'title' => __('These orders get a booking', 'bring-fraktguiden-for-woocommerce'),
		'note'  => '',
	],
	'booked' => [
		'title' => __('These orders already hold a booking', 'bring-fraktguiden-for-woocommerce'),
		'note'  => __('Bring does not book them again. Their labels still print.', 'bring-fraktguiden-for-woocommerce'),
	],
	'status' => [
		'title' => __('These orders are in a status that allows no booking', 'bring-fraktguiden-for-woocommerce'),
		'note'  => sprintf(
			/* translators: %1$s and %2$s are the open and close tags of a link to the booking settings. */
			__('Choose the statuses that allow a booking in the %1$sbooking settings%2$s.', 'bring-fraktguiden-for-woocommerce'),
			'<a href="' . esc_url($booking_settings_url) . '" target="_blank" rel="noopener">',
			'</a>'
		),
	],
	'skipped' => [
		'title' => __('These orders get no booking', 'bring-fraktguiden-for-woocommerce'),
		'note'  => $booking_without_bring
			? __('They carry no shipping line at all, so Bring has nothing to book.', 'bring-fraktguiden-for-woocommerce')
			: sprintf(
				/* translators: %1$s and %2$s are the open and close tags of a link to the booking settings. */
				__('They carry no Bring shipping service. Turn on %1$sAllow booking without Bring shipping%2$s to book them anyway.', 'bring-fraktguiden-for-woocommerce'),
				'<a href="' . esc_url($booking_settings_url) . '" target="_blank" rel="noopener">',
				'</a>'
			),
	],
];
?>

<div class="bfg-booking-groups">
	<?php foreach ($booking_group_blocks as $booking_group_key => $booking_group_block) : ?>
		<?php if (empty($booking_groups[$booking_group_key])) : ?>
			<?php continue; ?>
		<?php endif; ?>
		<div class="bfg-booking-groups__group bfg-booking-groups__group--<?php echo esc_attr($booking_group_key); ?>">
			<p class="bfg-booking-groups__title">
				<strong><?php echo esc_html($booking_group_block['title']); ?></strong>
				<span class="bfg-booking-groups__count"><?php echo esc_html(count($booking_groups[$booking_group_key])); ?></span>
			</p>
			<ul class="bfg-booking-groups__orders">
				<?php foreach ($booking_groups[$booking_group_key] as $booking_group_order) : ?>
					<li>
						<a href="<?php echo esc_url($booking_group_order['url']); ?>" target="_blank" rel="noopener">
							<?php echo esc_html('#' . $booking_group_order['number']); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php if ($booking_group_block['note']) : ?>
				<p class="bfg-booking-groups__note"><?php echo wp_kses_post($booking_group_block['note']); ?></p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>

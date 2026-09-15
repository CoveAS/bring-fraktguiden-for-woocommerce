<?php

/**
 * The banner of a service that Bring carries inside one country only.
 *
 * The booking box shows the banner for the one order it books, and passes an
 * empty list. The bulk booking modal books many orders, so it passes the
 * orders it found and the banner names each one with its service.
 *
 * @var array<int, array{url: string, number: string, service: string}> $cross_border_orders
 */
?>

<div class="bfg-notice-banner bfg-booking-notice">
	<?php require __DIR__ . '/notice-icon.php'; ?>
	<div class="bfg-booking-notice__body">
		<?php if ($cross_border_orders) : ?>
			<p><strong><t>These orders leave the country on a service Bring carries inside one country only, so their booking fails. Pick a service that crosses the border.</t></strong></p>

			<div class="bfg-booking-notice__group">
				<ul>
					<?php foreach ($cross_border_orders as $cross_border_order) : ?>
						<li>
							<a href="<?php echo esc_url($cross_border_order['url']); ?>" target="_blank" rel="noopener">
								<?php echo esc_html('#' . $cross_border_order['number']); ?>
							</a>
							<?php echo esc_html($cross_border_order['service']); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php else : ?>
			<p><strong><t>This order leaves the country on a service Bring carries inside one country only, so the booking fails. Pick a service that crosses the border.</t></strong></p>
		<?php endif; ?>
	</div>
</div>

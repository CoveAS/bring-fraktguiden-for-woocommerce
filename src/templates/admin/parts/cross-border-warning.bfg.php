<?php

/**
 * The banner of a service that Bring carries inside one country only.
 *
 * The booking box shows the banner for the one order it books, and passes an
 * empty list. The bulk booking modal books many orders, so it passes the
 * orders it found and the banner names them.
 *
 * @var WC_Order[] $cross_border_orders
 */
?>

<div class="bfg-notice-banner bfg-booking-notice">
	<?php require __DIR__ . '/notice-icon.php'; ?>
	<div class="bfg-booking-notice__body">
		<?php if ($cross_border_orders) : ?>
			<p><strong><t>Bring carries some of these services inside one country only.</t></strong></p>
			<p><t>These orders go to another country, so their booking fails. Pick a service that crosses the border.</t></p>

			<div class="bfg-booking-notice__group">
				<p class="bfg-booking-notice__label"><t>Orders</t></p>
				<ul>
					<?php foreach ($cross_border_orders as $cross_border_order) : ?>
						<li>
							<a href="<?php echo esc_url($cross_border_order->get_edit_order_url()); ?>" target="_blank" rel="noopener">
								<?php echo esc_html('#' . $cross_border_order->get_order_number()); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php else : ?>
			<p><strong><t>Bring carries this service inside one country only.</t></strong></p>
			<p><t>The order goes to another country, so the booking fails. Pick a service that crosses the border.</t></p>
		<?php endif; ?>
	</div>
</div>

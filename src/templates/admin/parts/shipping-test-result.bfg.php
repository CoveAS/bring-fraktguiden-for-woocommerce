<?php

use BringFraktguiden\Admin\ShippingTestResult;

/**
 * What one shipping test found.
 *
 * @var ShippingTestResult $result
 */
?>

<?php if ($result->problem || ! $result->rates): ?>
	<p class="bfg-shipping-test__problem">
		<?php if ($result->problem): ?>
			<?php echo esc_html($result->problem); ?>
		<?php else: ?>
			<t>Bring gave no price for this parcel.</t>
		<?php endif; ?>
	</p>
	<?php if ($result->messages): ?>
		<ul class="bfg-shipping-test__messages">
			<?php foreach ($result->messages as $bfg_message): ?>
				<li><?php echo esc_html($bfg_message); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
<?php else: ?>
	<p class="bfg-shipping-test__pass">
		<?php printf(
			esc_html(_n(
				'Bring gave %d price. The checkout shows it.',
				'Bring gave %d prices. The checkout shows them.',
				count($result->rates),
				'bring-fraktguiden-for-woocommerce'
			)),
			count($result->rates)
		); ?>
	</p>
	<ul class="bfg-shipping-test__rates">
		<?php foreach ($result->rates as $bfg_rate): ?>
			<?php $bfg_date = $bfg_rate->get_meta_data()['expected_delivery_date'] ?? ''; ?>
			<li class="bfg-shipping-test__rate">
				<span class="bfg-shipping-test__rate-label"><?php echo esc_html($bfg_rate->get_label()); ?></span>
				<?php if ($bfg_date): ?>
					<span class="bfg-shipping-test__rate-date"><?php echo esc_html($bfg_date); ?></span>
				<?php endif; ?>
				<span class="bfg-shipping-test__rate-cost"><?php echo wp_kses_post(wc_price($bfg_rate->get_cost())); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php

use BringFraktguiden\Admin\ShippingTestResult;

/**
 * What one shipping test found.
 *
 * @var ShippingTestResult $result
 */

$bfg_passed = $result->rates && ! $result->problem;
?>

<div class="bfg-shipping-test__card <?php echo $bfg_passed ? 'bfg-shipping-test__card--pass' : 'bfg-shipping-test__card--problem'; ?>">
	<?php if (! $bfg_passed): ?>
		<p class="bfg-shipping-test__headline">
			<?php if ($result->problem): ?>
				<?php echo esc_html($result->problem); ?>
			<?php else: ?>
				<t>Bring gave no price for this parcel.</t>
			<?php endif; ?>
		</p>
	<?php endif; ?>

	<?php if ($result->messages): ?>
		<ul class="bfg-shipping-test__messages">
			<?php foreach ($result->messages as $bfg_message): ?>
				<li><?php echo esc_html($bfg_message); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ($bfg_passed): ?>
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
</div>

<?php if ($result->call): ?>
	<p class="bfg-shipping-test__raw-line">
		<button type="button" class="bfg-shipping-test__raw"><t>Show the API call</t></button>
	</p>

	<dialog class="bfg-modal bfg-shipping-test__dialog">
		<div class="bfg-modal__head">
			<h2 class="bfg-modal__title"><t>Shipping Guide API call</t></h2>
			<button type="button" class="bfg-modal__close" data-bfg-dialog-close aria-label="<?php esc_attr_e('Close', 'bring-fraktguiden-for-woocommerce'); ?>">&times;</button>
		</div>
		<div class="bfg-shipping-test__tabs" role="tablist">
			<button type="button" class="bfg-shipping-test__tab" role="tab" aria-selected="false"
				aria-controls="bfg-shipping-test-request"><t>Request</t></button>
			<button type="button" class="bfg-shipping-test__tab" role="tab" aria-selected="true"
				aria-controls="bfg-shipping-test-answer"><t>Response</t></button>
		</div>

		<div class="bfg-modal__body">
			<pre class="bfg-shipping-test__json" id="bfg-shipping-test-request" role="tabpanel" hidden><?php echo esc_html($result->json('request')); ?></pre>
			<pre class="bfg-shipping-test__json" id="bfg-shipping-test-answer" role="tabpanel"><?php echo esc_html($result->json('answer')); ?></pre>
		</div>
	</dialog>
<?php endif; ?>

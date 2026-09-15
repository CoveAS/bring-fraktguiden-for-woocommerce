<?php

use BringFraktguiden\Customs\CustomsRoute;
use BringFraktguiden\Customs\CustomsWarning;

/**
 * @var CustomsWarning $warning
 */

// A warning that holds nothing but the missing consent prints the callout only.
$bfg_has_rules = (bool) array_filter(
	$warning->groups,
	fn(array $group) => $group['lines'] || $group['shop_messages']
);
?>

<?php if ($warning->needs_consent) : ?>
	<?php require __DIR__ . '/customs-consent.php'; ?>
<?php endif; ?>

<?php if ($bfg_has_rules) : ?>
<div class="bfg-notice-banner bfg-booking-notice">
	<?php require __DIR__ . '/notice-icon.php'; ?>
	<div class="bfg-booking-notice__body">
		<?php foreach ($warning->groups as $group) : ?>
			<div class="bfg-booking-notice__rule">
				<?php if (CustomsRoute::EXPORT === $group['route']) : ?>
					<p><strong><t>The goods leave Norway, so Bring needs customs data.</t></strong></p>
				<?php else : ?>
					<p><strong><t>The goods pass through another country, so Bring needs transit data.</t></strong></p>
				<?php endif; ?>

				<?php foreach ($group['lines'] as $line) : ?>
					<div class="bfg-booking-notice__group">
						<p class="bfg-booking-notice__label">
							<?php if ($line['url']) : ?>
								<a href="<?php echo esc_url($line['url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($line['name']); ?></a>
							<?php else : ?>
								<?php echo esc_html($line['name']); ?>
							<?php endif; ?>
						</p>
						<ul>
							<?php foreach ($line['messages'] as $message) : ?>
								<li><?php echo esc_html($message); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endforeach; ?>

				<?php foreach ($group['shop_messages'] as $message) : ?>
					<p><?php echo wp_kses_post($message); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>

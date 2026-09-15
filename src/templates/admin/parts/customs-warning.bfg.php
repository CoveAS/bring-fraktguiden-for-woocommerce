<?php

use BringFraktguiden\Customs\CustomsRoute;
use BringFraktguiden\Customs\CustomsWarning;

/**
 * @var CustomsWarning $warning
 */
?>

<div class="bfg-notice-banner bfg-booking-notice">
	<?php require __DIR__ . '/notice-icon.php'; ?>
	<div class="bfg-booking-notice__body">
		<?php if (in_array(CustomsRoute::EXPORT, $warning->reasons, true)) : ?>
			<p><strong><t>The goods leave Norway, so Bring needs customs data.</t></strong></p>
		<?php endif; ?>

		<?php if (in_array(CustomsRoute::NVIT, $warning->reasons, true)) : ?>
			<p><strong><t>The goods pass through another country, so Bring needs transit data.</t></strong></p>
		<?php endif; ?>

		<?php foreach ($warning->lines as $line) : ?>
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

		<?php foreach ($warning->shop_messages as $message) : ?>
			<p><?php echo wp_kses_post($message); ?></p>
		<?php endforeach; ?>
	</div>
</div>

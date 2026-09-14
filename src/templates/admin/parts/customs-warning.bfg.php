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
				<p class="bfg-booking-notice__label"><?php echo esc_html($line['name']); ?></p>
				<ul>
					<?php foreach ($line['messages'] as $message) : ?>
						<li><?php echo esc_html($message); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>

		<?php if ($warning->shop_messages) : ?>
			<div class="bfg-booking-notice__group">
				<p class="bfg-booking-notice__label"><t>Shop settings</t></p>
				<ul>
					<?php foreach ($warning->shop_messages as $message) : ?>
						<li><?php echo esc_html($message); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</div>

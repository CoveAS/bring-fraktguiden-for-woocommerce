<?php

use BringFraktguiden\Customs\CustomsWarning;

/**
 * @var CustomsWarning $warning
 */
?>

<div class="bfg-notice-banner bfg-customs-warning">
	<span class="bfg-notice-icon">
		<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
	</span>
	<div class="bfg-customs-warning__body">
		<?php if (CustomsWarning::EXPORT === $warning->reason) : ?>
			<p><strong><t>This order leaves Norway, so Bring needs customs data.</t></strong></p>
		<?php else : ?>
			<p><strong><t>This order passes through another country, so Bring needs transit data.</t></strong></p>
		<?php endif; ?>

		<p><t>You can still book. Bring answers with the reason if it refuses the booking.</t></p>

		<?php foreach ($warning->lines as $line) : ?>
			<p class="bfg-customs-warning__line"><?php echo esc_html($line['name']); ?></p>
			<ul>
				<?php foreach ($line['messages'] as $message) : ?>
					<li><?php echo esc_html($message); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endforeach; ?>

		<?php if ($warning->shop_messages) : ?>
			<p class="bfg-customs-warning__line"><t>Shop settings</t></p>
			<ul>
				<?php foreach ($warning->shop_messages as $message) : ?>
					<li><?php echo esc_html($message); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>

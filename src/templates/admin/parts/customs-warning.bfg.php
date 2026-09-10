<?php

use BringFraktguiden\Customs\CustomsWarning;

/**
 * @var CustomsWarning $warning
 */
?>

<div class="bfg-notice-banner bfg-customs-warning">
	<span class="bfg-notice-icon">
		<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 7.5V10.8333M10 14.1667H10.0083M8.5747 2.68333L1.51637 14.1667C1.34889 14.4566 1.26025 14.7854 1.25932 15.1202C1.25838 15.4551 1.34518 15.7843 1.51103 16.0752C1.67688 16.366 1.91598 16.6083 2.20453 16.7781C2.49308 16.9479 2.82106 17.0392 3.15587 17.0429H16.8442C17.179 17.0392 17.507 16.9479 17.7955 16.7781C18.0841 16.6083 18.3232 16.366 18.489 16.0752C18.6549 15.7843 18.7417 15.4551 18.7407 15.1202C18.7398 14.7854 18.6512 14.4566 18.4837 14.1667L11.4254 2.68333C11.2544 2.40158 11.0136 2.16867 10.7263 2.00711C10.439 1.84555 10.1149 1.76068 9.78504 1.76068C9.45518 1.76068 9.13108 1.84555 8.84379 2.00711C8.5565 2.16867 8.31569 2.40158 8.1447 2.68333H8.5747Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
	</span>
	<div class="bfg-customs-warning__body">
		<?php if (CustomsWarning::EXPORT === $warning->reason) : ?>
			<p><strong><t>This order leaves Norway, so Bring needs customs data.</t></strong></p>
		<?php else : ?>
			<p><strong><t>This order passes through another country, so Bring needs transit data.</t></strong></p>
		<?php endif; ?>

		<?php foreach ($warning->lines as $line) : ?>
			<div class="bfg-customs-warning__group">
				<p class="bfg-customs-warning__label"><?php echo esc_html($line['name']); ?></p>
				<ul>
					<?php foreach ($line['messages'] as $message) : ?>
						<li><?php echo esc_html($message); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>

		<?php if ($warning->shop_messages) : ?>
			<div class="bfg-customs-warning__group">
				<p class="bfg-customs-warning__label"><t>Shop settings</t></p>
				<ul>
					<?php foreach ($warning->shop_messages as $message) : ?>
						<li><?php echo esc_html($message); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	</div>
</div>

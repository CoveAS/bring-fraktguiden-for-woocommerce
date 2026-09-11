<?php

use BringFraktguiden\Customs\CustomsWarningView;
use BringFraktguidenPro\Booking\Box\BookingForm;
use BringFraktguidenPro\Booking\Box\BookingRecord;

/**
 * @var BookingForm     $form
 * @var BookingRecord[] $records
 * @var bool            $showing_form
 * @var BookingRecord|null $last_failure
 * @var string          $error
 * @var array<int, array{name: string, image: string, code: string}> $hs_rows
 * @var string          $rest_url
 * @var string          $nonce
 */
?>

<div class="bfg-booking-box" data-bfg-booking data-url="<?php echo esc_url($rest_url); ?>" data-nonce="<?php echo esc_attr($nonce); ?>">

	<?php if ($error) : ?>
		<div class="bfg-notice-banner bfg-booking-box__error" role="alert">
			<p><?php echo esc_html($error); ?></p>
		</div>
	<?php endif; ?>

	<?php if ($showing_form && $last_failure) : ?>
		<div class="bfg-notice-banner bfg-booking-box__error" role="alert">
			<div>
				<p><strong><t>Bring refused the last booking</t></strong></p>
				<ul>
					<?php foreach ($last_failure->errors() as $message) : ?>
						<li><?php echo esc_html($message); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($hs_rows) : ?>
		<?php require dirname(__DIR__) . '/parts/customs-products.php'; ?>
	<?php endif; ?>

	<?php CustomsWarningView::render($warning); ?>

	<?php if ($showing_form) : ?>
		<?php require __DIR__ . '/form.php'; ?>
	<?php else : ?>
		<?php require __DIR__ . '/history.php'; ?>
	<?php endif; ?>

	<div class="bfg-booking-box__busy" data-bfg-busy hidden>
		<span><t>Working</t></span>
	</div>
</div>

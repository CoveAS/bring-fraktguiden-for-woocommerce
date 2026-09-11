<?php

use BringFraktguidenPro\Booking\Box\BookingRecord;

/**
 * Every booking attempt of the order, newest first.
 *
 * @var BookingRecord[] $records
 * @var string          $labels_url
 */
?>

<div class="bfg-booking-history">

	<?php foreach ($records as $bfg_index => $record) : ?>
		<div class="bfg-booking-history__entry <?php echo $record->failed() ? 'bfg-booking-history__entry--failed' : ''; ?>">

			<div class="bfg-booking-history__head">
				<strong>
					<?php if ($record->failed()) : ?>
						<t>Bring refused this booking</t>
					<?php elseif (0 === $bfg_index) : ?>
						<t>Booked with Bring</t>
					<?php else : ?>
						<t>Replaced by a later booking</t>
					<?php endif; ?>
				</strong>
				<?php if ($record->booked_at_local()) : ?>
					<span class="bfg-booking-history__when">
						<?php echo esc_html($record->booked_at_local()); ?>
						<?php if ($record->booked_by) : ?>
							&middot; <?php echo esc_html($record->booked_by); ?>
						<?php endif; ?>
					</span>
				<?php endif; ?>
			</div>

			<?php if ($record->failed()) : ?>
				<ul class="bfg-booking-history__errors">
					<?php foreach ($record->errors() as $message) : ?>
						<li><?php echo esc_html($message); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<ul class="bfg-booking-history__consignments">
					<?php foreach ($record->consignments() as $consignment) : ?>
						<li>
							<span class="bfg-booking-history__number"><?php echo esc_html($consignment->get_consignment_number()); ?></span>
							<a href="<?php echo esc_url($consignment->get_tracking_link()); ?>" target="_blank" rel="noreferrer"><t>Track</t></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if (0 === $bfg_index && !$record->failed()) : ?>
				<?php // ponytail: a label downloads through the order, so only the newest booking offers one. Per booking labels need the download screen to take a consignment number. ?>
				<a class="button" href="<?php echo esc_url($labels_url); ?>" target="_blank" rel="noreferrer"><t>Print the label</t></a>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<div class="bfg-booking-history__actions">
		<button type="button" class="button button-primary" data-bfg-again><t>Book again</t></button>
	</div>
</div>

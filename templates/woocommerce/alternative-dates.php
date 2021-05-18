<div class="bring-fraktguiden-date-options">
	<div class="bring-fraktguiden-date-options__inner">
		<?php if ( ! empty( $alternatives ) && ! empty( $earliest ) && ! empty( $range ) && ! empty( $selected ) ) : ?>
			<div class="bring-fraktguiden-date-options__description">
				<?php esc_html_e( 'Choose delivery from' ); ?>
				<?php echo esc_html( $earliest->date( 'l' ) ); ?>
			</div>

			<div class="alternative-date-range">
				<?php foreach ( $range as $key => $range_item ) : ?>
					<div class="alternative-date-range__item">
						<div class="alternative-date-range__day">
							<?php echo esc_html( $range_item['day'] ); ?>
						</div>
						<div class="alternative-date-range__date">
							<?php echo esc_html( $range_item['date'] ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="alternative-date-items">
				<?php foreach ( $alternatives as $time_slot_group ) : ?>
					<div class="alternative-date-items__row">
						<div class="alternative-date-items__label">
							<?php echo esc_html( $time_slot_group['label'] ); ?>
						</div>

						<?php foreach ( $range as $key => $range_item ) : ?>

							<?php $alternative = $time_slot_group['items'][ $key ] ?? false; ?>

							<?php $time_slot_id = $key . 'T' . $time_slot_group['id']; ?>

							<?php if ( $alternative ) : ?>
								<div
									data-time_slot="<?php echo esc_attr( $time_slot_id ); ?>"
									class="
										alternative-date-item
										alternative-date-item--choice
										<?php if ( $selected === $time_slot_id) : ?>
											alternative-date-item--chosen
										<?php endif; ?>
									"
								>
									<div class="">
										Velg
									</div>
								</div>
							<?php else: ?>
								<div class="alternative-date-item alternative-date-item--empty"></div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

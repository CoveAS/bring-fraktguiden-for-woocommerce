<div class="bring-fraktguiden-date-options">
	<div class="bring-fraktguiden-date-options__inner">
		<?php if ( ! empty( $alternatives ) && ! empty( $earliest ) && ! empty( $range ) && ! empty( $selected ) ) : ?>
			<div class="bring-fraktguiden-date-options__description">
				<?php esc_html_e( 'Choose delivery from', 'bring-fraktguiden-for-woocommerce' ); ?>
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
										<?php if ( $selected === $time_slot_id ) : ?>
											alternative-date-item--chosen
										<?php endif; ?>
									"
								>
									<div class="alternative-date-item__label">
										<?php esc_html_e( 'Select', 'bring-fraktguiden-for-woocommerce' ); ?>
									</div>
									<div class="alternative-date-item__checkmark">
										<svg xmlns="http://www.w3.org/2000/svg" width="78" height="57"
											 viewBox="0 0 78 57">
											<g>
												<polyline
														style="fill:none;stroke:#FFFFFF;stroke-width:10;stroke-dasharray:100px, 100px; stroke-dashoffset: 200px;"
														points="3.69,25.61 25.69,49.61 73.69,3.61"/>
											</g>
										</svg>
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

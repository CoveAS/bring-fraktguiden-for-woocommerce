<div class="bfg-field">
	<label for="booking_address_country">
		<?php esc_html_e('Country', 'bring-fraktguiden-for-woocommerce'); ?>
	</label>
	<div class="bfg-input bfg-input--select">
		<select id="booking_address_country" name="booking_address_country" class="bfg-custom-select">
			<?php foreach ($countries as $code => $name) {
		$selected = $code === $base_country ? ' selected' : '';
		echo '<option value="' . esc_attr($code) . '"' . $selected . '>' . esc_html($name) . '</option>';
	} ?>
		</select>
	</div>
</div>

<?php
/**
 * @var array $countries
 * @var string $base_country
 */
?>

<bfg-field.select id="booking_address_country" name="booking_address_country" label="Country">
	<?php foreach ($countries as $code => $name) {
		$selected = $code === $base_country ? ' selected' : '';
		echo '<option value="' . esc_attr($code) . '"' . $selected . '>' . esc_html($name) . '</option>';
	} ?>
</bfg-field.select>

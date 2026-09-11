<?php

/**
 * The two addresses the booking sends, as Bring will receive them.
 *
 * @var array|null $sender
 * @var array|null $recipient
 */

$bfg_rows = static function (?array $address): array {
	if (!$address) {
		return [];
	}

	return array_filter([
		__('Name', 'bring-fraktguiden-for-woocommerce')                  => $address['name'] ?? '',
		__('Street', 'bring-fraktguiden-for-woocommerce')                => $address['addressLine'] ?? '',
		__('Street, second line', 'bring-fraktguiden-for-woocommerce')   => $address['addressLine2'] ?? '',
		__('Postcode', 'bring-fraktguiden-for-woocommerce')              => $address['postalCode'] ?? '',
		__('City', 'bring-fraktguiden-for-woocommerce')                  => $address['city'] ?? '',
		__('Country', 'bring-fraktguiden-for-woocommerce')               => $address['countryCode'] ?? '',
		__('Reference', 'bring-fraktguiden-for-woocommerce')             => $address['reference'] ?? '',
		__('Extra address line', 'bring-fraktguiden-for-woocommerce')    => $address['additionalAddressInfo'] ?? '',
		__('Contact', 'bring-fraktguiden-for-woocommerce')               => $address['contact']['name'] ?? ($address['name'] ?? ''),
		__('Email', 'bring-fraktguiden-for-woocommerce')                 => $address['contact']['email'] ?? '',
		__('Telephone', 'bring-fraktguiden-for-woocommerce')             => $address['contact']['phoneNumber'] ?? '',
	]);
};
?>

<div class="bfg-booking-parties">
	<div>
		<h4><t>Sender</t></h4>
		<table class="bfg-booking-parties__table">
			<?php foreach ($bfg_rows($sender) as $label => $value) : ?>
				<tr>
					<th scope="row"><?php echo esc_html($label); ?></th>
					<td><?php echo esc_html($value); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
	<div>
		<h4><t>Recipient</t></h4>
		<table class="bfg-booking-parties__table">
			<?php foreach ($bfg_rows($recipient) as $label => $value) : ?>
				<tr>
					<th scope="row"><?php echo esc_html($label); ?></th>
					<td><?php echo esc_html($value); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>

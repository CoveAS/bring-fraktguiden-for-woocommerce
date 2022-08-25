const form = document.getElementById('mainform')
const inputs = [
	document.getElementById('woocommerce_bring_fraktguiden_booking_address_store_name'),
	document.getElementById('woocommerce_bring_fraktguiden_booking_address_street1'),
	document.getElementById('woocommerce_bring_fraktguiden_booking_address_postcode'),
	document.getElementById('woocommerce_bring_fraktguiden_booking_address_city'),
	document.getElementById('woocommerce_bring_fraktguiden_booking_address_contact_person'),
	document.getElementById('woocommerce_bring_fraktguiden_booking_address_phone'),
	document.getElementById('woocommerce_bring_fraktguiden_booking_address_email'),
]
const bookingStatus = document.getElementById('woocommerce_bring_fraktguiden_booking_enabled')

form.addEventListener('submit', e => {
	if (!bookingStatus.checked) {
		return;
	}
	let input;
	let error = false;

	for (var i = 0; i < inputs.length; i++) {
		input = inputs[i]
		if (input.value) {
			continue;
		}
		e.preventDefault()
		input.style.borderColor = "red";
		error = true;
	}

	if (error) {
		jQuery('.fraktguiden-options').hashTabs('triggerTab', 'woocommerce_bring_fraktguiden_booking_title');
		jQuery([document.documentElement, document.body]).animate({
			scrollTop: jQuery("#woocommerce_bring_fraktguiden_booking_address_section_title").offset().top - 80
		}, 2000);
	}
})

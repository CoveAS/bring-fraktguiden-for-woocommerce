import { isValid } from 'swedish-postal-code-validator';

const form = document.querySelector('#mainform')
const inputs = [...document.querySelectorAll('input[id^=woocommerce_bring_fraktguiden_booking_address]')]
const bookingStatus = document.querySelector('#woocommerce_bring_fraktguiden_booking_enabled')

form.addEventListener('submit', e => {
		if ( bookingStatus.checked ) {
		var input;
	
		for ( var i = 0; i < inputs.length; i++ ) {
			input = inputs[i]
			if ( !input.value && i !== 2 ) {
				e.preventDefault()
				input.style.borderColor = "red";
			}
		}
	}
})

jQuery("#woocommerce_bring_fraktguiden_booking_address_postcode").on('keyup', function() {
	const countryInput = jQuery("#select2-woocommerce_bring_fraktguiden_booking_address_country-container").text()
	if ( countryInput === "Norway" ) {
		let isValidZip = /^\d{4}$/.test(this.value)
		if (isValidZip) {
			this.style.borderColor = "green";
		} else {
			this.style.borderColor = "red";
		}
	}
	else if ( countryInput === "Sweden" ) {
		if ( isValid(this.value) ) {
			this.style.borderColor = "green";
		} else {
			this.style.borderColor = "red";
		}
	}
	else if ( countryInput === "Denmak" ) {
		let isValidZip = /^(?:[1-24-9]\d{3}|3[0-8]\d{2})$/.test(this.value)
		if (isValidZip) {
			this.style.borderColor = "green";
		} else {
			this.style.borderColor = "red";
		}
	}
})

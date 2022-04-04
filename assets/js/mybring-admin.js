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
		let isValidZip = /^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$/.test(this.value)
		if (isValidZip && this.value > 11114 && this.value < 98500) {
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

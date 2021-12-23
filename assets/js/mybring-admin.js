const form = document.querySelector('#mainform')
const inputs = [...document.querySelectorAll('input[id^=woocommerce_bring_fraktguiden_booking_address]')]

form.addEventListener('submit', e => {
	var input;

	for ( var i = 0; i < inputs.length; i++ ) {
		input = inputs[i]
		if ( !input.value && i !== 2 ) {
			e.preventDefault()
			input.style.borderColor = "red";
		}
	}

})
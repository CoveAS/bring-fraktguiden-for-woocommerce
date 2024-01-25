import Packages from './components/Booking/Packages';
import {createApp} from 'vue';
import 'vue-select/dist/vue-select.css';

const booking = createApp(Packages);
booking.mount('#bring-fraktguiden-booking-packages');

function booking_step(step) {
	const currentUrl = new URL(window.location.href);
	const searchParams = currentUrl.searchParams;
	searchParams.set('booking_step', step);
	window.location.href = currentUrl.toString();
}
jQuery('[name="_bring-start-booking"]').on(
	'click',
	function (e) {
		e.preventDefault();
		booking_step('2');
	}
);
jQuery('[name="_bring-start-booking"]').on(
	'keydown',
	function (e) {
		if (e.keyCode !== 13 && e.keyCode !== 32) {
			return;
		}
		e.preventDefault();
		booking_step('2');
	}
);

import Packages from './components/Booking/Packages.vue';
import Vue from 'vue';

const booking = new Vue( {
	el: '#bring-fraktguiden-booking-packages',
	render: (createElement) => {
		return createElement( Packages )
	}
} );

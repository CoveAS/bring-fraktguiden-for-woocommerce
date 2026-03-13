import ShippingProduct from './components/shipping-product.vue';
import {createApp, ref} from 'vue';
import './mybring-api-validation.js';
import '../css/tailwind.css';

console.log('Settings script loaded');
console.log('bring_fraktguiden_settings exists:', !!window.bring_fraktguiden_settings);
console.log('bring_fraktguiden_settings data:', window.bring_fraktguiden_settings);

if ( window.bring_fraktguiden_settings ) {

	const selected = ref(bring_fraktguiden_settings.services_enabled);

	const settings = createApp( {
		setup() {
			return {
				selected: selected,
				services_data: bring_fraktguiden_settings.services_data,
				pro_activated: bring_fraktguiden_settings.pro_activated,
			};
		},
		computed: {
			services: function() {
				const services = [];
				let id, service;
				for (let i = 0; i < this.selected.length; i++) {
					id = this.selected[i];
					service = bring_fraktguiden_settings.services[id];
					services.push( service );
				}
				return services;
			}
		},
		components: {
			shippingproduct: ShippingProduct,
		},
	} );
	console.log('Vue app created, about to mount to #shipping_services');
	const mountTarget = document.getElementById('shipping_services');
	console.log('Mount target element:', mountTarget);
	settings.mount('#shipping_services');
	console.log('Vue app mounted successfully');

	jQuery( function( $ ) {
		$( '#shipping_services .select2' ).select2().on( 'change select2:clear', function( e ) {
			const values = $(this).val();
			while(selected.value.length > 0) {selected.value.pop();}
			if (! values) {
				return;
			}
			for (let i = 0; i < values.length; i++) {
				selected.value.push( values[i] );
			}
		} );
	} );
}


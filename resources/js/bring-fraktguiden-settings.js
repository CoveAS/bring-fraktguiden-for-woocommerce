import ShippingProduct from './components/shipping-product.vue';
import {createApp, ref} from 'vue';

if ( window.shipping_services && window.bring_fraktguiden_settings ) {

	const pro_activated = ref(bring_fraktguiden_settings.pro_activated);
	bring_fraktguiden_settings.pro_activated = pro_activated;
	const selected = ref(bring_fraktguiden_settings.services_enabled);

	const settings = createApp( {
		data() {
			return {
				selected: selected,
				services_data: bring_fraktguiden_settings.services_data,
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
	settings.config.globalProperties.pro_activated = pro_activated;
	settings.mount('#shipping_services');

	require( './mybring-api-validation.js' );

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


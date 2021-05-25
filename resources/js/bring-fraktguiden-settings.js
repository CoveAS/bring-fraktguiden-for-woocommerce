import ShippingProduct from './components/shipping-product.vue';
import Vue from 'vue';

if ( window.shipping_services && window.bring_fraktguiden_settings ) {

	window.Vue = Vue;

	var selected = bring_fraktguiden_settings.services_enabled;

	var settings = new Vue( {
		el: '#shipping_services',
		data: {
			selected: selected,
			services_data: bring_fraktguiden_settings.services_data,
			pro_activated: bring_fraktguiden_settings.pro_activated,
		},
		computed: {
			services: function() {
				var services = [];
				for (var i = 0; i < this.selected.length; i++) {
					var id = this.selected[i];
					var service = bring_fraktguiden_settings.services[id];
					services.push( service );
				}
				return services;
			}
		},
		components: {
			shippingproduct: ShippingProduct,
		},
	} );

	require( './mybring-api-validation.js' );

	Object.defineProperty(
		bring_fraktguiden_settings,
		'pro_activated',
		{
			get: function() {
				return settings.$root.pro_activated;
			},
			set: function( val ) {
				settings.$root.pro_activated = val;
			}
		}
	);

	jQuery( function( $ ) {
		$( '#shipping_services .select2' ).select2().on( 'change select2:clear', function( e ) {
			var values = $(this).val();
			while(selected.length > 0) {selected.pop();}
			if (! values) {
				return;
			}
			for (var i = 0; i < values.length; i++) {
				selected.push( values[i] );
			}
		} );
	} );

}


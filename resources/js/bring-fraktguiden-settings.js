import ShippingProduct from './components/shipping-product';
import TextValidator from './components/text-validator';
window.Vue = require( 'vue' );


if ( window.shipping_services ) {

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

	var api_key = new Vue( {
		el: '#woocommerce_bring_fraktguiden_mybring_api_key',
		render: function( createElement ) {
			var elem = createElement( TextValidator, {
				props: {
					original_el: this.$el,
					validator: function() {

					}
				}
			} );
			return elem;
		},
	} );

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
			selected.length = 0;
			if (! values) {
				return;
			}
			for (var i = 0; i < values.length; i++) {
				selected.push( values[i] );
			}
		} );
	} );

}


import ShippingProduct from './components/shipping-product';
window.Vue = require( 'vue' );

var selected = bring_fraktguiden_settings.services_enabled;

console.log('')
var settings = new Vue( {
	el: '#shipping_services',
	data: {
		selected: selected,
		services_data: bring_fraktguiden_settings.services_data,
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

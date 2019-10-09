<template>
	<div class="fraktguiden-product">
		<header :class="classes">
			<h3 v-html="service_data.productName"></h3>
			<p v-if="service_data.description" v-html="service_data.description"></p>
		</header>
		<div class="fraktguiden-product__fields">
			<label>
				<span>Shipping name:</span>
				<input
					:placeholder="service_data.productName"
					type="text"
					v-model="custom_name"
					:name="custom_name_id"
				>
			</label>
			<overridetoggle
				:checkbox_id="custom_price_cb_id"
				:checkbox_val="custom_price_cb"
				input_type="number"
				:field_id="custom_price_id"
				:field_val="custom_price"
			>
				Fixed price override:
			</overridetoggle>
			<overridetoggle
				:checkbox_id="customer_number_cb_id"
				:checkbox_val="customer_number_cb"
				input_type="text"
				:field_id="customer_number_id"
				:field_val="customer_number"
			>
				Alternative customer number:
			</overridetoggle>
			<overridetoggle
				:checkbox_id="free_shipping_id"
				:checkbox_val="free_shipping"
				input_type="number"
				:field_id="free_shipping_threshold_id"
				:field_val="free_shipping_threshold"
			>
				Free shipping override:
			</overridetoggle>
		</div>
	</div>
</template>
<style lang="scss">
.fraktguiden-product {
	&,* {
		box-sizing: border-box;
	}
	border: 1px solid #e1e1e1;
	background-color: #f9f9f9;
	margin-bottom: 1rem;
	margin-top: 1rem;
	header {
		&.warning {
			border-left: 3px solid #c00;
		}
		background-color: #fff;
		padding: 1rem;
		border-bottom: 1px solid #e1e1e1;
		h3 {
			margin: 0;
		}
	}
	&__fields {
		display: flex;
		flex-wrap: wrap;
		padding: 0.5rem;
		label {
			min-width: 50%;
			max-width: 100%;
			padding: 0.5rem;
			display: flex;
			flex: 1 0 auto;
		    align-items: center
		}
		span {
			flex: 0 0 10rem;
		}
		#shipping_services & {
			input[type="text"] {
				max-width: 20rem;
			}
			input[type="number"] {
				max-width: 100%;
				text-align: right;
			}
		}
	}
}
</style>
<script>
import OverrideToggle from './override-toggle';
export default {
	props: [
		'enabled',
		'custom_name',
		'custom_name_id',
		'custom_price',
		'custom_price_id',
		'customer_number',
		'customer_number_id',
		'free_shipping',
		'free_shipping_id',
		'free_shipping_threshold',
		'free_shipping_threshold_id',
		'service_data',
		'id',
	],
	data: function() {
		return {
			custom_price_cb: false,
			custom_price_cb_id: false,
		};
	},
	computed: {
		classes: function() {
			if ( ! this.service_data.class ) {
				return '';
			}
			return this.service_data.class;
		},
	},
	components: {
		overridetoggle: OverrideToggle
	},
	mounted: function() {
		if ( this.custom_price ) {
			this.custom_price_cb = true;
		}
	}
};
</script>

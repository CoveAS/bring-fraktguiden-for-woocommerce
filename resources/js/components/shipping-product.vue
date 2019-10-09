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
					:name="name_prefix + '[custom_name]'"
				>
			</label>
			<overridetoggle
				field_id="custom_price"
				:checkbox_val="custom_price_cb"
				input_type="number"
				:field_val="custom_price"
				:name_prefix="name_prefix"
			>
				Fixed price override:
			</overridetoggle>
			<overridetoggle
				field_id="customer_number"
				:checkbox_val="customer_number_cb"
				input_type="text"
				:field_val="customer_number"
				:name_prefix="name_prefix"
			>
				Alternative customer number:
			</overridetoggle>
			<overridetoggle
				field_id="free_shipping"
				:checkbox_val="free_shipping_cb"
				input_type="number"
				:field_val="free_shipping"
				:name_prefix="name_prefix"
			>
				Free shipping activated at:
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
			min-width: 25rem;
			max-width: 100%;
			padding: 0.5rem;
			display: flex;
			flex: 1 0 50%;
		    align-items: center;
		    @media (max-width: 32em) {
				min-width: 15rem;
			    flex-wrap: wrap;
		    }
		}
		span {
			flex: 0 0 14rem;
		}
		#shipping_services & {
			input[type="number"],
			input[type="text"] {
			    width: 100%;
			}
			input[type="text"] {
			}
			input[type="number"] {
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
		'bring_product',
		'option_key',
		'custom_name',
		'custom_price',
		'custom_price_cb',
		'customer_number',
		'customer_number_cb',
		'free_shipping',
		'free_shipping_cb',
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
		name_prefix: function() {
			return this.option_key + '[' + this.bring_product + ']';
		}
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

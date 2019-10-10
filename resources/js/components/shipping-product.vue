<template>
	<div class="fraktguiden-product">
		<header :class="classes">
			<h3 v-html="service_data.productName"></h3>
			<p class="warning" v-if="service_data.warning" v-html="service_data.warning"></p>
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
					:readonly="! pro_activated"
				>
			</label>
			<overridetoggle
				field_id="custom_price"
				:obj="this"
				input_type="number"
				:name_prefix="name_prefix"
			>
				Fixed price override:
			</overridetoggle>
			<overridetoggle
				field_id="customer_number"
				:obj="this"
				input_type="text"
				:name_prefix="name_prefix"
			>
				Alternative customer number:
			</overridetoggle>
			<overridetoggle
				field_id="free_shipping"
				:obj="this"
				input_type="number"
				:name_prefix="name_prefix"
			>
				Free shipping activated at:
			</overridetoggle>
			<overridetoggle
				field_id="additional_fee"
				:obj="this"
				input_type="number"
				:name_prefix="name_prefix"
			>
				Additional fee:
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
	p.warning {
		font-weight: 600;
		color: #d20e0e;
	}
	&__fields {
		display: flex;
		flex-wrap: wrap;
		padding: 0.5rem;
		label {
			position: relative;
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

			.pro-disabled &::after {
				content: 'Pro only';
				position: absolute;
				display: block;
				top: 0;
				right: 0;
				color: #C00;
				background-color: #fff;
				border-radius: 5px;
				padding: 0.25rem 0.5rem;
				opacity: 0.8;
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
		'service',
		'service_data',
		'id',
	],
	data: function() {
		return {
			custom_name        : this.service.custom_name,
			custom_price       : this.service.custom_price,
			custom_price_cb    : this.service.custom_price_cb,
			customer_number    : this.service.customer_number,
			customer_number_cb : this.service.customer_number_cb,
			free_shipping      : this.service.free_shipping,
			free_shipping_cb   : this.service.free_shipping_cb,
			additional_fee     : this.service.additional_fee,
			additional_fee_cb  : this.service.additional_fee_cb,
			name_prefix        : this.service.option_key + '[' + this.service.bring_product + ']',
		};
	},
	computed: {
		classes: function() {
			if ( ! this.service_data.class ) {
				return '';
			}
			return this.service_data.class;
		},
		pro_activated: function() {
			return this.$root.pro_activated;
		},
	},
	components: {
		overridetoggle: OverrideToggle
	},
	mounted: function() {
	}
};
</script>

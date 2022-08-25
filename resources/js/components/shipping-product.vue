<template>
	<div class="fraktguiden-product">
		<header :class="classes">
			<h3>
				<span class="fraktguiden-product__name" v-html="service_data.productName"></span>
				<span class="fraktguiden-product__id" v-html="service.bring_product"></span>
			</h3>
			<p class="warning" v-if="service_data.warning" v-html="service_data.warning"></p>
			<p v-if="service_data.description" v-html="service_data.description"></p>
		</header>
		<div class="fraktguiden-product__fields">
			<label>
				<span v-html="i18n.shipping_name"></span>
				<input
					:placeholder="service_data.productName"
					type="text"
					v-model="custom_name"
					:name="name_prefix + '[custom_name]'"
					:readonly="! pro_activated"
				>
			</label>
			<overridetoggle
				:label="i18n.fixed_price_override"
				field_id="custom_price"
				:obj="this"
				input_type="number"
				:name_prefix="name_prefix"
			>
			</overridetoggle>
			<overridetoggle
				field_id="customer_number"
				:label="i18n.alternative_customer_number"
				:obj="this"
				input_type="text"
				:name_prefix="name_prefix"
				:validation="validate_customer_number"
			>
			</overridetoggle>
			<overridetoggle
				:label="i18n.free_shipping_activated_at"
				field_id="free_shipping"
				:obj="this"
				input_type="number"
				:name_prefix="name_prefix"
			>
			</overridetoggle>
			<overridetoggle
				:label="i18n.additional_fee"
				field_id="additional_fee"
				:obj="this"
				input_type="number"
				:name_prefix="name_prefix"
			>
			</overridetoggle>
			<overridetoggle
				v-if="service_data.pickuppoint"
				:label="i18n.pickup_point"
				field_id="pickup_point"
				placeholder="0"
				step="1"
				:obj="this"
				input_type="number"
				:name_prefix="name_prefix"
			>
			</overridetoggle>
		</div>
		<div class="fraktguiden-product__vas" v-if="vas.length">
			<h4 v-html="i18n.value_added_services"></h4>
			<div class="vas-checkboxes">
				<component
					:is="service.vue_component"
					:name_prefix="name_prefix"
					:label="service.name"
					:code="service.code"
					:enabled="service.enabled"
					:checked="service.value"
					v-for="service in vas"
					:key="service.code"
				></component>
			</div>
		</div>
		<footer>
			<ul class="validation-errors" v-show="validation_errors.length">
				<li
					class="validation-errors__error"
					v-for="validation_error in validation_errors"
					v-html="validation_error.message"
				></li>
			</ul>
		</footer>
	</div>
</template>

<style lang="scss">
.fraktguiden-product {
	.validation-errors {
		margin: 0;
		background-color: #ffecc8;
		color: darken(#ffecc8, 60%);
		padding: 1rem;
		border-top: 1px solid #e1e1e1;
		&__error {
			margin: 0;
		}
	}
	&,* {
		box-sizing: border-box;
	}
	border: 1px solid #e1e1e1;
	background-color: #f9f9f9;
	margin-bottom: 1rem;
	margin-top: 1rem;
	h4, h3 {
		margin: 0;
	}
	header {
		&.warning {
			border-left: 3px solid #c00;
		}
		background-color: #fff;
		padding: 1rem;
		border-bottom: 1px solid #e1e1e1;
	}
	p.warning {
		font-weight: 600;
		color: #d20e0e;
	}
	&__id {
		float: right;
	}
	&__vas {
		border-top: 1px solid #e1e1e1;
		h4 {
			padding: 1rem 1rem 0.5rem 1rem;
		}
	}
	.vas-checkboxes {
		padding: 0 1rem 1rem 1rem;
		.checkbox {
			margin-right: 0.5rem;
		}
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
import Checkbox from './checkbox';
export default {
	props: [
		'vas',
		'service',
		'service_data',
		'id',
	],
	data: function() {
		return {
			i18n               : bring_fraktguiden_settings.i18n,
			pickup_point       : this.service.settings.pickup_point,
			pickup_point_cb    : this.service.settings.pickup_point_cb,
			custom_name        : this.service.settings.custom_name,
			custom_price       : this.service.settings.custom_price,
			custom_price_cb    : this.service.settings.custom_price_cb,
			customer_number    : this.service.settings.customer_number,
			customer_number_cb : this.service.settings.customer_number_cb,
			free_shipping      : this.service.settings.free_shipping,
			free_shipping_cb   : this.service.settings.free_shipping_cb,
			additional_fee     : this.service.settings.additional_fee,
			additional_fee_cb  : this.service.settings.additional_fee_cb,
			name_prefix        : this.service.option_key + '[' + this.service.bring_product + ']',
			validation_errors  : [],
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
	methods: {
		validate_customer_number: function( value, checkbox_value ) {
			for (var i = 0; i < this.validation_errors.length; i++) {
				if ( this.validation_errors[i] && 'customer_number' === this.validation_errors[i].id ) {
					this.validation_errors.splice( i, 1 );
				}
			}
			if ( typeof checkbox_value !== 'undefined' && ! checkbox_value ) {
				return true;
			}
			if ( value && ! value.match( /^[A-Za-z_]+\-\d+$/ ) && ! value.match( /^\d{6,}$/ ) ) {
				this.validation_errors.push( {
					id: 'customer_number',
					message: this.i18n.error_customer_number,
				} );
				return false;
			}
			return true;
		}
	},
	components: {
		overridetoggle: OverrideToggle,
		checkbox: Checkbox
	},
	mounted: function() {
		this.validate_customer_number( this.customer_number, this.customer_number_cb );
	}
};
</script>

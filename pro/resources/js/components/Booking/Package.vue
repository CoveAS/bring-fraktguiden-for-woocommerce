<template>

	<tr>
		<td :title="i18n.tip">
			<select class="order-item-id" name="order_item_id[]">
				<option
						v-for="id in orderItemIds"
						:value="id"
						:selected="id === package.id"
				>{{ id }}
				</option>
			</select>
		</td>
		<td>
			<v-select
					class="booking_shipping_service"
					name="booking_shipping_service[]"
					:options="services"
					v-model="package.serviceData.productName"
			>
			</v-select>
			<span
					v-if="package.pickupPoint"
					class="tips"
					:data-tip="package.pickupPoint.replace( '|', '<br/>')"
					v-html="i18n.pickupPoint"
			></span>
		</td>
		<td>
			<input
					name="width[]"
					class="dimension"
					type="number"
					v-model="package.dimensions.widthInCm"
			>
		</td>
		<td>
			<input
					name="height[]"
					class="dimension"
					type="number"
					v-model="package.dimensions.heightInCm"
			>
		</td>
		<td>
			<input
					name="length[]"
					class="dimension"
					type="number"
					v-model="package.dimensions.lengthInCm"
			>
		</td>
		<td>
			<input
					name="weight[]"
					class="dimension"
					type="number"
					step=".01"
					min="0"
					v-model="package.weightInKg"
			>
		</td>
		<td></td>
		<td>
			<span
			  v-if="removable"
			  class="button-link button-link-delete delete"
			  v-on:click="$emit('remove')"
			  v-html="i18n.delete"
	  ></span>
		</td>
	</tr>
</template>

<style>

.bring-booking-packages-form input.vs__search {
	border: none;
}

.bring-booking-packages-form .vs__selected {
	white-space: pre;
}

.bring-booking-packages-form .vs__selected-options {
	flex-wrap:nowrap;
}

.bring-booking-packages-form .dimension {
	width: 5rem;
}
</style>

<script>

import vSelect from 'vue-select'

export default {
	data() {
		return window.bring_fraktguiden_booking;
	},
	computed: {},
	props: {
		removable: {
			type: Boolean,
			required: true,
		},
		package: {
			type: Object,
			required: true
		}
	},
	components: {
		vSelect
	}
}
</script>

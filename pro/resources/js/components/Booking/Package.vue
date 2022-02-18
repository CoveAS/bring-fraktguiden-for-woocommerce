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
			{{ package.serviceData.productName }}
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

<script>
export default {
	data() {
		return booking_packages;
	},
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
	mounted() {
		console.log('Package:');
		console.log(this.package);
	},
	methods: {}
}
</script>

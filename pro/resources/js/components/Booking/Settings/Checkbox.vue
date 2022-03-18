<template>
	<div class="bfwc-flex bfwc-items-center">
		<input
				type="checkbox"
				:id="'booking_' + name"
				:name="name"
				:checked="checked"
		>
		<label :for="'booking_' + name" v-html="label"></label>
		<div class="bfwc-relative bring-booking__tooltip">
			<information-circle-icon class="bring-booking__info"/>
			<div
					class="bring-booking__tooltip__reveal"
					v-html="description"
			></div>
		</div>
	</div>
</template>

<script>
import {InformationCircleIcon} from '@heroicons/vue/outline';

export default {
	name: "Checkbox",
	props: {
		label: {
			required: true,
			type: String,
		},
		name: {
			required: true,
			type: String,
		},
		description: {
			required: true,
			type: String,
		},
		checked: {
			required: true,
			type: Boolean,
		}
	},
	components: {
		InformationCircleIcon
	}
}
</script>

<style scoped>

.bring-booking__tooltip {
	justify-self: end;
}

.bring-booking__tooltip:hover .bring-booking__tooltip__reveal {
	display: block;
}

.bring-booking__tooltip__reveal {
	position: absolute;
	bottom: 2rem;
	width: 16rem;
	left: -8rem;
	background-color: #fff;
	color: #0a4b78;
	border: 1px solid #0a4b78;
	padding: 0.5rem 1rem;
	border-radius: 0.25rem;
	display: none;
	box-shadow: 0 1px 10px rgba(0, 0, 0, 0.2);
	z-index: 2;
}

.bring-booking__info {
	width: 1.5rem;
	height: 1.5rem;
	color: #2271b1;
	margin-left: 0.25rem;
}

.bfwc-relative {
	position: relative;
}

.bfwc-flex {
	display: flex;
}

.bfwc-items-center {
	align-items: center;
}
</style>

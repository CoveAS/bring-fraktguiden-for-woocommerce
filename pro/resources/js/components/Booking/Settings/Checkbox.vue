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
			<span class="bring-booking__info">i</span>
			<div
					class="bring-booking__tooltip__reveal"
					v-html="description"
			></div>
		</div>
	</div>
</template>

<script>
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
	padding: 1rem 2rem;
	display: none;
}

.bring-booking__info {
	border: 2px solid #0a4b78;
	color: #0a4b78;
	display: block;
	width: 1rem;
	height: 1rem;
	font-weight: bold;
	border-radius: 1rem;
	text-align: center;
	margin-left: 1rem;
	position: relative;
	top: -2px;
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

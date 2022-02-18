<template>
<div>
				<strong>Booking settings:</strong>
		<ul class="">
			<li class="flex items-center">
				<input type="checkbox" id="booking_bag_on_door" name="bag_on_door">
				<label for="booking_bag_on_door">Use bag on door for mailbox packages</label>
				<div class="relative bring-booking__tooltip">
					<span class="bring-booking__info">i</span>
					<div class="bring-booking__tooltip__reveal">
						Lorem ipsum dolor ...
					</div>
				</div>
			</li>
			<li class="flex items-center">
				<input type="checkbox" id="booking_personal_delivery" name="personal_delivery">
				<label for="booking_personal_delivery">Use personal delivery for pickup parcels</label>
				<div class="relative bring-booking__tooltip">
					<span class="bring-booking__info">i</span>
					<div class="bring-booking__tooltip__reveal">
						Lorem ipsum dolor ...
					</div>
				</div>
			</li>
		</ul>
</div>
</template>

<script>
export default {
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

.relative {
	position: relative;
}

.flex {
	display: flex;
}

.items-center {
	align-items: center;
}
</style>

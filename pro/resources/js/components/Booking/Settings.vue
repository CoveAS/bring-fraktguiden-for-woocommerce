<template>
	<div>
		<strong>Booking settings:</strong>
		<ul class="">
			<li>
				<checkbox
						:label="i18n.bag_on_door"
						:description="i18n.bag_on_door_description"
						:checked="checked['1081']"
						name="bag_on_door"
				></checkbox>
			</li>
			<li>
				<checkbox
						:label="i18n.individual_verification"
						:description="i18n.individual_verification_description"
						:checked="checked['1134']"
						name="individual_verification"
				></checkbox>
			</li>
			<li>
				<checkbox
						:label="i18n.id_verification"
						:description="i18n.id_verification_description"
						:checked="checked['1133']"
						name="id_verification"
				></checkbox>
			</li>
		</ul>
	</div>
</template>

<script>
import Checkbox from "./Settings/Checkbox";

export default {
	data() {
		return {
			i18n: window.bring_fraktguiden_booking.i18n,
			checked: {
				1081: true,
				1133: true,
				1134: true,
			},
		}
	},
	components: {Checkbox}
}
</script>

<style scoped>
</style>

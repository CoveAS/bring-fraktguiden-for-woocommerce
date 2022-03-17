<template>
	<div>
		<strong>Booking settings:</strong>
		<ul class="">
			<li>
				<checkbox
						label="Bag on door (mailbox)"
						description="Mailbox Parcel (Pakke i postkassen) is a parcel that will be delivered in the recipient’s mailbox. If the parcel for various reasons does not fit in the mailbox, the sender may, against a surcharge, choose to leave the parcel on the door handle (in a special bag) to avoid it being sent to the pickup point. It’s recommended that this delivery option is actively confirmed by the receiver upon booking in the sender’s webshop. When the parcel is delivered as a bag on the door, the bar code is scanned and the recipient will receive an SMS/email. Note that if the parcel is delivered in the mailbox the additional fee will not occur."
						:checked="checked['1081']"
						name="bag_on_door"
				></checkbox>
			</li>
			<li>
				<checkbox
						description="Only the specified recipient can receive the shipment by showing identification. Use of authorization is not possible."
						label="Individual verification"
						:checked="checked['1134']"
						name="individual_verification"
				></checkbox>
			</li>
			<li>
				<checkbox
						description="ID is checked upon delivery. Any person (other than the recipient) can receive the shipment, but must legitimize before receiving it."
						label="ID verification"
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
			checked: {
				bag_on_door: true,
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

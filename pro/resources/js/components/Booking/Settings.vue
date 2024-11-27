<template>
	<div>
		<strong>Booking settings:</strong>
		<ul class="">
			<li v-show="showVas('1081')">
				<checkbox
						:label="i18n.bag_on_door"
						:description="i18n.bag_on_door_description"
						:checked="checked('1081')"
						name="bag_on_door"
				></checkbox>
			</li>
			<li v-show="showVas('1280')">
				<checkbox
					:label="i18n.signature_required"
					:description="i18n.signature_required_description"
					:checked="checked('1280')"
					name="signature_required"
				></checkbox>
			</li>
		<li v-show="showVas('2084')">
			<checkbox
					:label="i18n.electronic_notification"
					:description="i18n.electronic_notification_description"
					:checked="checked('2084')"
					name="electronic_notification"
			></checkbox>
		</li>
		<li v-show="showVas('1134')">
				<checkbox
						:label="i18n.individual_verification"
						:description="i18n.individual_verification_description"
						:checked="checked('1134')"
						name="individual_verification"
				></checkbox>
			</li>
			<li v-show="showVas('1133')">
				<checkbox
						:label="i18n.id_verification"
						:description="i18n.id_verification_description"
						:checked="checked('1133')"
						name="id_verification"
				></checkbox>
			</li>
		</ul>
	</div>
</template>

<script>
import Checkbox from "./Settings/Checkbox";

export default {
	props: {
		packages: {
			required: true,
			type: Object,
		}
	},
	data() {
		return {
			bag_on_door_consent: window.bring_fraktguiden_booking.bag_on_door_consent,
			services: window.bring_fraktguiden_booking.services,
			i18n: window.bring_fraktguiden_booking.i18n,
		}
	},
	computed: {
		availableVas() {
			const vas = [];
			for (let x in this.packages) {
				const pkg = this.packages[x];
				const service = this.services[pkg.key];
				if (!service) {
					continue;
				}
				vas.push(...service.vas)
			}
			return vas;
		}
	},
	methods: {
		showVas(code) {
			for (let x in this.availableVas) {
				const vas = this.availableVas[x];
				if (vas.code !== code) {
					continue;
				}
				return true;
			}
			return false;
		},
		checked(code) {
			if (code === '1081' && this.bag_on_door_consent !== '1') {
				return false;
			}
			for (let x in this.availableVas) {
				const vas = this.availableVas[x];
				if (vas.code !== code) {
					continue;
				}
				if (vas.enabled && vas.value) {
					return true;
				}
			}
			return false;
		}
	},
	components: {Checkbox}
}
</script>

<style scoped>
</style>

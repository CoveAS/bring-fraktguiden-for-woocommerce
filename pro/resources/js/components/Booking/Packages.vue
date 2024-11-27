<template>
	<div>

		<form class="bring-booking-packages-form">
			<input
					type="hidden"
					id="bring_order_id"
					name="bring_order_id"
					:value="orderId"
			>
			<div
					v-show="showLoader"
					class="bring-booking__loader"
					:class="loading ? 'bring-booking__active' : ''"
			>
			</div>
			<table
					class="bring-booking-packages"
			>
				<thead>
				<tr>
					<th v-html="orderId" :title="i18n.tip"></th>
					<th v-html="i18n.product"></th>
					<th v-html="i18n.width"></th>
					<th v-html="i18n.height"></th>
					<th v-html="i18n.length"></th>
					<th v-html="i18n.weight"></th>
					<th v-html="i18n.pickupPoint" v-show="showPickupPoint"></th>
					<th></th>
				</tr>
				</thead>
				<tbody>

				<package
						v-for="(packageData, id) in packages"
						:key="id"
						:show-pickup-point="showPickupPoint"
						:package="packageData"
						:removable="packages.length > 1"
						v-on:remove="removePackage(id)"
						v-on:bring-product-change="updateBringProductOnAllPackages"
				>
				</package>

				<tr>
					<td :colspan="showPickupPoint ? 7 : 6"></td>
					<td>
					<span
							class="button add"
							v-html="i18n.add"
							@click="addPackage"
					></span>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
		<settings
				:packages="packages"
		></settings>

	</div>
</template>

<style scoped>
.bring-booking-meta-box-content table th {
	text-align: left;
	background: transparent;
}

.bring-booking-packages-form {
	position: relative;
}

.bring-booking__loader {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #fff;
	opacity: 0;
	transition: opacity 0.5s;
}

.bring-booking__loader.bring-booking__active {
	opacity: 0.5;
}
</style>

<script>
import Package from "./Package";
import _ from 'lodash';
import Settings from "./Settings";

export default {
	components: {Settings, Package},
	data() {
		return {
			...window.bring_fraktguiden_booking,
			loading: false,
			showLoader: false,
			clearLoad: false,
		};
	},
	watch: {
		packages: {
			deep: true,
			handler: _.debounce(function () {
				this.updatePackages()
			}, 250)
		}
	},
	computed: {
		showPickupPoint() {
			let result = false;
			_.each(
					this.packages,
					(_package) => {
						if (_package.pickupPoint) {
							result = true;
						}
					}
			);
			return result;
		}
	},
	methods: {
		removePackage(id) {
			if (this.packages.length <= 1) {
				return;
			}
			this.packages.splice(id, 1);
			this.updatePackages();
		},
		addPackage() {
			this.packages.push(
					JSON.parse(
							JSON.stringify(
									this.packages[0]
							)
					)
			);
			this.updatePackages();
		},
		updateBringProductOnAllPackages(key) {
			_.each(
					this.packages,
					_package => {
						_package.key = key
					}
			);
		},
		updatePackages() {
			if (this.loading) {
				clearTimeout(this.clearLoad);
			}
			this.loading = true;
			this.showLoader = true;
			const packages = [];
			for (let i = 0; i < this.packages.length; i++) {
				const packageData = this.packages[i];
				packages.push({
					order_item_id: packageData.id,
					service_id: packageData.key,
					height: packageData.dimensions.heightInCm,
					length: packageData.dimensions.lengthInCm,
					width: packageData.dimensions.widthInCm,
					weight: packageData.weightInKg,
				});
			}
			jQuery.post(
					ajaxurl,
					{
						action: 'bring_update_packages',
						order_id: this.orderId,
						packages: packages
					},
					response => {
						this.loading = false;
						this.clearLoad = setTimeout(
								() => this.showLoader = false,
								500
						);
					}
			);
		}
	}
}
</script>

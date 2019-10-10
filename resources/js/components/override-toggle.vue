<template>
	<label>
		<span><slot></slot></span>
		<div class="togglererer">
			<input
				type="checkbox"
				class="bring-toggle-checkbox enable_free_shipping_limit"
				:name="name_prefix + '[' + field_id + '_cb]'"
				v-model="checkbox_val"
				:readonly="! pro_activated"
			>
			<em class="bring-toggle-alt"></em>
			<input
				v-if="'number' === input_type"
				type="number"
				step=".01"
				min="0"
				placeholder="0.00"
				v-model="field_val"
				:name="name_prefix + '[' + field_id + ']'"
				:pattern="pattern"
				:readonly="! checkbox_val || ! pro_activated"
			>
			<input
				v-else
				type="text"
				v-model="field_val"
				:name="name_prefix + '[' + field_id + ']'"
				:pattern="pattern"
				:readonly="! checkbox_val || ! pro_activated"
			>
		</div>
	</label>
</template>

<style lang="scss">
.togglererer {
	position: relative;
	width: 100%;
	.bring-toggle-alt {
		position: absolute;
		border-radius: 0;
		height: 100%;
		border-right: 1px solid #aaa;
		&::after {
		    top: 4px;
		    height: 22px;
		    width: 22px;
			border-radius: 2px;

		}
	}
	#shipping_services & {
		input[type="number"],
		input[type="text"] {
			padding-left: 4.2rem;
		}
	}
}
</style>
<script>
export default {
	props: [
		'name_prefix',
		'field_id',
		'obj',
		'input_type',
		'pattern',
	],
	data: function() {
		return {
			field_val: this.obj[ this.field_id ],
			checkbox_val: this.obj[ this.field_id + '_cb' ],
		};
	},
	computed: {
		pro_activated: function() {
			return this.$root.pro_activated;
		},
	},
};
</script>

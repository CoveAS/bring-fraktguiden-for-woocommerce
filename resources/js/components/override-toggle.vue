<template>
	<label>
		<span><slot></slot></span>
		<div class="togglererer">
			<input
				type="checkbox"
				class="bring-toggle-checkbox enable_free_shipping_limit"
				:name="name_prefix + '[' + field_id + '_cb]'"
				v-model="checkbox_val"
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
				:readonly="! checkbox_val"
			>
			<input
				v-else
				type="text"
				v-model="field_val"
				:name="name_prefix + '[' + field_id + ']'"
				:readonly="! checkbox_val"
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
		'field_val',
		'checkbox_val',
		'input_type',
	]
}
</script>

<template>
	<label :class="classes">
		<span v-html="label"></span>
		<div class="togglererer">
			<input
					type="checkbox"
					class="bring-toggle-checkbox"
					:name="name_prefix + '[' + field_id + '_cb]'"
					v-model="checkbox_val"
					:readonly="! pro_activated"
			>
			<em class="bring-toggle-alt"></em>
			<input
					v-if="'number' === input_type"
					type="number"
					:step="step"
					min="0"
					:placeholder="placeholder"
					v-model="field_val"
					:name="name_prefix + '[' + field_id + ']'"
					:readonly="! checkbox_val || ! pro_activated"
			>
			<input
					v-else
					type="text"
					v-model="field_val"
					:name="name_prefix + '[' + field_id + ']'"
					:readonly="! checkbox_val || ! pro_activated"
			>
		</div>
	</label>
</template>

<script>
var validation = function () {
	if (this.validation && !this.validation(this.field_val, this.checkbox_val)) {
		this.classes = 'validation-error';
	} else {
		this.classes = '';
	}
};
export default {
	props: {
		'name_prefix': {},
		'field_id': {},
		'obj': {},
		'input_type': {},
		'validation': {},
		'label': {},
		'step': {
			default: '0.01',
		},
		'placeholder': {
			default: '0.00',
		},
		pro_activated: {
			default: true,
			type: Boolean,
		}
	},
	data: function () {
		return {
			field_val: this.obj[this.field_id],
			checkbox_val: this.obj[this.field_id + '_cb'] === 'on',
			classes: '',
		};
	},
	mounted() {
		console.log(this.checkbox_val);
	},
	watch: {
		checkbox_val: validation,
		field_val: validation,
	},
};
</script>

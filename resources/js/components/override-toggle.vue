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

<style lang="scss">
.togglererer {
  position: relative;
  width: 100%;

  .bring-toggle-alt {
	position: absolute;
	border-radius: 0;
	height: 100%;
	border: 1px solid #7e8993;
	border-radius: 4px 0 0 4px;

	&::after {
	  top: 4px;
	  height: 20px;
	  width: 22px;
	  border-radius: 2px;
	}

	.validation-error & {
	  background-color: red;
	  border-color: #a50000;
	}
  }

  #shipping_services & {
	input[type="number"],
	input[type="text"] {
	  padding-left: 4.2rem;
	}
  }
}

.validation-error {
  input[type="number"],
  input[type="text"] {
	border-color: #CC0000;
	box-shadow: 0 0 2px rgba(255, 0, 0, 0.8);
  }
}
</style>
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

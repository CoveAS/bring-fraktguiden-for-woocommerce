<template>
	<div class="text-validator" :class="valid ? '' : 'validation-error'">
		<input
			class="input-text regular-input"
			:id="id"
			:type="type"
			v-model="value"
			:name="name"
			:placeholder="placeholder"
		>
		<ul v-show="! valid" class="text-validator__errors">
			<li v-for="error_message in error_messages" v-html="error_message"></li>
		</ul>
	</div>
</template>

<style lang="scss">
	.text-validator {
		&__errors {
			color: #C00;
		}
	}
</style>

<script>
export default {
	data: function() {
		return {
			id: '',
			value: '',
			type: '',
			name: '',
			placeholder: '',
			error_messages: [],
			valid: true,
		}
	},
	props: [
		'original_el',
		'validator',
	],
	watch: {
		value: function(new_value) {
			if ( this.validator ) {
				this.error_messages.length = 0;
				var error_messages = this.validator( new_value );
				for (var i = 0; i < error_messages.length; i++) {
					this.error_messages.push( error_messages[i] );
				}
				this.valid = ! this.error_messages.length;
			}
		}
	},
	mounted: function() {
		this.id          = this.original_el.id;
		this.value       = this.original_el.value;
		this.type        = this.original_el.type;
		this.name        = this.original_el.name;
		this.placeholder = this.original_el.placeholder;
	}
};
</script>

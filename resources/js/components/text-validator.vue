<template>
	<div class="text-validator" :class="valid ? '' : 'validation-error'">
		<input
			class="input-text regular-input"
			:id="id"
			:value="value"
			:name="name"
			:placeholder="placeholder"
		>
	</div>
</template>

<style lang="scss">
</style>

<script>
export default {
	data: function() {
		return {
			id: '',
			value: '',
			name: '',
			placeholder: '',
		}
	},
	props: [
		'original_el',
		'validator',
	],
	computed: {
		valid: function() {
			if ( this.validator && ! this.validator() ) {
				return false;
			}
			return true;
		}
	},
	mounted: function() {
		this.id          = this.original_el.id;
		this.value       = this.original_el.value;
		this.name        = this.original_el.name;
		this.placeholder = this.original_el.placeholder;
	}
};
</script>

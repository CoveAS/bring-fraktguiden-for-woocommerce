<template>
	<label class="checkbox">
		<input
			type="checkbox"
			class="bring-checkbox"
			:name="name_prefix + '[vas_' + code + ']'"
			v-model="checkbox_val"
		>
		<span v-html="label"></span>
	</label>
</template>

<style lang="scss">
</style>

<script>
export default {
	props: {
		'code':{},
		'settings':{},
		'label':{},
		'enabled':{},
		'checked':{},
		'name_prefix':{},
		pro_activated: {
			default: true,
			type: Boolean,
		}
	},
	data: function() {
		return {
			checkbox_val: this.checked,
		}
	},
};
</script>

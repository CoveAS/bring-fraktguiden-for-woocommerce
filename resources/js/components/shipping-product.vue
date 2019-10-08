<template>
	<div>
		<label v-if="service_data">
			<span>Service name:</span>
			<input type="text" v-html="custom_name" :value="service_data.productName" readonly="readonly">
		</label>
		<label>
			<span>Custom name:</span>
			<input type="text" v-html="custom_name">
		</label>
	</div>
</template>

<script>

export default {
	props: [
		'custom_name',
		'service_data'
	],
	data: function() {
		return {
			hello: 'world'
		};
	},
};
</script>

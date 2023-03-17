<template>
	<div class="am-pm w-full text-center d-flex flex-column align-center">
		<div
			class="before-after-numbers cursor-pointer"
			@click="$emit('input', value == 'pm' ? 'am' : 'pm')"
		>
			{{ value == "pm" ? $tr("am") : $tr("pm") }}
		</div>
		<div style="width: 54px">
			<v-text-field
				class="text-center number-input form-new-item text-uppercase"
				background-color="var(--new-input-bg)"
				dense
				:value="$tr(value)"
				outlined
				hide-details
				@change="() => {}"
				readonly
			></v-text-field>
		</div>
		<div class="before-after-numbers"></div>
	</div>
</template>
<script>
export default {
	props: {
		value: {
			type: String,
			default: "am",
		},
	},
};
</script>
<style>
.am-pm input,
.am-pm .before-after-numbers {
	text-transform: uppercase;
}
</style>

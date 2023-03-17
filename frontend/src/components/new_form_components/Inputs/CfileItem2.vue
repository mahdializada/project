<template>
	<div
		:class="`single-file pa-2 mb-1 position-relative ${
			error == '' || error == null ? '' : 'has-error'
		}`"
	>
		<div class="d-flex align-center">
			<div class="single-file-icon">
				<div class="single-file-icon-inner" v-html="icon"></div>
			</div>
			<div class="single-file-text ps-2 w-full">
				<p class="mb-0">{{ name }}</p>
				<p class="single-file-size">{{ size }}</p>
				<div class="d-flex align-center" v-if="hasProgress">
					<v-progress-linear
						class="w-full"
						:value="progress"
						background-color="var(--new-input-bg)"
						rounded
						height="6"
					></v-progress-linear>
				</div>
			</div>
			<div
				v-if="!(error == '' || error == null)"
				class="single-file-error error--text"
			>
				<v-icon color="error" size="18">mdi-alert-circle-outline</v-icon>
				{{ error }}
			</div>
		</div>
		<v-btn
			fab
			x-small
			text
			:color="error == '' || error == null ? 'primary' : 'error'"
			class="ma-0 single-file-close-btn"
			@click="$emit('close')"
		>
			<v-icon dark size="20"> mdi-close </v-icon>
		</v-btn>
	</div>
</template>
<script>
export default {
	props: {
		name: String,
		icon: String,
		progress: Number,
		size: String,
		hasProgress: {
			type: Boolean,
			default: true,
		},
		error: String,
	},
};
</script>
<style scoped>
.single-file {
	border: 1px solid var(--gray);
	border-radius: 10px;
}
.single-file.has-error {
	border: 1px solid var(--v-error-lighten4);
}

.single-file-icon-inner {
	height: 58px;
	width: 58px;
	background: #2e7be60f;
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
}

.single-file-text p {
	color: #777;
}
.single-file-size {
	font-size: 12px;
	margin-top: 4px;
	margin-bottom: 4px;
}
.stepper-progress-lable {
	font-size: 14px;
	font-weight: 500;
	line-height: 1;
}
.single-file-close-btn {
	position: absolute;
	top: 4px;
	right: 4px;
}
.v-application--is-rtl .single-file-close-btn {
	right: unset;
	left: 4px;
}
.single-file-error {
	font-size: 13px;
	font-weight: 500;
	min-width: 330px;
}
</style>
<style>
.single-file-icon-inner svg {
	height: 30px !important;
}
</style>

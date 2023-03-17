<template>
	<div class="files">
		items
		<div
			class="single-file pa-2 mb-1 position-relative"
			:style="`background-image: url(${getpath});`"
		>
			<div class="d-flex">
				<div class="single-file-icon">
					<div class="single-file-icon-inner" v-html="icon"></div>
				</div>
				<div class="single-file-text ps-2 w-full">
					<p class="mb-0">{{ name }}</p>
					<p class="single-file-size mb-0">{{ getSize }}</p>
					<div class="d-flex align-center">
						<template v-if="!progressing && !progress">
							<v-progress-linear
								indeterminate
								class="w-full"
								background-color="var(--new-input-bg)"
								rounded
								height="6"
							></v-progress-linear>
						</template>
						<template v-else>
							<v-progress-linear
								class="w-full"
								:value="progress"
								height="6"
								background-color="var(--new-input-bg)"
								rounded
							></v-progress-linear>
							<div class="primary--text stepper-progress-lable ps-1">
								{{ getProgress }}
							</div>
						</template>
					</div>
				</div>
			</div>
			<v-btn
				v-if="!progressing"
				fab
				x-small
				text
				color="primary"
				class="ma-0 single-file-close-btn"
				@click="
					$emit('abortUpload', {
						index: indexNumber,
					})
				"
			>
				<v-icon dark size="20"> mdi-close </v-icon>
			</v-btn>
		</div>
		<!--		<div class="single-file pa-2 mb-1 position-relative">
			<div class="d-flex">
				<div class="single-file-icon">
					<div class="single-file-icon-inner"></div>
				</div>
				<div class="single-file-text ps-2 w-full">
					<p class="mb-0">iPhone-case.jpg</p>
					<p class="single-file-size mb-0">2.4MB</p>
					<div class="d-flex align-center">
						<v-progress-linear
							class="w-full"
							value="15"
							height="6"
							background-color="var(&#45;&#45;new-input-bg)"
							rounded
						></v-progress-linear>
						<div class="primary&#45;&#45;text stepper-progress-lable ps-1">75%</div>
					</div>
				</div>
			</div>
			<v-btn
				fab
				x-small
				text
				color="primary"
				class="ma-0 single-file-close-btn"
			>
				<v-icon dark size="20"> mdi-close </v-icon>
			</v-btn>
		</div>
		<div class="single-file pa-2 mb-1 position-relative">
			<div class="d-flex">
				<div class="single-file-icon">
					<div class="single-file-icon-inner"></div>
				</div>
				<div class="single-file-text ps-2 w-full">
					<p class="mb-0">iPhone-case.jpg</p>
					<p class="single-file-size mb-0">2.4MB</p>
					<div class="d-flex align-center">
						<v-progress-linear
							class="w-full"
							value="15"
							height="6"
							background-color="var(&#45;&#45;new-input-bg)"
							rounded
						></v-progress-linear>
						<div class="primary&#45;&#45;text stepper-progress-lable ps-1">75%</div>
					</div>
				</div>
			</div>
			<v-btn
				fab
				x-small
				text
				color="primary"
				class="ma-0 single-file-close-btn"
			>
				<v-icon dark size="20"> mdi-close </v-icon>
			</v-btn>
		</div>-->
	</div>
</template>
<script>
export default {
	props: {
		size: {
			type: Number,
			required: true,
		},
		progress: {
			type: Number,
			required: true,
		},
		name: {
			type: String,
			required: true,
		},
		progressing: {
			type: Boolean,
			required: true,
		},
		uploaded: {
			type: Boolean,
			required: true,
		},
		indexNumber: {
			type: Number,
			required: true,
		},
		path: {
			type: File,
			required: true,
		},
		icon: {
			type: String,
		},
	},
	data() {
		return {
			prImagePath: "",
		};
	},
	computed: {
		getSize() {
			return (this.size / (1024 * 1024)).toFixed(2) + "MB";
		},
		getProgress() {
			return this.progress + "%";
		},

		getpath() {
			return URL.createObjectURL(this.path);
		},
	},
};
</script>
<style scoped>
.single-file {
	border: 1px solid var(--gray);
	border-radius: 10px;
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
</style>

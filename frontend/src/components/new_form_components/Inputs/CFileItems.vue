<template>
	<div class="files">
		<div class="single-file pa-2 mb-1 position-relative"
		>
			<div class="d-flex">
				<div class="single-file-icon">
					<v-avatar rounded size="57">
						<img :src="`${getPath}`" alt="" srcset="" v-if="!fileIcon">
						<span  v-else>
									<svg xmlns="http://www.w3.org/2000/svg" width="30.763" height="41.059" viewBox="0 0 30.763 41.059">
								<g id="excel" transform="translate(-278.618 -96.155)">
									<g id="Group_1486" data-name="Group 1486" transform="translate(278.618 96.155)">
									<path id="Path_593" data-name="Path 593" d="M312.679,108.2c-3.576,0-7.152-.022-10.728,0-.621,0-.82-.156-.816-.8.025-3.736,0-7.473,0-11.209,1.43,1.459,2.871,2.907,4.288,4.378q3.436,3.564,6.845,7.15C312.412,107.871,312.542,108.039,312.679,108.2Z" transform="translate(-282.083 -96.161)" fill="#409c57"/>
									<path id="Path_594" data-name="Path 594" d="M309.214,108.195c-3.576,0-7.152-.022-10.728,0-.621,0-.82-.156-.816-.8.025-3.736,0-7.473,0-11.209q-6.692-.015-13.383-.03a5.36,5.36,0,0,0-5.66,5.61q-.013,14.91,0,29.818a5.365,5.365,0,0,0,5.645,5.629q9.658,0,19.317,0a5.382,5.382,0,0,0,5.776-5.739q.008-11.223,0-22.445C309.358,108.75,309.466,108.437,309.214,108.195Z" transform="translate(-278.618 -96.155)" fill="#245c33"/>
									</g>
									<g id="Group_1487" data-name="Group 1487" transform="translate(282.42 124.739)">
									<path id="Path_595" data-name="Path 595" d="M284.752,131.307v.643h1.625v1.234h-1.625v.739H286.6v1.308h-3.487V130H286.6v1.307Z" transform="translate(-283.112 -129.948)" fill="#fff"/>
									<path id="Path_596" data-name="Path 596" d="M291.45,135.23l-.961-1.36-.8,1.36h-1.869l1.722-2.7-1.8-2.534h1.943l.924,1.307.768-1.307h1.869l-1.692,2.645,1.832,2.586Z" transform="translate(-283.826 -129.948)" fill="#fff"/>
									<path id="Path_597" data-name="Path 597" d="M295.213,131.217a2.267,2.267,0,0,1,.9-.942,2.7,2.7,0,0,1,1.374-.336,2.8,2.8,0,0,1,1.215.255,2.251,2.251,0,0,1,.871.72,2.607,2.607,0,0,1,.447,1.086h-1.729a.934.934,0,0,0-.336-.4.888.888,0,0,0-.5-.144.781.781,0,0,0-.653.311,1.57,1.57,0,0,0,0,1.669.783.783,0,0,0,.653.311.882.882,0,0,0,.5-.145.932.932,0,0,0,.336-.4h1.729a2.608,2.608,0,0,1-.447,1.086,2.251,2.251,0,0,1-.871.72,2.792,2.792,0,0,1-1.215.255,2.7,2.7,0,0,1-1.374-.336,2.267,2.267,0,0,1-.9-.942,3.19,3.19,0,0,1,0-2.763Z" transform="translate(-284.926 -129.939)" fill="#fff"/>
									<path id="Path_598" data-name="Path 598" d="M303.507,131.307v.643h1.625v1.234h-1.625v.739h1.846v1.308h-3.487V130h3.487v1.307Z" transform="translate(-285.999 -129.948)" fill="#fff"/>
									<path id="Path_599" data-name="Path 599" d="M308.579,133.989h1.58v1.241h-3.22V130h1.64Z" transform="translate(-286.779 -129.948)" fill="#fff"/>
									</g>
								</g>
								</svg>
						</span>
					</v-avatar>
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
		icon: {
			type: String,
		},
		path: {
			default:{}
		},
		fileIcon: {
			type:Boolean,
			default:false
		},
		isUpload: {
			type:Boolean,
			default:true
		},
	},
	computed: {
		getSize() {
			return (this.size / (1024 * 1024)).toFixed(2) + "MB";
		},
		getPath() {
			return URL.createObjectURL(this.path);
		},
		getProgress() {
			return this.progress + "%";
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

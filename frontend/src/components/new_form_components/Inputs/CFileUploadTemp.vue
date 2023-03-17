<template>
	<InputCard v-bind="$attrs" v-on="$listeners">
		<template slot-scope="{ attrs, listeners }">
			upload design
			<v-file-input
				:disabled="uploading"
				class="custom-file form-custom-file"
				outlined
				accept="image/jpeg,image/jpg,image/png"
				prepend-icon=""
				dense
				v-bind="attrs"
				v-on="listeners"
				@change="onChange"
				:multiple="isMultiple"
			>
				<template slot="prepend-inner">
					<div
						name="prepend-inner"
						style="max-width: 260px; text-align: center"
					>
						<svg
							id="upload-icon"
							width="40.152"
							height="50.415"
							class="mt-2 mb-2"
						>
							<g id="Group_136" data-name="Group 136" transform="translate(0)">
								<path
									id="Path_135"
									data-name="Path 135"
									d="M27.011,0l12.04,12.578c.2.214.41.428.612.645a1.744,1.744,0,0,1,.488,1.247q-.01,14.576-.009,29.153a6.693,6.693,0,0,1-5.937,6.763,6.59,6.59,0,0,1-.888.027q-13.248,0-26.5,0A6.679,6.679,0,0,1,.047,44.5a8.08,8.08,0,0,1-.025-1.035c0-12.2.033-24.4-.022-36.6A6.823,6.823,0,0,1,5.141.083C5.185.074,5.22.029,5.26,0ZM25.205,2.359h-.614q-8.8,0-17.6,0a4.225,4.225,0,0,0-4.512,4.5q0,18.3,0,36.6a4.224,4.224,0,0,0,4.511,4.5q13.076,0,26.151,0a4.254,4.254,0,0,0,4.539-4.535q0-13.738,0-27.476V15.4h-.611c-2.257,0-4.515.016-6.772-.012a5.249,5.249,0,0,1-5.057-4.922c-.066-2.481-.029-4.965-.037-7.448,0-.194,0-.389,0-.654m2.47,1.885c0,2-.013,3.832,0,5.669a2.865,2.865,0,0,0,3.083,3.025c1.564.007,3.128,0,4.692,0,.136,0,.272-.021.482-.038l-8.26-8.656"
									transform="translate(0 -0.001)"
									fill="#2e7be6"
									opacity="0.29"
								/>
								<path
									id="Path_136"
									data-name="Path 136"
									d="M54.67,72.885v.3q0,3.493,0,6.987a1.316,1.316,0,0,1-.061.466.687.687,0,0,1-.693.424.717.717,0,0,1-.619-.567,1.768,1.768,0,0,1-.027-.362q0-3.465,0-6.931v-.307l-.094-.08a1.456,1.456,0,0,1-.152.25q-1.239,1.34-2.485,2.675a.834.834,0,0,1-.676.338.647.647,0,0,1-.6-.4.661.661,0,0,1,.1-.736c.338-.376.683-.745,1.028-1.115q1.465-1.571,2.933-3.141a.744.744,0,0,1,1.28,0q1.949,2.08,3.891,4.167a.715.715,0,0,1,.021,1.048.692.692,0,0,1-1.042-.1q-1.278-1.362-2.548-2.731c-.063-.068-.118-.144-.176-.216l-.08.038"
									transform="translate(-33.9 -48.171)"
									fill="#2e7be6"
								/>
								<path
									id="Path_137"
									data-name="Path 137"
									d="M43.447,166.687q2.841,0,5.682,0a.7.7,0,1,1,.128,1.389,2.133,2.133,0,0,1-.281.007H37.949c-.075,0-.15,0-.225,0a.721.721,0,0,1-.679-.721.7.7,0,0,1,.719-.67q2.841-.005,5.682,0"
									transform="translate(-23.39 -131.525)"
									fill="#2e7be6"
								/>
							</g>
						</svg>
						<p class="ma-0 prepend-text mb-1">Drag and Drop Images to upload</p>
						<p class="ma-0 prepend-text2">or browse files on your computer</p>
					</div>
				</template>
			</v-file-input>
			<template v-if="attachments.length > 0">
				<CFileItems
					v-for="(f, index) in attachments"
					:key="index + 10000 + 'shp'"
					:name="f.name"
					:progress="100"
					:size="f.size"
					:path="f.file.file"
					:progressing="false"
					:uploaded="true"
					:index-number="index"
					@abortUpload="deleteUploadedFile"
				/>
			</template>
			<template v-if="files.length">
				<CFileItems
					v-for="(f, index) in files"
					:key="index"
					:name="f.file.name"
					:progress="f.progress"
					:size="f.file.size"
					:path="f.file"
					:progressing="currentUploadingIndex == index"
					:uploaded="f.uploaded"
					:index-number="index"
					@abortUpload="abortUpload"
				/>
			</template>
		</template>
	</InputCard>
</template>

<script>
import InputCard from "../components/InputCard.vue";
import CFileItems from "./CFileItemsTemp.vue";

export default {
	components: {
		InputCard,
		CFileItems,
	},
	props: {
		value: {
			required: true,
		},
		attachments: {
			type: Array,
			default: function () {
				return [];
			},
		},
	},
	data() {
		return {
			files: [],
			currentUploadingIndex: -1,
			uploading: false,
			cancelSource: null,
		};
	},
	watch: {
		async currentUploadingIndex(n, o) {
			if (
				n > o &&
				n >= 0 &&
				n < this.files.length &&
				!this.files[n].uploaded &&
				!this.files[n].uploading
			) {
				this.uploading = true;
				this.files[n].uploading = true;
				let path = await this.upload(this.files[n].file);

				if (path) {
					this.uploading = false;
					this.files[n].uploaded = true;
					this.files[n].uploading = false;
					this.files[n].path = path;

					this.$emit(
						"input",
						Array.isArray(this.value) ? [...this.value, path] : path
					);
				}

				this.currentUploadingIndex++;

				if (this.currentUploadingIndex == this.files.length) {
					this.currentUploadingIndex = -1;
				}
			}
		},
	},
	methods: {
		abortUpload(e) {
			let index = e.index;
			this.abortByIndex(index);
		},
		deleteUploadedFile(e) {
			let index = e.index;
			console.log("tesdfasdf", index);
			try {
				this.$axios.delete("/single-sales/deleted/upload", {
					params: { path: this.attachments[index].path },
				});
				this.attachments.splice(index, 1);
			} catch (e) {
				console.log("asdfasdfafsadfa", e);
			}
		},
		abortByIndex(index) {
			if (this.files[index].path) {
				try {
					this.$axios.delete("/single-sales/deleted/upload", {
						params: { path: this.files[index].path },
					});
					for (let i = 0; i < this.value.length; i++) {
						if (this.value[i] == this.files[index].path) {
							this.value.splice(i, 1);
						}
					}
					this.$emit("input", Array.isArray(this.value) ? [...this.value] : "");
					this.files.splice(index, 1);
					this.currentUploadingIndex--;
				} catch (e) {}
			}
		},
		async upload(file) {
			try {
				let formData = new FormData();
				var config = {
					onUploadProgress: (progressEvent) => {
						let percentCompleted = Math.round(
							(progressEvent.loaded * 100) / progressEvent.total
						);

						try {
							this.files[this.currentUploadingIndex].progress =
								percentCompleted;
						} catch (e) {
							console.log("error current index", this.currentUploadingIndex);
						}
					},
				};
				formData.append("file", file);
				// this.cancelSource = this.$axios.CancelToken.source();
				let result = await this.$axios.post(
					"/single-sales/upload",
					formData,
					config
				);
				this.cancelSource = null;
				return result.data?.path;
			} catch (e) {
				console.log("file could not be uploaded", e);
			}
		},
		async onChange(event) {
			if (Array.isArray(event)) {
				for (const a of event) {
					await this.queueFile(a);
				}
			} else {
				await this.queueFile(event);
			}
		},

		async queueFile(file) {
			if (file instanceof File) {
				this.files.push({
					uploaded: false,
					uploading: false,
					progress: 0,
					path: null,
					file: file,
				});

				if (!this.uploading) {
					this.currentUploadingIndex = this.files.length - 1;
				}

				if (!Array.isArray(this.value) && this.files.length > 0) {
					this.abortByIndex(0);
				}
			}
		},
	},
	computed: {
		isMultiple() {
			return Array.isArray(this.value);
		},
	},
};
</script>
<style>
.form-custom-file .v-input__slot {
	background-color: var(--new-input-bg) !important;
}

.form-custom-file .v-input__slot .v-file-input__text {
	height: 140px;
}

.form-custom-file.v-input--is-focused .v-input__slot {
	background-color: #2e7be60f !important;
}

.form-custom-file .prepend-text {
	color: var(--v-primary-base);
	font-weight: 500;
}

.prepend-text2 {
	font-size: 12px;
}
</style>

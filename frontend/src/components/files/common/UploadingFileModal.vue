<template>
	<div>
		<v-dialog v-if="!minimize" v-model="uploadingDialog" persistent width="800">
			<v-card>
				<v-card-title class="pa-1">
					<div class="mx-2 primary__text">
						{{
							$tr(
								"uploading_number_of_files",
								uploadedFiles.length + uploadingFiles.length,
								uploadedFiles.length +
									uploadingFiles.length +
									uploadQueue.length +
									retryFiles.length
							)
						}}
					</div>
					<v-spacer />
					<v-btn
						@click="() => toggleModal(false)"
						v-if="
							uploadingFiles.length + uploadQueue.length + retryFiles.length ==
							0
						"
						class="ma-1"
						text
						icon
						color="error"
					>
						<v-icon> mdi-close </v-icon>
					</v-btn>
					<v-btn v-else @click="minimizeFun" class="ma-1" text icon color="">
						<v-icon> mdi-minus </v-icon>
					</v-btn>
				</v-card-title>
				<v-divider />

				<v-card-text>
					<UploadableItems
						:items="[
							...uploadedFiles,
							...uploadingFiles,
							...uploadQueue,
							...retryFiles,
						]"
						@onCancel="cancelUpload"
					/>
				</v-card-text>
				<v-divider />
				<v-card-actions class="py-2">
					<v-spacer />
					<v-btn
						v-if="
							uploadingFiles.length + uploadQueue.length + retryFiles.length ==
							0
						"
						@click="cancelAll(true)"
						style="height: 30px"
						class="ms-2 px-2 font-weight-medium rounded-sm"
					>
						<span class="primary__text">
							{{ $tr("clear_all") }}
						</span>
					</v-btn>
					<v-btn
						v-else
						@click="cancelAll(false)"
						style="height: 30px"
						class="ms-2 px-2 font-weight-medium rounded-sm"
					>
						<span class="primary__text">
							{{ $tr("cancel") }}
						</span>
					</v-btn>
				</v-card-actions>
			</v-card>
		</v-dialog>

		<v-slide-x-reverse-transition>
			<v-btn
				v-if="minimize"
				@click="minimize = false"
				color="primary"
				class="fixed elevation-2"
			>
				<v-progress-circular
					:value="
						(uploadedFiles.length * 100) /
						(uploadedFiles.length +
							uploadingFiles.length +
							uploadQueue.length +
							retryFiles.length)
					"
					color="white"
					:rotate="-90"
					class="progress"
					size="26"
					:width="2"
				>
					<v-icon small> mdi-upload </v-icon>
				</v-progress-circular>
			</v-btn>
		</v-slide-x-reverse-transition>
	</div>
</template>

<script>
import UploadableItems from "./UploadableItems.vue";
import Alert from "~/helpers/alert";
import { mapMutations, mapState } from "vuex";

export default {
	components: { UploadableItems },
	data() {
		return {
			model: false,
			minimize: false,
		};
	},
	computed: {
		...mapState("files", [
			"uploadedFiles",
			"uploadingDialog",
			"uploadQueue",
			"isUploading",
			"uploadingFiles",
			"retryFiles",
		]),
	},

	watch: {
		uploadQueue(value) {
			if (value.length == 0) {
				this.toggleIsUploading(false);
			}
			if (!this.isUploading && value.length !== 0) {
				this.startUploading();
				this.toggleModal(true);
			}
		},
		uploadingFiles(value) {
			if (value.length < 6) {
				this.addOneItemToUpload();
			}
		},
	},

	methods: {
		...mapMutations("files", [
			"toggleUploadigDialog",
			"toggleIsUploading",
			"startUploading",
			"addOneItemToUpload",
			"cancelUpload",
			"cancelAllUpload",
		]),

		toggleModal(param = false) {
			this.toggleUploadigDialog(param);
		},

		cancelAll(isClear) {
			Alert.removeDialogAlert(
				this,
				() => {
					this.cancelAllUpload();
					this.toggleModal(false);
				},
				"",
				isClear ? "yes_clear" : "yes_cancel"
			);
		},
		minimizeFun() {
			this.minimize = !this.minimize;
		},
	},
};
</script>

<style lang="scss" scoped>
.primary__text {
	color: var(--v-primary-base);
}
</style>
<style lang="scss">
.fixed {
	position: fixed;
	right: -21px;
	bottom: 125px;
	z-index: 10000000;
	.progress {
		margin-left: -20px;
	}
}
</style>

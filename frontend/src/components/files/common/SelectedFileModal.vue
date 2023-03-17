<template>
	<v-dialog v-model="model" persistent width="800">
		<form ref="fileInputForm" class="d-none">
			<input ref="file_uploader" class="d-none" type="file" multiple @change="onFileChanged" />
			<input ref="reset" type="reset" value="reset" class="d-none" />
		</form>

		<v-card>
			<v-card-title class="pa-1">
				<div class="mx-2 primary__text">
					{{ $tr("uploading_number_files", selectedFiles.length) }}
				</div>
				<v-spacer />
				<v-btn @click="toggleModal" class="ma-1" text icon color="error">
					<v-icon> mdi-close </v-icon>
				</v-btn>
			</v-card-title>
			<v-divider />

			<v-card-text>
				<UploadableItems :items="selectedFiles" @onCancel="onCancel" />
			</v-card-text>
			<v-divider />
			<v-card-actions class="py-2">
				<v-spacer />
				<v-btn @click="handleFileImport" style="height: 30px" class="ms-2 px-2 font-weight-medium rounded-sm">
					<v-icon color="primary" class="me-1"> mdi-plus </v-icon>
					<span class="primary__text"> {{ $tr("add_files") }} </span>
				</v-btn>

				<v-btn
					@click="startUpload"
					:disabled="isuploadingFolders"
					color="primary"
					style="height: 30px"
					class="ms-2 px-2 font-weight-medium rounded-sm"
				>
					<v-icon class="me-1" v-html="isuploadingFolders ? 'mdi-loading fa-spin' : 'mdi-upload'"> </v-icon>
					{{ $tr("start_uploading") }}
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import UploadableItems from "./UploadableItems.vue";
import { mapMutations, mapState } from "vuex";

export default {
	components: { UploadableItems },
	data() {
		return {
			model: false,
			uploadFile: false,
			isuploadingFolders: false,
		};
	},

	computed: {
		...mapState("files", ["selectedFiles", "folders", "uploadedFolders"]),
	},

	methods: {
		...mapMutations("files", [
			"addSelectedFile",
			"removeSelectedFile",
			"removeAllSelectedFiles",
			"addFilesToQueue",
			"addUploadedFolders",
			"toggleAlert",
		]),

		toggleModal() {
			this.model = !this.model;
			if (!this.model) {
				this.removeAllSelectedFiles();
			}
		},

		handleCreateFolder() {
			this.$emit("onFolder");
		},
		handleFileImport() {
			this.uploadFile = true;
			this.$refs.file_uploader.click();
		},

		onFileChanged(event) {
			if (event && event.target.files.length > 0) {
				for (let i = 0; i < event.target.files.length; i++) {
					let path = event.target.files[i].webkitRelativePath;
					let file = event.target.files[i];
					let temp_file = {
						id: this.generateID(),
						file,
						name: file.name,
						status: null,
						showFormat: file.type.indexOf("video") == 0,
						size: file.size,
						quality: "auto",
						progress: 0,
						folders: event.target.files[i].webkitRelativePath.split("/"),
						path: path.substr(0, path.lastIndexOf("/")),
						parent_id: this.$route.params.slug ? this.$route.params.slug : null,
						parent_temp_id: null,
						folder_chunk_id: "",
					};
					this.addSelectedFile(temp_file);
				}
			}
			this.$refs.reset.click();
		},

		generateID() {
			return (
				"Id" +
				Math.floor(
					(Date.now() * Math.floor(Math.random() * Math.floor(Math.random() * Date.now()))) /
						(Math.random() * 1000000000)
				)
			);
		},

		onCancel(item) {
			this.removeSelectedFile(item);
		},

		async startUpload() {
			this.isuploadingFolders = true;
			this.toggleAlert(true)
			let res = await this.$axios
				.post("files/personal/folder/bulkupload", {
					folders: this.folders,
					parent_id: this.$route.params.slug,
				})
				.then((response) => {
					if (response.status == 201) {
						this.isuploadingFolders = false;
						this.addUploadedFolders({
							id: response.data.folders_chunk,
							folders: response.data.folders,
						});
						this.addFilesToQueue();
						this.toggleModal();
					}
				})
				.catch((error) => {
					this.isuploadingFolders = false;
				});
		},
	},
};
</script>

<style lang="scss" scoped>
.primary__text {
	color: var(--v-primary-base);
}
</style>

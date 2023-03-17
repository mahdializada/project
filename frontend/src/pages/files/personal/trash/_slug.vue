<template>
	<div class="pa-2">
		<FileBreadCrumb
			:items="currentPage.breadcrumb"
			@shortkey.native="theAction"
			v-shortkey="{
				up: ['arrowup'],
				down: ['arrowdown'],
				right: ['arrowright'],
				left: ['arrowleft'],
				ctrlA: ['ctrl', 'a'],
			}"
		/>

		<TopSelectActionMenu
			:trash="true"
			:trashChildMenu="currentPage.parent"
			@selectAll="selectAll"
			:selectedCount="selectedCount"
			:selected="getSelected()"
			:isAllSelected="
				selectedCount == currentPage.folders.length + currentPage.files.length
			"
		/>
		<RightClickMenu
			v-if="layout != 'list'"
			@onDelete="deleteFiles"
			@onProperty="onProperty"
			@onRestore="onRestore"
			@emptySelected="emptySelected"
			@onRefresh="$emit('onRefresh')"
			:selected="getSelected()"
		/>
		<div class="content-section" v-if="layout == 'list'">
			<ListView
				:trashChildMenu="currentPage.parent"
				:isPaginating="isPaginating"
				:loading="loading"
				@onDelete="deleteFiles"
				@onProperty="onProperty"
				@onRestore="onRestore"
				@onRightClick="onRightClick"
				@onRefresh="$emit('onRefresh')"
				:trash="true"
				link="trash"
				:currentPage="
					currentPage.folders && {
						...currentPage,
						items: [...currentPage.folders, ...currentPage.files],
					}
				"
			/>
		</div>
		<div
			v-else
			ref="container"
			:class="`file-content pa-2 bg-light-gray rounded-lg mt-2 ${
				selectedCount > 0 ? 'show-top-bar' : ''
			}`"
			@contextmenu.stop="onRightClick"
		>
			<div class="content-section" v-if="loading">
				<SectionTitle title="Folders" />
				<div class="d-flex flex-wrap px-2">
					<v-skeleton-loader
						v-for="item in 8"
						:key="item"
						style="width: 280px"
						class="ma-1"
						type="list-item-avatar, divider, table-heading"
					></v-skeleton-loader>
				</div>

				<SectionTitle title="Files" />
				<div class="d-flex flex-wrap px-2">
					<v-skeleton-loader
						v-for="item in 8"
						:key="item"
						style="width: 280px"
						class="ma-1 custom_skeleton"
						type="image, list-item-avatar, divider, table-heading"
					></v-skeleton-loader>
				</div>
			</div>
			<div v-else class="h-full">
				<div
					v-if="currentPage.folders && currentPage.folders.length > 0"
					class="content-section"
				>
					<SectionTitle title="Folders" />
					<div class="d-flex flex-wrap px-2">
						<Folder
							v-for="(folder, folderIndex) in currentPage.folders"
							:key="folder.id"
							:ref="folder.id"
							:folder="folder"
							:index="folderIndex"
							:isSelected="folder.id in selected"
							:link="`/files/personal/trash`"
							actionUrl="files/personal/folder/action"
							:parent_id="currentPage.parent && currentPage.parent.id"
							@onRightClick="onItemRightClick"
							@onClicked="
								(selected) => {
									if (selected) {
										selectedCount++;
									} else {
										selectedCount--;
									}
								}
							"
						/>
					</div>
				</div>
				<div
					v-else-if="
						currentPage.files &&
						currentPage.files.length < 1 &&
						currentPage.folders &&
						currentPage.folders.length < 1
					"
					style="height: 100%"
					class="content-section"
				>
					<NoFilesComponent />
				</div>
				<div
					v-if="currentPage.files && currentPage.files.length > 0"
					class="content-section"
				>
					<SectionTitle title="Files" />
					<div class="d-flex flex-wrap px-2">
						<File
							:trash="true"
							:isSelected="file.id in selected"
							v-for="file in currentPage.files"
							:key="file.id"
							:file="file"
							:ref="file.id"
							actionUrl="files/personal/files/rename"
							:parent_id="currentPage.parent && currentPage.parent.id"
							@onRightClick="onItemRightClick"
							@dbClick="() => {}"
							@onClicked="
								(selected) => {
									if (selected) {
										selectedCount++;
									} else {
										selectedCount--;
									}
								}
							"
						/>
					</div>
				</div>
			</div>
		</div>

		<v-navigation-drawer
			v-model="properties"
			absolute
			temporary
			right
			width="450"
		>
			<Properties
				@close="properties = false"
				:items="propertiesData"
			></Properties>
		</v-navigation-drawer>

		<!-- <ToggleListGrid /> -->
	</div>
</template>
<script>
import RightClickMenu from "~/components/files/common/RightClickMenu.vue";
import FileBreadCrumb from "~/components/files/common/FileBreadCrumb.vue";
import SectionTitle from "~/components/files/common/SectionTitle.vue";
import Folder from "~/components/files/common/Folder.vue";
import File from "~/components/files/common/File.vue";
import ToggleListGrid from "~/components/files/common/ToggleListGrid.vue";
import ListView from "~/components/files/common/ListView.vue";
import { mapState } from "vuex";
import FilePreview from "~/components/files/common/filePreview/FilePreview.vue";
import Properties from "~/components/files/common/Properties.vue";
import TopSelectActionMenu from "~/components/files/common/TopSelectActionMenu.vue";
import Alert from "../../../../helpers/alert";
import FileManagement from "~/utils/file_management/FileManagement";
import NoFilesComponent from "../../../../components/files/common/NoFilesComponent.vue";

export default {
	props: {
		loading: {
			type: Boolean,
			default: false,
		},

		currentPage: {
			type: Object,
			default: () => ({
				files: [],
				folders: [],
			}),
		},
	},

	data() {
		return {
			isPaginating: false,
			selectedItem: null,
			properties: false,
			propertiesData: null,
			selectedCount: 0,
		};
	},

	watch: {},

	computed: {
		...mapState("files", ["layout"]),
		...mapState("files_select_copy_move", ["selected"]),
	},

	mounted() {
		this.$root.$on("onDeleteSuccess", this.onDeleteSuccess);
		this.emptySelected();
	},

	async validate({ params }) {
		return params.slug == undefined;
	},
	methods: {
		emptySelected() {
			this.selectedCount = 0;
			let { folders, files } = this.currentPage;
			let arrLength = folders?.length;
			for (let i = 0; i < arrLength; i++) {
				if (folders[i].isSelected == true) {
					folders[i].isSelected = false;
				}
			}
			let arrLength2 = files?.length;
			for (let i = 0; i < arrLength2; i++) {
				if (files[i].isSelected == true) {
					files[i].isSelected = false;
				}
			}
		},

		/** =======> Common Operation Methods <====== */
		// onRestore
		async onRestore(restoreFile) {
			const fileManagement = new FileManagement(this);
			try {
				let items = [];
				if (Array.isArray(restoreFile)) {
					items = restoreFile;
				} else if (this.selectedItem) {
					const { id, type } = this.selectedItem;
					items = [{ id, type }];
				}
				const urlres = `files/personal/restoreFile`;
				let { files, folders } = this.currentPage;
				let removeResult = fileManagement.removeItemsAndReturn({
					items,
					files,
					folders,
				});
				restoreFile = removeResult.removedItems;
				this.currentPage.files = removeResult.files;
				this.currentPage.folders = removeResult.folders;
				await fileManagement.restoreFile(urlres, items);
			} catch (error) {
				let { files, folders } = this.currentPage;
				let pushResult = fileManagement.pushItemsAndReturn({
					items: restoreFile,
					files,
					folders,
				});
				this.currentPage.files = pushResult.files;
				this.currentPage.folders = pushResult.folders;
				this.isDeletingFromPreview = false;
			}
		},

		onProperty() {
			this.properties = true;
			this.propertiesData = this.selectedItem;
		},

		onRightClick(event) {
			event.preventDefault();
			const options = {
				showMenu: true,
				items: ["sort_by", "thumbnail", "list", "refresh"],
				positionX: event.clientX,
				positionY: event.clientY,
			};
			this.$root.$emit("toggleContextMenu", options);
		},
		selectAll(isSelected) {
			if (isSelected) {
				let { folders, files } = this.currentPage;
				let arrLength = folders.length;
				for (let i = 0; i < arrLength; i++) {
					if (folders[i].isSelected == false) {
						this.selectedCount++;
						folders[i].isSelected = true;
					}
				}
				let arrLength2 = files.length;
				for (let i = 0; i < arrLength2; i++) {
					if (files[i].isSelected == false) {
						this.selectedCount++;
						files[i].isSelected = true;
					}
				}
			} else {
				this.emptySelected();
			}
		},

		onItemRightClick({ event, item }) {
			let options = {};
			this.selectedItem = item;
			options = {
				showMenu: true,
				items: ["restore", "delete", "properties"],
				positionX: event.clientX,
				positionY: event.clientY,
			};

			this.$root.$emit("toggleContextMenu", options);
		},

		// Fire on scroll for pagination of data
		onScroll(event) {
			const element = event.target;
			if (element.scrollTop > 100) {
				const isScollInBottom =
					element.scrollHeight - (element.scrollTop + 100) <=
					element.clientHeight;
				if (isScollInBottom && !this.isPaginating) {
					const { nextCursor } = this.currentPage;
					if (nextCursor) {
						this.isPaginating = true;
						const { slug } = this.$route.params;
						if (slug) {
							const url = `files/personal/${slug}?cursor=${nextCursor}`;
							this.paginateItems(url);
						} else {
							const url = `files/personal/?cursor=${nextCursor}`;
							this.paginateItems(url);
						}
					}
				}
			}
		},

		// delete folder or files
		async deleteFiles(deletedItems) {
			await Alert.removeDialogAlert(this, () => {
				const fileManagement = new FileManagement(this);
				try {
					let items = [];
					if (Array.isArray(deletedItems)) items = deletedItems;
					else if (this.selectedItem) {
						const { id, type, parent_id } = this.selectedItem;
						items = [{ id, type, parent_id }];
					} else return;

					const url = `files/personal/trash/delete`;
					let { files, folders } = this.currentPage;
					let removeResult = fileManagement.removeItemsAndReturn({
						items,
						files,
						folders,
					});
					deletedItems = removeResult.removedItems;
					this.currentPage.files = removeResult.files;
					this.currentPage.folders = removeResult.folders;
					fileManagement.deleteFiles(url, items);
					this.emptySelected();
				} catch (error) {
					let { files, folders } = this.currentPage;
					let pushResult = fileManagement.pushItemsAndReturn({
						items: deletedItems,
						files,
						folders,
					});
					this.currentPage.files = pushResult.files;
					this.currentPage.folders = pushResult.folders;
				}
			});
		},

		//  Display context menu on folder right clicked

		theAction(event) {
			if (event.srcKey == "ctrlA") {
				this.selectAll(true);
				return;
			}
			let selects = this.getSelected();
			const element = document.querySelector(".file-content");
			if (selects?.length == 1) {
				let container_width = this.$refs.container.offsetWidth - 64;
				let items = [...this.currentPage.folders, ...this.currentPage.files];
				let index = items.findIndex((item) => item.id == selects[0].id);
				if (index != -1) {
					items[index].isSelected = false;
					if (this.layout == "grid") {
						let step = Math.floor(container_width / 296);
						switch (event.srcKey) {
							case "up":
								if (index - step >= 0) {
									items[index - step].isSelected = true;
								} else {
									items[0].isSelected = true;
								}
								break;
							case "down":
								if (index + step < items.length) {
									items[index + step].isSelected = true;
								} else {
									items[items.length - 1].isSelected = true;
								}
								break;
							case "right":
								if (index + 1 < items.length) {
									items[index + 1].isSelected = true;
								} else {
									items[items.length - 1].isSelected = true;
								}
								break;
							case "left":
								if (index - 1 >= 0) {
									items[index - 1].isSelected = true;
								} else {
									items[0].isSelected = true;
								}
								break;
						}
					} else if (this.layout == "list") {
						switch (event.srcKey) {
							case "up":
								if (index - 1 >= 0) {
									items[index - 1].isSelected = true;
								} else {
									items[0].isSelected = true;
								}
								break;
							case "down":
								if (index + 1 < items.length) {
									items[index + 1].isSelected = true;
								} else {
									items[items.length - 1].isSelected = true;
								}
								break;
						}
					}
				}
				if (element) {
					let elToScroll = this.$refs[this.getSelected()[0].id][0].$el;
					element.scrollTo({
						top:
							elToScroll.offsetTop -
							(element.offsetHeight / 2 - elToScroll.offsetHeight / 2),
					});
				}
			}
		},

		getSelected() {
			let files = this.currentPage.files.filter((item) => {
				if (item.isSelected) {
					return item;
				}
			});
			let folders = this.currentPage.folders.filter((item) => {
				if (item.isSelected) {
					return item;
				}
			});
			return [...files, ...folders];
		},
	},
	components: {
		FileBreadCrumb,
		SectionTitle,
		Folder,
		File,
		ToggleListGrid,
		ListView,
		RightClickMenu,
		FilePreview,
		Properties,
		TopSelectActionMenu,
		NoFilesComponent,
	},
};
</script>
<style lang="scss">
.file-content {
	width: calc(100% - 32px);
	transition: top 0.4s;
}

.show-top-bar {
	top: 130px !important;
}

.custom_skeleton .v-skeleton-loader__image {
	height: 100px !important;
	border-radius: 0;
}

.select-all__checkbox {
	position: absolute;
	left: 16px;
	top: 4px !important;
}

.select-action-menu {
	height: 38px;
	padding-top: 8px;
	padding-bottom: 8px;
	transition: all 0.4s;
}

.custom-h-0 {
	height: 0px !important;
	padding-top: 0px !important;
	padding-bottom: 0px !important;
	margin: 0px !important;
	overflow: hidden;
}
</style>

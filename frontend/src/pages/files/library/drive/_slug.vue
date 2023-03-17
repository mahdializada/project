<template>
	<div class="position-relative h-screen custom">
		<FileBreadCrumb
			:items="currentPage.breadcrumb"
			@shortkey.native="theAction"
			v-shortkey="{
				up: ['arrowup'],
				down: ['arrowdown'],
				right: ['arrowright'],
				left: ['arrowleft'],
			}"
		/>
		<!-- <FilePreview
      :showPreviewDialog="showPreviewDialog"
      @closeDialog="showPreviewDialog = false"
      ref="filePreviewRef"
      preview-link="files/library/details"
      file-type="library"
    /> -->

		<FilePreview
			:key="filePreviewKey"
			:showPreviewDialog="showPreviewDialog"
			:file-ids="currentPage.files.map(({ id }) => id)"
			ref="filePreviewRef"
			preview-link="files/library/details"
			file-type="library"
			:is-deleting.sync="isDeletingFromPreview"
			@on-close="(showPreviewDialog = false), filePreviewKey++"
			@on-download="onDownload"
		/>

		<ShareModal
			v-if="shareModal"
			:dialog="shareModal"
			:items="itemsToShare"
			@toggleShareModal="toggleShareModal"
		/>

		<!-- <TopSelectActionMenu
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @selectAll="selectAll"
      :selectedCount="selectedCount"
      :selected="getSelected()"
      @onShare="onShare"
      :isAllSelected="
        selectedCount == currentPage.folders.length + currentPage.files.length
      "
    /> -->
		<!-- <RightClickMenu
      v-if="layout != 'list'"
      @onOpen="openFolder"
      @onRename="onItemRename"
      @onFavorites="addToFavorites"
      @onDelete="deleteFiles"
      @onProperty="onProperty"
      @onCopy="onCopy"
      @onMove="onMove"
      @onDownload="onDownload"
      @onMultiDownload="onMultiDownload"
      @onShare="onShare"
      @emptySelected="emptySelected"
      :activeChildItem="currentPage.sortBy"
      :selected="getSelected()"
    /> -->
		<div
			class="file-content pa-2 bg-light-gray rounded-lg mt-2 d-flex align-center justify-center"
			v-if="selectedCompany == null"
		>
			<div class="text-center">
				<div>
					<v-icon size="80" class="text--disabled">mdi-domain</v-icon>
				</div>
				<p>{{ $tr("please_select_company") }}</p>
			</div>
		</div>
		<div class="content-section" v-else-if="layout == 'list'">
			<ListView
				:isPaginating="isPaginating"
				@onDelete="deleteFiles"
				@folderCreated="folderCreated"
				@onFavorites="addToFavorites"
				@updateItem="updatePersonalFile"
				@onDownload="onDownload"
				@onMultiDownload="onMultiDownload"
				@onCopy="onCopy"
				@onMove="onMove"
				link="drive"
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
							v-if="createFolder"
							:renameFolderProps="true"
							actionUrl="files/library/folder/action"
							:parent_id="currentPage.parent && currentPage.parent.id"
							@folderCreated="folderCreated"
						/>
						<Folder
							v-for="(folder, folderIndex) in currentPage.folders"
							:key="folder.id"
							:ref="folder.id"
							:folder="folder"
							:index="folderIndex"
							:link="`/files/library/drive/${folder.id}`"
							actionUrl="files/library/folder/action"
							:parent_id="currentPage.parent && currentPage.parent.id"
							@onRightClick="onItemRightClick"
							@folderCreated="({ name }) => (folder.name = name)"
							@onFavorites="addToFavorites"
							@onClicked="
								(selected) => {
									if (selected) {
										selectedCount++;
									} else {
										selectedCount--;
									}
								}
							"
							:showFavorite="true"
						/>
					</div>
				</div>
				<div v-else-if="createFolder" class="content-section">
					<SectionTitle title="Folders" />
					<div class="d-flex flex-wrap px-2">
						<Folder
							v-if="createFolder"
							:renameFolderProps="true"
							actionUrl="files/library/folder/action"
							:parent_id="currentPage.parent && currentPage.parent.id"
							@folderCreated="folderCreated"
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
					<div
						style="height: 100%"
						class="d-flex flex-column align-center justify-center px-2"
					>
						<svg
							xmlns="http://www.w3.org/2000/svg"
							width="80"
							viewBox="0 0 50 33.333"
						>
							<path
								id="Icon_awesome-folder-open"
								data-name="Icon awesome-folder-open"
								d="M49.713,24.3,43.426,35.077a5.555,5.555,0,0,1-4.8,2.756H3.908a2.083,2.083,0,0,1-1.8-3.133L8.4,23.923a5.556,5.556,0,0,1,4.8-2.756H47.914a2.083,2.083,0,0,1,1.8,3.133ZM13.194,18.389H41.667V14.222A4.167,4.167,0,0,0,37.5,10.056H23.611L18.056,4.5H4.167A4.167,4.167,0,0,0,0,8.667V32.8L6,22.523A8.362,8.362,0,0,1,13.194,18.389Z"
								transform="translate(0 -4.5)"
								fill="rgba(0,0,0,0.15)"
							/>
						</svg>
						<div class="mt-1 font-weight-medium text-h6">
							{{ $tr("no_file") }}
						</div>
					</div>
				</div>
				<div
					v-if="currentPage.files && currentPage.files.length > 0"
					class="content-section"
				>
					<SectionTitle title="Files" />
					<div class="d-flex flex-wrap px-2">
						<File
							v-for="file in currentPage.files"
							:key="file.id"
							:file="file"
							:ref="file.id"
							actionUrl="files/library/files/rename"
							:parent_id="currentPage.parent && currentPage.parent.id"
							@itemRenamed="({ name }) => (file.name = name)"
							@onRightClick="onItemRightClick"
							@dbClick="dbClickHandler"
							@onFavorites="addToFavorites"
							@onClicked="
								(selected) => {
									if (selected) {
										selectedCount++;
									} else {
										selectedCount--;
									}
								}
							"
							:showFavorite="true"
						/>
					</div>
				</div>
			</div>
			<div v-if="isPaginating">
				<div v-if="currentPage.files.length > 0" class="d-flex flex-wrap px-2">
					<v-skeleton-loader
						v-for="item in 8"
						:key="item"
						style="width: 280px"
						class="ma-1 custom_skeleton"
						type="image, list-item-avatar, divider, table-heading"
					></v-skeleton-loader>
				</div>
				<div v-else class="d-flex flex-wrap px-2">
					<v-skeleton-loader
						v-for="item in 8"
						:key="item"
						style="width: 280px"
						class="ma-1"
						type="list-item-avatar, divider, table-heading"
					></v-skeleton-loader>
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
		<!-- <CopyModal ref="copyDialog"></CopyModal> -->
		<!-- <FilePasswordModel setPasswordUrl="files/library/files/password" /> -->
	</div>
</template>
<script>
import RightClickMenu from "~/components/files/common/RightClickMenu.vue";
import FileBreadCrumb from "~/components/files/common/FileBreadCrumb.vue";
import SectionTitle from "~/components/files/common/SectionTitle.vue";
import Folder from "~/components/files/common/Folder.vue";
import File from "~/components/files/common/File.vue";
import ToggleListGrid from "~/components/files/common/ToggleListGrid.vue";
import FilePasswordModel from "~/components/files/common/FilePasswordModel.vue";
import ListView from "~/components/files/common/ListView.vue";
import CopyModal from "~/components/files/common/CopyModal.vue";
import { mapMutations, mapState } from "vuex";
import FilePreview from "~/components/files/common/filePreview/FilePreview.vue";
import Properties from "~/components/files/common/Properties.vue";
import TopSelectActionMenu from "~/components/files/common/TopSelectActionMenu.vue";
import ShareModal from "~/components/files/common/Share/ShareModal.vue";
import Persons from "~/utils/file_management/personal";
import FileManagement from "~/utils/file_management/FileManagement";
import Personal from "~/utils/file_management/personal";

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

		selectedCompany: {
			type: String,
		},
	},

	data() {
		return {
			filePreviewKey: 0,
			isDeletingFromPreview: false,
			showPreviewDialog: false,
			isPaginating: false,
			selectedItem: null,
			properties: false,
			propertiesData: null,
			shareModal: false,
			itemsToShare: [],
			selectedCount: 0,
		};
	},

	watch: {
		createFolder: function (value) {
			if (value) {
				const element = document.querySelector(".file-content");
				if (element) {
					element.scrollTo({
						top: 0,
						behavior: "smooth",
					});
				}
			}
		},
	},

	computed: {
		...mapState("files", ["createFolder", "layout"]),
	},

	mounted() {
		this.$root.$on("onDeleteSuccess", this.onDeleteSuccess);
		this.emptySelected();
	},

	beforeDestroy() {
		this.emptySelected();
	},

	methods: {
		...mapMutations("files", ["pushZippingFile"]),
		...mapMutations("files_select_copy_move", ["changeSelected"]),

		/** =======> Common Operation Methods <====== */
		onDeleteSuccess(items) {
			let deletedItems = [];
			if (Array.isArray(items)) {
				let files = this.currentPage.files;
				let folders = this.currentPage.folders;
				for (let index = 0; index < items.length; index++) {
					const { id, type } = items[index];
					if (type == "folder") {
						folders = folders.filter((folder) => folder.id != id);
					} else {
						files = files.filter((file) => file.id != id);
					}
				}
				this.currentPage.files = files;
				this.currentPage.folders = folders;
			}
			return deletedItems;
		},

		async deleteFiles(deletedItems) {
			try {
				let items = [];
				if (Array.isArray(deletedItems)) {
					items = deletedItems;
				} else if (this.selectedItem) {
					const { id, type } = this.selectedItem;
					items = [{ id, type }];
				}
				const url = `files/library/delete`;
				const callback = () => this.$root.$emit("onDeleteSuccess", items);
				const fileManagement = new FileManagement(this);
				// let { files, folders } = this.currentPage;
				// let [fileItems, folderItems, removedItems] =
				//   fileManagement.removeItemsAndReturn({ items, files, folders });
				await fileManagement.deleteFiles(url, items, callback);
			} catch (error) {}
		},

		onCopy(selectedItems) {
			let items = [];
			if (Array.isArray(selectedItems)) {
				items = selectedItems;
			} else if (this.selectedItem) {
				const { id, type } = this.selectedItem;
				items = [{ id, type }];
			}
			this.$refs.copyDialog.toggleDialog({ action: "copy", items });
		},

		onMove(selectedItems) {
			let items = [];
			if (Array.isArray(selectedItems)) {
				items = selectedItems;
			} else if (this.selectedItem) {
				const { id, type } = this.selectedItem;
				items = [{ id, type }];
			}
			this.$refs.copyDialog.toggleDialog({ action: "move", items });
		},

		onMultiDownload(items) {
			const url = "files/library/files/download";
			this.pushZippingFile({ url, items: items, context: this });
		},

		onDownload(item) {
			const url = "files/library/files/download";
			if (item) {
				this.pushZippingFile({ url, item: item });
			} else if (this.selectedItem) {
				this.pushZippingFile({ url, item: this.selectedItem, context: this });
			}
		},

		addToFavorites(items) {
			if (!items) {
				const { id, name, type } = this.selectedItem;
				items = [{ id, name, type }];
			}
			const onFinished = (items) =>
				items.forEach((item) => this.updatePersonalFile(item, "favorites"));
			const url = "files/library/actions/activities";
			const fileManagement = new FileManagement(this);
			fileManagement.toggleFavorites(url, items, onFinished);
		},

		onScroll(event) {
			const element = event.target;
			if (element.scrollTop > 100) {
				const isScollInBottom =
					element.scrollHeight - (element.scrollTop + 100) <=
					element.clientHeight;
				if (isScollInBottom && !this.isPaginating) {
					this.isPaginating = true;
					const { slug } = this.$route.params;
					if (slug) {
					} else {
						this.paginateParentItems();
					}
				}
			}
		},

		async paginateParentItems() {
			try {
				const { company, page } = this.currentPage;
				if (company) {
					const currentPage = page + 1;
					const url = `files/library?company_id=${company.id}&page=${currentPage}`;
					const { data } = await this.$axios.get(url);
					const items = data.data;
					const folders = [];
					const files = [];
					for (let index = 0; index < items.length; index++) {
						const item = items[index];
						if (item.type == "folder") {
							folders.push(item);
						} else {
							files.push(item);
						}
					}
					const currentFolders = this.currentPage.folders;
					const currentFiles = this.currentPage.files;
					const allFolder = [...currentFolders, ...folders];
					const allFiles = [...currentFiles, ...files];
					this.currentPage.folders = allFolder;
					this.currentPage.files = allFiles;
					this.currentPage.page = currentPage;
				}
			} catch (_) {}
			this.isPaginating = false;
		},

		// rename items
		onItemRename() {
			const { id } = this.selectedItem;
			const selectedItem = this.$refs[id];
			if (Array.isArray(selectedItem)) {
				selectedItem[0].toggleNameInput();
			} else if (selectedItem) {
				selectedItem.toggleNameInput();
			}
		},

		// rename items
		onItemRename() {
			const { id } = this.selectedItem;
			const selectedItem = this.$refs[id];
			if (Array.isArray(selectedItem)) {
				selectedItem[0].toggleNameInput();
			} else if (selectedItem) {
				selectedItem.toggleNameInput();
			}
		},

		//  Open Folder in the current Page or In New Tab
		openFolder({ isNewWindow, item }) {
			let id = null;
			if (item && item.id) {
				id = item.id;
			} else {
				id = this.selectedItem?.id;
			}
			if (id) {
				const url = `/files/library/drive/${id}`;
				if (isNewWindow) {
					window.open(url, "_blank").focus();
				} else {
					this.$router.push(url);
				}
			}
		},

		/** =======> Common Operation Methods <====== */
		onProperty() {
			this.properties = true;
			this.propertiesData = this.selectedItem;
		},

		dbClickHandler(file_id) {
			this.showPreviewDialog = true;
			this.$refs.filePreviewRef.fetchFileInfo(file_id);
		},
		//  Display context menu on folder right clicked
		onItemRightClick({ event, item }) {
			return;
			this.selectedItem = item;
			const options = {
				type: "folderMenuItems",
				showMenu: true,
				positionX: event.clientX,
				positionY: event.clientY,
				favorites: item.favorites,
			};
			this.$root.$emit("toggleContextMenu", options);
		},

		folderCreated(folder) {
			this.currentPage?.folders?.unshift(folder);
		},

		/** =======> Current Page Methods <====== */

		// display context menu on current page right clicked
		onRightClick(event) {
			return;
			event.preventDefault();
			const options = {
				type: "pageMenuItems",
				showMenu: true,
				positionX: event.clientX,
				positionY: event.clientY,
			};
			this.$root.$emit("toggleContextMenu", options);
		},

		theAction(event) {
			// if (event.srcKey == "ctrlA") {
			// 	this.selectAll(true);
			// 	return;
			// }
			// let selects = this.getSelected();
			// const element = document.querySelector(".file-content");
			// if (selects?.length == 1) {
			// 	let container_width = this.$refs.container.offsetWidth - 64;
			// 	let items = [...this.currentPage.folders, ...this.currentPage.files];
			// 	let index = items.findIndex((item) => item.id == selects[0].id);
			// 	if (index != -1) {
			// 		items[index].isSelected = false;
			// 		if (this.layout == "grid") {
			// 			let step = Math.floor(container_width / 296);
			// 			switch (event.srcKey) {
			// 				case "up":
			// 					if (index - step >= 0) {
			// 						items[index - step].isSelected = true;
			// 					} else {
			// 						items[0].isSelected = true;
			// 					}
			// 					break;
			// 				case "down":
			// 					if (index + step < items.length) {
			// 						items[index + step].isSelected = true;
			// 					} else {
			// 						items[items.length - 1].isSelected = true;
			// 					}
			// 					break;
			// 				case "right":
			// 					if (index + 1 < items.length) {
			// 						items[index + 1].isSelected = true;
			// 					} else {
			// 						items[items.length - 1].isSelected = true;
			// 					}
			// 					break;
			// 				case "left":
			// 					if (index - 1 >= 0) {
			// 						items[index - 1].isSelected = true;
			// 					} else {
			// 						items[0].isSelected = true;
			// 					}
			// 					break;
			// 			}
			// 		} else if (this.layout == "list") {
			// 			switch (event.srcKey) {
			// 				case "up":
			// 					if (index - 1 >= 0) {
			// 						items[index - 1].isSelected = true;
			// 					} else {
			// 						items[0].isSelected = true;
			// 					}
			// 					break;
			// 				case "down":
			// 					if (index + 1 < items.length) {
			// 						items[index + 1].isSelected = true;
			// 					} else {
			// 						items[items.length - 1].isSelected = true;
			// 					}
			// 					break;
			// 			}
			// 		}
			// 	}
			// 	if (element) {
			// 		let elToScroll = this.$refs[this.getSelected()[0].id][0].$el;
			// 		element.scrollTo({
			// 			top:
			// 				elToScroll.offsetTop -
			// 				(element.offsetHeight / 2 - elToScroll.offsetHeight / 2),
			// 		});
			// 	}
			// }
		},

		selectAll(isSelected) {
			return;
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

		emptySelected() {
			return;
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

		toggleShareModal() {
			return;
			this.shareModal = !this.shareModal;
		},

		onShare(selected) {
			this.shareModal = true;
			if (selected?.length > 0) {
				this.itemsToShare = selected;
			} else {
				this.itemsToShare = [this.selectedItem.id];
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
		CopyModal,
		ToggleListGrid,
		ListView,
		RightClickMenu,
		FilePreview,
		Properties,
		TopSelectActionMenu,
		ShareModal,
		FilePasswordModel,
	},
};
</script>
<style scoped>
.file-list-row {
	transition: all 0.4s;
}
.file-list-row:hover {
	box-shadow: 0px 3px 20px -1px rgb(85 85 85 / 8%),
		0px 6px 20px 0px rgb(85 85 85 / 6%), 0px 1px 30px 0px rgb(85 85 85 / 3%) !important;
}
.file-content {
	position: absolute;
	top: 22px;
	bottom: 201px;
	left: 0px;
	right: 0px;
	overflow-y: auto;
}

.show-top-bar {
	top: 60px !important;
}
.custom_skeleton .v-skeleton-loader__image {
	height: 100px !important;
	border-radius: 0;
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

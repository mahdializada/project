<template>
	<client-only>
		<MenuItems
			:hide-btn="true"
			:activeChildItem="activeChildItem"
			v-model="contextMenu.showMenu"
			:position-x="contextMenu.positionX"
			:position-y="contextMenu.positionY"
			icon="mdi-dots-vertical"
			:items="items"
		/>
	</client-only>
</template>

<script>
import MenuItems from "./MenuItems.vue";
import { mapState, mapMutations } from "vuex";

export default {
	components: { MenuItems },
	props: {
		activeChildItem: null,
		selected: {
			type: Array,
		},
	},
	created() {
		this.$root.$on("toggleContextMenu", this.toggleMenuItem);
	},

	data() {
		return {
			/** OPTION MENU DATA */
			contextMenu: {
				type: "optionMenuItems",
				showMenu: false,
				positionY: 0,
				positionX: 0,
			},
			showShareDelete: true,
			defaultItems: {
				create_folder: {
					icon: "mdi-folder-plus",
					title: "create_folder",
					click: this.onCreateMenuClicked,
					divider: true,
				},
				upload_file: {
					icon: "mdi-tray-arrow-up",
					title: "upload_file",
					click: this.onCreateMenuClicked,
				},
				upload_folder: {
					icon: "mdi-folder-upload",
					title: "upload_folder",
					click: this.onCreateMenuClicked,
					divider: true,
				},
				sort_by: {
					icon: "mdi-order-bool-ascending",
					title: "sort_by",
					click: this.onchangeLayout,
					divider: true,
					children: [
						{
							icon: "mdi-sort-alphabetical-descending",
							title: "name",
							click: this.handleSortBy,
						},
						{
							icon: "mdi-scale",
							title: "size",
							click: this.handleSortBy,
						},
						{
							icon: "mdi-sort-calendar-ascending",
							title: "date_modified",
							click: this.handleSortBy,
						},
					],
				},
				thumbnail: {
					icon: "mdi-grid",
					title: "thumbnail",
					click: this.onchangeLayout,
				},
				list: {
					icon: "mdi-text-box-outline",
					title: "list",
					click: this.onchangeLayout,
				},
				refresh: {
					icon: "mdi-refresh",
					title: "refresh",
					click: this.onRefresh,
				},
				download: {
					icon: "mdi-download-multiple",
					title: "download",
					click: this.onDownload,
				},
				multi_download: {
					icon: "mdi-download-multiple",
					title: "download",
					click: this.onMultiDownload,
				},
				add_to_favorites: {
					icon: "mdi-star",
					title: "add_to_favorites",
					click: this.onFavorites,
				},
				multi_add_to_favorites: {
					icon: "mdi-star",
					title: "add_to_favorites",
					click: this.onMultiFavorites,
				},
				multi_remove_from_favorites: {
					icon: "mdi-star-off",
					title: "remove_from_favorites",
					click: this.onRemoveMultiFavorites,
				},
				remove_from_favorites: {
					icon: "mdi-star-off",
					title: "remove_from_favorites",
					click: this.onFavorites,
				},
				share_to: {
					icon: "mdi-share",
					title: "share_to",
					click: this.onShareClick,
				},
				multi_share_to: {
					icon: "mdi-share",
					title: "share_to",
					click: this.onShareClickMulti,
				},
				rename: {
					icon: "mdi-form-textbox",
					title: "rename",
					click: this.onItemRename,
				},
				copy_to: {
					icon: "mdi-content-copy",
					title: "copy_to",
					click: this.onCopy,
				},
				multi_copy_to: {
					icon: "mdi-content-copy",
					title: "copy_to",
					click: this.onMultiCopy,
				},
				move_to: {
					icon: "mdi-content-cut",
					title: "move_to",
					click: this.onMove,
				},
				move_to_trashed: {
					icon: "mdi-delete",
					title: "move_to_trashed",
					click: this.onDelete,
				},
				multi_move_to: {
					icon: "mdi-content-cut",
					title: "move_to",
					click: this.onMultiMove,
				},
				multi_move_to_trashed: {
					icon: "mdi-delete",
					title: "move_to_trashed",
					click: this.onMultiDelete,
				},
				properties: {
					icon: "mdi-information",
					title: "properties",
					click: this.onProperty,
				},
				open: {
					icon: "mdi-folder-open",
					title: "open",
					click: this.onOpen,
				},
				open_file: {
					icon: "mdi-file",
					title: "open",
					click: this.onOpenFile,
				},
				open_in_new_window: {
					icon: "mdi-open-in-new",
					title: "open_in_new_window",
					divider: true,
					click: this.onOpen,
				},
				restore: {
					icon: "mdi-restore",
					title: "restore",
					click: this.onRestore,
				},
				multi_restore: {
					icon: "mdi-restore",
					title: "restore",
					click: this.onRestoreMulti,
				},

				multi_delete: {
					icon: "mdi-delete",
					title: "delete",
					click: this.onMultiDelete,
				},
				delete: {
					icon: "mdi-delete",
					title: "delete",
					click: this.onDelete,
				},
				unshare: {
					icon: "mdi-share-off",
					title: "unshare",
					click: this.onDelete,
				},
				multi_unshare: {
					icon: "mdi-share-off",
					title: "unshare",
					click: this.onMultiDelete,
				},
			},
			items: [],
			empty: [],
		};
	},

	methods: {
		...mapMutations("files", ["changeLayout"]),
		handleSortBy(item) {
			this.$root.$emit("onSortChanged", item);
		},

		onRefresh() {
			this.$root.$emit("onRefreshPage");
		},

		onchangeLayout({ title }) {
			if (title == "list") {
				this.changeLayout("list");
			} else if (title == "thumbnail") {
				this.changeLayout("grid");
			}
		},

		toggleMenuItem(options = null) {
			let data = [];
			const { items } = options;
			items.forEach((key) => {
				if (this.defaultItems[key] !== undefined) {
					data.push(this.defaultItems[key]);
				}
			});
			this.items = data;
			this.contextMenu = options;
		},

		checkForShareDelete({ showShareDelete }) {
			this.showShareDelete = showShareDelete;
		},

		/** MULTI OPTIONS MENU METHODS */

		/** OPTION MENU METHODS */
		onOpen({ title }) {
			let options = {
				isNewWindow: title == "open_in_new_window",
				item: null,
			};
			if (this.selected) {
				options.item = this.selected[0];
			}
			this.$emit("onOpen", options);
		},

		onOpenFile(item) {
			this.$emit("onOpenFile", item);
		},

		onProperty() {
			this.$emit("onProperty");
		},

		onMultiDownload() {
			const selectedItems = this.selected;
			if (selectedItems.length) {
				const map = ({ id, type, name }) => ({ id, type, name });
				const mappedItems = selectedItems.map(map);
				if (selectedItems.length > 1) {
					this.$emit("onMultiDownload", mappedItems);
				} else {
					this.$emit("onDownload", mappedItems[0]);
				}
			}
		},

		onDownload() {
			this.$emit("onDownload");
		},

		onDelete() {
			this.$emit("onDelete");
		},

		onMultiDelete() {
			const selectedItems = this.selected;
			const map = ({ id, type, path, parent_id, user_id, name }) => ({
				id,
				type,
				path,
				parent_id,
				user_id,
				name,
			});
			const mappedItems = selectedItems.map(map);
			this.$emit("onDelete", mappedItems);
			// this.$emit("emptySelected");
		},

		onRestore() {
			this.$emit("onRestore");
		},

		onRestoreMulti() {
			const selectedItems = this.selected;
			const map = ({ id, type }) => ({ id, type });
			const mappedItems = selectedItems.map(map);
			this.$emit("onRestore", mappedItems);
			this.$emit("emptySelected");
		},

		onFavorites() {
			this.$emit("onFavorites");
		},

		onMultiFavorites() {
			const selectedItems = this.selected.filter(
				(item) => item.favorites == false
			);
			const map = ({ id, type, name, favorites, parent_id }) => ({
				id: id,
				type,
				name,
				favorites,
				parent_id,
			});
			const mappedItems = selectedItems.map(map);
			this.$emit("onFavorites", mappedItems);
			this.$emit("emptySelected");
		},

		onRemoveMultiFavorites() {
			const selectedItems = this.selected.filter(
				(item) => item.favorites == true
			);
			const map = ({ id, type, name, favorites }) => ({
				id: id,
				type,
				name,
				favorites,
			});
			const mappedItems = selectedItems.map(map);
			this.$emit("onFavorites", mappedItems);
			this.$emit("emptySelected");
		},

		onShareClick() {
			this.$emit("onShare");
		},

		onShareClickMulti() {
			const selectedItems = this.selected.map((item) => item.id);
			this.$emit("onShare", selectedItems);
		},

		onItemRename() {
			this.$emit("onRename");
		},

		onMultiMove() {
			const selectedItems = this.selected;
			const map = ({ id, type }) => ({ id, type });
			const mappedItems = selectedItems.map(map);
			this.$emit("onMove", mappedItems);
		},

		onMove() {
			this.$emit("onMove");
		},

		onMultiCopy() {
			const selectedItems = this.selected;
			const map = ({ id, type }) => ({ id, type });
			const mappedItems = selectedItems.map(map);
			this.$emit("onCopy", mappedItems);
		},

		onCopy() {
			this.$emit("onCopy");
		},

		/** CREATE MENU METHODS */
		onCreateMenuClicked({ title }) {
			this.$root.$emit("onCreateMenuClicked", title);
		},
	},
};
</script>

<style></style>

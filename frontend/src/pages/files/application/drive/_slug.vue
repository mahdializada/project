<template>
	<div class="position-relative h-screen">
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
		<div
			ref="container"
			:class="`file-content pa-2 bg-light-gray rounded-lg mt-2 ${
				2 < 4 ? 'show-top-bar' : ''
			}`"
			@contextmenu.stop="() => {}"
		>
			<div v-if="!isCompanySelected" class="d-flex justify-center align-center">
				<h4>Please select a company</h4>
			</div>
			<div class="content-section" v-else-if="loading">
				<div class="d-flex flex-wrap px-2">
					<v-skeleton-loader
						v-for="item in 8"
						:key="item"
						style="width: 280px"
						class="ma-1"
						type="list-item-avatar, divider, table-heading"
					></v-skeleton-loader>
				</div>

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
					<div class="d-flex flex-wrap px-2">
						<Folder
							v-for="(folder, folderIndex) in currentPage.folders"
							:key="folder.id"
							:ref="folder.id"
							:folder="folder"
							:index="folderIndex"
							:isSelected="true"
							:link="`/files/application/drive/${folder.sub_system_id}`"
							actionUrl="files/personal/folder/action"
							:parent_id="currentPage.parent && currentPage.parent.id"
							@onRightClick="onItemRightClick"
							@folderCreated="({ name }) => (folder.name = name)"
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
					<div class="d-flex flex-wrap px-2">
						<File
							:isSelected="2 > 3"
							v-for="file in currentPage.files"
							:key="file.id"
							:file="file"
							:ref="file.id"
							:parent_id="currentPage.parent && currentPage.parent.id"
						/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
import { mapMutations, mapState } from "vuex";

import FileBreadCrumb from "~/components/files/common/FileBreadCrumb.vue";
import Folder from "~/components/files/common/Folder.vue";
import File from "~/components/files/common/File.vue";

export default {
	components: {
		FileBreadCrumb,
		Folder,
		File,
	},
	props: {
		loading: {
			type: Boolean,
			default: false,
		},
		isCompanySelected: {
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
		return {};
	},

	methods: {
		...mapMutations("files_select_copy_move", ["changeSelected"]),
		onItemRightClick({ event, item }) {
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
	width: calc(100% - 0px);
	transition: top 0.4s;
	position: absolute;
	top: 18px;
	bottom: 198px;
	left: 0;
	right: 0;
	overflow-y: auto;
}

/* show top bar  */

.show-top-bar {
	top: 20px !important;
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

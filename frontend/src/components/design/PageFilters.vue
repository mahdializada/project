<template>
	<v-card class="w-full mb-2 px-2 py-1" elevation="1" outlined>
		<h4 class="title ma-0 mb-2">{{ $tr("search_filter") }}</h4>
		<h5 class="title2 ms-2">{{ $tr("search_type") }}</h5>

		<div class="filter-container combobox-scrollable">
			<div class="filter-tablet-container">
				<div class="filter-container__left me-3">
					<div class="filter-container__left-radio">
						<v-radio-group
							v-model="searchType"
							row
							style="height: 36px"
							class="ma-0 mx-2"
							mandatory
							@change="onSearchTypeChange"
						>
							<v-radio value="id">
								<template v-slot:label>
									<div class="radio-label">{{ $tr("id") }}</div>
								</template>
							</v-radio>
							<v-radio value="content">
								<template v-slot:label>
									<div class="radio-label">{{ $tr("content") }}</div>
								</template>
							</v-radio>
						</v-radio-group>
					</div>
					<div class="filter-container__left-input">
						<CustomTextField
							v-if="searchType == 'content'"
							:label="$tr('search') + '...'"
							style="width: 300px !important"
							v-model="searchContent"
							@keyup.enter="onSearchContent"
							@click:append="onSearchContent"
							@paste="onSearchContent"
							@blur="onSearchContent"
							append-icon="mdi-magnify"
							class="w-full"
						/>
						<v-combobox
							v-else
							style="width: 300px"
							v-model="searchIds"
							:label="$tr('search') + '...'"
							multiple
							dense
							small-chips
							rounded
							outlined
							height="36"
							class="custom-textfield"
							append-icon="mdi-magnify"
							chips
							hide-details
							deletable-chips
							@keyup.delete="onDeleteChip"
							@keyup.enter="searchById"
						
							
						>
							<template v-slot:selection="{ attrs, item }">
								<v-chip
									class="py-0 mx-0 primary"
									:style="`${
										$vuetify.rtl ? 'margin-left:' : 'margin-right:'
									} 4px !important;`"
									v-bind="attrs"
									small
									close
									@click:close="removeChip(item)"
									color="primary"
									>{{ item }}
								</v-chip>
							</template>
						</v-combobox>
					</div>
				</div>
				<div class="filter-container__middle">
					<CustomButton
						v-if="showFilter"
						icon="fa-filter"
						text="filter_button"
						@click="$emit('onFilter')"
					/>
					<v-dialog persistent v-model="downloadDialog" max-width="800">
						<NewDownloadPopup
							:key="downloadKey"
							@closeDownload="closeDownload"
							:selected_header.sync="selected_header"
							:content="downloadContent"
							:filename="filename"
						/>
					</v-dialog>

					<CustomButton
						@click="$emit('onColumn')"
						icon="fa-columns"
						text="column"
						v-if="showColumn"
					/>
					<!-- <CustomButton @click="$emit('onTab')" icon="fa-columns" text="tab" v-if="showTab" /> -->
					<CustomButton
						v-if="showDownload"
						icon="fa-download"
						text="download"
						@click="$emit('onDownload')"
					/>

					<CustomButton
						v-if="showOrdering"
						text=""
						:isSvg="true"
						icon="/File Manage/File Managment system ICon-02.svg"
						@click="$emit('onOrdering')"
					/>
					<CustomButton
						v-if="showViewType"
						text=""
						:isSvg="true"
						icon="/File Manage/File Managment system ICon-03.svg"
						@click="$emit('onViewType')"
						:isDropdown="true"
						:dropdownItems="dropdownItems"
						@viewStyle="viewStyle"
						@changeSize="changeSize"
					/>
				</div>
			</div>

			<div class="filter-container__right">
				<CustomButton
					v-if="showBulkUpload"
					icon="fa-file-upload"
					text="manual_upload"
					type="primary"
					@click="$emit('onBulkUpload')"
				/>
				<slot />
				<CustomButton
					v-if="showAddLocation"
					icon="fa-warehouse"
					text="add_location"
					type="primary"
				/>
			</div>
		</div>
	</v-card>
</template>
<script>
import CustomButton from "./components/CustomButton.vue";
import CustomTextField from "./components/CustomTextfield.vue";
import DownloadPopUp from "../common/DownloadPopUp.vue";
import NewDownloadPopup from "../common/NewDownloadPopup.vue";
import PopOver from "./PopOver.vue";

export default {
	components: {
		CustomButton,
		CustomTextField,
		DownloadPopUp,
		NewDownloadPopup,
		PopOver,
	},
	props: {
		selected_item: {
			type: Array,
			default: () => {
				return [];
			},
		},
		custom_search: {
			type: Boolean,
			default: false,
		},

		show_graph: {
			type: Boolean,
			default: false,
		},
		filename: {
			type: String,
			required: false,
			default: "Filename",
		},
		selected_header: {
			type: Array,
			required: true,
		},
		downloadContent: {
			type: Array,
			required: true,
		},

		downloadDialog: {
			type: Boolean,
			default: false,
			required: false,
		},

		showBulkUpload: {
			type: Boolean,
			default: false,
		},
		showAddLocation: {
			type: Boolean,
			default: false,
		},

		showColumn: {
			type: Boolean,
			default: true,
		},
		showFilter: {
			type: Boolean,
			default: true,
		},
		showDownload: {
			type: Boolean,
			default: true,
		},
		showViewType: {
			type: Boolean,
			default: false,
		},
		showOrdering: {
			type: Boolean,
			default: false,
		},
		showTab: {
			type: Boolean,
			default: false,
		},
		dropdownItems: {
			type: Array,
		},
	},
	data() {
		return {
			downloadKey: 0,
			searchType: "id",
			searchContent: "",
			prevContent: "",
			searchIds: [],

			existedIds: [],
			isAlreadySearched: false,
			showProfilePopover: false,
		};
	},

	created() {
		this.$root.$on("removeSearchId", (id) => {
			this.searchIds = this.searchIds.filter((row) => row != id);
		});
	},

	methods: {
		closeDownload() {
			this.downloadKey++;
			this.$emit("onDownload");
		},
		viewStyle(key) {
			this.$emit("viewStyle", key);
		},
		changeSize(size) {
			this.$emit("changeSize", size);
		},

		searchById() {	
			if (this.custom_search) {
				if (this.searchIds.length == 0) {
					console.log('from local')
				this.$emit("searchById", '');
				}
			}
			const enteredId = this.searchIds[this.searchIds.length - 1];
			if (enteredId != "" && enteredId != undefined) {
				console.log('from real')

				this.existedIds = this.searchIds;

				this.isAlreadySearched = true;
				this.$emit("searchById", enteredId);
			}
		},

		onSearchContent() {
			if (this.prevContent === this.searchContent && this.isAlreadySearched)
				return;
			else {
				this.$emit("searchByContent", this.searchContent);
				this.prevContent = this.searchContent;
				this.isAlreadySearched = true;
			}
		},

		clearSearchData() {
			this.isAlreadySearched = false;
			this.searchContent = "";
			this.searchIds = [];
			this.existedIds = [];
		},

		onSearchTypeChange() {
			if (
				this.searchType === "id" &&
				this.searchContent != "" &&
				this.isAlreadySearched
			)
				this.$emit("searchByContent", "");
			this.clearSearchData();
			this.$emit("onTypeChange");
		},

		onDeleteChip() {
			
			if (this.searchIds.length < this.existedIds.length) {
				this.$emit(
					"clearAndUnselectId",
					this.existedIds[this.existedIds.length - 1]
				);
				this.existedIds = this.searchIds;
			}
		},

		removeChip(item) {
			this.isAlreadySearched = true;
			this.$emit("clearAndUnselectId", item);
			this.searchIds = this.searchIds.filter((ele) => ele != item);
			this.existedIds = this.searchIds;
		},
	},
};
</script>
<style scoped>
.filter-container {
	display: flex;
	align-items: center;
	flex-wrap: wrap;
	justify-content: space-between;
}

.filter-container__left {
	display: flex;
}

.filter-container__left-input {
	width: 100%;
}

.filter-container__middle {
	display: flex;
	flex-wrap: wrap;
}

.filter-container__right {
	display: flex;
	flex-wrap: wrap;
}

.filter-tablet-container {
	display: flex;
}

@media screen and (max-width: 800px) {
	.filter-container {
		display: block;
	}

	.filter-container__right {
		margin-top: 20px;
		justify-content: center;
	}
}

@media screen and (min-width: 1600px) {
	.filter-container {
		margin-bottom: 20px;
	}

	.filter-container__middle {
		position: absolute;
		left: 50%;
		transform: translate(-50%);
	}
}

@media screen and (max-width: 700px) {
	.filter-tablet-container {
		display: block;
	}

	.filter-container__middle {
		margin-top: 30px;
		justify-content: center;
	}
}

.title,
.title2 {
	font-size: 1rem !important;
	line-height: 1rem !important;
}

.radio-label {
	font-size: 1rem;
	font-weight: 500;
}
</style>
<style>
.v-radio .v-input--selection-controls__input .v-icon.v-icon {
	font-size: 16px;
}

.v-radio .v-input--selection-controls__ripple {
	height: 0;
	width: 0;
}

.w-full .v-radio .v-input--selection-controls__input {
	margin: 0 !important;
}

.w-full
	.v-input.tag-input.w-full.custom-textfield.v-input--hide-details.v-input--dense.v-autocomplete
	.v-input__control {
	min-height: 36px !important;
}

.w-full .v-select.v-select--is-menu-active .v-input__icon--append .v-icon {
	transform: unset !important;
}
</style>

<style>
.combobox-scrollable .v-select__selections {
	overflow: auto;
	flex-wrap: nowrap;
}

.combobox-scrollable ::-webkit-scrollbar {
	height: 0px;
	background: transparent;
}

.combobox-scrollable .v-chip {
	overflow: initial;
}
</style>

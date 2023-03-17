w
<template>
	<v-container fluid class="pa-0">
		<v-row no-gutters>
			<ProgressBar v-if="showProgressBar" />

			<registerReasonStepperVue ref="reasonInsertRef" :slug="slug" />

			<v-dialog
				v-model="showProfile"
				persistent
				width="1300"
				scrollable
				v-if="$isInScope('rn' + $route.params.slug.charAt(0) + 'v')">
				<reason-profile
					:key="profileKey"
					@onEdit="editSelectedReason"
					:profileData="profileData"
					:dialog.sync="showProfile" />
			</v-dialog>
			<!-- Reason Bulk Upload -->
			<v-dialog
				v-model="bulkUploadFlag"
				width="1300"
				v-if="$isInScope('rn' + $route.params.slug.charAt(0) + 'c')">
				<Dialog
					persistent
					max-width="1300"
					@closeDialog="bulkUploadFlag = false">
					<ReasonBulkUpload v-if="bulkUploadFlag" :slug="slug" />
				</Dialog>
			</v-dialog>
			<!-- edit reaason -->
			<EditReaonStepper ref="reasonEditRef" :slug="slug" />
			<!-- edit reason -->
			<v-col cols="12">
				<PageHeader
					:Icon="`reason_list`"
					Title="reasons"
					:Breadcrumb="breadcrumb" />
			</v-col>
			<v-col cols="12">
				<PageFilters
					ref="pageFilterRef"
					:selected_header.sync="headers"
					:downloadContent="reasons"
					@onDownload="toggleDownload"
					:downloadDialog="downloadDialog"
					:filename="$tr('reasons')"
					:showFilter="false"
					@onBulkUpload="onBulkUpload"
					:show-add-note="$isInScope('rnc')"
					@searchById="searchById"
					@clearAndUnselectId="clearAndUnselectId"
					@searchByContent="searchByContent"
					@onTypeChange="onSearchTypeChange"
					:show-bulk-upload="
						$isInScope('rn' + $route.params.slug.charAt(0) + 'c')
					"
					:showDownload="$isInScope('rn' + $route.params.slug.charAt(0) + 'e')"
					:showColumn="false">
					<CustomButton
						@click="toggleReasonRegister"
						icon="fa-plus"
						text="add_reason"
						type="primary" />
				</PageFilters>
			</v-col>
			<v-col cols="12">
				<PageActions
					:selectedItems="selected"
					:showSelect="false"
					:showBlock="false"
					:showApply="false"
					:showDelete="true"
					:defaultAction="false"
					:showView="true"
					@onView="onView"
					@onDelete="onDelete"
					@onEdit="toggleReasonEdit"
					:routeName="'reasons'"
					:showEdit="$isInScope('rn' + $route.params.slug.charAt(0) + 'u')"
					:showAutoEdit="false" />
			</v-col>
			<v-col cols="12">
				<v-row class="mx-0">
					<v-col cols="12 pa-0">
						<v-tabs
							v-model="tabIndex"
							height="40"
							background-color="primary"
							active-class="active-background"
							show-arrows
							dark
							center-active>
							<client-only>
								<v-tab v-for="(item, i) in tabItems" :key="i" class="px-3">
									<span
										:class="`${
											item.isSelected ? 'selected' : 'not-selected'
										} tab-icon`">
										<v-icon left small class="me-1">{{ item.icon }}</v-icon>
									</span>
									<p
										:class="`${
											item.isSelected ? 'selected' : 'not-selected'
										} tab-title text-capitalize mb-0`">
										{{ $tr(item.text) }}
									</p>
									<v-chip
										class="ms-1 tab-chip"
										:color="item.isSelected ? 'primary' : 'white'"
										:text-color="item.isSelected ? 'white' : 'primary'"
										small
										v-text="getTotal" />
								</v-tab>
							</client-only>
						</v-tabs>
					</v-col>
				</v-row>
				<DataTable
					ref="tableRef"
					:headers="headers"
					:items="reasons"
					:ItemsLength="getTotal"
					v-model="selected"
					@click:row="onRowClicked"
					@onTablePaginate="onTableChanges">
					<template v-slot:[`item.code`]="{ item }">
						<span class="mx-1 primary--text font-weight-bold">
							{{ item.code }}
						</span>
					</template>
					<template v-slot:[`item.system.name`]="{ item }">
						<span>
							{{ $tr(item.system.phrase) }}
						</span>
					</template>
					<template v-slot:[`item.title`]="{ item }">
						<v-tooltip bottom max-width="800">
							<template v-slot:activator="{ on, attrs }">
								<span v-bind="attrs" v-on="on">
									<span v-if="item.title.length >= 50"
										>{{ item.title.substring(0, 49) }} ...
									</span>
									<span v-else>{{ item.title.substring(0, 49) }} </span>
								</span>
							</template>
							<span>{{ item.title }}</span>
						</v-tooltip>
					</template>
				</DataTable>
			</v-col>
		</v-row>
	</v-container>
</template>

<script>
import ProgressBar from "~/components/common/ProgressBar";
import PageHeader from "~/components/design/PageHeader";
import PageFilters from "~/components/design/PageFilters";
import PageActions from "~/components/design/PageActions";
import DataTable from "~/components/design/DataTable";
import Reasons from "~/configs/pages/reason";
import CustomButton from "~/components/design/components/CustomButton.vue";
import { mapGetters, mapActions } from "vuex";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import EditReason from "~/components/statusManagement/reasons/EditReason";
import ReasonProfile from "~/components/statusManagement/reasons/ReasonProfile";
import RegisterReason from "~/components/statusManagement/reasons/RegisterReason";
import ReasonBulkUpload from "~/components/statusManagement/reasons/ReasonBulkUpload";
import registerReasonStepperVue from "../../../components/statusManagement/reasons/registerReasonStepper.vue";
import HandleException from "~/helpers/handle-exception";
import EditReaonStepper from "../../../components/statusManagement/reasons/EditReaonStepper.vue";

export default {
	meta: {
		hasAuth: true,
		scope: ["rnuv", "rnmv", "rncv", "rnav", 'rnad'],
	},
	name: "reason",
	async asyncData({ store, params }) {
		// await store.dispatch("reasons/fetchReasons", {
		//   slug: params.slug,
		// });
		return { slug: params.slug };
	},

	components: {
		CustomButton,
		DataTable,
		PageActions,
		PageFilters,
		PageHeader,
		ProgressBar,
		Dialog,
		ReasonProfile,
		EditReason,
		RegisterReason,
		ReasonBulkUpload,
		registerReasonStepperVue,
		EditReaonStepper,
	},

	data() {
		return {
			selected: [],
			tabItems: Reasons(this).tabItems,
			headers: Reasons(this).headers,
			breadcrumb:
				this.$route.params.slug === "user"
					? Reasons(this).breadcrumb_user
					: this.$route.params.slug === "master"
					? Reasons(this).breadcrumb_master
					: this.$route.params.slug === "content"
					? Reasons(this).breadcrumb_landing
					: this.$route.params.slug === "personal"
					? Reasons(this).breadcrumb_personal
          : this.$route.params.slug === "advertisement"
					? Reasons(this).breadcrumb_advertisement
					: [],
			showProgressBar: false,
			tabIndex: 0,
			key: 0,
			type: 1,
			content: [],
			contentData: "",
			bulkUploadFlag: false,

			// Edit and Auto Edit
			editKey: 0,
			editItems: [],
			editDialog: false,
			isAutoEdit: false,

			// Profile
			showProfile: false,
			profileData: {},
			profileKey: 0,
			downloadDialog: false,
		};
	},

	async validate({ params }) {
		return !params.slug ? false : true;
	},

	computed: {
		...mapGetters({
			getTranslations: "translations/getTranslations",
			reasons: "reasons/get_reasons",
			getTotal: "reasons/get_total",
		}),
	},
	async created() {
		const slug = this.$route.params.slug;
		this.fetchReasons({
			slug: slug,
		});
	},
	methods: {
		...mapActions({
			fetchReasons: "reasons/fetchReasons",
			fetchTranslations: "translations/fetchTranslations",
			fetchReasonPagination: "reasons/fetchReasonPaginate",
			searchReasons: "reasons/searchReasons",
		}),
		// toggle bulk upload dialogue
		onBulkUpload() {
			this.bulkUploadFlag = !this.bulkUploadFlag;
		},
		// add or remove item from selected items
		onRowClicked(item) {
			let items = new Set(this.selected);
			items.has(item) ? items.delete(item) : items.add(item);
			this.selected = Array.from(items);
		},

		async onTableChanges(options) {
			this.getRecord();
		},

		toggleDownload() {
			this.downloadDialog = !this.downloadDialog;
		},

		async viewSelectedReason(item) {
			this.profileData = item;
			this.showProfile = true;
		},

		async editSelectedReason(item) {
			this.showProgressBar = true;
			this.showProfile = false;
			this.editKey++;
			this.editItems[0] = this._.clone(item);
			this.editDialog = true;
			this.showProgressBar = false;
		},

		onDelete() {
			if (this.selected.length > 1) {
				this.$toasterNA("orange", this.$tr("item_max_limit_text"));
			} else {
				this.$TalkhAlertNA(
					"Do you want to delete this record?", //text
					"delete", //icon
					async () => {
						try {
							const response = await this.$axios.delete(
								`reasons/${this.selected[0].id}`, 
                {params: this.slug}
							);
							if (response.status == 200) {
								this.fetchReasons({ slug: this.slug });
								this.$toasterNA("green", this.$tr("successfully_deleted"));
							}
              this.selected = []
						} catch (error) {
								this.$toasterNA("red", this.$tr("something_went_wrong"));

            }
					}, // callback function
					"delete", // btn text
					"swal_remove_text", //info text
				);
			}
		},

		onView() {
			this.profileKey++;
			if (this.selected.length == 1) {
				this.profileData = this.selected[0];
				this.showProfile = true;
			} else {
				this.$toasterNA("orange", this.$tr("view_item_max_limit_text"));
			}
		},
		// search functions
		async getRecord() {
			try {
				const data = this.fetchOptions();
				await this.fetchReasons(data);
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
		async searchById(id) {
			this.searchId = id;
			try {
				const options = this.fetchOptions();
				const response = await this.searchReasons(options);
				this.selectData(response);
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
		selectData(response) {
			if (response != null) {
				this.selected?.unshift(response);
				// this.onRowClicked(data);
			} else {
				this.$root.$emit("removeSearchId", this.searchId);
			}
		},
		fetchOptions() {
			let data = { slug: this.slug };
			if (!this.$isObjectEmpty(this.filterData))
				Object.assign(data, this.filterData);
			if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);
			if (this.searchContent != "") data.searchContent = this.searchContent;
			if (this.searchId != "") data.code = this.searchId;
			return data;
		},
		clearAndUnselectId(id) {
			this.selected = this.selected.filter((row) => row?.code != id);
		},
		searchByContent(searchContent) {
			this.searchContent = searchContent;
			this.getRecord();
		},
		onSearchTypeChange() {
			this.selected = [];
			this.searchContent = "";
			this.searchId = "";
		},
		toggleReasonRegister() {
			this.$refs.reasonInsertRef.showDialog();
		},
		toggleReasonEdit() {
			this.$refs.reasonEditRef.showDialog(this.selected[0]);
		},
	},
};
</script>

<style scoped></style>

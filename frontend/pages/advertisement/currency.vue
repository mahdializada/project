<template>
	<v-container fluid class="pa-0">
		<client-only>
			<ProductStudyFilter
				ref="productStudyFilter"
				@filterRecords="onFilterRecords"
			/>

			<dailyRateStepper
				v-if="$isInScope('advappsc') || $isInScope('advappsu')"
				ref="dailyRateStepperRef"
				@fetchNewData="fetchDataAccordingtoStatus"
			/>

			<v-dialog v-model="columnDialog" width="1300" persistent>
				<CustomizeColumn
					@saveChanges="
						(data) => {
							all_headers[0] = data;
							all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
							columnDialog = false;
						}
					"
					:page_name="'applications_all'"
					@closeColumnDialog="columnDialog = false"
				/>
			</v-dialog>

			<v-row no-gutters>
				<v-col cols="12">
					<PageHeader
						Icon="applications"
						Title="currency"
						:Breadcrumb="breadcrumb"
					>
					</PageHeader>
				</v-col>
				<v-col cols="12">
					<PageFilters
						ref="pageFilterRef"
						:selected_header.sync="all_headers[0].selected_headers"
						:downloadContent="dailyRateRecords"
						:downloadDialog="downloadDialog"
						@onFilter="openFilterDialog"
						filename="application"
						@onDownload="
							() => {
								downloadDialog = !downloadDialog;
							}
						"
						@searchById="searchById"
						@clearAndUnselectId="clearAndUnselectId"
						@searchByContent="searchByContent"
						@onTypeChange="onSearchTypeChange"
						@onColumn="columnDialog = true"
						note-title=""
						:showBulkUpload="false"
						:showDownload="$isInScope('advappse')"
					>
						<CustomButton
							v-if="$isInScope('advappsc')"
							icon="fa-plus"
							@click="openInsertDialog"
							:text="$tr('create_item', $tr('daily_rate'))"
							type="primary"
						/>
					</PageFilters>
				</v-col>

				<v-col cols="12">
					<CustomPageActions
						:showView="false"
						:showEdit="false"
						:showAutoEdit="false"
						:showDelete="$isInScope('advappsd')"
						:ignoreReason="true"
						:showSelect="true"
						:showApply="true"
						:showAssign="false"
						:showApprove="false"
						:showUnAssign="false"
						:showRestore="false"
						:selectedItems.sync="selectedItems"
						:tab-key.sync="tabKey"
						:showBlock="false"
						@onDelete="onDelete"
						:statusItems="statusItems"
						noReasonSubmitTabs="pending"
						noReasonSubmitOperations="active"
						:routeName="'advertisement/currencies'"
						subSystemName="Column Category"
						notAllowedTabs="all"
						@fetchNewData="fetchDataAccordingtoStatus"
						@onEdit="onEdit"
					>
					</CustomPageActions>
				</v-col>
				<v-col cols="12">
					<v-col cols="12 pa-0">
						<TableTabs
							@getSelectedTabRecords="
								(key) => {
									tabKey = key;
									setStatusItems(tabKey);
									fetchDataAccordingtoStatus();
								}
							"
							:tabData="tabItems"
							:extraData="extraData"
						/>
					</v-col>

					<DataTable
						ref="projectTableRef"
						:headers="all_headers[0].selected_headers"
						:items="dailyRateRecords"
						:ItemsLength="getTotals(tabKey)"
						v-model="selectedItems"
						:loading="apiCalling"
						@onTablePaginate="onTableChanges"
						@click:row="onRowClicked"
						:key="tableKey"
					>
						<template v-slot:[`item.currency_from`]="{ item }">
							<span class="mx-1">
								{{ item.currency_from.name }}
							</span>
						</template>
						<template v-slot:[`item.currency_to`]="{ item }">
							<span class="mx-1">
								{{ item.currency_to.name }}
							</span>
						</template>
						<template v-slot:[`item.created_by`]="{ item }">
							<span class="mx-1" v-if="item.creator !== null">
								{{ item.creator.firstname + " " + item.creator.lastname }}
							</span>
						</template>
					</DataTable>
				</v-col>
			</v-row>
			<br />
			<br />
			<br />
			<br />
			<br />
		</client-only>
	</v-container>
</template>

<script>
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import DesignRequestOperation from "~/components/landing/DesignRequestOperation";
import Currency from "~/configs/pages/advertisement/currency";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import DesignRequestFilter from "~/components/landing/DesignRequestFilter.vue";
import Profile from "~/components/landing/profile.vue";
import LandingNoteView from "~/components/landing/Orders/LandingNoteView.vue";
import RejectionReview from "~/components/landing/Orders/RejectionReview.vue";
import DesignRequestChart from "~/components/landing/DesignRequestChart.vue";
import DesignRequestAutoReview from "~/components/landing/DesignRequestAutoReview.vue";
import HandleException from "~/helpers/handle-exception";
import ProductStudyFilter from "~/components/single_sales_management_system/ProductStudyFilter.vue";
import dailyRateStepper from "../../components/advertisement/currency/dailyRateStepper.vue";
import Alert from "../../helpers/alert";
import ApplicationAssign from "../../components/advertisement/ApplicationAssign.vue";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
	meta: {
		hasAuth: true,
		scope: "advappsv",
	},

	components: {
		CustomPageActions,
		CustomButton,
		DesignRequestEdit,
		PageHeader,
		PageFilters,
		Dialog,
		DataTable,
		CustomizeColumn,
		DesignRequestOperation,
		DesignRequestFilter,
		Profile,
		LandingNoteView,
		RejectionReview,
		DesignRequestChart,
		DesignRequestAutoReview,
		ProductStudyFilter,
		dailyRateStepper,
		ApplicationAssign,
		TableTabs,
	},

	data() {
		return {
			dailyRateRecords: [],
			tabItems: Currency(this).tabItems,
			breadcrumb: Currency(this).breadcrumb,
			selectedItems: [],
			tableKey: 0,
			options: {},
			downloadDialog: false,
			// profile section
			showProfile: false,
			// for custom columns
			apiCalling: false,
			autoEdit: false,
			editKey: 0,
			autoEditData: [],
			content: [],
			contentData: "",
			filterData: {},
			profileData: {},

			note_title: "",
			isEdit: false,
			isAutoEdit: false,
			editData: {},
			isRedirecting: false,

			selected_headers: [],
			columnDialog: false,
			all_headers: [
				{
					key: "all",
					selected_headers: [],
					headers: [],
				},
			],
			extraData: {},
			statusItems: [],
			tabKey: "all",
		};
	},

	watch: {
		tabIndex: function () {
			this.fetchDataAccordingtoStatus();
		},
	},

	mounted() {
		this.isRedirecting = false;
	},

	async created() {
		this.isRedirecting = false;
		this.createApplication();
		this.reAuthentication();
		await this.fetchHeaders();
		this.fetchDataAccordingtoStatus();
	},

	methods: {
		async fetchHeaders() {
			try {
				if (this.all_headers[0].selected_headers != []) {
					const response = await this.$axios.get("sub-system-header", {
						params: {
							tab_name: "currency_all",
						},
					});
					this.all_headers[0].selected_headers = response.data.selected_headers;
					this.all_headers[0].headers = response.data.headers;
				}
			} catch (error) {}
		},

		async onRestore() {
			// if (this.getTabKey() == "removed") {
			// 	try {
			// 		const ids = this.selectedItems.map(({ id }) => id);
			// 		const url = `advertisement/applications/update?restore=true`;
			// 		const { data } = await this.$axios.put(url, { ids });
			// 		if (data.result) {
			// 			this.$toastr.s(this.$tr("successfully_restored"));
			// 		} else {
			// 			this.$toasterNA("red", this.$tr('something_went_wrong'));
			// 		}
			// 	} catch (error) {
			// 		this.$toasterNA("red", this.$tr('something_went_wrong'));
			// 	}
			// } else {
			// }
		},

		onAssign() {
			if (this.selectedItems.length == 1) {
				this.$refs.applicationAssingRef.toggle(this.selectedItems);
			} else if (this.selectedItems.length < 1) {
				// this.$toastr.e(this.$tr("please_select_an_item_first"));
				this.$toasterNA("red", this.$tr('please_select_an_item_first'));

			} else if (this.selectedItems.length > 1) {
				// this.$toastr.e(this.$tr("please_select_one_option"));
				this.$toasterNA("red", this.$tr('please_select_one_option'));

			}
		},

		assignData(application) {
			this.dailyRateRecords = this.dailyRateRecords.map((item) => {
				if (item.id == application.id) {
					item = application;
				}
				return item;
			});
			this.selectedItems = this.selectedItems.map((item) => {
				if (item.id == application.id) {
					item = application;
				}
				return item;
			});
		},

		async redirectToAuthenticate() {
			if (this.isRedirecting) return;
			const len = this.selectedItems.length;
			if (len === 1) {
				try {
					this.isRedirecting = true;
					const item = this.selectedItems[0];
					const url = `/advertisement/applications/${item.id}/redirect`;
					const { data } = await this.$axios.post(url);
					sessionStorage.setItem("application_id", item.id);
					window.open(data.redirect, "_parent");
				} catch (error) {
					this.isRedirecting = false;
					// this.$toastr.e(this.$tr("application_authentication_failed"));
				this.$toasterNA("red", this.$tr('application_authentication_failed'));

				}
			} else {
        this.$toasterNA("red" , this.$tr('cant_view_more_than_one_item_at_the_same_time'))
			}
		},

		async reAuthentication() {
			const application_id = sessionStorage.getItem("application_id");
			const { code } = this.$route.query;
			if (code && application_id) {
				try {
					this.isRedirecting = true;
					const url = `/advertisement/applications/${application_id}/authenticate`;
					const { data } = await this.$axios.post(url, { code });
					if (data.result) {
						this.$router.replace({ query: null });
						// this.$toastr.s(this.$tr("application_authenticated_successfully"));
				this.$toasterNA("green", this.$tr('application_authenticated_successfully'));

						this.fetchDataAccordingtoStatus();
					} else {
						// this.$toastr.e(this.$tr("invalid_authentication_code"));
				this.$toasterNA("red", this.$tr('invalid_authentication_code'));

					}
				} catch (error) {
					// this.$toastr.e(this.$tr("application_authentication_failed"));
				this.$toasterNA("red", this.$tr('application_authentication_failed'));

				}
				sessionStorage.removeItem("application_id");
				this.isRedirecting = false;
			}
		},

		async createApplication() {
			const applicationData = sessionStorage.getItem("application_data");
			const { code } = this.$route.query;
			if (code && applicationData) {
				console.log(applicationData);
				try {
					const { data } = await this.$axios.post(
						"advertisement/applications",
						{
							code,
							application_data: JSON.parse(applicationData),
						}
					);
					if (data.result) {
						this.$router.replace({ query: null });
						// this.$toastr.s(this.$tr("application_authenticated_successfully"));
				this.$toasterNA("green", this.$tr('application_authenticated_successfully'));

						this.fetchDataAccordingtoStatus();
					} else {
						// this.$toastr.e(this.$tr("invalid_authentication_code"));
				this.$toasterNA("red", this.$tr('invalid_authentication_code'));


					}
				} catch (error) {
					// this.$toastr.e(this.$tr("application_authentication_failed"));
				this.$toasterNA("red", this.$tr('application_authentication_failed'));

				}
				sessionStorage.removeItem("application_data");
			}
		},

		async onDelete() {
			if (this.selectedItems.length > 100) {
				const msg = "cant_do_operation_on_more_than_100_items_at_the_same_time";
				// this.$toastr.w(this.$tr(msg));
				this.$toasterNA("orange",this.$tr(msg));

				return;
			}
			try {
				const ids = this.selectedItems.map(({ id }) => id);
				const url = `advertisement/currency/${ids}`;
				const { data } = await this.$axios.delete(url);
				if (data.result) {
				this.$toasterNA("green", this.$tr('successfully_deleted'));
					// 				this.$toasterNA("green", this.$tr('successfully_deleted'));
;
					this.fetchDataAccordingtoStatus();
				} else {
					// this.$toastr.e(this.$tr(data.message));
				this.$toasterNA("red", this.$tr('data.message'));

				}
			} catch (error) {
				this.$toasterNA("red", this.$tr('something_went_wrong'));
			}
		},

		openInsertDialog() {
			this.$refs.dailyRateStepperRef.toggle();
		},

		openFilterDialog() {
			this.$refs.productStudyFilter.changeModel();
		},

		onFilterRecords(filterData) {
			this.$root.$emit("resetPageNumber");
			this.filterData = filterData;
			this.fetchDataAccordingtoStatus();
		},

		async fetchDataAccordingtoStatus() {
			this.apiCalling = true;
			this.selectedItems = [];
			this.checkSelectedTab(this.tabKey);
			const data = this.fetchOptions();

			const response = await this.$axios.get(`advertisement/currency`, {
				params: data,
			});
			if (response?.status === 200) {
				this.dailyRateRecords = response.data?.data;
				this.extraData = response?.data;
			}

			this.apiCalling = false;
		},

		onTableChanges(options) {
			if (JSON.stringify(options) !== JSON.stringify(this.options)) {
				this.options = this._.clone(options);
				this.fetchDataAccordingtoStatus();
			} else this.options = this._.clone(options);
		},

		searchByContent(searchContent) {
			this.searchContent = searchContent;
			this.fetchDataAccordingtoStatus();
		},

		onSearchTypeChange() {
			this.selectedItems = [];
			this.searchContent = "";
			this.searchId = "";
		},

		clearAndUnselectId(code) {
			this.selectedItems = this.selectedItems.filter((row) => row.id !== code);
		},

		checkSelectedTab(value) {
			this.selectedItems = [];
			let currentTab = "";
			this.tabItems = this.tabItems.map((item, index) => {
				index === value
					? ((item.isSelected = 1), (currentTab = item))
					: (item.isSelected = 0);
				return item;
			});
		},

		async searchById(id) {
			this.searchId = id;
			try {
				const options = this.fetchOptions();
				const response = await this.$axios.get(
					"advertisement/applications/search",
					{
						params: options,
					}
				);
				this.selectData(response);
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},

		selectData(response) {
			if (response.status === 200) {
				const data = response.data;
				if (data != null) {
					this.dailyRateRecords = this.dailyRateRecords.filter(
						(item) => item.id !== data.id
					);
					this.dailyRateRecords?.unshift(data);
					this.selectedItems?.unshift(data);
				}
			} else {
				this.$root.$emit("removeSearchId", this.searchId);
			}
		},

		fetchOptions() {
			let data = {
				key: this.tabKey,
			};
			if (!this.$isObjectEmpty(this.filterData))
				Object.assign(data, this.filterData);

			if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

			if (this.searchContent != "") data.searchContent = this.searchContent;

			if (this.searchId != "") data.code = this.searchId;

			return data;
		},

		fetchNewData() {
			this.selectedItems = [];
			this.fetchDataAccordingtoStatus();
		},

		// add or remove item from selected items
		onRowClicked(item) {
			let items = new Set(this.selectedItems);
			items.has(item) ? items.delete(item) : items.add(item);
			this.selectedItems = Array.from(items);
		},

		getTotals(tabKey) {
			switch (tabKey) {
				case "all":
					return this.extraData?.allTotal || 0;
				case "active":
					return this.extraData?.activeTotal || 0;
				case "removed":
					return this.extraData?.removedTotal || 0;
				case "inactive":
					return this.extraData?.inactiveTotal || 0;
				case "pending":
					return this.extraData?.pendingTotal || 0;
			}
		},

		setStatusItems(tabKey) {
			if (tabKey == "pending")
				this.statusItems = [{ id: "active", name: "active" }];
			else this.statusItems = [];
		},

		onEdit() {
			console.log("ds");
		},
	},
};
</script>

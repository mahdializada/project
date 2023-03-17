<template>
	<!-- main components in a page -->
	<v-container fluid class="pa-0">
		<ProductFilter ref="ProductsFilterRef" @filterRecords="onFilterRecords" />
		<ProductInsertion ref="productInsertionRef" @pushRecord="pushRecord" />
		<StepperComponent ref="productInsertionRefStep" @pushRecord="pushRecord" />
		<!-- <productUpdateStepper
      ref="productUpdateStepperRef"
      @getRecord="getRecord"
    ></productUpdateStepper> -->
				
		<v-dialog v-model="columnDialog" width="1300" persistent>
			<CustomizeColumn
				@saveChanges="
					(data) => {
						all_headers[0] = data;
						all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
						columnDialog = false;
					}
				"
				:page_headers.sync="all_headers[0]"
				:page_name="'products_all'"
				@closeColumnDialog="columnDialog = false"
			/>
		</v-dialog>

		<client-only>
			<v-row no-gutters>
				<ProgressBar v-if="showProgressBar" />
				<v-col cols="12">
					<PageHeader
						:Icon="`products`"
						:Title="breadcrumb[1].text"
						:Breadcrumb="breadcrumb"
					>
					</PageHeader>
				</v-col>

				<!--start  page filter  -->
				<PageFilters
					ref="pageFilterRef"
					:selected_header.sync="all_headers[0].selected_headers"
					:downloadContent="tableValues"
					:downloadDialog="downloadDialog"
					:filename="$tr('products')"
					:show-add-note="true"
					@onFilter="openFilterDialog"
					:showDownload="$isInScope('adve')"
					note-title="add_project_note"
					@onDownload="
						() => {
							downloadDialog = !downloadDialog;
						}
					"
					@searchById="searchById"
					@clearAndUnselectId="clearAndUnselectId"
					@searchByContent="searchByContent"
					@onTypeChange="() => {}"
					@onColumn="columnDialog = true"
				>
					<CustomButton
						v-if="$isInScope('advc')"
						icon="fa-plus"
						@click="openInsertDialog"
						:text="$tr('create_item', $tr('product'))"
						type="primary"
					/>
				</PageFilters>
				<!--end  page filter  -->

				<v-col cols="12">
					<CustomPageActions
						:showView="$isInScope('advv')"
						:showEdit="true"
						@fetchNewData="fetchNewData"
						:showAutoEdit="false"
						:showDelete="$isInScope('advd')"
						:showSelect="$isInScope('advu')"
						:showApply="$isInScope('advu')"
						:selectedItems.sync="selectedItems"
						:showRestore="showRestore"
						:tab-key.sync="tabKey"
						:showBlock="false"
						:statusItems="statusItems"
						:routeName="'single-sales/products-ssp'"
						:subSystemName="'products'"
						:noReasonSubmitOperations="'comming_soon , now'"
						@onEdit="onEditProduct"
					/>
				</v-col>
				<v-col cols="12">
					<TableTabs
						@getSelectedTabRecords="
							(key) => {
								tabKey = key;
								getRecord();
							}
						"
						:tabData="tabItems"
						:extraData="extraData"
					/>

					<DataTable
						ref="projectTableRef"
						:headers="all_headers[0].selected_headers"
						:items="tableValues"
						v-model="selectedItems"
						:ItemsLength="getTotals(tabItems[tabIndex].key)"
						:loading="apiCalling"
						@onTablePaginate="onTableChanges"
						@click:row="onRowClicked"
						:item-class="completedStyle"
						:key="tableKey"
					>
						<template v-slot:[`item.code`]="{ item }">
							<span
								@click="() => viewSelectedDesignProduct(item)"
								class="mx-1 primary--text font-weight-bold"
							>
								{{ item.code }}
							</span>
						</template>
						<template v-slot:[`item.product_img`]="{ item }">
							<v-menu offset-y open-on-hover :close-on-content-click="false">
								<template v-slot:activator="{ on, attrs }">
									<span
										style="white-space: nowrap"
										v-bind="attrs"
										v-on="on"
										class="mb-1"
									>
										<v-img
											v-on:mouseover="previewLogo"
											v-on:mouseleave="leaveMouse"
											class="rounded-circle"
											width="30"
											height="30"
											lazy-src="https://picsum.photos/id/11/10/6"
											:src="item.product_img"
										></v-img>
									</span>
								</template>
								<span>
									<v-img
										v-if="isPreview"
										width="400"
										lazy-src="https://picsum.photos/id/11/10/6"
										:src="item.product_img"
									></v-img>
								</span>
							</v-menu>
						</template>
						<template v-slot:[`item.description`]="{ item }">
							<v-tooltip bottom max-width="200">
								<template v-slot:activator="{ on, attrs }">
									<span
										v-bind="attrs"
										v-on="on"
										style="white-space: nowrap"
										class="primary--text"
									>
										<span class="mx-auto d-flex justify-center">
											{{ item.description.substring(0, 30) + " ..." }}
										</span>
									</span>
								</template>
								{{ item.description }}
							</v-tooltip>
						</template>
						<template v-slot:[`item.is_published`]="{ item }">
							<span class="mx-1 ps-5">
								{{ item.is_published ? $tr("yes") : $tr("no") }}
							</span>
						</template>
					</DataTable>
				</v-col>
				<progress-bar v-if="isFetchCancels" />
			</v-row>
		</client-only>
	</v-container>
</template>

<script>
import { mapActions, mapGetters, mapState } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import product from "~/configs/pages/product";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import ColumnHelper from "~/helpers/column-helper";
import TableTabs from "~/components/common/TableTabs.vue";
import ProductFilter from "~/components/single_sales_management_system/productFilter.vue";
import ProductInsertion from "../../../components/single_sales_management_system/ProductInsertion.vue";
import StepperComponent from "../../../components/single_sales_management_system/products/StepperComponent";
import productUpdateStepper from "../../../components/single_sales_management_system/products/updateSteps/productUpdateStepper.vue";

export default {
	components: {
		CustomPageActions,
		PageActions,
		CustomButton,
		PageHeader,
		PageFilters,
		DataTable,
		ProgressBar,
		TableTabs,
		ProductFilter,
		CustomizeColumn,
		ProductInsertion,
		StepperComponent,
		productUpdateStepper,
	},
	data() {
		return {
			tabKey: "all",
			showRestore: false,
			tableValues: [],

			tabItems: product(this).tabItems,
			breadcrumb: product(this).breadcrumb,
			selectedItems: [],
			tableKey: 0,
			tabIndex: 0,
			options: {},
			extraData: {},
			//register section
			statusItems: [],
			// profile section
			showProfile: false,
			// for custom columns
			selected_headers: [],
			columnDialog: false,
			all_headers: [
				{
					key: "all",
					selected_headers: [],
					headers: [],
				},
			],

			apiCalling: false,
			isFetchCancels: false,
			showProgressBar: false,
			// download dialog
			downloadDialog: false,
			// search
			searchId: "",
			searchContent: "",
			filterData: {},

			isPreview: false,
			timer: null,
			overlay: false,
			overlayLogo: "",
		};
	},
	async created() {
		this.$root.$on("closeEdit", this.toggleEdit);
		//customize columns
		await this.fetchHeaders();
	},
	watch: {
		tabKey: function (index) {
			this.checkSelectedTab(index);
		},
	},

	async fetch() {
		await this.getRecord();
	},

	methods: {
		async fetchHeaders() {
			try {
				if (this.all_headers[0].selected_headers != []) {
					const response = await this.$axios.get("sub-system-header", {
						params: {
							tab_name: "products_all",
						},
					});
					this.all_headers[0].selected_headers = response.data.selected_headers;
					this.all_headers[0].headers = response.data.headers;
				}
			} catch (error) {}
		},

		// leaveMove on logo
		async leaveMouse() {
			if (this.timer) {
				await clearTimeout(this.timer);
				this.isPreview = false;
				this.timer = null;
			}
		},
		// preview logo on mouse hover
		async previewLogo() {
			let vm = this;
			vm.isPreview = false;
			if (vm.timer) {
				await clearTimeout(vm.timer);
				vm.timer = null;
			}
			vm.timer = await setTimeout(function () {
				vm.isPreview = true;
			}, 800);
		},
		openInsertDialog() {
			// this.$refs.productInsertionRef.toggle();
			this.$refs.productInsertionRefStep.showDialog();
		},
		viewSelectedDesignProduct(item) {
			this.showProfile = true;
		},
		// getTotal(tabKey) {
		// 	switch (tabKey) {
		// 		case "now":
		// 			return this.extraData?.availableTotal || 0;
		// 		case "comming_soon":
		// 			return this.extraData?.commingSoonTotal || 0;
		// 		case "removed":
		// 			return this.extraData?.removedTotal || 0;
		// 		default:
		// 			return this.extraData?.allTotal || 0;
		// 	}
		// },
		getTotals(tabKey) {
      switch (tabKey) {
        case "now":
          return this.extraData?.availableTotal || 0;
        case "comming_soon":
          return this.extraData?.commingSoonTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
		  default:
          return this.extraData?.allTotal || 0;
      }
    },
		async getRecord() {
			try {
				const data = this.$fetchOptions(
					this.tabKey,
					this.filterData,
					this.options,
					this.searchContent,
					this.searchId
				);
				this.apiCalling = true;
				const response = await this.$axios.get("product-management/pr-products", {
					params: data,
				});
				if (response.status === 200) {
					this.tableValues = response.data?.data;
					this.extraData = response?.data;
				}
				this.apiCalling = false;
			} catch (error) {
				console.log(error);
				HandleException.handleApiException(this, error);
			}
		},
		async pushRecord(data) {
			if (this.tabKey == "all" || this.tabKey == data.available)
				await this.tableValues.unshift(data);
			this.extraData.allTotal += 1;
			if (this.tabKey == "now") {
				this.extraData.availableTotal += 1;
			} else if (this.tabKey == "comming_soon") {
				this.extraData.commingSoonTotal += 1;
			}
			this.extraData.total += 1;
		},
		setStatusItems(status) {
			this.showRestore = false;
			switch (status) {
				case "now":
					this.statusItems = [{ id: "comming_soon", name: "comming_soon" }];
					break;
				case "comming_soon":
					this.statusItems = [{ id: "now", name: "now" }];
					break;
				case "removed":
					this.statusItems = [];
					this.showRestore = true;
					break;
				default:
					this.statusItems = [];
					break;
			}
		},
		checkSelectedTab(value) {
			// this.tableKey++;    // Commented due to pagination bug
			this.selectedItems = [];
			let currentTab = "";
			this.tabItems = this.tabItems.map((item) => {
				item.key === value
					? ((item.isSelected = 1), (currentTab = item.key))
					: (item.isSelected = 0);
				return item;
			});
			this.setStatusItems(currentTab);
		},
		fetchNewData() {
			this.selectedItems = [];
			this.getRecord();
		},
		onRowClicked(item) {
			let items = new Set(this.selectedItems);
			items.has(item) ? items.delete(item) : items.add(item);
			this.selectedItems = Array.from(items);
		},
		selectData(response) {
			if (response.status === 200) {
				const data = response.data;
				if (data != null) {
					this.tableValues = this.tableValues.filter(
						(row) => row.id !== data.id
					);
					this.tableValues.unshift(data);
					this.selectedItems?.unshift(data);
				}
			} else {
				this.$root.$emit("removeSearchId", this.searchId);
			}
		},
		searchByContent(searchContent) {
			this.searchContent = searchContent;
			this.getRecord();
		},
		async searchById(id) {
			this.searchId = id;
			try {
				const options = this.$fetchOptions(
					this.tabKey,
					this.filterData,
					this.options,
					this.searchContent,
					this.searchId
				);
				const response = await this.$axios.post(
					"single-sales/products-ssp/search",
					options
				); // Change the route
				this.selectData(response);
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
		onFilterRecords(filterData) {
			this.$root.$emit("resetPageNumber");
			this.filterData = filterData;
			this.getRecord();
		},
		clearAndUnselectId(code) {
			this.selectedItems = this.selectedItems.filter(
				(row) => row.code !== code
			);
		},
		openFilterDialog() {
			this.$refs.ProductsFilterRef.changeModel();
		},
		onTableChanges(options) {
			if (JSON.stringify(options) !== JSON.stringify(this.options)) {
				this.options = this._.clone(options);
				this.getRecord();
			} else this.options = this._.clone(options);
		},
		completedStyle(item) {
			return item.status == "completed" ? "completedAnimation" : "";
		},

		async onEditProduct() {
			if (this.selectedItems.length != 1) {
				// this.$toastr.w(this.$tr("please_select_one_option"));
				this.$toasterNA("orange",this.$tr('please_select_one_option'));

				return;
			}

			this.$refs.productUpdateStepperRef.showDialog();
			this.$refs.productUpdateStepperRef.onEdit(this.selectedItems[0]);
		},
	},
};
</script>

<style></style>

<template>
	<!-- main components in a page -->
	<v-container fluid class="pa-0">
		<InsertOrderStepper ref="InsertOrderStepperRef"></InsertOrderStepper>
		<client-only>
			<v-row no-gutters>
				<ProgressBar v-if="showProgressBar" />
				<v-col cols="12">

					<PageHeader
						:Icon="`my_orders`"
						:Title="breadcrumb[1].text"
						:Breadcrumb="breadcrumb"
					>
					</PageHeader>
				</v-col>

				<!--start  page filter  -->

				<!--end  page filter  -->

				<v-col cols="12">
					<PageFilters
						ref="pageFilterRef"
						:selected_header.sync="all_headers[0].selected_headers"
						:show_graph="false"
						:downloadContent="tableRecords"
						:downloadDialog="downloadDialog"
						:filename="'orders-'"
						note-title=""
						:showFilter="false"
						:showBulkUpload="false"
						:showDownload="false"
						:showTab="false"
						:showColumn="false"
					>
						<CustomButton
							v-if="$isInScope('crm_ordersc')"
							icon="fa-plus"
							@click="openInsertDialog"
							:text="$tr('create_item', $tr('order'))"
							type="primary"
						/>
					</PageFilters>
				</v-col>
				<v-col cols="12">
					<CustomPageActions
						:showView="$isInScope('crm_ordersv')"
						:showEdit="false"
						:showAutoEdit="false"
						:showDelete="$isInScope('crm_ordersv')"
						:showApply="$isInScope('crm_ordersv')"
						:selectedItems.sync="selectedItems"
						:tab-key.sync="tabItems[tabIndex].key"
						:showBlock="false"
						:statusItems="statusItems"
						:routeName="'design-request'"
						:subSystemName="'Design Request'"
						:noReasonSubmitOperations="'in storyboard, storyboard review, in design, design review, in programming, completed'"
					>
						<template slot="afterDelete"> </template>
					</CustomPageActions>
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
						:extraData="'countries/getTotal'"
					/>

					<DataTable
						ref="projectTableRef"
						:headers="selected_header"
						:items="design_request_data"
						:ItemsLength="getTotals(tabItems[tabIndex].key)"
						v-model="selectedItems"
						:loading="apiCalling"
						@onTablePaginate="onTableChanges"
						@click:row="onRowClicked"
						:item-class="completedStyle"
						:key="tableKey"
					>
						<template v-slot:[`item.code`]="{ item }">
							<span
								@click="() => viewSelectedDesignRequest(item)"
								class="mx-1 primary--text font-weight-bold"
							>
								{{ item.code }}
							</span>
						</template>
					</DataTable>
				</v-col>
				<progress-bar v-if="isFetchCancels" />
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
import { mapActions, mapGetters, mapState } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import Orders from "~/configs/pages/crm_orders";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn.vue";
import ColumnHelper from "~/helpers/column-helper";
import TableTabs from "~/components/common/TableTabs.vue";
import InsertOrderStepper from "~/components/crmOrder/InsertOrderStepper.vue";

export default {
	components: {
		CustomPageActions,
		PageActions,
		CustomButton,
		DesignRequestEdit,
		PageHeader,
		PageFilters,
		DataTable,
		ProgressBar,
		TableTabs,
		InsertOrderStepper,
	},
	data() {
		return {
			tableRecords: [],
			totalRecords: 0,
			downloadDialog: false,
			headers: [],
			tabKey: "all",
			design_request_data: [],
			headers: Orders(this).headers,
			tabItems: this.$getTabItems(["all", "active", "inactive", "blocked"]),
			breadcrumb: Orders(this).breadcrumb,
			selectedItems: [],
			tableKey: 0,
			tabIndex: 0,
			options: {},
			//register section

			statusItems: [],

			// profile section
			showProfile: false,

			// for custom columns
			selected_header: [],
			apiCalling: false,
			isFetchCancels: false,

			showProgressBar: false,
			all_headers: [
				{
					key: "country",
					name: "Country",
					selected_headers: [],
					headers: [],
				},
			],
		};
	},

	computed: {
		...mapGetters({
			getTotals: "countries/getTotal",
		}),
	},

	methods: {
		viewSelectedDesignRequest(item) {
			this.showProfile = true;
		},
		getRecord() {
			return [];
		},

		onRowClicked(item) {
			let items = new Set(this.selectedItems);
			items.has(item) ? items.delete(item) : items.add(item);
			this.selectedItems = Array.from(items);
		},

		completedStyle(item) {
			return item.status == "completed" ? "completedAnimation" : "";
		},

		onTableChanges(options) {
			return 1;
		},
		openInsertDialog() {
			this.$refs.InsertOrderStepperRef.toggle();
		},
	},
};
</script>

<style></style>

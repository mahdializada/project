<template>
	<v-dialog v-model="showDialog" width="800" persistent>
		<v-card>
			<v-card-title class="pa-2 pb-1" style="color: #777">
				<span class="dialog-title"> {{ $tr("budget_history") }}</span>
				<v-spacer />
				<div class="d-flex align-center">
					<span style="font-weight: normal !impotant">
						<FilterDateRange
							ref="filterDateRange"
							:hideTitle="true"
							@dateChanged="onDateRangeSubmit"
						/>
					</span>
					<svg
						@click="closeDialog()"
						width="48px"
						height="48px"
						viewBox="0 0 700 560"
						fill="currentColor"
						style="transform: scaleY(-1); cursor: pointer"
					>
						<g>
							<path
								d="m350 58.332c-52.727 0.019531-103.72 18.836-143.82 53.066-40.105 34.23-66.695 81.637-74.996 133.7-8.3008 52.07 2.2305 105.39 29.703 150.4l6.0664 9.918 19.832-11.668-6.0664-9.918c-25.156-41.191-34.43-90.148-26.078-137.69 8.3516-47.543 33.754-90.406 71.445-120.56 37.691-30.156 85.086-45.527 133.3-43.238 48.215 2.2891 93.941 22.082 128.61 55.672 34.668 33.586 55.895 78.664 59.703 126.78 3.8125 48.121-10.055 95.977-39.004 134.6s-70.988 65.367-118.24 75.215c-47.254 9.8516-96.477 2.1289-138.45-21.719l-10.035-5.7148-11.668 20.301 10.148 5.7148h0.003907c39.484 22.207 84.82 31.785 129.91 27.445 45.09-4.3398 87.77-22.391 122.29-51.723 34.52-29.328 59.227-68.531 70.793-112.33 11.562-43.801 9.4336-90.09-6.1055-132.64-15.539-42.551-43.742-79.32-80.812-105.36-37.07-26.035-81.227-40.09-126.52-40.27z"
							/>
							<path
								d="m256.67 389.79 93.332-93.336 93.332 93.336 16.453-16.453-93.336-93.332 93.336-93.332-16.453-16.453-93.332 93.336-93.332-93.336-16.453 16.453 93.336 93.332-93.336 93.332z"
							/>
						</g>
					</svg>
				</div>
			</v-card-title>

			<!-- start header -->
			<TabHistory
				:item_data="item_data"
				:totals="totals"
				:titleData.sync="titleData"
				@getSelectedTabRecords="getSelectedTabRecords"
				:tab.sync="tab_inex"
				ref="tabRefs"
			/>
			<div class="overflow-auto pr-4 pl-2" style="height: 50vh">
				<BidHistory
					ref="bidHistoryRefs"
					v-show="selected_tab == 'bid_history'"
					@setTitleData="setTitleData"
				/>
				<LabelHistory
					ref="labelRefs"
					v-show="selected_tab == 'label'"
					:selected_item="item_data.item"
				></LabelHistory>

				<BudgetHistoryList
					v-show="selected_tab == 'budget_history'"
					:history_data="history_data"
					:budget_api_loading="budget_api_loading"
					:item_data.sync="item_data"
					@saveDetails="fetchHistoryDetails($event)"
				></BudgetHistoryList>
				<RemarkHistory
					v-show="selected_tab == 'remark'"
					:selected_items="item_data"
					ref="remarkRefs"
				/>
			</div>
			<v-card-actions class="justify-end">
				<v-btn class="primary">{{ $tr("close") }}</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import CustomInput from "../../design/components/CustomInput.vue";
import FilterDateRange from "../FilterDateRange.vue";
import FlagIcon from "../../common/FlagIcon.vue";
import TabHistory from "./TabHistory.vue";
import BudgetHistoryList from "./BudgetHistoryList.vue";
import BidHistory from "./BidHistory.vue";
import LabelHistory from "./LabelHistory.vue";
import RemarkHistory from "./RemarkHistory.vue";
export default {
	data() {
		return {
			showDialog: false,
			budget_api_loading: false,
			details_loading: false,
			budgetDate: "today",
			budgetDates: [
				{ value: "today", text: this.$tr("today") },
				{ value: "yesterday", text: this.$tr("yesterday") },
				{ value: "last_7_days", text: this.$tr("last_7_days") },
				{ value: "last_30_days", text: this.$tr("last_30_days") },
				{ value: "last_3_months", text: this.$tr("last_3_months") },
				{ value: "lifetime", text: this.$tr("lifetime") },
			],

			headers: [
				{
					text: this.$tr("ad_name"),
					align: "start",
					value: "ad_name",
					width: "75%",
				},
				{ text: this.$tr("spend"), value: "spend" },
			],
			item_data: {},
			history_data: [],
			panels: null,
			panel_model: [],
			totals: { total_budget: 0, total_spend: 0 },
			showBidHistory: false,
			titleData: {},
			selected_tab: "budget_history",
			tab_inex: 0,
		};
	},

	props: {
		fetchBudget: Boolean,
	},
	methods: {
		closeDialog() {
			this.showDialog = false;
		},
		async openDialog(data) {
			this.item_data = data;
			this.showDialog = true;
			this.fetchBudgetHistory();
			this.panel_model = [];
			this.tab_inex = 1;
		},
		generateId() {
			switch (this.item_data.model) {
				case "campaign":
					return this.item_data.item.campaign_id;

				case "ad":
					return this.item_data.item.server_adset_id;

				case "ad_set":
					return this.item_data.item.adset_id;
				default:
					return this.item_data.item.id;

					break;
			}
		},
		async fetchBudgetHistory(date_range = {}) {
			try {
				this.budget_api_loading = true;
				let data = { item_id: this.generateId(), model: this.item_data.model };
				data = { ...data, ...date_range };

				const response = await this.$axios.post(
					"advertisement/budget-history",
					data
				);
				this.history_data = response.data.budgets;
				this.totals = response.data.extra_data;
			} catch (error) {
			}
			this.budget_api_loading = false;
		},

		async fetchHistoryDetails(data) {
			this.history_data[data.index].ads = data.data;
			this.history_data = [...this.history_data];
		},
		onDateRangeSubmit(range) {
			const timeRange = {
				end_date: range.end_date,
				start_date: range.start_date,
			};
			this.fetchBudgetHistory(timeRange);
		},

		getSelectedTabRecords(tab) {
			this.selected_tab = tab;
			switch (tab) {
				case "bid_history":
					this.$refs.bidHistoryRefs.toggleDialog(this.item_data.item, tab);
					break;
				case "label":
					this.$refs.labelRefs.toggleDialog(this.item_data.model);
					break;
				case "remark":
					this.$refs.remarkRefs.toggleDialog();
					break;

				default:
					break;
			}
		},

		setTitleData(titleData) {
			this.titleData = titleData;
		},
	},
	components: {
		CustomInput,
		FilterDateRange,
		FlagIcon,
		TabHistory,
		BudgetHistoryList,
		BidHistory,
		LabelHistory,
		RemarkHistory,
	},
};
</script>

<style scoped>
.dialog-title {
	font-size: 20px;
	font-weight: 600;
	color: #777;
}
.custom-chip {
	color: #777;
	padding: 5px 5px;
	border-radius: 20px;
	border: solid 1.5px rgb(212, 212, 212);
	cursor: pointer;
}
.custom-chip.selected {
	background: var(--v-primary-base);
	color: var(--bg-white);
	border: unset;
}
.label-color {
	display: inline-block;
	width: 23px;
	height: 23px;

	border-radius: 50px;
}
.custom-chip.selected .label-color {
	border: 2.3px solid var(--bg-white);
	width: 25px !important;
	height: 25px !important;
}
.custom-card-title-2 {
	font-size: 16px;
	font-weight: 400;
	color: #777;
}
.custom-wraper {
	height: 500px;
	overflow-y: auto;

	scroll-behavior: smooth;
}
.custom-btn {
	font-size: 14px;
	font-weight: 400;
}
</style>
<style>
.customExpand .v-expansion-panel-header__icon {
	align-items: center !important;
}
.customTimeLine .v-timeline-item__dot {
	box-shadow: unset !important;
	margin-top: 36% !important ;
}
</style>

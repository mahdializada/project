<template>
	<div :class="`adv-tabs background ${isGraphOpen ? 'show' : ''}`">
		<div class="d-flex">
			<div class="w-full">
				<v-tabs show-arrows v-model="graphCurrentTabIndex">
					<v-tab v-for="(tabItem, tabIndex) in graphTabs" :key="tabIndex">
						<div class="text-start tab-content me-auto">
							<div class="tab-adv-sub-title">{{ $tr(tabItem.id) }}</div>
							<div class="tab-adv-title">{{ tabItem.value }}</div>
						</div>
					</v-tab>
				</v-tabs>
			</div>
		</div>

		<transition name="slide-horizontal">
			<div v-if="isFetchingGraphData && isGraphOpen" class="graph__loader mt-1">
				<div v-for="i in 5" :key="i" class="d-flex w-full">
					<v-skeleton-loader type="list-item" class="rounded-0 w-full" />
					<v-skeleton-loader type="list-item" class="rounded-0 w-full" />
					<v-skeleton-loader type="list-item" class="rounded-0 w-full" />
					<v-skeleton-loader type="list-item" class="rounded-0 w-full" />
					<v-skeleton-loader type="list-item" class="rounded-0 w-full" />
				</div>
			</div>

			<div v-else>
				<div
					v-if="isGraphOpen"
					style="background: var(--v-background-base)"
					class="pa-4 mt-1"
				>
					<div id="legend-container" class="position-relative">
						<line-chart
							:height="200"
							:chart-options="chartOptions"
							:chart-data="chartData"
							:plugins="plugins"
						/>
					</div>
				</div>
			</div>
		</transition>
		<div
			:class="`chart-btn d-flex flex-column align-center justify-center primary--text`"
			style="
				font-size: 12px;
				position: absolute;
				left: 50%;
				bottom: -20px;
				padding: 5px;
			"
			@click="toggleGraph"
		>
			<v-icon class="d-block primary--text" size="16">
				{{
					isGraphOpen
						? "mdi-chevron-up-circle-outline"
						: "mdi-chevron-down-circle-outline"
				}}
			</v-icon>
		</div>
	</div>
</template>
<script>
import moment from "moment";
import htmlLegendPlugin from "../../plugins/chartjs/htmlLegend";
export default {
	props: {
		dateRange: Object,
	},
	watch: {
		dateRange: function (dateRange) {
			if (dateRange) {
				const selectedDates = this.getDatesBetween(
					dateRange.start_date,
					dateRange.end_date
				);
				this.chartData.labels = selectedDates;
				this.fetchGraphTabs();
				this.fetchGraphData();
			}
		},
		graphCurrentTabIndex: function (tabIndex) {
			const { id } = this.graphTabs[tabIndex];
			this.graphCurrentTab = id;
			this.fetchGraphData();
		},
	},
	data() {
		return {
			isGraphOpen: false,
			comparison: false,
			graphCurrentTabIndex: 0,
			graphCurrentTab: "spend",
			isFetchingTabs: false,
			isFetchingGraphData: false,
			axiosSource: null,
			graphTabs: [],
			defaultDataSet: {
				label: null,
				data: [],
				backgroundColor: "#11559844",
				borderColor: "#115598",
				pointStyle: "circle",
				pointRadius: 6,
				pointHoverRadius: 8,
				borderWidth: 1,
				pointBorderWidth: 1,
			},
			chartData: {
				labels: [],
				datasets: [],
			},
			chartOptions: {
				responsive: true,
				maintainAspectRatio: false,
				interaction: {
					intersect: false,
					mode: "index",
				},
				plugins: {
					htmlLegend: {
						// ID of the container to put the legend in
						containerID: "legend-container",
					},
					legend: {
						display: false,
					},
				},
			},
			plugins: [htmlLegendPlugin],
		};
	},

	created() {
		if (this.dateRange) {
			const selectedDates = this.getDatesBetween(
				this.dateRange.start_date,
				this.dateRange.end_date
			);
			this.chartData.labels = selectedDates;
		}

		this.fetchGraphTabs();
	},

	methods: {
		async fetchGraphTabs(options = {}) {
			try {
				this.isFetchingTabs = true;
				let url = `advertisement/graph/tabs`;
				const body = { ...this.dateRange, ...options };
				const { data } = await this.$axios.post(url, body);
				if (data.tab_items) {
					this.graphTabs = data.tab_items;
				}
			} catch (error) {}
			this.isFetchingTabs = false;
		},

		async fetchGraphData(options = {}) {
			try {
				this.isFetchingGraphData = true;
				if (this.axiosSource) this.axiosSource.cancel("Request-Cancelled");
				this.axiosSource = this.$axios.CancelToken.source();
				const currentTab = this.graphCurrentTab;
				let url = `advertisement/graph/${currentTab}`;
				let body = { ...options, ...this.dateRange };
				// body = { ...body, ...this.dateRange };

				const header = { cancelToken: this.axiosSource.token };
				const { data } = await this.$axios.post(url, body, header);
				if (currentTab.trim() == "result") {
					const spendRes = await this.$axios.post(
						`advertisement/graph/spend`,
						body,
						header
					);
					const d1 = JSON.parse(JSON.stringify(this.defaultDataSet));
					const d2 = JSON.parse(JSON.stringify(this.defaultDataSet));
					d1.label = this.$tr("spend");
					d1.data = data.data;

					d2.label = this.$tr(currentTab);
					d2.data = spendRes.data.data;
					d2.backgroundColor = "#00bc81";
					d2.borderColor = "#00bc81";

					this.chartData.datasets = [d1, d2];
					this.chartData.labels = spendRes.data.label;
					this.defaultDataSet = d1;
				} else {
					if (data.label) {
						this.defaultDataSet.label = this.$tr(currentTab);
						this.defaultDataSet.data = data.data;
						this.chartData.datasets = [this.defaultDataSet];
						this.chartData.labels = data.label;
					}
				}
			} catch (error) {}
			this.isFetchingGraphData = false;
		},

		getDateIndexes(dates) {
			let length = dates.length;
			if (length <= 16) {
				let daysArray = [];
				for (let index = 1; index < length - 1; index++) {
					daysArray.push(index);
				}
				daysArray.unshift(0);
				daysArray.push(length - 1);
				daysArray = [...new Set(daysArray)];
				return daysArray;
			}
			let daysArray = [];
			for (let index = 1; index < 15; index++) {
				daysArray.push(Math.round((length / 16) * index));
			}
			daysArray = daysArray.sort((a, b) => a - b);
			daysArray.unshift(0);
			daysArray.push(length - 1);
			daysArray = [...new Set(daysArray)];
			return daysArray;
		},
		getDatesBetween(startDate, endDate) {
			let dates = [];
			startDate = moment(startDate);
			endDate = moment(endDate);

			if (startDate.isAfter(endDate)) {
				let temp = startDate;
				startDate = endDate;
				endDate = temp;
			}

			while (startDate.isSameOrBefore(endDate)) {
				dates.push(startDate.format("MMMM DD, YYYY"));
				startDate.add(1, "days");
			}
			let dateIndexes = this.getDateIndexes(dates);
			dates = dateIndexes.map((index) => dates[index]);
			return dates;
		},
		toggleGraph() {
			this.isGraphOpen = !this.isGraphOpen;
			if (this.isGraphOpen && this.defaultDataSet.label == null) {
				this.fetchGraphData();
			}
		},
	},
};
</script>
<style>
.graph__loader {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: space-between;
}

.theme--dark .adv-tabs {
	background: #242424;
}
.adv-tabs .v-tabs-bar {
	background: transparent !important;
}
.adv-tabs .v-tab {
	background: var(--v-surface-base);
	border-radius: 6px;
	margin: 0 4px;
	width: 300px;
	overflow: hidden;
}
.adv-tabs .v-tab.v-tab--active {
	background: var(--v-background-base);
}
.adv-tabs .v-tabs-slider-wrapper {
	display: none;
}
.text-start {
	text-align: left;
}
.v-application--is-rtl .text-start {
	text-align: right;
}
.adv-tabs .tab-content {
	text-transform: none !important;
	cursor: unset;
}
.adv-tabs .tab-adv-sub-title {
	font-size: 16px;
	font-weight: 100;
	color: #777;
}
.theme--dark .adv-tabs .tab-adv-sub-title {
	color: #ccc;
}
.adv-tabs .tab-adv-title {
	font-weight: bold;
	color: var(--v-friqiBase-base);
}
.adv-tabs .v-slide-group__prev,
.adv-tabs .v-slide-group__next {
	background: var(--v-background-base);
	min-width: 26px;
	max-width: 26px;
}
.adv-tabs .v-slide-group__prev {
	margin-right: 4px;
}
.adv-tabs .v-slide-group__next {
	margin-left: 4px;
}
.adv-tabs .chart-btn {
	min-width: 52px;
	background: var(--v-background-base);
	border-radius: 6px;
	transform: translateX(-100%);
	cursor: pointer;
	transition: all 0.3s;
}
.adv-tabs .chart-btn.rotate .v-icon {
	transform: rotate(180deg);
}

.adv-graph-legend-icon {
	position: relative;
}
.adv-graph-legend-icon::after {
	height: 1px;
	width: 40px;
	background: currentColor;
	display: block;
	content: "";
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
}

.slide-horizontal-enter-active,
.slide-horizontal-leave-active {
	transition: all 0.6s;
}

.slide-horizontal-leave,
.slide-horizontal-enter-to {
	max-height: 600px;
	overflow: hidden;
}

.slide-horizontal-leave {
	opacity: 0;
}

.slide-horizontal-enter,
.slide-horizontal-leave-to {
	overflow: hidden;
	max-height: 0;
}

.slide-horizontal-leave-to {
	opacity: 0;
}
.adv-graph-switch {
	position: absolute;
	top: -24px;
	right: 0px;
}
</style>

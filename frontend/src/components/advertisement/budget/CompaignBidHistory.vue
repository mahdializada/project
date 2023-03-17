<template>
	<v-card>
		<!-- body -->
		<v-card-text class="overflow-auto" style="height: 50vh">
			<div v-if="!api_loading">
				<v-timeline
					align-top
					dense
					flat
					class="custom-timeLine customTimeLine"
					v-if="allData != null"
				>
					<v-timeline-item
						class="pb-1"
						v-for="[key, history] in Object.entries(historyData)"
						:key="history[0].id"
						color="primary"
						icon="mdi-book"
					>
						<v-expansion-panels accordion flat class="customExpand">
							<v-expansion-panel>
								<v-expansion-panel-header>
									<div>
										<p class="mb-0 text-caption">{{ key }}</p>
										<div class="text-body-1 my-1 font-weight-regular">
											<span class="primary--text">{{
												allData != null
													? allData.ad_account.name
													: $tr("Campaign")
											}}</span>
											<span>{{ $tr("total_budget_updated_to") }}</span>
											<v-chip
												class="mx-2 px-1"
												color="blue lighten-5"
												small
												label
												text-color="primary"
											>
												{{ getTotal(history) }} $
											</v-chip>
										</div>
										<span class="text-caption">
											<v-avatar size="20">
												<img :src="allData.connections[0].platform.logo" />
												<!-- <v-icon>mdi-facebook</v-icon> -->
											</v-avatar>
											<span class="ms-1">{{
												allData.connections[0].platform.platform_name
													? allData.connections[0].platform.platform_name
													: $tr("ad_platform")
											}}</span>
										</span>
									</div>
									<template v-slot:actions>
										<span class="primary--text">{{ $tr("show_cost") }}</span>
										<v-icon color="primary"> mdi-chevron-down </v-icon>
									</template>
								</v-expansion-panel-header>
								<v-expansion-panel-content>
									<v-data-table
										:headers="headers"
										:items="history"
										fixed-header
										height="20vh"
										dense
										item-key="name"
										hide-default-footer
									></v-data-table>
								</v-expansion-panel-content>
							</v-expansion-panel>
						</v-expansion-panels>
						<v-divider></v-divider>
					</v-timeline-item>
				</v-timeline>
			</div>

			<div v-else class="px-5">
				<v-skeleton-loader
					v-for="i in 2"
					:key="i"
					type=" list-item-avatar, list-item-three-line"
				></v-skeleton-loader>
			</div>
		</v-card-text>
	</v-card>
</template>

<script>
export default {
	data() {
		return {
			showDialog: false,
			headers: [
				{
					text: "Name",
					align: "start",
					value: "name",
				},
				{ text: "Bid", value: "bid" },
			],
			api_loading: false,
			historyData: [],
			allData: [],
			propData: {},
			totalBid: 0,
		};
	},
	props: {
		data: {
			type: Object,
			require: true,
		},
	},
	created() {
		let data = { campaign_id: this.data.item.id };
		data = { ...data, ...this.data.dateRange };
		this.getHistory(data);
	},
	methods: {
		toggleDialog(prop) {
			this.clearData();
			this.showDialog = !this.showDialog;
			this.getHistory(prop);
		},
		onClose() {
			this.clearData();
			this.showDialog = !this.showDialog;
		},

		async getHistory(prop) {
			this.propData = prop;
			this.api_loading = true;
			try {
				const response = await this.$axios.post(
					"advertisement/campaign-bids-history",
					prop
				);
				this.api_loading = false;
				this.allData = response.data.data;
				if (this.allData != null) {
					const history = response.data?.data?.campaign_adsets;
					history.map((item) => {
						this.totalBid = this.totalBid + parseFloat(item.bid);
						return item;
					});
					this.historyData = this.groupBy(history, "data_date");
					let totals = {
						totals: [{ name: "Campaign Total Bid", total: this.totalBid }],
					};
					this.$emit("setTitleData", totals);
				}
			} catch (error) {
				this.api_loading = false;
			}
		},

		async getAdsetHistory(data) {
			try {
				this.labelProp = data;
				this.skeleton_loading = true;

				const response = await this.$axios.post(
					"advertisement/get-bids-history",
					data
				);

				this.historyData = response.data.data;
				this.historyData = this.historyData.map((item) => {
					item.total = this.getTotal(item.campaign_adsets);
					this.totalBid = this.totalBid + item.total;
					return item;
				});
				this.titleData.totals = [
					{ total: this.totalBid, name: this.$tr("total_bid") },
				];

				this.$emit("setTitleData", this.titleData);

				let totals = {
					totals: [{ name: "Current Labels", total: this.data.item.labels }],
				};
				this.$emit("setTitleData", totals);
			} catch (error) {
			}
			this.skeleton_loading = false;
		},
		prepareData() {},

		clearData() {
			this.historyData = {};
			this.allData = [];
			this.totalBid = 0;
		},

		groupBy(arr, criteria) {
			const newObj = arr.reduce(function (acc, currentValue) {
				if (!acc[currentValue[criteria]]) {
					acc[currentValue[criteria]] = [];
				}
				acc[currentValue[criteria]].push(currentValue);
				return acc;
			}, {});
			return newObj;
		},

		getTotal(adserts) {
			let sum = 0;
			adserts.forEach((element) => {
				sum = parseFloat(element.bid) + sum;
			});

			return sum;
		},

		getTotalBid() {
			const sum = this.totalBid.reduce((partialSum, a) => partialSum + a, 0);
		},
		onDateRangeSubmit(range) {
			const timeRange = {
				campaign_id: this.propData.campaign_id,
				end_date: range.end_date,
				start_date: range.start_date,
			};
			this.getHistory(timeRange);
		},
	},
	components: {},
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

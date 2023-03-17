<template>
	<v-dialog v-if="showDialog" v-model="showDialog" width="800" persistent>
		<v-card>
			<div class="px-2 pt-2 pb-2 d-flex" style="color: #777">
				<div class="text-capitalize dialog-title">{{ $tr("bid_history") }}</div>
				<v-spacer />
				<div class="d-flex">
					<span style="font-weight: normal !impotant; color: red">
						<FilterDateRange
							ref="filterDateRange"
							:hideTitle="true"
							@dateChanged="onDateRangeSubmit"
						/>
					</span>

					<svg
						@click="onClose"
						width="42px"
						height="42px"
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
			</div>
			<v-card-title
				class="pa-0 font-weight-regular"
				style="box-shadow: unset !important"
			>
				<v-card
					class="w-full mb-2 pa-2 d-flex align-center card_Page_header"
					elevation="1"
					outlined
				>
					<div class="ps-4">
						<v-avatar
							size="30"
							color="primary darken-2"
							class="white--text text-h6 text-uppercase"
							outlined
						>
							<span>{{
								allData.ad_account !== undefined
									? allData.ad_account.name[0].toLowerCase()
									: "C"
							}}</span>
						</v-avatar>

						<span class="ps-1 font-weight-medium">{{
							allData.ad_account !== undefined ? allData.ad_account.name : ""
						}}</span>
					</div>

					<v-spacer></v-spacer>
					<div class="pe-4">
						<span class="text-body-1 px-1 text-center">
							{{ $tr('total_bid') }}: {{ totalBid }}</span
						>
					</div>
				</v-card>
			</v-card-title>
			<!-- body -->
			<v-card-text class="overflow-auto" style="height: 50vh">
				<div v-if="!api_loading">
					<v-timeline
						align-top
						dense
						flat
						class="customTimeLine"
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
			<v-card-actions class="justify-end">
				<v-btn class="primary" @click="onClose">{{ $tr("close") }}</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import CustomInput from "../design/components/CustomInput.vue";
import FilterDateRange from "../../components/advertisement/FilterDateRange.vue";
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
	props: {},
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
						this.totalBid = this.totalBid + parseInt(item.bid);
						return item;
					});
					this.historyData = this.groupBy(history, "data_date");
				}
			} catch (error) {
				this.api_loading = false;
			}
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
				sum = parseInt(element.bid) + sum;
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
	components: { CustomInput, FilterDateRange },
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
	margin-top: 35% !important ;
}
</style>

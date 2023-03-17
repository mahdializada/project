<template>
	<!-- timeline -->
	<div class="py-3 history-body" style="position: relative">
		<v-timeline
			:reverse="reverse"
			class="customTimeLine custom-timeLine"
			dense
			v-if="!loading && has_status > 0"
			flat
			align-top
		>
			<v-timeline-item v-for="history in data" :key="history.id">
				<template v-slot:icon>
					<v-icon light size="15" color="white">mdi-clipboard-check</v-icon>
				</template>

				<v-expansion-panels>
					<v-expansion-panel>
						<v-expansion-panel-header>
							<div>
								<p class="mb-0 text-caption">
									{{ localeHumanReadableTime(history.created_at) }}
								</p>
								<div class="text-body-1 my-1 font-weight-regular">
									<span class="ms-1"> {{ history.pname }}</span>
									<v-chip
										class="mx-2 px-1"
										color="blue lighten-5"
										small
										label
										:text-color="
											history.advertisement_status == 'inactive'
												? 'grey'
												: 'primary'
										"
									>
										{{ history.advertisement_status }}
									</v-chip>
								</div>

								<!-- <span class="text-caption">
									<span class="ms-1"> {{ history.pname }}</span>
								</span> -->
							</div>

							<template v-slot:actions>
								<span class="primary--text mt-1"> See History</span>
								<v-icon color="primary"> mdi-chevron-down </v-icon>
							</template>
						</v-expansion-panel-header>

						<v-expansion-panel-content>
							<v-data-table
								:headers="headers"
								:items="history.reasonables2"
								fixed-header
								height="20vh"
								dense
								item-key="pivot.id"
								hide-default-footer
							>
								<template v-slot:[`item.date`]="{ item }">
									<div>
										{{ localeHumanReadableTime(item.pivot.created_at) }}
									</div>
								</template>
							</v-data-table>
						</v-expansion-panel-content>
					</v-expansion-panel>
				</v-expansion-panels>
				<v-divider></v-divider>
			</v-timeline-item>
		</v-timeline>
		<div
			v-if="!loading && has_status == 0"
			class="text-center"
			style="
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
			"
		>
			<NoStatus />
			<p
				class="mb-0 text-h6"
				style="font-size: 20px; font-weight: 500; color: #777"
			>
				{{ $tr("no_status_product") }}
			</p>
		</div>

		<div v-if="loading" class="px-5">
			<v-skeleton-loader
				v-for="i in 2"
				:key="i"
				type=" list-item-avatar, list-item-three-line"
			>
			</v-skeleton-loader>
		</div>
	</div>
</template>

<script>
import moment from "moment-timezone";
import NoStatus from "./NoStatus.vue";

export default {
	data() {
		return {
			model: false,
			active: true,
			reverse: false,
			panel: [],
			data: [],
			grouped: [],
			props: {},
			loading: false,
			has_status: 0,
			headers: [
				{
					text: this.$tr("created_at"),
					align: "start",
					value: "date",
				},
				{ text: this.$tr("status"), value: "pivot.status" },
			],
		};
	},
	props: {
		statusData: {
			type: Object,
			require: true,
		},
	},
	created() {
		let data = {
			column:
				this.statusData.model == "country"
					? "country_id"
					: this.statusData.model == "company"
					? "company_id"
					: "project_id",
			id: this.statusData.item.id,
		};
		data = { ...data, ...this.statusData.dateRange };
		this.fetchAllData(data);
	},
	methods: {
		onClose() {
			this.model = !this.model;
		},
		async onToggleDialog(data) {
			this.model = !this.model;
			await this.fetchAllData(data);
		},

		async fetchAllData(data) {
			this.props = data;
			try {
				this.loading = true;
				this.data = [];
				let res = await this.$axios.post(
					"advertisement/product-status-history",
					data
				);

				this.data = res.data.data;
				this.has_status = this.data.length;
				console.log(res, this.data);
				this.loading = false;
			} catch (error) {
				this.$toasterNA("red", this.$tr("something_went_wrong"));
				this.loading = false;
			}
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
		onDateRangeSubmit(range) {
			const timeRange = {
				id: this.props.id,
				column: this.props.column,
				start_date: range.start_date,
				end_date: range.end_date,
			};
			this.fetchAllData(timeRange);
		},
		localeHumanReadableTime(date) {
			return moment(date)
				.locale(this.$vuetify.lang.current)
				.format("YYYY-MM-DD h:mm: A");
		},
	},
	components: { NoStatus },
};
</script>

<style scoped>
.history-body {
	height: 40vh;

	overflow-y: auto;
}
</style>
<style>
#custom-scroll::-webkit-scrollbar {
	box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3) !important;
	border-radius: 10px !important;
	background-color: #f5f5f5 !important;
}

#custom-scroll::-webkit-scrollbar {
	width: 10px;
	background-color: var(--v-surface-base);
}

#custom-scroll::-webkit-scrollbar:hover {
	cursor: alias !important;
}

#custom-scroll::-webkit-scrollbar-thumb:hover {
	cursor: alias !important;
}

#custom-scroll::-webkit-scrollbar-thumb {
	border-radius: 10px;
	box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
	background-color: var(--v-background-darken2);
}

.dialog-title {
	font-size: 20px;
	font-weight: 600;
	color: #777;
}

.customTimeLine .v-timeline-item__dot {
	box-shadow: unset !important;
	margin-top: 36% !important;
}
</style>

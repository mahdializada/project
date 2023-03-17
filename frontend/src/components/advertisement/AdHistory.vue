<template>
	<v-dialog v-if="model" v-model="model" width="800" persistent>
		<v-card>
			<div class="px-2 pt-2 pb-2 d-flex" style="color: #777">
				<div class="text-capitalize dialog-title">{{ $tr("history") }}</div>
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
			<v-card-text class="pl-0">
				<!-- timeline -->
				<div class="py-3 history-body" id="custom-scroll">
					<v-timeline
						:reverse="reverse"
						class="customTimeLine"
						dense
						v-if="!loading"
						flat
					>
						<v-timeline-item
							v-for="history in data"
							:key="history[0].id"
							:color="history[0].status == 'inactive' ? 'grey' : 'primary'"
						>
							<template v-slot:icon>
								<v-icon light size="15" color="white"
									>mdi-clipboard-check</v-icon
								>
							</template>

							<v-expansion-panels>
								<v-expansion-panel>
									<v-expansion-panel-header>
										<div>
											<p class="mb-0 text-caption">
												{{ localeHumanReadableTime(history[0].created_at) }}
											</p>

											<div class="text-body-1 my-1 font-weight-regular">
												<span class="primary--text">
													{{ history[0].reasonable.name }}</span
												>

												<v-chip
													class="mx-2 px-1"
													color="blue lighten-5"
													small
													label
													:text-color="
														history[0].status == 'inactive' ? 'grey' : 'primary'
													"
												>
													{{ history[0].status }}
												</v-chip>
											</div>

											<span class="text-caption">
												<v-avatar size="20">
													<img :src="history[0].creator.image" />
													<!-- <v-icon>mdi-facebook</v-icon> -->
												</v-avatar>
												<span class="ms-1">
													{{
														history[0].creator.firstname +
														" " +
														history[0].creator.lastname
													}}</span
												>
											</span>
										</div>

										<template v-slot:actions>
											<span class="primary--text mt-1"> See Reasons</span>
											<v-icon color="primary"> mdi-chevron-down </v-icon>
										</template>
									</v-expansion-panel-header>

									<v-expansion-panel-content>
										<v-list-item v-for="reason in history" :key="reason.id">
											<v-list-item-icon>
												<v-icon> mdi-subdirectory-arrow-right </v-icon>
											</v-list-item-icon>
											<v-list-item-content>
												{{ reason.reasons.title }}
											</v-list-item-content>
										</v-list-item>
									</v-expansion-panel-content>
								</v-expansion-panel>
							</v-expansion-panels>
							<v-divider></v-divider>
						</v-timeline-item>
					</v-timeline>
					<div v-else class="px-5">
						<v-skeleton-loader
							v-for="i in 2"
							:key="i"
							type=" list-item-avatar, list-item-three-line"
						></v-skeleton-loader>
					</div>
					<NoRemark v-if="(data.length == 0 || data == null) && !loading" />
				</div>
			</v-card-text>
			<v-card-actions class="d-flex justify-end py-3">
				<v-btn color="primary " @click="onClose">
					{{ $tr("close") }}
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import FilterDateRange from "./FilterDateRange.vue";
import moment from "moment-timezone";
import NoRemark from "./remarks/NoRemark.vue";
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
		};
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
				let res = await this.$axios.post("/get-reason-history", data);
				this.data = await this.groupBy(res.data, "uuid");
				this.loading = false;
			} catch (error) {
				// this.$toasterNA("red", this.$tr('something_went_wrong'));
			 this.$toasterNA('red',this.$tr("something_went_wrong"));

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
				model: this.props.model,
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
	components: { FilterDateRange, NoRemark },
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
	margin-top: 36% !important ;
}
</style>

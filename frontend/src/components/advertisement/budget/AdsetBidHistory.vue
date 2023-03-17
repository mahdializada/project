<template>
	<v-card-text class="ps-2 pe-1">
		<div
			id="custom-scroll"
			class="custom-wraper pe-1"
			style="position: relative"
		>
			<template v-if="!skeleton_loading">
				<v-timeline
					dense
					flat
					class="custom-timeLine customTimeLine"
					align-top
					v-if="!(historyData.length === 0)"
				>
					<!-- (Object.keys(historyData).length === 0) -->
					<v-timeline-item
						v-for="history in historyData"
						:key="history.id"
						color="primary"
						icon="mdi-book"
					>
						<div class="pa-2">
							<p class="mb-0 text-caption">
								{{ localeHumanReadableTime(history.created_at) }}
							</p>
							<div class="text-body-1 my-1 font-weight-regular">
								<!-- <v-avatar size="30" color="primary">
									{{ history.name[0] }}
				 
								</v-avatar> -->
								<span class="primary--text">{{ history.name }}</span>
								<v-chip
									class="mx-3 px-1"
									color="blue lighten-5"
									small
									label
									text-color="primary"
									>{{ history.bid }}</v-chip
								>
							</div>
						</div>

						<v-divider color="#edebeb"></v-divider>
					</v-timeline-item>
				</v-timeline>
				<div
					v-else
					class="text-center"
					style="
						position: absolute;
						top: 50%;
						left: 50%;
						transform: translate(-50%, 50%);
					"
				>
					<NoLabel />
					<p
						class="mb-0 text-h6"
						style="font-size: 20px; font-weight: 500; color: #777"
					>
						{{ $tr("no_label_activity") }}
					</p>
				</div>
			</template>
			<template v-else>
				<span v-for="i in 3" :key="i">
					<v-skeleton-loader
						v-bind="attrs_sk"
						type=" card-heading, list-item-three-line"
					>
					</v-skeleton-loader>
				</span>
			</template>
		</div>
	</v-card-text>
</template>

<script>
import NoLabel from "../label/NoLabel.vue";
import moment from "moment-timezone";
export default {
	data() {
		return {
			showDialog: false,
			api_loading: false,
			selected_labels: [],
			historyData: [],
			has_label: [],
			skeleton_loading: false,
			attrs_sk: {
				style: "margin-bottom:0.2px",
				boilerplate: true,
				elevation: 0,
			},
			labelProp: {},
			totalBid: 0,
		};
	},
	props: {
		subsystem_name: {
			type: String,
			require: true,
		},

		data: {
			type: Object,
			require: true,
		},
	},
	created() {
		let data = { adset_id: this.data.item.adset_id };
		data = { ...data, ...this.data.dateRange };
		this.getHistory(data);
	},
	methods: {
		async getHistory(data) {
			try {
				this.labelProp = data;
				this.skeleton_loading = true;

				const response = await this.$axios.post(
					"advertisement/get-bids-history",
					data
				);

				this.historyData = response.data.data;
				this.historyData = this.historyData.map((item) => {
					this.totalBid = this.totalBid + item.bid;
					return item;
				});
				const titleData = {
					totals: [{ total: this.totalBid, name: this.$tr("total_bid") }],
				};

				this.$emit("setTitleData", titleData);
			} catch (error) {
			}
			this.skeleton_loading = false;
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

		localeHumanReadableTime(date) {
			return moment(date)
				.locale(this.$vuetify.lang.current)
				.format("YYYY-MM-DD h:mm: A");
		},

		faltObject(items) {
			items = items.map((ob) => {
				const flattenObj = (ob) => {
					// The object which contains the
					// final result
					let result = {};

					// loop through the object "ob"
					for (const i in ob) {
						if (typeof ob[i] === "object" && !Array.isArray(ob[i])) {
							const temp = flattenObj(ob[i]);
							for (const j in temp) {
								result[j] = temp[j];
							}
						} else {
							result[i] = ob[i];
						}
					}
					return result;
				};

				return flattenObj(ob);
			});
			return items;
		},

		onDateRangeSubmit(range) {
			let data = {
				tab: this.labelProp.tab,
				id: this.labelProp.id,
				end_date: range.end_date,
				start_date: range.start_date,
			};

			this.getHistory(data);
		},
	},
	components: { NoLabel },
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
	width: 20px;
	height: 20px;

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

/* .custom-wraper {
  height: 500px;
  overflow-y: auto;

  scroll-behavior: smooth;
} */
.custom-btn {
	font-size: 14px;
	font-weight: 400;
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
</style>

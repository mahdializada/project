<template>
	<v-card-text class="ps-2 pe-1">
		<div
			id="custom-scroll"
			class="custom-wraper pe-1"
			style="position: relative"
		>
			<template v-if="!skeleton_loading">
				<div v-if="label_categories.length > 0 && has_label.length > 0">
					<div v-for="(category, index) in label_categories" :key="index">
						<div v-if="category.label.length > 0">
							<div class="d-flex align-center flex-nowrap mb-2 mt-1">
								<p
									class="mb-0 me-1 custom-card-title-2"
									style="white-space: nowrap"
								>
									{{ category.title }}
								</p>
								<div
									class="rounded me-1"
									style="
										padding-top: 1px;
										width: 100%;
										background-color: rgb(212, 212, 212);
									"
								></div>
							</div>
							<div class="d-flex flex-wrap">
								<div
									outlined
									class="me-1 mb-1 custom-chip d-flex align-center"
									:class="selected_labels.includes(label.id) ? 'selected' : ''"
									v-for="(label, index) in category.label"
									:key="index"
								>
									<div
										class="label-color me-1 white--text text-center text-uppercase"
										:style="'background-color:' + label.color"
									>
										{{ label.label.charAt(0) }}
									</div>
									<span style="line-height: 10px">{{ label.label }}</span>
									<v-icon
										small
										v-html="
											selected_labels.includes(label.id)
												? 'mdi-minus'
												: 'mdi-plus'
										"
										style="color: inherit; line-height: 5px"
										class="mx-1"
									></v-icon>
								</div>
							</div>
						</div>
					</div>
				</div>
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
export default {
	data() {
		return {
			showDialog: false,
			api_loading: false,
			selected_labels: [],
			label_categories: [],
			has_label: [],
			skeleton_loading: false,
			attrs_sk: {
				style: "margin-bottom:0.2px",
				boilerplate: true,
				elevation: 0,
			},
		};
	},
	props: {
		subsystem_name: {
			type: String,
			require: true,
		},

		selected_item: {
			type: Object,
			require: true,
		},
		model: {
			type: String,
		},
	},
	created() {
		this.setSelectedLabels();
		this.getLabels(this.model);
	},
	methods: {
		toggleDialog(model) {
			this.setSelectedLabels();
			this.getLabels(model);
		},

		async getLabels(model) {
			try {
				this.skeleton_loading = true;
				const response = await this.$axios.get("get-label-history", {
					params: {
						id: this.selected_item.id,
						tab: model,
					},
				});
				this.label_categories = response.data;
				this.has_label = this.label_categories.filter(
					(row) => row.label.length > 0
				);
				this.totalLabel();
			} catch (error) {
			}
			this.skeleton_loading = false;
		},

		setSelectedLabels() {
			this.selected_labels = [];
			this.selected_item.labels.forEach((row) => {
				this.selected_labels.push(row.id);
			});
		},
		totalLabel() {
			let total_label = 0;
			this.label_categories.forEach((row) => {
				row.label.forEach((element) => {
					total_label++;
				});
			});
			this.$emit("setTitleData", {
				totals: [{ name: "total_labels", total: total_label }],
			});
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

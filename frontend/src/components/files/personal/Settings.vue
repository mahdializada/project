<template lang="">
	<div>
		<div
			ref="container"
			:class="`file-content pa-2 bg-light-gray rounded-lg mt-2 show-top-bar2`"
		>
			<v-card class="">
				<v-row class="pa-0 ma-0 h-full">
					<v-col cols="12" md="8" class="py-1">
						<v-row class="pa-0 ma-0">
							<v-col cols="12" md="3" class="px-0">
								<p class="pa-0 ma-0 title">{{ $tr("settings") }}</p>
							</v-col>
							<v-col cols="12" md="9" class="pb-1">
								<p>
									{{
										items.limited_size == "unlimited"
											? $tr("you_have_unlimited_storage_size")
											: $tr("you_can_request_to_buy_more_storage")
									}}
								</p>
								<v-btn
									v-if="items.storageRequest == null"
									:disabled="items.limited_size == 'unlimited'"
									@click="showRequestStorageCard = true"
									color="primary"
									outlined
									class="mt-1"
								>
									{{ $tr("request_for_more_storage") }}
								</v-btn>

								<v-card
									v-if="showRequestStorageCard"
									class="my-1 pa-2 blue lighten-5"
								>
									<v-radio-group
										style="margin-top: 0 !important"
										v-model="requestStorage.type"
										hide-details
										row
										dense
									>
										<v-radio value="limited" :label="$tr('limited')"></v-radio>
										<v-radio
											value="unlimited"
											:label="$tr('unlimited')"
										></v-radio>
									</v-radio-group>

									<div class="mt-1 d-md-flex flex-column flex-md-row">
										<v-text-field
											:disabled="requestStorage.type !== 'limited'"
											hide-details
											dense
											outlined
											rounded
											class="rounded-pill me-md-1 my-1"
											placeholder="Size"
											type="number"
											v-model="requestStorage.amount"
											min="0"
										></v-text-field>
										<v-select
											:disabled="requestStorage.type !== 'limited'"
											append-icon="mdi-chevron-down"
											class="w-lg-3-12 me-md-1 my-1 storage"
											:items="['MB', 'GB']"
											rounded
											hide-details
											outlined
											placeholder=""
											dense
											v-model="requestStorage.size"
										></v-select>
										<v-btn
											class="w-2-12 me-1 font-weight-medium my-1"
											hide-details
											dense
											rounded
											color="primary"
											:loading="isRequestingStorage"
											@click="onRequestStorage"
											>{{ $tr("request") }}</v-btn
										>
										<v-btn
											class="w-2-12 primary--text font-weight-medium my-1"
											hide-details
											dense
											text
											rounded
											@click="showRequestStorageCard = false"
											>{{ $tr("cancel") }}</v-btn
										>
									</div>
								</v-card>
								<v-alert
									v-if="items.storageRequest != null"
									color="primary"
									border="left"
									colored-border
									text
									dense
									icon="mdi-clock-fast"
									class="my-1"
								>
									<p class="text-wrap">
										{{
											$tr("your_storage_request_for") +
											" " +
											(items.storageRequest.type != "unlimited"
												? items.storageRequest.amount
												: "unlimited") +
											" " +
											(items.storageRequest.type != "unlimited"
												? items.storageRequest.size
												: "") +
											" " +
											$tr("is_in_pending")
										}}
									</p>
									<v-btn
										@click="cancelRequest"
										:loading="loadingCancel"
										class="mt-1"
										color="primary"
										outlined
										>{{ $tr("cancel_request") }}</v-btn
									>
								</v-alert>
							</v-col>
						</v-row>

						<v-divider class="mt-1"></v-divider>
						<v-row class="pa-0 ma-0">
							<v-col cols="12" md="3" class="px-0">
								<p class="pa-0 ma-0 title">
									{{ $tr("default_view") }}
								</p>
							</v-col>
							<v-col cols="12" md="9">
								<p>
									{{
										$tr(
											"choose_a_default_view_for_my_folders_team_folders_and_favorites"
										)
									}}
								</p>
								<v-radio-group
									v-model="radioGroupGrid"
									@change="defaultView"
									hide-details
								>
									<v-radio value="grid">
										<template v-slot:label>
											<v-icon class="me-1"> mdi-view-grid</v-icon>
											{{ $tr("thumbnail") }}
										</template>
									</v-radio>

									<v-radio value="list">
										<template v-slot:label>
											<div>
												<v-icon class="me-1">mdi-format-list-bulleted</v-icon>
												{{ $tr("list") }}
											</div>
										</template>
									</v-radio>
								</v-radio-group>
							</v-col>
						</v-row>
						<v-divider></v-divider>
						<v-row class="pa-0 ma-0">
							<v-col cols="12" md="3" class="px-0">
								<p class="pa-0 ma-0 title">
									{{ $tr("default_sorting") }}
								</p>
							</v-col>
							<v-col cols="12" md="9">
								<p>
									{{
										$tr(
											"choose_a_default_sort_option_for_my_folders_team_folders_and_favorites"
										)
									}}
								</p>
								<v-radio-group
									v-model="radioGroupSort"
									@change="defaultSorting"
									hide-details
								>
									<v-radio value="name" :label="$tr('name')"> </v-radio>

									<v-radio value="size" :label="$tr('size')"> </v-radio>

									<v-radio value="date_modified" :label="$tr('date_modified')">
									</v-radio>
								</v-radio-group>
							</v-col>
						</v-row>
					</v-col>
					<v-col cols="12" md="4" class="px-0 pe-md-1 py-1">
						<v-card class="surface h-full">
							<v-card-title class="justify-center mb-2">
								<v-progress-circular
									:indeterminate="loading"
									:rotate="-90"
									:size="180"
									:width="15"
									:value="fesadi"
									:color="progressColor"
								>
									<span class="d-flex flex-column align-center">
										<p class="pa-0 ma-0">
											{{ items.used_size | getFileSize(2) }}
										</p>
										<p
											class="pa-0 ma-0 surface--text text--darken-2"
											style="font-size: 14px !important"
										>
											{{ $tr("of") }}
											{{ fullSize }}
											{{ $tr("used") }}
										</p>
									</span>
								</v-progress-circular>
								<span class="ms-2">
									<div>
										<v-avatar :color="progressColor" size="10"></v-avatar>
										{{ $tr("used") }}
									</div>
									<div>
										<v-avatar color="surface darken-2" size="10"></v-avatar>
										{{ $tr("free") }}
									</div>
								</span>
							</v-card-title>
							<v-card-text>
								<div class="d-flex justify-space-between font-weight-bold mb-1">
									<p class="text-no-wrap">
										{{ $tr("total") }} :
										<span>
											{{ fullSize }}
										</span>
									</p>
									<p class="text-no-wrap">
										{{ $tr("used") }}:
										<span>{{ items.used_size | getFileSize(2) }}</span>
									</p>
								</div>
								<p class="text-no-wrap font-weight-bold">
									{{ $tr("free") }} :
									<span>{{ freeSize }}</span>
								</p>
								<v-divider></v-divider>
								<div class="overflow-auto" style="height: 40vh">
									<SettingsCard
										v-for="item in cardItems"
										:key="item.name"
										:name="item.name"
										:FileSize="items[item.name + '_used']"
										:FileAmount="fileAmounts[item.name]"
										:colorIcon="item.color"
										:icon="item.icon"
									></SettingsCard>
								</div>
							</v-card-text>
						</v-card>
					</v-col>
				</v-row>
			</v-card>
		</div>
	</div>
</template>
<script>
import SettingsCard from "./SettingsCard.vue";
import Alert from "~/helpers/alert";
import HandleException from "~/helpers/handle-exception";
export default {
	components: {
		SettingsCard,
	},
	props: {
		items: {
			type: Object,
			default: () => ({}),
			require: true,
		},
		loading: {
			type: Boolean,
		},
		fileAmounts: {
			type: Object,
			default: () => ({}),
			require: true,
		},
	},
	data() {
		return {
			loadingCancel: false,
			cardItems: [
				{ name: "documents", color: "primary", icon: "mdi-file" },
				{ name: "images", color: "red", icon: "mdi-image" },
				{ name: "videos", color: "purple", icon: "mdi-filmstrip-box" },
				{ name: "audios", color: "green", icon: "mdi-music" },
				{ name: "others", color: "orange", icon: "mdi-file" },
				{ name: "share_files", color: "teal", icon: "mdi-folder-download" },
			],

			showRequestStorageCard: false,
			isRequestingStorage: false,
			progressColor: "primary",
			requestStorage: {
				amount: 1,
				size: "MB",
				type: "limited",
			},
			sizeRules: [
				(v) => !!v || "This field is required",
				(v) => (v && v >= 1) || "Size should be at least 1",
			],
		};
	},

	computed: {
		fesadi: function () {
			if (this.items.limited_size == "unlimited") {
				return 1;
			} else {
				const total = this.items.limited_size;
				const used = this.items.used_size;
				return (used * 100) / total;
			}
		},
		radioGroupGrid: {
			get() {
				return this.items.default_view;
			},
			set(newValue) {
				this.items.default_view = newValue;
			},
		},
		radioGroupSort: {
			get() {
				return this.items.default_sorting;
			},
			set(newValue) {
				this.items.default_sorting = newValue;
			},
		},
		fullSize: function () {
			return this.items.limited_size == "unlimited"
				? "unlimited"
				: this.$options.filters.getFileSize(this.items.limited_size);
		},

		freeSize: function () {
			const value = this.items;
			return value.limited_size == "unlimited"
				? "unlimited"
				: this.$options.filters.getFileSize(
						value.limited_size - value.used_size
				  );
		},
	},

	watch: {
		fesadi: function (val) {
			if (val > 95) {
				this.progressColor = "error";
			}
		},
	},

	methods: {
		async defaultView(radio) {
			try {
				await this.$axios.post(`files/personal/setSettings`, {
					default_view: radio,
				});
			} catch (error) {}
		},
		async defaultSorting(radio) {
			try {
				await this.$axios.post(`files/personal/setSettings`, {
					default_sorting: radio,
				});
			} catch (error) {}
		},
		async onRequestStorage() {
			if (
				this.requestStorage.amount >= 1 ||
				this.requestStorage.type === "unlimited"
			) {
				try {
					this.isRequestingStorage = true;
					const response = await this.$axios.post(
						"storage-requests",
						this.requestStorage
					);
					this.$parent.fetchDataSettings();
					if (response.data.message == "pending") {
						// this.$toastr.w(
						// 	"Your request is in pending, please wait for the response"
						// );
						this.$toasterNA("orange",'Your request is in pending, please wait for the response');

					} else {
						// this.$toastr.s("Your request has been sent successfully");
				this.$toasterNA("green", "Your request has been sent successfully");

					}
					this.isRequestingStorage = false;
					this.requestStorage = {
						amount: 1,
						size: "MB",
						type: "limited",
					};
					this.showRequestStorageCard = false;
				} catch (error) {
					HandleException.handleApiException(this, error);
					this.isRequestingStorage = false;
				}
			} else {
				this.isRequestingStorage = false;
			}
		},
		async cancelRequest() {
			Alert.confirmAlert(
				this,
				async () => {
					this.loadingCancel = true;
					const id = this.items.storageRequest.id;
					try {
						const res = await this.$axios.delete(`storage-requests/${id}`);
						if (res.data.result == true) {
							this.$parent.fetchDataSettings();
							// this.$toastr.i("Your request has been canceled successfully");
							this.$toasterNA("primary",this.$tr('Your request has been canceled successfully'));

						} else if (res.data.result == false) {
							this.$parent.fetchDataSettings();
							// this.$toastr.i("something went wrong");
							this.$toasterNA("red",this.$tr('something_went_wrong'));

						}
						this.loadingCancel = false;
					} catch (error) {}
				},
				"",
				"warning",
				"are_you_sure"
			);
		},
	},
	async mounted() {
		this.$echo
			.private(`update.request-storage`)
			.listen("Updated", async (e) => {
				if (e.action == "status-changed") {
					if (e.data.new_status != "pending" && e.data.new_status.length > 0) {
						this.items.storageRequest = null;
					}
				}
			});
	},
	beforeDestroy() {
		this.$echo.leave("update.request-storage");
	},
};
</script>
<style lang="scss">
.storage .v-input__slot {
	padding: 0px 6px !important;
}
.show-top-bar2 {
	top: 60px !important;
}
p {
	margin-bottom: 0 !important;
}
</style>

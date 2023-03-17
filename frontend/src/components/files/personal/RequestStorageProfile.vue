<template>
	<v-card>
		<v-card-title class="pa-1">
			<h5>Storage Request</h5>
			<v-spacer></v-spacer>
			<close-btn @click="$emit('closeDialog')" :xsmall="true" />
		</v-card-title>
		<v-card-text>
			<v-card class="pa-2 custom-card-request">
				<div class="d-flex justify-space-between align-center flex-wrap">
					<div class="d-flex align-center">
						<v-avatar color="primary" class="white--text text-subtitle-1 font-weight-medium">
							<img :src="item.user.image" alt="" />
						</v-avatar>
						<div class="ps-3">
							<h4>{{ item.user.firstname + " " + item.user.lastname }}</h4>
							<v-tooltip bottom>
								<template v-slot:activator="{ on, attrs }">
									<span v-bind="attrs" v-on="on" style="white-space: nowrap" class="primary--text">
										{{ item.companies[0].name + " " + $tr("company") }}
									</span>
								</template>
								<div v-if="2 > 1">
									<div v-if="item.companies.length > 0">
										<p v-for="comapany in item.companies" :key="comapany.index" class="pb-0 mb-0">
											{{ comapany.name }}
										</p>
									</div>
									<div v-else>
										<p class="pb-0 mb-0">
											{{ $tr("no_item", $tr("system")) }}
										</p>
									</div>
								</div>
							</v-tooltip>
						</div>
					</div>

					<div class="d-flex justify-center align-center mt-1 mt-sm-0">
						<div
							class="rounded grey lighten-3 d-flex align-center justify-center"
							style="width: 40px; height: 40px"
						>
							<svg
								xmlns="http://www.w3.org/2000/svg"
								width="16.324"
								height="17.916"
								viewBox="0 0 14.324 15.916"
							>
								<path
									id="date"
									d="M9.275,10.162H7.683v1.592H9.275Zm3.183,0H10.866v1.592h1.592Zm3.183,0H14.05v1.592h1.592Zm1.592-5.571h-.8V3H14.845V4.592H8.479V3H6.887V4.592h-.8A1.584,1.584,0,0,0,4.508,6.183L4.5,17.324a1.591,1.591,0,0,0,1.592,1.592H17.233a1.6,1.6,0,0,0,1.592-1.592V6.183A1.6,1.6,0,0,0,17.233,4.592Zm0,12.733H6.092V8.571H17.233Z"
									transform="translate(-4.5 -3)"
									fill="#2e7be6"
								/>
							</svg>
						</div>
						<div class="ps-4 ps-md-2">
							<p class="mb-0 grey--text text-subtitle-2">{{ $tr("date_of_request") }}</p>
							<p class="font-weight-medium mb-0">{{ localeHumanReadableTime(item.created_at) }}</p>
						</div>
					</div>
				</div>
			</v-card>
		</v-card-text>
		<v-container class="pa-0">
			<v-row class="pa-0 mx-0">
				<v-col cols="12" md="5" class="ps-2 py-0 pe-sm-2">
					<div class="grey lighten-4 rounded-sm pa-1 text-center pb-3">
						<v-progress-circular
							:rotate="-90"
							:size="120"
							:width="10"
							:value="fesadi"
							color="primary"
							class="my-2"
						>
							<span class="d-flex flex-column align-center">
								<p class="pa-0 ma-0">
									{{ item.used_size | getFileSize() }}
								</p>
								<p
									class="pa-0 ma-0 surface--text text--darken-2"
									style="font-size: 12px !important"
								>
									{{ $tr("of") }}
									{{ item.current_size | getFileSize() }}
									{{ $tr("used") }}
								</p>
							</span>
						</v-progress-circular>
						<div class="d-flex justify-center">
							<div class="d-flex align-center pe-2">
								<div class="rounded-circle grey me-1" style="width: 12px; height: 12px"></div>
								<span>{{$tr("free")}}</span>
							</div>
							<div class="d-flex align-center">
								<div class="rounded-circle primary me-1" style="width: 12px; height: 12px"></div>
								<span>{{ $tr("used") }}</span>
							</div>
						</div>
					</div>
				</v-col>
				<v-col
					cols="12"
					md="7"
					class="ps-0 py-0 pe-1"
					:style="
						$vuetify.breakpoint.lg || $vuetify.breakpoint.md ? 'border-left:1px solid black' : ''
					"
				>
					<div>
						<v-list subheader two-line :max-width="!$vuetify.breakpoint.sm ? '330px' : '100%'">
							<v-list-item v-for="(storage, index) in storage_info_list" :key="index">
								<div
									class="me-1 rounded grey lighten-3 d-flex align-center justify-center"
									style="width: 40px; height: 40px"
								>
									<svg
										v-show="storage.icon == 'request_icon'"
										xmlns="http://www.w3.org/2000/svg"
										width="15.779"
										height="18.724"
										viewBox="0 0 11.779 14.724"
									>
										<path
											id="request-storage"
											d="M16.307,3h-5.89l-4.4,4.417L6,16.252a1.477,1.477,0,0,0,1.472,1.472h8.834a1.477,1.477,0,0,0,1.472-1.472V4.472A1.477,1.477,0,0,0,16.307,3ZM11.89,7.417H10.417V4.472H11.89Zm2.209,0H12.626V4.472H14.1Zm2.209,0H14.834V4.472h1.472Z"
											transform="translate(-6 -3)"
											fill="#2e7be6"
										/>
									</svg>

									<svg
										v-show="storage.icon == 'current_icon'"
										xmlns="http://www.w3.org/2000/svg"
										width="19.181"
										height="16.145"
										viewBox="0 0 17.181 14.145"
									>
										<g id="current-size" transform="translate(-11.91 -13.428)">
											<g
												id="Icon_feather-hard-drive"
												data-name="Icon feather-hard-drive"
												transform="translate(12.91 14.428)"
											>
												<path
													id="Path_108"
													data-name="Path 108"
													d="M18.181,18H3"
													transform="translate(-3 -11.928)"
													fill="none"
													stroke="#2e7be6"
													stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
												/>
												<path
													id="Path_109"
													data-name="Path 109"
													d="M5.619,6.843,3,12.072v4.554a1.518,1.518,0,0,0,1.518,1.518H16.663a1.518,1.518,0,0,0,1.518-1.518V12.072l-2.619-5.23A1.518,1.518,0,0,0,14.2,6H6.977a1.518,1.518,0,0,0-1.359.843Z"
													transform="translate(-3 -6)"
													fill="none"
													stroke="#2e7be6"
													stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
												/>
												<path
													id="Path_110"
													data-name="Path 110"
													d="M9,24H9"
													transform="translate(-5.964 -14.892)"
													fill="none"
													stroke="#2e7be6"
													stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
												/>
												<path
													id="Path_111"
													data-name="Path 111"
													d="M15,24h0"
													transform="translate(-8.928 -14.892)"
													fill="none"
													stroke="#2e7be6"
													stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
												/>
											</g>
											<path
												id="Path_112"
												data-name="Path 112"
												d="M16.822,14.366l-3.12,5.8,14.078.241-3.16-5.84Z"
												fill="#2e7be6"
											/>
										</g>
									</svg>

									<svg
										v-show="storage.icon == 'free_icon'"
										xmlns="http://www.w3.org/2000/svg"
										width="19.181"
										height="16.145"
										viewBox="0 0 17.181 14.145"
									>
										<g id="current-free-size" transform="translate(1 1)">
											<path
												id="Path_108"
												data-name="Path 108"
												d="M18.181,18H3"
												transform="translate(-3 -11.928)"
												fill="none"
												stroke="#2e7be6"
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
											/>
											<path
												id="Path_109"
												data-name="Path 109"
												d="M5.619,6.843,3,12.072v4.554a1.518,1.518,0,0,0,1.518,1.518H16.663a1.518,1.518,0,0,0,1.518-1.518V12.072l-2.619-5.23A1.518,1.518,0,0,0,14.2,6H6.977a1.518,1.518,0,0,0-1.359.843Z"
												transform="translate(-3 -6)"
												fill="none"
												stroke="#2e7be6"
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
											/>
											<path
												id="Path_110"
												data-name="Path 110"
												d="M9,24H9"
												transform="translate(-5.964 -14.892)"
												fill="none"
												stroke="#2e7be6"
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
											/>
											<path
												id="Path_111"
												data-name="Path 111"
												d="M15,24h0"
												transform="translate(-8.928 -14.892)"
												fill="none"
												stroke="#2e7be6"
												stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
											/>
										</g>
									</svg>
								</div>

								<v-list-item-content>
									<v-list-item-title v-html="$tr(storage.title)" />
								</v-list-item-content>

								<v-list-item-action class="primary--text font-wieght-bold">
									{{
										storage.title == "request_size" ? storage.size : getFileSize(storage.size, 2)
									}}
								</v-list-item-action>
							</v-list-item>
						</v-list>
					</div>
				</v-col>
			</v-row>
		</v-container>
		<v-card-actions class="pb-2">
			<v-spacer></v-spacer>

			<v-btn text color="primary" class="text-subtitle-2 px-2" @click="$emit('rejected')">
				{{ $tr("reject") }}
			</v-btn>

			<v-btn color="primary" class="text-subtitle-2 primary px-2" @click="$emit('approved')">
				{{ $tr("approve") }}
			</v-btn>
		</v-card-actions>
	</v-card>
</template>

<script>
import TextButton from "../../common/buttons/TextButton.vue";
import CloseBtn from "../../design/Dialog/CloseBtn.vue";
import moment from "moment-timezone";

export default {
	components: { CloseBtn, TextButton },
	props: {
		item: {
			type: Object,
		},
	},
	data() {
		return {
			storage_info_list: [
				{
					icon: "request_icon",
					title: "request_size",
					size:
						this.item.type == "unlimited" ? "unlimited" : this.item.amount + " " + this.item.size,
				},
				{
					icon: "current_icon",
					title: "current_size",
					size: this.item.current_size ,
				},
				{
					icon: "free_icon",
					title: "free_size",
					size: this.item.free_size,
				},
			],
		};
	},
	computed: {
		fesadi: function () {
			if (this.item.current_size == "unlimited") {
				return 1;
			} else {
				const total = this.item.current_size;
				const used = this.item.used_size;
				return (used * 100) / total;
			}
		},
	},
	methods: {
		localeHumanReadableTime(date) {
			return moment(date).locale(this.$vuetify.lang.current).fromNow();
		},
		getFileSize(bytes, decimals = 0) {
			if (bytes == 0 || bytes == null) return "0 B";
			const k = 1024;
			const dm = decimals < 0 ? 0 : decimals;
			const sizes = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
			const i = Math.floor(Math.log(bytes) / Math.log(k));
			return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + " " + sizes[i];
		},
	},
};
</script>

<style>
.custom-card-request {
	/* box-shadow: 0px 4px 0px var(--v-primary-base) !important; */
	border-bottom: var(--v-primary-base) 5px solid !important;
}
</style>

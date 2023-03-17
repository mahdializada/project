<template>
	<v-card :color="$vuetify.theme.dark ? 'black' : '#f9fafd'">
		<v-card-title class="pa-0">
			<div style="width: 100%">
				<div class="primary" style="min-height: 180px">
					<div class="d-flex align-center pa-2">
						<div
							class="title ms-3 font-weight-bold white--text"
							:class="
								$vuetify.breakpoint.sm || $vuetify.breakpoint.xs
									? ''
									: 'text-h5'
							"
						>
							{{ $tr(titleProfile) + " " + $tr("profile") }}
						</div>
						<v-spacer></v-spacer>
						<CloseBtn class="white--text" @click="$emit('click')" />
					</div>
				</div>
				<div
					class=""
					style="background-color: var(--v-surface-base)"
					:style="
						$vuetify.breakpoint.sm || $vuetify.breakpoint.xs
							? 'height:257px'
							: 'height:150px;'
					"
				>
					<v-card
						class="rounded mx-4 px-2 d-flex align-center"
						style="transform: translateY(-100px)"
						min-height="200"
					>
						<v-container fluid class="pa-0">
							<v-row class="py-0 my-0">
								<v-col cols="12 py-1 he" class="d-flex justify-space-between">
									<v-row>
										<v-col
											cols="12"
											class="
												d-flex
												justify-md-space-between justify-center
												align-center
												flex-column flex-md-row
												pa-0
												px-md-6
											"
										>
											<div
												class="
													d-flex
													flex-column flex-md-row
													justify-start
													align-center
													position-relative
												"
											>
												<div class="pe-2">
													<v-avatar
														color="grey lighten-3"
														:size="
															$vuetify.breakpoint.md ||
															$vuetify.breakpoint.sm ||
															$vuetify.breakpoint.xs
																? '90'
																: '140'
														"
													>
														<v-avatar
															color="primary"
															:size="
																$vuetify.breakpoint.md ||
																$vuetify.breakpoint.sm ||
																$vuetify.breakpoint.xs
																	? '80'
																	: '120'
															"
														>
															<img
																v-if="imageProfile"
																:src="imageProfile"
																alt=""
															/>
															<span v-else class="white--text text-h4">
																{{
																	profileData[name_selector]
																		? profileData[
																				name_selector
																		  ][0].toUpperCase()
																		: ""
																}}
															</span>
														</v-avatar>
													</v-avatar>
												</div>
												<div class="w-full">
													<div
														class="
															d-flex
															justify-center justify-md-start
															flex-column flex-sm-row
															align-center
														"
													>
														<div class="item-name">
															<h2
																class="
																	mb-0
																	text-center text-md-start
																	primary--text
																"
																:style="
																	$vuetify.breakpoint.md ||
																	$vuetify.breakpoint.sm ||
																	$vuetify.breakpoint.xs
																		? 'font-size:15px'
																		: 'font-size:20px'
																"
															>
																{{ profileData[name_selector] }}
															</h2>
														</div>
													</div>

													<div
														class="mt-2 d-flex justify-center justify-md-start"
														:style="
															$vuetify.breakpoint.xs
																? 'display:inline'
																: ''
														"
													>
														<v-chip
															:color="statusColor"
															outlined
															style="
																height: 40px;
																width: 100px;
																border-radius: 50px;
															"
															class="d-flex justify-center"
														>
															<span class="">
																{{ $tr(profileData.status) }}
															</span>
														</v-chip>
													</div>
												</div>
											</div>

											<div class="d-flex align-center flex-column flex-md-row">
												<span
													v-if="profileData.created_by"
													class="me-md-5 me-0"
												>
													<v-icon color="primary" class="me-1">
														mdi-account-plus</v-icon
													>
													{{ profileData.created_by.lastname }}
												</span>
												<span
													v-if="
														!(
															$vuetify.breakpoint.md ||
															$vuetify.breakpoint.sm ||
															$vuetify.breakpoint.xs
														)
													"
													style="
														background-color: var(--v-surface-darken1);
														border-radius: 50px;
													"
													class="px-3 d-flex di"
												>
													<v-tooltip bottom>
														<template v-slot:activator="{ on, attrs }">
															<span v-bind="attrs" v-on="on">
																<div
																	v-if="profileData.created_at"
																	class="me-2 hover font-weight-regular"
																>
																	<v-avatar
																		size="40"
																		class="blue lighten-4 my-1"
																	>
																		<v-icon color="primary"
																			>mdi-calendar-month-outline
																		</v-icon>
																	</v-avatar>
																	<span>
																		{{
																			localeHumanReadableTime(
																				profileData.created_at
																			)
																		}}
																	</span>
																</div></span
															>
														</template>
														<span>{{ $tr("created_at") }}</span>
													</v-tooltip>

													<v-divider
														v-if="profileData.updated_at"
														class="mb-1"
														inset
														vertical
													>
													</v-divider>

													<v-tooltip bottom>
														<template v-slot:activator="{ on, attrs }">
															<span v-bind="attrs" v-on="on">
																<div
																	v-if="profileData.updated_at"
																	class="ms-2 hover font-weight-regular"
																>
																	<v-avatar
																		size="40"
																		class="blue lighten-4 my-1"
																	>
																		<v-icon color="primary"
																			>mdi-calendar-refresh
																		</v-icon>
																	</v-avatar>
																	{{
																		localeHumanReadableTime(
																			profileData.updated_at
																		)
																	}}
																</div></span
															>
														</template>
														<span>{{ $tr("updated_at") }}</span>
													</v-tooltip>
												</span>

												<span v-else>
													<span
														style="
															background-color: var(--v-surface-darken1);
															border-radius: 50px;
														"
														class="px-3 d-flex di my-1"
													>
														<v-tooltip bottom>
															<template v-slot:activator="{ on, attrs }">
																<span v-bind="attrs" v-on="on">
																	<div v-if="profileData.created_at" class="font-weight-regular">
																		<v-avatar
																			:size="
																				$vuetify.breakpoint.xs
																					? '20'
																					: '40'
																			"
																			class="blue lighten-4 my-1"
																		>
																			<v-icon color="primary"
																				>mdi-calendar-month-outline
																			</v-icon>
																		</v-avatar>
																		{{
																			localeHumanReadableTime(
																				profileData.created_at
																			)
																		}}
																	</div></span
																>
															</template>
															<span><span>{{ $tr("created_at") }}</span></span>
														</v-tooltip>
													</span>

													<span
														style="
															background-color: var(--v-surface-darken1);
															border-radius: 50px;
														"
														class="px-3 d-flex di"
													>
														<div v-if="profileData.updated_at" class="font-weight-regular">
															<v-avatar
																:size="
																	$vuetify.breakpoint.xs ? '20' : '40'
																"
																class="blue lighten-4 my-1"
															>
																<v-icon color="primary"
																	>mdi-calendar-refresh
																</v-icon>
															</v-avatar>
															{{
																localeHumanReadableTime(profileData.updated_at)
															}}
														</div>
													</span>
												</span>
											</div>
										</v-col>
									</v-row>
								</v-col>
							</v-row>
						</v-container>
					</v-card>
				</div>
			</div>
		</v-card-title>

		<v-card-text style="background-color: var(--v-surface-base)">
			<v-container class="pa-0">
				<v-tabs
					v-model="tabs"
					class="custome"
					hide-slider
					grow
					center-active
					show-arrows
				>
					<div class="d-flex w-full align-end">
						<v-tab v-for="(item, n) in tabitem" :key="n" style="height: 100%">
							{{ $tr(item.tab) }}
						</v-tab>

						<div
							class="w-full"
							style="
								height: 1px;
								width: 1px;
								background-color: var(--v-surface-darken1);
							"
						>
						</div>
					</div>
					<CustomButton v-if="isEditBtn && !isOpenEdit" icon="fa-user-edit" text="edit" type="primary" @click="()=> {$emit('onEdit')}"/>						
					<CustomButton v-if="isEditBtn && isOpenEdit" :loading="isLoading" icon="fa-check" text="save" type="primary" @click="()=> {$emit('onSave')}"/>						
					<CustomButton v-if="isEditBtn && isOpenEdit" icon="fa-ban" text="cancel" type="danger" @click="()=> {$emit('cancel')}"/>						
				</v-tabs>
				<v-tabs-items v-model="tabs">
					<v-tab-item v-for="(item, index) in tabitem" :key="index">
						<v-card
							max-height="400"
							style="background-color: inherit"
							elevation="0"
						>
						<slot name="btn"></slot>
							<v-card
								outlined
								elevation="0"
								class="overflow-y-auto overflow-x-hidden"
								style="background-color: inherit; height: 30vh"
								:class="mt"
							>
								<slot :name="'slot_' + item.tab"></slot>
							</v-card>
						</v-card>
					</v-tab-item>
				</v-tabs-items>
			</v-container>
		</v-card-text>
	</v-card>
</template>
<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import moment from "moment-timezone";
import CustomButton from "../design/components/CustomButton.vue";

export default {
	components: { CloseBtn, CustomButton },
	props: {
		name_selector: { type: String, default: "name" },
		titleProfile: {
			type: String,
			required: true,
		},
		profileData: {
			type: Object,
			required: true,
		},
		tabitem: {
			type: Array,
			required: true,
		},
		imageProfile: {
			type: String,
		},
		mt:{
			type:String,
			default:'mt-2'
		},
		isEditBtn:{
			type:Boolean,
			default:false,
		},
		isOpenEdit:{
			type:Boolean,
			default:false,
		},
		isLoading:{
			type:Boolean,
			default:false,
		},
	},
	methods: {
		localeHumanReadableTime(date) {
			return moment(date)
				.locale(this.$vuetify.lang.current)
				.format("YYYY-MM-DD h:mm: A");
		},
		onEdit(){
			this.$emit('onEdit')
		},
	},
	computed: {},
	data() {
		return {
			selected_tab: 0,
			tabs: null,
			statusColor: "",
		};
	},
	mounted() {
		switch (this.profileData.status) {
			case "inactive":
				this.statusColor = "warning";
				break;
			case "active":
				this.statusColor = "success";
				break;
			case "removed":
				this.statusColor = "error";
				break;
			case "pending":
				this.statusColor = "warning";
				break;
			case "blocked":
				this.statusColor = "error";
				break;
			case "warning":
				this.statusColor = "warning";
				break;
			default:
				this.statusColor = "primary";
		}
	},
};
</script>

<style>
.custome .v-slide-group__wrapper {
	background-color: var(--v-surface-base);
}

.custome .v-tab.v-tab--active,.customeBtn  {
	border: 1px solid var(--v-surface-darken1);
	border-bottom: 1px solid transparent !important;
	border-radius: 3px 3px 0 0;
}
.custome .v-tab {
	border-bottom: 1px solid var(--v-surface-darken1);
	border-top: 1px solid transparent;
}
.di .theme--light.v-divider,
.di .theme--dark.v-divider {
	border: 1px solid rgba(0, 0, 0, 0.09) !important;
}
.custome .v-slide-group__next,
.custome .v-slide-group__prev {
	background-color: var(--v-surface-base);
}
.hover:hover {
	cursor: pointer;
}
</style>

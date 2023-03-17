<template>
	<div>
		<v-dialog v-model="locationsView" width="1000" persistent>
			<v-card>
				<v-card elevation="24" style="margin-bottom: 10px">
					<v-card-title style="color: #777" class="px-2 pt-2 pb-2">
						<div class="dialog-title d-flex align-center">
							{{ $tr("location_details") }}
							<div
								class="remarks-number ms-1"
								:style="`background: ${$vuetify.theme.defaults.light.primary}`"
							></div>
						</div>
						<v-spacer></v-spacer>
						<DialogCloseBtn class="close-dialog-btn" @click="closeDialog" />
					</v-card-title>
				</v-card>
				<v-card-text
					style="
						box-shadow: none;
						overflow-y: scroll;
						overflow-x: hidden;
						height: 500px;
					"
				>
				<div v-if="locationskeleton">
					<v-skeleton-loader
					v-for="i in 4"
					:key="i"
					:loading="load"
					v-bind="attrs"
					height="70px"
					style="margin-bottom: -0.5rem !important"
					type="list-item-avatar, divider"
					></v-skeleton-loader>
				</div>
				<div v-if="locationItems.length > 0  && locationskeleton == false">
					<template>
							<v-expansion-panels v-model="panel" multiple>
								<v-expansion-panel v-for="item in locationItems" :key="item.id">
									<v-expansion-panel-header
										hide-actions
										@click.native.stop="isEdit = false"
										style="margin-bottom: -2rem; margin-top: -2rem"
									>
										<template v-slot:default="{ open }">
											<v-col cols="12" md="12">
												<v-avatar
													style="
														margin-bottom: -2rem;
														width: 80px;
														height: 80px;
														padding: 10px;
														border-radius: 50pc;
														color: white;
													"
												>
													<FlagIcon :flag="item.country?.iso2" :round="true" />
												</v-avatar>
												&nbsp;&nbsp;
												<span style="font-size: 1.5rem">
													{{ item.country.name }}
												</span>
												<br /><br />
												<span
													:class="`${
														$vuetify.rtl ? 'statenameRtl' : 'statename'
													}`"
												>
													{{ item.state.name }}
												</span>
											</v-col>
											<p style="z-index: 2">
												<v-btn
													:class="`${$vuetify.rtl ? 'editbtnRtl' : 'editbtn'}`"
													@click.native.stop="onEdit(item)"
													v-if="open && !isEdit"
													class="primary sm"
													>Edit</v-btn
												>
												<v-btn
													:class="`${$vuetify.rtl ? 'savebtnRtl' : 'savebtn'}`"
													v-if="open && isEdit"
													@click.native.stop="update(item.id)"
													:disabled="!myForm"
													type="submit"
													:loading="isLoading"
													class="success"
													>Save</v-btn
												>
												<v-btn
													:class="`${
														$vuetify.rtl ? 'cancelbtnRtl' : 'cancelbtn'
													}`"
													class="error sm"
													v-if="open && isEdit"
													@click="isEdit = false"
													>Cancel</v-btn
												>
											</p>
											<v-icon
												:class="`${$vuetify.rtl ? 'chevronRtl' : 'chevron'}`"
												>{{
													open
														? "mdi-chevron-up-circle-outline"
														: "mdi-chevron-down-circle-outline"
												}}</v-icon
											>
										</template>
									</v-expansion-panel-header>
										<v-expansion-panel-content>
											<div v-if="isEdit">
												<v-col
													cols="12"
													style="margin-bottom: -4rem !important"
												>
													<h3 style="color: #777">
														{{ $tr("location_information") }}
													</h3>
												</v-col>
												<v-form
													lazy-validation
													ref="validationFormRef"
													v-model="myForm"
												>
													<div class="location">
														<v-card-text>
															<div>
																<div class="h-full d-flex">
																	<InputCard
																		:title="
																			$tr('label_required', $tr('country'))
																		"
																		:hasSearch="true"
																		:hasPagination="false"
																		class="mb-0 pb-0"
																		@search="(v) => (searchCountry = v)"
																	>
																		<ItemsContainer
																			class="mb-0 pb-0"
																			v-model="form.country_id"
																			:items="filterCountry"
																			:loading="fetchingCountries"
																			:no_data="
																				filterCountry.length < 1 &&
																				!fetchingCountries
																			"
																			@input="selectedCountry"
																			height="150px"
																		></ItemsContainer>
																	</InputCard>
																</div>
																<div class="h-full d-flex">
																	<div class="w-full">
																		<CAutoComplete
																			v-model="form.state_id"
																			:title="
																				$tr('label_required', $tr('state'))
																			"
																			:items="states"
																			item-value="id"
																			item-text="name"
																			:placeholder="
																				$tr('choose_item', $tr('state'))
																			"
																			:loading="isFetchingStates"
																			prepend-inner-icon="#"
																			:rules="stateRules"
																		/>
																	</div>

																	<div class="w-full mt-2">
																		<c-radio-w-ith-body
																			:items="radioItem"
																			v-model="form.location_type"
																			:text="$tr('location_type')"
																			@onChange="
																				(v) => (form.location_type = v)
																			"
																		/>
																	</div>
																</div>
																<div class="h-full d-flex">
																	<div class="w-full">
																		<CTextField
																			v-model="form.map_link"
																			:title="
																				$tr('label_required', $tr('map_link'))
																			"
																			:placeholder="$tr('map_link')"
																			prepend-inner-icon="mdi-link-variant"
																			:rules="mapRules"
																		/>
																	</div>
																	<div class="w-full">
																		<CTextField
																			v-model="form.location_phone"
																			:title="
																				$tr(
																					'label_required',
																					$tr('location_phone')
																				)
																			"
																			:placeholder="$tr('location_phone')"
																			prepend-inner-icon="mdi-link-variant"
																			:rules="phoneRules"
																		/>
																	</div>
																</div>
																<div class="h-full d-flex">
																	<div class="w-full">
																		<CAutoComplete
																			v-model="form.crowd_status"
																			:title="
																				$tr(
																					'label_required',
																					$tr('crowd_status')
																				)
																			"
																			:items="crowdStatuses"
																			item-value="id"
																			item-text="name"
																			:placeholder="
																				$tr('choose_item', $tr('crowd_status'))
																			"
																			prepend-inner-icon="#"
																			:rules="crowdRules"
																		/>
																	</div>
																	<div class="w-full">
																		<CTextField
																			v-model="form.contact_staff_name"
																			:title="
																				$tr(
																					'label_required',
																					$tr('contact_staff_name')
																				)
																			"
																			:placeholder="$tr('contact_staff_name')"
																			prepend-inner-icon="mdi-link-variant"
																			:rules="staffRules"
																		/>
																	</div>
																</div>
																<div class="h-full d-flex">
																	<div class="w-full">
																		<CTextField
																			v-model="form.address"
																			:title="
																				$tr('label_required', $tr('address'))
																			"
																			:placeholder="$tr('address')"
																			prepend-inner-icon="mdi-link-variant"
																			:rules="addressRules"
																		/>
																	</div>
																</div>
															</div>
														</v-card-text>
													</div>
												</v-form>
											</div>
											<div v-else-if="!isEdit">
												<v-col
													cols="12"
													style="margin-bottom: -4rem !important"
												>
													<h3 style="color: #777">
														{{ $tr("location_information") }}
													</h3>
												</v-col>
												<v-container style="margin-left: 1rem !important">
													<v-row v-if="item.country_id">
														<v-col cols="5"
															><strong style="color: #777"
																>{{ $tr("country") }}
															</strong></v-col
														>
														<v-col cols="5" id="span_right_side_styles">{{
															item.country.name
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row style="background-color: #faf8faff">
														<v-col cols="5"
															><strong style="color: #777"
																>{{ $tr("state") }}
															</strong></v-col
														>
														<v-col cols="5" id="span_right_side_styles">{{
															item.state.name
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row style="">
														<v-col cols="5"
															><strong style="color: #777">{{
																$tr("location_type")
															}}</strong>
														</v-col>
														<v-col cols="5" id="span_right_side_styles">{{
															item.location_type
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row style="background-color: #faf8faff">
														<v-col cols="5"
															><strong style="color: #777">
																{{ $tr("address") }}</strong
															>
														</v-col>
														<v-col cols="5" id="span_right_side_styles">{{
															item.address
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row style="">
														<v-col cols="5"
															><strong style="color: #777">
																{{ $tr("map_link") }}
															</strong></v-col
														>
														<v-col cols="5" id="span_right_side_styles">{{
															item.map_link
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row style="background-color: #faf8faff">
														<v-col cols="5"
															><strong style="color: #777">{{
																$tr("location_phone")
															}}</strong>
														</v-col>
														<v-col cols="5" id="span_right_side_styles">{{
															item.location_phone
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row style="">
														<v-col cols="5"
															><strong style="color: #777">{{
																$tr("supplier_name")
															}}</strong>
														</v-col>
														<v-col cols="5" id="span_right_side_styles">{{
															item.supplier.supplier_name
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
													<v-row style="background-color: #faf8faff">
														<v-col cols="5"
															><strong style="color: #777"
																>{{ $tr("staff_name") }}
															</strong></v-col
														>
														<v-col cols="5" id="span_right_side_styles">{{
															item.contact_staff_name
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>

													<v-row>
														<v-col cols="5"
															><strong style="color: #777"
																>{{ $tr("crowd_status") }}
															</strong></v-col
														>
														<v-col cols="5" id="span_right_side_styles">{{
															item.crowd_status
														}}</v-col>
														<v-col cols="2"></v-col>
													</v-row>
												</v-container>
											</div>
										</v-expansion-panel-content>
										
									</v-expansion-panel>
								</v-expansion-panels>
							</template>
						</div>
					<div v-else-if="locationItems.length == 0 && locationskeleton == false">
							<h3 style="text-align: center ">No Data</h3>
						</div>
				</v-card-text>
			</v-card>
		</v-dialog>
	</div>
</template>

<script>
import DialogCloseBtn from "../design/Dialog/CloseBtn.vue";
import CustomInput from "../design/components/CustomInput.vue";
import DoneMessage from "../design/components/DoneMessage.vue";
import CustomStepper from "../design/FormStepper/CustomStepper.vue";
import ItemsContainer from "../new_form_components/cat_product_selection/ItemsContainer.vue";
import InputCard from "../new_form_components/components/InputCard.vue";
import CAutoComplete from "../new_form_components/Inputs/CAutoComplete.vue";
import CRadioButton from "../new_form_components/Inputs/CRadioButton.vue";
import CRadioWIthBody from "../new_form_components/Inputs/CRadioWIthBody.vue";
import CTextArea from "../new_form_components/Inputs/CTextArea.vue";
import CTextField from "../new_form_components/Inputs/CTextField.vue";
import FlagIcon from "../common/FlagIcon.vue";
export default {
	components: {
		CTextField,
		InputCard,
		ItemsContainer,
		CustomInput,
		CTextArea,
		CRadioButton,
		CRadioWIthBody,
		CAutoComplete,
		CustomStepper,
		DoneMessage,
		DialogCloseBtn,
		FlagIcon,
	},
	data() {
		return {
			panel:[0],
			form: [
				{
					country_id: "",
					state_id: "",
					location_type: "",
					map_link: "",
					location_phone: "",
					crowd_status: "",
					contact_staff_name: "",
					address: "",
				},
			],
			radioItem: [
				{
					value: 1,
					label: "Main",
				},
				{
					value: 0,
					label: "Sub",
				},
			],
			header: [
				{
					icon: "fa-globe",
					title: "Location",
					slotName: "step1",
				},
				{
					icon: "fa-thumbs-up",
					title: "done",
					slotName: "step2",
				},
			],
			crowdStatuses: [
				{ id: "low", name: "low" },
				{ id: "crowd", name: "crowd" },
			],
			attrs: {
				class: "mb-6",
				boilerplate: true,
				elevation: 2,
			},
			stateRules: [(v) => !!v || "State is required"],
			mapRules: [(v) => !!v || "Map Link is required"],
			staffRules: [(v) => !!v || "Contact Staff Name is required"],
			addressRules: [(v) => !!v || "Address is required"],
			crowdRules: [(v) => !!v || "Crowd Status is required"],
			phoneRules: [(v) => !!v || "Location Phone is required"],

			registerLocationStepper: "",
			loading: false,
			searchCountry: "",
			countries: [],
			locationsView: false,
			fetchingCountries: false,
			isFetchingStates: false,
			states: [],
			supplier_id: "",
			isEdit: false,
			locationItems: [],
			
			isLoading: false,
			myForm: true,
			locationskeleton: false,
			load: false,
		};
	},
	computed: {
		filterCountry: function () {
			if (this.searchCountry) {
				const filter = (item) =>
					item?.name
						?.toLowerCase()
						?.includes(this.searchCountry?.toLowerCase());
				return this.countries.filter(filter);
			}
			return this.countries;
		},
	},
	methods: {
		onEdit(item) {
			console.log(item);
			this.isEdit = true;
			this.form = {
				location_type: item.location_type,
				address: item.address,
				map_link: item.map_link,
				location_phone: item.location_phone,
				crowd_status: item.crowd_status,
				contact_staff_name: item.contact_staff_name,
				supplier_id: item.supplier_id,
				country_id: item.country_id,
				state_id: item.id,
			};
		},
		selectedCountry(item) {
			this.fetchCountryStates(item);
			console.log("in fetchcountry", item);
		},
		async loaded() {
			this.countries = [];
			await this.fetchCountries();
		},
		async update(id) {
			try {
				this.form["id"] = id;
				if (this.$refs.validationFormRef[0].validate()) {
					this.isLoading = true;
					const result = await this.$axios.put("update-locations", this.form);
					if (result.status == 200) {
						this.locationItems = this.locationItems.map((item) => {
							if (result.data.id == item.id) {
								return result.data;
							}
							return item;
						});
						this.resetForm();
						this.$toasterNA(
							"green",
							" The Location has successfully updated  "
						);
						this.isLoading = false;
						this.isEdit = false;
					}
					this.isEdit = false;
				}
			} catch (error) {
				this.$toasterNA("red", " Something went wrong");
				this.isLoading = false;
			}
		},
		async fetchCountries() {
			try {
				this.countries = [];
				this.fetchingCountries = true;
				const url = "advertisement/connection/generate_link/country/all";
				const { data } = await this.$axios.get(url);
				console.log(data);
				this.countries = data.items;
			} catch (error) {}
			this.fetchingCountries = false;
		},
		async fetchCountryStates(country_id) {
			this.isFetchingStates = true;
			try {
				const response = await this.$axios.get(
					`states/country-states/${country_id}`
				);
				this.states = response?.data;
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
			this.isFetchingStates = false;
		},
		openDialog(id) {
			this.locationsView = true;
			this.fetchCountries();
			this.supplier_id = id;
			this.getSupplierLocations(id);
			this.resetForm();
		},
		async getSupplierLocations(id) {
			this.locationskeleton = true;
			const result = await this.$axios.get(`get-locations/${id}`);
			console.log(result);
			if(result.data.length > 0){
			this.locationItems = result.data;
			}
			this.locationskeleton = false;
		},
		closeDialog() {
			this.locationsView = false;
			this.locationItems = [];
			this.panel = [0];
			this.locationskeleton = false;
		},
		resetForm() {
			this.form = [
				{
					country_id: "",
					state_id: "",
					location_type: "",
					map_link: "",
					location_phone: "",
					crowd_status: "",
					contact_staff_name: "",
					address: "",
				},
			];
			// this.$refs.form.resetValidation()
		},
	},
};
</script>
<style scoped>
#span_left_side_styles {
	font-size: 20px;
	color: #777;
}
#span_right_side_styles {
	font-size: 18px;
}
.chevron {
	position: absolute;
	right: 30px;
}
.chevronRtl {
	position: absolute;
	left: 30px;
}
.editbtnRtl {
	margin-left: 4rem;
	margin-top: 2rem;
}
.editbtn {
	margin-right: 3rem !important;
	margin-top: 2rem;
}
.savebtnRtl {
	position: absolute;
	left: 5rem;
}
.cancelbtnRtl {
	position: absolute;
	left: 10rem;
}
.savebtn {
	/* margin-right: 3rem !important;
	*/
	position: absolute !important;
	right: 70px !important;
}
.cancelbtn {
	position: absolute !important;
	right: 150px !important;
	z-index: 1 !important;
}
.statenameRtl {
	position: absolute;
	right: 9.5rem;
	margin-top: -1rem;
}
.statename {
	position: absolute;
	left: 9.5rem;
	margin-top: -1rem;
}
.flag-icon.flag-round {
	background-size: cover;
	border-radius: 100%;
	height: 60px;
	width: 60px;
}
</style>

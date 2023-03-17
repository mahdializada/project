<template>
	<v-form lazy-validation ref="form" @submit.prevent="() => {}">
		<div class="w-full h-full d-flex justify-center pt-5">
			<div class="w-full h-full">
				<div class="mb-3">
					<!-- time line -->
					<InputCard
						:title="'brand'"
						:sub_title="'you_can_add_one_or_more_brand'"
					>
						<v-timeline align-top dense flat class="my-5">
							<v-timeline-item 
								v-for="(brand, index) in form.brands.$each.$iter "
								:key="parseInt(index) + 1"
								color="red"
							>
								<template v-slot:icon  >
									<v-avatar size="33" color="primary" class="mouse white--text">
										{{ parseInt(index) + 1 }}
									</v-avatar>
								</template>
								<v-expansion-panels
									accordion
									flat
									class="customExpand"
									:value="expandPanel"
								>
									<v-expansion-panel>
										<v-expansion-panel-header>
											<h3 class="grey--text">{{ $tr("brand_name") }}</h3>

											<template v-slot:actions>
												<v-icon
													color="primary"
													@click="deleteRow(index, brand)"
													style="
														float: right; 
														font-size: 19px;
														margin-top: 13px;
													"
													v-if="form.brands.$model.length > 1"
												>
													mdi-close
												</v-icon>
											</template>
										</v-expansion-panel-header>

										<v-expansion-panel-content>
											<div class="d-flex">
												
												<CTextField
													px="px-1"
													v-model="brand.arabic_name.$model"
													:title="$tr('label_required', $tr('arabic_name'))"
													:rules="
														validateRule(
															brand.arabic_name, // validate objec
															$root, // context
															$tr('arabic_name') // lable for feedback
														)
													"
													:placeholder="$tr('arabic_name')"
													:invalid="brand.arabic_name.$invalid"
												/>
												<CTextField
													px="px-1"
													v-model="brand.name.$model"
													:title="$tr('label_required', $tr('english_name'))"
													:rules="
														validateRule(
															brand.name, // validate objec
															$root, // context
															$tr('english_name') // lable for feedback
														)
													"
													:placeholder="$tr('english_name')"
													:invalid="brand.name.$invalid"
												/>

												
											</div>
											<div class="w-full">
												<CFileInputSingle
												:key="updateKey"
													px="px-0"
													:title="'upload_icon'"
													v-model="brand.logo.$model"
													:rules="
														validateRule(brand.logo, $root, $tr('brand_logo'))
													"
													:invalid="brand.logo.$invalid"
													:placeholder="$tr('upload_your_icon_here')"
													prepend-inner-icon="mdi-cloud-upload"
													accept="image/*"
												/>
											</div>
										</v-expansion-panel-content>
									</v-expansion-panel>
								</v-expansion-panels>
							</v-timeline-item>
						</v-timeline>

						<v-row class="px-7" v-if="!form.$model.id">
							<v-avatar
								size="33"
								color="primary"
								class="mouse"
								@click="addMore()"
							>
								<v-icon dark> mdi-plus </v-icon>
							</v-avatar>
							<p class="primary--text pa-1">
								{{ $tr("add_another_brand") }}
							</p>
						</v-row>
					</InputCard>
					<!-- end -->
					<!-- <InputCard
						:title="'brand'"
						:sub_title="'you_can_add_one_or_more_brand'"
					>
						<v-stepper
							v-for="(brand, index) in form.brands.$each.$iter"
							:key="parseInt(index) + 1"
							v-model="stepper_element"
							vertical
							elevation="0"
						>
							<v-stepper-step
								:step="parseInt(index) + 1"
								@click="stepper_element = parseInt(index) + 1"
							>
								<p
									:style="
										form.brands.$model.length == 1 ? 'margin-top:-8px' : ''
									"
								>
									{{ $tr("brand_name") }}
								</p>

								<v-icon
									color="primary"
									@click="deleteRow(index, brand)"
									style="float: right; font-size: 19px; margin-top: 13px"
									v-if="form.brands.$model.length > 1"
								>
									mdi-close
								</v-icon>
							</v-stepper-step>

							<v-stepper-content :step="parseInt(index) + 1">
								<v-row>
									<v-col cols="12" lg="6" md="6" sm="6">
										<CTextField
											px="px-0"
											v-model="brand.name.$model"
											:title="$tr('label_required', $tr('brand_name'))"
											:rules="
												validateRule(
													brand.name, // validate objec
													$root, // context
													$tr('brand_name') // lable for feedback
												)
											"
											:placeholder="$tr('brand_name')"
											:invalid="brand.name.$invalid"
										/>
									</v-col>
									<v-col cols="12" lg="6" md="6" sm="6">
										<CFileInputSingle
											px="px-0"
											:title="'upload_icon'"
											v-model="brand.logo.$model"
											:rules="
												validateRule(brand.logo, $root, $tr('brand_logo'))
											"
											:invalid="brand.logo.$invalid"
											:placeholder="$tr('upload_your_icon_here')"
											prepend-inner-icon="mdi-cloud-upload"
											accept="image/*"
										/>
									</v-col>
								</v-row>
							</v-stepper-content>
							<span />
						</v-stepper>
						<v-row class="px-5">
							<v-avatar
								size="33"
								color="primary"
								class="mouse"
								@click="addMore()"
							>
								<v-icon dark> mdi-plus </v-icon>
							</v-avatar>
							<p class="primary--text pa-1">
								{{ $tr("add_another_brand") }}
							</p>
						</v-row>
					</InputCard> -->
				</div>
			</div>
		</div>
	</v-form>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CFileInputSingle from "../../new_form_components/Inputs/CFileInputSingle.vue";
import InputCard from "../../new_form_components/components/InputCard.vue";

export default {
	props: {
		form: Object,
	},
	data() {
		return {
			validateRule: GlobalRules.validate,
			userLoading: false,
			logoPath: "",
			stepper_element: 1,
			selected_stepper: 0,
			items: ["Single Choice", "Multiple Choice"],
			selectedItem: "",
			completed: false,
			counter: 1,
			expandPanel: 0,
			updateKey:0,
		};
	},

	methods: {
		async validate() {
			this.form.brands.$touch();
			return !this.form.brands.$invalid;
		},
		async loaded() {
			this.logoPath = "";
			this.updateKey++;
		},
		async addMore() {
			this.$refs.form.validate();
			const valid = await this.validate();
			if (valid) {
				this.stepper_element++;

				this.form.brands.$model.push({
					logo: null,
					name: "",
					arabic_name:"",
				});
				this.selected_stepper = this.form.brands.$model.length - 1;
			} else {
				console.log("invalid");
			}
		},

		deleteRow(index, brand) {
			this.form.brands.$model = this.form.brands.$model.filter(
				(item, i) => i != index
			);
			this.stepper_element--;
		},
		validatePathImg(file) {
			if (file) {
				const fileExtension = file.type;
				const allowedExtensions = ["image/jpeg", "image/jpg", "image/png"];
				if (!allowedExtensions.includes(fileExtension)) {
					// this.$toastr.w(this.$tr("inappropriate_uploaded_file"));
					this.$toasterNA("orange",this.$tr('inappropriate_uploaded_file'));

					return;
				}
				this.form.logo.$model = file;
				this.logoPath = URL.createObjectURL(file);
			}
		},
		clearImg() {
			this.logoPath = "";
		},
	},
	components: { CTextField, CFileInputSingle, InputCard },
};
</script>
<style>
.mouse:hover {
	cursor: pointer;
}
#addButton {
	margin-left: 16px;
	margin-top: -59px;
	width: 27px;
	height: 27px;
}

.form-custom-file-input .v-input__prepend-inner {
	margin-top: 16px;
	margin-right: 8px !important;
}
.v-stepper--vertical .v-stepper__step {
	padding: 14px 15px 19px !important;
}

.form-custom-file-input.has-append .v-input__slot {
	padding-right: 8px !important;
}

.v-application--is-rtl .form-custom-file-input.has-append .v-input__slot {
	padding-right: 24px !important;
	padding-left: 8px;
}

.form-custom-file-input.has-append .v-input__append-inner {
	margin-top: 8px !important;
}

.form-custom-file-input .v-input__append-inner {
	margin-top: 8px;
}

.form-custom-file-input .file-input-btn {
	height: 40px !important;
	font-size: 14px;
	font-weight: 500;
}

.form-custom-file-input .v-input__slot {
	padding-right: 8px !important;
}

.v-application--is-rtl .form-custom-file-input .v-input__slot {
	padding-right: unset !important;
	padding-left: 8px !important;
}
div.v-stepper__step--active {
	background-color: #f6fafe !important;
	padding-top: 15px !important;
	padding-bottom: 17px !important;
	margin-bottom: -40px !important;
	border-radius: 6px;
}
.v-application p {
	margin-bottom: -29px !important;
	color: black;
}

.theme--light.v-stepper
	.v-stepper__step:not(.v-stepper__step--active):not(.v-stepper__step--complete):not(.v-stepper__step--error)
	.v-stepper__step__step {
	/* background: rgba(0, 0, 0, 0.38); */
	background: #1e88e5 !important;
}
.v-application--is-ltr .v-stepper--vertical .v-stepper__content {
	margin: -14px -36px -25px 29px;
}
.v-stepper__step__step {
	align-items: center;
	border-radius: 50%;
	display: inline-flex;
	font-size: 0.75rem;
	justify-content: center;

	width: 31px;
	height: 29px;
	transition: 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.v-stepper__step v-stepper__step--active p {
	color: blue;
}
.v-stepper__step--active .v-stepper__step__step::after {
	content: "";
	position: absolute;
	border: 1px solid #1976d2 !important;
	width: 40px !important;
	height: 38px !important;
	padding: 10px !important;
	margin-left: 0px !important;
	margin-top: 0px !important;
	border-radius: 50% !important;
}
.v-stepper__step--active p {
	color: #1976d2 !important;
	margin-bottom: -29px;
}
.v-stepper--vertical .v-stepper__step {
	padding: 0px 23px 19px;
}
.theme--light.v-stepper .v-stepper__step--active .v-stepper__label {
	text-shadow: 0px 0px 0px black;
	color: #1976d2 !important;
}
</style>

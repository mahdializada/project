<template>
	<v-form
		ref="designRequestForm"
		lazy-validation
		id="designRequestForm"
		@submit.prevent="onSubmit"
	>
		<Edit
			:headers="headers"
			:topIcon="topIcon"
			:tableName="tableName"
			@close="closeDialog"
			@onChangeTo="onChangeTo"
			:iconOrLogo="editData.logo"
			ref="edit_stepper"
			:isIcon="false"
			@onValidate="changeStepper"
			@onSubmit="onSubmit"
			:isLoading="isDataLoaded"
			:isSubmitting="isDataLoaded"
			:is-auto-edit="isAutoEdit"
			:totals="editData.length"
			:current-index="currentIndex"
			@onItemChange="onItemChange"
			@onSave="onSave"
			@OnSaveAndNext="onSaveAndNext"
		>
			<template v-slot:step1>
				<div class="topDiv">
					<HeaderTitle title="general" />
					<div class="px-3 pa-1">
						<div class="w-full">
							<CustomInput
								v-model="$v.designRequest.company_id.$model"
								:rules="validate($v.designRequest.company_id, $root, 'company')"
								:label="$tr('label_required', $tr('company'))"
								item-value="id"
								:items="companies"
								disabled
								item-text="name"
								:placeholder="$tr('choose_item', $tr('company'))"
								type="autocomplete"
							/>
						</div>
						<div class="d-flex flex-column flex-md-row">
							<div class="w-full me-0 me-md-2">
								<p class="mb-1 custom-input-title">
									{{ $tr("product_code") }} *
								</p>
								<v-combobox
									validate-on-blur
									v-model="$v.designRequest.product_code.$model"
									ref="productCodeInputRef"
									:rules="
										validate(
											$v.designRequest.product_code,
											$root,
											'product_code'
										)
									"
									class="custom-input auto-complete"
									dense
									:placeholder="$tr('product_code')"
									:menu-props="{ bottom: true, offsetY: true, class: 'test' }"
									outlined
									disabled
									:items="products"
									item-value="pcode"
									item-text="pcode"
									@change="onProductCodeChanged"
								>
									<template v-slot:[`no-data`]>
										<div class="d-flex pa-1 align-center">
											<div class="me-1">{{ $tr("no_data_available") }}</div>
											<div class="ms-5" style="cursor: pointer">
												<span
													@click="$refs.productCodeInputRef.blur()"
													class="font-weight-bold primary--text"
													>{{ $tr("create") }}
												</span>
												{{ $tr("new_product") }}
											</div>
										</div>
									</template>
								</v-combobox>
							</div>

							<div class="w-full ms-0 ms-md-2">
								<p class="mb-1 custom-input-title">
									{{ $tr("product_name") }} *
								</p>
								<v-combobox
									validate-on-blur
									v-model="$v.designRequest.product_name.$model"
									ref="productNameInputRef"
									:rules="
										validate(
											$v.designRequest.product_name,
											$root,
											'product_name'
										)
									"
									class="custom-input auto-complete"
									dense
									:placeholder="$tr('product_name')"
									:menu-props="{ bottom: true, offsetY: true, class: 'test' }"
									outlined
									disabled
									:items="products"
									item-value="pcode"
									item-text="name"
									@change="onProductNameChanged"
								>
									<template v-slot:[`no-data`]>
										<div class="d-flex pa-1 align-center">
											<div class="me-1">{{ $tr("no_data_available") }}</div>
											<div class="ms-5" style="cursor: pointer">
												<span
													@click="$refs.productNameInputRef.blur()"
													class="font-weight-bold primary--text"
													>{{ $tr("create") }}
												</span>
												{{ $tr("new_product") }}
											</div>
										</div>
									</template>
								</v-combobox>
							</div>
						</div>
						<div class="d-flex flex-column flex-md-row">
							<div class="w-full me-2">
								<CustomInput
									@blur="$v.designRequest.priority.$touch()"
									v-model="$v.designRequest.priority.$model"
									:rules="
										validate($v.designRequest.priority, $root, 'priority')
									"
									:label="$tr('label_required', $tr('priority'))"
									:items="priorities"
									item-value="id"
									item-text="name"
									:placeholder="$tr('choose_item', $tr('priority'))"
									type="autocomplete"
								/>
							</div>
							<div class="w-full ms-0 ms-md-2">
								<CustomInput
									@blur="$v.designRequest.template.$touch()"
									v-model="$v.designRequest.template.$model"
									:rules="
										validate($v.designRequest.template, $root, 'template')
									"
									:label="$tr('label_required', $tr('template'))"
									:items="templates"
									item-value="id"
									item-text="name"
									placeholder="template"
									type="autocomplete"
								/>
							</div>
						</div>
					</div>
				</div>
			</template>

			<template v-slot:step2>
				<div class="topDiv">
					<HeaderTitle title="order" />
					<div class="px-3 pa-1">
						<div class="w-full">
							<CustomInput
								@blur="
									$v.designRequest.design_request_order.order_type.$touch()
								"
								:items="orderTypes"
								:label="$tr('label_required', $tr('order_type'))"
								:placeholder="$tr('choose_item', $tr('order_type'))"
								v-model.trim="
									$v.designRequest.design_request_order.order_type.$model
								"
								:rules="
									validate(
										$v.designRequest.design_request_order.order_type,
										$root,
										'order_type'
									)
								"
								type="autocomplete"
								item-text="name"
								item-value="id"
							/>
						</div>
						<div class="w-full">
							<Editor
								@blur="
									$v.designRequest.design_request_order.sales_note.$touch()
								"
								v-model.trim="
									$v.designRequest.design_request_order.sales_note.$model
								"
								:rules="
									validate(
										$v.designRequest.design_request_order.sales_note,
										$root,
										'sales_note'
									)
								"
								:label="$tr('label_required', $tr('sales_note'))"
								:hidePlaceholder="
									designRequest.design_request_order.sales_note != ''
										? true
										: false
								"
							/>
						</div>
					</div>
				</div>
			</template>
			<template v-slot:step3>
				<div class="topDiv">
					<HeaderTitle title="storyboard" />
					<div class="px-3 pa-1">
						<div class="w-full">
							<Editor
								v-model.trim="
									$v.designRequest.design_request_order.storyboard_note.$model
								"
								@blur="
									$v.designRequest.design_request_order.storyboard_note.$touch()
								"
								:rules="
									validate(
										$v.designRequest.design_request_order.storyboard_note,
										$root,
										'storyboard_note'
									)
								"
								:label="$tr('label_required', $tr('storyboard_note'))"
								:disabled="
									designRequest.status !== 'In Storyboard' ||
									designRequest.status !== 'Storyboard Review'
								"
								:hidePlaceholder="
									designRequest.design_request_order.storyboard_note != ''
										? true
										: false
								"
							/>
						</div>
						<div class="w-full">
							<Editor
								@blur="
									$v.designRequest.design_request_order.design_note.$touch()
								"
								v-model.trim="
									$v.designRequest.design_request_order.design_note.$model
								"
								:rules="
									validate(
										$v.designRequest.design_request_order.design_note,
										$root,
										'disign_note'
									)
								"
								:label="$tr('label_required', $tr('design_note'))"
								:disabled="
									designRequest.status !== 'In Design' ||
									designRequest.status !== 'Design Review'
								"
								:hidePlaceholder="
									designRequest.design_request_order.design_note != ''
										? true
										: false
								"
							/>
						</div>
					</div>
				</div>
			</template>
			<template v-slot:step4>
				<div class="topDiv">
					<HeaderTitle title="link" />
					<div class="px-3 pa-1">
						<div class="w-full">
							<CustomInput
								@blur="
									$v.designRequest.design_request_order.design_link.$touch()
								"
								v-model="
									$v.designRequest.design_request_order.design_link.$model
								"
								:rules="
									validate(
										$v.designRequest.design_request_order.design_link,
										$root,
										'design_link'
									)
								"
								:label="$tr('design_link')"
								:placeholder="$tr('choose_item', $tr('design_link'))"
								type="textfield"
							/>
						</div>
						<div class="w-full">
							<CustomInput
								@blur="
									$v.designRequest.design_request_order.landing_page_link.$touch()
								"
								v-model="
									$v.designRequest.design_request_order.landing_page_link.$model
								"
								:rules="
									validate(
										$v.designRequest.design_request_order.landing_page_link,
										$root,
										'landing_page_link'
									)
								"
								:label="$tr('landing_page_link')"
								:placeholder="$tr('choose_item', $tr('landing_page_link'))"
								type="textfield"
							/>
						</div>
					</div>
				</div>
			</template>
			<template v-slot:step5>
				<DoneMessage
					:title="$tr('item_all_set', $tr('design_request'))"
					:subTitle="$tr('you_can_access_your_item', $tr('design_request'))"
				/>
			</template>
		</Edit>
	</v-form>
</template>

<script>
import Edit from "~/components/design/Edit.vue";
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import FormBtn from "~/components/design/components/FormBtn";
import SelectedItem from "~/components/design/components/SelectedItem";
import CustomInput from "~/components/design/components/CustomInput";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import { mapActions, mapGetters } from "vuex";
import HeaderTitle from "~/components/users/HeaderTitle";
import HandleException from "~/helpers/handle-exception";
import DesignRequestValidtions from "~/validations/design-request-validations";
import GlobalRules from "~/helpers/vuelidate";
import Editor from "~/components/design/Editor.vue";
import Alert from "~/helpers/alert";
import { required, minValue } from "vuelidate/lib/validators";
import Validations from "~/validations/validations";
export default {
	components: {
		FormBtn,
		CustomInput,
		SelectedItem,
		CloseBtn,
		Edit,
		DoneMessage,
		HeaderTitle,
		DesignRequestValidtions,
		Editor,
	},
	props: {
		editData: {
			require: true,
			type: Array,
		},
		editDialog: {
			required: true,
			type: Boolean,
		},
		isAutoEdit: {
			required: false,
			type: Boolean,
		},
	},
	data() {
		const firstIndex = 0;
		return {
			currentIndex: firstIndex,
			isDataLoaded: false,
			validate: GlobalRules.validate,
			headers: DesignRequestValidtions.steppers,
			formKey: 0,
			designRequest: {
				id: this.editData[firstIndex].id,
				product_code: this.editData[firstIndex].product_code,
				product_name: this.editData[firstIndex].product_name,
				template: this.editData[firstIndex].template,
				priority: this.editData[firstIndex].priority,
				company_id: this.editData[firstIndex].company_id,
				status: this.editData[firstIndex].status,
				design_request_order: {
					order_type:
						this.editData[firstIndex].design_request_order?.order_type,
					design_note:
						this.editData[firstIndex].design_request_order?.design_note,
					sales_note:
						this.editData[firstIndex].design_request_order?.sales_note,
					storyboard_note:
						this.editData[firstIndex].design_request_order?.storyboard_note,
					design_link:
						this.editData[firstIndex].design_request_order?.design_link,
					landing_page_link:
						this.editData[firstIndex].design_request_order?.landing_page_link,
				},
			},
			stepperStep: 1,
			current: 1,
			checkbox1: true,
			back: false,
			topIcon: "mdi-account",
			tableName: this.$tr("design_request"),
			topCurrent: 0,
			isLoading: false,
			editorKey: 0,
			newValue: "",
			selectedProduct: null,
			products: DesignRequestValidtions.products,
			isFetchingProducts: false,
			priorities: [
				{ id: "low", name: this.$tr("low") },
				{ id: "medium", name: this.$tr("medium") },
				{ id: "high", name: this.$tr("high") },
			],
			orderTypes: [
				{ id: "scratch", name: this.$tr("scratch") },
				{ id: "sopy", name: this.$tr("copy") },
				{ id: "mixed", name: this.$tr("mixed") },
			],
			templates: [
				{ id: "fredy", name: "Fredy" },
				{ id: "fredo", name: "Oredo" },
			],
			step1: [
				"priority",
				"product_code",
				"product_name",
				"template",
				"company_id",
			],
		};
	},
	validations: {
		designRequest: {
			product_code: Validations.requiredValidation,
			product_name: Validations.requiredValidation,
			template: Validations.requiredValidation,
			priority: Validations.requiredValidation,
			company_id: Validations.requiredValidation,
			design_request_order: {
				order_type: Validations.requiredValidation,
				sales_note: Validations.requiredValidation,
				storyboard_note: {},
				design_note: {},
				design_link: Validations.urlValidation,
				landing_page_link: Validations.urlValidation,
			},
		},
	},
	created() {
		if (this.companies?.length == 0) {
			this.getCompanies();
		}
	},
	computed: {
		...mapGetters({
			companies: "departments/companies",
		}),
	},
	methods: {
		...mapActions({
			getCompanies: "departments/fetchCompanies",
		}),

		onProductCodeChanged(product) {
			if (product) {
				this.designRequest.product_name = product.name
					? product.name
					: this.designRequest.product_name;
				this.designRequest.product_code = product.pcode
					? product.pcode
					: product;
			} else {
				this.designRequest.product_code = null;
				this.designRequest.product_name = null;
			}
		},
		onProductNameChanged(product) {
			if (product) {
				this.designRequest.product_name = product.name ? product.name : product;
				this.designRequest.product_code = product.pcode
					? product.pcode
					: this.designRequest.product_code;
			} else {
				this.designRequest.product_code = null;
				this.designRequest.product_name = null;
			}
		},

		closeDialog() {
			this.$emit("update:editDialog", false);
			this.$emit("update:isAutoEdit", false);
			// this.$root.$emit("closeEdit");
		},
		async onChangeTo(step) {
			switch (step) {
				case 1:
					{
						this.$refs.edit_stepper.setCurrent(1);
					}
					break;
				case 2:
					this.$refs.designRequestForm.validate();
					{
						this.$v.designRequest.$touch();
						if (GlobalRules.isDataValid(this.$v.designRequest, this.step1)) {
							this.$refs.edit_stepper.setCurrent(2);
							this.$refs.designRequestForm.resetValidation();
						}
					}
					break;
				case 3:
					this.$refs.designRequestForm.validate();
					{
						this.$v.designRequest.$touch();
						if (
							GlobalRules.isDataValid(this.$v.designRequest, this.step1) &&
							GlobalRules.isDataValid(
								this.$v.designRequest.design_request_order,
								["order_type", "sales_note"]
							)
						) {
							this.$refs.edit_stepper.setCurrent(3);
							this.$refs.designRequestForm.resetValidation();
						}
					}
					break;
				case 4:
					this.$refs.designRequestForm.validate();
					{
						this.$v.designRequest.$touch();
						if (
							GlobalRules.isDataValid(this.$v.designRequest, this.step1) &&
							GlobalRules.isDataValid(
								this.$v.designRequest.design_request_order,
								["order_type", "sales_note"]
							)
						) {
							this.$refs.edit_stepper.setCurrent(4);
							this.$refs.designRequestForm.resetValidation();
						}
					}
					break;
				default:
					break;
			}
		},
		async changeStepper(step) {
			switch (step) {
				case 1:
					{
						this.$refs.designRequestForm.validate();
						this.$v.designRequest.$touch();
						if (GlobalRules.isDataValid(this.$v.designRequest, this.step1)) {
							this.$refs.edit_stepper.nextForce();
							this.$refs.designRequestForm.resetValidation();
						}
					}
					break;
				case 2:
					{
						this.$refs.designRequestForm.validate();
						this.$v.designRequest.$touch();
						if (
							GlobalRules.isDataValid(
								this.$v.designRequest.design_request_order,
								["order_type", "sales_note"]
							)
						) {
							this.$refs.edit_stepper.nextForce();
							this.$refs.designRequestForm.resetValidation();
						} else {
							// this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
			this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

							return;
						}
					}
					break;
				case 3:
					{
						this.$refs.designRequestForm.validate();
						this.$v.designRequest.$touch();
						if (
							GlobalRules.isDataValid(
								this.$v.designRequest.design_request_order,
								["storyboard_note", "design_note"]
							)
						) {
							this.$refs.edit_stepper.nextForce();
							this.$refs.designRequestForm.resetValidation();
						} else {
							// this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
			this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

							return;
						}
					}
					break;
				case 4:
					{
						this.$refs.designRequestForm.validate();
						this.$v.designRequest.$touch();
						if (
							GlobalRules.isDataValid(
								this.$v.designRequest.design_request_order,
								["design_link", "landing_page_link"]
							)
						) {
							this.$refs.edit_stepper.nextForce();
							this.$refs.designRequestForm.resetValidation();
						} else {
							// this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
			this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

							return;
						}
					}
					break;
				default:
					break;
			}
		},
		// fired on item changed
		async onItemChange(actionType) {
			if (actionType === "next") {
				const index = this.currentIndex + 1;
				if (index < this.editData.length) {
					this.designRequest = {
						id: this.editData[index].id,
						product_code: this.editData[index].product_code,
						product_name: this.editData[index].product_name,
						template: this.editData[index].template,
						priority: this.editData[index].priority,
						company_id: this.editData[index].company_id,
						status: this.editData[index].status,
						design_request_order: {
							order_type: this.editData[index].design_request_order?.order_type,
							design_note:
								this.editData[index].design_request_order?.design_note,
							sales_note: this.editData[index].design_request_order?.sales_note,
							storyboard_note:
								this.editData[index].design_request_order?.storyboard_note,
							design_link:
								this.editData[index].design_request_order?.design_link,
							landing_page_link:
								this.editData[index].design_request_order?.landing_page_link,
						},
					};
					this.currentIndex = index;
				}
			} else if (actionType === "back") {
				const index = this.currentIndex - 1;
				if (index >= 0) {
					this.designRequest = {
						id: this.editData[index].id,
						product_code: this.editData[index].product_code,
						product_name: this.editData[index].product_name,
						template: this.editData[index].template,
						priority: this.editData[index].priority,
						company_id: this.editData[index].company_id,
						status: this.editData[index].status,
						design_request_order: {
							order_type: this.editData[index].design_request_order?.order_type,
							design_note:
								this.editData[index].design_request_order?.design_note,
							sales_note: this.editData[index].design_request_order?.sales_note,
							storyboard_note:
								this.editData[index].design_request_order?.storyboard_note,
							design_link:
								this.editData[index].design_request_order?.design_link,
							landing_page_link:
								this.editData[index].design_request_order?.landing_page_link,
						},
					};
					this.currentIndex = index;
				}
			}
		},

		// fired on save and next button event
		async onSaveAndNext() {
			await this.onSubmit("onSaveAndNext");
		},

		// fired on save button event
		async onSave() {
			await this.onSubmit("can't");
		},

		async onSubmit(canNext = "canNext") {
			this.$refs.designRequestForm.validate();
			this.$v.designRequest.$touch();
			if (!this.$v.designRequest.$invalid) {
				this.isDataLoaded = true;
				await this.updateRecord(this.designRequest, canNext);
				this.isDataLoaded = false;
			} else {
				// this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
			this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

			}
		},

		async updateRecord(data, canNext) {
			try {
				const response = await this.$axios.put(
					`/design_requests/${this.designRequest.id}`,
					data
				);
				if (response?.status === 201 && response?.data?.result) {
					if (canNext === "onSaveAndNext") {
						if (this.currentIndex < this.editData.length) {
							Alert.successAlert(this, this.$tr("successfully_updated"));
							await this.onItemChange("next");
						}
					} else if (canNext === "canNext") {
						this.$refs.edit_stepper.nextForce();
					} else {
						Alert.successAlert(this, this.$tr("successfully_updated"));
					}
					await this.updateData(response?.data?.data);
					await this.$emit("fetchNewData");
				} else {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},
		async updateData(data) {
			this.designRequest = {
				id: data.id,
				product_code: data.product_code,
				product_name: data.product_name,
				template: data.template,
				priority: data.priority,
				company_id: data.company_id,
				status: data.status,
				design_request_order: {
					order_type: data.design_request_order?.order_type,
					design_note: data.design_request_order?.design_note,
					sales_note: data.design_request_order?.sales_note,
					storyboard_note: data.design_request_order?.storyboard_note,
					design_link: data.design_request_order?.design_link,
					landing_page_link: data.design_request_order?.landing_page_link,
				},
			};
		},
	},
};
</script>

<style>
.main-Card {
	background-color: #f9fafd !important;
}

.main-Card .title {
	color: #b6c1d2 !important;
}

.main-Card .v-stepper .v-stepper__header .v-stepper__step__step {
	display: none !important;
}

.main-Card .v-stepper__step.v-stepper__step--active .v-stepper__label .v-btn {
	background-color: var(--v-primary-base);
	color: white;
}
.custom-logo {
	background-repeat: no-repeat;
	background-size: cover;
	background-position: center;
	background: #edf2f9;
}
.custom-logo .v-file-input__text {
	height: 110px;
	opacity: 0;
}
.custom-logo .v-input__slot fieldset {
	border-style: dashed !important;
}
.custom-logo .v-input__slot {
	min-height: 200px !important;
	display: flex;
	justify-content: center;
	align-items: center;
}
.upload-first-p {
	font-size: 18px;
	color: var(--upload-input-first-p);
	letter-spacing: 0.5px;
}
.upload-second-p {
	color: var(--input-border-color);
	line-height: 1.5;
}
.custom-logo .v-input__prepend-inner {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
}
.topDiv {
	position: relative;
}
.topTitle {
	top: 0px;
	position: sticky !important;
	z-index: 2;
	background-color: white;
}
.dublicate-name {
	top: -34% !important;
	padding: 0rem !important;
	position: relative !important;
	color: red;
}

.avatar {
	height: 110px;
	width: 110px;
	background-color: var(--gray-cyan);
	border: 2px solid var(--gray-cyan);
	background-repeat: no-repeat;
	background-size: cover;
	background-position: center;
}

.avatar .v-icon {
	color: var(--color-white);
	font-size: 60px;
}

.custom-file .v-file-input__text {
	height: 110px;
	opacity: 0;
}

.custom-file .v-input__slot fieldset {
	border-style: dashed !important;
}

.custom-file .v-input__slot {
	display: flex;
	justify-content: center;
	align-items: center;
}

.upload-first-p {
	font-size: 18px;
	color: var(--upload-input-first-p);
	letter-spacing: 0.5px;
}

.upload-second-p {
	color: var(--input-border-color);
	line-height: 1.5;
}

.custom-file .v-input__prepend-inner {
	position: absolute;
}
</style>

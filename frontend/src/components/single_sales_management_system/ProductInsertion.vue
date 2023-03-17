<template>
	<v-dialog v-model="model" persistent max-width="1300" width="1300">
		<v-card class="main-Card px-3">
			<v-card-title
				primary-title
				class="pb-1 my-0 title d-flex justify-space-between"
				style="padding: 5px 10px 10px"
			>
				<h2 class="text-h5 font-weight-bold">
					{{ $tr("create_item", $tr("product")) }}
				</h2>
				<CloseBtn @click="toggle" type="button" />
			</v-card-title>
			<v-card-text
				class="position-relative card-pos"
				style="height: 650px; overflow-y: auto"
			>
				<v-form ref="insertForm" lazy-validation>
					<div class="w-full">
						<v-file-input
							class="custom-file"
							outlined
							accept="image/jpeg,image/jpg,image/png"
							prepend-icon=""
							dense
							:rules="validate($v.product.product_img, $root, 'product_logo')"
							@change="validateUserProfile"
							@click:clear="clearUserProfile"
							:success="logoPath !== ''"
						>
							<template slot="prepend-inner">
								<div
									name="prepend-inner"
									style="max-width: 260px; text-align: center"
								>
									<p class="ma-0">
										<svg
											width="30"
											height="30"
											viewBox="100 100 500 140"
											fill="currentColor"
											style="transform: translateY(4px)"
										>
											<g>
												<path
													d="m479.71 198.78h-0.86328c-6.1133-51.496-50.938-91.512-102.99-91.512-42.559 0-81.152 26.367-96.625 65.824-2.7773-0.30469-5.5078-0.44141-8.168-0.44141-33.949 0-63.816 23.754-71.656 56.746-32.059 2.8945-56.934 29.938-56.934 62.348 0 34.512 28.094 62.625 62.625 62.625h119.63c4.293 0 7.7695-3.4766 7.7695-7.793 0-4.3164-3.4766-7.793-7.7695-7.793l-119.6-0.003906c-25.922 0-47.039-21.094-47.039-47.039 0-25.898 21.023-47.016 46.828-47.016l1.0039 0.023438c3.8281 0 7.1406-2.8477 7.7227-6.6484 4.0586-28.426 28.746-49.867 57.445-49.867 3.9883 0 7.957 0.42188 11.945 1.2383l1.4023 0.13672c3.4062 0 6.4648-2.2617 7.3984-5.4375 11.738-36.656 45.5-61.32 84.023-61.32 47.414 0 86.078 37.191 88.059 84.699 0.14063 2.0781 1.0977 4.0117 2.7305 5.4375 1.5391 1.3086 3.2422 1.6562 5.9258 1.8203 2.7305-0.30469 5.0859-0.46484 7.1641-0.46484 34.301 0 62.207 27.906 62.207 62.207s-27.906 62.207-62.207 62.207h-104.42c-4.293 0-7.7695 3.4766-7.7695 7.793s3.5 7.793 7.7695 7.793h104.42c42.887 0 77.793-34.883 77.793-77.793s-34.93-77.77-77.816-77.77z"
												/>
												<path
													d="m413.02 318.92c0.023437 0 0.023437 0 0 0 2.125 0 4.1289-0.81641 5.5312-2.2383 2.9883-3.0352 2.9883-7.957 0-11.012l-63.047-63.047c-2.9648-2.9414-8.0273-2.918-11.012 0.070312l-63.023 62.977c-1.4688 1.4688-2.2617 3.4297-2.2617 5.5078 0 2.1016 0.81641 4.0586 2.2383 5.4609 1.3984 1.4453 3.4062 2.2852 5.4844 2.3086h0.046875c2.1016 0 4.1055-0.81641 5.5312-2.2617l49.699-49.676v177.92c0 4.3164 3.5 7.793 7.7695 7.793 4.3164 0 7.8164-3.4766 7.8164-7.793v-177.92l49.652 49.652c1.4453 1.4375 3.4492 2.2578 5.5742 2.2578z"
												/>
											</g>
										</svg>
										{{ $tr("select_product_logo") }}
									</p>
									<div v-if="logoPath" class="ma-1">
										<p class="mb-0">
											{{ $tr("name") }}:
											{{ $v.product.product_img.$model.name }}
										</p>
										<p class="mb-0" style="margin-top: 5px">
											{{ $tr("size") }}:
											{{
												($v.product.product_img.$model.size / 1024).toFixed(2)
											}}
											KB
										</p>
									</div>
								</div>
							</template>
						</v-file-input>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('product_name'))"
								placeholder="product_name"
								v-model="$v.product.name.$model"
								:rules="validate($v.product.name, $root, 'product_name')"
								type="textfield"
								class="me-md-2"
							/>
						</div>
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('product_code'))"
								placeholder="product_code"
								v-model="$v.product.pcode.$model"
								:rules="validate($v.product.pcode, $root, 'product_code')"
								type="textfield"
								class="ms-md-2"
							/>
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('product_sku'))"
								placeholder="product_sku"
								v-model="$v.product.parent_sku.$model"
								:rules="validate($v.product.parent_sku, $root, 'product_sku')"
								type="textfield"
								class="me-md-2"
							/>
						</div>
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('number_of_sales'))"
								placeholder="number_of_sales"
								v-model="$v.product.number_of_sales.$model"
								:rules="
									validate($v.product.number_of_sales, $root, 'number_of_sales')
								"
								type="textfield"
								class="ms-md-2"
							/>
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('product_vat_tax'))"
								placeholder="product_vat_tax"
								v-model="$v.product.VAT_tax.$model"
								:rules="validate($v.product.VAT_tax, $root, 'product_vat_tax')"
								type="textfield"
								class="me-md-2"
							/>
						</div>
						<div class="w-full">
							<CustomInput
								:label="$tr('label_required', $tr('product_unit'))"
								placeholder="product_unit"
								v-model="$v.product.unit.$model"
								:rules="validate($v.product.unit, $root, 'product_unit')"
								type="textfield"
								class="ms-md-2"
							/>
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								@blur="$v.product.is_published.$touch()"
								v-model="$v.product.is_published.$model"
								:rules="
									validate($v.product.is_published, $root, 'is_published')
								"
								:label="$tr('is_published')"
								:items="publish_items"
								item-value="id"
								item-text="text"
								:placeholder="$tr('is_published')"
								type="select"
								class="me-md-2"
							/>
						</div>
						<div class="w-full">
							<CustomInput
								@blur="$v.product.category_id.$touch()"
								v-model="$v.product.category_id.$model"
								:rules="validate($v.product.category_id, $root, 'category')"
								:label="$tr('label_required', $tr('category'))"
								:items="categories"
								item-value="id"
								item-text="name"
								:placeholder="$tr('choose_item', $tr('category'))"
								type="autocomplete"
								class="ms-md-2"
							/>
						</div>
					</div>
					<div class="d-flex flex-column flex-md-row">
						<div class="w-full">
							<CustomInput
								@blur="$v.product.supplier_id.$touch()"
								v-model="$v.product.supplier_id.$model"
								:rules="validate($v.product.supplier_id, $root, 'supplier')"
								:label="$tr('label_required', $tr('supplier'))"
								:items="suppliers"
								item-value="id"
								item-text="code"
								:placeholder="$tr('choose_item', $tr('supplier'))"
								type="autocomplete"
								class="me-md-2"
							/>
						</div>
						<div class="w-full">
							<CustomInput
								@blur="$v.product.brand_id.$touch()"
								v-model="$v.product.brand_id.$model"
								:rules="validate($v.product.brand_id, $root, 'brand')"
								:label="$tr('label_required', $tr('brand'))"
								:items="brands"
								item-value="id"
								item-text="name"
								:placeholder="$tr('choose_item', $tr('brand'))"
								type="autocomplete"
								class="ms-md-2"
							/>
						</div>
					</div>
					<div class="w-full">
						<CustomInput
							:label="$tr('description')"
							placeholder="description"
							v-model.trim="$v.product.description.$model"
							:rules="validate($v.product.description, $root, 'description')"
							type="textarea"
						/>
					</div>
				</v-form>
			</v-card-text>
			<v-card-actions class="pa-3">
				<v-btn
					@click="resetForm"
					color="success"
					class="stepper-btn mr-2"
					type="button"
				>
					{{ $tr("reset_form") }}
				</v-btn>
				<v-spacer></v-spacer>
				<v-btn
					color="primary"
					class="stepper-btn px-3"
					style="min-width: 160px"
					@click="validateForm"
					:loading="isSubmitting"
					:disable="isSubmitting"
				>
					<template v-slot:loader>
						<span>
							<span>
								{{ $tr("submitting") + "..." }}
							</span>
							<v-progress-circular
								class="ma-0"
								indeterminate
								color="white"
								size="25"
								:width="2"
							/>
						</span>
					</template>
					{{ $tr("submit") }}
				</v-btn>
				<v-btn
					@click="toggle"
					color="error"
					class="stepper-btn"
					:type="'button'"
				>
					{{ $tr("cancel") }}
				</v-btn>
			</v-card-actions>
		</v-card>
	</v-dialog>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Validations from "../../validations/validations";
import CustomInput from "../design/components/CustomInput.vue";
import GlobalRules from "~/helpers/vuelidate";
import Editor from "../design/Editor.vue";
import Helpers from "../../helpers/helpers";
import HandleException from "../../helpers/handle-exception";

export default {
	data() {
		return {
			model: false,
			validate: GlobalRules.validate,
			logoPath: "",
			isSubmitting: false,
			categories: [],
			suppliers: [],
			brands: [],
			publish_items: [
				{ id: 0, text: this.$tr("no") },
				{ id: 1, text: this.$tr("yes") },
			],
			product: {
				is_published: 0,
				parent_sku: null,
				pcode: null,
				name: null,
				product_img: null,
				description: null,
				VAT_tax: null,
				unit: null,
				number_of_sales: null,
				category_id: null,
				supplier_id: null,
				brand_id: null,
			},
		};
	},

	validations: {
		product: {
			parent_sku: Validations.requiredValidation,
			pcode: Validations.requiredValidation,
			name: Validations.requiredValidation,
			product_img: Validations.requiredValidation,
			description: Validations.requiredValidation,
			VAT_tax: Validations.requiredValidation,
			unit: Validations.requiredValidation,
			number_of_sales: Validations.requiredValidation,
			is_published: Validations.requiredValidation,
			category_id: Validations.requiredValidation,
			supplier_id: Validations.requiredValidation,
			brand_id: Validations.requiredValidation,
		},
	},
	methods: {
		toggle() {
			this.model = !this.model;
			if (this.model) {
				this.fetchCategories();
				this.fetchSuppliers();
				this.fetchBrands();
			}
		},

		async fetchCategories() {
			try {
				const url = "single-sales/categories/get?filter_category=true";
				const { data } = await this.$axios.get(url);
				this.categories = data;
			} catch (error) {}
		},
		async fetchSuppliers() {
			try {
				const url = "single-sales/products-ssp/get?filter_supplier=true";
				const { data } = await this.$axios.get(url);
				this.suppliers = data;
			} catch (error) {}
		},
		async fetchBrands() {
			try {
				const url = "single-sales/brand-ssp/get?filter_brand=true";
				const { data } = await this.$axios.get(url);
				this.brands = data;
			} catch (error) {}
		},

		resetForm() {
			this.$refs.insertForm.reset();
			this.logoPath = "";
		},

		async validateForm() {
			this.$refs.insertForm.validate();
			this.$v.product.$touch();
			if (!this.$v.product.$invalid) {
				this.isSubmitting = true;
				const product = Helpers.getFormData(this.product);
				await this.insertRecord(product);
				this.isSubmitting = false;
			}
		},

		async insertRecord(product) {
			try {
				const url = "single-sales/products-ssp";
				const { data } = await this.$axios.post(url, product);
				if (data.result) {
					this.$toasterNA("green", this.$tr('successfully_inserted'));
					const insertedProduct = data.product;
					this.$emit("pushRecord", insertedProduct);
					this.resetForm();
					this.toggle();
				} else {
					// this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

				}
			} catch (error) {
				HandleException.handleApiException(this, error);
			}
		},

		clearUserProfile() {
			this.$v.product.product_img.$model = null;
			this.logoPath = "";
		},

		validateUserProfile(file) {
			if (file) {
				const fileExtension = file.type;
				const allowedExtensions = ["image/jpeg", "image/jpg", "image/png"];
				if (!allowedExtensions.includes(fileExtension)) {
					// this.$toastr.w(this.$tr("inappropriate_uploaded_file"));
					this.$toasterNA("orange",this.$tr('inappropriate_uploaded_file'));

					return;
				}
				this.$v.product.product_img.$model = file;
				this.logoPath = URL.createObjectURL(file);
			}
		},
	},
	components: { CloseBtn, CustomInput, Editor },
};
</script>

<style></style>

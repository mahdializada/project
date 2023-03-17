<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <v-card class="main-Card px-3">
      <v-card-title
        primary-title
        class="pb-1 my-0 title d-flex justify-space-between"
        style="padding: 5px 10px 10px"
      >
        <h2 class="text-h5 font-weight-bold">
          {{ $tr("create_item", $tr("Ispp")) }}
        </h2>
        <CloseBtn @click="toggle" type="button" />
      </v-card-title>
      <v-card-text
        class="position-relative card-pos"
        style="height: 650px; overflow-y: auto"
      >
        <v-form ref="insertForm" lazy-validation>
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.category_id.$touch()"
                v-model="$v.ispp.category_id.$model"
                :rules="validate($v.ispp.category_id, $root, 'category')"
                :label="$tr('label_required', $tr('category'))"
                :items="categories"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('category'))"
                type="autocomplete"
                class="me-md-2"
                @change="fetchProducts($v.ispp.category_id.$model)"
              />
            </div>
            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.product_id.$touch()"
                v-model="$v.ispp.product_id.$model"
                :rules="validate($v.ispp.product_id, $root, 'product')"
                :label="$tr('label_required', $tr('product'))"
                :items="products"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('product'))"
                type="autocomplete"
                class="ms-md-2"
                :loading="is_fetching_product"
                @change="fetchProductStudy($v.ispp.product_id.$model)"
              />
            </div>
          </div>

          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.product_study_id.$touch()"
                v-model="$v.ispp.product_study_id.$model"
                :rules="
                  validate($v.ispp.product_study_id, $root, 'product_study')
                "
                :label="$tr('label_required', $tr('product_study'))"
                :items="product_studies"
                item-value="id"
                item-text="code"
                :placeholder="$tr('choose_item', $tr('product_study'))"
                type="autocomplete"
                class="me-md-2"
                :loading="is_fetching_studies"
              />
            </div>
            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.sales_modal.$touch()"
                v-model="$v.ispp.sales_modal.$model"
                :rules="validate($v.ispp.sales_modal, $root, 'sales_modal')"
                :label="$tr('label_required', $tr('sales_modal'))"
                :items="sales_modals"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('sales_modal'))"
                type="autocomplete"
                class="ms-md-2"
              />
            </div>
          </div>

          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.landing_page_style.$touch()"
                v-model="$v.ispp.landing_page_style.$model"
                :rules="
                  validate(
                    $v.ispp.landing_page_style,
                    $root,
                    'landing_page_style'
                  )
                "
                :label="$tr('label_required', $tr('landing_page_style'))"
                :items="landing_styles"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('landing_page_style'))"
                type="autocomplete"
                class="me-md-2"
              />
            </div>

            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.creation_type.$touch()"
                v-model="$v.ispp.creation_type.$model"
                :rules="validate($v.ispp.creation_type, $root, 'creation_type')"
                :label="$tr('label_required', $tr('creation_type'))"
                :items="creation_type"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('creation_type'))"
                type="autocomplete"
                class="ms-md-2"
              />
            </div>
          </div>
          <!--          target sale countries start              -->
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <v-autocomplete
                  :items="countries"
                  @blur="$v.ispp.attribute.$touch()"
                  v-model="$v.ispp.attribute.$model"
                  :rules="validate($v.ispp.attribute, $root, 'attribute')"
                  :placeholder="$tr('choose_item', $tr('target sale countries'))"
                  :label="$tr('target sale countries')"
                  :no-data-text="$tr('no_data_available')"
                  v-bind="$attrs"
                  v-on="$listeners"
                  item-value="id"
                  item-text="name"
                  multiple
                  chips
                  class="mt-2 custom-input"
                  outlined
                  dense
              >
                <template v-slot:selection="data">
                  <v-chip
                      v-bind="data.attrs"
                      :input-value="data.selected"
                      close
                      @click="data.select"
                      @click:close="remove(data.item)"
                      style="margin: 2px 0px !important"
                  >
                    <v-avatar
                        left
                        color="primary"
                        style="color: white !important"
                    >
                      {{ $tr(data.item.name).charAt(0) }}
                    </v-avatar>
                    {{ $tr(data.item.name) }}
                  </v-chip>
                </template>
                <template v-slot:[`item`]="{ item }">
                  <v-list-item-content>
                    <div class="d-flex align-center">
                      <div>
                        <v-list-item-title
                            v-html="`${$tr(item.name)}`"
                        ></v-list-item-title>
                      </div>
                    </div>
                  </v-list-item-content>
                </template>
              </v-autocomplete>
            </div>
          </div>
          <!--          target sale countries end                -->
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.company_id.$touch()"
                v-model="$v.ispp.company_id.$model"
                :rules="validate($v.ispp.company_id, $root, 'company_id')"
                :label="$tr('label_required', $tr('company'))"
                :items="companies"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('company'))"
                type="autocomplete"
                class="me-md-2"
              />
            </div>

            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.display_language_id.$touch()"
                v-model="$v.ispp.display_language_id.$model"
                :rules="
                  validate(
                    $v.ispp.display_language_id,
                    $root,
                    'display_language_id'
                  )
                "
                :label="$tr('label_required', $tr('display_language'))"
                :items="languages"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('display_language'))"
                type="autocomplete"
                class="ms-md-2"
              />
            </div>
          </div>

          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                :label="$tr('label_required', $tr('head_selling_price'))"
                placeholder="head_selling_price"
                v-model="$v.ispp.head_selling_price.$model"
                :rules="
                  validate(
                    $v.ispp.head_selling_price,
                    $root,
                    'head_selling_price'
                  )
                "
                type="number"
                class="me-md-2"
              />
            </div>


            <div class="w-full">
              <CustomInput
                @blur="$v.ispp.sale_currency.$touch()"
                v-model="$v.ispp.sale_currency.$model"
                :rules="validate($v.ispp.sale_currency, $root, 'sale_currency')"
                :label="$tr('sale_currency')"
                :items="currencies"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('sale_currency'))"
                type="autocomplete"
                class="ms-md-2"
              />
            </div>
          </div>

          <div class="w-full">
            <CustomInput
              :label="$tr('selling_goals')"
              placeholder="selling_goals"
              v-model.trim="$v.ispp.selling_goals.$model"
              :rules="validate($v.ispp.selling_goals, $root, 'selling_goals')"
              type="textarea"
            />
          </div>
          <div class="w-full">
            <CustomInput
              :label="$tr('landing_page_note')"
              placeholder="landing_page_note"
              v-model.trim="$v.ispp.landing_page_note.$model"
              :rules="
                validate($v.ispp.landing_page_note, $root, 'landing_page_note')
              "
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
import HandleException from "../../helpers/handle-exception";

export default {
  data() {
    return {
      model: false,
      validate: GlobalRules.validate,
      logoPath: "",
      isSubmitting: false,
      categories: [],
      products: [],
      product_studies: [],
      languages: [],
      countries: [],
      companies: [],
      creation_type: [
        { id: "online", name: this.$tr("online") },
        { id: "offline", name: this.$tr("offline") },
      ],
      sales_modals: [
        { id: "model1", name: this.$tr("model1") },
        { id: "model2", name: this.$tr("model2") },
      ],
      landing_styles: [
        { id: "simple", name: this.$tr("simple") },
        { id: "good", name: this.$tr("good") },
        { id: "nice", name: this.$tr("nice") },
      ],
      currencies: [
        { id: "dollor", name: this.$tr("dollor") },
        { id: "afg", name: this.$tr("afghani") },
      ],
      is_fetching_product: false,
      is_fetching_studies: false,
      ispp: {
        category_id: null,
        product_id: null,
        creation_type: null,
        selling_goals: null,
        sale_currency: null,
        landing_page_style: null,
        company_id: null,
        display_language_id: null,
        landing_page_note: null,
        head_selling_price: null,
        product_study_id: null,
        sales_modal: null,
        attribute: [],
      },

    };
  },

  validations: {
    ispp: {
      category_id: Validations.requiredValidation,
      product_id: Validations.requiredValidation,
      creation_type: Validations.requiredValidation,
      selling_goals: Validations.requiredValidation,
      head_selling_price: Validations.requiredValidation,
      landing_page_style: Validations.requiredValidation,
      company_id: Validations.requiredValidation,
      display_language_id: Validations.requiredValidation,
      sale_currency: Validations.requiredValidation,
      landing_page_note: Validations.requiredValidation,
      product_study_id: Validations.requiredValidation,
      sales_modal: Validations.requiredValidation,
      attribute: Validations.requiredValidation,
    },

  },
  methods: {
    remove(item) {
      this.ispp.attribute = this.ispp.attribute.filter((a) =>{
        return  a !== item.id
      });
    },
    toggle() {
      this.model = !this.model;
      if (this.model) {
        this.fetchCategories();
        this.fetchLanguage();
        this.fetchCompanies();
        this.fetchCountries();
      }
    },

    async fetchCategories() {
      try {
        const url = "single-sales/categories/get?filter_category=true";
        const { data } = await this.$axios.get(url);
        this.categories = data;
      } catch (error) {}
    },
    async fetchCountries() {
      try {
        const url = "common/countries";
        const { data } = await this.$axios.get(url);
        this.countries = data.countries;
      } catch (error) {}
    },
    async fetchLanguage() {
      try {
        const url = "common/languages";
        const { data } = await this.$axios.get(url);
        this.languages = data.languages;
      } catch (error) {}
    },
    async fetchCompanies() {
      try {
        const url = "auth-companies";
        const { data } = await this.$axios.get(url);
        this.companies = data;
      } catch (error) {}
    },

    async fetchProducts(id) {
      try {
        this.is_fetching_product = true;
        const url = "single-sales/products-ssp/get?category_id=" + id;
        const { data } = await this.$axios.get(url);
        this.is_fetching_product = false;

        this.products = data;
      } catch (error) {
        console.log("error", error);
        this.is_fetching_product = false;
      }
    },

    async fetchProductStudy(id) {
      try {
        this.is_fetching_studies = true;
        const url = "single-sales/product-study/get?product_id=" + id;
        const { data } = await this.$axios.get(url);
        this.is_fetching_studies = false;
        console.log("data", data);

        this.product_studies = data;
      } catch (error) {
        console.log("error", error);
        this.is_fetching_studies = false;
      }
    },

    resetForm() {
      this.$refs.insertForm.reset();
    },

    async validateForm() {
      this.$refs.insertForm.validate();
      this.$v.ispp.$touch();
      if (!this.$v.ispp.$invalid) {
        this.isSubmitting = true;
        await this.insertRecord(this.ispp);
        this.isSubmitting = false;
      }
    },

    async insertRecord(ispp) {
      try {
        const url = "single-sales/ispp";
        const { data } = await this.$axios.post(url, ispp);
        if (data.result) {
          this.$toasterNA("green", this.$tr('successfully_inserted'));
          const insertispp = data?.ispp; 
          this.$emit("pushRecord", data?.ispp);
 
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
  },
  components: { CloseBtn, CustomInput, Editor },
};
</script>

<style></style>

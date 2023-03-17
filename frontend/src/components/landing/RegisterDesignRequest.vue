<template>
  <div>
    <v-dialog v-model="model" width="1300" persistent>
      <Dialog @closeDialog="closeDialog">
        <v-form lazy-validation ref="designRequestForm">
          <CustomStepper
            :headers="steppers"
            @close="close"
            :canNext="false"
            @validate="validateStepper"
            :is-submitting="isSubmitting"
            :loading="isLoading"
            ref="designRequestRef"
            @submit="insertSubmitForm"
            @changeToValidate="changeToValidate"
          >
            <template v-slot:step1>
              <CustomInput
                @blur="$v.designRequest.company_id.$touch()"
                v-model="$v.designRequest.company_id.$model"
                :rules="validate($v.designRequest.company_id, $root, 'company')"
                :label="$tr('label_required', $tr('company'))"
                :items="companies"
                item-value="id"
                logoName=""
                has-logo
                item-text="name"
                :placeholder="$tr('choose_item', $tr('company'))"
                type="autocomplete"
              />
              <div class="d-flex flex-column flex-md-row">
                <div class="w-full me-0 me-md-2">
                  <p class="mb-1 custom-input-title">
                    {{ $tr("product_code") }} *
                  </p>
                  <v-combobox
                    validate-on-blur
                    @blur="$v.designRequest.product_code.$touch()"
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
                    :loading="isFetchingProducts"
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
                    @blur="$v.designRequest.product_name.$touch()"
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
                    :loading="isFetchingProducts"
                    :items="products"
                    item-value="name"
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
                <div class="w-full me-0 me-md-2">
                  <CustomInput
                    @blur="$v.designRequest.priority.$touch()"
                    :items="priorities"
                    :label="$tr('label_required', $tr('priority'))"
                    :placeholder="$tr('choose_item', $tr('priority'))"
                    v-model.trim="$v.designRequest.priority.$model"
                    :rules="
                      validate($v.designRequest.priority, $root, 'priority')
                    "
                    type="autocomplete"
                    item-text="name"
                    item-value="id"
                  />
                </div>
                <div class="w-full ms-0 ms-md-2">
                  <p class="mb-1 custom-input-title">{{ $tr("template") }} *</p>
                  <v-combobox
                    validate-on-blur
                    @blur="$v.designRequest.template.$touch()"
                    v-model="$v.designRequest.template.$model"
                    :rules="
                      validate($v.designRequest.template, $root, 'template')
                    "
                    class="custom-input auto-complete"
                    ref="templateRefs"
                    dense
                    :placeholder="$tr('template')"
                    :menu-props="{ bottom: true, offsetY: true, class: 'test' }"
                    outlined
                    :loading="isFetchingProducts"
                    :items="templates"
                    item-value="id"
                    item-text="name"
                  >
                    <template v-slot:[`no-data`]>
                      <div class="d-flex pa-1 align-center">
                        <div class="me-1">{{ $tr("no_data_available") }}</div>
                        <div class="ms-5" style="cursor: pointer">
                          <span
                            @click="$refs.templateRefs.blur()"
                            class="font-weight-bold primary--text"
                            >{{ $tr("create") }}
                          </span>
                          {{ $tr("new_template") }}
                        </div>
                      </div>
                    </template>
                  </v-combobox>
                </div>
              </div>
            </template>
            <template v-slot:step2>
              <CustomInput
                @blur="$v.designRequest.order_type.$touch()"
                :items="orderTypes"
                :label="$tr('label_required', $tr('order_type'))"
                :placeholder="$tr('choose_item', $tr('order_type'))"
                v-model.trim="$v.designRequest.order_type.$model"
                :rules="
                  validate($v.designRequest.order_type, $root, 'order_type')
                "
                type="autocomplete"
                item-text="name"
                item-value="id"
              />

              <Editor
                :key="salesNoteKey"
                v-model.trim="$v.designRequest.sales_note.$model"
                :rules="
                  validate($v.designRequest.sales_note, $root, 'sales_note')
                "
                :label="$tr('sales_note')"
              />
            </template>

            <template v-slot:step3>
              <LabelCategoryStepper />
            </template>
            <template v-slot:step4>
              <DoneMessage
                :title="$tr('item_all_set', $tr('design_request'))"
                :subTitle="
                  $tr('you_can_access_your_item', $tr('design_request'))
                "
              />
            </template>
          </CustomStepper>
        </v-form>
      </Dialog>
    </v-dialog>
  </div>
</template>

<script>
import Editor from "../design/Editor.vue";
import StepTwo from "./StepTwo.vue";
import HandleException from "~/helpers/handle-exception";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import CustomStepper from "~/components/design/FormStepper/CustomStepper";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import CustomInput from "~/components/design/components/CustomInput";
import DesignRequestValidtions from "~/validations/design-request-validations";
import GlobleRules from "~/helpers/vuelidate";
import LabelCategoryStepper from "./LabelCategoryStepper.vue";

export default {
  components: {
    Dialog,
    CustomStepper,
    DoneMessage,
    CustomInput,
    StepTwo,
    Editor,
    LabelCategoryStepper,
  },
  name: "RegisterDesignRequest",
  props: {
    registerDialog: {
      type: Boolean,
      required: true,
    },
  },

  // fetch Products
  async fetch() {
    this.fetchProducts();
  },

  data() {
    return {
      model: true,
      isLoading: false,
      isSubmitting: false,
      shouldGoesBack: false,
      salesNoteKey: 0,
      validate: GlobleRules.validate,
      steppers: DesignRequestValidtions.steppers,
      designRequest: JSON.parse(JSON.stringify(DesignRequestValidtions.schema)),
      // necessary items
      products: [],
      companies: [],
      isFetchingProducts: false,
      orderTypes: [
        { id: "scratch", name: this.$tr("scratch") },
        { id: "copy", name: this.$tr("copy") },
        { id: "mixed", name: this.$tr("mixed") },
      ],
      priorities: [
        { id: "low", name: this.$tr("low") },
        { id: "medium", name: this.$tr("medium") },
        { id: "high", name: this.$tr("high") },
      ],
      templates: [],
    };
  },
  created() {
    if (this.companies.length == 0) {
      this.fetchCompanies();
    }
  },

  methods: {
    async fetchCompanies() {
      try {
        const response = await this.$axios.get(
          "auth-companies?has-landing=true"
        );
        this.companies = response.data;
      } catch (error) {}
    },

    async fetchProducts() {
      try {
        this.isFetchingProducts = true;
        const currentComUrl = "design-request?product-code=true";
        const reponse = await this.$axios.get(currentComUrl);
        let products = reponse.data.products;
        this.templates = reponse.data.templates;
        this.products = products;
      } catch (error) {}
      this.isFetchingProducts = false;
    },

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

    changeToValidate(changeTo) {
      let isValid = false;
      this.$refs.designRequestForm.validate();
      this.$v.designRequest.$touch();
      if (changeTo === 2) {
        isValid = GlobleRules.isDataValid(this.$v.designRequest, [
          "template",
          "priority",
          "product_code",
          "product_name",
          "company_id",
        ]);
      }

      if (changeTo === 3) {
        isValid = GlobleRules.isDataValid(this.$v.designRequest, [
          "order_type",
          "sales_note",
        ]);
      }
      if (changeTo === 4) {
        isValid = true;
      }

      if (isValid) {
        this.$v.$reset();
        this.$refs.designRequestRef.changeTo(changeTo);
        this.salesNoteKey++;
      }
    },

    // validate form and data
    async validateStepper(currentStep) {
      this.$refs.designRequestForm.validate();
      this.$v.designRequest.$touch();
      if (currentStep === 1) {
        if (
          GlobleRules.isDataValid(this.$v.designRequest, [
            "template",
            "product_code",
            "product_name",
            "company_id",
          ])
        ) {
          this.$v.$reset();
          this.$refs.designRequestRef?.forceNext();
          this.salesNoteKey++;
        }
      }
      if (currentStep === 2) {
        if (
          GlobleRules.isDataValid(this.$v.designRequest, [
            "order_type",
            "priority",
            "sales_note",
          ])
        ) {
          this.$v.$reset();
          this.$refs.designRequestRef?.forceNext();
          this.salesNoteKey++;
        }
      }
      if (currentStep === 3) {
        this.$v.$reset();
        this.$refs.designRequestRef?.forceNext();
        this.salesNoteKey++;
      }
      // if (currentStep === 4) {
      //   this.$v.$reset();
      //   this.$refs.designRequestRef?.forceNext();
      //   this.salesNoteKey++;
      // }
    },

    // submit registration form
    async insertSubmitForm() {
      this.$refs.designRequestForm.validate();
      this.$v.designRequest.$touch();
      if (!this.$v.designRequest.$invalid) {
        this.isSubmitting = true;
        await this.insertRecord(this.designRequest);
        this.isSubmitting = false;
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
			this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },
    // insert record into database
    async insertRecord(data) {
      try {
        const response = await this.$axios.post("design-request", data);
        if (response?.status === 201 && response?.data?.result) {
          this.salesNoteKey++;
          // this.$parent.fetchDataAccordingtoStatus();
          this.fetchProducts();
          this.$refs.designRequestRef.forceNext();
          this.clearAllPrevData();
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
			this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        if (error?.response?.status === 502) {
          // this.$toasterNA("red", this.$tr('sending_email_failed'));;
			this.$toasterNA("red", this.$tr('sending_email_failed'));

          return;
        }
        HandleException.handleApiException(this, error);
      }
    },

    // remove all previous data
    clearAllPrevData() {
      this.designRequest = JSON.parse(
        JSON.stringify(DesignRequestValidtions.schema)
      );
      this.$v.designRequest.$reset();
      this.$refs.designRequestForm.reset();
      this.$v.$reset();
      this.shouldGoesBack = true;
    },

    // close dialog
    close() {
      if (this.shouldGoesBack) {
        this.shouldGoesBack = false;
        this.$refs.designRequestRef.setCurrent(1);
      }
    },
    // close dialog
    closeDialog() {
      this.$emit("update:registerDialog", false);
    },
  },
  validations: {
    designRequest: DesignRequestValidtions.validations,
  },
};
</script>

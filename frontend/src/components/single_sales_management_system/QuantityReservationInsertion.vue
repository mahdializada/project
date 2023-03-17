<template>
  <v-dialog v-model="model" persistent max-width="1000" width="1000">
    <v-card class="main-Card px-3">
      <v-card-title
        primary-title
        class="pb-1 my-0 title d-flex justify-space-between"
        style="padding: 5px 10px 10px"
      >
        <h2 class="text-h5 font-weight-bold">
          {{ $tr("create_item", $tr("quantity_reservation")) }}
        </h2>
        <CloseBtn @click="toggle" type="button" />
      </v-card-title>
      <v-card-text
        class="position-relative card-pos"
        style="height: 250px; overflow-y: auto"
      >
        <v-form ref="insertForm" lazy-validation>
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                @blur="$v.quantityReservation.product_id.$touch()"
                v-model="$v.quantityReservation.product_id.$model"
                :rules="
                  validate($v.quantityReservation.product_id, $root, 'product')
                "
                :label="$tr('label_required', $tr('product'))"
                :items="products"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('product'))"
                type="autocomplete"
                class="me-md-2"
              />
            </div>
            <div class="w-full">
              <CustomInput
                :label="$tr('label_required', $tr('quantity'))"
                placeholder="quantity"
                v-model="$v.quantityReservation.quantity.$model"
                :rules="
                  validate($v.quantityReservation.quantity, $root, 'quantity')
                "
                type="number"
                class="ms-md-2"
              />
            </div>
          </div>

          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                @blur="$v.quantityReservation.required_shipping_method.$touch()"
                v-model="$v.quantityReservation.required_shipping_method.$model"
                :rules="
                  validate(
                    $v.quantityReservation.required_shipping_method,
                    $root,
                    'required_shipping_method'
                  )
                "
                :label="$tr('label_required', $tr('required_shipping_method'))"
                :items="shippingMethods"
                item-value="id"
                item-text="name"
                :placeholder="
                  $tr('choose_item', $tr('required_shipping_method'))
                "
                type="autocomplete"
                class="me-md-2"
              />
            </div>
            <div class="w-full">
              <CustomInput
                @blur="$v.quantityReservation.receiving_country_id.$touch()"
                v-model="$v.quantityReservation.receiving_country_id.$model"
                :rules="
                  validate(
                    $v.quantityReservation.receiving_country_id,
                    $root,
                    'receiving_country'
                  )
                "
                :label="$tr('receiving_country')"
                :items="countries"
                item-value="id"
                item-text="name"
                country
                :placeholder="$tr('choose_item', $tr('receiving_country'))"
                type="autocomplete"
                class="ms-md-2"
              />
            </div>
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
      products: [],
      countries: [],

      shippingMethods: [
        { id: "air", name: this.$tr("air") },
        { id: "ground", name: this.$tr("ground") },
        { id: "sea", name: this.$tr("sea") },
      ],
      quantityReservation: {
        product_id: null,
        quantity: null,
        required_shipping_method: null,
        receiving_country_id: null,
      },
    };
  },

  validations: {
    quantityReservation: {
      product_id: Validations.requiredValidation,
      quantity: Validations.requiredValidation,
      required_shipping_method: Validations.requiredValidation,
      receiving_country_id: Validations.requiredValidation,
    },
  },
  methods: {
    toggle() {
      this.model = !this.model;
      if (this.model) {
        this.fetchCountries();
        this.fetchProducts();
      }
    },

    async fetchCountries() {
      try {
        const url = "common/countries";
        const { data } = await this.$axios.get(url);
        this.countries = data.countries;
      } catch (error) {}
    },
    async fetchProducts() {
      try {
        const url = "single-sales/products-ssp/get?filter_product=true";
        const { data } = await this.$axios.get(url);
        this.products = data;
      } catch (error) {}
    },

    resetForm() {
      this.$refs.insertForm.reset();
    },

    async validateForm() {
      this.$refs.insertForm.validate();
      this.$v.quantityReservation.$touch();
      if (!this.$v.quantityReservation.$invalid) {
        this.isSubmitting = true;
        await this.insertRecord(this.quantityReservation);
        this.isSubmitting = false;
      }
    },

    async insertRecord(quantityReservation) {
      try {
        const url = "single-sales/quantity/reservation";
        const { data } = await this.$axios.post(url, quantityReservation);
        if (data.result) {
          this.$toasterNA("green", this.$tr('successfully_inserted'));
          const insertedRecord = data.data;
          this.$emit("pushRecord", insertedRecord);
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

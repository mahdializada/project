<template>
  <div>
    <div class="h-full d-flex align-center-justify-center">
      <div class="w-full">
        <CNumber
          :title="$tr('label_required', $tr('quantity'))"
          placeholder="quantity"
          v-model="form.quantity.$model"
          :rules="
            validateRule(
              form.quantity, // validate objec
              $root, // context
              $tr('quantity') // lable for feedback
            )
          "
        />
      </div>
      <div class="w-full ml-2">
        <CSelect
          @blur="form.receiving_country_id.$touch()"
          v-model="form.receiving_country_id.$model"
          :rules="
            validateRule(
              form.receiving_country_id, // validate objec
              $root, // context
              $tr('country') // lable for feedback
            )
          "
          :title="$tr('label_required', $tr('country'))"
          :items="countries"
          item-value="id"
          item-text="name"
          :placeholder="$tr('choose_item', $tr('country'))"
          prepend-inner-icon="fa fa-globe"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CSelect
          @blur="form.shipping_method.$touch()"
          v-model="form.shipping_method.$model"
          :rules="
            validateRule(
              form.shipping_method, // validate objec
              $root, // context
              $tr('shipping_method') // lable for feedback
            )
          "
          :title="$tr('label_required', $tr('shipping_method'))"
          :items="shippingMethods"
          item-value="id"
          item-text="name"
          :placeholder="$tr('choose_item', $tr('shipping_method'))"
          prepend-inner-icon="fa fa-train"
        />
      </div>
      <div class="w-full ml-2">
        <CTextField
          @blur="form.arrival_time.$touch()"
          v-model="form.arrival_time.$model"
          :rules="
            validateRule(
              form.arrival_time, // validate objec
              $root, // context
              $tr('arrival_time') // lable for feedback
            )
          "
          :title="$tr('label_required', $tr('arrival_time'))"
          :placeholder="$tr('choose_item', $tr('arrival_time'))"
          prepend-inner-icon="fa fa-file-text-o"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextField
          @blur="form.purchase_order_number.$touch()"
          v-model="form.purchase_order_number.$model"
          :rules="
            validateRule(
              form.purchase_order_number, // validate objec
              $root, // context
              $tr('required_purchase_order_number') // lable for feedback
            )
          "
          :title="$tr('label_required', $tr('purchase_order_number'))"
          :placeholder="$tr('choose_item', $tr('purchase_order_number'))"
          prepend-inner-icon="fa fa-file-text-o"
        />
      </div>
    </div>
    <div class="h-full d-flex align-center-justify-center mt-2">
      <div class="w-full">
        <CTextArea
          :title="$tr('order_note')"
          placeholder="order_note"
          v-model.trim="form.order_note.$model"
          :rules="
            validateRule(
              form.order_note, // validate objec
              $root, // context
              'order_note' // lable for feedback
            )
          "
          prepend-inner-icon="fa fa-sticky-note"
        />
      </div>
    </div>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CSelect from "../../new_form_components/Inputs/CSelect";
import CNumber from "../../new_form_components/Inputs/CNumber";
import CTextField from "../../new_form_components/Inputs/CTextField";
import CTextArea from "../../new_form_components/Inputs/CTextArea";

export default {
  name: "StoreInfo",
  components: {
    CNumber,
    CSelect,
    CTextField,
    CTextArea,
  },
  props: {
    form: Object, // default prop
  },
  data() {
    return {
      countries: [],
      shippingMethods: [
        { id: "air", name: this.$tr("air") },
        { id: "ground", name: this.$tr("ground") },
        { id: "sea", name: this.$tr("sea") },
      ],
      validateRule: GlobalRules.validate,
    };
  },
  created() {
    this.fetchCountries();
  },
  methods: {
    async fetchCountries() {
      try {
        const url = "common/countries";
        const { data } = await this.$axios.get(url);
        this.countries = data.countries;
        console.log(this.shippingMethods);
      } catch (error) {}
    },
    async validate() {
      // validate function must validate this step here and return true of false
      this.form.quantity.$touch();
      this.form.receiving_country_id.$touch();
      this.form.shipping_method.$touch();
      this.form.purchase_order_number.$touch();
      this.form.arrival_time.$touch();
      this.form.order_note.$touch();
      return (
        !this.form.quantity.$invalid &&
        !this.form.receiving_country_id.$invalid &&
        !this.form.shipping_method.$invalid &&
        !this.form.purchase_order_number.$invalid &&
        !this.form.arrival_time.$invalid &&
        !this.form.order_note.$invalid
      );
    },
  },
};
</script>

<style scoped></style>

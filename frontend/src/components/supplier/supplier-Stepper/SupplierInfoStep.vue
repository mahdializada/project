<template>
  <div>
    <CTitle :text="'supplier_info'" />
    <div class="h-full d-flex">
      <div class="w-full me-1">
        <CTextField
          :title="$tr('label_required', $tr('supplier_trading_name'))"
          placeholder="Supplier Trading Name"
          type="textfield"
          class="me-md-2"
          rounded
          :prepend-inner-icon="'mdi-account-supervisor'"
          v-model="form.supplier_trading_name.$model"
          @blur="form.supplier_trading_name.$touch()"
          :rules="
            validateRule(
              form.supplier_trading_name,
              $root,
              $tr('supplier_trading_name')
            )
          "
          :invalid="form.supplier_trading_name.$invalid"
        />
      </div>
      <div class="w-full ms-1">
        <CTextField
          :title="$tr('label_required', $tr('supplier_name'))"
          placeholder="Supplier Name"
          type="textfield"
          class="me-md-2"
          rounded
          :prepend-inner-icon="'#'"
          v-model="form.supplier_name.$model"
          @blur="form.supplier_name.$touch()"
          :rules="validateRule(form.supplier_name, $root, $tr('supplier_name'))"
          :invalid="form.supplier_name.$invalid"
        />
      </div>
    </div>
    <div class="h-full d-flex mt-2">
      <div class="w-full me-1">
        <CTextField
          :title="$tr('label_required', $tr('supplier_phone'))"
          placeholder="Supplier Phone"
          type="textfield"
          class="me-md-2"
          rounded
          :prepend-inner-icon="'mdi-cellphone-basic'"
          v-model="form.main_phone.$model"
          @blur="form.main_phone.$touch()"
          :rules="validateRule(form.main_phone, $root, $tr('supplier_phone'))"
          :invalid="form.main_phone.$invalid"
        />
      </div>
      <div class="w-full ms-1">
        <CTextField
          :title="$tr('label_required', $tr('supplier_email'))"
          placeholder="Supplier email"
          type="textfield"
          class="me-md-2"
          rounded
          :prepend-inner-icon="'mdi-gmail'"
          v-model="form.email.$model"
          @blur="form.email.$touch()"
          :rules="validateRule(form.email, $root, $tr('supplier_email'))"
          :invalid="form.email.$invalid"
        />
      </div>
    </div>
    <div class="h-full d-flex mt-2">
      <div class="w-full me-1">
        <CTextField
          :title="$tr('label_required', $tr('purchase_order_phone'))"
          placeholder="Purchase Order Phone"
          type="textfield"
          class="me-md-2"
          rounded
          :prepend-inner-icon="'mdi-cellphone'"
          v-model="form.purchase_order_phone.$model"
          @blur="form.purchase_order_phone.$touch()"
          :rules="
            validateRule(
              form.purchase_order_phone,
              $root,
              $tr('purchase_order_phone')
            )
          "
          :invalid="form.purchase_order_phone.$invalid"
        />
      </div>
  <div class="w-full me-1" style="overflow-x: hidden;">
    <div class="d-flex pa-4" style="width: 117%;margin-top: -33px;margin-left: -18px;">
      <CTextField
      :title="$tr('label_required', $tr('website'))"
      placeholder="Supplier Website"
      type="textfield"
      class="me-md-2"
      rounded
      :prepend-inner-icon="'#'"
      v-model="form.website.$model"
      @blur="form.website.$touch()"
      :rules="
            validateRule(
              form.website,
              $root,
              $tr('website')
              )
              "
          :invalid="form.website.$invalid"
          />
          <v-checkbox v-model="form.isRequired.$model" class="pe-2 position-absolute float-left" style="margin-left: 27rem;"></v-checkbox>
    </div>
  </div>
  </div>
    <div class="h-full d-flex mt-2">
      <div class="w-full">
        <!-- <CTextField
          :title="$tr('label_required', $tr('prepration_period'))"
          placeholder="Supplier Prepration Period"
          type="textfield"
          class="me-md-2"
          rounded
          :prepend-inner-icon="'#'"
          v-model="form.prepration_period.$model"
          @blur="form.prepration_period.$touch()"
          :rules="
            validateRule(
              form.prepration_period,
              $root,
              $tr('prepration_period')
            )
          "
          :invalid="form.prepration_period.$invalid"
        /> -->
        <CAutoComplete
        v-model="form.prepration_period.$model"
        @blur="form.prepration_period.$touch()"
        :rules="
          validateRule(
            form.prepration_period,
            $root,
            $tr('prepration_period')
          )
        "
        :invalid="form.prepration_period.$invalid"
          :items="preprationPeriod"
          item-value="value"
          item-text="value"
          placeholder="Supplier Prepration Period"
          :prepend-inner-icon="'#'"
          :isCheckNeeded="true"
          :title="$tr('label_required', $tr('prepration_period'))"
        />
      </div>
    </div>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import CTitle from "../../new_form_components/Inputs/CTitle.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
export default {
  computed: {},
  props: { form: Object },
  components: {
    CTitle,
    CTextField,
    CAutoComplete
},

  data() {
    return {
      validateRule: GlobalRules.validate,
      preprationPeriod:[
        { value:"1 Day"},
        { value:"2 Day"},
        { value:"3 Day"},
        { value:"4 Day"},
        { value:"5 Day"},
        { value:"6 Day"},
        { value:"7 Day"},
        { value:"8 Day"},
        { value:"9 Day"},
        { value:"10 Day"},
        { value:"11 Day"},
        { value:"12 Day"},
        { value:"13 Day"},
        { value:"14 Day"},
        { value:"15 Day"},
        { value:"16 Day"},
        { value:"17 Day"},
        { value:"18 Day"},
        { value:"19 Day"},
        { value:"20 Day"},
        { value:"21 Day"},
        { value:"22 Day"},
        { value:"23 Day"},
        { value:"24 Day"},
        { value:"25 Day"},
        { value:"26 Day"},
        { value:"27 Day"},
        { value:"28 Day"},
        { value:"29 Day"},
        { value:"30 Day"},
        { value:"31 Day"},
        { value:"32 Day"},
        { value:"33 Day"},
        { value:"34 Day"},
        { value:"35 Day"},
        { value:"36 Day"},
        { value:"37 Day"},
        { value:"38 Day"},
        { value:"39 Day"},
        { value:"40 Day"},
        { value:"41 Day"},
        { value:"42 Day"},
        { value:"43 Day"},
        { value:"44 Day"},
        { value:"45 Day"},
      ]
    };
  },
    methods: {
      async validate() {
        this.form.supplier_trading_name.$touch();
        this.form.supplier_name.$touch();
        this.form.main_phone.$touch();
        this.form.email.$touch();
        this.form.purchase_order_phone.$touch();
        this.form.website.$touch()
        this.form.prepration_period.$touch();
        return (
          !this.form.supplier_trading_name.$invalid &&
          !this.form.supplier_name.$invalid &&
          !this.form.main_phone.$invalid &&
          !this.form.email.$invalid &&
          !this.form.purchase_order_phone.$invalid &&
          !this.form.website.$invalid &&
          !this.form.prepration_period.$invalid
        );
      },
    async loaded() {},
  },
};
</script>

<style></style>

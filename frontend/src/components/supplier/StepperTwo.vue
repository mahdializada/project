<template>
  <v-row class="d-flex flex-column flex-md-row">
    <!-- input supplierTrading_name-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required',$tr('supplier_trading_name'))"
          placeholder="supplier_trading_name"
          type="textfield"
          class="me-md-2"
          v-model="supplier.supplier_trading_name.$model"
          :rules="rules.supplierTradingNameRule(supplier, $root)"
        />
      </div>
    </v-col>

    <!-- input supplier_name-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required',$tr('supplier_name'))"
          placeholder="supplier_name"
          type="textfield"
          class="me-md-2"
          v-model="supplier.supplier_name.$model"
          :rules="rules.supplierNameRule(supplier, $root)"
        />
      </div>
    </v-col>
    <!-- input main phone number-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required',$tr('phone'))"
          placeholder="phone"
          type="textfield"
          class="me-md-2"
          v-model="supplier.main_phone.$model"
          :rules="rules.mainPhoneRule(supplier, $root)"
          :error-messages="phoneErrorMessage"
        />
      </div>
    </v-col>

    <!-- input email-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required',$tr('email'))"
          placeholder="email"
          type="textfield"
          class="me-md-2"
          v-model="supplier.email.$model"
          :rules="rules.emailRule(supplier, $root)"
          :error-messages="emailErrorMessage"
        />
      </div>
    </v-col>

    <!-- input purchase_phone-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required',$tr('purchase_phone'))"
          placeholder="purchase_phone"
          type="textfield"
          class="me-md-2"
          v-model="supplier.purchase_order_phone.$model"
          :rules="rules.purchaseOrderPhoneRule(supplier, $root)"
          :error-messages="purchase_order_phoneErrorMessage"
        />
      </div>
    </v-col>
    <!-- input website-->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :label="$tr('label_required',$tr('website'))"
          placeholder="website"
          type="textfield"
          class="me-md-2"
          v-model="supplier.website.$model"
          :rules="rules.websiteRule(supplier, $root)"
        />
      </div>
    </v-col>
    <!-- input prepration period -->
    <v-col cols="12" md="6" class="py-0">
      <div class="w-full">
        <CustomInput
          :items="preprationperiod"
          :label="$tr('label_required',$tr('prepration_period'))"
          :placeholder="$tr('choose_item',$tr('prepration_period'))"
          type="autocomplete"
          item-text="name"
          item-value="id"
          class="me-md-2"
          v-model="supplier.prepration_period.$model"
          :rules="rules.preprationPeriodRule(supplier, $root)"
        />
      </div>
    </v-col>
  </v-row>
</template>

<script>
import CustomInput from "~/components/design/components/CustomInput.vue";

export default {
  components: { CustomInput },
  props: {
    supplier: {
      required: true,
      type: Object,
    },
    rules: {
      required: true,
      type: Object,
    },
  },
  data() {
    return {
      preprationperiod: [
        { id: "perparationperiod1", name: "Firstttt" },
        { id: "perparationperiod2", name: "Second" },
      ], 
      emailErrorMessage: "",
      phoneErrorMessage: "",
      purchase_order_phoneErrorMessage: "",
    };
  },
  methods: {
 async   checkValidation() {
      let isValid= !this.supplier.supplier_trading_name.$invalid &&
        !this.supplier.supplier_name.$invalid &&
        !this.supplier.main_phone.$invalid &&
        !this.supplier.email.$invalid &&
        !this.supplier.purchase_order_phone.$invalid &&
        !this.supplier.prepration_period.$invalid &&
        !this.supplier.website.$invalid;
        if(isValid){
          isValid=  await this.checkColumns();
          
          return !isValid;
        }
        return isValid;
        
        
    },
      // check column uniqueness from server
    async checkColumns() {
      const uniqueNess = await this.checkUniqueNess();
      

      // check email address
      const isEmailAlreadyExists = uniqueNess?.isEmailAlreadyExists;
      if (isEmailAlreadyExists) {
        this.emailErrorMessage = this.$tr("exists", [
          this.$tr("email"),
        ]);
      } else {
        this.emailErrorMessage = "";
      }

      // check phone number
      const isPhoneAlreadyExists = uniqueNess?.isPhoneAlreadyExists;
      if (isPhoneAlreadyExists) {
        this.phoneErrorMessage = this.$tr("exists", [
          this.$tr("phone"),
        ]);
      } else {
        this.phoneErrorMessage = "";
      }

      // check purchase_order_phone number
      const ispurchase_order_phoneAlreadyExists = uniqueNess?.ispurchase_order_phoneAlreadyExists;
      if (ispurchase_order_phoneAlreadyExists) {
        this.purchase_order_phoneErrorMessage = this.$tr("exists", [
          this.$tr("purchase_order_phone"),
        ]);
      } else {
        this.purchase_order_phoneErrorMessage = "";
      }
      // alert("email"+isEmailAlreadyExists+" phone"+isPhoneAlreadyExists+" pu phone"+ispurchase_order_phoneAlreadyExists);
     
     return ( 
        isEmailAlreadyExists ||
        isPhoneAlreadyExists ||
        ispurchase_order_phoneAlreadyExists
      );
    },
    // check for unique ness of some columns
    async checkUniqueNess() {
      try {
         
        const email = this.supplier?.email.$model;
        const main_phone = this.supplier?.main_phone.$model;
        const purchase_order_phone = this.supplier?.purchase_order_phone.$model;

        const columns = [ 
          { columnName: "email", value: email },
          { columnName: "main_phone", value: main_phone },
          { columnName: "purchase_order_phone", value: purchase_order_phone },
        ];
        let url = "suppliers/check-uniqueness";
       
        const response = await this.$axios.post(url, columns);
       
        return { 
          isEmailAlreadyExists: response?.data?.email,
          isPhoneAlreadyExists: response?.data?.main_phone,
          ispurchase_order_phoneAlreadyExists: response?.data?.purchase_order_phone,
        };
      } catch (error) {
        
        HandleException.handleApiException(this, error.data);
        return false;
      }
    },
  },
};
</script>

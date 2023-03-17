<template>
  <v-form
    ref="supplierRegisterForm"
    @submit.prevent="onSubmit"
    id="SupplierForm"
  >
    <!-- @onNext="changeStepper"
      @onChangeTo="onChangeTo" -->
    <CustomStepper
      ref="customStepper"
      :headers="steppers"
      :canNext="false"
      @close="closeForm"
      @submit="isStep4Valid"
      @validate="validateStepper"
      :skip="[3]"
      @onSkip="skipLocation"
      :is-submitting="isLoading"
    >
      <template v-slot:step1>
        <StepperOne ref="StepOne" :supplierProp="$v.supplier" :rules="rules" />
      </template>
      <template v-slot:step2>
        <StepperTwo ref="StepTwo" :supplier="$v.supplier" :rules="rules" />
      </template>
      <template v-slot:step3>
        <StepperThree ref="StepThree" />
      </template>

      <template v-slot:step4>
        <StepperFour ref="StepFour" :supplier="$v.supplier" :rules="rules" />
      </template>
      <template v-slot:step5>
        <DoneMessage
          :title="$tr('item_all_set', $tr('supplier'))"
          :subTitle="$tr('you_can_access_your_item', $tr('supplier'))"
        />
      </template>
    </CustomStepper>
  </v-form>
</template>

<script>
import CustomStepper from "../design/FormStepper/CustomStepper";
import suppliers from "../../configs/pages/suppliers";
import CustomInput from "../design/components/CustomInput";
import SelectedItem from "../design/components/SelectedItem";
import FormBtn from "../design/components/FormBtn";
import ManualPermission from "../design/ManualPermission.vue";
import DoneMessage from "../design/components/DoneMessage.vue";
import TextField from "~/components/common/TextField";
import { mapActions, mapGetters } from "vuex";
import HandleException from "~/helpers/handle-exception";
import CloseDialogButton from "../common/buttons/CloseDialogButton";
import AutoComplete from "../common/AutoComplete";

// new
import StepperOne from "./StepperOne.vue";
import StepperTwo from "./StepperTwo.vue";
import StepperThree from "./StepperThree.vue";
import StepperFour from "./StepperFour.vue";
import SupplierValidations from "../../validations/supplier-validations";

export default {
  name: "SupplierRegistration",
  components: {
    ManualPermission,
    TextField,
    CloseDialogButton,
    AutoComplete,
    CustomStepper,
    suppliers,
    CustomInput,
    SelectedItem,
    FormBtn,
    DoneMessage,
    StepperOne,
    StepperTwo,
    StepperThree,
    StepperFour,
    SupplierValidations,
  },
  props: {},

  data() {
    return {
      steppers: suppliers(this).steppers,
      isLoading: false,
      isFileSelected: false,
      supplier: JSON.parse(JSON.stringify(SupplierValidations.schema)),
      rules: SupplierValidations.rules,
      isLocationSkipped: false,
      isInserted: false,
    };
  },

  computed: {
    ...mapGetters({
      successInsert: "suppliers/getSuccessInsert",
    }),
  },

  methods: {
    ...mapActions({
      insertNewSupplier: "suppliers/insertSupplier",
    }),

    // *********************  INSERTION METHODS GOES HERE   ***********************
    async onSubmit() {
      this.isLoading = true;
      console.log("before", this.supplier);
      this.prepairDataFormat();
      let result = await this.insertNewSupplier(this.supplier);
      

      if (result.result != "false") {
        this.isLoading = false;

        this.$refs.customStepper.setCurrent(5);
        this.resetForm();
      } else {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

      }
      this.isLoading = false;
    },
    prepairDataFormat() {
      const supplierData = this.$refs.StepOne?.supplier;
      this.supplier.companyIds = supplierData.selectedCompanies?.map((row) => {
        return row.id;
      });
      this.supplier.sourcerIds = supplierData.selectedSourcers?.map((row) => {
        return row.id;
      }); 
      this.supplier.location = this.$refs.StepThree?.addedLocations; 
      this.supplier.status = "pending";
      this.supplier.county_type = "pending";
    },

    resetForm() {
      this.$v.supplier.$reset();
      this.$refs.supplierRegisterForm.reset();
      this.$refs.supplierRegisterForm.resetValidation();
    },

    closeForm() {
      this.resetForm();
      this.$refs.customStepper.setCurrent(1);
      return this.isInserted;
    },

    // **************************  VALIDATIONS METHODS GOES EHER   **********************************

    validateStepper(step) {
      this.$v.supplier.$touch();
      this.$refs.supplierRegisterForm.validate();
      switch (step) {
        case 1:
          this.isStep1Valid();
          return;
          break;
        case 2:
          this.isStep2Valid();
          break;

        case 3:
          {
            this.isStep3Valid();

            return;
          }
          break;
        default:
          break;
      }
    },

    // check Steeper one
    isStep1Valid() {
      const isStepOneValid = this.$refs.StepOne.checkValidation();
      if (isStepOneValid) {
        this.$refs.customStepper?.forceNext();
        this.$refs.supplierRegisterForm?.resetValidation();
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },
    // check Steeper Two
    async isStep2Valid() {
      const isStepTwoValid = await this.$refs.StepTwo.checkValidation();

      if (isStepTwoValid) {
        this.$refs.customStepper?.forceNext();
        this.$refs.supplierRegisterForm?.resetValidation();
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },

    // check Steeper Three
    isStep3Valid() {
      const isStepThreeValid = this.$refs.StepThree.checkValidation();

      if (isStepThreeValid) {
        this.$refs.customStepper?.forceNext();
        this.$refs.supplierRegisterForm?.resetValidation();
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },
    // check Steeper four
    async isStep4Valid() {
      this.$v.supplier.$touch();
      this.$refs.supplierRegisterForm.validate();
      this.isLoading = true;
      const isStepFourValid = this.$refs.StepFour.checkValidation();
      this.isLoading = false;
      if (isStepFourValid) {
        await this.onSubmit();
        this.resetForm();
        this.isInserted = true;
      } else {
        // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

      }
    },
    /** Roles Section */
    skipLocation() {
      this.isLocationSkipped = true;
      this.$refs.supplierRegisterForm?.resetValidation();
    },
  },
  validations: {
    supplier: SupplierValidations.validations,
  },
};
</script>

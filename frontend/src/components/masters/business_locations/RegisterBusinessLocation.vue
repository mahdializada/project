<template>
  <div>
    <v-dialog v-model="model" width="1300" persistent>
      <Dialog @closeDialog="closeDialog">
        <v-form lazy-validation ref="businessLocationForm">
          <CustomStepper
            :headers="steppers"
            @close="close"
            :canNext="false"
            @validate="validateStepper"
            :is-submitting="isSubmitting"
            :loading="isLoading"
            ref="businessLocationStepper"
            @submit="insertSubmitForm"
          >
            <template v-slot:step1>
              <FilterCountry
                ref="filterCountry"
                showCompanyBtn
                mt-4
                isDepartment
                skipDepartment
              />
            </template>
            <template v-slot:step2>
              <BusinessLocationStepTwo
                :businessLocation="$v.businessLocation"
                :rules="rules"
                ref="businessLocationStepTwoRef"
              />
            </template>

            <template v-slot:step3>
              <LabelRemark
                ref="labelRemarkRef"
                :item="$v.businessLocation"
                :rules="rules"
              />
            </template>

            <template v-slot:step4>
              <DoneMessage
                :title="$tr('item_all_set', $tr('business_locations'))"
                :subTitle="
                  $tr('you_can_access_your_item', $tr('business_locations'))
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
import HandleException from "~/helpers/handle-exception";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import CustomStepper from "~/components/design/FormStepper/CustomStepper";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import BusinessLocationStepOne from "./BusinessLocationStepOne";
import BusinessLocationStepTwo from "./BusinessLocationStepTwo";
import Helpers from "~/helpers/helpers";
import LabelRemark from "~/components/users/LabelRemark";
import BusinessLocationValidations from "~/validations/business-location-validations";
import FilterCountry from "../../users/FilterCountry.vue";

import { mapActions } from "vuex";

export default {
  components: {
    Dialog,
    CustomStepper,
    LabelRemark,
    DoneMessage,
    BusinessLocationStepTwo,
    BusinessLocationStepOne,
    FilterCountry,
  },
  name: "RegisterBusinessLocation",
  props: {
    tableKey: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      model: true,
      isLoading: false,
      isSubmitting: false,
      shouldGoesBack: false,
      rules: BusinessLocationValidations.rules,
      steppers: BusinessLocationValidations.steppers,
      businessLocation: JSON.parse(
        JSON.stringify(BusinessLocationValidations.schema)
      ),
    };
  },

  methods: {
    ...mapActions({
      fetchAllCountries: "countries/fetchAscCountries",
      fetchItems: "business_locations/fetchItems",
    }),
    // validate form and data
    async validateStepper(currentStep) {
      this.$refs.businessLocationForm.validate();
      switch (currentStep) {
        case 1:
          this.isLoading = true;
          const isGeneralSectionValid =
            await this.$refs.filterCountry.checkValidation();
          if (isGeneralSectionValid) {
            await this.fetchAllCountries();
            this.isLoading = false;
            this.$refs.businessLocationForm?.resetValidation();
            this.$refs.businessLocationStepper?.forceNext();
          }
          this.isLoading = false;
          break;
        case 2:
          this.isLoading = true;
          const isInformationValid =
            await this.$refs.businessLocationStepTwoRef.checkValidations();
          if (isInformationValid) {
            this.isLoading = false;
            this.$refs.businessLocationForm?.resetValidation();
            this.$refs.businessLocationStepper?.forceNext();
          }
          this.isLoading = false;
          break;
        default:
          break;
      }
    },

    // submit registration form
    async insertSubmitForm() {
      this.$v.businessLocation.company_id.$model =
        this.$refs.filterCountry.selectedCompan;
      const selectedCompanies =
        this.$refs.filterCountry.selectedCompanies;
      if (selectedCompanies && selectedCompanies.length) {
        this.$v.businessLocation.company_id.$model = selectedCompanies[0].id;
      }
      this.$refs.businessLocationForm.validate();
      this.$v.businessLocation.$touch();

      const isRemartkValid = this.$refs.labelRemarkRef.checkValidation();
      if (!this.$v.businessLocation.$invalid && isRemartkValid) {
        this.isSubmitting = true;
        const businessLocationFormData = Helpers.getFormData(
          this.businessLocation
        );
        await this.insertRecord(businessLocationFormData);
        this.isSubmitting = false;
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
					this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },

    // insert record into database
    async insertRecord(data) {
      try {
        const response = await this.$axios.post("business-locations", data);
        if (response?.status === 201 && response?.data?.result) {
          this.$refs.businessLocationStepper.forceNext();
          this.clearAllPrevData();
          this.fetchItems({ key: this.tableKey });
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
      this.businessLocation = JSON.parse(
        JSON.stringify(BusinessLocationValidations.schema)
      );

      this.$refs.filterCountry.clearItems();
      this.$v.businessLocation.$reset();
      this.$refs.businessLocationForm.reset();
      this.$refs.businessLocationForm.resetValidation();
      this.shouldGoesBack = true;
    },

    // close dialog
    close() {
      if (this.shouldGoesBack) {
        this.shouldGoesBack = false;
        this.$refs.businessLocationStepper.setCurrent(1);
      }
    },

    // close dialog
    closeDialog() {
      this.$emit("update:registerDialog", false);
    },
  },

  validations: {
    businessLocation: BusinessLocationValidations.validations,
  },
};
</script>

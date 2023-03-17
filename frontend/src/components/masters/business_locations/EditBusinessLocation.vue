<template>
  <v-dialog v-model="model" persistent width="1300">
    <v-form
      :key="currentIndex"
      lazy-validation
      ref="updateForm"
      @submit.prevent="onSubmit"
    >
      <Edit
        :is-icon="false"
        :headers="steppers"
        :title="title"
        :showTitle="true"
        :topIcon="topIcon"
        :tableName="tableName"
        :isLoading="isLoading"
        :is-submitting="isSubmitting"
        :is-auto-edit="isAutoEdit"
        :totals="editItems.length"
        :current-index="currentIndex"
        ref="editRef"
        @onValidate="validateStepper"
        @onChangeTo="onChangeTo"
        @onSubmit="onSubmit"
        @close="closeDialog"
        @onItemChange="onItemChange"
        @onSave="onSave"
        @OnSaveAndNext="onSaveAndNext"
      >
        <template v-slot:step1>
          <div class="topDiv">
            <HeaderTitle title="general" />
            <div class="pl-3 pa-1">
              <BusinessLocationStepOne
                :businessLocation="$v.businessLocation"
                :company-props="[businessLocation.company]"
                :country-props="businessLocation.selectedCountries"
                :rules="rules"
                ref="businessLocationStepOneRef"
              />
            </div>
          </div>
        </template>
        <template v-slot:step2>
          <div class="topDiv">
            <HeaderTitle title="information" />
            <div class="pl-3 pa-1">
              <BusinessLocationStepTwo
                :businessLocation="$v.businessLocation"
                :rules="rules"
                is-edit
                ref="businessLocationStepTwoRef"
              />
            </div>
          </div>
        </template>

        <template v-slot:step3>
          <div class="topDiv">
            <HeaderTitle title="remarks" />
            <div class="px-3 pa-1">
              <LabelRemark
                ref="labelRemarkRef"
                :item="$v.businessLocation"
                :rules="rules"
              />
            </div>
          </div>
        </template>
        <template v-slot:step4>
          <DoneMessage
            :title="$tr('item_all_set', $tr('business_locations'))"
            :subTitle="
              $tr('you_can_access_your_item', $tr('business_locations'))
            "
          />
        </template>
      </Edit>
    </v-form>
  </v-dialog>
</template>

<script>
import HandleException from "~/helpers/handle-exception";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import BusinessLocationStepOne from "./BusinessLocationStepOne";
import BusinessLocationStepTwo from "./BusinessLocationStepTwo";
import Alert from "~/helpers/alert";
import LabelRemark from "~/components/users/LabelRemark";
import Edit from "~/components/design/Edit";
import HeaderTitle from "../../users/HeaderTitle";
import BusinessLocationValidations from "~/validations/business-location-validations";
import { mapActions, mapGetters } from "vuex";

export default {
  components: {
    LabelRemark,
    DoneMessage,
    Edit,
    HeaderTitle,
    BusinessLocationStepTwo,
    BusinessLocationStepOne,
  },
  name: "EditBusinessLocation",
  props: {
    editItems: {
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

    tabKey: {
      required: true,
    },
  },
  data() {
    const firstIndex = 0;
    return {
      model: true,
      currentIndex: firstIndex,
      topIcon: "mdi-account",
      tableName: this.$tr("business_locations"),
      rules: BusinessLocationValidations.rules,
      steppers: BusinessLocationValidations.steppers,
      businessLocation: this.editItems[firstIndex],
      isLoading: false,
      isSubmitting: false,
      showProgressBar: false,
      title: this.editItems[firstIndex]?.name,
    };
  },

  computed: {
    ...mapGetters({
      companies: "filterData/getCompaniesOfTheCountries",
    }),
  },

  methods: {
    ...mapActions({
      fetchAllCountries: "countries/fetchAscCountries",
      fetchItems: "business_locations/fetchItems",
    }),

    // close dialog
    closeDialog() {
      this.$emit("update:editDialog", false);
      this.$emit("update:isAutoEdit", false);
    },

    // fired on item changed
    async onItemChange(actionType) {
      if (actionType === "next") {
        const index = this.currentIndex + 1;
        if (index < this.editItems.length) {
          this.businessLocation = this.editItems[index];
          this.title = this.editItems[index]?.name;
          this.currentIndex = index;
        }
      } else if (actionType === "back") {
        const index = this.currentIndex - 1;
        if (index >= 0) {
          this.businessLocation = this.editItems[index];
          this.title = this.editItems[index]?.name;
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

    // validate form and data
    async validateStepper(currentStep) {
      this.$refs.updateForm.validate();
      switch (currentStep) {
        case 1:
          this.isLoading = true;
          const isGeneralSectionValid =
            await this.$refs.businessLocationStepOneRef.checkValidations();
          if (isGeneralSectionValid) {
            await this.fetchAllCountries();
            const countryId = this.businessLocation.country_id;
            await this.$refs.businessLocationStepTwoRef.onCountrySelected(
              countryId,
              true
            );
            this.isLoading = false;
            this.$refs.updateForm?.resetValidation();
            this.$refs.editRef.nextForce();
          }
          this.isLoading = false;
          break;
        case 2:
          this.isLoading = true;
          const isInformationValid =
            await this.$refs.businessLocationStepTwoRef.checkValidations();
          if (isInformationValid) {
            this.isLoading = false;
            this.$refs.updateForm?.resetValidation();
            this.$refs.editRef.nextForce();
          }
          this.isLoading = false;
          break;
        case 3:
          this.isLoading = true;
          const isLocationSectionValid =
            this.$refs.businessLocationStepThreeRef.checkValidations();
          if (isLocationSectionValid) {
            this.isLoading = false;
            this.$refs.updateForm?.resetValidation();
            this.$refs.editRef.nextForce();
          }
          this.isLoading = false;
          break;
        default:
          break;
      }
    },
    async onChangeTo(step) {
      this.$refs.updateForm.validate();

      switch (step) {
        case 1:
          // this.isLoading = true;
          this.$refs.updateForm?.resetValidation();
          this.$refs.editRef.setCurrent(step);
          // this.isLoading = false;
          break;
        case 2:
          const isGeneralSectionValid =
            await this.$refs.businessLocationStepOneRef.checkValidations();
          if (isGeneralSectionValid) {
            this.$refs.updateForm?.resetValidation();
            this.$refs.editRef.setCurrent(step);
          }
        case 3:
          const isInformationValid =
            await this.$refs.businessLocationStepTwoRef.checkValidations();

          const isGeneralSectionValid2 =
            await this.$refs.businessLocationStepOneRef.checkValidations();
          if (isInformationValid && isGeneralSectionValid2) {
            this.$refs.updateForm?.resetValidation();
            this.$refs.editRef.setCurrent(step);
          }
          break;
        default:
          break;
      }
    },
    // submit update form
    async onSubmit(canNext = "canNext") {
      this.$v.businessLocation.company_id.$model =
        this.$refs.businessLocationStepOneRef.selectedCompan;
      const selectedCompanies =
        this.$refs.businessLocationStepOneRef.selectedCompanies;
      if (selectedCompanies && selectedCompanies.length) {
        this.$v.businessLocation.company_id.$model = selectedCompanies[0].id;
      }
      this.$refs.updateForm.validate();
      this.$v.businessLocation.$touch();

      const isRemartkValid = this.$refs.labelRemarkRef.checkValidation();
      if (!this.$v.businessLocation.$invalid && isRemartkValid) {
        const { selectedCountries, company, ...businessLocation } =
          this.businessLocation;

        await this.updateBusinessLocation(businessLocation, canNext);
      } else {
        // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
						this.$toasterNA("orange",this.$tr('please_fill_all_fields_correctly'));

      }
    },

    // update item from server
    async updateBusinessLocation(data, canNext) {
      this.isSubmitting = true;
      try {
        const url = "business-locations/itemId";
        const response = await this.$axios.put(url, data);
        this.isSubmitting = false;
        if (response?.status === 200 && response?.data.result) {
          if (canNext === "onSaveAndNext") {
            if (this.currentIndex < this.editItems.length) {
              Alert.successAlert(this, this.$tr("successfully_updated"));
              this.onItemChange("next");
            }
          } else if (canNext === "canNext") {
            this.$refs.editRef.nextForce();
          } else {
            Alert.successAlert(this, this.$tr("successfully_updated"));
          }
          await this.fetchItems({ key: this.tabKey });
        } else {
          // this.$toastr.w(this.$tr("something_went_wrong"));
						this.$toasterNA("orange",this.$tr('something_went_wrong'));

        }
      } catch (error) {
        this.isSubmitting = false;
        HandleException.handleApiException(this, error);
      }
    },
  },

  validations: {
    businessLocation: BusinessLocationValidations.validations,
  },
};
</script>

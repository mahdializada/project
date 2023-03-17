<template>
  <v-form lazy-validation ref="userRegisterForm">
    <CustomStepper
      :headers="steppers"
      @close="close"
      :canNext="false"
      @validate="validateStepper"
      :loading="isLoading"
      :is-submitting="isSubmitting"
      ref="registerUserStepper"
      @submit="submitForm"
      :skip="[4]"
      @onSkip="skipRoleSection"
    >
      <template v-slot:step1>
        <FilterCountry ref="filterCountryComponent" />
      </template>
      <template v-slot:step2>
        <UserStepOne ref="userStepOneRef" :user="$v.user" :rules="rules" />
      </template>
      <template v-slot:step3>
        <UserStepTwo ref="userStepTwoRef" :user="$v.user" :rules="rules" />
      </template>
      <template v-slot:step4>
        <StaticPermission
          ref="statisPermissions"
          :padding="0"
          :allCompaniesProps="companies"
          showTabs
        />
      </template>
      <template v-slot:step5>
        <LabelRemark ref="userStepThreeRef" :item="$v.user" :rules="rules" />
      </template>
      <template v-slot:step6>
        <DoneMessage
          :title="$tr('item_all_set', $tr('account'))"
          :subTitle="$tr('you_can_access_your_item', $tr('account'))"
        />
      </template>
    </CustomStepper>
  </v-form>
</template>

<script>
import CustomStepper from "../design/FormStepper/CustomStepper.vue";
import CustomInput from "../design/components/CustomInput.vue";
import SelectedItem from "../design/components/SelectedItem.vue";
import FormBtn from "../design/components/FormBtn.vue";
import StaticPermission from "./StaticPermission";
import ManualPermission from "../design/ManualPermission.vue";
import UserValidations from "../../validations/user-validation";
import DoneMessage from "../design/components/DoneMessage.vue";
import { mapActions, mapGetters } from "vuex";
import HandleException from "../../helpers/handle-exception";
import Helpers from "../../helpers/helpers";
import FilterCountry from "./FilterCountry";
import UserStepOne from "./UserStepOne";
import UserStepTwo from "./UserStepTwo";
import LabelRemark from "./LabelRemark";

export default {
  components: {
    StaticPermission,
    LabelRemark,
    UserStepTwo,
    UserStepOne,
    FilterCountry,
    CustomStepper,
    CustomInput,
    SelectedItem,
    FormBtn,
    ManualPermission,
    DoneMessage,
  },
  props: {
    tableKey: {
      required: true,
    },
  },

  data() {
    return {
      isLoading: false,
      isSubmitting: false,
      rules: UserValidations.rules,
      steppers: UserValidations.steppers,
      user: JSON.parse(JSON.stringify(UserValidations.schema)),
      shouldGoesBack: false,
      isPermissionsSkipped: false,
    };
  },

  computed: {
    ...mapGetters({
      allCountries: "countries/getAscCountries",
      countryLanguages: "filterData/getCountryLanguages",
      companies: "filterData/getCompaniesOfTheCountries",
    }),
  },

  methods: {
    ...mapActions({
      fetchAllCountries: "countries/fetchAscCountries",
      fetchCountryLanguages: "filterData/fetchCountryLanguages",
      insertNewItem: "users/insertNewItem",
      fetchItems: "users/fetchItems",
    }),

    // validate form and data
    validateStepper(currentStep) {
      switch (currentStep) {
        case 1:
          this.checkGeneralValidation();
          break;
        case 2:
          this.checkAccountValidations();
          break;
        case 3:
          this.checkPersonalValidation();
          break;
        case 4:
          this.checkRoleSection();
          break;
        default:
          break;
      }
    },

    /** General Section */
    // check general section validations
    checkGeneralValidation() {
      const isGenerateValid =
        this.$refs.filterCountryComponent.checkValidation();
      if (isGenerateValid) {
        this.$refs.registerUserStepper?.forceNext();
      }
    },

    /** Account Section */
    // check account section validations
    async checkAccountValidations() {
      this.$refs.userRegisterForm.validate();

      this.isLoading = true;
      const isAccountSectionValid =
        await this.$refs.userStepOneRef.checkValidations();
      this.isLoading = false;

      if (isAccountSectionValid) {
        this.$refs.registerUserStepper?.forceNext();
        this.$refs.userRegisterForm?.resetValidation();
        await this.fetchAllCountries();
      }
    },

    /** Personal Section */

    // check personal section validations
    async checkPersonalValidation() {
      this.$refs.userRegisterForm.validate();
      const isPersonalSectionValid =
        this.$refs.userStepTwoRef.checkPersonalValidation();
      if (isPersonalSectionValid) {
        this.isLoading = true;
        const teams = await this.fetchAllTeams();
        const selectedCompanies =
          this.$refs.filterCountryComponent.selectedCompanies;
        const companyIds = selectedCompanies.map((company) => company.id);
        await this.$refs.statisPermissions.fetchCompaniesSystems(companyIds);
        this.$refs.statisPermissions.setExtraData(teams, selectedCompanies);
        this.isLoading = false;
        this.$refs.registerUserStepper.forceNext();
        this.$refs.userRegisterForm.resetValidation();
      }
    },

    /** Roles Section */
    skipRoleSection() {
      this.isPermissionsSkipped = true;
      this.$refs.userRegisterForm?.resetValidation();
    },

    // check role section validations
    async checkRoleSection() {
      this.isPermissionsSkipped = false;
      const isRoleSectionValid =
        this.$refs.statisPermissions.checkRoleSection();

      if (isRoleSectionValid) {
        this.$refs.registerUserStepper?.forceNext();
        this.$refs.userRegisterForm?.resetValidation();
      }
    },

    // fetch all teams
    async fetchAllTeams() {
      try {
        const departments =
          this.$refs.filterCountryComponent.selectedDepartments;
        const departmentsIds = departments.map(({ id }) => id);
        const response = await this.$axios.get(`common/team/${departmentsIds}`);
        return response?.data;
      } catch (_) {
        return [];
      }
    },

    /** Remarks Section */
    async submitForm() {
      this.$refs.userRegisterForm.validate();
      this.$v.user.$touch();
      const isRemarkValid = this.$refs.userStepThreeRef.checkValidation();

      if (!this.$v.user.$invalid && isRemarkValid) {
        this.isSubmitting = true;
        const permissions = this.$refs.statisPermissions.getData();
        permissions.isPermissionsSkipped = this.isPermissionsSkipped;
        const user = this.user;
        const userData = {
          ...permissions,
          ...user,
        };
        const userFormData = Helpers.getFormData(userData);
        await this.storeUser(userFormData);
        this.isSubmitting = false;
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },

    // store user into database
    async storeUser(userData) {
      try {
        const response = await this.$axios.post("users", userData);
        if (response?.status === 201 && response?.data?.result) {
          this.$refs.registerUserStepper.forceNext();

          const insertedUser = response?.data?.data;
          this.insertNewItem(insertedUser);
          this.clearAllPrevData();
          this.$refs.statisPermissions.clearAllPermissions();
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
      this.$store.commit("countries/clearAscCountries");
      this.$store.commit("filterData/clearCountryLanguages");
      this.$store.dispatch("filterData/clearCompanyDepartment");
      this.user = JSON.parse(JSON.stringify(UserValidations.schema));
      this.$refs.filterCountryComponent.clearItems();
      this.$refs.userStepTwoRef.clearItems();
      this.$v.user.$reset();

      this.$refs.userRegisterForm?.reset();
      this.$refs.userRegisterForm?.resetValidation();
      this.shouldGoesBack = true;
      this.isPermissionsSkipped = false;
      this.$v.user.change_password.$model = true;
      this.$v.user.email_password.$model = true;
      this.$v.user.generate_password.$model = true;
    },

    // close dialog
    close() {
      if (this.shouldGoesBack) {
        this.shouldGoesBack = false;
        this.$refs.registerUserStepper.setCurrent(1);
      }
    },
  },

  validations: {
    user: UserValidations.validations,
  },
};
</script>

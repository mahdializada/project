<template>
  <v-form
    :key="currentIndex"
    lazy-validation
    ref="userUpdateForm"
    @submit.prevent="onSubmit"
  >
    <Edit
      hasLogo
      :is-icon="false"
      :showTitle="true"
      :icon-or-logo="userImage"
      :headers="headers"
      :title="title"
      :topIcon="topIcon"
      :tableName="tableName"
      :isLoading="isLoading"
      :is-submitting="isSubmitting"
      :is-auto-edit="isAutoEdit"
      :totals="editItems.length"
      :current-index="currentIndex"
      ref="editRef"
      @onValidate="checkValidations"
      @onChangeTo="onChangeTo"
      @onSubmit="onSubmit"
      @close="closeDialog"
      @onItemChange="onItemChange"
      @onSave="onSave"
      @OnSaveAndNext="onSaveAndNext"
    >
      <template v-slot:step1>
        <div>
          <HeaderTitle title="general" />
          <div class="pl-3 pa-1">
            <FilterCountry
              :company-props="user.selectedCompanies"
              :country-props="user.selectedCountries"
              :department-props="user.selectedDepartments"
              ref="filterCountryComponent"
            />
          </div>
        </div>
      </template>
      <template v-slot:step2>
        <div>
          <HeaderTitle title="account" />
          <div class="pl-3 pa-1">
            <UserStepOne
              ref="userStepOneRef"
              :user="$v.user"
              :rules="rules"
              is-edit
            />
          </div>
        </div>
      </template>
      <template v-slot:step3>
        <div>
          <HeaderTitle title="personal" />
          <div class="pa-1 px-3">
            <UserStepTwo
              ref="userStepTwoRef"
              :user="$v.user"
              :rules="rules"
              is-edit
            />
          </div>
        </div>
      </template>
      <template v-slot:step4>
        <div>
          <HeaderTitle title="permissions" />
          <div class="px-3 pa-1">
            <StaticPermission
              ref="statisPermissions"
              :padding="0"
              showTabs
              :allCompaniesProps="companies"
              :scheduleTypeProps="user.schedule_type"
              :selectedSubSystemsProps="user.selectedSubSystems"
              :selectedActionsProps="user.selectedSystems"
              :selectedCompanySystemsProps="user.companiesSystems"
              :selectedTeamsProps="user.selectedTeams"
              :selectedRolesProps="user.selectedRoles"
              :selectedCompaniesProps="user.selectedCompanies"
              :allTeamsProps="user.companyDepartmentsTeams"
              :timeRangeProps="user.time_range"
              :dateRangeProps="user.date_range"
              :actualTimeRangeProps="user.actualTimeRange"
              :actualDateRangeProps="user.actualDateRange"
            />
          </div>
        </div>
      </template>
      <template v-slot:step5>
        <div>
          <HeaderTitle title="remarks" />
          <div class="px-3 pa-1">
            <LabelRemark
              ref="userStepThreeRef"
              :item="$v.user"
              :rules="rules"
            />
          </div>
        </div>
      </template>
      <template v-slot:step6>
        <DoneMessage
          :title="$tr('item_all_set', $tr('user'))"
          :subTitle="$tr('you_can_access_your_item', $tr('user'))"
        />
      </template>
    </Edit>
  </v-form>
</template>

<script>
import Edit from "../design/Edit.vue";
import UserValidations from "../../validations/user-validation";
import UserStepOne from "./UserStepOne";
import UserStepTwo from "./UserStepTwo";
import { mapActions, mapGetters } from "vuex";
import LabelRemark from "./LabelRemark";
import Helper from "../../helpers/helpers";
import HandleException from "../../helpers/handle-exception";
import Alert from "../../helpers/alert";
import DoneMessage from "../design/components/DoneMessage";
import Users from "../../configs/pages/users";
import FilterCountry from "./FilterCountry";
import HeaderTitle from "./HeaderTitle";
import StaticPermission from "./StaticPermission";

export default {
  components: {
    FilterCountry,
    DoneMessage,
    LabelRemark,
    UserStepTwo,
    UserStepOne,
    Edit,
    HeaderTitle,
    StaticPermission,
  },
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
      currentIndex: firstIndex,
      topIcon: "mdi-account",
      userImage: this.editItems[firstIndex].image,
      tableName: this.$tr("user"),
      headers: this.isAutoEdit
        ? Users(this).autoEditSteppers
        : Users(this).editSteppers,
      user: this.editItems[firstIndex],
      rules: UserValidations.rules,
      isLoading: false,
      isSubmitting: false,
      showProgressBar: false,
      isPermissionsSkipped: this.isAutoEdit,
      title:
        this.editItems[firstIndex]?.firstname +
        " " +
        this.editItems[firstIndex]?.lastname,
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
      fetchCountryLanguages: "filterData/fetchCountryLanguages",
      fetchUsers: "users/fetchItems",
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
          this.userImage = this.editItems[index].image;
          this.user = this.editItems[index];
          this.title =
            this.editItems[index]?.firstname +
            " " +
            this.editItems[index]?.lastname;

          this.currentIndex = index;
        }
      } else if (actionType === "back") {
        const index = this.currentIndex - 1;
        if (index >= 0) {
          this.userImage = this.editItems[index].image;
          this.user = this.editItems[index];
          this.title =
            this.editItems[index]?.firstname +
            " " +
            this.editItems[index]?.lastname;
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

    async fetchStaticPermissionsData() {
      const teams = await this.fetchAllTeams();
      const selectedCompanies =
        this.$refs.filterCountryComponent.selectedCompanies;
      const companyIds = selectedCompanies.map((company) => company.id);
      await this.$refs.statisPermissions.fetchCompaniesSystems(companyIds);
      this.$refs.statisPermissions.setExtraData(teams, selectedCompanies);
    },

    // check validations for every steps
    async checkValidations(currentStep) {
      this.$refs.userUpdateForm.validate();
      switch (currentStep) {
        case 1:
          const isGeneralSectionValid =
            this.$refs.filterCountryComponent.checkValidation();
          if (isGeneralSectionValid) {
            this.isLoading = true;
            await this.fetchStaticPermissionsData();
            this.isLoading = false;
            this.$refs.editRef.nextForce();
            this.$refs.userUpdateForm.resetValidation();
          }
          break;
        case 2:
          this.isLoading = true;
          const isAccountSectionValid =
            await this.$refs.userStepOneRef.checkValidations();
          if (isAccountSectionValid) {
            await this.fetchAllCountries();
            this.isLoading = false;

            this.$refs.editRef.nextForce();
            this.$refs.userUpdateForm.resetValidation();

            await this.$refs.userStepTwoRef.onCountrySelected(
              this.$v.user.$model.country_id
            );

            await this.$refs.userStepTwoRef.onCurrentCountrySelected(
              this.$v.user.$model.current_country_id
            );
          }
          this.isLoading = false;

          break;
        case 3:
          const isPersonalValid =
            this.$refs.userStepTwoRef.checkPersonalValidation();
          if (isPersonalValid) {
            this.$refs.userUpdateForm.resetValidation();
            this.$refs.editRef.nextForce();
          }
          break;

        case 4:
          this.isLoading = true;
          this.isLoading = false;

          this.$refs.editRef.nextForce();
          this.$refs.userUpdateForm.resetValidation();

          break;
      }
    },

    async onChangeTo(step) {
      this.$refs.userUpdateForm.validate();

      switch (step) {
        case 1:
          this.isLoading = true;
          const isGeneralSectionValid =
            this.$refs.filterCountryComponent.checkValidation();
          if (isGeneralSectionValid) {
            this.$refs.editRef.setCurrent(step);
            this.$refs.userUpdateForm.resetValidation();
          } else {
            // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr("please_fill_all_fields_correctly"));

          }
          this.isLoading = false;
          break;
        case 2:
          this.isLoading = true;
          const isGeneralSectionValid2 =
            this.$refs.filterCountryComponent.checkValidation();
          if (isGeneralSectionValid2) {
            this.$refs.editRef.setCurrent(step);
            this.$refs.userUpdateForm.resetValidation();
          } else {
            // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr("please_fill_all_fields_correctly"));

          }
          this.isLoading = false;

          break;
        case 3:
          this.isLoading = true;
          const isAccountSectionValid2 =
            await this.$refs.userStepOneRef.checkValidations();
          const isGeneralSectionValid3 =
            this.$refs.filterCountryComponent.checkValidation();
          if (isAccountSectionValid2 && isGeneralSectionValid3) {
            await this.fetchAllCountries();
            this.isLoading = false;

            this.$refs.userUpdateForm.resetValidation();
            this.$refs.editRef.setCurrent(step);

            await this.$refs.userStepTwoRef.onCountrySelected(
              this.$v.user.$model.country_id
            );

            await this.$refs.userStepTwoRef.onCurrentCountrySelected(
              this.$v.user.$model.current_country_id
            );
          } else {
            // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr("please_fill_all_fields_correctly"));

          }
          this.isLoading = false;
          break;

        case 4:
          this.isLoading = true;
          const isPersonalValid =
            this.$refs.userStepTwoRef.checkPersonalValidation();
          const isAccountSectionValid3 =
            await this.$refs.userStepOneRef.checkValidations();
          const isGeneralSectionValid4 =
            this.$refs.filterCountryComponent.checkValidation();
          if (
            isPersonalValid &&
            isAccountSectionValid3 &&
            isGeneralSectionValid4
          ) {
            this.isLoading = true;
            await this.fetchStaticPermissionsData();
            this.isLoading = false;
            this.$refs.editRef.setCurrent(step);
            this.$refs.userUpdateForm.resetValidation();
          } else {
            // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr("please_fill_all_fields_correctly"));

          }
          this.isLoading = false;
          break;
        case 5:
          this.isLoading = true;
          const isPersonalValid2 =
            this.$refs.userStepTwoRef.checkPersonalValidation();
          const isAccountSectionValid4 =
            await this.$refs.userStepOneRef.checkValidations();
          const isGeneralSectionValid5 =
            this.$refs.filterCountryComponent.checkValidation();
          if (
            isPersonalValid2 &&
            isAccountSectionValid4 &&
            isGeneralSectionValid5
          ) {
            this.$refs.editRef.setCurrent(step);
            this.$refs.userUpdateForm.resetValidation();
          } else {
            // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr("please_fill_all_fields_correctly"));

          }
          this.isLoading = false;
          break;
      }
    },

    // submit update form
    async onSubmit(canNext = "canNext") {
      const isRemarkValid = this.$refs.userStepThreeRef.checkValidation();
      this.$v.user.$touch();
      this.$refs.userUpdateForm.validate();
      if (!this.$v.user.$invalid && isRemarkValid) {
        const userData = this.getUserData();
        const userFormData = Helper.getFormData(userData);
        await this.updateUser(userFormData, canNext);
      } else {
        // this.$toastr.w(this.$tr("please_fill_all_fields_correctly"));
					this.$toasterNA("orange",this.$tr("please_fill_all_fields_correctly"));

      }
    },

    // update user from server
    async updateUser(userData, canNext) {
      this.isSubmitting = true;
      try {
        const response = await this.$axios.post(
          `users/userId?_method=PUT`,
          userData
        );
        this.isSubmitting = false;
        if (response?.status === 200 && response?.data?.result) {
          if (canNext === "onSaveAndNext") {
            if (this.currentIndex < this.editItems.length) {
              Alert.successAlert(this, "successfully_updated");
              this.onItemChange("next");
            }
          } else if (canNext === "canNext") {
            this.$refs.editRef.nextForce();
          } else {
            Alert.successAlert(this, "successfully_updated");
          }

          this.$store.dispatch("filterData/clearCompanyDepartment");
          // await this.fetchUsers({ key: this.tabKey });
        } else {
          // this.$toastr.w(this.$tr("something_went_wrong"));
					this.$toasterNA("orange",this.$tr("something_went_wrong"));

        }
      } catch (error) {
        this.isSubmitting = false;
        HandleException.handleApiException(this, error);
      }
    },

    // fetch all teams
    async fetchAllTeams() {
      try {
        const departments =
          this.$refs.filterCountryComponent.selectedDepartments;
        const departmentsIds = departments.map(({ id }) => id);
        const url = `common/team/=${departmentsIds}`;
        const { data } = await this.$axios.get(url);
        return data;
      } catch (_) {
        return [];
      }
    },

    getUserData() {
      const {
        selectedDepartments,
        companyDepartmentsTeams,
        companiesSystems,
        selectedCompanies,
        selectedCountries,
        selectedSystems,
        selectedSubSystems,
        selectedRoles,
        selectedTeams,
        date_range,
        time_range,
        generate_password,
        email_password,
        change_password,
        current_country,
        country,
        language,
        roles,
        teams,
        companies,
        actualTimeRange,
        actualDateRange,
        state,
        ...user
      } = this.user;
      const userData = {
        ...user,
      };

      const permissions = this.$refs.statisPermissions.getData();
      permissions.isPermissionsSkipped = this.isPermissionsSkipped;

      return Object.assign(userData, permissions);
    },
  },

  validations: {
    user: UserValidations.validations,
  },
};
</script>

<style>
.main-Card {
  background-color: #f9fafd !important;
}

.main-Card .title {
  color: #b6c1d2 !important;
}

.main-Card .v-stepper .v-stepper__header .v-stepper__step__step {
  display: none !important;
}

.main-Card .v-stepper__step.v-stepper__step--active .v-stepper__label .v-btn {
  background-color: var(--v-primary-base);
  color: white;
}
</style>

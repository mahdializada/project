<template>
  <div>
    <CustomInput
      :label="$tr('label_required', $tr('name'))"
      placeholder="name"
      v-model="businessLocation.name.$model"
      :rules="rules.nameRule(businessLocation, $root)"
      type="textfield"
    />

    <CustomInput
      :label="$tr('label_required', $tr('email'))"
      placeholder="email"
      v-model="businessLocation.email.$model"
      :rules="rules.emailRule(businessLocation, $root)"
      type="textfield"
      :error-messages="emailErrorMessage"
    />

    <CustomInput
      :items="locationTypes"
      v-model="businessLocation.location_type.$model"
      :rules="rules.locationTypeRule(businessLocation, $root)"
      item-value="id"
      item-text="name"
      :label="$tr('label_required', $tr('location_type'))"
      :placeholder="$tr('choose_item',$tr('location_type'))"
      type="autocomplete"
    />

    <div class="d-flex flex-column flex-md-row">
      <div class="w-full">
        <CustomInput
          :items="allCountries"
          item-value="id"
          country
          item-text="name"
          v-model="businessLocation.country_id.$model"
          :rules="rules.countryRule(businessLocation, $root)"
          :label="$tr('label_required', $tr('country'))"
          :placeholder="$tr('choose_item',$tr('country'))"
          type="autocomplete"
          class="mb-0 me-md-2"
          @change="onCountrySelected"
        />
      </div>
      <div class="w-full">
        <CustomInput
          :items="selectedCountryStates"
          item-value="id"
          item-text="name"
          :loading="isFetching"
          v-model="businessLocation.state_id.$model"
          :rules="rules.countryStateRule(businessLocation, $root)"
          :label="$tr('label_required', $tr('city'))"
          :placeholder="$tr('choose_item',$tr('city'))"
          type="autocomplete"
          class="mb-0 ms-md-2"
        />
      </div>
    </div>

    <CustomInput
      label="map_link"
      placeholder="map_link"
      v-model="businessLocation.map_link.$model"
      :rules="rules.mapLinkRule(businessLocation, $root)"
      type="textfield"
    />

    <CustomInput
      v-model="businessLocation.address.$model"
      :rules="rules.addressRule(businessLocation, $root)"
      :label="$tr('label_required', $tr('address'))"
      placeholder="address"
      type="textarea"
      rows="4"
    />
  </div>
</template>

<script>
import CustomInput from "../../design/components/CustomInput";
import HandleException from "~/helpers/handle-exception";
import { mapGetters, mapActions } from "vuex";

export default {
  name: "BusinessLocationStepOne",
  components: { CustomInput },
  props: {
    businessLocation: {
      required: true,
      type: Object,
    },
    rules: {
      required: true,
      type: Object,
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      locationTypes: [
        { id: "head office", name: "Head Office" },
        { id: "branch", name: "Branch" },
        { id: "service branch", name: "Service Branch" },
        { id: "none physical location", name: "None Physical Location" },
      ],
      emailErrorMessage: "",
      isFetching: false,
      selectedCountryStates: [],
    };
  },

  computed: {
    ...mapGetters({
      allCountries: "countries/getAscCountries",
    }),
  },

  methods: {
    ...mapActions({
      fetchCompanies: "filterData/fetchCompaniesOfCountries",
    }),

    // check validations for first step in businessLocation validations
    async checkValidations() {
      this.businessLocation.$touch();
      const isNameInvalid = this.businessLocation.name.$invalid;
      const isEmailInvalid = this.businessLocation.email.$invalid;
      const isLocationTypeInvalid =
        this.businessLocation.location_type.$invalid;
      const isAddressInvalid = this.businessLocation.address.$invalid;
      const isCountryInvalid = this.businessLocation.country_id.$invalid;
      const isCountryStateInvalid = this.businessLocation.state_id.$invalid;

      let accountSectionHasError =
        isNameInvalid ||
        isEmailInvalid ||
        isLocationTypeInvalid ||
        isCountryInvalid ||
        isCountryStateInvalid ||
        isAddressInvalid;

      if (!accountSectionHasError) {
        accountSectionHasError = await this.checkColumns();
      }
      return !accountSectionHasError;
    },

    // check column uniqueness from server
    async checkColumns() {
      const uniqueNess = await this.checkUniqueNess();

      // check email address
      const isEmailAlreadyExists = uniqueNess.isEmailAlreadyExists;
      if (isEmailAlreadyExists) {
        this.emailErrorMessage = this.$tr("exists", [this.$tr("email")]);
      } else {
        this.emailErrorMessage = "";
      }

      return isEmailAlreadyExists;
    },

    // check for unique ness of some columns
    async checkUniqueNess() {
      try {
        const email = this.businessLocation?.email.$model;

        const columns = [{ columnName: "email", value: email }];

        let url = "business-locations/check-uniqueness";
        if (this.isEdit) {
          const businessLocationId = this.businessLocation.$model.id;
          url = `business-locations/check-uniqueness?businessLocationId=${businessLocationId}`;
        }
        const response = await this.$axios.post(url, columns);
        return {
          isEmailAlreadyExists: response?.data?.email,
        };
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },

    async onCountrySelected(countryId, isEdit = false) {
      this.isFetching = true;
      try {
        if (!isEdit) {
          this.businessLocation.company_id.$model = "";
          this.businessLocation.state_id.$model = "";
        }

        await this.fetchCompanies([countryId]);
        const response = await this.$axios.get(
          `states/country-states/${countryId}`
        );
        this.selectedCountryStates = response?.data;
        if (!isEdit) {
          this.businessLocation.state_id.$model = "";
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isFetching = false;
    },
  },
};
</script>

<style scoped></style>

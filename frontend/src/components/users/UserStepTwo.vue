<template>
  <div>
    <div class="d-flex profile">
      <div>
        <div
          class="avatar d-flex align-center me-4 justify-center rounded-circle"
          :style="`background-image: url(${profileImagePath});`"
        >
          <v-icon v-if="profileImagePath === ''">fa-user</v-icon>
        </div>
      </div>
      <div class="w-full">
        <v-file-input
          class="custom-file"
          outlined
          accept="image/jpeg,image/jpg,image/png"
          prepend-icon=""
          dense
          :rules="rules.imageRule(user, $root)"
          @change="validateUserProfile"
          @click:clear="clearUserProfile"
          :success="profileImagePath !== ''"
        >
          <template slot="prepend-inner">
            <div
              name="prepend-inner"
              style="max-width: 260px; text-align: center"
            >
              <p class="ma-0">
                <svg
                  width="30"
                  height="30"
                  viewBox="100 100 500 140"
                  fill="currentColor"
                  style="transform: translateY(4px)"
                >
                  <g>
                    <path
                      d="m479.71 198.78h-0.86328c-6.1133-51.496-50.938-91.512-102.99-91.512-42.559 0-81.152 26.367-96.625 65.824-2.7773-0.30469-5.5078-0.44141-8.168-0.44141-33.949 0-63.816 23.754-71.656 56.746-32.059 2.8945-56.934 29.938-56.934 62.348 0 34.512 28.094 62.625 62.625 62.625h119.63c4.293 0 7.7695-3.4766 7.7695-7.793 0-4.3164-3.4766-7.793-7.7695-7.793l-119.6-0.003906c-25.922 0-47.039-21.094-47.039-47.039 0-25.898 21.023-47.016 46.828-47.016l1.0039 0.023438c3.8281 0 7.1406-2.8477 7.7227-6.6484 4.0586-28.426 28.746-49.867 57.445-49.867 3.9883 0 7.957 0.42188 11.945 1.2383l1.4023 0.13672c3.4062 0 6.4648-2.2617 7.3984-5.4375 11.738-36.656 45.5-61.32 84.023-61.32 47.414 0 86.078 37.191 88.059 84.699 0.14063 2.0781 1.0977 4.0117 2.7305 5.4375 1.5391 1.3086 3.2422 1.6562 5.9258 1.8203 2.7305-0.30469 5.0859-0.46484 7.1641-0.46484 34.301 0 62.207 27.906 62.207 62.207s-27.906 62.207-62.207 62.207h-104.42c-4.293 0-7.7695 3.4766-7.7695 7.793s3.5 7.793 7.7695 7.793h104.42c42.887 0 77.793-34.883 77.793-77.793s-34.93-77.77-77.816-77.77z"
                    />
                    <path
                      d="m413.02 318.92c0.023437 0 0.023437 0 0 0 2.125 0 4.1289-0.81641 5.5312-2.2383 2.9883-3.0352 2.9883-7.957 0-11.012l-63.047-63.047c-2.9648-2.9414-8.0273-2.918-11.012 0.070312l-63.023 62.977c-1.4688 1.4688-2.2617 3.4297-2.2617 5.5078 0 2.1016 0.81641 4.0586 2.2383 5.4609 1.3984 1.4453 3.4062 2.2852 5.4844 2.3086h0.046875c2.1016 0 4.1055-0.81641 5.5312-2.2617l49.699-49.676v177.92c0 4.3164 3.5 7.793 7.7695 7.793 4.3164 0 7.8164-3.4766 7.8164-7.793v-177.92l49.652 49.652c1.4453 1.4375 3.4492 2.2578 5.5742 2.2578z"
                    />
                  </g>
                </svg>
                {{ $tr("upload_user_profile") }}
              </p>
              <div v-if="isFileSelected" class="ma-1">
                <p class="mb-0">
                  {{ $tr("name") }}: {{ user.image.$model.name }}
                </p>
                <p class="mb-0" style="margin-top: 5px">
                  {{ $tr("size") }}:
                  {{ (user.image.$model.size / 1024).toFixed(2) }} KB
                </p>
              </div>
            </div>
          </template>
        </v-file-input>
      </div>
    </div>
    <div class="w-full">
      <CustomInput
        :items="gender"
        v-model="user.gender.$model"
        :rules="rules.genderRule(user, $root)"
        item-value="id"
        item-text="name"
        :label="$tr('label_required', $tr('gender'))"
        :placeholder="$tr('choose_item', $tr('gender'))"
        type="autocomplete"
        class="mb-0"
      />

      <div>
        <p class="mb-1 custom-input-title">
          {{ $tr("label_required", $tr("birth_date")) }}
        </p>
        <v-menu
          v-model="showBirthDatePicker"
          transition="scale-transition"
          offset-y
          min-width="auto"
          :close-on-content-click="false"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              class="custom-input"
              v-model="user.birth_date.$model"
              :rules="rules.dobRule(user, $root)"
              :placeholder="$tr('birth_date_format')"
              v-bind="attrs"
              v-on="on"
              dense
              readonly
              outlined
            ></v-text-field>
          </template>
          <v-date-picker
            no-title
            :active-picker.sync="birthPickerType"
            :max="
              new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
                .toISOString()
                .substr(0, 10)
            "
            @input="showBirthDatePicker = false"
            v-model="user.birth_date.$model"
          >
          </v-date-picker>
        </v-menu>
      </div>

      <CustomInput
        :items="allCountries"
        item-value="id"
        country
        item-text="name"
        v-model="user.country_id.$model"
        :rules="rules.countryRule(user, $root)"
        :label="$tr('label_required', $tr('country'))"
        :placeholder="$tr('choose_item', $tr('country'))"
        type="autocomplete"
        class="mb-0"
        @change="onCountrySelected"
      />

      <CustomInput
        :items="countryLanguages"
        item-value="id"
        item-text="name"
        v-model="user.language_id.$model"
        :rules="rules.languageRule(user, $root)"
        :label="$tr('label_required', $tr('language'))"
        :placeholder="$tr('choose_item', $tr('language'))"
        type="autocomplete"
        class="mb-0"
      />

      <!--
      <CustomInput
        label="textField.nationality_required"
        placeholder="textField.nationality"
        type="textfield"
        disabled
        v-model="selectedCountry.national"
        class="mb-0"
      /> -->

      <div class="d-flex flex-column flex-md-row">
        <div class="w-full">
          <CustomInput
            :items="allCountries"
            item-value="id"
            country
            item-text="name"
            v-model="user.current_country_id.$model"
            :rules="rules.currentCountryRule(user, $root)"
            :label="$tr('label_required', $tr('current_country'))"
            :placeholder="$tr('choose_item', $tr('current_country'))"
            type="autocomplete"
            class="mb-0 me-md-2"
            @change="onCurrentCountrySelected"
          />
        </div>
        <div class="w-full">
          <CustomInput
            :items="selectedCountryStates"
            item-value="id"
            item-text="name"
            :loading="isFetchingStates"
            v-model="user.state_id.$model"
            :rules="rules.countryStateRule(user, $root)"
            :label="$tr('label_required', $tr('city'))"
            :placeholder="$tr('choose_item', $tr('city'))"
            type="autocomplete"
            class="mb-0 ms-md-2"
          />
        </div>
      </div>
      <CustomInput
        v-model="user.address.$model"
        :rules="rules.addressRule(user, $root)"
        :label="$tr('label_required', $tr('address'))"
        placeholder="address"
        type="textarea"
        class="mb-0"
        rows="4"
      />
    </div>
  </div>
</template>

<script>
import CustomInput from "../design/components/CustomInput";
import { mapActions, mapGetters } from "vuex";
import HandleException from "../../helpers/handle-exception";

export default {
  name: "UserStepTwo",
  components: { CustomInput },
  props: {
    user: {
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
      birthPickerType: null,
      showBirthDatePicker: false,
      gender: [
        { id: "male", name: this.$tr("male") },
        { id: "female", name: this.$tr("female") },
      ],

      // personal section
      selectedCountry: {},
      selectedCountryStates: [],
      isFetchingStates: false,
      profileImagePath: this.isEdit ? this.user.$model.image : "",
      isFileSelected: false,
    };
  },
  computed: {
    ...mapGetters({
      allCountries: "countries/getAscCountries",
      countryLanguages: "filterData/getCountryLanguages",
    }),
  },

  watch: {
    showBirthDatePicker(val) {
      val && setTimeout(() => (this.birthPickerType = "YEAR"));
    },
  },

  methods: {
    ...mapActions({
      fetchAllCountries: "countries/fetchAscCountries",
      fetchCountryLanguages: "filterData/fetchCountryLanguages",
    }),

    /** Personal Section */
    // check personal section validations
    checkPersonalValidation() {
      this.user.$touch();

      const imageInvalid = this.user?.image.$invalid;
      const genderInvalid = this.user?.gender.$invalid;
      const birthDateInvalid = this.user?.birth_date.$invalid;
      const countryInvalid = this.user?.country_id.$invalid;
      const currentCountryInvalid = this.user?.current_country_id.$invalid;
      const cityInvalid = this.user?.state_id.$invalid;
      const addressInvalid = this.user?.address.$invalid;
      const languageInValid = this.user?.language_id.$invalid;

      let isPersonalSectionHasError =
        imageInvalid ||
        genderInvalid ||
        birthDateInvalid ||
        countryInvalid ||
        currentCountryInvalid ||
        cityInvalid ||
        languageInValid ||
        addressInvalid;

      return !isPersonalSectionHasError;
    },

    // check & validate user profile image
    validateUserProfile(file) {
      if (file) {
        const fileExtension = file.type;
        const allowedExtensions = ["image/jpeg", "image/jpg", "image/png"];
        if (!allowedExtensions.includes(fileExtension)) {
          // this.$toastr.w(this.$tr("inappropriate_uploaded_file"));
					this.$toasterNA("orange",this.$tr("inappropriate_uploaded_file"));

          return;
        }
        this.isFileSelected = true;
        this.user.image.$model = file;
        this.profileImagePath = URL.createObjectURL(file);
      }
    },

    // take selected country
    onCountrySelected(countryId) {
      this.fetchCountryLanguages(countryId);
      this.selectedCountry = this.allCountries.find(
        (country) => country?.id === countryId
      );
    },

    // find all selected country states
    async onCurrentCountrySelected(countryId) {
      this.isFetchingStates = true;
      try {
        const response = await this.$axios.get(
          `states/country-states/${countryId}`
        );
        this.selectedCountryStates = response?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isFetchingStates = false;
    },

    // clear user profile image
    clearUserProfile() {
      this.user.image.$model = "";
      this.profileImagePath = "";
      this.isFileSelected = false;
    },

    // clear all fields
    clearItems() {
      this.profileImagePath = "";
      this.isFileSelected = false;
      this.selectedCountry = {};
      this.selectedCountryStates = [];
    },
  },
};
</script>

<style scoped></style>

<template>
  <v-form
    ref="companyForm"
    lazy-validation
    id="companyForm"
    @submit.prevent="onSubmit"
  >
    <AutoEdit
      :headers="headers"
      :autoEditData="autoEditData"
      :tableName="tableName"
      @getCurrent="setCurrent"
      :iconOrLogo="autoEditData[topCurrent].logo"
      :isIcon="false"
      @next="onNext"
      @prev="onPrev"
      ref="auto_edit"
      @changeTo="onChangeTo"
      @saveAandNext="onSaveAandNext"
      :if_save="save"
      :if_save_and_next="save_and_next"
      @save="onSave"
      :isLoading="isDataLoaded"
      :isSubmitting="submittingOrvalidating"
      hasLogo
    >
      <template v-slot:name1>
        <!-- <v-form ref="companyForm" lazy-validation id="companyForm"> -->
        <div class="topDiv">
          <div class="topTilte">
            <h1
              class="text-center text-h4 font-weight-bold border-bottom pa-1"
              style="
                color: #58595b;
                border-bottom: 2px solid #bfc8d7;
                text-transform: uppercase;
              "
            >
              {{ $tr("account") }}
            </h1>
          </div>
          <div class="countries my-2 mx-3">
            <div class="d-flex flex-column flex-md-row">
              <div class="w-full">
                <CustomInput
                  :items="countries"
                  v-model="selectedCountryId"
                  item-value="id"
                  item-text="name"
                  :label="$tr('label_required', $tr('country'))"
                  :placeholder="$tr('choose_item', $tr('country'))"
                  country
                  type="autocomplete"
                  class="me-md-4 mb-md-2 mb-0"
                  ref="countryInput"
                  :error-messages="countryErrorMessage"
                />
              </div>
              <div class="w-100">
                <FormBtn
                  title="add_plus"
                  class="mt-md-4 mb-2"
                  @click="addSelectedCountry"
                />
              </div>
            </div>
            <div class="selected d-flex flex-wrap">
              <SelectedItem
                v-for="selectedCountry in selectedCountries"
                :key="selectedCountry.id"
                @close="() => removeSelectedCountry(selectedCountry.id)"
                :title="selectedCountry.name"
                :flag="`${selectedCountry.iso2.toLowerCase()}`"
              />
            </div>
            <div class="d-flex flex-column flex-md-row">
              <div class="w-full">
                <CustomInput
                  :items="systems"
                  v-model="selectedSystemId"
                  item-value="id"
                  item-text="name"
                  :label="$tr('label_required', $tr('system'))"
                  :placeholder="$tr('choose_item', $tr('system'))"
                  type="autocomplete"
                  class="me-md-4 mb-md-2 mb-0"
                  has-logo
                  :error-messages="systemErrorMessage"
                />
              </div>
              <div class="w-100">
                <FormBtn
                  title="add_plus"
                  class="mt-md-4 mb-2"
                  @click="addSelectedSystems"
                />
              </div>
            </div>
            <div class="selected d-flex flex-wrap">
              <SelectedItem
                v-for="selectedSystem in selectedSystems"
                :key="selectedSystem.id"
                @close="() => removeSelectedSystem(selectedSystem.id)"
                :title="selectedSystem.name"
                :logo-url="selectedSystem.logo"
              />
            </div>
            <CustomInput
              :items="investment_types"
              item-value="id"
              item-text="name"
              :label="$tr('label_required', $tr('investment_type'))"
              :placeholder="$tr('choose_item', $tr('investment_type'))"
              type="autocomplete"
              class="mt-2"
              v-model="$v.project.investment_type.$model"
              :rules="CompanyRules.investmentTypeRule($v.project, $root)"
            />
          </div>
        </div>
        <!-- </v-form> -->
      </template>
      <template v-slot:name2>
        <div class="pb-3 topDiv">
          <div class="topTitle">
            <h1
              class="text-center text-h4 font-weight-bold border-bottom pb-2 pt-1 text-uppercase"
              style="color: #58595b"
            >
              {{ $tr("project") }}
            </h1>
          </div>
          <div class="d-flex flex-column flex-md-row mt-3 mb-0 pb-0 mx-3">
            <div class="w-full">
              <CustomInput
                :invalid="$v.project.name.$invalid"
                v-model.trim="$v.project.name.$model"
                :rules="CompanyRules.nameRule($v.project, $root, 'name')"
                :label="$tr('name')"
                :placeholder="$tr('name')"
                type="textfield"
                class="me-md-4 mb-md-2 mb-0"
                name="name"
                :error-messages="nameErrorMessage"
              />
            </div>
          </div>
          <div class="d-flex flex-column flex-md-row mt-0 pt-0 mx-3">
            <div class="w-full">
              <CustomInput
                :invalid="$v.project.email.$invalid"
                v-model.trim="$v.project.email.$model"
                :rules="CompanyRules.emailRule($v.project, $root, 'email')"
                :label="$tr('email')"
                :placeholder="$tr('email')"
                type="textfield"
                class="me-md-4 mb-md-2 mb-0"
                :error-messages="emailErrorMessage"
              />
            </div>
          </div>
          <div class="d-flex profile mt-2 mx-3">
            <div>
              <div
                class="avatar d-flex align-center me-4 justify-center rounded-circle"
                :style="`background-image: url(${filePath});`"
              >
                <v-icon v-if="filePath === ''">fa-user</v-icon>
              </div>
            </div>
            <div class="w-full">
              <v-file-input
                class="custom-file"
                outlined
                accept="image/jpeg,image/jpg,image/png"
                prepend-icon=""
                dense
                :rules="CompanyRules.logoRule($v.project, $root, 'logo')"
                @change="validateUserProfile"
                @click:clear="clearUserProfile"
                :success="filePath !== ''"
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
                    </p>
                    <div v-if="isFileSelected" class="ma-1">
                      <p class="mb-0">
                        {{ $tr("name") }}: {{ $v.project.logo.$model.name }}
                      </p>
                      <p class="mb-0" style="margin-top: 5px">
                        {{ $tr("size") }}:
                        {{ ($v.project.logo.$model.size / 1024).toFixed(2) }} KB
                      </p>
                    </div>
                  </div>
                </template>
              </v-file-input>
            </div>
          </div>
        </div>
      </template>
      <template v-slot:name3>
        <div class="topDiv">
          <div class="topTitle">
            <h1
              class="text-center text-h4 font-weight-bold border-bottom pa-1"
              style="
                color: #58595b;
                border-bottom: 2px solid #bfc8d7;
                text-transform: uppercase;
              "
            >
              {{ $tr("social_media") }}
            </h1>
          </div>
          <div class="d-flex flex-column flex-md-row mx-3 mt-3">
            <div class="w-full">
              <CustomInput
                :items="social_media"
                v-model="selectedMedias"
                item-value="id"
                item-text="name"
                :label="$tr('social_media')"
                :placeholder="$tr('choose_item', $tr('social_media'))"
                hasLogo
                logoName=""
                multiple
                type="autocomplete"
                class="me-md-4 mb-md-2 mb-0"
              />
            </div>
            <div class="w-100">
              <FormBtn
                title="add_plus"
                class="mt-md-4 mb-2"
                @click="addSelectedMedia"
              />
            </div>
          </div>
          <v-row v-if="selectedSocialMedias.length > 0" class="mx-1">
            <v-col
              cols="12 py-0"
              sm="6"
              v-for="(media, id) in $v.selectedSocialMedias.$each.$iter"
              :key="id"
            >
              <div class="w-full">
                <div class="d-flex justify-space-between align-center">
                  <div class="d-flex align-center">
                    <v-avatar size="40">
                      <img :src="media.logo.$model" alt="" />
                    </v-avatar>
                    <h4 class="ps-1">{{ $tr(media.name.$model) }}</h4>
                  </div>
                  <CloseBtn @click="closeSocialMedia(media.id.$model)" />
                </div>
                <CustomInput
                  class="pt-1"
                  type="textfield"
                  :placeholder="
                    media.sample_url_account.$model
                      ? media.sample_url_account.$model
                      : $tr('add_your_account_url')
                  "
                  v-model="media.url.$model"
                  :rules="
                    SocialMediaRules.socialMediaRule(
                      media,
                      $root,
                      $tr(media.name.$model)
                    )
                  "
                />
              </div>
            </v-col>
          </v-row>
        </div>
      </template>
      <template v-slot:name4>
        <div class="topDiv">
          <div class="topTitle">
            <h1
              class="text-center text-h4 font-weight-bold border-bottom pa-1"
              style="
                color: #58595b;
                border-bottom: 2px solid #bfc8d7;
                text-transform: uppercase;
              "
            >
              {{ $tr("remarks") }}
            </h1>
          </div>
          <div class="mx-4">
            <CustomInput
              :label="$tr('label_required', $tr('note'))"
              placeholder="note"
              type="textarea"
              v-model="$v.project.note.$model"
              :rules="CompanyRules.noteRule($v.project, $root)"
            />
          </div>
        </div>
      </template>
    </AutoEdit>
  </v-form>
</template>

<script>
import CloseBtn from "~/components/design/Dialog/CloseBtn";
import AutoEdit from "~/components/design/AutoEdit";
import FormBtn from "../design/components/FormBtn";
import SelectedItem from "../design/components/SelectedItem";
import CustomInput from "../design/components/CustomInput";
import ProjectValidation from "~/validations/project-validations";
import { mapActions, mapGetters } from "vuex";
import Alert from "../../helpers/alert";
import Rules from "~/validations/rules-new.js";
import Validations from "~/validations/validations";
import HandleException from "../../helpers/handle-exception";

export default {
  components: {
    CloseBtn,
    AutoEdit,
    FormBtn,
    SelectedItem,
    CustomInput,
  },
  props: {
    autoEditData: {
      require: true,
      type: Array,
    },
    countries: {
      require: true,
    },
    systems: {
      require: true,
    },
  },
  data() {
    return {
      save: true,
      save_and_next: true,
      CompanyRules: ProjectValidation.rules,
      formKey: 0,
      investment_types: [
        { id: "Main Company", name: this.$tr("main_company") },
        { id: "Other", name: this.$tr("other") },
      ],
      project: {
        id: this.autoEditData[0].id,
        name: this.autoEditData[0].name,
        investment_type: this.autoEditData[0].investment_type,
        email: this.autoEditData[0].email,
        logo: {},
        note: this.autoEditData[0].note,
      },
      temFile: "",
      stepperStep: 1,
      current: 1,
      checkbox1: true,
      back: false,
      topIcon: "mdi-account",
      tableName: this.$tr("company"),
      topCurrent: 0,
      isNameDataLoaded: false,
      headers: [
        {
          icon: "fa-info",
          title: "general",
          slotName: "name1",
        },
        {
          icon: "fa-user",
          title: "project",
          slotName: "name2",
        },
        {
          icon: "fa-icons",
          title: "social_media",
          slotName: "name3",
        },
        {
          icon: "fa-user-shield",
          title: "remarks",
          slotName: "name4",
        },
      ],
      filePath: this.autoEditData[0].logo,
      selectedCountries: this._.clone(this.autoEditData[0].countries),
      selectedCountryId: "",
      countryErrorMessage: "",
      isLoading: false,
      isNameDublicate: false,

      selectedSystems: this._.clone(this.autoEditData[0].systems),
      selectedSystemId: "",
      systemErrorMessage: "",

      isEmailDataLoaded: false,
      isEmailDublicate: false,
      isFileSelected: false,
      isDataLoaded: false,
      submittingOrvalidating: false,
      selectedMedias: [],
      selectedSocialMedias: this._.clone(this.autoEditData[0].social_media),
      SocialMediaRules: Rules,
      nameErrorMessage: "",
      emailErrorMessage: "",
    };
  },
  validations: {
    project: ProjectValidation.validations.company,
    selectedSocialMedias: {
      $each: {
        id: {},
        social_media_id: {},
        name: {},
        logo: {},
        sample_url_account: {},
        url: Validations.urlValidationRequired,
      },
    },
  },
  async beforeMount() {
    if (this.social_media.length == 0) {
      await this.fetchSocialMedia();
    }
    if (process.client) {
      this.selectedSocialMedias = this._.clone(
        this.autoEditData[this.topCurrent].social_media
      );
    }
    this.setSelectedSocialMedias();
  },

  computed: {
    ...mapGetters({
      social_media: "social_media/getAllItems",
    }),
  },
  methods: {
    ...mapActions({
      fetchSocialMedia: "social_media/fetchAllSocialMedia",
      updateItem: "companies/updateItem",
    }),
    async checkNameUniqueness() {
      try {
        this.$v.project.$touch();
        const name = this.$v.project?.name.$model;
        const id = this.project?.id;
        const columns = { name: name };
        let url = `companies/check-name-uniqueness-on-crud?id=${id}`;
        const response = await this.$axios.post(url, columns);
        return {
          isNameAlreadyExists: response?.data?.name,
        };
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
    async checkEmailUniqueness() {
      try {
        this.$v.project.$touch();
        const email = this.$v.project?.email.$model;
        const id = this.project?.id;
        const columns = { email: email };
        let url = `companies/check-email-uniqueness-on-crud?id=${id}`;
        const response = await this.$axios.post(url, columns);
        return {
          isEmailAlreadyExists: response?.data?.email,
        };
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
    addSelectedMedia() {
      this.selectedMedias.forEach((element) => {
        let media = this.social_media?.find((media) => media?.id === element);
        if (
          this.selectedSocialMedias?.some(
            (mediaItem) => mediaItem?.social_media_id === media?.id
          )
        ) {
          return;
        }
        media = { ...media, url: "", social_media_id: media.id };
        this.selectedSocialMedias?.push(media);
      });
      this.selectedMedias = "";
    },
    setSelectedSocialMedias() {
      this.selectedSocialMedias = this.selectedSocialMedias.map((element) => {
        let data = {
          id: element.pivot.id,
          social_media_id: element.id,
          name: element.name,
          logo: element.logo,
          sample_url_account: element.sample_url_account,
          url: element.pivot.url,
        };
        return data;
      });
    },
    closeSocialMedia(id) {
      this.$refs.companyForm.resetValidation();
      this.selectedSocialMedias = this.selectedSocialMedias.filter(
        (element) => element.id !== id
      );
      this.$refs.companyForm.validate();
    },
    profileChange(file) {
      if (file) {
        const reader = new FileReader();
        reader.addEventListener(
          "load",
          (e) => (this.filePath = e.target.result)
        );
        reader.addEventListener("error", (e) => (this.filePath = ""));
        reader.readAsDataURL(file);
        this.temFile = file;
      }
    },
    setCurrent(topCurrent) {
      if (topCurrent < this.autoEditData.length) {
        this.countryErrorMessage = "";
        this.topCurrent = topCurrent;
        const data = this.autoEditData[topCurrent];
        for (const [key, value] of Object.entries(this.project)) {
          this.project[key] = data[key];
        }
        this.filePath = this.autoEditData[topCurrent].logo;
        this.selectedCountries = this.autoEditData[topCurrent].countries;
        this.selectedSystems = this.autoEditData[topCurrent].systems;
        this.selectedSocialMedias = this.autoEditData[topCurrent].social_media;
        this.setSelectedSocialMedias();
        return true;
      } else {
        this.countryErrorMessage = "";
        const data = this.autoEditData[this.topCurrent];
        for (const [key, value] of Object.entries(this.project)) {
          this.project[key] = data[key];
        }
        this.filePath = this.autoEditData[this.topCurrent].logo;
        this.selectedCountries = this.autoEditData[this.topCurrent].countries;
        this.selectedSystems = this.autoEditData[this.topCurrent].systems;
        this.selectedSocialMedias =
          this.autoEditData[this.topCurrent].social_media;
        this.setSelectedSocialMedias();

        return false;
      }
    },
    isCountryAlreadySelected() {
      return this.selectedCountries.find(
        (c) => c.id == this.project.country_id
      );
    },
    addSelectedCountry() {
      if (process.client) {
        this.selectedCountries = this._.clone(this.selectedCountries);
      }
      const requiredText = this.$tr("item_is_required", this.$tr("country"));
      if (this.selectedCountryId) {
        const country = this.countries?.find(
          (country) => country?.id === this.selectedCountryId
        );
        if (
          this.selectedCountries?.some(
            (countryItem) => countryItem?.id === country?.id
          )
        ) {
          return;
        }
        this.selectedCountries?.unshift(country);
        this.selectedCountryId = "";
        this.selectedCountries?.length < 1
          ? (this.countryErrorMessage = requiredText)
          : (this.countryErrorMessage = "");
      } else {
        this.countryErrorMessage = requiredText;
      }
    },
    addSelectedSystems() {
      if (process.client) {
        this.selectedSystems = this._.clone(this.selectedSystems);
      }
      // this.selectedCountries = []
      const requiredText = this.$tr("item_is_required", this.$tr("system"));
      if (this.selectedSystemId) {
        const system = this.systems?.find(
          (system) => system?.id === this.selectedSystemId
        );
        if (
          this.selectedSystems?.some(
            (systemItem) => systemItem?.id === system?.id
          )
        ) {
          return;
        }
        this.selectedSystems?.unshift(system);
        this.selectedSystemId = "";
        this.selectedSystems?.length < 1
          ? (this.systemErrorMessage = requiredText)
          : (this.systemErrorMessage = "");
      } else {
        this.systemErrorMessage = requiredText;
      }
    },
    // remove selected Countries
    removeSelectedSystem(systemId) {
      this.selectedSystems = this.selectedSystems?.filter(
        (system) => system?.id !== systemId
      );

      const requiredText = this.$tr("item_is_required", this.$tr("system"));
      this.selectedSystems?.length < 1
        ? (this.systemErrorMessage = requiredText)
        : (this.systemErrorMessage = "");
    },
    validateForm() {
      this.$v.project.$touch();
    },

    async onSave() {
      this.isDataLoaded = true;
      this.submittingOrvalidating = true;
      this.project.country_ids = this.selectedCountries.map(
        (country) => country.id
      );
      this.project.system_ids = this.selectedSystems.map((system) => system.id);
      this.project.socialMedias = this.selectedSocialMedias.map(
        (socialMedia) => {
          const data = {
            id: socialMedia.social_media_id,
            url: socialMedia.url,
          };
          return data;
        }
      );
      if (!this.checkValidations() && this.project.country_ids.length > 0) {
        if (this.temFile) {
          const base64Logo = await this.getBase64(this.temFile);
          this.project.logo = base64Logo;
        } else {
          this.project.logo = "";
        }
        const data = this.project;
        this.$emit("toggleProgressBar");
        const response = await this.$axios.put(
          `/companies/${this.project.id}`,
          data
        );
        this.$emit("toggleProgressBar");
        if (response?.status == 202) {
          this.isDataLoaded = false;
          this.submittingOrvalidating = false;
          // Alert.successAlert(this, this.$tr("successfully_updated"));
				  this.$toasterNA("green", this.$tr("successfully_updated"));

          const data = response?.data?.data;
          this.updateItem(data);
          this.autoEditData[this.topCurrent] = data;
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
				  this.$toasterNA("red", this.$tr("something_went_wrong"));
          this.isDataLoaded = false;
          this.submittingOrvalidating = false;
        }
      } else {
        this.isDataLoaded = false;
        this.submittingOrvalidating = false;

        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
        this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));

      }
    },
    async isStepTwoDataValid() {
      let isStepTwoDataValid =
        this.$v.project.name.$invalid ||
        this.$v.project.email.$invalid ||
        this.filePath == "";
      if (!isStepTwoDataValid) {
        this.isDataLoaded = true;
        const res = await this.checkNameUniqueness();
        const res2 = await this.checkEmailUniqueness();
        this.isDataLoaded = false;
        isStepTwoDataValid =
          res.isNameAlreadyExists ||
          res2.isEmailAlreadyExists ||
          this.filePath == "";
        if (res.isNameAlreadyExists) {
          this.nameErrorMessage = this.$tr(
            "item_already_exists",
            this.$tr("company")
          );
        } else {
          this.nameErrorMessage = "";
        }
        if (res2.isEmailAlreadyExists) {
          this.emailErrorMessage = this.$tr(
            "item_already_exists",
            this.$tr("email")
          );
        } else {
          this.emailErrorMessage = "";
        }
      }
      return !isStepTwoDataValid;
    },
    isStepOneDataValid() {
      let isStepOneDataValid =
        this.selectedCountries.length > 0 &&
        this.selectedSystems.length > 0 &&
        !this.$v.project.investment_type.$invalid;
      return isStepOneDataValid === true;
    },
    isStepThreeDataValid() {
      let isStepThreeDataValid = !this.$v.project.note.$invalid;
      return isStepThreeDataValid === true;
    },
    profileClear() {
      this.filePath = "";
    },
    async onNext() {
      const isStepTwoDataValid = await this.isStepTwoDataValid();
      if (
        !this.isStepOneDataValid() ||
        !isStepTwoDataValid ||
        this.$v.selectedSocialMedias.$invalid ||
        !this.isStepThreeDataValid()
      ) {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
        this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));

        this.validateForm();
        return;
      } else {
        this.$refs.auto_edit.nextPageResult();
      }
    },
    async onPrev() {
      const isStepTwoDataValid = await this.isStepTwoDataValid();
      if (
        !this.isStepOneDataValid() ||
        !isStepTwoDataValid ||
        this.$v.selectedSocialMedias.$invalid ||
        !this.isStepThreeDataValid()
      ) {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
        this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));

        this.validateForm();
        return;
      } else {
        this.$refs.auto_edit.prevPageResult();
      }
    },
    async onChangeTo(step) {
      const isStepTwoDataValid = await this.isStepTwoDataValid();
      if (
        !this.isStepOneDataValid() ||
        !isStepTwoDataValid ||
        this.$v.selectedSocialMedias.$invalid ||
        !this.isStepThreeDataValid()
      ) {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
        this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));

        this.validateForm();
        return;
      } else {
        this.$refs.auto_edit.changeToResult(step);
      }
    },

    removeSelectedCountry(countryId) {
      this.selectedCountries = this.selectedCountries?.filter(
        (country) => country?.id !== countryId
      );

      const requiredText = this.$tr("item_is_required", this.$tr("country"));

      this.selectedCountries?.length < 1
        ? (this.countryErrorMessage = requiredText)
        : (this.countryErrorMessage = "");
    },
    async getBase64(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = () => reject(error);
      });
    },
    checkValidations() {
      return (
        this.$v.project.name.$invalid ||
        this.$v.project.investment_type.$invalid ||
        this.$v.project.email.$invalid ||
        this.$v.project.note.$invalid ||
        this.$v.selectedSocialMedias.$invalid ||
        this.filePath == ""
      );
    },
    onSaveAandNext(next_page_index) {
      this.onSubmit(next_page_index);
    },
    async onSubmit(next_page_index) {
      try {
        this.isDataLoaded = true;
        this.submittingOrvalidating = true;
        this.project.country_ids = this.selectedCountries.map(
          (country) => country.id
        );
        this.project.system_ids = this.selectedSystems.map(
          (system) => system.id
        );
        this.project.socialMedias = this.selectedSocialMedias.map(
          (socialMedia) => {
            const data = {
              id: socialMedia.social_media_id,
              url: socialMedia.url,
            };
            return data;
          }
        );
        if (
          !this.checkValidations() &&
          this.project.country_ids.length > 0 &&
          this.project.system_ids.length > 0
        ) {
          if (this.temFile) {
            const base64Logo = await this.getBase64(this.temFile);
            this.project.logo = base64Logo;
          } else {
            this.project.logo = "";
          }
          const data = this.project;
          this.$emit("toggleProgressBar");
          const response = await this.$axios.put(
            `/companies/${this.project.id}`,
            data
          );
          this.$emit("toggleProgressBar");
          if (response?.status == 202) {
            // Alert.successAlert(this, this.$tr("successfully_updated"));
            this.$toasterNA("green",this.$tr('successfully_updated'));
            this.submittingOrvalidating = false;
            this.isDataLoaded = false;
            const data = response?.data?.data;
            this.updateItem(data);
            this.autoEditData[this.topCurrent] = data;
            this.setCurrent(next_page_index + 1);
            if (this.setCurrent(next_page_index + 1)) {
              this.$refs.auto_edit.saveAndNextResult();
            }
          } else {
            // this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));

            this.submittingOrvalidating = false;
            this.isDataLoaded = false;
          }
        } else {
          this.submittingOrvalidating = false;
          this.isDataLoaded = false;
          // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
        this.$toasterNA("red", this.$tr("please_fill_all_fields_correctly"));

        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));

        // this.$toasterNA("red", this.$tr('something_went_wrong'));
      }
    },
    // check & validate user profile image
    validateUserProfile(file) {
      if (file) {
        const fileExtension = file.type;
        const allowedExtensions = ["image/jpeg", "image/jpg", "image/png"];
        if (!allowedExtensions.includes(fileExtension)) {
          // this.$toastr.w(this.$tr("invalid_extension", this.$tr("image")));
          this.$toasterNA("orange", this.$tr("invalid_extension", this.$tr("image")));
          return;
        }
        this.isFileSelected = true;
        this.$v.project.logo.$model = file;
        this.filePath = URL.createObjectURL(file);
        this.temFile = file;
      }
    },

    // clear user profile image
    clearUserProfile() {
      this.$v.project.logo.$model = "";
      this.filePath = "";
      this.isFileSelected = false;
    },
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
.custom-logo {
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  background: #edf2f9;
}
.custom-logo .v-file-input__text {
  height: 110px;
  opacity: 0;
}
.custom-logo .v-input__slot fieldset {
  border-style: dashed !important;
}
.custom-logo .v-input__slot {
  min-height: 200px !important;
  display: flex;
  justify-content: center;
  align-items: center;
}
.upload-first-p {
  font-size: 18px;
  color: var(--upload-input-first-p);
  letter-spacing: 0.5px;
}
.upload-second-p {
  color: var(--input-border-color);
  line-height: 1.5;
}
.custom-logo .v-input__prepend-inner {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
}
.topDiv {
  position: relative;
}
.topTitle {
  top: 0px;
  position: sticky;
  z-index: 2;
  background-color: white;
}
.dublicate-name {
  top: -34% !important;
  padding: 0rem !important;
  position: relative !important;
  color: red;
}

.avatar {
  height: 110px;
  width: 110px;
  background-color: var(--gray-cyan);
  border: 2px solid var(--gray-cyan);
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
}

.avatar .v-icon {
  color: var(--color-white);
  font-size: 60px;
}

.custom-file .v-file-input__text {
  height: 110px;
  opacity: 0;
}

.custom-file .v-input__slot fieldset {
  border-style: dashed !important;
}

.custom-file .v-input__slot {
  display: flex;
  justify-content: center;
  align-items: center;
}

.upload-first-p {
  font-size: 18px;
  color: var(--upload-input-first-p);
  letter-spacing: 0.5px;
}

.upload-second-p {
  color: var(--input-border-color);
  line-height: 1.5;
}

.custom-file .v-input__prepend-inner {
  position: absolute;
}
</style>

<template>
  <v-form
    ref="socialMedia"
    lazy-validation
    id="socialMedia"
    @submit.prevent="onSave"
  >
    <AutoEdit
      hasLogo
      :headers="steppers"
      :autoEditData="autoEditData"
      :tableName="tableName"
      @getCurrent="setCurrent"
      :iconOrLogo="autoEditData[topCurrent].logo"
      :isIcon="false"
      @next="onNext"
      @prev="onPrev"
      ref="auto_edit"
      @changeTo="onChangeTo"
      @saveAandNext="onSaveAndNext"
      :if_save="save"
      :if_save_and_next="save_and_next"
      :loading="isLoading"
      @save="onSave"
      :isLoading="isLoading"
      :isSubmitting="submittingOrvalidating"
    >
      <template v-slot:step1>
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
              {{ $tr("social_media") }}
            </h1>
          </div>
          <div class="w-full mt-2 px-3">
            <CustomInput
              type="textfield"
              :label="$tr('item_name_required', $tr('social_media'))"
              :placeholder="$tr('item_name', $tr('social_media'))"
              v-model="$v.social_media.name.$model"
              :rules="rules.nameRule($v.social_media, $root)"
            />
          </div>
          <div class="w-full pt-1 px-3">
            <div class="d-flex profile mt-2">
              <div>
                <div
                  class="
                    avatar
                    d-flex
                    align-center
                    me-4
                    justify-center
                    rounded-circle
                  "
                  :style="`background-image: url(${filePath});`"
                >
                  <v-icon v-if="filePath === ''">fa-icons</v-icon>
                </div>
              </div>
              <div class="w-full">
                <v-file-input
                  class="custom-file"
                  outlined
                  accept="image/*"
                  prepend-icon=""
                  dense
                  :rules="rules.logoRule($v.social_media, $root)"
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

                        {{ $tr("upload_logo", $tr("social_media")) }}
                      </p>
                      <div v-if="isFileSelected" class="ma-1">
                        <p class="mb-0">
                          name: {{ $v.social_media.logo.$model.name }}
                        </p>
                        <p class="mb-0" style="margin-top: 5px">
                          size:
                          {{
                            ($v.social_media.logo.$model.size / 1024).toFixed(2)
                          }}
                          KB
                        </p>
                      </div>
                    </div>
                  </template>
                </v-file-input>
              </div>
            </div>
          </div>
          <div class="w-full mt-1 px-3">
            <CustomInput
              type="textfield"
              :label="$tr('label_required', $tr('website'))"
              :placeholder="$tr('item_url', $tr('website'))"
              v-model="$v.social_media.website.$model"
              :rules="rules.websiteRule($v.social_media, $root, $tr('website'))"
            />
          </div>
          <div class="w-full mt-1 px-3">
            <CustomInput
              type="textfield"
              :label="$tr('sample_url_account')"
              :placeholder="$tr('sample_url_account')"
              v-model="$v.social_media.sample_url_account.$model"
              :rules="rules.sampleAccountUrlRule($v.social_media, $root)"
            />
          </div>
        </div>
      </template>

      <template v-slot:step2>
        <DoneMessage
          :title="$tr('item_all_set', $tr('social_media'))"
          :subTitle="$tr('you_can_access_your_item', $tr('social_media'))"
        />
      </template>
    </AutoEdit>
  </v-form>
</template>

<script>
import { mapActions } from "vuex";
import CustomStepper from "../../design/FormStepper/CustomStepper";
import HandleException from "~/helpers/handle-exception";
import CustomInput from "../../design/components/CustomInput.vue";
import FormBtn from "../../design/components/FormBtn.vue";
import SelectedItem from "../../design/components/SelectedItem";
import DoneMessage from "../../design/components/DoneMessage.vue";
import Rules from "~/validations/rules-new.js";
import Validations from "~/validations/validations";
import Helpers from "../../../helpers/helpers";
import AutoEdit from "~/components/design/AutoEdit";
import Alert from "../../../helpers/alert";

export default {
  name: "EditSocialMedia",
  components: {
    AutoEdit,
    CustomInput,
    FormBtn,
    SelectedItem,
    CustomStepper,
    DoneMessage,
  },
  props: {
    autoEditData: {
      require: true,
      type: Array,
    },
  },

  data() {
    return {
      indexKey: 0,
      isLoading: false,
      steppers: [{ icon: "fa-lock", title: "general", slotName: "step1" }],
      rules: Rules,
      social_media: {
        id: this.autoEditData[0].id,
        name: this.autoEditData[0].name,
        logo: this.autoEditData[0].logo,
        website: this.autoEditData[0].website,
        sample_url_account: this.autoEditData[0]?.sample_url_account,
      },
      topIcon: "fa-icons",
      tableName: this.$tr("social_media"),
      filePath: this._.clone(this.autoEditData[0].logo),
      isFileSelected: false,
      topCurrent: 0,
      back: false,
      stepperStep: 1,
      current: 1,
      save: true,
      save_and_next: true,
      submittingOrvalidating: false,
    };
  },

  validations: {
    social_media: {
      name: Validations.name100Validation,
      logo: Validations.requiredValidation,
      website: Validations.urlValidationRequired,
      sample_url_account: Validations.urlValidation,
    },
  },
  methods: {
    ...mapActions({
      updateItem: "social_media/updateItem",
    }),

    // check & validate user profile image
    validateUserProfile(file) {
      if (file) {
        this.isFileSelected = true;
        this.$v.social_media.logo.$model = file;
        this.filePath = URL.createObjectURL(file);
      }
    },
    setCurrent(topCurrent) {
      if (topCurrent < this.autoEditData.length) {
        this.topCurrent = topCurrent;
        const data = this.autoEditData[topCurrent];
        for (const [key, value] of Object.entries(this.social_media)) {
          this.social_media[key] = data[key];
        }
        this.filePath = this.autoEditData[topCurrent].logo;
        return true;
      } else {
        const data = this.autoEditData[this.topCurrent];
        for (const [key, value] of Object.entries(this.social_media)) {
          this.social_media[key] = data[key];
        }
        this.filePath = this.autoEditData[this.topCurrent].logo;
        return false;
      }
    },
    onChangeTo(step) {
      return;
    },
    onNext() {
      if (this.$v.social_media.$invalid) {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

        please_fill_all_fields_correctly
        this.$v.social_media.$touch();
        return;
      } else {
        this.$refs.auto_edit.nextPageResult();
      }
    },
    onPrev() {
      if (this.$v.social_media.$invalid) {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

        
        this.$touch();
        return;
      } else {
        this.$refs.auto_edit.prevPageResult();
      }
    },
    // clear user profile image
    clearUserProfile() {
      this.$v.social_media.logo.$model = "";
      this.filePath = "";
      this.isFileSelected = false;
    },
    onSaveAndNext(next_page_index) {
      this.onSubmit(next_page_index);
    },
    async onSubmit(next_page_index) {
      this.$refs.socialMedia.validate();
      this.$v.social_media.$touch();
      if (!this.$v.social_media.$invalid) {
        this.isLoading = true;
        this.submittingOrvalidating = true;
        await this.updateRecordAndNext(next_page_index);
        this.isLoading = false;
        this.submittingOrvalidating = false;
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },
    async updateRecordAndNext(next_page_index) {
      try {
        let formData = Helpers.getFormData(this.social_media);
        const response = await this.$axios.post(
          "social_media/social_mediaId?_method=PUT",
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
        if (response?.status === 201 && response?.data.result) {
          const data = response?.data?.data;
          this.updateItem(data);
          Alert.successAlert(this, this.$tr("successfully_updated"));
          this.autoEditData[this.topCurrent] = data;
          this.setCurrent(next_page_index + 1);
          if (this.setCurrent(next_page_index + 1)) {
            this.$refs.auto_edit.saveAndNextResult();
          }
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    async onSave() {
      this.$refs.socialMedia.validate();
      this.$v.social_media.$touch();
      if (!this.$v.social_media.$invalid) {
        this.isLoading = true;
        this.submittingOrvalidating = true;
        await this.updateRecord();
        this.isLoading = false;
        this.submittingOrvalidating = false;
      } else {
        // this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));
				this.$toasterNA("red", this.$tr('please_fill_all_fields_correctly'));

      }
    },

    async updateRecord() {
      try {
        let formData = Helpers.getFormData(this.social_media);
        const response = await this.$axios.post(
          "social_media/social_mediaId?_method=PUT",
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
        if (response?.status === 201 && response?.data.result) {
          const data = response?.data?.data;
          this.autoEditData[this.topCurrent] = data;
          this.updateItem(data);
          Alert.successAlert(this, this.$tr("successfully_updated"));
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    resetForm() {
      this.social_media = {
        name: "",
        logo: "",
        website: "",
        sample_url_account: "",
      };
      this.filePath = "";
      this.isFileSelected = false;
      this.$refs.socialMedia.resetValidation();
    },
    closeDialog() {
      // this.resetForm();
      this.$root.$emit("closeEdit");
    },
  },
};
</script>
<style>
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
.topDiv {
  position: relative;
}
.topTitle {
  top: 0px;
  position: sticky;
  z-index: 2;
  background-color: white;
}
</style>

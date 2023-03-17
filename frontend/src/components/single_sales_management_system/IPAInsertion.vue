<template>
  <v-dialog v-model="model" persistent max-width="1300" width="1300">
    <v-card class="main-Card px-3">
      <v-card-title
        primary-title
        class="pb-1 my-0 title d-flex justify-space-between"
        style="padding: 5px 10px 10px"
      >
        <h2 class="text-h5 font-weight-bold">
          {{ $tr("create_item", $tr("ipa")) }}
        </h2>
        <CloseBtn @click="toggle" type="button" />
      </v-card-title>
      <v-card-text
        class="position-relative card-pos"
        style="height: 500px; overflow-y: auto"
      >
        <v-form ref="insertForm" lazy-validation>
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full"
            >
               <CustomInput
                @blur="$v.ipa.country_name.$touch()"
                v-model="$v.ipa.country_name.$model"
                :rules="validate($v.ipa.country_name, $root, 'country')"
                :label="$tr('label_required', $tr('country'))"
                :items="allCountries"
                item-value="name"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('country'))"
                type="autocomplete"
                @change="onCountrySelected"
                class="ml-md-2"
              />
            </div>
            <div class="w-full">
                <v-autocomplete
                  :items="cities"
                  @blur="$v.ipa.city.$touch()"
                  v-model="$v.ipa.city.$model"
                  :loading="isFetchingStates"
                  :rules="validate($v.ipa.city, $root, 'cities')"
                  :placeholder="$tr('choose_item', $tr('cities'))"
                  :label="$tr('cities')"
                  :no-data-text="$tr('no_data_available')"
                  v-bind="$attrs"
                  v-on="$listeners"
                  item-value="name"
                  item-text="name"
                  multiple
                  chips
                  
                  class="mt-md-4 ml-md-2  custom-input"
                  outlined
                  dense
                >
                  <template v-slot:selection="data">
                    <v-chip
                      v-bind="data.attrs"
                      :input-value="data.selected"
                      close
                      @click="data.select"
                      @click:close="remove(data.item.name)"
                      style="margin: 2px 0px !important"
                    >
                        <v-avatar
                          left
                          color="primary"
                          style="color: white !important"
                        >
                          {{ $tr(data.item.name).charAt(0) }}
                        </v-avatar>
                        {{ $tr(data.item.name) }}
                      </v-chip>
                    </template>
                    <template v-slot:[`item`]="{ item }">
                      <v-list-item-content>
                        <div class="d-flex align-center">
                          <div>
                            <v-list-item-title
                              v-html="`${$tr(item.name)}`"
                            ></v-list-item-title>
                          </div>
                        </div>
                      </v-list-item-content>
                    </template>
                  </v-autocomplete>
            </div>
          </div>
           <div class="d-flex flex-column flex-md-row">
           
            <div class="w-full">
                  <v-menu
                    v-model="menu_start"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="$v.ipa.target_time_start.$model"
                        @blur="$v.ipa.target_time_start.$touch()"
                        :rules="validate($v.ipa.target_time_start, $root, 'target_time_start')"
                        :label="$tr('label_required', $tr('target_time_start'))"
                        prepend-icon="mdi-calendar"
                        class="mb-md-2 mr-md-2"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="$v.ipa.target_time_start.$model"
                      @input="menu_start = false"
                    ></v-date-picker>
                  </v-menu>
            </div>
            <div class="w-full">
                  <v-menu
                    v-model="menu_end"
                    :close-on-content-click="false"
                    :nudge-right="40"
                    transition="scale-transition"
                    offset-y
                    min-width="auto"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="$v.ipa.target_time_end.$model"
                        :label="$tr('label_required', $tr('target_time_end'))"
                        @blur="$v.ipa.target_time_end.$touch()"
                        :rules="validate($v.ipa.target_time_end, $root, 'target_time_end')"
                        prepend-icon="mdi-calendar"
                        class="mb-md-2 mr-md-2"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                      ></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="$v.ipa.target_time_end.$model"
                      :min="$v.ipa.target_time_start.$model"
                      @input="menu_end = false"
                    ></v-date-picker>
                  </v-menu>
            </div>
             <div class="w-full">
              <CustomInput
                :label="$tr('label_required', $tr('target_age_min'))"
                placeholder="target_age_min"
                v-model="$v.ipa.target_age_min.$model"
                :rules="validate($v.ipa.target_age_min, $root, 'target_age_min')"
                type="number"
                class="mb-md-2 ml-md-2"
              />
            </div>
            <div class="w-full">
              <CustomInput
                :label="$tr('label_required', $tr('target_age_max'))"
                placeholder="target_age_max"
                v-model="$v.ipa.target_age_max.$model"
                :rules="validate($v.ipa.target_age_max, $root, 'target_age_max')"
                type="number"
                class="mb-md-2 ml-md-2"
              />
            </div>
          </div>
          <div class="d-flex flex-column flex-md-row">
            
             <div class="w-full"
            >
               <CustomInput
                @blur="$v.ipa.ispp_id.$touch()"
                v-model="$v.ipa.ispp_id.$model"
                :rules="validate($v.ipa.ispp_id, $root, 'ispp')"
                :label="$tr('label_required', $tr('ispp'))"
                :items="ispps"
                item-value="id"
                item-text="code"
                :placeholder="$tr('choose_item', $tr('ispp'))"
                type="autocomplete"
                 class="mb-md-1 mr-md-2"
              />
            </div>
            <div class="w-full"
            >
               <CustomInput
                v-model="$v.ipa.target_gender.$model"
                @blur="$v.ipa.target_gender.$touch()"
                :rules="validate($v.ipa.target_gender, $root, 'target_gender')"
                :label="$tr('label_required', $tr('target_gender'))"
                :items="target_genders"
                item-value="type"
                item-text="type"
                :placeholder="$tr('choose_item', $tr('target_gender'))"
                type="autocomplete"
                 class="mb-md-1 ml-md-2"
              />
            </div>
          </div>
          
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <v-btn color="primary" @click="addPlatform" class="rounded-sm">
                {{ $tr("add_more_plateform") }}
              </v-btn>
                <div v-for="(platforms, index) in ipa.platforms" :key="index">
                    <div class="d-flex align-center w-full justify-space-between">
                      <h2 class="my-1">{{ $tr("platform") }} {{ index * 1 + 1 }}</h2>
                      <CloseBtn class="pt-0" v-if="index !=0 " @click="removePlatform(index)" />
                    </div>
                  <div class="d-flex flex-column flex-md-row">
                    <div class="w-full me-2">
                      <CustomInput
                        v-model="platforms.platform_name"
                        :items="social_media"
                        item-value="name"
                        item-text="name"
                        :label="$tr('label_required', $tr('platform_name'))"
                        :placeholder="$tr('choose_item', $tr('platform_name'))"
                        type="autocomplete"
                      />
                    </div>
                    <div class="w-full ms-2">
                      <CustomInput
                        v-model="platforms.platform_placement"
                        :label="$tr('label_required', $tr('platform_placement'))"
                        placeholder="platform_placement"
                        type="textfield"
                      />
                    </div>
                    <div class="w-full ms-2">
                      <CustomInput
                        v-model="platforms.budget"
                        :label="$tr('label_required', $tr('budget'))"
                        placeholder="budget"
                        type="number"
                      />
                    </div>
                    <div class="w-full ms-2">
                      <CustomInput
                        v-model="platforms.target_cpo"
                        :label="$tr('label_required', $tr('target_cpo'))"
                        placeholder="target_cpo"
                        type="number"
                      />
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </v-form>
      </v-card-text>
      <v-card-actions class="pa-3">
        <v-btn
          @click="resetForm"
          color="success"
          class="stepper-btn mr-2"
          type="button"
        >
          {{ $tr("reset_form") }}
        </v-btn>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          class="stepper-btn px-3"
          style="min-width: 160px"
          @click="validateForm"
          :loading="isSubmitting"
          :disable="isSubmitting"
        >
          <template v-slot:loader>
            <span>
              <span>
                {{ $tr("submitting") + "..." }}
              </span>
              <v-progress-circular
                class="ma-0"
                indeterminate
                color="white"
                size="25"
                :width="2"
              />
            </span>
          </template>
          {{ $tr("submit") }}
        </v-btn>
        <v-btn
          @click="toggle"
          color="error"
          class="stepper-btn"
          :type="'button'"
        >
          {{ $tr("cancel") }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Validations from "../../validations/validations";
import CustomInput from "../design/components/CustomInput.vue";
import GlobalRules from "~/helpers/vuelidate";
import Editor from "../design/Editor.vue";
import Helpers from "../../helpers/helpers";
import HandleException from "../../helpers/handle-exception";
import { mapActions, mapGetters } from "vuex";

export default {
  data() {
    return {
      model: false,
      validate: GlobalRules.validate,
      isSubmitting: false,
      cities: [],
      ispps: [],
      menu_start: false,
      menu_end: false,
      target_genders:[
        {type:'male'},
        {type:'female'},
        {type:'both'},
      ],
      isFetchingStates: false,
      ipa: {
        country_name: null,
        city: [],
        ispp_id:null,
        target_age_min: '',
        target_age_max: '',
        target_time_start:'',
        target_time_end:'',
        target_gender:'',
        platforms:[
          {
            platform_name: '',
            platform_placement: '',
            budget: '',
            target_cpo: '',
          },
        ],
      },
    };
  },

  validations: {
    ipa: {
      city: Validations.requiredValidation,
      target_age_min: Validations.requiredValidation,
      target_age_max: Validations.requiredValidation,
      ispp_id: Validations.requiredValidation,
      country_name: Validations.requiredValidation,
      target_time_start: Validations.requiredValidation,
      target_time_end: Validations.requiredValidation,
      target_gender: Validations.requiredValidation,
     /* platforms:{
        platform_name: Validations.requiredValidation,
        platform_placement: Validations.requiredValidation,
        budget: Validations.requiredValidation,
        target_cpo: Validations.requiredValidation,
      }*/
    },
  },
    computed: {
    ...mapGetters({
      allCountries: "countries/getAscCountries",
      social_media: "social_media/getAllItems",
    }),
  },
 async created(){
     await this.fetchAllCountries();
     await this.fetchSocialMedia();
     try {
      const response = await this.$axios.get("single-sales/ispp?key=all");
      this.ispps = response?.data?.data;
     } catch (error) {
      
     }
  },
  methods: {
     ...mapActions({
      fetchAllCountries: "countries/fetchAscCountries",
      fetchSocialMedia: "social_media/fetchAllSocialMedia",
    }),
      removePlatform(index) {
      this.ipa.platforms.splice(index, 1);
    },

    addPlatform() {
      this.ipa.platforms.push({
        platform_name: '',
        platform_placement: '',
        budget: '',
        target_cpo: '',
      });
    },

    toggle() {
      this.model = !this.model;
      if (this.model) {
        // this.getAttributes();
      }
    },
    resetForm() {
      this.$refs.insertForm.reset();
    },

    async validateForm() {
      this.$refs.insertForm.validate();
      this.$v.ipa.$touch();
      if (!this.$v.ipa.$invalid) {
        this.isSubmitting = true;
        const ipa = Helpers.getFormData(this.ipa);
        await this.insertRecord(ipa);
        this.isSubmitting = false;
      }
    },

    async insertRecord(ipa) {
      try {
        const url = "single-sales/ipa";
        const  data  = await this.$axios.post(url, ipa);
        if (data.data.result) {
          this.$toasterNA("green", this.$tr('successfully_inserted'));
          this.$emit("pushRecord", data?.data?.data);
          this.toggle();
          this.resetForm();
        } else {
          // this.$toasterNA("red", this.$tr('something_went_wrong'));
				this.$toasterNA("red", this.$tr('something_went_wrong'));

        }
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
   remove(id) {
    console.log(id);
    console.log(this.ipa.city);
      this.ipa.city = this.ipa.city.filter(
        (d) => d != id
      );
    },
    async onCountrySelected(name){
      this.isFetchingStates = true;
      try {
        const all = this.allCountries.find((item) => item.name == name);
        const response = await this.$axios.get(
          `states/country-states/${all.id}`
        );
        this.cities = response?.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.isFetchingStates = false;
    }
  },
  components: { CloseBtn, CustomInput, Editor },
};
</script>

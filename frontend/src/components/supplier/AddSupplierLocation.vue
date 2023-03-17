<template>
    <div>
      <v-dialog v-model="locationRegisterModel" width="1200" persistent>
        <v-card class="pb-5">
          <!-- <v-card-title>
            <h1>{{ $tr("add_location") }}</h1>
            <v-spacer></v-spacer>
          </v-card-title> -->
          <Dialog @closeDialog="closeModel"/>
          <v-form
          lazy-validation
          ref="locationRegisterForm"
          @submit.prevent="submitForm"
          >
          <custom-stepper
          :headers="header"
          @close="() => {}"
          :canNext="false"
          @validate="() => {}"
          :is-submitting="false"
          ref="registerLocationStepper"
          @submit="submit"
          :isSubmitting="isSubmitting"
          >
          <template v-slot:step1>
            <div class="location" style="margin-top: -100px;">
              <v-card-text>
                <MultiInputItem
                :items="form"
                @addMore="addMore"
                @removeItem="removeItem"
                :title="'location'"
                    >
                      <template v-slot:content="{ item }">
                        <div>
                          <div class="h-full d-flex">
                            <InputCard
                              :title="$tr('label_required', $tr('country'))"
                              :hasSearch="true"
                              :hasPagination="false"
                              class="mb-0 pb-0"
                              @search="(v) => (searchCountry = v)"
                            >
                              <ItemsContainer
                                class="mb-0 pb-0"
                                v-model="item.country_id"
                                :items="filterCountry"
                                :loading="fetchingCountries"
                                :no_data="
                                  filterCountry.length < 1 && !fetchingCountries
                                "
                                @input="selectedCountry"
                                height="150px"
                              ></ItemsContainer>
                            </InputCard>
                          </div>
                          <div class="h-full d-flex">
                            <!-- <v-row> -->
                            <!-- <v-col cols="12" sm="6" class="py-0"> -->
                            <div class="w-full">
                              <CAutoComplete
                                v-model="item.state_id"
                                :title="$tr('label_required', $tr('state'))"
                                :items="states"
                                item-value="id"
                                item-text="name"
                                :placeholder="$tr('choose_item', $tr('state'))"
                                :loading="isFetchingStates"
                                prepend-inner-icon="#"
                                :rules="stateRules"
                              />
                            </div>
  
                            <div class="w-full mt-2">
                              <c-radio-w-ith-body
                                :items="radioItem"
                                v-model="item.location_type"
                                :text="$tr('location_type')"
                                @onChange="(v) => (item.location_type = v)"
                              />
                            </div>
                          </div>
                          <div class="h-full d-flex">
                            <div class="w-full">
                              <CTextField
                                v-model="item.map_link"
                                :title="$tr('label_required', $tr('map_link'))"
                                :placeholder="$tr('map_link')"
                                prepend-inner-icon="mdi-link-variant"
                                :rules="mapRules"
                              />
                            </div>
                            <div class="w-full">
                              <CTextField
                                v-model="item.location_phone"
                                :title="
                                  $tr('label_required', $tr('location_phone'))
                                "
                                :placeholder="$tr('location_phone')"
                                prepend-inner-icon="mdi-link-variant"
                                :rules="phoneRules"
                              />
                            </div>
                          </div>
                          <div class="h-full d-flex">
                            <div class="w-full">
                              <CAutoComplete
                                v-model="item.crowd_status"
                                :title="
                                  $tr('label_required', $tr('crowd_status'))
                                "
                                :items="crowdStatuses"
                                item-value="id"
                                item-text="name"
                                :placeholder="
                                  $tr('choose_item', $tr('crowd_status'))
                                "
                                prepend-inner-icon="#"
                                :rules="crowdRules"
                              />
                              <!-- <CTextField
                              :items="crowdStatuses"
                              v-model="item.crowd_status"
                              :title="$tr('label_required', $tr('crowd_status'))"
                              :placeholder="$tr('crowd_status')"
                              prepend-inner-icon="mdi-link-variant "
                              :rules="crowdRules"
                            /> -->
                            </div>
                            <div class="w-full">
                              <CTextField
                                v-model="item.contact_staff_name"
                                :title="
                                  $tr('label_required', $tr('contact_staff_name'))
                                "
                                :placeholder="$tr('contact_staff_name')"
                                prepend-inner-icon="mdi-link-variant"
                                :rules="staffRules"
                              />
                            </div>
                          </div>
                          <div class="h-full d-flex">
                            <div class="w-full">
                              <CTextField
                                v-model="item.address"
                                :title="$tr('label_required', $tr('address'))"
                                :placeholder="$tr('address')"
                                prepend-inner-icon="mdi-link-variant"
                                :rules="addressRules"
                              />
                            </div>
                          </div>
                        </div>
                      </template>
                    </MultiInputItem>
                  </v-card-text>
                  <!-- <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="primary" @click="submit" :loading="loading">{{
                    $tr("save")
                  }}</v-btn>
                  <v-btn color="error" @click="closeDialog">{{
                    $tr("cancel")
                  }}</v-btn>
                </v-card-actions> -->
                </div>
              </template>
              <template v-slot:step2>
                <done-message
                  :title="$tr('item_all_set', $tr('location'))"
                  :subTitle="$tr('you_can_access_your_item', $tr('location'))"
                />
              </template>
            </custom-stepper>
          </v-form>
        </v-card>
      </v-dialog>
    </div>
  </template>
  
  <script>
  import CustomInput from "../design/components/CustomInput.vue";
  import DoneMessage from "../design/components/DoneMessage.vue";
  import CloseBtn from "../design/Dialog/CloseBtn.vue";
import Dialog from '../design/Dialog/Dialog.vue';
  import CustomStepper from "../design/FormStepper/CustomStepper.vue";
  import ItemsContainer from "../new_form_components/cat_product_selection/ItemsContainer.vue";
  import MultiInputItem from "../new_form_components/cat_product_selection/MultiInputItem.vue";
  import InputCard from "../new_form_components/components/InputCard.vue";
  import CAutoComplete from "../new_form_components/Inputs/CAutoComplete.vue";
  import CRadioButton from "../new_form_components/Inputs/CRadioButton.vue";
  import CRadioWIthBody from "../new_form_components/Inputs/CRadioWIthBody.vue";
  import CTextArea from "../new_form_components/Inputs/CTextArea.vue";
  import CTextField from "../new_form_components/Inputs/CTextField.vue";
  
  export default {
    components: {
      MultiInputItem,
      CTextField,
      InputCard,
      ItemsContainer,
      CustomInput,
      CTextArea,
      CRadioButton,
      CRadioWIthBody,
      CAutoComplete,
      CustomStepper,
      DoneMessage,
      CloseBtn,
        Dialog
  },
    data() {
      return {
        form: [
          {
            country_id: "",
            state_id: "",
            location_type: "",
            map_link: "",
            location_phone: "",
            crowd_status: "",
            contact_staff_name: "",
            address: "",
          },
        ],
        radioItem: [
          {
            value: 1,
            label: "Main",
          },
          {
            value: 0,
            label: "Sub",
          },
        ],
        header: [
          {
            icon: "fa-globe",
            title: "Location",
            slotName: "step1",
          },
          {
            icon: "fa-thumbs-up",
            title: "done",
            slotName: "step2",
          },
        ],
        crowdStatuses: [
          { id: "low", name: "low" },
          { id: "crowd", name: "crowd" },
        ],
  
        stateRules: [(v) => !!v || "State is required"],
        mapRules: [(v) => !!v || "Map Link is required"],
        staffRules: [(v) => !!v || "Contact Staff Name is required"],
        addressRules: [(v) => !!v || "Address is required"],
        crowdRules: [(v) => !!v || "Crowd Status is required"],
        phoneRules: [(v) => !!v || "Location Phone is required"],
  
        registerLocationStepper: "",
        isSubmitting: false,
        loading: false,
        searchCountry: "",
        countries: [],
        locationRegisterModel: false,
        fetchingCountries: false,
        isFetchingStates: false,
        states: [],
        supplier_id: "",
  
      };
    },
    computed: {
      filterCountry: function () {
        if (this.searchCountry) {
          const filter = (item) =>
            item?.name
              ?.toLowerCase()
              ?.includes(this.searchCountry?.toLowerCase());
          return this.countries.filter(filter);
        }
        return this.countries;
      },
    },
    methods: {
      closeModel(){
        this.locationRegisterModel = !this.locationRegisterModel;
      },
      selectedCountry(item) {
        this.fetchCountryStates(item);
        console.log("in fetchcountry",item);
      },
      async loaded() {
              this.countries = [];
              await this.fetchCountries();
          },
      async submit() {
        try {
          if (this.$refs.locationRegisterForm.validate()) {
            this.isSubmitting = true;
            this.loading = true;
            let locationdata = {
              location: this.form,
              supplier_id: this.supplier_id,
            };
  
            const result = await this.$axios.post(
              "suppliers/store-location",
             locationdata
            );
            if (result.status == 201) {
              this.resetForm();
              this.$refs.registerLocationStepper.forceNext();
              this.$toasterNA(
                "green",
                this.$tr("locations_successfully_added_to_selected_supplier")
              );
            } else {
              this.$toasterNA("red", this.$tr("someting_went_wrong"));
            }
          }
          this.isSubmitting = false;
        } catch (error) {
          this.isSubmitting = false;
          this.$toasterNA("red", this.$tr("someting_went_wrong"));
        }
      },
      async fetchCountries() {
        try {
          this.countries = [];
          this.fetchingCountries = true;
          const url = "advertisement/connection/generate_link/country/all";
          const { data } = await this.$axios.get(url);
          console.log(data);
          this.countries = data.items;
        } catch (error) {}
        this.fetchingCountries = false;
      },
      async fetchCountryStates(country_id) {
        this.isFetchingStates = true;
        try {
          const response = await this.$axios.get(
            `states/country-states/${country_id}`
          );
          this.states = response?.data;
        } catch (error) {
          HandleException.handleApiException(this, error);
        }
        this.isFetchingStates = false;
      },
      // requiredValidations(item, textfield) {
      //   return [(_) => item.required || this.$tr("item_is_required", textfield)];
      // },
      openDialog(id) {
        this.locationRegisterModel = true;
        this.fetchCountries();
        this.supplier_id = id;
      },
      closeDialog() {
        this.locationRegisterModel = false;
        this.resetForm();
      },
      resetForm() {
        this.form = [{
                  country_id: "",
              }];
        this.$refs.locationRegisterForm.reset();
      },
      addMore() {
        if (this.$refs.locationRegisterForm.validate()) {
          this.form.push({
            country_id: "",
            state_id: "",
            location_type: "",
            map_link: "",
            location_phone: "",
            crowd_status: "",
            contact_staff_name: "",
            address: "",
          });
        }
      },
      removeItem(item) {
        if (this.form.length > 1) {
          this.form.splice(item, 1);
        }
      },
    },
  };
  </script>
  
  <style>
  </style>
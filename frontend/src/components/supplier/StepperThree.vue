<template>
  <div>
    <div
      class="rounded-circle stepper-header-item-inner d-flex justify-center align-center mx-auto"
    >
      <v-icon large color="white"> fa-warehouse </v-icon>
    </div>
    <div class="d-flex justify-center mx-auto mt-1">
      <v-btn color="primary" class="rounded-sm" @click="addlocation">
        {{ $tr("add_location") }}
      </v-btn>
    </div>
      <div class="d-flex flex-column flex-md-row"  v-for="(locationParts, k) in $v.addedLocations.$each.$iter" :key="k">
    <v-row
      class="d-flex flex-column flex-md-row"

    >
      <v-col cols="12" sm="12" md="6" class="py-0 mt-3">
        <h2>Location {{ k + 1 }}</h2>
      </v-col>
      <v-col cols="12" sm="12" md="6" class="d-flex justify-end align-end">
        <v-icon style="color: red" @click="removeLocation(k)">mdi-close</v-icon>
      </v-col>
      <!-- input country-->
      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <CustomInput
             @input="fetchCountryStates(k)"
            :items="Countries"
            :label="$tr('label_required',$tr('country'))"
          :placeholder="$tr('choose_item',$tr('country'))"
          type="autocomplete"
            item-text="name"
            item-value="id"
            class="me-md-2"
            v-model="locationParts.country_id.$model"
            :rules="requiredValidations(locationParts.country_id, 'country_id')"

          />
        </div>
      </v-col>


      <!-- input state-->
      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <CustomInput
            :loading="isFetchingStates"
            :label="$tr('label_required',$tr('state'))"
          :placeholder="$tr('choose_item',$tr('state'))"
          type="autocomplete"
            item-text="name"
            item-value="id"
            class="me-md-2"
            :items="addedLocations[k].country_states"
            v-model.trim="locationParts.state_id.$model"
            :rules="requiredValidations(locationParts.state_id, 'state_id')"

          />
        </div>
      </v-col>


      <!-- location Type -->
      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <label for="">Location Type</label>
          <v-radio-group
            :rules="requiredValidations(locationParts.location_type, 'location_type')"
            v-model.trim="locationParts.location_type.$model"
            row
            class="my-1 mb-0"
            tabindex="3"
          >
            <v-radio :label="$tr('main')" :value="1" />
            <v-radio :label="$tr('sub')" :value="0" />
          </v-radio-group>
          <br />
        </div>
      </v-col>

      <!-- input address-->
      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <CustomInput
            :label="$tr('label_required',$tr('address'))"
            placeholder="address"
            type="textarea"
            class="me-md-2"
            v-model.trim="locationParts.address.$model"
            :rules="unRequiredValidations(locationParts.address, 'address')"
          />
        </div>
      </v-col>

      <!-- input main Map Link-->

      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <CustomInput
            :label="$tr('label_required',$tr('map_link'))"
            placeholder="map_link"
            type="textfield"
            class="me-md-2"
            tabindex="5"
             v-model.trim="locationParts.map_link.$model"
             :rules="unRequiredValidations(locationParts.map_link, 'map_link')"

          />
        </div>
      </v-col>

      <!-- input location_phone-->
      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <CustomInput
            :label="$tr('label_required',$tr('location_phone'))"
            placeholder="location_phone"
            type="textfield"
            class="me-md-2"
            tabindex="6"
            v-model.trim="locationParts.location_phone.$model"
            :rules="locationPhoneRule(locationParts.location_phone)"  />

        </div>
      </v-col>

      <!-- input crowd status -->
      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <CustomInput
            :items="crowdStatuses"
            :placeholder="$tr('choose_item',$tr('crowd_status'))"
            :label="$tr('label_required',$tr('crowd_status'))"
            type="autocomplete"
            item-text="id"
            item-value="name"
            class="me-md-2"
             v-model.trim="locationParts.crowd_status.$model"
            :rules="requiredValidations(locationParts.crowd_status, 'crowd_status')"
          />
        </div>
      </v-col>
      <!-- input contact_staff_name-->
      <v-col cols="12" sm="12" md="6" class="py-0">
        <div class="w-full">
          <CustomInput
            :label="$tr('label_required', $tr('contact_staff_name'))"
            placeholder="contact_staff_name"
            type="textfield"
            class="me-md-2"
            tabindex="8"
            v-model.trim="locationParts.contact_staff_name.$model"
            :rules="unRequiredValidations(locationParts.contact_staff_name, 'contact_staff_name')"
          />
        </div>
      </v-col>
    </v-row>
    </div>
  </div>
</template>

<script>
import CustomInput from "~/components/design/components/CustomInput.vue";
import { mapActions, mapGetters } from "vuex";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import Validations from "./../../validations/validations";

export default {
  components: { CustomInput },
  computed: {
    ...mapGetters({
      Countries: "countries/getCountries",
    }),
  },
  data() {
    return {
      supplier: {
        location: [],
        selectedCountry: "",
        deletedLocations: [],
      },
      crowdStatuses: [
        { id: "low", name: "low" },
        { id: "crowd", name: "crowd" },
      ],
      isFetchingStates: false,
      addedLocations: [],
    };
  },
  methods: {
    ...mapActions({
      allCountries: "countries/fetchCountries",
    }),
    addlocation() {
      this.addedLocations.push({
        id: "",
        country_id: "",
        location_type: "",
        map_link: "",
        crowd_status: "",
        notes: "",
        state_id: "",
        address: "",
        location_phone: "",
        contact_staff_name: "",
        country_states: [],
      });
    },
    async fetchCountryStates(index, idd = ''){
            this.addedLocations[index].state = '';
            const country_id = !idd ? this.addedLocations[index].country_id : idd;
            this.isFetchingStates = true;
            try {
                const response = await this.$axios.get(
                  `states/country-states/${country_id}`
                  );
                this.states = response?.data;
                this.addedLocations[index].country_states = response?.data;
            } catch (error) {
              HandleException.handleApiException(this, error);
            }
            this.isFetchingStates = false;
          },
          removeLocation(index) {
            this.addedLocations.splice(index, 1);
          },
          
          checkValidation() {
      if (this.addedLocations.length > 0) {
        console.log("location" ,this.supplier.location);
        return !this.$v.addedLocations.$invalid ? true : false;
      } else {

        return '123';
      }
    },
    requiredValidations(item, textfield) {
      return [
        (_) =>
          item.required ||
          this.$tr("item_is_required",  textfield),
      ];
    },
    unRequiredValidations(item, textfield) {
      return [
        (_) =>
          item.minLength ||
          this.$tr("min_length",
            this.$tr(textfield),
            item.$params.minLength.min,
          ),
        (_) =>
          item.maxLength ||
          this.$tr("max_length",
            this.$tr(textfield),
            item.$params.maxLength.max,
          ),
      ];
    },
    locationPhoneRule(item) {
      return [
        (_) =>
          item.required ||
          this.$tr("item_is_required",
            this.$tr("location_phone"),
          ),
        (_) =>
          item.phoneNumber ||
          this.$tr("item_is_invalid", this.$tr("location_phone")),
      ];
    },
  },
  created() {
    this.allCountries({
      key: "all",
      countriesHasCompanies: "false",
      options: null,
    });
  },
    validations:{
        addedLocations: {
            $each: {
                country_id:             Validations.requiredValidation,
                location_type:          Validations.requiredValidation,
                crowd_status:           Validations.requiredValidation,
                state_id:               Validations.requiredValidation,
                location_phone:         Validations.phoneValidation,
                map_link:               { minLength: minLength(3), maxLength: maxLength(500) },
                address:                { minLength: minLength(3), maxLength: maxLength(100) },
                contact_staff_name:     { minLength: minLength(2), maxLength: maxLength(100) }
            },
        },
    },
};
</script>
<style scoped>
.stepper-header-item-inner {
  height: 80px;
  width: 80px;

  background: gray;
}
</style>

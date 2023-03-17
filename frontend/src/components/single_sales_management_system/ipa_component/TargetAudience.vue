<template>
  <v-container>
    <v-row dense>
      <v-col>
        <CSelect
          @blur="form.ispp_id.$touch()"
          v-model="form.ispp_id.$model"
          :rules="
            validateRule(
              form.ispp_id, // validate objec
              $root, // context
              $tr('ispp') // lable for feedback
            )
          "
          :title="$tr('label_required', $tr('ispp'))"
          :items="ispps"
          item-value="id"
          item-text="code"
          :placeholder="$tr('choose_item', $tr('ispp'))"
          prepend-inner-icon="fa fa-hashtag"
        />
      </v-col>
    </v-row>
    <v-row dense>
      <v-col cols="6">
        <CSelect
          @blur="form.country_name.$touch()"
          v-model="form.country_name.$model"
          :rules="
            validateRule(
              form.country_name, // validate objec
              $root, // context
              $tr('target_country') // lable for feedback
            )
          "
          :title="$tr('label_required', $tr('target_country'))"
          :items="allCountries"
          item-value="name"
          item-text="name"
          :placeholder="$tr('choose_item', $tr('target_country'))"
          @change="onCountrySelected"
          prepend-inner-icon="fa fa-globe"
        />
      </v-col>
      <v-col cols="6">
        <CSelectMulti
          :placeholder="$tr('choose_item', $tr('target_cities'))"
          :title="$tr('target_cities')"
          :no-data-text="$tr('no_data_available')"
          :items="cities"
          :selectedItems="selectedCity"
          :loading="isFetchingStates"
          v-model="selectModel"
          item-text="name"
          item-value="name"
          multi
          hasItems
          @add="addItems"
          @removeItem="removeItem"
          return-object
          prepend-inner-icon="mdi-bookmark"
          @blur="form.city.$touch()"
          :invalid="form.city.$invalid"
          :rules="
            validateRule(
              form.city, // validate objec
              $root, // context
              $tr('target_cities') // lable for feedback
            )
          "
        />
      </v-col>
    </v-row>
    <v-row dense>
      <v-col cols="6">
        <CGenderSelection
          title="target_gender"
          v-model="form.target_gender.$model"
          :rules="
            validateRule(
              form.target_gender, // validate objec
              $root, // context
              $tr('target_gender') // lable for feedback
            )
          "
          :items="target_genders"
        />
      </v-col>
      <v-col cols="6">
        <CRangeSlider
          v-model="form.target_age.$model"
          :rules="
            validateRule(
              form.target_age, // validate objec
              $root, // context
              $tr('target_age') // lable for feedback
            )
          "
          title="target_age"
          :min="0"
          :max="70"
        />
      </v-col>
    </v-row>
    <v-row dense>
      <v-col cols="6">
        <CDateRangePicker
          title="arrival_time"
          v-model="form.date.$model"
          :icon="['mdi-calendar-clock', 'mdi-calendar-clock']"
          :placeholder="[$tr('start_date'), $tr('end_date')]"
          :rules="[
            validateRule(form.date.start_date, $root, 'start_date'),
            validateRule(form.date.end_date, $root, 'end_date'),
          ]"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import { mapActions, mapGetters } from "vuex";
import HandleException from "../../../helpers/handle-exception";
import CSelect from "../../new_form_components/Inputs/CSelect";
import CGenderSelection from "../../new_form_components/Inputs/CGenderSelection";
import CRangeSlider from "../../new_form_components/Inputs/CRangeSlider";
import CSelectMulti from "../../new_form_components/Inputs/CSelectMulti.vue";
import CDateRangePicker from "../../new_form_components/Inputs/CDateRangePicker.vue";

export default {
  name: "TargetAudience",
  components: {
    CSelect,
    CGenderSelection,
    CRangeSlider,
    CSelectMulti,
    CDateRangePicker,
  },
  props: {
    form: Object,
  },
  data() {
    return {
      setCityToDefault: false,
      target_genders: [
        {
          value: "male",
          label: "male",
        },
        {
          value: "female",
          label: "female",
        },
        {
          value: "both",
          label: "both",
        },
      ],

      isFetchingStates: false,
      cities: [],
      ispps: [],
      selectedCity: [],
      selectModel: [],
      validateRule: GlobalRules.validate,
    };
  },
  computed: {
    ...mapGetters({
      allCountries: "countries/getAscCountries",
      social_media: "social_media/getAllItems",
    }),
  },
  async created() {
    await this.fetchAllCountries();
    try {
      const response = await this.$axios.get("single-sales/ispp?key=all");
      this.ispps = response?.data?.data;
    } catch (error) {}
  },
  methods: {
    ...mapActions({
      fetchAllCountries: "countries/fetchAscCountries",
    }),
    async loaded() {
      this.selectModel = [];
      this.selectedCity = [];
      this.setCityToDefault = false;
      if (this.form.$model.country_name) {
        await this.onCountrySelected(this.form.$model.country_name);
        if (this.form.$model.city.length > 0) {
          await this.editCity();
          this.setCityToDefault = true;
        }
      }
    },
    async validate() {
      // validate function must validate this step here and return true of false
      this.form.ispp_id.$touch();
      this.form.country_name.$touch();
      this.form.city.$touch();
      this.form.target_gender.$touch();
      this.form.target_age.$touch();
      this.form.date.start_date.$touch();
      this.form.date.end_date.$touch();
      return (
        !this.form.ispp_id.$invalid &&
        !this.form.country_name.$invalid &&
        !this.form.city.$invalid &&
        !this.form.target_gender.$invalid &&
        !this.form.date.$invalid &&
        !this.form.date.start_date.$invalid &&
        !this.form.date.end_date.$invalid &&
        !this.form.target_age.$invalid
      );
    },
    editCity() {
      for (let i = 0; i < this.form.$model.city.length; i++) {
        this.selectedCity.push(
          this.cities.find((item) => item.name == this.form.$model.city[i])
        );
      }
    },
    async onCountrySelected(name) {
      this.selectModel = [];
      this.selectedCity = [];
      if (this.setCityToDefault) {
        this.form.$model.city = [];
      }
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
    },
    addItems() {
      for (let i = 0; i < this.selectModel.length; i++) {
        const el = this.selectModel[i];
        let index = this.selectedCity.findIndex((item) => item.id == el.id);
        if (index == -1) {
          this.selectedCity.push(el);
          this.form.$model.city.push(el.name);
        }
      }
      this.selectModel = [];
    },
    removeItem(item) {
      this.selectedCity = this.selectedCity.filter((el) => el.id != item.id);
      this.form.$model.city = this.form.$model.city.filter(
        (el) => el != item.name
      );
    },
  },
};
</script>

<style scoped></style>

<template>
  <div>
    <CTitle :text="'supplier_location'" />
    <MultiInputItem
      :items="form.location.$each.$iter"
      @addMore="addMore"
      @removeItem="removeItem"
      :title="'supplier_location'"
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
                v-model="item.country_id.$model"
                :items="filterCountry"
                :loading="fetchingCountries"
                :no_data="filterCountry.length < 1 && !fetchingCountries"
                @input="selectedCountry"
                @blur="item.country_id.$touch()"
                :rules="validateRule(item.country_id, $root, $tr('country_id'))"
                :invalid="item.country_id.$invalid"
                height="150px"
              ></ItemsContainer>
            </InputCard>
          </div>
          <div class="h-full d-flex">
            <!-- <v-row> -->
            <!-- <v-col cols="12" sm="6" class="py-0"> -->
            <div class="w-full">
              <CAutoComplete
                v-model="item.state_id.$model"
                :title="$tr('label_required', $tr('state'))"
                :items="states[item.country_id.$model]"
                item-value="id"
                item-text="name"
                :placeholder="$tr('choose_item', $tr('state'))"
                :loading="isFetchingStates"
                prepend-inner-icon="mdi-format-list-bulleted-square"
                @blur="item.state_id.$touch()"
                :rules="validateRule(item.state_id, $root, $tr('state_id'))"
                :invalid="item.state_id.$invalid"
              />
            </div>

            <div class="w-full mt-2">
              <CRadioWIthBody
                :items="radioItem"
                :value="item.location_type.$model"
                v-model="item.location_type.$model"
                :text="$tr('label_required', $tr('location_type'))"
                @onChange="(v) => (item.location_type.$model = v)"
                @blur="item.location_type.$touch()"
                :rules="
                  validateRule(item.location_type, $root, $tr('location_type'))
                "
                :invalid="item.location_type.$invalid"
              />
            </div>
          </div>
          <div class="h-full d-flex">
            <div class="w-full">
              <CTextField
                v-model="item.map_link.$model"
                :title="$tr('label_required', $tr('map_link'))"
                :placeholder="$tr('map_link')"
                prepend-inner-icon="mdi-link-variant"
                @blur="item.map_link.$touch()"
                :rules="validateRule(item.map_link, $root, $tr('map_link'))"
                :invalid="item.map_link.$invalid"
              />
            </div>
            <div class="w-full">
              <CTextField
                v-model="item.location_phone.$model"
                :title="$tr('label_required', $tr('location_phone'))"
                :placeholder="$tr('location_phone')"
                prepend-inner-icon="mdi-cellphone-basic"
                @blur="item.location_phone.$touch()"
                :rules="
                  validateRule(
                    item.location_phone,
                    $root,
                    $tr('location_phone'),
                  )
                "
                :invalid="item.location_phone.$invalid"
              />
            </div>
          </div>
          <div class="h-full d-flex">
            <!-- <div class="w-full">
              <CTextField
                v-model="item.crowd_status.$model"
                :title="$tr('label_required', $tr('crowd_status'))"
                :placeholder="$tr('crowd_status')"
                prepend-inner-icon="mdi-crowd"
                @blur="item.crowd_status.$touch()"
                :rules="
                  validateRule(item.crowd_status, $root, $tr('crowd_status'))
                "
                :invalid="item.crowd_status.$invalid"
              />
            </div> -->
            <div class="w-full">
              <CAutoComplete
                v-model="item.crowd_status.$model"
                @blur="item.crowd_status.$touch()"
                :rules="
                  validateRule(
                    item.crowd_status, // validate objec
                    $root, // context
                    $tr('crowd_status'), // lable for feedback
                  )
                "
                :items="crowdStatus"
                item-value="type"
                item-text="type"
                :placeholder="$tr('choose_item', $tr('crowd_status'))"
                :invalid="item.crowd_status.$invalid"
                prepend-inner-icon="mdi-account-supervisor"
                :isCheckNeeded="true"
                title="crowd_status"
              />
            </div>
            <div class="w-full">
              <CTextField
                v-model="item.contact_staff_name.$model"
                :title="$tr('label_required', $tr('contact_staff_name'))"
                :placeholder="$tr('contact_staff_name')"
                prepend-inner-icon="mdi-contacts-outline"
                @blur="item.contact_staff_name.$touch()"
                :rules="
                  validateRule(
                    item.contact_staff_name,
                    $root,
                    $tr('contact_staff_name'),
                  )
                "
                :invalid="item.contact_staff_name.$invalid"
              />
            </div>
          </div>
          <div class="h-full d-flex">
            <div class="w-full">
              <CTextField
                v-model="item.address.$model"
                :title="$tr('label_required', $tr('address'))"
                :placeholder="$tr('address')"
                prepend-inner-icon="mdi-map-marker-account"
                @blur="item.address.$touch()"
                :rules="validateRule(item.address, $root, $tr('address'))"
                :invalid="item.address.$invalid"
              />
            </div>
          </div>
        </div>
      </template>
    </MultiInputItem>
  </div>
</template>

<script>
import CTitle from '../../new_form_components/Inputs/CTitle.vue'
import GlobalRules from '~/helpers/vuelidate'
import MultiInputItem from '../../new_form_components/cat_product_selection/MultiInputItem.vue'
import InputCard from '../../new_form_components/components/InputCard.vue'
import ItemsContainer from '../../new_form_components/cat_product_selection/ItemsContainer.vue'
import CAutoComplete from '../../new_form_components/Inputs/CAutoComplete.vue'
import CRadioWIthBody from '../../new_form_components/Inputs/CRadioWIthBody.vue'
import CTextField from '../../new_form_components/Inputs/CTextField.vue'

export default {
  components: {
    CTitle,
    MultiInputItem,
    InputCard,
    ItemsContainer,
    CAutoComplete,
    CRadioWIthBody,
    CTextField,
  },
  props: { form: Object },
  data() {
    return {
      crowdStatus: [
        { id: "1", type: "Normal" },
        { id: "2", type: "Crowd" },
      ],
      radioItem: [
        {
          value: 0,
          label: 'sub',
        },
        {
          value: 1,
          label: 'main',
        },
      ],
      searchCountry: '',
      countries: [],
      fetchingCountries: false,
      isFetchingStates: false,
      states: [],
      validateRule: GlobalRules.validate,
    }
  },
  computed: {
    filterCountry: function () {
      if (this.searchCountry) {
        const filter = (item) =>
          item?.name?.toLowerCase()?.includes(this.searchCountry?.toLowerCase())
        return this.countries.filter(filter)
      }
      return this.countries
    },
  },
  methods: {
    async validate() {
      this.form.location.$touch()
      return !this.form.location.$invalid
    },
    async loaded() {
      this.fetchCountries()
    },
    selectedCountry(item) {
      this.fetchCountryStates(item)
    },
    async fetchCountries() {
      try {
        this.countries = []
        this.fetchingCountries = true
        const url = 'advertisement/connection/generate_link/country/all'
        const { data } = await this.$axios.get(url)
        this.countries = data.items
        if (this.form.$model.supplier_id) {
          for (
            let index = 0;
            index < this.form.location.$model.length;
            index++
          ) {
            this.fetchCountryStates(this.form.location.$model[index].country_id)
          }
        }
      } catch (error) {}
      this.fetchingCountries = false
    },
    async fetchCountryStates(country_id) {
      this.isFetchingStates = true
      try {
        const response = await this.$axios.get(
          `states/country-states/${country_id}`,
        )
        this.states[country_id] = response?.data
      } catch (error) {
        HandleException.handleApiException(this, error)
      }
      this.isFetchingStates = false
    },
    addMore() {
      this.form.location.$model.push({
        country_id: '',
        state_id: '',
        location_type: '',
        map_link: '',
        location_phone: '',
        crowd_status: '',
        contact_staff_name: '',
        address: '',
      })
    },
    removeItem(item) {
      if (this.form.location.$model.length > 1) {
        this.form.location.$model.splice(item, 1)
      }
    },
  },
}
</script>

<style></style>

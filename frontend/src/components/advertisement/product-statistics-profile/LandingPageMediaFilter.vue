<template>
  <v-card class="white" flat>
    <v-card-title class="">
      <div
        style="background-color: #f7f8fc"
        class="
          d-flex
          align-center
          justify-space-between
          w-full
          pa-1
          ps-0
          rounded
        "
      >
        <div class="d-flex align-center">
          <v-btn icon plain text small color="primary" @click="$emit('close')"
            ><v-icon>mdi-close</v-icon></v-btn
          >
          <p class="custom-card-title-1 ma-0">{{ $tr("filter") }}</p>
        </div>
        <v-spacer></v-spacer>
        <div class="d-flex">
          <v-btn
            elevation="2"
            style="background-color: #ff4081; width: 28px; height: 28px"
            fab
            icon
            x-small
            class="green lighten-4"
            @click="$emit('filter', filters)"
          >
            <v-icon color="green">mdi-check-bold</v-icon>
          </v-btn>
        </div>
      </div>
    </v-card-title>
    <v-card-text>
      <div
        :style="`border: 1px solid #eeeeee; height: ${
          scroll ? '90vh' : '100%'
        }; overflow - y: auto`"
        class="rounded pa-1"
      >
        <div class="grey lighten-3">
          <div
            class="
              grey
              lighten-3
              rounded
              pa-1
              d-flex
              align-center
              justify-space-between
            "
          >
            <div class="d-flex align-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="19.444"
                height="17.77"
                viewBox="0 0 19.444 17.77"
              >
                <path
                  id="Path_2156"
                  data-name="Path 2156"
                  d="M-6343.477-4389.457a2.534,2.534,0,0,1-1.428-2.814,2.509,2.509,0,0,1,2.333-1.974,2.4,2.4,0,0,1,2.376,1.4.417.417,0,0,0,.451.262c.745-.011,1.489-.006,2.234,0a.826.826,0,0,1,.923.833c0,.518-.335.817-.923.83h-1.1c-.118,0-.236.009-.354,0a1.364,1.364,0,0,0-1.545.718,2.133,2.133,0,0,1-1.888.974A2.672,2.672,0,0,1-6343.477-4389.457Zm-10.545-1.462c-.626,0-.966-.3-.966-.832s.362-.832.967-.832q3.218,0,6.439,0a.83.83,0,0,1,.944.815c.01.534-.33.843-.947.846-1.086,0-2.172,0-3.259,0h-3.179Zm2.663-5.561a.446.446,0,0,0-.476-.291c-.76.015-1.518.012-2.276,0a.836.836,0,0,1-.845-.583.793.793,0,0,1,.314-.933,1.366,1.366,0,0,1,.591-.167c.376-.026.758-.007,1.135-.007.106,0,.211-.008.315,0a1.366,1.366,0,0,0,1.525-.685,2.33,2.33,0,0,1,2.9-.793,2.5,2.5,0,0,1,1.53,2.649,2.507,2.507,0,0,1-2.076,2.157,2.2,2.2,0,0,1-.426.042A2.525,2.525,0,0,1-6351.359-4396.48Zm7.3-.29a.857.857,0,0,1-.857-.618.808.808,0,0,1,.392-.94,1.5,1.5,0,0,1,.6-.127c1.06-.012,2.119-.005,3.179-.005s2.146-.006,3.221,0a.836.836,0,0,1,.93.952c-.046.458-.411.738-.99.74q-2.139,0-4.278,0c-.246,0-.489,0-.733,0h-.6C-6343.48-4396.762-6343.77-4396.764-6344.059-4396.77Zm2.453-6.71a2.551,2.551,0,0,1,2.53-2.519,2.506,2.506,0,0,1,2.489,2.5,2.471,2.471,0,0,1-2.492,2.522h-.009A2.512,2.512,0,0,1-6341.605-4403.48Zm-12.489.837a.82.82,0,0,1-.893-.828.8.8,0,0,1,.881-.833c1.662-.006,3.323,0,4.983,0s3.323,0,4.985,0a.8.8,0,0,1,.829.588.764.764,0,0,1-.312.9,1.025,1.025,0,0,1-.544.167q-3.147.01-6.3.009Z"
                  transform="translate(6355.529 4406.499)"
                  fill="#555"
                  stroke="rgba(0,0,0,0)"
                  stroke-width="1"
                />
              </svg>
              <p
                class="text-uppercase ma-0 ps-1"
                style="color: #555; font-size: 14px; font-weight: 500"
              >
                {{ $tr("filter") }}
              </p>
            </div>
            <p
              class="mb-0 primary--text me-1 cursor-pointer"
              style="font-size: 14px; font-weight: 500"
              @click="filters = []"
            >
              {{ $tr("reset") }}
            </p>
          </div>
          <div class="d-flex flex-wrap ps-1">
            <v-chip
              class="me-1 mb-1"
              label
              outlined
              v-for="(item, index) in filters"
              :key="index"
            >
              <span
                v-if="item.column == 'countries' || item.column == 'companies'"
              >
                {{ getSelectedName(item.value, item.column) }}
              </span>
              <span v-else>
                {{
                  item.column != "date"
                    ? $tr(item.value)
                    : item.value.start_date + " - " + item.value.end_date
                }}
              </span>
              <v-icon
                right
                small
                class="cursor-pointer"
                style="margin-top: 3px"
                @click="unSelect(index)"
              >
                mdi-close
              </v-icon></v-chip
            >
          </div>
        </div>
        <v-expansion-panels
          accordion
          class="elevation-0 mt-2 custom-panel"
          style="box-shadow: unset"
        >
          <v-expansion-panel v-if="isContentLibrary">
            <v-expansion-panel-header
              class="px-2 grey--text text--darken-1"
              style="font-size: 15px; font-weight: 400"
              >{{ $tr("common") }}</v-expansion-panel-header
            >

            <v-expansion-panel-content class="pt-0">
              <FilterOptions title="countries">
                <template>
                  <SelectItemRohullah
                    v-for="item in countries"
                    :key="item.id"
                    :item="item"
                    :flag="true"
                    :selected="checkSelected(item, 'countries')"
                    @click="onSelect(item, 'countries')"
                  />
                </template>
              </FilterOptions>
              <FilterOptions title="companies">
                <template>
                  <SelectItemRohullah
                    v-for="item in companies"
                    :key="item.id"
                    :item="item"
                    logo
                    :selected="checkSelected(item, 'companies')"
                    @click="onSelect(item, 'companies')"
                  />
                </template>
              </FilterOptions>

              <FilterOptions title="product_code" class="" v-if="withPcode">
                <v-combobox
                  hide-details
                  style="z-index: 10000"
                  :items="item_codes"
                  outlined
                  dense
                  flat
                  class="mx-1"
                  @change="onItemCodeChange($event)"
                >
                </v-combobox>
              </FilterOptions>
            </v-expansion-panel-content>
          </v-expansion-panel>

          <v-expansion-panel>
            <v-expansion-panel-header
              class="px-2 grey--text text--darken-1"
              style="font-size: 15px; font-weight: 400"
              >{{ $tr("general_information") }}</v-expansion-panel-header
            >

            <v-expansion-panel-content class="pt-0">
              <FilterOptions title="content_type">
                <template>
                  <SelectItemRohullah
                    v-for="item in contentType"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'content_type')"
                    @click="onSelect(item, 'content_type')"
                  />
                </template>
              </FilterOptions>
              <FilterOptions title="content_source">
                <template>
                  <SelectItemRohullah
                    v-for="item in contentSource"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'content_source')"
                    @click="onSelect(item, 'content_source')"
                  />
                </template>
              </FilterOptions>

              <FilterOptions title="content_language">
                <template>
                  <SelectItemRohullah
                    v-for="item in contentLanguage"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'content_language')"
                    @click="onSelect(item, 'content_language')"
                  /> </template
              ></FilterOptions>

              <FilterOptions title="content_size">
                <template>
                  <SelectItemRohullah
                    v-for="item in mediaSize"
                    :key="item.id"
                    :item="item"
                    :icon="true"
                    hasDetails
                    :selected="checkSelected(item, 'media_size')"
                    @click="onSelect(item, 'media_size')"
                  />
                </template>
              </FilterOptions>

              <FilterOptions title="request_when">
                <template>
                  <SelectItemRohullah
                    v-for="item in requestWhen"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'request_when')"
                    @click="onSelect(item, 'request_when')"
                  />
                </template>
              </FilterOptions>
            </v-expansion-panel-content>
          </v-expansion-panel>

          <v-expansion-panel>
            <v-expansion-panel-header
              class="px-2 grey--text text--darken-1"
              style="font-size: 15px; font-weight: 400"
              >{{ $tr("marketing_information") }}</v-expansion-panel-header
            >
            <v-expansion-panel-content>
              <FilterOptions title="is_the_content_for_the_specific_season?">
                <template>
                  <SelectItemRohullah
                    v-for="item in seasons"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'season')"
                    @click="onSelect(item, 'season')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions
                title="content_risks_of_violating_advertising_policy"
              >
                <template>
                  <SelectItemRohullah
                    v-for="item in riskPalicy"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'risk_palicy')"
                    @click="onSelect(item, 'risk_palicy')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions
                title="is_there_an_oreder_of_the_custormer_to_do_someting?"
              >
                <template>
                  <SelectItemRohullah
                    v-for="item in mainOrCustomer"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'main_or_customer')"
                    @click="onSelect(item, 'main_or_customer')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions title="information_offering">
                <template>
                  <SelectItemRohullah
                    v-for="item in infoOffer"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'info_offer')"
                    @click="onSelect(item, 'info_offer')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>
            </v-expansion-panel-content>
          </v-expansion-panel>

          <v-expansion-panel>
            <v-expansion-panel-header
              class="px-2 grey--text text--darken-1"
              style="font-size: 15px; font-weight: 400"
              >{{ $tr("design_information") }}</v-expansion-panel-header
            >
            <v-expansion-panel-content>
              <FilterOptions title="content_direction">
                <template>
                  <SelectItemRohullah
                    v-for="item in contentDirections"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'content_direction')"
                    @click="onSelect(item, 'content_direction')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions title="voice_over">
                <template>
                  <SelectItemRohullah
                    v-for="item in voiceOver"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'voice_over')"
                    @click="onSelect(item, 'voice_over')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions title="music">
                <template>
                  <SelectItemRohullah
                    v-for="item in musics"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'music')"
                    @click="onSelect(item, 'music')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions title="shooting">
                <template>
                  <SelectItemRohullah
                    v-for="item in shootings"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'shooting')"
                    @click="onSelect(item, 'shooting')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions title="people">
                <template>
                  <SelectItemRohullah
                    v-for="item in peoples"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'people')"
                    @click="onSelect(item, 'people')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>

              <FilterOptions title="graphics">
                <template>
                  <SelectItemRohullah
                    v-for="item in graphics"
                    :key="item.id"
                    :item="item"
                    :selected="checkSelected(item, 'graphics')"
                    @click="onSelect(item, 'graphics')"
                  >
                  </SelectItemRohullah>
                </template>
              </FilterOptions>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import moment from "moment";
import SelectItemRohullah from "../../new_form_components/components/SelectItemRohullah.vue";
import FilterDateRange from "../FilterDateRange.vue";
import FilterOptions from "./FilterOptions.vue";
export default {
  components: {
    SelectItemRohullah,
    FilterOptions,
    FilterDateRange,
  },
  props: {
    scroll: Boolean,
    withPcode: Boolean,
    isContentLibrary:Boolean
  },
  data() {
    return {
      colors: {
        profit: "#42a2e2",
        medium_profit: "#42a2e2",
        less_profit: "#fbcc5f",
        loss: "#39a0e6",
        medium_loss: "#39a0e6",
        more_loss: "#cacbcf",
      },
      filters: [],
      selectedItems: [],

      Content_Type: "",
      Content_Language: "",
      Content_Size: "",
      season: "",
      risk_palicy: "",
      main_or_customer: "",
      info_offer: "",
      content_direction: "",
      voice_over: "",
      music: "",
      shooting: "",
      people: "",
      graphic: "",
      countries: [
        {
          id: "1",
          name: "afghanistan",
          iso2: "af",
          active: true,
        },
      ],
      date_range: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
      companies: [
        {
          id: "1",
          name: "UAE - SHOPIX",
          img: "af",
          active: true,
        },
      ],
      item_codes: ["TK80", "PR21"],
      selected_item_code: [],

      requestWhen: [
        {
          id: "product_is_sold_for_the_first_time",
          name: "product_is_sold_for_the_first_time",
        },
        { id: "during_the_sales_phase", name: "during_the_sales_phase" },
      ],
      contentSource: [
        { id: "creation", name: "creation" },
        { id: "copy", name: "copy" },
      ],
      contentType: [
        { id: "video", name: "video", icon: "fa fa-video", active: true },
        {
          id: "banner",
          name: "banner",
          icon: "fa fa-flag",
          active: true,
        },
        {
          id: "animate_banner",
          icon: "fa fa-file-video",
          name: "animate_banner",
          active: true,
        },
      ],
      contentLanguage: [
        { id: "english", name: "english", image: "/english.jpg", active: true },
        {
          id: "UAE",
          name: "UAE",
          image: "/uae.jpg",
          active: true,
        },
      ],

      mediaSize: [
        {
          id: "Story 1080x1920",
          name: "story",
          details: "1080 x 1920",
          image: "/icons/icons/story-size.png",
          active: true,
        },
        {
          id: "Youtube 1920x1080",
          name: "youtube",
          details: "1920 x 1080",
          image: "/icons/icons/youtube-size.png",
          active: true,
        },
        {
          id: "Facebook 1200x628",
          name: "facebook",
          details: "1200 x 628",
          image: "/icons/icons/facebook-size.png",
          active: true,
        },
        {
          id: "Instagram post 1080x1080",
          name: "instagram_post",
          details: "1080 x 1080",
          image: "/icons/icons/instagram-post-size.png",
          active: true,
        },
        {
          id: "Instagram portrait 1080x1350",
          name: "instagram_portrait",
          details: "1080 x 1350",
          image: "/icons/icons/instagram-portriat-size.png",
          active: true,
        },
      ],

      seasons: [
        { id: "yes", name: "yes", active: true },
        { id: "no", name: "no", active: true },
      ],
      mainOrCustomer: [
        { id: "yes", name: "yes", active: true },
        { id: "no", name: "no", active: true },
      ],

      riskPalicy: [
        { id: "no_risk", name: "no_risk", active: true },
        {
          id: "average",
          name: "average",
          active: true,
        },
        {
          id: "high",
          name: "high",
          active: true,
        },
      ],
      infoOffer: [
        { id: "none", name: "none", active: true },
        { id: "basic", name: "basic", active: true },
        {
          id: "average",
          name: "average",
          active: true,
        },
        {
          id: "high",
          name: "high",
          active: true,
        },
      ],

      contentDirections: [
        { id: "interesting", name: "interesting", active: true },
        { id: "suspense", name: "suspense", active: true },
        { id: "attractive", name: "attractive", active: true },
        { id: "stimulate", name: "stimulate", active: true },
        { id: "fanny", name: "fanny", active: true },
        { id: "shocking", name: "shocking", active: true },
        { id: "story", name: "story", active: true },
        { id: "lure", name: "lure", active: true },
        { id: "surprise", name: "surprise", active: true },
      ],
      voiceOver: [
        { id: "none", name: "none", active: true },
        {
          id: "eloquant_arabic",
          name: "eloquant_arabic",
          active: true,
        },
        {
          id: "gulf_emirate",
          name: "gulf_emirate",
          active: true,
        },
        {
          id: "english",
          name: "english",
          active: true,
        },
      ],
      musics: [
        { id: "none", name: "none", active: true },
        { id: "musical_background", name: "musical_background", active: true },
        { id: "trending_audios", name: "trending_audios", active: true },
        { id: "gulf_song", name: "gulf_song", active: true },
        {
          id: "trending_global_songs",
          name: "trending_global_songs",
          active: true,
        },
      ],
      shootings: [
        { id: "none", name: "none", active: true },
        {
          id: "clip_from_the_internet",
          name: "clip_from_the_internet",
          active: true,
        },
        { id: "local_shooting", name: "local_shooting", active: true },
      ],
      peoples: [
        { id: "none", name: "none", active: true },
        {
          id: "clip_from_the_internet",
          name: "clip_from_the_internet",
          active: true,
        },
        {
          id: "person_infront_of_the_camera",
          name: "person_infront_of_the_camera",
          active: true,
        },
        {
          id: "person_behind_of_the_camera",
          name: "person_behind_of_the_camera",
          active: true,
        },
      ],
      graphics: [
        { id: "none", name: "none", active: true },
        { id: "simple_graphics", name: "simple_graphics", active: true },
        { id: "meduim_graphics", name: "meduim_graphics", active: true },
        { id: "full_graphics", name: "full_graphics", active: true },
      ],
      media: [],
      fetchingCountries: false,
    };
  },
  created() {
    this.getFilterData();
  },

  methods: {
    onSelect(item, column) {
      const data = this.filters.find(
        (row) => row.value == item.id && row.column == column
      );
      if (data == undefined) {
        this.filters.push({ value: item.id, column: column });
      } else {
        this.filters = this.filters.filter((row) => row.value != item.id);
      }
    },
    checkSelected(item, column) {
      const result = this.filters.find(
        (row) => row.value == item.id && row.column == column
      );
      if (result == undefined) {
        return false;
      }
      return true;
    },
    unSelect(index) {
      this.filters.splice(index, 1);
    },
    getItem(index) {
      return this.media[index];
    },

    onItemCodeChange(val) {
      this.onSelect({ id: val }, "item_code");
    },
    typeChange(type) {
      console.log(type);
    },
    dateChanged(date) {
      this.filters = this.filters.filter((row) => row.column != "date");
      this.onSelect({ id: date }, "date");
    },
    async getFilterData() {
      this.fetchingCountries = true;
      await this.fetchCountries();
      await this.fetchCompanies();
      await this.fetchItemCodes();
      this.fetchingCountries = false;
    },
    async fetchCountries() {
      try {
        this.countries = [];

        const url = "advertisement/connection/generate_link/country/all";
        const { data } = await this.$axios.get(url);
        this.countries = data.items;
      } catch (error) {}
    },

    async fetchCompanies() {
      try {
        this.companies = [];

        const url = "connection-companies";
        const { data } = await this.$axios.get(url);
        this.companies = data;
      } catch (error) {}
    },

    async fetchItemCodes() {
      if (this.withPcode) {
        try {
          this.item_codes = [];
          const url = "library/get-itemcodes";
          const { data } = await this.$axios.get(url);
          this.item_codes = data;
        } catch (error) {}
      }
    },
    getSelectedName(id, type) {
      let data = this[type].find((row) => row.id == id);
      if (data != undefined) {
        return data.name;
      }
      return "";
    },
  },
};
</script>

<style scoped>
.custom-card-title-1 {
  font-size: 17px;
  text-transform: uppercase;
  font-weight: 500;
  color: #777;
}
</style>
<style>
.custom-panel .v-expansion-panel-content__wrap {
  padding-left: 6px !important;
  padding-right: 6px !important;
}
</style>
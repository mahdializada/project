<template>
  <div>
    <v-card color="white" v-show="!show_filter" flat>
      <v-card-title class="">
        <div
          style="background-color: #f7f8fc"
          class="d-flex align-center justify-space-between w-full pa-1 rounded"
        >
          <div class="d-flex flex-column">
            <p class="custom-card-title-1 ma-0">{{ $tr("media") }}</p>
          </div>
          <v-spacer></v-spacer>
          <div class="d-flex">
            <div class="d-flex">
              <v-btn
                style="background-color: transparent; width: 28px; height: 28px"
                elevation="0"
                fab
                icon
                plain
                color="primary"
                @click="showFilter()"
                x-small
              >
                <v-badge dot :value="filter.length > 0">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="18.444"
                    height="18.77"
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
                </v-badge>
              </v-btn>
            </div>
          </div>
        </div>
      </v-card-title>
      <v-card-text style="max-height: 90vh; overflow-y: auto">
        <div class="d-flex justify-space-between pb-2">
          <div
            class="count-style rounded-sm"
            :style="`background-color: ${item.color}`"
            v-for="(item, index) in status_counts"
            :key="index"
          >
            <span class="count-num pe-1" style="color: white">
              {{ item.count }}
            </span>
            <span class="count-text" style="color: white">{{
              $tr(item.title)
            }}</span>
          </div>
        </div>
        <div
          style="width: 100%"
          class="d-flex justify-space-between"
          v-if="!loading_media"
        >
          <div class="black--text" style="width: 49%">
            <div v-for="(media, index) in medias" :key="index">
              <MediaItemRohullah
                v-if="index % 2 == 0"
                :media="media"
                @onSelect="onSelect"
                :isSelected="selected_items.includes(media.id)"
              />
            </div>
          </div>
          <div class="black--text" style="width: 49%">
            <div v-for="(media, index) in medias" :key="index">
              <MediaItemRohullah
                v-if="index % 2 == 1"
                :media="media"
                @onSelect="onSelect"
                :isSelected="selected_items.includes(media.id)"
              />
            </div>
          </div>
          <NoRemark
            v-if="(medias.length == 0 || medias == null) && !loading"
            style="position: absolute; top: 350px; left: 20%"
          />
        </div>
        <div style="width: 100%" class="d-flex justify-space-between" v-else>
          <div class="black--text" style="width: 49%">
            <div v-for="(media, index) in 11" :key="index" class="mb-1">
              <v-skeleton-loader
                type="image"
                style="width: 100%; height: 130px"
                v-if="index % 2 == 0"
              ></v-skeleton-loader>
            </div>
          </div>
          <div class="black--text" style="width: 49%">
            <div v-for="(media, index) in 11" :key="index" class="mb-1">
              <v-skeleton-loader
                type="image"
                style="width: 100%; height: 150px"
                v-if="index % 2 == 1"
              ></v-skeleton-loader>
            </div>
          </div>
        </div>
      </v-card-text>
    </v-card>
    <landing-page-media-filter
      @onClick="show_filter = false"
      @filter="applyFilter"
      @close="show_filter = false"
      ref="filterRefs"
      :filter="filter"
      :scroll="true"
      v-show="show_filter"
    />
  </div>
</template>

<script>
import MediaItemRohullah from "../../content-library/content-library-page/MediaItemRohullah.vue";
import NoRemark from "../remarks/NoRemark.vue";
import LandingPageMedia from "./LandingPageMedia.vue";
import LandingPageMediaFilter from "./LandingPageMediaFilter.vue";
import LandingPagePreview from "./LandingPagePreview.vue";
export default {
  components: {
    LandingPagePreview,
    LandingPageMedia,
    LandingPageMediaFilter,
    MediaItemRohullah,
    NoRemark,
  },
  data() {
    return {
      show_filter: false,
      medias: [],
      loading_media: false,
      loading: false,
      selected_item: { pcode: "" },
      filter: [],
      loaded_media: false,
      selected_items: [],
      status_counts: [
        { title: "new", color: "#2196f3", count: 0 },
        { title: "used", color: "#4caf50", count: 0 },
        { title: "rejected", color: "#f44336", count: 0 },
      ],
    };
  },
  mounted() {},
  methods: {
    addLabel() {},
    onSelect(id) {
      if (this.selected_items.includes(id)) {
        this.selected_items = this.selected_items.filter((row) => row != id);
        return;
      }
      this.selected_items.push(id);
    },
    showFilter() {
      this.show_filter = true;
    },
    async applyFilter(filter) {
      this.filter = filter;
      this.show_filter = false;
      console.log("log filter", this.selected_item);
      try {
        let filter_data = {};
        filter.forEach((row) => {
          if (!filter_data[row.column]) filter_data[row.column] = [];
          filter_data[row.column].push(row.value);
        });
        this.loading_media = true;
        let data = {
          item_code:
            this.selected_item.pcode || this.selected_item.product_code,
          filter_data: filter_data,
        };
        const response = await this.$axios.post(
          "library/get-product-media",
          data
        );
        this.medias = response.data;
        this.medias = [...this.medias];
        this.setStatusCount();
      } catch (error) {
        console.error("error", error);
      }
      this.loading_media = false;
    },
    getItem(index) {
      return this.medias[index];
    },
    resetMedia() {
      this.loaded_media = false;
      this.filter = [];
      this.$refs.filterRefs.filters = [];
    },
    async fetchMedia(item) {
      if (this.loaded_media) return;
      this.loaded_media = true;
      try {
        this.loading_media = true;
        this.selected_item = JSON.parse(JSON.stringify(item));

        let data = {
          item_code:
            this.selected_item.pcode || this.selected_item.product_code,
        };
        const response = await this.$axios.post(
          "library/get-product-media",
          data
        );
        this.medias = response.data;
        this.setStatusCount();
      } catch (error) {
        console.error("error", error);
      }
      this.loading_media = false;
    },

    setStatusCount() {
      this.status_counts[0].count = 0;
      this.status_counts[1].count = 0;
      this.status_counts[2].count = 0;
      this.medias.map((record)=>{
        if(record.status == 'not publish')
          this.status_counts[0].count++;
        else if(record.status == 'publish')
          this.status_counts[1].count++;
        else if(record.status == 'rejected')
          this.status_counts[2].count++;
      })
      this.status_counts = [...this.status_counts];
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
.count-style {
  width: 110px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
}
.count-num {
  font-size: 1.1rem;
  font-weight: 500;
}
.count-text {
  font-size: 1rem;
}
</style>

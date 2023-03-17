<template>
  <v-card color="white" elevation="0">
    <v-card-title class="">
      <div
        style="background-color: #f7f8fc"
        class="d-flex align-center justify-space-between w-full pa-1 rounded"
      >
        <div class="d-flex flex-column">
          <p class="custom-card-title-1 ma-0">{{ $tr("landing_page") }}</p>
        </div>
        <v-spacer></v-spacer>
        <div class="d-flex flex-column">
          <v-btn small class="primary" @click="editImages()">
            Edit images
          </v-btn>
          <EditImage ref="editRef" @updateFile="(data) => $emit('updateFile',data)" />
        </div>
        <v-spacer></v-spacer>
        <div class="d-flex">
          <v-btn
            outlined
            small
            rounded
            :href="getLink()"
            target="blank"
            color="primary"
            style="font-size: 15px; font-weight: 400px"
          >
            <v-icon class="" left> mdi-open-in-new</v-icon>
            open</v-btn
          >
        </div>
      </div>
    </v-card-title>
    <v-card-text>
      <CustomCarousel
        :items.sync="profile_data.attachments"
        :height="'200'"
        :show_arrows="true"
        :cycle="false"
      />

      <div class="d-flex flex-column pt-2">
        <p class="custom-card-title-1 mt-0 mb-1">{{ $tr("live_preview") }}</p>

        <v-carousel
          v-model="current_landing_link"
          show-arrows-on-hover
          :show-arrows="getLength() > 1"
          delimiter-icon="mdi-minus"
          hide-delimiters
          hide-delimiter-background
          :cycle="false"
          height="100%"
          class="my-carousel rounded"
        >
          <!-- show-arrows-on-hover -->
          <v-carousel-item
            v-for="(item, i) in profile_data.connections"
            :key="i"
          >
            <iframe
              style="width: 100%; height: 560px"
              :src="item.landing_page_link"
              title="landing page link"
              class="custom-iframe rounded"
            ></iframe>
          </v-carousel-item>

          <template v-slot:prev="{ on, attrs }">
            <v-btn class="" fab x-small color="white" v-bind="attrs" v-on="on">
              <v-icon color="primary">
                {{ $vuetify.rtl ? "mdi-chevron-right" : "mdi-chevron-left" }}
              </v-icon>
            </v-btn>
          </template>
          <template v-slot:next="{ on, attrs }">
            <v-btn class="" fab x-small color="white" v-bind="attrs" v-on="on">
              <v-icon color="primary">
                {{ $vuetify.rtl ? "mdi-chevron-left" : "mdi-chevron-right" }}
              </v-icon>
            </v-btn>
          </template>
        </v-carousel>
        <div
          class="d-flex align-center justify-center pt-1"
          v-if="profile_data.attachments && getLength() > 1"
        >
          <span
            v-for="(item, index) in profile_data.connections"
            :key="index"
            @click="changeSlide('landing_page', index)"
            :class="`rounded-sm  cursor-pointer ${
              index == current_landing_link ? 'primary' : 'grey lighten-1'
            }`"
            style="width: 20px; height: 4.5px; margin: auto 2px"
          ></span>
        </div>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import CustomCarousel from "../../common/CustomCarousel.vue";
import EditImage from "./EditImage.vue";
export default {
  components: { CustomCarousel, EditImage },
  props: {
    loading: Boolean,
    profile_data: Object,
  },
  data() {
    return {
      current_carousel: 0,
      current_landing_link: 0,
    };
  },
  methods: {
    editImages() {
      const data = {
        item_code: this.profile_data.item_code,
        attachments: this.profile_data.attachments,
      };
      this.$refs.editRef.openImageModal(this.$_.cloneDeep(data));
    },
    changeSlide(link = "img", index) {
      if (link == "img") this.current_carousel = index;
      if (link == "landing_page") this.current_landing_link = index;
    },
    getLink() {
      try {
        return this.profile_data.connections[this.current_landing_link]
          .landing_page_link;
      } catch (error) {
        return "";
      }
    },
    getLength() {
      try {
        return this.profile_data.connections.length;
      } catch (error) {
        return 0;
      }
    },
  },
};
</script>

<style scoped>
.custom-iframe {
  border: unset !important ;
}
.custom-card-title-1 {
  font-size: 17px;
  text-transform: uppercase;
  font-weight: 500;
  color: #777;
}
</style>
<style>
.my-carousel .v-window__prev {
  margin: 0 8px !important;
}
.my-carousel .v-window__next {
  margin: 0 8px !important;
}
.v-carousel__controls {
  position: absolute;
  bottom: -30px;
  z-index: 1000 !important;
}
/* .v-window {
  overflow: unset !important;
} */
</style>
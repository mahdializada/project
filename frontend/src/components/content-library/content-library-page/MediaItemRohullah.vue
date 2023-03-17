<template>
  <v-hover v-slot="{ hover }">
    <v-card
      class="w-full black--text rounded-lg overflow-hidden mb-1 cursor-pointer"
      :elevation="hover ? 12 : 0"
      style="background: #f7f8fc; padding: 3px"
    >
      <p class="mb-0 ps-1 pt-0 media-title">
        {{ media.content_library.item_name }}
      </p>
      <div
        class="iframe_container"
        :style="{ paddingTop: getSize(media) + '%' }"
      >
        <!-- <v-overlay
          :absolute="true"
          class="rounded align-start justify-end"
          :value="hover"
        >
          <v-checkbox
            @click.stop="$emit('onSelect', media.id)"
            :input-value="isSelected"
            value
            class="ma-0"
            hide-details
            dense
            color="white"
          ></v-checkbox>
        </v-overlay>

        <v-checkbox
          v-show="!hover && isSelected"
          :input-value="isSelected"
          value
          class="ma-0"
          hide-details
          dense
          color="white"
          style="position: absolute; top: 0; right: 0; z-index: 5"
        ></v-checkbox> -->
        <div
          class="media-label rounded"
          style="top: 10px; bottom: unset; padding: 4px"
        >
          <v-icon color="white">{{
            media.content_library.content_type == "video"
              ? "mdi-video"
              : "mdi-image"
          }}</v-icon>
        </div>

        <div
          :class="`rounded white--text media-label ${
            media.status == 'publish'
              ? 'green'
              : media.status == 'not publish'
              ? 'blue'
              : 'red'
          }`"
        >
          {{
            $tr(
              media.status == "publish"
                ? "used"
                : media.status == "not publish"
                ? "new"
                : "rejected"
            )
          }}
        </div>
        <div
          class="rounded white--text media-label-right"
          @click="openlink(media.media_url)"
        >
          <v-icon color="white">mdi-link</v-icon>
        </div>

        <iframe
          :style="`height: {};`"
          class="custom-iframe rounded"
          :src="media.media_url"
          title="Media"
          style="width: 100%; height: 100%"
        >
        </iframe>
      </div>
    </v-card>
  </v-hover>
</template>

<script>
export default {
  props: {
    media: Object,
    isSelected: Boolean,
  },
  data() {
    return {
      mediaSize: "1080x1920",
      selected_media: [],
    };
  },
  methods: {
    getSize(media) {
      let size = 100;
      switch (media.size) {
        case "1080x1920":
          size = 177.77;
          break;
        case "1080x1350":
          size = 125;
          break;
        case "1080x1080":
          size = 100;
          break;
        case "1920x1080":
          size = 56.25;
          break;
        case "1200x628":
          size = 52.63;
      }
      return size;
    },
    openlink(link) {
      console.log(link);
      window.open(link, "_blank").focus();
    },
  },
};
</script>

<style scoped>
.custom-iframe {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  border: unset !important ;
}
.media-title {
  width: 95%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #777;
  font-size: 14px;
  font-weight: 500;
  padding: 3px;
}
.iframe_container {
  position: relative;
  /* overflow: hidden; */
  width: 100%;
}

.media-label {
  background: rgba(0, 0, 0, 0.5);

  position: absolute;
  bottom: 10px;
  left: 10px;
  padding: 6px 8px;
  z-index: 5;
}
.media-label-right {
  background: rgba(0, 0, 0, 0.5);

  position: absolute;
  bottom: 10px;
  padding: 6px 8px;
  z-index: 5;
  right: 0;
}
</style>

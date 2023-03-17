<template>
  <div>
    <div style="position: relative" v-if="data">
      <div style="border-radius: 10px" v-if="true">
        <v-card class="mx-auto profile_container">
          <div>
            <button @click="$emit('closeProperties')">close</button>
            <div class="p-5">
              <!-- <img
								width="100%"
								
								src="https://cdn.vuetifyjs.com/images/cards/sunshine.jpg"
								alt=""
							/> -->
              <div>
                <iframe
                  class="rounded"
                  :src="data.media_url"
                  title="Media"
                  style="width: 100%; height: 200px"
                >
                </iframe>
              </div>
            </div>

            <div class="d-flex align-center">
              <div class="media_title_icon">
                <v-icon>mdi-filter</v-icon>
              </div>
              <div class="media_title_text">
                <span class="media_text font-weight-medium">{{
                  data ? data?.content_library.item_name : $tr("media_name")
                }}</span>
                <span class="media_content">{{
                  data
                    ? data?.content_library.content_direction
                    : $tr("content_direction")
                }}</span>
              </div>
              <div class="ml-auto">
                <v-switch v-model="switch1" inset></v-switch>
              </div>
            </div>
          </div>
        </v-card>

        <div style="background-color: #f6f8fc" class="px-2 py-1">
          <div class="mt-1">
            <span
              class="text-uppercase font-weight-medium"
              style="opacity: 0.3"
              >{{ $tr("general_information") }}</span
            >
            <div
              class="px-2 py-2 mt-1"
              style="background-color: white; border-radius: 10px"
            >
              <div
                class="d-flex align-center mb-2"
                v-for="(item, index) in general"
                :key="index"
              >
                <div class="mb-auto" style="margin-top: 5px">
                  <div
                    class="media_title_icon media_profile_icon"
                    style="position: relative; background-color: transparent"
                  >
                    <v-icon style="z-index: 2; color: var(--v-primary-base)">{{
                      item.icon || "mdi-filter"
                    }}</v-icon>
                  </div>
                </div>
                <div
                  class="media_title_text pb-2"
                  style="width: 100%; border-bottom: 1px solid #e5e5e5"
                >
                  <span
                    class="media_text font-weight-regular"
                    style="opacity: 0.5"
                    >{{ item.title }}</span
                  >
                  <span class="media_content" v-if="item">
                    {{
                      item.value == "media_size"
                        ? data
                          ? data[data.value]
                          : item.value
                        : data.content_library[item.value]
                    }}</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["item"],
  watch: {
    item: function (data) {
      this.data = data;
    },
  },
  data() {
    return {
      switch1: null,
      data: null,
      general: [
        {
          title: "source",
          value: "content_source",
          icon: "mdi-account-box",
        },
        {
          title: "type",
          value: "content_type",
          icon: "mdi-image",
          icon2: "mdi-video",
        },
        {
          title: "language",
          value: "content_language",
          icon: "mdi-web",
        },
        {
          title: "content_size",
          value: "media_size",
          icon: "mdi-open-in-new",
        },
        {
          title: "requested_when",
          value: "requested_when",
          icon: "mdi-timer-sand",
        },
      ],
    };
  },
  methods: {},
};
</script>

<style scoped>
.media_title_icon {
  margin-right: 10px;
  background-color: #b2b2b2;
  min-width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white !important;
}

.media_title_text {
  display: flex;
  flex-direction: column;
}
</style>

<style>
.profile_container {
  box-shadow: none !important;
  padding: 10px;
  height: 100% !important;
}
</style>

<template>
  <v-menu
    offset-y
    v-model="menu"
    :close-on-content-click="false"
    :close-on-click="false"
    class="elevation-12"
    nudge-left="270"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn
        @click="editProperty(selectedItem)"
        plain
        small
        text
        icon
        color="primary"
        ><v-icon>mdi-pencil</v-icon></v-btn
      >
    </template>
    <div class="pa-2 white" style="width: 300px">
      <p class="primary--text text-subtitle-1">Edit</p>
      <v-select
        :items="items"
        outlined
        item-value="id"
        item-text="value"
        v-model="updatedValue"
        dense
        filled
        hide-details
        class="mb-2"
      >
        <template v-slot:[`selection`]="{ item }">
          {{ $tr(item.value) }}
        </template>
        <template v-slot:[`item`]="{ item, on, attrs }">
          {{ $tr(item.value) }}
        </template>
      </v-select>

      <div class="d-flex justify-space-between mt-3">
        <v-btn
          class="px-2 py-1"
          rounded
          text
          small
          @click="closeMenu()"
          color="primary"
          style="font-size: 14px; font-weight: 400"
          >{{ $tr("cancle") }}</v-btn
        >
        <v-btn
          class="px-2 py-1"
          @click="updateColumn()"
          :loading="loading"
          rounded
          small
          color="primary"
          style="font-size: 14px; font-weight: 400"
          >{{ $tr("save") }}</v-btn
        >
      </div>
    </div>
  </v-menu>
</template>

<script>
export default {
  props: {
    selectedItem: {
      required: true,
    },
    property: {
      required: true,
    },
    media_data: {
      required: true,
    },
  },
  data() {
    return {
      // content info
      menu: false,
      loading: false,
      updatedValue: "",

      requested_when: [
        {
          id: "product_is_sold_for_the_first_time",
          value: "product_is_sold_for_the_first_time",
        },
        {
          id: "during_the_sales_phase",
          value: "during_the_sales_phase",
        },
      ],
      content_source: [
        {
          id: "creation",
          value: "creation",
        },
        {
          id: "copy",
          value: "copy",
        },
      ],
      content_type: [
        {
          id: "video",
          value: "video",
        },
        {
          id: "banner",
          value: "banner",
        },
        {
          id: "animate_banner",
          value: "animate_banner",
        },
      ],
      content_language: [
        {
          id: "english",
          value: "english",
        },
        {
          id: "UAE",
          value: "UAE",
        },
      ],

      //     design info
      content_direction: [
        {
          id: "interesting",
          value: "interesting",
        },
        {
          id: "suspense",
          value: "suspense",
        },
        {
          id: "attractive",
          value: "attractive",
        },
        {
          id: "stimulate",
          value: "stimulate",
        },
        {
          id: "fanny",
          value: "fanny",
        },
        {
          id: "shocking",
          value: "shocking",
        },
        {
          id: "story",
          value: "story",
        },
        {
          id: "lure",
          value: "lure",
        },
        {
          id: "surprise",
          value: "surprise",
        },
      ],
      voice_over: [
        {
          id: "none",
          value: "none",
        },
        {
          id: "eloquant_arabic",
          value: "eloquant_arabic",
        },
        {
          id: "gulf_emirate",
          value: "gulf_emirate",
        },
        {
          id: "english",
          value: "english",
        },
      ],
      music: [
        {
          id: "none",
          value: "none",
        },

        {
          id: "musical_background",
          value: "musical_background",
        },
        {
          id: "trending_audios",
          value: "trending_audios",
        },
        {
          id: "gulf_song",
          value: "gulf_song",
        },
        {
          id: "trending_global_songs",
          value: "trending_global_songs",
        },
      ],
      shooting: [
        {
          id: "none",
          value: "none",
        },
        {
          id: "clip_from_the_internet",
          value: "clip_from_the_internet",
        },
        {
          id: "local_shooting",
          value: "local_shooting",
        },
      ],
      people: [
        {
          id: "none",
          value: "none",
        },
        {
          id: "clip_from_the_internet",
          value: "clip_from_the_internet",
        },
        {
          id: "person_infront_of_the_camera",
          value: "person_infront_of_the_camera",
        },
        {
          id: "person_behind_of_the_camera",
          value: "person_behind_of_the_camera",
        },
      ],
      graphics: [
        {
          id: "none",
          value: "none",
        },
        {
          id: "simple_graphics",
          value: "simple_graphics",
        },
        {
          id: "meduim_graphics",
          value: "meduim_graphics",
        },
        {
          id: "full_graphics",
          value: "full_graphics",
        },
      ],

      //     marketing info

      season: [
        {
          id: "yes",
          value: "yes",
        },
        {
          id: "no",
          value: "no",
        },
      ],
      main_or_customer: [
        {
          id: "yes",
          value: "yes",
        },
        {
          id: "no",
          value: "no",
        },
      ],
      risk_policy: [
        {
          id: "no_risk",
          value: "no_risk",
        },
        {
          id: "average",
          value: "average",
        },
        {
          id: "high",
          value: "high",
        },
      ],
      info_offer: [
        {
          id: "none",
          value: "none",
        },
        {
          id: "basic",
          value: "basic",
        },
        {
          id: "average",
          value: "average",
        },
        {
          id: "high",
          value: "high",
        },
      ],

      // media size
      media_size: [
        {
          id: "story 1080x1920",
          value: "story 1080x1920",
        },
        {
          id: "instagram_portrait 1080x1350",
          value: "instagram_portrait 1080x1350",
        },
        {
          id: "instagram_post 1080x1080",
          value: "instagram_post 1080x1080",
        },
        {
          id: "youtube 1920x1080",
          value: "youtube 1920x1080",
        },
        {
          id: "facebook 1200x628",
          value: "facebook 1200x628",
        },
      ],
    };
  },
  computed: {
    items() {
      return this[this.property];
    },
  },
  methods: {
    editProperty(data) {
      this.$emit("onClick");
      this.menu = true;
    },
    async updateColumn() {
      console.log("log cl", this.updatedValue);
      try {
        if (this.updatedValue == this.selectedItem[this.property]) {
          return;
        }
        this.loading = true;
        const data = {
          id: this.selectedItem?.id,
          media_id: this.media_data.id,
          property: this.property,
          value: this.updatedValue,
        };
        const response = await this.$axios.put("library/edit-property", data);
        if (response.status == 200) {
          if (data.property == "media_size") {
            this.media_data[this.property] = data.value;
          } else {
            this.selectedItem[this.property] = data.value;
          }
          this.closeMenu();
        } else {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));

        console.log("eerrr", error);
      }
      this.loading = false;
    },
    closeMenu() {
      this.menu = false;
      this.$emit("onClose");
    },
  },
  created() {
    if (this.property == "media_size") {
      this.updatedValue = this.$_.cloneDeep(this.media_data[this.property]);
      return;
    }
    this.updatedValue = this.$_.cloneDeep(this.selectedItem[this.property]);
    console.log(this.property);
    console.log("created", this.selectedItem);
    console.log("2ddd", this.media_data);
  },
};
</script>

<style>
</style>
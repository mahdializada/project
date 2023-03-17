<template>
  <div>
    <v-hover v-slot="{ hover }">
      <div
        class="media_container on_hover"
        :class="isSelected ? 'selected_item' : ''"
      >
        <div class="media_title" style="cursor: pointer">
          <div class="d-flex align-center" @click="$emit('showProfile')">
            <div class="media_title_icon">
              <v-icon v-if="item?.content_library?.content_type == 'image'"
                >mdi-image</v-icon
              >
              <v-icon v-else>mdi-video</v-icon>
            </div>
            <div class="media_title_text">
              <span
                class="media_text font-weight-medium"
                style="text-overflow: ellipsis; line-height: 1.5"
                >{{
                  $tr(item?.content_library?.item_name) || $tr("media_name")
                }}</span
              >
              <span class="media_content">
                {{
                  $tr(item?.content_library?.content_direction) ||
                  $tr("interesting_content")
                }}
              </span>
            </div>
          </div>

          <v-checkbox
            v-if="hover || isSelected"
            :value="isSelected"
            class="item_check"
            @click="$emit('onItemClicked')"
            label=""
          ></v-checkbox>
        </div>

        <div class="media_img" style="cursor: pointer">
          <v-btn
            @click="confirmFavorite"
            class="add_favorite"
            :class="item.is_favorite_count == 1 ? 'show_favorite favorite' : ''"
            icon
            color="white"
          >
            <v-icon
              >mdi-{{
                item.is_favorite_count == 1 ? "star" : "star-outline"
              }}</v-icon
            >
          </v-btn>
          <div v-if="hover" class="media_details" @click="openProfileDialog">
            <div class="iconic_details d-flex justify-space-between">
              <div class="d-flex flex-column flex-sm-wrap">
                <!-- country -->
                <div class="ml-1 icon_container" v-if="item">
                  <v-hover v-slot="{ hover }">
                    <div>
                      <div class="mb-1 icon_holder">
                        <FlagIcon
                          :flag="
                            item.content_library.country?.iso2.toLowerCase()
                          "
                          :round="true"
                        />
                      </div>
                      <div class="iconic_details_tooltip" v-if="hover">
                        <p class="hover_title">{{ $tr("country") }}</p>
                        <p class="hover_text">
                          {{ item.content_library.country.name }}
                        </p>
                      </div>
                    </div>
                  </v-hover>
                </div>
                <!-- end country -->
                <div v-if="item">
                  <div
                    class="ml-1 icon_container"
                    v-for="(detail, index) in details"
                    :key="index"
                  >
                    <v-hover v-slot="{ hover }">
                      <div>
                        <div class="mb-1 icon_holder">
                          <v-icon color="white"> {{ detail.icon }}</v-icon>
                        </div>
                        <div class="iconic_details_tooltip" v-if="hover">
                          <p class="hover_title">{{ $tr(detail.title) }}</p>
                          <p class="hover_text">
                            {{
                              item
                                ? $tr(item.content_library[detail.value])
                                : $tr(detail.value)
                            }}
                          </p>
                        </div>
                      </div>
                    </v-hover>
                  </div>
                </div>
                <div class="ml-1">
                  <v-hover v-slot="{ hover }">
                    <div
                      class="rest_details d-flex justify-center align-center"
                      style="position: relative"
                    >
                      <span class="font-weight-medium">+11</span>
                      <div class="iconic_details_tooltip" v-if="hover">
                        <p class="hover_title">{{ $tr("more_details") }}</p>
                        <p class="hover_text">{{ $tr("more_details") }}</p>
                      </div>
                    </div>
                  </v-hover>
                </div>
                <div class="ml-1 mt-1">
                  <v-hover v-slot="{ hover }">
                    <div
                      class="rest_details d-flex justify-center align-center"
                      style="position: relative"
                      @click="openProfileDialog(item.project_url)"
                    >
                      <v-icon>mdi-link</v-icon>
                      <div class="iconic_details_tooltip" v-if="hover">
                        <h4 class="hover_title">{{ $tr("project_url") }}</h4>
                      </div>
                    </div>
                  </v-hover>
                </div>
              </div>

              <div></div>
            </div>

            <div class="media_company align-end">
              <div class="d-flex align-center">
                <div class="media_company_logo">
                  <img
                    width="100%"
                    :src="
                      item && item.content_library
                        ? item.content_library.company.logo
                        : 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT-2fhSxs3xjOOJmxnVnHWhvkzeIpI4yj9KSbVQlXuEvvG9wiiLGDwPixIQQArsYZAFIX8&usqp=CAU'
                    "
                    alt="logo"
                  />
                </div>
                <div class="media_company_text">
                  <span class="media_text font-weight-bold">{{
                    item && item.content_library
                      ? item.content_library.company.name
                      : $tr("company_name")
                  }}</span>
                  <span class="media_size">{{
                    item ? item.media_size : $tr("media_size")
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="iframe_container" :style="{ paddingTop: setSize + '%' }">
            <iframe
              class="iframe_item"
              :src="item.media_url ? item.media_url : ''"
              title="Media"
              style="width: 100%; height: 100%"
            >
            </iframe>
          </div>
          <!-- <img v-else width="100%" :src="src" alt="media" /> -->
        </div>
      </div>
    </v-hover>
  </div>
</template>

<script>
import ProfileDialog from "./ProfileDialog.vue";
import FlagIcon from "../../common/FlagIcon.vue";

export default {
  components: { ProfileDialog, FlagIcon },
  props: ["item", "selectedItems"],
  emits: ["showProfile"],
  watch: {
    selectedItems: function (val) {
      this.isSelected = val.includes(this.item);
    },
  },
  computed: {},
  data() {
    return {
      isSelected: false,
      itemCheck: false,
      isFavorite: false,
      mediaSize: "1080x1920",
      setSize: "1200x628",
      details: [
        {
          title: "music",
          value: "music",
          icon: "mdi-music-note-eighth",
        },
        {
          title: "shooting",
          value: "shooting",
          icon: "mdi-film",
        },
        {
          title: "people",
          value: "people",
          icon: "mdi-account-multiple",
        },
        {
          title: "graphics",
          value: "graphics",
          icon: "mdi-auto-fix",
        },
      ],
    };
  },

  mounted() {
    this.getSize();
  },

  methods: {
    confirmFavorite() {
      if (this.item.is_favorite_count == 1) {
        this.$TalkhAlertNA(
          "Are you sure to remove from favorite ?", //text
          "delete", //icon
          async () => {
            this.addToFavorite();
          }, // callback function
          "yes_remove" // btn text
        );
      } else {
        this.addToFavorite();
      }
    },
    async addToFavorite() {
      try {
        if (this.item.is_favorite_count == 1) {
          this.item.is_favorite_count = 0;
        } else {
          this.item.is_favorite_count = 1;
        }
        const result = await this.$axios.post("library/add-to-favorite", {
          id: this.item.id,
          favorite: this.item.is_favorite_count,
        });
        if (result.status != 200) {
          this.$toasterNA("red", this.$tr("something_went_wrong"));
          if (this.item.is_favorite_count == 1) {
            this.item.is_favorite_count = 0;
          } else {
            this.item.is_favorite_count = 1;
          }
        }
        this.$emit("onFavorite", this.item.id);
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },
    openProfileDialog(link) {
      if (!link?.type && link?.includes("https://www.canva.com")) {
        window.open(link, "_blank");
      } else this.$emit("showCamparingDialog");
    },
    getSize() {
      let size;
      if (this.item) this.mediaSize = this.item.size;
      switch (this.mediaSize) {
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
      this.setSize = size;
    },
  },
};
</script>

<style scoped>
/* .media_text {
  
} */

.rest_details {
  width: 30px;
  height: 30px;
  background-color: rgba(0, 0, 0, 0.4);
  border-radius: 5px;
  color: white;
}

.media_container {
  background-color: #f3f3f3;
  padding: 10px;
  border-radius: 15px;
  transition: 0.2s;
  border: 1px solid transparent;
  /* margin-top   : 10px; */
}

.add_favorite {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 4;
  display: none;
}

.on_hover:hover .add_favorite {
  display: block;
}

.show_favorite {
  display: block !important;
}

.favorite {
  background-color: rgba(0, 0, 0, 0.3);
}

.media_container.on_hover:hover {
  background-color: white !important;
}

.on_hover:hover {
  box-shadow: 2px 10px 25px -2px rgba(0, 0, 0, 0.21);
  -webkit-box-shadow: 2px 10px 25px -2px rgba(0, 0, 0, 0.21);
  -moz-box-shadow: 2px 10px 25px -2px rgba(0, 0, 0, 0.21);
}

.selected_item {
  border: 1px solid #2e7be6;
  background-color: white;
}

.media_img {
  overflow: hidden;
  border-radius: 10px;
  position: relative;
}

.media_company {
  height: 30%;
  display: flex;
  padding: 5px 0 10px 0;
  justify-content: flex-start;
  align-items: center;
  justify-self: end;
  /* z-index: 10; */
  /* align-items: flex-end; */
}

.media_company_text {
  display: flex;
  flex-direction: column;
  color: white;
}

.media_company_text .media_size {
  margin-top: 20px;
  font-size: 0.8rem;
}

.media_company_logo {
  width: 40px;
  height: 40px;
  background-color: white;
  border-radius: 50%;
  overflow: hidden;
  margin-right: 10px;
}

.media_title {
  display: flex;
  padding: 5px 0 10px 0;
  justify-content: flex-start;
  align-items: center;
  margin-bottom: 10px;
}

.media_title_text {
  display: flex;
  flex-direction: column;
}

.media_title_text .media_content {
  margin-top: 10px;
  font-size: 0.8rem;
}

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

.media_details {
  position: absolute;
  width: 100%;
  height: 100%;
  background-image: linear-gradient(
    180deg,
    rgba(0, 0, 0, 0.5),
    rgba(0, 0, 0, 0.1),
    rgba(0, 0, 0, 0.5)
  );
  top: 0;
  left: 0;
  /* opacity: 0.4; */
  display: flex;
  flex-direction: column;
  /* align-items: flex-end; */
  justify-content: space-between;
  padding: 10px;
  z-index: 3;
}

.iconic_details {
  height: 70%;
  /* display: flex;
    flex-direction: column;
    justify-content: start;
    align-items: flex-start; */
}

.icon_holder {
  background: rgba(0, 0, 0, 0.3);
  padding: 3px;
  border-radius: 5px;
  display: inline-block;
  /* position: relative; */
}

.iconic_details_tooltip {
  color: black !important;
  position: absolute;
  top: 0;
  left: 40px;
  padding: 15px;
  background-color: white;
  border-radius: 5px;
  width: max-content;
  z-index: 10;
}

.iconic_details_tooltip p {
  word-break: keep-all !important;
  word-wrap: normal !important;
}

.iconic_details_tooltip::after {
  content: " ";
  position: absolute;
  top: 30%;
  right: 100%;
  /* To the left of the tooltip */
  margin-top: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent white transparent transparent;
}

.iconic_details_tooltip p {
  margin-bottom: 0;
}

.iconic_details_tooltip p:first-child {
  margin-bottom: 20px;
}

.iconic_details_tooltip > .hover_title {
  font-size: 0.8rem;
  opacity: 0.6;
  /* margin-bottom: 15px; */
}

.iframe_container {
  position: relative;
  overflow: hidden;
  width: 100%;
  /* padding-top: 177%; */
}
.iframe_item {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  border: none;
}
</style>

<style>
.media_title_icon i {
  color: white !important;
}

.iconic_details i {
  color: white !important;
}

.icon_container {
  position: relative;
}

.item_check {
  padding: 0 !important;
  margin-top: 0 !important;
  margin-left: auto;
  margin-right: 10px;
}

.item_check .v-messages.theme--light {
  display: none;
}

.item_check .v-input__slot,
.item_check .v-input--selection-controls__input {
  margin: 0 !important;
}

/* .v-tooltip .v-tooltip--right{
    background-color: red !important;
} */
</style>

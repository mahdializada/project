<template>
  <!-- <v-dialog
    v-model="dialog"
    persistent
    max-width="1250"
    style="position: relative; overflow: auto"
  > -->
  <div v-show="dialog">
    <div id="overlay" class="d-flex align-center justify-center">
      <div class="white rounded" style="height: 750px; position: relative">
        <HorizentalTab
          :tabs="tabs"
          :selected_profile.sync="selected_profile"
          @onTabChange="onTabChange($event)"
        />
        <v-tabs-items v-model="selected_profile">
          <v-tab-item>
            <div>
              <v-card
                class="d-flex profileDialog overflow-hidden"
                elevation="0"
              >
                <div class="d-flex flex-column" style="width: 75%">
                  <div class="d-flex" style="background-color: #f6f8fc">
                    <div
                      style="background-color: white; width: 70px; height: 88px"
                      class="d-flex justify-center align-center"
                    >
                      <v-btn icon @click="showDialog">
                        <v-icon style="font-size: 1.8rem; opacity: 0.8"
                          >mdi-close-circle-outline</v-icon
                        >
                      </v-btn>
                    </div>
                    <div
                      class="d-flex align-center pa-2"
                      style="
                        background-color: white;
                        width: 92%;
                        margin: 0 10px;
                      "
                    >
                      <div>
                        <div class="media_title">
                          <div class="d-flex align-center">
                            <div class="media_title_icon">
                              <v-icon>{{
                                contentData &&
                                contentData?.content_type == "video"
                                  ? "mdi-video"
                                  : "mdi-image"
                              }}</v-icon>
                            </div>
                            <div class="media_title_text">
                              <span class="media_text font-weight-medium">{{
                                contentData
                                  ? $tr(contentData?.item_name)
                                  : "Media Name"
                              }}</span>
                              <span class="media_content">{{
                                contentData
                                  ? $tr(contentData?.content_direction)
                                  : "Interesting Content"
                              }}</span>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div>
                        <!-- header image place -->
                      </div>
                    </div>
                  </div>

                  <template>
                    <v-card class="dialog-card">
                      <v-tabs
                        vertical
                        class="verticalTaps"
                        style="height: 100%"
                        v-model="comment_tab"
                      >
                        <div class="custom_taps_holder">
                          <v-tab
                            style="
                              padding: 0;
                              margin: 0 10px;
                              min-width: 50px;
                              border-radius: 5px;
                            "
                          >
                            <v-icon> mdi-play-box-outline </v-icon>
                          </v-tab>

                          <v-tab
                            style="
                              padding: 0;
                              margin: 0 10px;
                              min-width: 50px;
                              border-radius: 5px;
                            "
                          >
                            <v-icon> mdi-comment </v-icon>
                          </v-tab>

                          <v-tab
                            style="
                              padding: 0;
                              margin: 0 10px;
                              min-width: 50px;
                              border-radius: 5px;
                            "
                          >
                            <v-icon> mdi-label </v-icon>
                          </v-tab>
                        </div>

                        <v-tab-item transition="slide-y-reverse-transition">
                          <v-card
                            flat
                            class="d-flex flex-column"
                            style="height: 100%"
                          >
                            <v-card-text>
                              <iframe
                                v-if="!loadingMedia"
                                :src="
                                  mediaData &&
                                  mediaData[selectedMedia]?.media_url
                                    ? mediaData[selectedMedia]?.media_url
                                    : ''
                                "
                                title="Media"
                                style="width: 100%; height: 380px"
                                class="iframe_video"
                              >
                              </iframe>
                              <v-skeleton-loader
                                v-else
                                class="iframe_video"
                                type="image"
                              ></v-skeleton-loader>
                            </v-card-text>

                            <div
                              class="mx-2"
                              style="
                                background-color: white;
                                height: 242px;
                                padding: 10px;
                              "
                            >
                              <div class="related_video_tabs">
                                <span class="mb-1 d-block">{{
                                  $tr("related_media")
                                }}</span>
                                <template style="background-color: transparent">
                                  <v-card class="related_video_bg pa-1">
                                    <div class="d-flex" v-if="loadingMedia">
                                      <v-skeleton-loader
                                        v-for="i in 4"
                                        :key="i"
                                        class="mx-1"
                                        height="140"
                                        width="200"
                                        type="card"
                                      ></v-skeleton-loader>
                                    </div>
                                    <v-tabs
                                      center-active
                                      dark
                                      v-else
                                      v-model="selectedMedia"
                                    >
                                      <v-tab
                                        v-for="(item, index) in mediaData"
                                        :key="index"
                                      >
                                        <div
                                          style="
                                            border: 1px solid transparent;
                                            background-color: white;
                                            padding: 5px;
                                            margin: 5px;
                                            overflow: hidden;
                                          "
                                          class="rounded"
                                        >
                                          <div
                                            style="
                                              /* width: 140px; */
                                              width: 140px;
                                              height: 95px;
                                              overflow: hidden;
                                              border-radius: 5px;
                                              position: relative;
                                            "
                                          >
                                            <div class="playing_active">
                                              <v-icon>
                                                mdi-play-outline
                                              </v-icon>
                                            </div>
                                            <iframe
                                              class="disable-iframe"
                                              @click="selectedMedia = index"
                                              :src="
                                                item.media_url
                                                  ? item.media_url
                                                  : ''
                                              "
                                              height="100%"
                                              width="100"
                                            >
                                            </iframe>
                                          </div>
                                          <div
                                            class="related_video_text mt-1"
                                            style="color: black"
                                          >
                                            <span
                                              class="
                                                font-weight-medium
                                                text-lowercase
                                              "
                                              style="
                                                letter-spacing: normal;
                                                word-wrap: break-all;
                                              "
                                            >
                                              {{
                                                item?.media_size
                                                  ? item?.media_size
                                                  : "media_size"
                                              }}
                                            </span>
                                          </div>
                                        </div>
                                      </v-tab>
                                    </v-tabs>
                                  </v-card>
                                </template>
                              </div>
                            </div>
                          </v-card>
                        </v-tab-item>
                        <v-tab-item transition="slide-y-reverse-transition">
                          <RemarkTemplate
                            :selected_items.sync="selected_items"
                            ref="remarkTemplateRefs"
                          />
                        </v-tab-item>

                        <v-tab-item transition="slide-y-reverse-transition">
                          <LabelTemplate
                            :selected_item.sync="selected_items"
                            ref="labelTemplateRefs"
                          />
                        </v-tab-item>
                      </v-tabs>
                    </v-card>
                  </template>
                </div>

                <div
                  style="width: 25%; height: 100%"
                  class="d-flex flex-column"
                >
                  <template>
                    <div class="info_tabs">
                      <v-tabs
                        class="pt-2"
                        v-model="marketing_tab"
                        centered
                        icons-and-text
                      >
                        <v-tabs-slider></v-tabs-slider>

                        <v-tab v-ripple="false">
                          {{ $tr("general") }}
                          <v-icon>mdi-information</v-icon>
                        </v-tab>

                        <v-tab v-ripple="false">
                          {{ $tr("marketing") }}
                          <v-icon>mdi-cart</v-icon>
                        </v-tab>

                        <v-tab v-ripple="false">
                          {{ $tr("design") }}
                          <v-icon>mdi-pencil-ruler</v-icon>
                        </v-tab>

                        <v-tab-item>
                          <v-card flat class="pa-2">
                            <div
                              class="mt-2"
                              v-for="(item, index) in general"
                              :key="index"
                            >
                              <v-hover v-slot="{ hover }">
                                <div>
                                  <div
                                    class="
                                      d-flex
                                      align-start
                                      justify-space-between
                                    "
                                  >
                                    <div class="d-flex">
                                      <div class="info_item">
                                        <v-icon
                                          style="color: var(--v-primary-base)"
                                          >{{ item.icon }}</v-icon
                                        >
                                      </div>

                                      <div class="d-flex flex-column">
                                        <span
                                          style="
                                            font-size: 0.9rem;
                                            opacity: 0.7;
                                          "
                                          >{{ $tr(item.title) }}</span
                                        >

                                        <span
                                          class="font-weight-medium"
                                          v-if="contentData"
                                        >
                                          {{
                                            item.value == "media_size"
                                              ? mediaData.length > 0 &&
                                                mediaData[0].media_size
                                                ? mediaData[selectedMedia][
                                                    item.value
                                                  ]
                                                : $tr(item.value)
                                              : $tr(contentData[item.value])
                                          }}
                                        </span>
                                        <span
                                          class="font-weight-medium"
                                          v-else
                                          >{{ $tr(item.value) }}</span
                                        >
                                      </div>
                                    </div>
                                    <ContentLibraryEdit
                                      :selectedItem="contentData"
                                      :property="item.value"
                                      :media_data="mediaData[selectedMedia]"
                                      v-if="hover || index == selected_index"
                                      @onClick="selectItem(index)"
                                      @onClose="selectItem(null)"
                                    ></ContentLibraryEdit>
                                  </div>
                                </div>
                              </v-hover>
                            </div>
                          </v-card>
                        </v-tab-item>

                        <v-tab-item class="pa-2">
                          <v-card flat>
                            <div
                              class="mt-2"
                              v-for="(item, index) in marketing"
                              :key="index"
                            >
                              <v-hover v-slot="{ hover }">
                                <div>
                                  <div
                                    class="
                                      d-flex
                                      align-start
                                      justify-space-between
                                    "
                                  >
                                    <div class="d-flex">
                                      <div class="info_item">
                                        <v-icon
                                          style="color: var(--v-primary-base)"
                                          >{{
                                            item.icon || "mdi-account-box"
                                          }}</v-icon
                                        >
                                      </div>
                                      <div class="d-flex flex-column">
                                        <span
                                          style="
                                            font-size: 0.9rem;
                                            opacity: 0.7;
                                          "
                                          >{{ $tr(item.title) }}</span
                                        >
                                        <span
                                          class="font-weight-medium"
                                          v-if="contentData"
                                          >{{
                                            $tr(contentData[item.value])
                                          }}</span
                                        >
                                      </div>
                                    </div>

                                    <ContentLibraryEdit
                                      :selectedItem="contentData"
                                      :property="item.value"
                                      :media_data="mediaData[selectedMedia]"
                                      v-if="hover || index == selected_index"
                                      @onClick="selectItem(index)"
                                      @onClose="selectItem(null)"
                                    ></ContentLibraryEdit>
                                  </div>
                                </div>
                              </v-hover>
                            </div>
                          </v-card>
                        </v-tab-item>

                        <v-tab-item class="pa-2">
                          <v-card flat>
                            <div
                              class="mt-2"
                              v-for="(item, index) in design"
                              :key="index"
                            >
                              <v-hover v-slot="{ hover }">
                                <div>
                                  <div class="d-flex justify-space-between">
                                    <div class="d-flex">
                                      <div class="info_item">
                                        <v-icon
                                          style="color: var(--v-primary-base)"
                                          >{{
                                            item.icon || "mdi-account-box"
                                          }}</v-icon
                                        >
                                      </div>
                                      <div class="d-flex flex-column">
                                        <span
                                          style="
                                            font-size: 0.9rem;
                                            opacity: 0.7;
                                          "
                                          >{{ $tr(item.title) }}</span
                                        >
                                        <span
                                          class="font-weight-medium"
                                          v-if="contentData"
                                          >{{
                                            $tr(contentData[item.value])
                                          }}</span
                                        >
                                      </div>
                                    </div>
                                    <ContentLibraryEdit
                                      :selectedItem="contentData"
                                      :property="item.value"
                                      :media_data="mediaData[selectedMedia]"
                                      v-if="hover || index == selected_index"
                                      @onClick="selectItem(index)"
                                      @onClose="selectItem(null)"
                                    ></ContentLibraryEdit>
                                  </div>
                                </div>
                              </v-hover>
                            </div>
                          </v-card>
                        </v-tab-item>
                      </v-tabs>
                    </div>
                  </template>

                  <div class="pa-2 d-flex flex-column" style="height: 40%">
                    <div class="d-flex align-center">
                      <span class="mr-2">Tags</span>
                      <v-divider class="mr-2"></v-divider>
                      <v-btn
                        color="red"
                        fab
                        small
                        style="width: 30px; height: 30px; margin-right: 5px"
                        @click="closeTagEdit()"
                        v-show="editTags"
                      >
                        <v-icon style="font-size: 1.1rem; color: white">
                          mdi-window-close
                        </v-icon>
                      </v-btn>

                      <v-btn
                        color="primary"
                        fab
                        small
                        style="width: 30px; height: 30px"
                        @click="saveTags()"
                        :loading="submitting"
                      >
                        <v-icon style="font-size: 1.1rem">
                          {{ editTags ? "mdi-check-bold" : "mdi-pencil" }}
                        </v-icon>
                      </v-btn>
                    </div>

                    <div
                      class="addTag d-flex justify-center align-center"
                      v-if="editTags"
                    >
                      <CComboBoxOnly
                        :placeholder="$tr('choose_item', $tr('area'))"
                        prepend-inner-icon="fa fa-tag"
                        :canAddNewItem="true"
                        :items="tags"
                        v-model="mediaData[selectedMedia].tags"
                        :title="$tr('label_required', $tr('area'))"
                        @onChange="onNewEreaAded"
                      />
                    </div>

                    <div class="mt-1" style="height: 100%; overflow-y: auto">
                      <div
                        class="tags_chips d-flex pt-1 flex-wrap align-start"
                        style="gap: 5px"
                        v-if="!loadingMedia"
                      >
                        <div
                          v-for="(tag, index) in loopTags(
                            mediaData[selectedMedia]
                          )"
                          :key="index"
                        >
                          <v-chip
                            v-if="true"
                            :close="editTags"
                            outlined
                            @click:close="deleteTag(tag)"
                          >
                            {{ tag }}
                          </v-chip>
                        </div>
                      </div>
                      <div v-if="loadingMedia" class="d-flex flex-wrap">
                        <span v-for="item in 7" :key="item">
                          <v-skeleton-loader
                            type="chip"
                            class="me-1 mb-1"
                          ></v-skeleton-loader>
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
              </v-card>
            </div>
          </v-tab-item>
          <v-tab-item>
            <ComparingGraph
              ref="comparingGraphRefs"
              :contentData.sync="selected_items"
              @close="showDialog()"
            />
          </v-tab-item>
        </v-tabs-items>
      </div>
    </div>
  </div>

  <!-- </v-dialog> -->
</template>

<script>
import ProfileHorizentalTab from "../../advertisement/graph/ProfileHorizentalTab.vue";
import LabelTemplate from "../../advertisement/label/LabelTemplate.vue";
import RemarkTemplate from "../../advertisement/remarks/RemarkTemplate.vue";
import CAutoComplete from "../../new_form_components/Inputs/CAutoComplete.vue";
import CComboBoxOnly from "../../new_form_components/Inputs/CComboBoxOnly.vue";
import ComparingGraph from "./ComparingGraph.vue";
import ContentLibraryEdit from "./ContentLibraryEdit.vue";
import HorizentalTab from "./HorizentalTab.vue";
import MediaSkeleton from "./MediaSkeleton.vue";
export default {
  components: {
    MediaSkeleton,
    ProfileHorizentalTab,
    HorizentalTab,
    ComparingGraph,
    RemarkTemplate,
    LabelTemplate,
    CAutoComplete,
    CComboBoxOnly,
    ContentLibraryEdit,
  },
  mounted() {
    // this.gallryLayout();
  },
  props: { filter_type: String },
  data() {
    return {
      submitting: false,
      selected_index: null,
      tags: [],
      selected_profile: 0,
      dialog: false,
      comment_tab: 0,
      marketing_tab: 0,
      editTags: false,
      key: 0,
      mediaData: [{ tags: "" }],
      contentData: {},
      selected_items: {},
      loadingMedia: false,
      selectedMedia: 0,
      tagValue: "",

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
      marketing: [
        {
          title: "season",
          value: "season",
          icon: "mdi-salesforce",
        },
        {
          title: "risk_palicy",
          value: "risk_palicy",
          icon: "mdi-ubisoft",
        },
        {
          title: "main_or_customer",
          value: "main_or_customer",
          icon: "mdi-account",
        },
        {
          title: "info_offer",
          value: "info_offer",
          icon: "mdi-note-alert",
        },
      ],
      design: [
        {
          title: "content_direction",
          value: "content_direction",
          icon: "mdi-file-plus",
        },
        {
          title: "voice_over",
          value: "voice_over",
          icon: "mdi-music-note-plus",
        },
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
      setSize: "1200x628",
      loopingCol1: [],
      loopingCol2: [],
      loopingCol3: [],
      loopingCol4: [],
      tabs: [
        { title: "media", icon: "video" },
        { title: "comparing", icon: "graph" },
      ],
    };
  },

  watch: {
    selectedMedia: function (selected) {},
    mediaData: function (data) {
      // this.gallryLayout();
    },
    comment_tab: function (val) {
      console.log("tab change", val);
      if (val == 1) {
        this.$refs?.remarkTemplateRefs?.fetchRemarks();
      }
      if (val == 2) {
        this.$refs?.labelTemplateRefs?.fetchLabels();
      }
    },
  },

  methods: {
    async saveTags() {
      if (this.editTags) {
        try {
          this.submitting = true;
          const result = await this.$axios.post(
            "library/assign-tags",
            this.mediaData[this.selectedMedia]
          );
        } catch (error) {
          console.error("error", error);
        }
        this.submitting = false;
        this.editTags = false;
      } else {
        this.editTags = true;
      }
    },
    closeTagEdit() {
      this.editTags = false;
    },
    onNewEreaAded(area) {
      console.log("area", area);
      if (typeof area != "object") {
        console.log("aloggdgdgdd", area);
        // this.form.$model.area = {
        // 	name: area,
        // 	id: "new",
        // };
      }
    },
    onTabChange(index) {
      this.selected_profile = index;
      try {
        if (index == 1) this.$refs.comparingGraphRefs.tabChange();
      } catch (error) {
        console.log(error);
      }
    },
    tagCovertions() {
      let rawTags;
      rawTags = this.tagValue.split(",");
      rawTags.forEach((item, index) => {
        let arrItem = item.trim();
        if (arrItem.trim() == "") {
          return;
        } else {
          let lastIndex = this.tags.length - 1;
          this.tags.push({ id: lastIndex, text: arrItem });
        }
      });
      this.tagValue = "";
    },

    showDialog(data = null) {
      if (data !== null) {
        this.selected_items = data;
        this.contentData = data?.content_library || {};
        this.getContentMedia();
      }

      this.key++;
      if (this.dialog) {
        this.selected_profile = 0;
        this.selectedMedia = 0;
        this.comment_tab = 0;
        try {
          this.selected_items = {};
          this.$refs.comparingGraphRefs.reset();
        } catch (error) {}
      }

      this.dialog = !this.dialog;
    },

    async getContentMedia() {
      this.loadingMedia = true;
      try {
        const response = await this.$axios.get("library/get-content-media", {
          params: {
            contentId: this.contentData?.id,
            mediaId: this.selected_items.id,
            filterType: this.filter_type,
          },
        });
        if (response.status == 200) {
          this.mediaData = response.data.media;
          const index = this.mediaData.findIndex(
            (row) => row.id == this.selected_items.id
          );
          if (index >= 0) {
            this.selectedMedia = index;
          }
          this.tags = response.data.tags;
        }
      } catch (error) {
        console.log("getContentMedia:", error);
      }
      this.loadingMedia = false;
    },

    deleteTag(item) {
      this.mediaData[this.selectedMedia].tags = this.mediaData[
        this.selectedMedia
      ].tags?.filter((row) => {
        return row != item;
      });
    },
    loopTags(item) {
      try {
        if (item.tags) {
          return item.tags;
        } else {
          return [];
        }
      } catch (error) {
        return [];
      }
      return [];
    },
    selectItem(index) {
      this.selected_index = index;
      console.log("select index", this.selected_index);
    },
  },
};
</script>

<style scoped>
.disable-iframe {
  pointer-events: none !important;
}
.dialog-card {
  box-shadow: none;
  height: 100%;
}
.profileDialog {
  width: 1250px;
  height: 750px;
  min-height: 650px;
  border-radius: 10px;
  overflow: scroll;
}

.media-list {
  height: 600px;
  overflow-y: auto;
}
.addTag {
  /* border: 1px solid rgb(242, 242, 242); */
  /* width: 100%; */
  /* padding: 10px; */
  border-radius: 5px;
  margin-top: 10px;
  margin-bottom: 10px;
}

.addTag input {
  width: 235px;
  height: 35px;
  margin-right: 10px;
  background-color: #e8e8e8;
  padding: 10px;
}

.addTag input:focus-visible {
  background-color: white;
  border-color: var(--v-primary-base);
}

.media_title {
  display: flex;
  padding: 5px 0 10px 0;
  justify-content: flex-start;
  align-items: center;
}

.iframe_video {
  border: none;
  border-radius: 10px;
}

.media_title_text {
  display: flex;
  flex-direction: column;
}

.media_title_text .media_content {
  margin-top: 0px;
  font-size: 0.8rem;
}

.media_title_icon {
  margin-right: 10px;
  background-color: #b2b2b2;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white !important;
}
</style>

<style>
.verticalTaps .v-tabs-slider {
  display: none;
}

.verticalTaps .custom_taps_holder .v-tab--active {
  position: relative;
}

.verticalTaps .custom_taps_holder .v-tab {
  margin-bottom: 10px !important;
}

.verticalTaps .custom_taps_holder .v-tab--active::after {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-color: var(--v-primary-base);
  opacity: 0.2;
  border-radius: 5px;
}

.verticalTaps .v-tabs-bar .custom_taps_holder {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 100%;
  background: white !important;
}

.verticalTaps {
  background-color: #f6f8fc !important;
}

.verticalTaps .v-window-item,
.verticalTaps .v-window__container,
.verticalTaps .v-window,
.verticalTaps .v-window-item .v-window-item--active,
.verticalTaps .v-card--flat {
  background: transparent !important;
}

.profile_btns button {
  width: 30px !important;
  height: 30px !important;
}

.related_video_tabs {
  height: 100%;
}

.related_video_tabs .v-sheet {
  box-shadow: none !important;
}

.related_video_tabs .v-tabs-bar {
  height: auto;
  position: relative;
}

.related_video_tabs .v-tabs {
  background-color: transparent;
}

.related_video_tabs .related_video_bg {
  position: relative;
}

.related_video_tabs .related_video_bg::before {
  position: absolute;
  content: "";
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-color: var(--v-primary-base);
  opacity: 0.1;
  border-radius: 10px;
}

.related_video_tabs .v-tab {
  padding: 0;
}

.related_video_tabs .v-slide-group__next {
  position: absolute;
  right: -20px;
  top: 50%;
  transform: translateY(-50%);
  z-index: 5;
  /* box-shadow: ; */
}

.related_video_tabs .v-slide-group__prev {
  position: absolute;
  left: -20px;
  top: 50%;
  transform: translateY(-50%);
  z-index: 5;
}

.related_video_tabs .v-slide-group__next,
.related_video_tabs .v-slide-group__prev {
  background-color: white;
  min-width: auto !important;
  min-height: auto !important;
  border-radius: 50%;
  /* padding: 5px; */
  -webkit-box-shadow: 0px 3px 6px 0px rgba(0, 0, 0, 0.16);
  -moz-box-shadow: 0px 3px 6px 0px rgba(0, 0, 0, 0.16);
  box-shadow: 0px 3px 6px 0px rgba(0, 0, 0, 0.16);
}

.related_video_tabs .v-slide-group__next i,
.related_video_tabs .v-slide-group__prev i {
  color: var(--v-primary-base) !important;
}

.related_video_text {
  text-align: left !important;
}

.related_video_tabs .v-tab--active > div:first-child {
  border: 1px solid var(--v-primary-base) !important;
  position: relative;
}

.playing_active {
  width: 100%;
  height: 100%;
  position: absolute;
  top: 0;
  left: 0;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
  display: none;
}

.related_video_tabs .v-tab--active .playing_active {
  display: flex;
}

.info_tabs {
  height: 70%;
}

.info_tabs .v-tabs-bar__content a {
  letter-spacing: normal;
  font-weight: 600;
  /* opacity: 0.8; */
}

.info_tabs .v-tabs-bar__content a i {
  font-size: 1.3rem;
}

.info_tabs .v-tabs-slider-wrapper {
  height: 3px !important;
  display: flex;
  align-items: center;
  justify-content: center;
}

.info_tabs .v-tabs-slider {
  border-top-left-radius: 10px !important;
  border-top-right-radius: 10px !important;
  width: 70%;
}

.info_item {
  width: 40px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-right: 15px;
  position: relative;
}

.info_item::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: var(--v-primary-base);
  opacity: 0.1;
  border-radius: 5px;
}

/* .tags_chips {
  height: 100%;
  overflow-y: auto;
} */

.tags_chips .v-chip {
  border-color: var(--v-primary-base) !important;
}

.tags_chips .v-chip span,
.tags_chips .v-chip button::before {
  color: var(--v-primary-base);
}

.info_tabs .v-window__container {
  display: block;
  height: auto !important;
}

.v-skeleton-loader__image {
  height: 380px !important;
}
.media-wrapper .media-item {
  flex: 25%;
  max-width: 25%;
  padding: 0 10px;
}
.media-wrapper {
  line-height: 0;
  display: flex;
  flex-wrap: wrap;
  width: 100%;
}
.media-item {
  flex: 25%;
  max-width: 25%;
  padding: 0 10px;
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
  border-radius: 10px;
}
.media-list {
  height: 600px;
  overflow-y: auto;
}

#overlay {
  position: fixed; /* Sit on top of the page content */
  width: 100%; /* Full width (cover the whole page) */
  height: 100%; /* Full height (cover the whole page) */
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
  z-index: 50000000000000000; /* Specify a stack order in case you're using a different order for other elements */
  cursor: pointer; /* Add a pointer on hover */
}
</style>

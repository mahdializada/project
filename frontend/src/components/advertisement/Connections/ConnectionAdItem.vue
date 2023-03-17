<template>
  <div>
    <div class="connection__container pa-5" v-if="showLoader">
      <lottie-player
        src="/assets/man-on-rocket.json"
        background="transparent"
        speed="1"
        style="width: 550px; height: 550px"
        loop
        autoplay
      >
      </lottie-player>
      <h2 class="pa-2 primary--text">
        {{
          // $tr("pulish_ads", published, form.$model.ads.length) +
          "publish " + publishe + "  Of " + form.$model.ads.length + "ads"
        }}
      </h2>
    </div>
    <div class="connection__container" v-else>
      <CTitle text="ad_information" class="mt-0" />
      <div class="">
        <MultiInputItem
          :items="form.ads.$each.$iter"
          @addMore="addOneMore"
          @removeItem="removeItem"
          :title="'ads'"
          :loading.sync="loading"
        >
          <template v-slot:content="{ item, index }">
            <div class="d-flex">
              <CTextField
                v-model="item.ad_id.$model"
                :title="$tr('label_required', $tr('ad_id'))"
                :placeholder="$tr('ad_id')"
                class="me-3"
                prepend-inner-icon="mdi-link-variant "
                :rules="validateRule(item.ad_id, $root, $tr('ad_id'))"
                :invalid="item.ad_id.$invalid"
                :hasCustomBtn="true"
                btnIcon="mdi-content-paste "
                @add="copyToAdId"
              />
              <CTextField
                v-model="item.generated_link.$model"
                :title="$tr('generated_link')"
                :placeholder="$tr('generated_link')"
                prepend-inner-icon="mdi-link-variant "
                :hasCustomBtn="true"
                btnIcon="mdi-clipboard-multiple"
                readonly
                @add="copyGeneratedLinkToClipboard(item.generated_link.$model)"
              />
            </div>
            <v-tabs
              v-model="tabItem"
              height="35"
              show-arrows
              :hide-slider="true"
              center-active
              active-class="as-border"
              background-color="primary"
            >
              <v-tab
                v-for="tab in ['all', 'publish', 'not_publish']"
                :key="tab"
                width="100%"
                >{{ $tr(tab) }}</v-tab
              >
              <v-tab-item v-for="n in 3" :key="n">
                <v-radio-group v-model="single" mandatory>
                  <div
                    id="scroll-box"
                    class="d-flex align-center px-1"
                    :style="`
                    overflow-x: ${medias?.length > 3 ? 'scroll' : 'visible'};
                    scroll-behavior: smooth;
                    width: 700px;`"
                  >
                    <div
                      class="custom-card mx-1 d-flex"
                      height="80"
                      v-for="(media, i) in medias"
                      :key="i"
                    >
                      <v-card
                        class="mb-2 gray"
                        elevation="1"
                        :style="`border-radius: 10px;${
                          single == media[0].project_url
                            ? 'border:2px solid #2e7be6'
                            : ''
                        }`"
                      >
                        <v-card-text class="px-1 pb-0">
                          <div class="w-full position-relative mb-3">
                            <v-radio
                              :value="media[0].project_url"
                              @click="onchange(media[0], index)"
                              color="#2e7be6"
                              class="d-flex flex-row-reverse"
                              on-icon="mdi-checkbox-marked"
                              off-icon="mdi-checkbox-blank-outline"
                            >
                              <template v-slot:label>
                                <div>
                                  <v-avatar
                                    size="32"
                                    style="background: lightgray"
                                  >
                                    <v-icon class="white--text"
                                      >mdi-video</v-icon
                                    >
                                  </v-avatar>
                                  <span
                                    style="position: absolute"
                                    class="ms-1"
                                    >{{
                                      media[0]?.content_library?.item_code
                                    }}</span
                                  >
                                  <span
                                    style="position: absolute"
                                    class="mt-3 ms-1"
                                    >{{
                                      media[0]?.content_library?.item_name
                                    }}</span
                                  >
                                </div>
                              </template>
                            </v-radio>
                          </div>
                          <div class="position-relative">
                            <span
                              style="
                                position: absolute;
                                top: 80px;
                                left: 10px;
                                background-color: #00000091;
                                z-index: 11111;
                              "
                              class="rounded"
                            >
                              <v-tooltip right color="white">
                                <template v-slot:activator="{ on, attrs }">
                                  <v-avatar size="35" v-bind="attrs" v-on="on">
                                    <FlagIcon
                                      small
                                      :flag="
                                        media[0]?.content_library?.country?.iso2.toLowerCase()
                                      "
                                      round
                                    />
                                  </v-avatar>
                                </template>
                                <div class="black--text">
                                  {{ media[0]?.content_library?.country?.name }}
                                </div>
                              </v-tooltip>
                            </span>
                            <span
                              style="
                                position: absolute;
                                top: 130px;
                                left: 10px;
                                background-color: #00000091;
                                z-index: 11111;
                              "
                              class="rounded"
                            >
                              <v-tooltip right color="white">
                                <template v-slot:activator="{ on, attrs }">
                                  <v-avatar size="35" v-bind="attrs" v-on="on">
                                    <v-img
                                      :src="
                                        media[0]?.content_library?.company?.logo
                                      "
                                      max-width="80%"
                                    ></v-img>
                                  </v-avatar>
                                </template>
                                <div class="black--text">
                                  {{ media[0]?.content_library?.company?.name }}
                                </div>
                              </v-tooltip>
                            </span>
                            <span
                              style="
                                position: absolute;
                                top: 180px;
                                left: 10px;
                                background-color: #00000091;
                                z-index: 11111; 
                                cursor:pointer
                              "
                              class="rounded"
                              @click="openCanvaLink(media[0].project_url)"
                            >
                            <v-icon color="white" style="font-size:35px">mdi-link</v-icon>
                            </span>

                            <iframe
                              loading="lazy"
                              class="canvaView"
                              :src="media[0]?.media_url"
                              allowfullscreen="allowfullscreen"
                              allow="fullscreen"
                            >
                            </iframe>
                          </div>
                        </v-card-text>
                        <v-card-actions class="py-0">
                          <v-autocomplete
                            auto-select-first
                            dense
                            rounded
                            outlined
                            :items="media"
                            v-model="item.media_id.$model"
                            item-text="size"
                            item-value="id"
                            :disabled="isDisabled(media, index)"
                            @change="onchangeSize(media, index)"
                            class="mx-1 pb-2"
                            style="z-index: 10000"
                            append-icon="mdi-chevron-down"
                            :placeholder="$tr('select_size')"
                            hide-details
                          ></v-autocomplete>
                        </v-card-actions>
                      </v-card>
                    </div>
                  </div>
                  <v-row class="mb-2" v-if="isFetchingCanva">
                    <v-col class="px-2 pb-0" v-for="i in 3" :key="i" cols="4">
                      <v-skeleton-loader
                        class="rounded-0"
                        type="image"
                        width="100%"
                      ></v-skeleton-loader>
                      <v-skeleton-loader
                        type="image"
                        class="rounded-0"
                        width="100%"
                      ></v-skeleton-loader>
                      <v-skeleton-loader
                        type="image"
                        class="rounded-0"
                        width="100%"
                        height="80px"
                      ></v-skeleton-loader>
                    </v-col>
                  </v-row>
                </v-radio-group>
              </v-tab-item>
            </v-tabs>
          </template>
        </MultiInputItem>
      </div>
    </div>
  </div>
</template>
<script>
import GlobalRules from "~/helpers/vuelidate";
import MultiInputItem from "~/components/new_form_components/cat_product_selection/MultiInputItem.vue";
import CTitle from "~/components/new_form_components/Inputs/CTitle.vue";
import CTextField from "../../new_form_components/Inputs/CTextField.vue";
import FlagIcon from "../../common/FlagIcon.vue";

export default {
  props: {
    form: Object,
    data: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      loading: false,
      showLoader: false,
      published: 0,
      submit: false,
      single: null,
      medias: [],
      isFetchingCanva: false,
      tabItem: 0,
    };
  },
  mounted() {},
  computed: {
    publishe: function () {
      if (this.submit) {
        if (this.published < this.form.$model.ads.length)
          setTimeout(
            () => {
              this.published = this.published + 1;
            },
            this.form.$model.ads.length > 2 ? 1000 : 500
          );
      } else {
        this.published = 1;
      }
      return this.published;
    },
  },
  watch: {
    tabItem: function () {
      if (this.tabItem == 0) {
        this.getProjectUrl();
      } else if (this.tabItem == 1) {
        this.getProjectUrl("publish");
      } else {
        this.getProjectUrl("not publish");
      }
    },
  },
  methods: {
    isDisabled(item, index) {
      if (item.find((i) => i.id == this.form.$model.ads[index].media_id))
        return false;
      else return true;
    },
    onchangeSize(item, index) {
      this.form.$model.ads[index].media_size = item.find(
        (i) => i.id == this.form.$model.ads[index].media_id
      ).size;
    },
    openCanvaLink(link){
      console.log(link);
      window.open(link, '_blank').focus();
    },
    onchange(item, i) {
      console.log(i);
      this.form.$model.ads[i].media_link = item.project_url;
      this.form.$model.ads[i].media_id = item.id;
      this.form.$model.ads[i].media_size = item.size;
    },
    async copyGeneratedLinkToClipboard(link) {
      try {
        const clipboard = navigator.clipboard;
        await clipboard.writeText(link);
        this.$toasterNA(
          "primary",
          this.$tr("link_has_copied_to_your_clipboard")
        );
      } catch (error) {
        this.$toasterNA("red", this.$tr("link_is_not_copied"));
      }
    },

    async copyToAdId(isCanva = false) {
      // try {
      // 	const clipboard = navigator.clipboard;
      // 	const ad_id = await clipboard.readText();
      // 	if (ad_id) {
      // 		if (isCanva) this.form.media_link.$model = ad_id;
      // 		else this.form.ad_id.$model = ad_id;
      // 	}
      // } catch (error) {}
    },

    validate() {
      this.form.ads.$touch();
      if (!this.form.ads.$invalid) {
        this.submit = true;
        this.published = 1;
        this.showLoader = true;
      }
      // setTimeout(() => {}, 300);
      // if(this.form.ads.$invalid)
      //   this.$toasterNA("red", this.$tr("please_select_an_item",this.$tr('media')));
      return !this.form.ads.$invalid;
    },
    async addOneMore() {
      this.loading = true;
      let generateLink = await this.generateLink();
      if (generateLink) this.single = this.medias[0][0]?.project_url;
      this.form.ads.$model.push({
        media_link: this.medias[0][0]?.project_url,
        media_id: this.medias[0][0]?.id,
        media_size: this.medias[0][0]?.size,
        ad_id: null,
        generated_link: generateLink.generated_link,
        connection_id: generateLink.id,
      });
      this.loading = false;
    },

    async generateLink() {
      try {
        let connection = this.data;
        delete connection.companies;
        const url = "advertisement/connection/generate_link";
        const { data } = await this.$axios.post(url, connection);
        if (data.result) {
          this.$toasterNA("green", this.$tr("successfully_inserted"));
          const insertedRecord = data.connection;
          return insertedRecord;
        } else {
          this.$toasterNA(
            "red",
            this.$tr(data.message || "something_went_wrong")
          );
          return false;
        }
      } catch (error) {
        HandleException.handleApiException(this, error);
        return false;
      }
    },
    loaded() {
      this.showLoader = false;
      this.submit = false;
      this.published = 1;
      this.getProjectUrl();
    },
    async getProjectUrl(status = "all") {
      try {
        this.medias = [];
        this.isFetchingCanva = true;
        const response = await this.$axios.get("library/get-project-url", {
          params: {
            status: status,
            item_code: this.form.pcode.$model,
            country_id: this.form.country_id.$model,
            company_id: this.form.company_id.$model,
          },
        });
        if (response.status == 200) {
          const data = response.data;
          for (var key in data) {
            if (data.hasOwnProperty(key)) {
              this.medias.push(data[key]);
            }
          }
          if (this.medias.length > 0) {
            this.single = this.medias[0][0]?.project_url;
            this.form.$model.ads[0].media_link = this.medias[0][0]?.project_url;
            this.form.$model.ads[0].media_id = this.medias[0][0]?.id;
            this.form.$model.ads[0].media_size = this.medias[0][0]?.size;
          }
          this.isFetchingCanva = false;
        }
        this.isFetchingCanva = false;
      } catch (error) {
        this.isFetchingCanva = false;
        console.log(error);
      }
    },
    async removeItem(item) {
      this.loading = true;
      let param = { ids: [this.form.$model.ads[item].connection_id] };
      const { data } = await this.$axios.post(
        "advertisement/connection/delete_generated_links",
        param
      );
      if (data && this.form.ads.$model.length > 1)
        this.form.ads.$model.splice(item, 1);
      this.loading = false;
    },
    onColseDialog() {
      if (!this.submit && this.form.$model.ads[0].connection_id) {
        let ids = this.form.$model.ads.map((ad) => ad.connection_id);
        if (ids.length > 0) {
          let param = { ids: ids };
          const { data } = this.$axios.post(
            "advertisement/connection/delete_generated_links",
            param
          );
        }
      }
    },
  },
  components: {
    CTitle,
    CTextField,
    MultiInputItem,
    FlagIcon,
  },
};
</script>

<style scoped lang="scss">
.connection__container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}
.canvaView {
  width: 100%;
  height: 363px;
  top: 0;
  left: 0;
  border: 1px solid #949494;
  padding: 0;
  margin: 0;
  border-radius: 15px;
}
// #vdivider{
//   background: #2e7be6;
// }
#vdivider::after {
  border-left: 15px solid #2e7be6;
  border-bottom: 10px solid transparent;
  border-top: 10px solid transparent;
  background: white;
  display: block;
  content: "";
  position: absolute;
  top: 50%;
  left: 60%;
  transform: translate(-20%, -50%);
}
.custom-card {
  min-width: 220px;
  max-width: 220px;
  z-index: 100 !important;
}
.as-border {
  border-bottom: 2px solid #2e7be6 !important;
}
</style>

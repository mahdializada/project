<template>
  <div class="connection__container">
    <div class="friqiBase--text title">
      {{ $tr("how_do_you_want_to_create_your_link") }}
    </div>
    <div class="mb-3 d-flex mt-0 align-center justify-center">
      <ItemCard
        v-for="(item, index) in items"
        :key="index"
        :item="item"
        :selected="item.type == form.canva_type.$model"
        class="custom__item__card"
        @click="handleClick(item)"
      />
    </div>
    <div v-show="form.canva_type.$model == 'new_canva'">
      <div
        id="scroll-box"
        class="d-flex align-center px-1 flex-no-wrap"
        style="overflow-x: auto; scroll-behavior: smooth; width: 780px"
      >
        <div
          class="custom-card mx-1"
          style="max-width: 200px"
          v-for="(media, i) in medias"
          :key="i"
        >
          <v-card
            class="mb-2 gray"
            elevation="1"
            :style="`border-radius: 10px;${
              selected_media.project_url == media[i].project_url
                ? 'border:2px solid #2e7be6'
                : ''
            }`"
          >
            <v-card-text class="px-1 pb-0 pt-1">
              <div
                class="d-flex align-center cursor-pointer"
                style="margin-bottom: 5px"
                @click="onchange(media[i])"
              >
                <v-avatar size="28" style="background: lightgray">
                  <v-icon class="white--text">mdi-video</v-icon>
                </v-avatar>
                <div class="d-flex align-center">
                  <span class="ms-1">{{
                    media[i]?.content_library?.item_code
                  }}</span>
                  <div class="ms-1 no-wrap-title">
                    {{ media[i]?.content_library?.item_name }}
                  </div>
                </div>
              </div>
              <div class="position-relative">
                <span class="rounded show-country">
                  <v-tooltip right color="white">
                    <template v-slot:activator="{ on, attrs }">
                      <v-avatar size="35" v-bind="attrs" v-on="on">
                        <FlagIcon
                          small
                          :flag="
                            media[
                              i
                            ]?.content_library?.country?.iso2.toLowerCase()
                          "
                          round
                        />
                      </v-avatar>
                    </template>
                    <div class="black--text">
                      {{ media[i]?.content_library?.country?.name }}
                    </div>
                  </v-tooltip>
                </span>
                <span class="rounded show-company">
                  <v-tooltip right color="white">
                    <template v-slot:activator="{ on, attrs }">
                      <v-avatar size="35" v-bind="attrs" v-on="on">
                        <v-img
                          :src="media[i]?.content_library?.company?.logo"
                          max-width="80%"
                        ></v-img>
                      </v-avatar>
                    </template>
                    <div class="black--text">
                      {{ media[i]?.content_library?.company?.name }}
                    </div>
                  </v-tooltip>
                </span>
                <iframe
                  loading="lazy"
                  class="canvaView"
                  :src="media[i]?.media_url"
                  allowfullscreen="allowfullscreen"
                  allow="fullscreen"
                >
                </iframe>
              </div>
            </v-card-text>
            <v-card-actions class="py-0 px-0">
              <v-autocomplete
                auto-select-first
                dense
                rounded
                outlined
                :items="media"
                item-text="size"
                return-object
                class="mx-1 pb-1 rohullah"
                style="z-index: 10000; min-height: 26px"
                append-icon="mdi-chevron-down"
                :placeholder="$tr('select_size')"
                hide-details
                @change="onchange($event)"
              ></v-autocomplete>
            </v-card-actions>
          </v-card>
        </div>
      </div>
    </div>

    <v-row class="" v-if="isFetchingCanva">
      <v-col class="px-2 pb-0" v-for="i in 3" :key="i" cols="4">
        <v-skeleton-loader
          class=""
          type="image"
          width="100%"
          style="height: 300px"
        ></v-skeleton-loader>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import GlobalRules from "~/helpers/vuelidate";
import FlagIcon from "../../../common/FlagIcon.vue";
import ItemCard from "../../../new_form_components/components/ItemCard.vue";
export default {
  props: {
    form: Object,
    data: Object,
  },
  data() {
    return {
      validateRule: GlobalRules.validate,
      single: null,
      selected_media: {},
      medias: [],
      isFetchingCanva: false,
      tabItem: 0,
      items: [
        {
          type: "new_canva",
          name: this.$tr("new_canva"),
        },
        {
          type: "current_canva",
          name: this.$tr("current_canva"),
        },
      ],
    };
  },
  watch: {
    "form.canva_type.$model": function (type) {
      console.log("canva changed", type);
      if (type == "new_canva") {
      } else {
        this.selected_media = {};
        const { media_link, media_size, media_id } = this.data.connection_copy;
        this.form.$model.ads[0].media_link = media_link;
        this.form.$model.ads[0].media_id = media_id;
        this.form.$model.ads[0].media_size = media_size;
      }
    },
  },
  created() {
    this.getProjectUrl();
  },
  methods: {
    selectMedia(item) {
      console.log("item", item);
    },
    handleClick(item) {
      this.form.canva_type.$model = item.type;
    },
    validate() {
      return true;
    },
    isDisabled(item) {
      if (item.find((i) => i.id == this.form.$model.ads[0].media_id))
        return false;
      else return true;
    },
    onchange(item) {
      this.selected_media = item;
      this.form.$model.ads[0].media_link = item.project_url;
      this.form.$model.ads[0].media_id = item.id;
      this.form.$model.ads[0].media_size = item.size;
    },
    async getProjectUrl(status = "all") {
      console.log("project url");
      try {
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
          this.medias = [];
          const data = response.data;
          for (var key in data) {
            if (data.hasOwnProperty(key)) {
              this.medias.push(data[key]);
            }
          }

          this.isFetchingCanva = false;
        }
        this.isFetchingCanva = false;
      } catch (error) {
        this.isFetchingCanva = false;
        console.log(error);
      }
    },
  },
  components: { FlagIcon, ItemCard },
};
</script>
<style>
.rohullah .v-input__append-inner {
  margin-top: 5px !important;
}
.rohullah .v-input__slot {
  min-height: 30px !important;
}
</style>

<style scoped>
.no-wrap-title {
  width: 104px;
  overflow: hidden;
  text-overflow: ellipsis !important;
  word-wrap: none !important;
  white-space: nowrap;
}
.connection__container {
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100% !important;
  min-height: 500px;
  background-color: #f7f8fc;
}
.custom__item__card {
  width: 180px;
  height: 120px;
}
.show-country {
  position: absolute;
  top: 15px;
  left: 10px;
  background-color: #00000091;
  z-index: 11111;
}
.show-company {
  position: absolute;
  top: 55px;
  left: 10px;
  background-color: #00000091;
  z-index: 11111;
}
.canvaView {
  width: 100%;
  height: 180px;
  top: 0;
  left: 0;
  border: 1px solid #949494;
  padding: 0;
  margin: 0;
  border-radius: 15px;
}
</style>
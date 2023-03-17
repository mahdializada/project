<template>
  <div>
    <div
      style="border-radius: 30px 0 0 0; width: 100%"
      class="white ps-4 d-flex align-center justify-space-between"
    >
      <div class="d-flex align-center">
        <v-btn fab x-small class="me-1 primary" @click="$emit('closeDialog')">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-btn fab x-small class="primary" @click="$emit('fullScreen')">
          <v-icon>mdi-fullscreen</v-icon>
        </v-btn>
        <div class="grey rounded mx-3" style="height: 40px; width: 1px"></div>
        <span>
          <v-avatar size="40" class="grey lighten-3 me-1">
            <v-avatar size="35" color="white">
              <v-avatar size="30" color="white">
                <v-avatar size="30" class="text-h6 primary white--text">
                  {{ selectedItems.product_code[0] }}
                </v-avatar>
              </v-avatar>
            </v-avatar>
          </v-avatar>
          <span class="dialog-title"> {{ selectedItems.product_code }}</span>
        </span>
      </div>

      <div class="d-flex align-center">
        <slot name="date_slot">
          <FilterDateRange
            :customSelectDate="{ type: 'lifetime', index: 5 }"
            ref="filterDateRange"
            :dateRangeProp="date_range"
            :hide-title="true"
            @dateChanged="FilterByDateRange($event)"
            :custom_design="true"
            :nudge_left="130"
          />
        </slot>

        <v-img
          src="/new_landing/product_profile_header.svg"
          height="60px"
        ></v-img>
      </div>
    </div>
    <div class="w-full position-relative ms-2 mt-2">
      <v-btn
        color="primary"
        class="position-fixed"
        @click="$emit('changeStatus')"
      >
        {{ $tr("change_status") }}
      </v-btn>
      <div style="height: 100vh; overflow-y: auto" class="pb-14">
        <div
          class="d-flex align-center"
          v-for="(item, index) in itemHistoris"
          :key="index"
          :style="`height: ${item.isActive == 1 ? '170' : '170'}px;`"
        >
          <div style="width: 45%; text-align: right" class="">
            <v-card width="300" style="border-radius: 50px" class="float-end">
              <v-card-text class="d-flex align-start">
                <v-avatar color="primary" size="50" style="color: white">
                  <img
                    v-if="item.user?.image"
                    :src="item.user?.image"
                    lazy-src="https://picsum.photos/id/11/10/6"
                  />
                  <span v-else>{{
                    item.user?.firstname
                      ? item.user?.firstname[0].toUpperCase()
                      : ""
                  }}</span>
                </v-avatar>
                <div class="d-flex flex-column ms-1 mt-1 align-start">
                  <h3>
                    {{ item.user?.firstname + " " + item.user?.lastname }}
                  </h3>
                  <span class="mt-1">{{ item.created_at }}</span>
                </div>
              </v-card-text>
            </v-card>
          </div>
          <div style="width: 10%; text-align: center; position: relative">
            <v-avatar :color="item.color" size="100" class="white--text">
              {{ $tr(item.item_status) }}
            </v-avatar>

            <lottie-player
              v-if="index == 0"
              class="mb-2"
              src="/stepper/stepper-animation.json"
              speed="1"
              style="
                width: 140px;
                height: 140px;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
              "
              loop
              autoplay
            ></lottie-player>
            <span
              v-if="index != itemHistoris.length - 1"
              class="float-end position-absolute dotstype"
              :style="`top: ${index == 0 ? 120 : 105}px;${
                index != 0 ? 'height:60px' : ''
              }`"
            ></span>
          </div>
          <div style="width: 45%; text-align: left">
            <v-card width="350" style="border-radius: 50px" class="float-start">
              <v-card-text class="d-flex align-start">
                <img
                  src="/alarm.png"
                  width="40px"
                  class="ms-1"
                  lazy-src="https://picsum.photos/id/11/10/6"
                />
                <div class="d-flex flex-column ms-2 align-start">
                  Remains in this state for

                  <h3 class="mt-1" style="font-size: 18px">
                    {{ daysBetween(item.created_at, item.end_date) }}
                  </h3>
                </div>
              </v-card-text>
            </v-card>
          </div>
        </div>
        <div
          :class="`${
            isFetchingItemStatusHistory ? 'd-flex' : 'd-none'
          } align-center`"
          style="height: 150px"
          v-for="(item, index) in isFetchingItemStatusHistory ? itemStatus : []"
          :key="index"
        >
          <div style="width: 45%; text-align: right" class="">
            <v-card width="300" style="border-radius: 50px" class="float-end">
              <v-card-text class="d-flex align-start">
                <v-skeleton-loader
                  class="rounded-3"
                  height="45px"
                  type="image"
                  width="45px"
                ></v-skeleton-loader>
                <div class="d-flex flex-column ms-1 mt-1 align-start">
                  <v-skeleton-loader
                    class="rounded-3"
                    height="18px"
                    type="image"
                    width="150px"
                  ></v-skeleton-loader>
                  <span class="mt-1">
                    <v-skeleton-loader
                      class="rounded-3"
                      height="15px"
                      type="image"
                      width="100px"
                    ></v-skeleton-loader>
                  </span>
                </div>
              </v-card-text>
            </v-card>
          </div>
          <div style="width: 10%; text-align: center; position: relative">
            <v-avatar :color="item.color" size="100" class="white--text">
              <v-skeleton-loader
                class="rounded-3"
                height="105px"
                type="image"
                width="105px"
              ></v-skeleton-loader>
            </v-avatar>
            <span
              v-if="index != itemStatus.length - 1"
              class="float-end position-absolute dotstype"
            ></span>
          </div>
          <div style="width: 45%; text-align: left">
            <v-card width="350" style="border-radius: 50px" class="float-start">
              <v-card-text class="d-flex align-start">
                <v-skeleton-loader
                  class="rounded-3"
                  height="45px"
                  type="image"
                  width="45px"
                ></v-skeleton-loader>
                <div class="d-flex flex-column ms-2 align-start">
                  <v-skeleton-loader
                    class="rounded-3"
                    height="18px"
                    type="image"
                    width="150px"
                  ></v-skeleton-loader>
                  <span class="mt-1">
                    <v-skeleton-loader
                      class="rounded-3"
                      height="15px"
                      type="image"
                      width="100px"
                    ></v-skeleton-loader>
                  </span>
                </div>
              </v-card-text>
            </v-card>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
  <script>
import moment from "moment";
import FilterDateRange from "../../advertisement/FilterDateRange.vue";
export default {
  props: {
    selectedItems: Object,
    openItemProfile: Boolean,
  },
  data() {
    return {
      itemStatus: [
        {
          id: "new_sales",
          count: 0,
          isSelected: 0,
          change_to: "in_source",
          color: "#007f35 !important",
        },
        {
          id: "in_source",
          count: 0,
          isSelected: 0,
          change_to: "in_study",
          color: "purple !important",
        },
        {
          id: "in_study",
          change_to: "content_creation",
          count: 0,
          isSelected: 0,
          color: "#37383a !important",
        },
        {
          id: "content_creation",
          change_to: "final_review",
          count: 0,
          isSelected: 0,
          color: "#fa8059 !important",
        },
        {
          id: "final_review",
          change_to: "ready_to_sale",
          count: 0,
          isSelected: 0,
          color: "#8a53fd !important",
        },
        {
          id: "ready_to_sale",
          change_to: "",
          count: 0,
          isSelected: 0,
          color: "#0049b0 !important",
        },
        {
          id: "remove",
          change_to: "new_sales",
          count: 0,
          isSelected: 0,
          color: "#bb732b !important",
        },
      ],
      date_range: {
        start_date: "2023-01-12",
        end_date: moment().format("YYYY-MM-DD"),
      },
      itemHistoris: [],
      isFetchingItemStatusHistory: false,
    };
  },
  mounted() {
    this.$root.$on("updateItemChangeStatus", () => {
      if (this.openItemProfile)
        setTimeout(() => {
          this.fetchData();
        }, 500);
    });
  },
  beforeDestroy() {
    // removing eventBus listener
    this.$root.$off("updateItemChangeStatus");
  },
  methods: {
    FilterByDateRange() {
      this.fetchData();
    },
    async fetchData() {
      this.itemHistoris = [];
      this.isFetchingItemStatusHistory = true;
      const result = await this.$axios.get(
        `online-sales/fetch-history-status/${this.selectedItems.product_code}`,
        { params: this.date_range }
      );
      if (result.status == 200) {
        this.itemHistoris = result.data;
      }
      this.isFetchingItemStatusHistory = false;
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm:ss");
    },
    daysBetween(startDate, endDate) {
      try {
        // startDate = this.localeHumanReadableTime(startDate);
        // endDate = this.localeHumanReadableTime(endDate);
        // if (endDate != "") endDate = this.localeHumanReadableTime(endDate);
        // else endDate = moment().format("YYYY-MM-DD h:mm:ss");
        startDate = new Date(startDate + " UTC");
        startDate.toString();
        endDate = new Date(endDate + " UTC");
        endDate.toString();
        let difference_ms =
          endDate.getTime() / 1000 - startDate.getTime() / 1000;
        var days = Math.floor(difference_ms / 86400);
        days = days > 0 ? days + "D " : "";
        difference_ms = difference_ms % 86400;
        var hours = Math.floor(difference_ms / 3600);
        hours = hours > 0 ? hours + "H " : "";
        difference_ms = difference_ms % 3600;
        var minutes = Math.floor(difference_ms / 60);
        minutes = minutes > 0 ? minutes + "M " : "";
        var seconds = difference_ms % 60;
        seconds = seconds > 0 ? seconds + "S" : "";
        return days + hours + minutes + seconds;
      } catch (error) {
        console.log(error);
      }
    },
  },
  components: { FilterDateRange },
};
</script>
  
  <style>
.dotstype {
  border-left: 10px dotted #2e7be6;
  height: 40px;
  left: 47%;
  top: 105px;
}
</style>
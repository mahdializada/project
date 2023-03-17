<template>
  <div class="d-flex align-center">
    <div
      class="white ms-1 rounded d-flex align-center justify-center"
      style="height: 80px; min-width: 30px; max-width: 30px"
    >
      <v-icon @click="scrollTo('left')" class="primary--text"
        >mdi-chevron-left</v-icon
      >
    </div>
    <!-- <v-btn fab x-small class=" btn-background me-1" @click="scrollTo('left')">
               <v-icon>mdi-chevron-left</v-icon>
          </v-btn> -->
    <div
      id="scroll-box"
      class="d-flex align-center px-1"
      style="overflow-x: hidden; scroll-behavior: smooth"
      v-if="api_loading"
    >
      <v-card
        class="custom-card mx-1 d-flex"
        style="min-width: 200px"
        height="80"
        elevation="0"
        v-for="item in 7"
        :key="item"
      >
        <div class="d-flex align-center px-1">
          <v-avatar class="blue lighten-5" size="60"></v-avatar>
          <div class="ps-2 d-flex flex-column">
            <p
              class="mb-0 primary--text align-self-start"
              style="font-size: 12px; opacity: 0.6"
            ></p>
            <h4 class="primary--text"></h4>
          </div>
        </div>
      </v-card>
    </div>
    <div
      id="scroll-box"
      class="d-flex align-center px-1"
      style="overflow-x: hidden; scroll-behavior: smooth"
      v-if="!api_loading"
    >
      <v-card
        class="custom-card mx-1 d-flex"
        style="min-width: 200px"
        height="80"
        elevation="0"
        v-for="(item, index) in card_items"
        :key="index"
      >
        <div class="d-flex align-center px-1">
          <v-avatar class="blue lighten-5" size="60">
            <SvgIcon
              style="height: 26.16px; width: 26.16px"
              :icon="getIcon(index)"
            ></SvgIcon>
          </v-avatar>
          <div class="ps-2 d-flex flex-column">
            <p
              class="mb-0 align-self-start text-wrap"
              style="font-size: 12px; color: #2e7be6; opacity: 0.6"
            >
              {{ $tr(index) }}
            </p>
            <h4 style="color: #2e7be6">{{ item }}</h4>
          </div>
        </div>
      </v-card>
    </div>
    <div
      class="white ms-1 rounded d-flex align-center justify-center"
      style="height: 80px; min-width: 30px; max-width: 30px"
    >
      <v-icon @click="scrollTo('right')" class="primary--text"
        >mdi-chevron-right</v-icon
      >
    </div>
  </div>
</template>

<script>
import SvgIcon from "../../design/components/SvgIcon.vue";

export default {
  props: {
    card_items: {
      type: Object,
      require: true,
    },
    api_loading: {
      type: Boolean,
      require: true,
    },
  },
  data() {
    return {
      graphCurrentTabIndex: 0,
      scroll_left: 0,
      scroll_right: 0,
    };
  },
  methods: {
    scrollTo(direction) {
      const scrollBox = document.getElementById("scroll-box");
      if (direction == "left") {
        scrollBox.scrollLeft -= 500;
      } else {
        scrollBox.scrollLeft += 500;
      }
      this.scroll_left = scrollBox.scrollLeft;
    },
    getIcon(icon) {
      switch (icon) {
        case "total_labels":
          return "label-icon";
        case "total_remarks":
          return "remark-icon2";
        case "total_bid_history":
          return "bid-icon2";
        case "total_budget_history":
          return "budget-icon2";
        case "total_bid":
          return "bid_icon";
        case "total_budgets":
          return "budget_icon";
        case "cpo":
          return "cpo_icon";
        case "total_orders":
          return "total_orders";
        case "total_spend":
          return "total_spends";
        case "result":
          return "total_result";

          break;
      }
    },
  },
  components: { SvgIcon },
};
</script>

<style scoped>
.btn-background {
  background: #2e7be622 !important;
  color: var(--v-primary-base) !important;
}
</style>

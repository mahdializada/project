<template>
  <v-tabs
    v-model="currentTabIndex"
    height="40"
    background-color="tabBackground"
    active-class="activeClass"
    show-arrows
    :hide-slider="true"
    class="custom"
    center-active
    id="tabs"
  >
    <client-only>
      <v-tab
        v-for="(item, i) in items"
        :key="i"
        :id="i"
        outline
        :class="`px-3 me-1 customTab ${!$vuetify.rtl ? 'ltrTab' : 'rtlTab'}`"
        :style="`z-index: ${
          item.isSelected ? items.length + 1 : items.length - i
        }`"
      >
        <span
          v-html="item.icon"
          class="d-flex align-center me-2"
          :style="`color: ${
            item.isSelected
              ? 'var(--v-friqiBase-base) !important'
              : 'var(--v-surface-base) !important'
          }`"
        ></span>
        <p
          :class="`${
            item.isSelected ? 'selected' : 'not-selected'
          } tab-title text-capitalize mb-0`"
          style="white-space: nowrap"
        >
          {{ $tr(item.text) }}
        </p>
        <div class="ms-2 selected__item__container" style="color: black">
          <div
            v-if="selectedCount(item)"
            class="d-flex justify-space-around align-center selected_chip rounded-pill"
          >
            <v-tooltip bottom color="primary" nudge-bottom="8px">
              <template v-slot:activator="{ on, attrs }">
                <div class="text-capitalize" v-bind="attrs" v-on="on">
                  {{ $tr("number_item_selected", selectedCount(item)) }}
                </div>
              </template>
              <v-list-item
                class="white--text px-0"
                dense
                v-for="(selected_name, i) in getSelectedInfo(item)"
                :key="i"
              >
                <v-list-item-icon class="me-2">
                  <v-avatar color="blue-grey darken-3" size="25">
                    <img
                      :src="selected_name.avatar"
                      alt=""
                      v-if="['company', 'platform'].includes(item.key)"
                    />
                    <FlagIcon
                      v-else-if="['country'].includes(item.key)"
                      :flag="selected_name.avatar"
                      :round="true"
                    />
                    <span v-else class="white--text text-h6 text-uppercase">
                      {{ selected_name?.charAt(0) }}
                    </span>
                  </v-avatar>
                </v-list-item-icon>

                <v-list-item-title
                  class="ps-2"
                  v-if="['country', 'company', 'platform'].includes(item.key)"
                  >{{ selected_name.name }}</v-list-item-title
                >
                <v-list-item-title class="ps-2" v-else>{{
                  selected_name
                }}</v-list-item-title>
              </v-list-item>
            </v-tooltip>
            <div @click.stop="unselect(item, i)" class="item__icon">
              <v-icon small>mdi-close</v-icon>
            </div>
          </div>
        </div>
        <!-- v-if="!selectedCount(item)" -->

        <v-chip
          v-on:mouseover="onCountHover(item, i)"
          v-on:mouseleave="mouseup"
          class="ms-1 tab-chip"
          :color="item.isSelected ? 'primary' : 'white'"
          :text-color="item.isSelected ? 'white' : 'primary'"
          small
          v-text="
            typeof item.count === 'object' ? item.count.total : item.count
          "
        >
        </v-chip>
      </v-tab>
    </client-only>
  </v-tabs>
</template>

<script>
import FlagIcon from "../common/FlagIcon.vue";
export default {
  props: {
    items: {
      required: true,
    },
    totalRecords: {
      required: true,
    },
  },
  watch: {
    currentTabIndex: function (index, prev_index) {
      this.$emit("prev_index", prev_index);
      this.$emit("onChange", index);
      this.items.forEach((item) => (item.isSelected = 0));
      this.items[index].isSelected = 1;
    },
  },
  data() {
    return {
      currentTabIndex: 0,
      tabChipKey: 0,
    };
  },
  methods: {
    mouseup() {
      this.$emit("closePrompt");
    },
    onCountHover(item, i) {
      if (
        item?.key == "ad" ||
        item?.key == "ad_set" ||
        item?.key == "campaign" ||
        item?.key == "ad_account" ||
        item?.key == "item_code"
      ) {
        var e = window.event;
        let pointerX = e.pageX - 408;

        this.$emit("showPrompt", { data: item.count, ofset: pointerX });
      }
    },
    unselect(item, index) {
      item.selectedItems = [];
      this.$emit("unselect", index);
    },
    selectedCount(item) {
      if (item) {
        const { selectedItems } = item;
        return selectedItems?.length;
      }
      return 0;
    },
    getSelectedInfo(item) {
      let tabs = [
        "item_code",
        "sales_type",
        "ispp_code",
        "platform",
        "ad",
        "country",
        "company",
      ];
      if (!tabs.includes(item.key)) {
        return item.selectedItems.map((row) => row.name);
      }
      switch (item.key) {
        case "country":
          return item.selectedItems.map((row) => {
            return {
              name: row.name,
              avatar: row.iso2?.toLowerCase(),
            };
          });
        case "company":
          return item.selectedItems.map((row) => {
            return {
              name: row.name,
              avatar: row.logo,
            };
          });
        case "platform":
          return item.selectedItems.map((row) => {
            return {
              name: row.platform_name,
              avatar: row.logo,
            };
          });
        case "item_code":
          return item.selectedItems.map((row) => row.pname);
        case "sales_type":
          return item.selectedItems.map((row) => row.sales_type);
        case "ispp_code":
          return item.selectedItems.map((row) => row.ispp_code);

        case "ad":
          return item.selectedItems.map((row) => row.ad_name);
      }
      return [];
    },
  },
  components: { FlagIcon },
};
</script>

<style>
.customTab.v-tab {
  max-width: unset !important;
}
.selected__item__container {
  width: 150px;
}
.selected_chip {
  padding-bottom: 2px;
  padding-top: 2px;
  background-color: var(--v-secondary-lighten1);
  min-width: 80px !important;
}
.customTab {
  display: flex;
  justify-content: space-between;
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  position: relative;
  background-color: var(--v-primary-base);
  border-top: 0.2px solid var(--v-surface-lighten1);
  border-left: 0.2px solid var(--v-surface-lighten1);
  width: 100%;
}
.customTab::before {
  background-color: unset !important;
}
.customTab:after {
  content: " ";
  position: absolute;
  display: block;
  width: 30px;
  height: 100%;
  top: 0;
  right: 0;
  z-index: -1;
  background-color: var(--v-primary-base);
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  transform-origin: top right;
  -ms-transform: skew(25deg, 0);
  -webkit-transform: skew(25deg);
  transform: skew(25deg);
  border-right: 0.2px solid var(--v-surface-lighten1);
}
.activeClass:after {
  background-color: var(--v-surface-lighten1);
  border-right: 0.2px solid var(--v-tabBackground-darken2);
}
.activeClass {
  background-color: var(--v-surface-lighten1);
  color: var(--v-primary-base) !important;
  border-top: 0.2px solid var(--v-tabBackground-darken2);
  border-left: 0.2px solid var(--v-tabBackground-darken2);
}
.custom .v-slide-group__prev,
.custom .v-slide-group__next {
  background-color: var(--v-background-base);
}
.custom .v-slide-group__next .v-icon,
.custom .v-slide-group__prev .v-icon {
  color: var(--v-primary-base) !important;
}

.custom .v-slide-group__next .v-icon.v-icon--disabled,
.custom .v-slide-group__prev .v-icon.v-icon--disabled {
  color: var(--v-primary-lighten3) !important;
}

.custom .v-ripple__container {
  display: none !important;
}
</style>
<style scoped>
.v-tooltip__content {
  opacity: 1 !important;
}
.v-tooltip__content::after {
  content: " ";
  position: absolute;
  bottom: 100%; /* At the top of the tooltip */
  right: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent transparent var(--v-primary-base) transparent;
}
</style>

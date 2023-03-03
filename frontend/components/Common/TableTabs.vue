<template>
  <div>
    <v-tabs v-model="tabIndex" height="40" :background-color="tabItems.length < 3 ? `primary` : `tabBackground`"
      active-class="activeClass" show-arrows :hide-slider="true" class="custom" center-active>
      <client-only>
        <v-tab v-for="(item, i) in tabItems" :key="i"
          :class="`px-3 me-1 customTab ${!$vuetify.rtl ? 'ltrTab' : 'rtlTab'}`" :style="`z-index: ${
            item.isSelected ? tabItems.length + 1 : tabItems.length - i
          }`">
          <span :class="`${item.isSelected ? 'selected' : 'not-selected'} tab-icon`">
            <v-icon left small class="me-1" :color="`${item.isSelected ? 'primary' : 'white'}`">{{ item.icon }}</v-icon>
          </span>
          <p :class="`${
            item.isSelected ? 'selected' : 'not-selected'
          } tab-title text-capitalize mb-0`">
            {{ $tr(item.text) }}
          </p>
          <v-chip class="ms-1 tab-chip" :color="item.isSelected ? 'primary' : 'white'"
            :text-color="item.isSelected ? 'white' : 'primary'" small v-text="getTotals(tabItems[i].key)" />
        </v-tab>
      </client-only>
    </v-tabs>
  </div>
</template>

<script>
export default {
  name: "TableTabs",
  props: {
    tabData: {
      type: Array,
      required: true,
    },

    extraData: {
      required: true,
    },
  },
  data() {
    return {
      tabIndex: 0,
      tabItems: this.tabData,
    };
  },

  watch: {
    tabIndex: function (val, oldValue) {
      this.tabItems[val].isSelected = true;
      this.tabItems[oldValue].isSelected = false;
      this.$emit("getSelectedTabRecords", this.tabItems[val].key);
    },
  },

  methods: {
    getTotals(key) {
      if (typeof this.extraData === "string") {
        return this.$store.getters[this.extraData](key);
        
      }
      else {
        for (const item in this.extraData) {
          const camelToSnakeCase = item
          .replace(/[A-Z]/g, (letter) => ` ${letter.toLowerCase()}`)
          .replace("total", "")
          .trim();
          if (camelToSnakeCase === key) {
            return this.extraData[item];
          }
        }
      }

      return 0;
    },
  },
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

.custom .v-tab.v-tab {
  /* min-width: 300px !important;
  max-width: 400px !important; */
  min-width: auto !important;
  max-width: auto !important;
}
</style>

<template>
  <v-card class="library_custom_tab">
    <v-tabs color="deep-purple accent-4" v-model="selectedTab">
      <v-tab v-for="(tab, index) in tabs" :key="index" :ripple="false">
        <v-icon left>{{ tab.icon }}</v-icon>
      <span class="me-1">{{ $tr(tab.label) }}</span>
      <v-badge
            class="v-chip v-size--small"
            v-if="$route.params.filter_type=='media'"
      >
      {{ extraData[tab.label]??0 }}
    </v-badge>&nbsp;
    </v-tab>
  </v-tabs>
  </v-card>
</template>
<script>
export default {
  watch: {
    selectedTab: function (index) {
      const tab = this.tabs[index].value;
      this.$emit("input", tab);
    },
  },
  props: {
    tabs: {
      default: () => {
        return [
          { icon: "mdi-folder", label: "all", value: "all" },
          { icon: "mdi-folder-check", label: "publish", value: "publish" },
          { icon: "mdi-folder-open", label: "not_publish", value: "not publish", },
          { icon: "mdi-folder-remove", label: "rejected", value: "rejected" },
        ];
      },
    },
  },
  data() {
    return {
      selectedTab: null,
      tabKey: "all",
      extraData:[],
    };
  },
  mounted() {
    this.$root.$on("extraDataInContentLibrary", (data) => {
     if(this.$route.params.filter_type=='media')
       this.extraData = data;
    });
  },
  beforeDestroy() {
    // removing eventBus listener
    this.$root.$off("extraDataInContentLibrary");
  },
};
</script>

<style>
.library_custom_tab {
  position: absolute;
  bottom: 0;
  left: 10px;
}

.library_custom_tab,
.v-slide-group__wrapper,
.v-tabs-bar__content,
.v-tabs-bar {
  background: transparent !important;
}

.library_custom_tab {
  box-shadow: none !important;
  z-index: 0;
}

.library_custom_tab .v-tab {
  /* background-color: red; */
  background-color: #58b2eb;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  color: white !important;
  text-transform: capitalize;
  position: relative;
  margin-right: 20px;
  padding-right: 10px !important;
}

.library_custom_tab .v-tab::after {
  content: "";
  width: 24px;
  height: 100%;
  position: absolute;
  border-top-right-radius: 5px;
  right: -15px;
  background-color: #58b2eb;
  z-index: 10;
  transform: skewX(20deg);
}
.library_custom_tab .v-tab:last-child::after {
  content: "";
  width: 24px;
  height: 143%;
  bottom: -20px;
  position: absolute;
  border-top-right-radius: 5px;
  right: -15px;
  background-color: #58b2eb;
  z-index: 10;
  transform: skewX(20deg);
}

.library_custom_tab .v-icon {
  margin-right: 10px !important;
  color: white !important;
}

.library_custom_tab .v-tab--active{
  background-color: white;
  color: #2e7be6 !important;
  z-index: 10;
}

.library_custom_tab .v-tab--active .v-icon {
  color: #2e7be6 !important;
}
.library_custom_tab .v-tab--active .v-chip {
  background-color: #2e7be6 !important;
  color: white !important;
}

.library_custom_tab .v-tab--active::after {
  background-color: white !important;
}

.library_custom_tab .v-tab--active:hover::before {
  background-color: white !important;
}

.library_custom_tab .v-tabs-slider {
  display: none;
}

.page_breadcumb .v-breadcrumbs {
  padding: 0;
}

.page_breadcumb .v-breadcrumbs li a {
  color: white !important;
}

.page_breadcumb .v-breadcrumbs i {
  color: white !important;
}

.title_searchinput {
  position: absolute;
  top: 0;
  right: 50px;
  top: 120px;
  /* z-index: 10; */
}

.title_searchinput input {
  background-color: white;
  width: 317px;
  height: 48px;
  padding: 10px 20px;
  z-index: 2;
  position: relative;
}

.title_searchinput input:focus-visible {
  outline-color: #2e7be6;
}

.title_searchinput::before {
  content: "";
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 329px;
  height: 59px;
  background-color: rgba(255, 255, 255, 0.3);
  border-radius: 80px;
  /* z-index: 10; */
}
.library_custom_tab .v-chip {
  background-color: white !important;
  color: #2e7be6 !important;
  padding: 0px 8px !important;
}


</style>

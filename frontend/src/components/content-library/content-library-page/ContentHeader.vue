<template>
  <span :class="headerExpand">
    <div class="library_cover d-flex px-4">
      <div class="page_title d-flex justify-start align-center h-100 pb-5">
        <div
          class="rounded-circle d-flex justify-center align-center"
          style="
            width: 65px;
            height: 65px;
            background-color: rgba(255, 255, 255, 0.3);
          "
        >
          <v-icon color="white"> mdi-filmstrip-box-multiple </v-icon>
        </div>

        <div class="pl-2">
          <h2 style="color: white">
            {{ breadCumbsItems[breadCumbsItems.length - 1].text }}
          </h2>
          <div class="page_breadcumb pt-1">
            <v-breadcrumbs :items="breadCumbsItems">
              <template v-slot:divider>
                <v-icon>mdi-chevron-right</v-icon>
              </template>
            </v-breadcrumbs>
          </div>
        </div>

        <div class="title_img">
          <img
            height="190px"
            src="../../../static/images/content-library/arabic-header-char.png"
            alt="char"
          />
        </div>

        <div class="title_searchinput">
          <input
            class="rounded-pill pr-7"
            type="text"
            v-model="searchData"
            placeholder="Search"
            @keyup="searchMedia"
          />
          <v-btn
            class="title_search_icon"
            icon
            color="black"
            @click="searchMedia({ key: 'Enter' })"
          >
            <v-icon>mdi-magnify</v-icon>
          </v-btn>
        </div>

        <ContentTabs
          class="header___tabs"
          v-model="selectedTab"
          @input="
            (tab) => {
              selectedTab = tab;
              searchData = '';
            }
          "
        ></ContentTabs>
      </div>
    </div>
    <div
      class="act_btn"
      style="padding: 10px; background-color: white; width: 100%"
    >
      <ContentAction
        @openInsertDialog="openInsertDialogNow"
        @openLabelDialog="openLabelDialogNow"
        @openRemarkDialog="openRemarkDialog"
        @onSelectAll="onSelectAll"
        @openChangeStatusDialog="openChangeStatusDialogNew"
        @onFilter="toggleFilter()"
        @onDelete="onDelete($event)"
        @onDateChanged="onDateChanged($event)"
        :filters.sync="filters"
        :filter_type="filter_type"
        :selectedItems.sync="selectedItems"
      ></ContentAction>
    </div>

    <div :class="`filter_holder ${showDrawer ? 'active_properties' : ''}`">
      <Properties
        :item="showItem"
        v-show="showMediaProfile"
        @closeProperties="closeProp"
      />
      <landing-page-media-filter
        @filter="applyFilter"
        ref="filterRefs"
        :filter.sync="filters"
        :scroll="false"
        :withPcode="true"
        v-show="show_filter"
        @close="closeProp"
        :isContentLibrary="true"
      />
    </div>
  </span>
</template>

<script>
import ContentTabs from "./ContentTabs.vue";
import ContentAction from "./ContentAction.vue";
import Properties from "./Properties.vue";
import LandingPageMediaFilter from "../../advertisement/product-statistics-profile/LandingPageMediaFilter.vue";
export default {
  components: {
    ContentTabs,
    ContentAction,
    Properties,
    LandingPageMediaFilter,
  },
  props: ["headerExpand", "showItem", "selectedItems", "filter_type"],

  watch: {
    selectedTab: async function (tab) {
      await this.resetAll();
      this.$emit("onTabChanged", { tab: tab, page: 1 });
    },
  },
  data() {
    return {
      filters: [],
      show_filter: false,
      showMediaProfile: false,
      showDrawer: false,
      breadCumbsItems: [
        {
          text: "Content Library Management System",
          disabled: false,
          href: "breadcrumbs_dashboard",
        },
        {
          text: "Content Library",
          disabled: false,
          href: "breadcrumbs_link_1",
        },
      ],
      tabs: [
        { icon: "mdi-account", label: "All" },
        { icon: "mdi-account", label: "Publish" },
        { icon: "mdi-account", label: "Not Publish" },
        { icon: "mdi-account", label: "Rejected" },
      ],
      selectedTab: null,
      searchData: "",
      searchPrevData: "",
      isSearching: false,
    };
  },

  methods: {
    searchMedia(e) {
      if (
        e.key == "Enter" &&
        this.searchData != "" &&
        this.searchData != this.searchPrevData
      ) {
        this.$emit("searchMedia", this.searchData);
        this.searchPrevData = this.searchData;
        this.isSearching = true;
      }
      if (e.key == "Backspace" && this.searchData == "" && this.isSearching) {
        this.$emit("searchMedia");
        this.isSearching = false;
      }
    },
    toggleFilter() {
      this.show_filter = true;
      this.showDrawer = true;
      this.showMediaProfile = false;
    },

    resetAll() {
      this.onSelectAll(false);
      this.searchValue = "";
      this.oldSearchValue = "";
    },
    applyFilter(data) {
      this.filters = data;
      this.$emit("filter", { filter_data: data });
      this.closeProp();
    },
    onDateChanged(date) {
      this.$emit("filter", { date });
      this.closeProp();
    },
    openInsertDialogNow() {
      this.$emit("openInsertDialogNow");
    },
    openLabelDialogNow() {
      this.$emit("openLabelDialogNow");
    },
    openRemarkDialog() {
      this.$emit("openRemarkDialog");
    },

    openInsertDialogNow() {
      this.$emit("openInsertDialogNow");
    },
    openChangeStatusDialogNew() {
      this.$emit("openChangeStatusDialogNew");
    },

    showProp() {
      this.showDrawer = true;
      this.showMediaProfile = true;
      this.show_filter = false;
    },

    closeProp() {
      this.showDrawer = false;
      this.showMediaProfile = false;
      this.show_filter = false;
      this.$emit("closeProp");
    },

    onScrollH() {
      let proper = document.querySelector(".filter_holder");
      proper.style.height = "72vh";

      // if(!proper.classList.contains("onsScrollHeight")){
      // 	proper.classList.add('onsScrollHeight')
      // }else{
      // 	proper.classList.remove('onsScrollHeight')
      // }
    },
    onScrollHRemove() {
      let proper = document.querySelector(".filter_holder");
      proper.style.height = "62vh";
    },
    onSelectAll(data) {
      this.$emit("onSelectAll", data);
    },
    onSearch() {
      if (this.oldSearchValue != this.searchValue)
        this.$emit("onTabChanged", {
          searchValue:
            this.searchValue === "" ? "clearSearch" : this.searchValue,
          page: 1,
        });

      this.oldSearchValue = this.searchValue;
    },

    onDelete(id) {
      this.$emit("onDelete", id);
    },
  },
};
</script>

<style scoped>
.onsScrollHeight {
  height: 72vh !important;
}

.filter_holder {
  width: 0;
  transition: 0.2s;
  position: absolute;
  right: 0;
  overflow: auto;
  background: white;
  min-height: 62vh;
}

.active_properties {
  width: 350px;
  transition: 0.2s;
  border: 1px solid rgb(232, 232, 232);
}
</style>

<style>
.library_cover {
  width: 100%;
  height: 190px;
  background-image: linear-gradient(to right, #2e7be6, #2ebae6);
  position: relative;
  transition: all 0.2s ease;
}
.title_img {
  position: absolute;
  right: 0;
  top: 0;
}

.title_search_icon {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  z-index: 2;
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
  transition: all 0.2s ease-out;
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

.collapse_header {
  display: block;
  /* box-shadow: 0 6px 20px 20px rgba(255, 255, 255, 0.3);
  -webkit-box-shadow: 0 6px 20px 20px rgba(255, 255, 255, 0.3);
  -moz-box-shadow: 0 6px 20px 20px rgba(255, 255, 255, 0.3); */
  box-shadow: -1px 13px 21px -10px rgba(0, 0, 0, 0.1);
  -webkit-box-shadow: -1px 13px 21px -10px rgba(0, 0, 0, 0.1);
  -moz-box-shadow: -1px 13px 21px -10px rgba(0, 0, 0, 0.1);
}

.collapse_header .library_cover {
  height: 80px !important;
  transition: all 0.2s ease;
  /* background: white !important; */
}

.collapse_header .page_title > div:not(.title_searchinput, .header___tabs) {
  display: none !important;
  transition: all 0.2s ease;
}

.collapse_header .title_searchinput {
  top: 17px !important;
  /* display: sticky; */
  /* top: 20px !important; */
  transition: all 0.2s ease-out;
}

/* .collapse_header .title_searchinput input {
  background-color: #ebebeb;
} */

/* .collapse_header .library_custom_tab .v-tab {
  background-color: #f7f7f7;
  color: #6a6a6a !important;
  min-width: 0;
} */

/* .collapse_header .library_custom_tab .v-tab span {
  display: none;
} */

/* .collapse_header .library_custom_tab .v-tab::after {
  background-color: #f7f7f7;
} */

/* .collapse_header .library_custom_tab .v-icon {
  color: var(--v-primary-base) !important;
} */

/* .collapse_header .library_custom_tab .v-tab--active {
  background-color: var(--v-primary-base);
  color: white !important;
} */
/* 
.collapse_header .library_custom_tab .v-tab--active::after {
  background-color: var(--v-primary-base) !important;
} */

/* .collapse_header .library_custom_tab .v-tab--active .v-icon {
  color: white !important;
} */

/* .collapse_header .act_btn {
  border-top: 1px solid #eaeaea;
} */
</style>

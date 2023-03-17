<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <client-only>
      <ColumnSetting ref="ColumnSettingRefs" />
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`column_setting`"
            Title="column_setting"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>

        <!--start  page filter  -->
        <v-col cols="12">
          <PageFilters
            ref="pageFilterRef"
            :downloadContent="[]"
            :selected_header="[]"
            :downloadDialog="false"
            :showBulkUpload="false"
            :showFilter="false"
            :show-add-note="false"
            @searchById="searchById"
            @clearAndUnselectId="clearAndUnselectId"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            :showDownload="false"
            :showColumn="false"
          >
            <CustomButton
              @click="addColumnSetting"
              icon="fa-plus"
              text="add_category"
              type="primary"
            />
          </PageFilters>
        </v-col>
        <!--end  page filter  -->

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('drv')"
            :showEdit="false"
            :showAutoEdit="false"
            :showDelete="$isInScope('drd')"
            :showSelect="$isInScope('dru')"
            :showApply="$isInScope('dru')"
            :showAssign="false"
            :showApprove="false"
            :showUnAssign="false"
            :selectedItems.sync="selectedItems[tabIndex]"
            :tab-key.sync="tabItems[tabIndex].key"
            :showBlock="false"
            :statusItems="statusItems"
            :routeName="'column-setting'"
            :subSystemName="'Advertisement'"
            :noReasonSubmitOperations="'share,personal'"
          />
        </v-col>
        <v-col cols="12">
          <v-row class="mx-0">
            <v-col cols="12 pa-0">
              <v-tabs
                v-model="tabIndex"
                height="40"
                background-color="#F0F3F6"
                active-class="activeClass"
                :hide-slider="true"
                class="custom"
                show-arrows
                dark
                center-active
              >
                <client-only>
                  <v-tab
                    v-for="(item, i) in tabItems"
                    :key="i"
                    :class="`px-3 me-1 customTab ${
                      !$vuetify.rtl ? 'ltrTab' : 'rtlTab'
                    }`"
                    :style="`z-index: ${
                      item.isSelected
                        ? tabItems.length + 1
                        : tabItems.length - i
                    }`"
                    class="d-flex justify-space-between"
                  >
                    <span class="d-flex">
                      <span
                        :class="`${
                          item.isSelected ? 'selected' : 'not-selected'
                        } tab-icon`"
                      >
                        <v-icon left small class="me-1">{{ item.icon }}</v-icon>
                      </span>
                      <p
                        :class="`${
                          item.isSelected ? 'selected' : 'not-selected'
                        } tab-title text-capitalize mb-0`"
                      >
                        {{ $tr(item.text) }}
                      </p>
                    </span>

                    <span>
                      <!-- <v-chip
                        v-if="selectedItems[tabIndex].length > 0"
                        class="mx-1"
                        :color="tabIndex == 0 ? 'primary' : 'white'"
                        label
                        outlined
                      >
                        {{
                          $tr("selected") + " " + selectedItems[tabIndex].length
                        }}
                      </v-chip> -->

                      <v-chip
                        :key="tabChipKey"
                        class="ms-1 tab-chip"
                        :color="item.isSelected ? 'primary' : 'white'"
                        :text-color="item.isSelected ? 'white' : 'primary'"
                        small
                        v-text="getTotal(tabItems[i].key)"
                      />
                    </span>
                  </v-tab>
                </client-only>
              </v-tabs>
            </v-col>
          </v-row>

          <DataTable
            ref="projectTableRef"
            :headers="headers"
            :itemsPerPage="1000"
            :items="category_data"
            :ItemsLength="getTotal(tabItems[tabIndex].key)"
            v-model="selectedItems[tabIndex]"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :key="tableKey"
            :single-select="true"
          >
            <template v-slot:[`item.code`]="{ item }">
              <span
                @click="() => viewProfile(item)"
                class="mx-1 primary--text font-weight-bold"
              >
                {{ item.code }}
              </span>
            </template>
            <template v-slot:[`item.user`]="{ item }">
              {{ item.user.firstname + " " + item.user.lastname }}
            </template>
            <template v-slot:[`item.category_id`]="{ item }">
              {{ getCategory(item) }}
            </template>
            <template v-slot:[`item.text`]="{ item }">
              {{ $tr(item.text) }}
            </template>
            <template v-slot:[`item.system_logo`]="{ item }">
              <v-avatar size="30" color="primary">
                <v-img
                  class="rounded-circle"
                  lazy-src="https://picsum.photos/id/11/10/6"
                  :src="item.logo"
                ></v-img>
              </v-avatar>
            </template>

            <template v-slot:[`item.sub_systems`]="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    {{ $tr("sub_system") }}
                  </span>
                </template>
                <span
                  v-for="sub_system in item.sub_systems"
                  :key="sub_system.id"
                >
                  {{ $tr(sub_system.name) }} <br />
                </span>
              </v-tooltip>
            </template>
          </DataTable>
        </v-col>
        <progress-bar v-if="isFetchCancels" />
      </v-row>
      <br />
      <br />
      <br />
      <br />
      <br />
    </client-only>
  </v-container>
</template>

<script>
import { mapActions, mapGetters, mapState } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import column_setting from "~/configs/pages/column_setting";
import ProgressBar from "~/components/common/ProgressBar.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import ColumnCategoryOperation from "../../../components/customizeColumn/ColumnCategory/ColumnCategoryOperation.vue";
import ColumnSetting from "../../../components/customizeColumn/ColumnSetting/ColumnSetting.vue";

export default {
  components: {
    CustomPageActions,
    PageActions,
    CustomButton,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    DataTable,
    ProgressBar,
    TableTabs,
    ColumnCategoryOperation,
    ColumnSetting,
  },

  watch: {
    tabIndex: function (index) {
      this.tabItems.forEach((item) => (item.isSelected = 0));
      this.tabItems[index].isSelected = 1;
      this.tabChange();
    },
  },

  data() {
    return {
      tabChipKey: 0,
      tabKey: "all",
      tabKeyChange: "system",
      category_data: [],
      total: 0,
      headers: column_setting(this).headers_system,
      tabItems: [
        {
          text: "system",
          icon: "fa-table",
          isSelected: 1,
          key: "system",
        },
        {
          text: "sub_system",
          icon: "fa-table",
          isSelected: 1,
          key: "sub_system",
        },
        {
          text: "columns",
          icon: "fa-table",
          isSelected: 1,
          key: "columns",
        },
      ],
      breadcrumb: column_setting(this).breadcrumb,
      selectedItems: [[], [], []],
      tableKey: 0,
      tabIndex: 0,
      options: {},
      // search
      searchId: "",
      searchContent: "",
      filterData: {},
      //register section
      statusItems: [],

      // profile section
      showProfile: false,

      // for custom columns
      selected_header: [],
      apiCalling: false,
      isFetchCancels: false,

      showProgressBar: false,
      all_data: [],
      allData: [],
    };
  },

  computed: {
    ...mapGetters({}),
  },

  methods: {
    viewProfile(item) {
      this.showProfile = true;
    },

    tabChange() {
      switch (this.tabIndex) {
        case 0:
          this.headers = column_setting(this).headers_system;
          this.allData = this.all_data;
          break;
        case 1:
          this.allData = [];
          if (this.selectedItems[0].length > 0) {
            this.allData = this.selectedItems[0][0].sub_systems;
            this.allData = this.allData.filter((row) => row.tabs.length > 0);
            this.headers = column_setting(this).headers_sub_system;
          } else {
            // this.$toastr.w(this.$tr("please_selecte_a_system"));
				this.$toasterNA("orange",this.$tr('please_selecte_a_system'));

          }
          break;
        case 2:
          this.allData = [];
          if (this.selectedItems[1].length > 0) {
            let tabs = this.selectedItems[1][0].tabs;
            tabs.forEach((row) => {
              this.allData = this.allData.concat(row.columns);
            });

            this.headers = column_setting(this).headers_columns;
          } else {
            // this.$toastr.w(this.$tr("please_selecte_a_sub_system"));
				this.$toasterNA("orange",this.$tr('please_selecte_a_sub_system'));

          }
          break;

        default:
          break;
      }
      this.category_data = this.allData;
      //  if (item.key == "sub_system") {
      //    this.tabKeyChange = "sub_system";
      //    if (this.selectedItems[0].length == 0) {
      //      this.category_data = [];
      //    } else {
      //      this.allData = [];
      //      this.selectedItems.forEach((ele) => {
      //        this.allData = [...this.allData, ...ele.sub_systems];
      //      });
      //      this.category_data = this.allData;
      //      this.headers = column_setting(this).headers_sub_system;
      //    }
      //  } else {
      //    this.tabKeyChange = "system";
      //    this.headers = column_setting(this).headers_system;
      //    this.category_data = this.all_data;
      //  }
    },

    async getRecord() {
      try {
        let data = this.$fetchOptions(
          this.tabKey,
          this.filterData,
          this.options,
          this.searchContent,
          this.searchId
        );
        const response = await this.$axios.get(
          "all-system-subsystem?with_columns=true",
          {
            params: data,
          }
        );

        if (response?.status === 200) {
          this.tabChipKey++, (this.category_data = response.data);
          this.all_data = response.data;
          this.total = this.category_data.length;
        }
      } catch (error) {}
    },

    getTotal(key) {
      switch (key) {
        case "system":
          return this.all_data.length;
        case "sub_system":
          return this.allData.length > 0 ? this.allData.length : 0;
        case "columns":
          return this.allData.length > 0 ? this.allData.length : 0;
      }
    },

    onRowClicked(item) {
      if (this.tabIndex == 0) {
        this.selectedItems[1] = [];
        this.selectedItems[2] = [];
      }
      this.selectedItems[this.tabIndex] = [item];
      this.selectedItems = [...this.selectedItems];
    },

    onTableChanges(options) {
      if (this.tabKeyChange == "system") {
        if (JSON.stringify(options) !== JSON.stringify(this.options)) {
          this.options = this._.clone(options);
          this.getRecord();
        } else this.options = this._.clone(options);
      }
    },
    addColumnSetting() {
      this.$refs.ColumnSettingRefs.toggleDialog();
    },
    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.$fetchOptions(
          this.tabKey,
          this.filterData,
          this.options,
          this.searchContent,
          this.searchId
        );

        const response = await this.$axios.post(
          "column_category/search",
          options
        ); // Change the route

        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    clearAndUnselectId(id) {
      this.selectedItems = this.selectedItems.filter((row) => row.id != id);
    },
    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.getRecord();
    },

    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },
    selectData(response) {
      try {
        if (response.status === 200) {
          const data = response.data;
          let found_data = {};
          if (data != null) {
            this.category_data = this.category_data.filter((row) => {
              if (row.id != data.id) {
                return row;
              } else {
                found_data = row;
              }
            });
            this.category_data.unshift(found_data);
            this.selectedItems?.unshift(found_data);
          }
        } else {
          this.$root.$emit("removeSearchId", this.searchId);
        }
      } catch (error) {
        console.log("error", error);
      }
    },
    getCategory(item) {
      let cat = this.selectedItems[1][0].categories.filter(
        (row) => row.id == item.category_id
      );
      if (cat.length > 0) {
        return cat[0].category_name;
      }
      return "";
    },
  },

  async created() {
    await this.getRecord();
  },
};
</script>

<style>
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
  border-top-right-radius: 8px;
  border-top-left-radius: 8px;
  position: relative;
  background-color: var(--v-primary-base);
  border-top: 0.2px solid white;
  border-left: 0.2px solid white;
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
  border-right: 0.2px solid white;
}
.activeClass:after {
  background-color: white;

  border-right: 0.2px solid #c6cedc;
}
.activeClass {
  background-color: white;
  color: var(--v-primary-base) !important;
  border-top: 0.2px solid #c6cedc;
  border-left: 0.2px solid #c6cedc;
}
.custom .v-slide-group__prev,
.custom .v-slide-group__next {
  background-color: var(--v-background-base);
  color: var(--v-background-base);
}
.custom .v-ripple__container {
  display: none !important;
}
</style>

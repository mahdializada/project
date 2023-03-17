<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <client-only>
      <ColumnCategoryOperation ref="columnCategoryRefs" />
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`column_category`"
            Title="column_category"
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
              @click="addCategory"
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
            @fetchNewData="fetchNewData"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            :showBlock="false"
            :routeName="'column_category'"
            subSystemName="Column Category"
            noReasonSubmitOperations="active,inactive"
            noReasonSubmitTabs="active,inactive"
            notAllowedTabs="all"
          />
        </v-col>
        <v-col cols="12">
          <TableTabs
            @getSelectedTabRecords="
              (key) => {
                tabKey = key;
                getRecord();
              }
            "
            :tabData="tabItems"
            :extraData="extraData"
          />

          <DataTable
            ref="projectTableRef"
            :headers="all_headers[0].selected_headers"
            :items="category_data"
            :ItemsLength="extraData.total"
            v-model="selectedItems"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :key="tableKey"
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
import category_setting from "~/configs/pages/category_setting";
import ProgressBar from "~/components/common/ProgressBar.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import ColumnCategoryOperation from "../../../components/customizeColumn/ColumnCategory/ColumnCategoryOperation.vue";

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
  },
  data() {
    return {
      tabKey: "all",
      category_data: [],
      extraData: [],

      tabItems: this.$getTabItems(["all", "active", "inactive", "removed"]),
      breadcrumb: category_setting(this).breadcrumb,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,
      options: {},
      // search
      searchId: "",
      searchContent: "",
      filterData: {},
      //register section

      // profile section
      showProfile: false,

      // for custom columns
      selected_header: [],
      apiCalling: false,
      isFetchCancels: false,

      showProgressBar: false,
      all_headers: [
        {
          key: "all",
          selected_headers: [],
          headers: [],
        },
      ],
    };
  },

  computed: {
    ...mapGetters({}),
  },

  methods: {
    viewProfile(item) {
      this.showProfile = true;
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
        this.apiCalling = true;
        this.selectedItems = [];
        const response = await this.$axios.get("column_category", {
          params: data,
        });

        if (response?.status === 200) {
          this.category_data = response.data.data;
          this.extraData = response.data;
        }
      } catch (error) {}
      this.apiCalling = false;
    },
    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },
    addCategory() {
      this.$refs.columnCategoryRefs.showDialog();
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
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "column_category_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },
  },

  async created() {
    await this.fetchHeaders();
    await this.getRecord();
  },
};
</script>

<style></style>

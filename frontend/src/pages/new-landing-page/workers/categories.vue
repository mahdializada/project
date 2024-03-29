<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <client-only>
      <v-dialog v-model="showRegisterDialog" persistent width="1300">
        <RegisterCategory
          @closeDialog="showRegisterDialog = false"
          api_url="landing/all-worker-categories"
          store_url="landing/worker-categories"
          :key="register_dialog_key"
        />
      </v-dialog>

      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="breadcrumb[1].icon"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>

        <!--start  page filter  -->
        <v-col cols="12">
          <PageFilters
            ref="pageFilterRef"
            :selected_header.sync="selected_header"
            :downloadContent="categories"
            :downloadDialog="downloadDialog"
            :filename="'Design Request'"
            @onDownload="
              () => {
                downloadDialog = !downloadDialog;
              }
            "
            @searchById="searchById"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            @clearAndUnselectId="clearAndUnselectId"
            note-title="buttons.add_project_note"
            :showBulkUpload="false"
            :showFilter="false"
            :showDownload="$isInScope('dre')"
          >
            <CustomButton
              v-if="$isInScope('drc')"
              icon="fa-plus"
              @click="openRegisterDialog()"
              :text="$tr('create_item', $tr('category'))"
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
            :showAssign="$isInScope('dra')"
            :showApprove="$isInScope('draprj')"
            :showUnAssign="$isInScope('dra')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="this.tabKey"
            :showBlock="false"
            :statusItems="statusItems"
            :routeName="'landing/worker-categories'"
            :subSystemName="'worker Category'"
            :noReasonSubmitOperations="'active, inactive, pending,removed'"
          />
        </v-col>
        <v-col cols="12">
          <TableTabs
            @getSelectedTabRecords="
              (key) => {
                tabKey = key;
                selectedItems = [];
                fetchCategories();
              }
            "
            :tabData="tabItems"
            :extraData="'new_landing/getTotal'"
          />

          <DataTable
            ref="projectTableRef"
            :headers="headers"
            :items="categories"
            :ItemsLength="getTotals(tabItems[tabIndex].key)"
            v-model="selectedItems"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :item-class="completedStyle"
            :key="tableKey"
          >
            <template v-slot:[`item.code`]="{ item }">
              <span
                @click="() => viewSubCategories(item.id)"
                class="mx-1 primary--text font-weight-bold"
              >
                {{ item.code }}
              </span>
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
import new_landing_page from "~/configs/pages/new_landing_page";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn.vue";
import ColumnHelper from "~/helpers/column-helper";
import TableTabs from "~/components/common/TableTabs.vue";
import RegisterCategory from "~/components/new_landing/registerCategoryAndSubCategory/RegisterCategory.vue";
import WorkerCategories from "~/configs/pages/landing/worker/WorkerCategory";

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
    RegisterCategory,
  },
  data() {
    return {
      tabKey: "all",

      headers: WorkerCategories(this).headers,
      tabItems: WorkerCategories(this).tabItems,
      breadcrumb: WorkerCategories(this).CategoryBreadcrumb,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,
      options: {},
      //register section

      statusItems: [],

      // profile section
      showProfile: false,

      // for custom columns
      selected_header: [],
      isFetchCancels: false,

      showProgressBar: false,
      showRegisterDialog: false,
      downloadDialog: false,
      searchId: "",
      searchContent: "",

      filterData: {},
      register_dialog_key: 0,
    };
  },

  computed: {
    ...mapGetters({
      getTotals: "new_landing/getTotal",
      apiCalling: "new_landing/isApiCalling",
      categories: "new_landing/getCategories",
    }),
  },
  async created() {
    this.fetchCategories();
  },

  methods: {
    ...mapActions({
      fetchCategoriesData: "new_landing/fetchCategories",
      searchCategory: "new_landing/searchCategory",
    }),

    async viewSubCategories(id) {
      window.location.pathname =
        "/new-landing-page/workers/subcategories/" + id;
    },

    async fetchCategories() {
      let data = this.fetchOptions();
      this.fetchCategoriesData({
        data: data,
        url: "landing/worker-categories",
      });
    },

    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.fetchCategories();
      } else this.options = this._.clone(options);
    },

    getRecord() {
      return [];
    },

    onRowClicked(item) {
      switch (item.status) {
        case "active":
          this.statusItems = [
            {
              id: "inactive",
              name: this.$tr("in_active"),
            },
            { id: "removed", name: this.$tr("removed") },
          ];
          break;
        case "inactive":
          this.statusItems = [
            {
              id: "active",
              name: this.$tr("active"),
            },
            { id: "removed", name: this.$tr("removed") },
          ];
          break;

        case "pending":
          this.statusItems = [
            {
              id: "active",
              name: this.$tr("active"),
            },
            {
              id: "inactive",
              name: this.$tr("in_active"),
            },
            { id: "removed", name: this.$tr("removed") },
          ];
          break;
        default:
          break;
      }
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    completedStyle(item) {
      return item.status == "completed" ? "completedAnimation" : "";
    },

    openRegisterDialog() {
      this.showRegisterDialog = true;
      this.register_dialog_key++;
    },

    async searchById(code) {
      const data = { code: code };
      const result = await this.searchCategory({
        data: data,
        url: "/search-category-worker",
      });
      if (result) {
        this.selectedItems = [result];
      }
    },

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.fetchCategories();
    },

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },

    fetchOptions() {
      let data = {
        key: this.getTabKey(),
      };

      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;
      return data;
    },

    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },

    // return table tab key
    getTabKey() {
      return this.tabKey;
    },
  },
};
</script>

<style></style>

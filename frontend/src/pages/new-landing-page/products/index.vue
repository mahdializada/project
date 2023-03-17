<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <client-only>
      <!-- <v-dialog v-model="createFlag" persistent width="1300">
        <CreateCategory @closeDialog="createFlag = false" />
      </v-dialog> -->

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
            :downloadContent="products"
            :downloadDialog="downloadDialog"
            :filename="'products'"
            @onDownload="
              () => {
                downloadDialog = !downloadDialog;
              }
            "
            @searchById="searchById"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            note-title="buttons.add_project_note"
            :showBulkUpload="false"
            :showDownload="$isInScope('dre')"
          >
            <CustomButton
              v-if="$isInScope('drc')"
              icon="fa-plus"
              @click="openRegisterDialog()"
              :text="$tr('create_item', $tr('skill'))"
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
            :routeName="'landing/products'"
            :subSystemName="'products'"
            :noReasonSubmitOperations="'active, inactive, pending,removed'"
          />
        </v-col>
        <v-col cols="12">
          <TableTabs
            @getSelectedTabRecords="
              (key) => {
                tabKey = key;
                selectedItems = [];
                fetchItems(this.url);
              }
            "
            :tabData="tabItems"
            :extraData="'new_landing/getTotal'"
          />

          <DataTable
            ref="projectTableRef"
            :headers="headers"
            :items="products"
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
                @click="() => viewSelectedDesignRequest(item)"
                class="mx-1 primary--text font-weight-bold"
              >
                {{ item.code }}
              </span>
            </template>
          </DataTable>
        </v-col>
        <progress-bar v-if="isFetchCancels" />
      </v-row>
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
import ProgressBar from "~/components/common/ProgressBar.vue";
import TableTabs from "~/components/common/TableTabs.vue";
// import CreateCategory from "~/components/new_landing/skills/CreateCategory.vue";
import ProductCategories from "~/configs/pages/landing/master/productCategories";

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
    // CreateCategory,
  },
  data() {
    return {
      tabKey: "all",

      headers: ProductCategories(this).headers,
      tabItems: ProductCategories(this).tabItems,
      breadcrumb: ProductCategories(this).breadcrumb,
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
      createFlag: false,
      downloadDialog: false,
      searchId: "",
      searchContent: "",

      url: "landing/products",
    };
  },

  computed: {
    ...mapGetters({
      getTotals: "new_landing/getTotal",
      apiCalling: "new_landing/isApiCalling",
      products: "new_landing/items",
    }),
  },
  async created() {
    this.fetchItems(this.url);
  },

  methods: {
    ...mapActions({
      fetchProducts: "new_landing/fetchItems",
      searchItems: "new_landing/searchItems",
    }),

    viewSelectedDesignRequest(item) {
      this.showProfile = true;
    },

    async fetchItems(url) {
      let data = this.fetchOptions();
      this.fetchProducts({ data, url });
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
      this.createFlag = true;
    },

    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.fetchItems(this.url);
      } else this.options = this._.clone(options);
    },

    async searchById(code) {
      const data = { code: code };
      const result = await this.searchItems({
        data: data,
        url: "/search-skill",
      });
      if (result) {
        this.selectedItems = [result];
      }
    },

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.fetchItems(this.url);
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

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
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

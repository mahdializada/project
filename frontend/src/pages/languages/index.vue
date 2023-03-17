<template>
  <v-container fluid class="pa-0">
    <v-dialog v-model="columnDialog" width="1300" persistent>
      <CustomizeColumn
        @saveChanges="
          (data) => {
            all_headers[0] = data;
            all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
            columnDialog = false;
          }
        "
        :page_headers.sync="all_headers[0]"
        :page_name="'languages_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <v-dialog
      v-model="addLanguageDialog"
      width="1300"
      persistent
      v-if="$isInScope('lnc') && addLanguageDialog"
    >
      <Dialog @closeDialog="toggleAddLanguage">
        <AddLanguage ref="addLanguage" :tabKey="getTabKey()" />
      </Dialog>
    </v-dialog>
    <v-dialog
      v-model="filterDialog"
      persistent
      max-width="1300"
      width="1300"
      v-if="$isInScope('lnv')"
    >
      <LanguageFilter
        @close="filterDialog = false"
        @getRecord="onFilterRecords"
      />
    </v-dialog>
    <!-- Add Bulk upload Dialog -->
    <v-row no-gutters>
      <ProgressBar v-if="showProgress" />
      <v-col cols="12">
        <PageHeader
          :Icon="`languages`"
          Title="languages"
          :Breadcrumb="breadcrumb"
        />
      </v-col>
      <v-col cols="12">
        <PageFilters
          :selected_header.sync="download_header"
          :downloadContent="download_content"
          :downloadDialog="downloadDialog"
          :filename="$tr('languages')"
          :show-bulk-upload="false"
          :showDownload="$isInScope('lne')"
          @onDownload="toggleDownload"
          @onColumn="columnDialog = true"
          @onBulkUpload="uploadDialog = true"
          @searchById="searchById"
          @onFilter="filterDialog = true"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
          <CustomButton
            v-if="$isInScope('lnc')"
            @click="toggleAddLanguage"
            icon="fa-plus"
            :text="$tr('create_item', $tr('language'))"
            type="primary"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :selectedItems="selectedItems"
          :tab-key.sync="tabKey"
          :isLanguage="true"
          :showAutoEdit="false"
          :showTracing="false"
          :showEdit="false"
          :showView="false"
          :showBlock="$isInScope('lnu')"
          :showSelect="$isInScope('lnu')"
          :showApply="$isInScope('lnu')"
          :routeName="'languages_translated'"
          :subSystemName="'Languages'"
          @fetchNewData="fetchNewData"
        />
      </v-col>
      <v-col cols="12">
        <TableTabs
          @getSelectedTabRecords="tabKeyGetter"
          :tabData="tabItems"
          :extraData="extraData"
        />
        <DataTable
          :key="tableKey"
          v-model="selectedItems"
          :headers="all_headers[0].selected_headers"
          :ItemsLength="getTotals(tabItems[tabIndex].key)"
          :items="languages"
          :loading="apiCalling"
          @click:row="onRowClicked"
          @current-data="currentData"
          @onTablePaginate="onTableChanges"
          ref="languageRef"
        >
          <template v-slot:[`item.code`]="{ item }">
            <nuxt-link :to="`/languages/${item.code}`">
              <span class="mx-1 primary--text font-weight-bold">
                {{ item.code }}
              </span>
            </nuxt-link>
          </template>
          <template v-slot:[`item.translatedCount`]="{ item }">
            <span class="mx-1">
              {{ item.translatedCount }} / {{ getTotals("totalWords") }}
            </span>
          </template>
          <template v-slot:[`item.frontendCount`]="{ item }">
            <span class="mx-1">
              {{ item.frontendCount }} / {{ getTotals("frontendWords") }}
            </span>
          </template>
          <template v-slot:[`item.backendCount`]="{ item }">
            <span class="mx-1">
              {{ item.backendCount }} / {{ getTotals("backendWords") }}
            </span>
          </template>
          <template v-slot:[`item.percentage`]="{ item }">
            <span class="mx-1">
              {{
                Number.parseFloat(
                  (
                    (item.translatedCount / getTotals("totalWords")) *
                    100
                  ).toFixed(2)
                )
              }}%
            </span>
          </template>
          <template v-slot:[`item.country_language.country.iso2`]="{ item }">
            <nuxt-link :to="`/languages/${item.code}`">
              <FlagIcon
                :flag="
                  item.country_language
                    ? item.country_language.country.iso2.toLowerCase()
                    : ''
                "
                :round="true"
              />
            </nuxt-link>
          </template>
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import HandleException from "~/helpers/handle-exception";
import { mapActions, mapGetters } from "vuex";
import Languages from "~/configs/pages/languages";
import ProgressBar from "~/components/common/ProgressBar";
import PageHeader from "~/components/design/PageHeader";
import PageFilters from "~/components/design/PageFilters";
import PageActions from "~/components/design/PageActions";
import DataTable from "~/components/design/DataTable";
import CustomButton from "~/components/design/components/CustomButton";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
// devMaster
import Dialog from "~/components/design/Dialog/Dialog.vue";
import AddLanguage from "~/components/languages/AddLanguage.vue";

// import Bulk upload component
import BulkUpload from "~/components/common/BulkUpload";
import FlagIcon from "~/components/common/FlagIcon.vue";
import LanguageFilter from "~/components/languages/LanguageFilter.vue";
import ColumnHelper from "~/helpers/column-helper";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "lnv",
  },
  name: "languages",
  components: {
    Dialog,
    AddLanguage,
    ProgressBar,
    currentItems: [],
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    CustomizeColumn,
    BulkUpload,
    FlagIcon,
    LanguageFilter,
    TableTabs,
  },

  data() {
    return {
      breadcrumb: Languages(this).breadcrumb,
      tabItems: Languages(this).tabItems,
      // devmaster
      addLanguageDialog: false,

      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,

      // page actions
      downloadDialog: false,
      download_header: [],
      download_content: [],

      // for custom columns
      selected_headers: [],
      columnDialog: false,
      all_headers: [
        {
          key: "all",
          selected_headers: [],
          headers: [],
        },
      ],

      showProgressBar: false,
      // Bulk upload flag
      uploadDialog: false,
      files: null,
      bulkData: [],
      filterDialog: false,

      filterData: {},
      options: {},
      searchContent: "",
      searchId: "",
      tabKey: "all",
      extraData: [],
    };
  },
  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      languages: "languages/getLanguages",
      itemsTotal: "languages/itemsTotal",
      apiCalling: "languages/isApiCalling",
      getTotals: "languages/getTotal",
    }),
  },
  async created() {
    await this.fetchHeaders();
    this.getRecord();
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },
  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
      fetchActiveLanguages: "languages/fetchActiveLanguages",
      fetchLanguages: "languages/fetchLanguages",
      deleteItem: "languages/deleteItem",
      insertNewItem: "languages/insertNewItem",
    }),

    tabKeyGetter(key) {
      this.tabKey = key;
      this.getRecord();
    },

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "languages_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    toggleAddLanguage() {
      this.addLanguageDialog = !this.addLanguageDialog;
      if (!this.addLanguageDialog) {
        this.$refs.addLanguage.reset();
      }
    },

    checkSelectedTab(value) {
      this.selectedItems = [];
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    async toggleDownload() {
      this.download_header = [
        { id: 1, text: "Code", value: "code" },
        { id: 2, text: "Name", value: "name" },
        { id: 3, text: "Native", value: "native" },
        { id: 4, text: "Translated", value: "translatedCount" },
        { id: 5, text: "Percentage", value: "percentage" },
        { id: 6, text: "Status", value: "status" },
        { id: 7, text: "Created By", value: "created_by" },
        { id: 8, text: "Updated By", value: "updated_by" },
      ];
      this.showProgressBar = true;
      this.download_content = await this.languages.map((row) => {
        const item = {
          code: row.code,
          name: row?.country_language?.language?.name,
          native: row?.country_language?.language?.native,
          translatedCount:
            row?.translatedCount + " / " + this.getTotals("totalWords"),
          percentage:
            (row?.translatedCount / this.getTotals("totalWords")) * 100 + "%",
          status: row?.status,
          updated_by: row?.updated_by,
          created_by: row?.created_by,
        };
        return item;
      });
      this.showProgressBar = false;
      this.downloadDialog = !this.downloadDialog;
    },

    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
    },

    currentData(e) {
      this.currentItems = e;
    },

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },

    //customize columns: called from child
    saveColumnSetting(data) {
      if (data.close_form) {
        this.columnDialog = false;
      }
      this.selected_header = data.selected_header;
      this.selected_column = data.selected_header.map((row) => row.id);
      this.category = ColumnHelper.getCategoryValue(
        data.selected_header,
        this.category
      );
    },

    // 	***********************		start No changes required		************************
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
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
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.deleteItem(data.id);
          this.insertNewItem(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
    },

    fetchOptions() {
      let data = {
        key: this.tabKey,
      };
      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;

      return data;
    },

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post(
          "languages_translated/searchLanguage",
          options
        ); // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchLanguages(data); // Change request url
        this.extraData = data2.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    // 	***********************		End NO changes required		************************
  },
};
</script>

<style scoped></style>

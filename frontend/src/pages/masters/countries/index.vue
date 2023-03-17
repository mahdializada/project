<template>
  <v-container fluid class="pa-0">
    <v-dialog v-model="columnDialog" width="1300" persistent>
      <CustomizeColumn @saveChanges="
        (data) => {
          all_headers[0] = data;
          all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
          columnDialog = false;
        }
      " :page_headers.sync="all_headers[0]" :page_name="'countries_all'" @closeColumnDialog="columnDialog = false" />
    </v-dialog>

    <v-dialog v-model="filterDialog" persistent max-width="1300" width="1300" v-if="$isInScope('cnv')">
      <CountryFilter @close="filterDialog = false" @filterRecords="onFilterRecords" />
    </v-dialog>

    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />
      <v-col cols="12">
        <PageHeader :Icon="`countries`" Title="countries" :Breadcrumb="breadcrumb" />
      </v-col>
      <v-col cols="12">
        <PageFilters ref="pageFilterRef" :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="Countries" :downloadDialog="downloadDialog" :filename="$tr('countries')"
          :show-add-note="true" @onFilter="filterDialog = true" :showDownload="$isInScope('sme')"
          note-title="add_country_note" @onDownload="toggleDownload" @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId" @searchByContent="searchByContent" @onTypeChange="onSearchTypeChange"
          @onColumn="columnDialog = true" />
      </v-col>
      <v-col cols="12">
        <PageActions :selectedItems="selectedItems" :tab-key.sync="tabKey" :showAutoEdit="false" :showTracing="false"
          :showEdit="false" :showView="false" :showDelete="false" :showBlock="$isInScope('cnu')"
          :showSelect="$isInScope('cnu')" :showApply="$isInScope('cnu')" :routeName="'countries'"
          :subSystemName="'Countries'" />
      </v-col>
      <v-col cols="12">
        <TableTabs @getSelectedTabRecords=" (key) => {
          tabKey = key;
          getRecord();
        }" :tabData="tabItems" :extraData="extraData" />
        <DataTable :key="tableKey" v-model="selectedItems" :headers="all_headers[0].selected_headers"
          :ItemsLength="extraData.total" :items="Countries" :loading="apiCalling" @click:row="onRowClicked"
          @onTablePaginate="onTableChanges" ref="countryTableRef">
          <template v-slot:[`item.code`]="{ item }">
            <span class="mx-1 primary--text font-weight-bold">
              {{ item.code }}
            </span>
          </template>
          <template v-slot:[`item.iso2`]="{ item }">
            <div class="d-flex align-center justify-center">
              <FlagIcon :flag="item.iso2.toLowerCase()" :round="true" />

            </div>
          </template>
          <template v-slot:[`item.timezones`]="{ item }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on" style="white-space: nowrap" class="primary--text">
                  {{ $tr("timezones") }}
                </span>
              </template>
              <div v-for="(timezone, key) in JSON.parse(item.timezones)" :key="key">
                <span class="pe-1">{{ timezone.zoneName }},</span>
                <span class="pe-1">{{ timezone.gmtOffsetName }},</span>
                <span class="pe-1">{{ timezone.abbreviation }},</span>
                <span class="pe-1">{{ timezone.tzName }}</span>
              </div>
            </v-tooltip>
          </template>
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import HandleException from "~/helpers/handle-exception";
import { mapActions, mapGetters, mapMutations } from "vuex";
import Countries from "../../../configs/pages/countries";
import ProgressBar from "../../../components/common/ProgressBar";
import PageHeader from "../../../components/design/PageHeader";
import PageFilters from "../../../components/design/PageFilters";
import PageActions from "../../../components/design/PageActions";
import DataTable from "../../../components/design/DataTable";
import CustomButton from "../../../components/design/components/CustomButton";
import CustomizeColumn from "../../../components/customizeColumn/CustomizeColumn2.vue";
import FlagIcon from "../../../components/common/FlagIcon.vue";
import CountryFilter from "../../../components/countries/CountryFilter.vue";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "cnv",
  },
  name: "countries",
  components: {
    CountryFilter,
    ProgressBar,
    currentItems: [],
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    CustomizeColumn,
    FlagIcon,
    TableTabs,
  },

  data() {
    return {
      tabChipKey: 0,
      breadcrumb: Countries(this).breadcrumb,
      tabItems: Countries(this).tabItems,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,
      tabKey: "all",
      extraData: [],

      // page actions
      downloadDialog: false,

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
      type: 1,
      content: [],
      searchContent: "",
      searchId: "",
      filterData: {},
      options: {},
      filterDialog: false,
    };
  },
  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      Countries: "countries/getCountries",
      itemsTotal: "countries/itemsTotal",
      apiCalling: "countries/isApiCalling",
      getTotals: "countries/getTotal",
    }),
  },
  async created() {
    //customize columns
    await this.fetchHeaders();
    await this.getRecord();
  },

  // async fetch() {
  // },
  async mounted() {
    this.$echo.private(`update.country`).listen("Updated", async (e) => {
      if (e.action == "status-changed") {
        let res = await this.fetchNewCountry(e.data.item);
        this.set_status_for_country({
          new_status: e.data,
          item: res,
          tabKey: this.tabKey,
        });
      }
      await this.fetchNewRealtime();
      this.tabChipKey++;
    });
  },
  beforeDestroy() {
    this.$echo.leave("update.country");
  },
  methods: {
    ...mapActions({
      fetchCountries: "countries/fetchCountries",
      changeRecordStatus: "countries/changeRecordStatus",
      deleteRecord: "countries/deleteRecord",
      deleteItem: "countries/deleteItem",
      insertNewItem: "countries/insertNewItem",
    }),

    ...mapMutations({
      set_status_for_country: "countries/SET_STATUS_FOR_COUNTRY",
      merge_countries: "countries/MERGE_COUNTRIES",
    }),

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "countries_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) { }
    },

    // Bulk upload methods
    async fetchNewCountry(ids) {
      try {
        const response = await this.$axios.get(`countries/${ids}`, {});
        if (response?.status === 200) {
          return response?.data?.data;
        }
      } catch (error) {
        return error;
      }
    },

    async fetchNewRealtime() {
      const options = this.fetchOptions();
      const data = {
        ...options,
        itemsPerPage: 1,
        page: this.Countries.length + 1,
      };

      const response = await this.$axios.get(`countries`, {
        params: data,
      });
      this.merge_countries(response);
      this.selectedItems = [];
    },

    checkSelectedTab(value) {
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    toggleDownload() {
      this.downloadDialog = !this.downloadDialog;
    },

    // 	***********************			No changes required		************************
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    // getTabKey() {
    // 	return this.tabItems[this.tabIndex]?.key;
    // },

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

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    // 	***********************			No changes required		************************

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post(
          "countries/searchCountry",
          options
        );
        console.log(this.searchId, options);
        // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchCountries(data); // Change request url
        this.extraData = data2.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },
};
</script>

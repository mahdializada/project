<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <client-only>
      <IPAFilter ref="ipaFilter" @filterRecords="onFilterRecords" />
      <StepperComponent
        ref="ipaComponentStepper"
        @pushRecord="pushRecord"
        @updateRecord="updateRecord"
      />
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
          :page_name="'ipa_all'"
          @closeColumnDialog="columnDialog = false"
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
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="downloadRecords"
          @onDownload="downloadDialog = !downloadDialog"
          :downloadDialog="downloadDialog"
          @onFilter="openFilterDialog"
          :filename="'ipa'"
          :show-add-note="$isInScope('tc')"
          :show-bulk-upload="false"
          :showDownload="$isInScope('te')"
          @onColumn="columnDialog = true"
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
          <CustomButton
            v-if="$isInScope('advc')"
            icon="fa-plus"
            @click="openInsertDialog"
            :text="$tr('create_item', $tr('ipa'))"
            type="primary"
          />
        </PageFilters>
        <!--end  page filter  -->

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            :showEdit="$isInScope('adve')"
            :showAutoEdit="false"
            :showDelete="$isInScope('advd')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :showApprove="$isInScope('adva')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            @fetchNewData="fetchNewData"
            :showBlock="false"
            :showRestore="showRestore"
            @onAutoEdit="onAutoEdit"
            @onEdit="onEdit"
            :statusItems="statusItems"
            :routeName="'single-sales/ipa'"
            :subSystemName="'ipa'"
            :noReasonSubmitOperations="'in review,in creation, in testing, sales moving,sales unstable'"
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
            ref="ipaTableRef"
            :headers="all_headers[0].selected_headers"
            :items="tableValues"
            v-model="selectedItems"
            :loading="apiCalling"
            :ItemsLength="extraData.total"
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

            <template v-slot:[`item.target_age`]="{ item }">
              <span class="mx-auto d-flex justify-center">
                {{ item.target_age_min }} - {{ item.target_age_max }}
              </span>
            </template>

            <template v-slot:[`item.target_date`]="{ item }">
              <div class="d-flex justify-left">
                <p class="mx-auto mb-0 d-flex justify-center">
                  {{ $tr("from") }}
                  {{ localeHumanReadableTime(item.target_time_start) }}
                </p>
                <p class="mx-auto mb-0 d-flex justify-center">
                  {{ $tr("to") }}
                  {{ localeHumanReadableTime(item.target_time_end) }}
                </p>
              </div>
            </template>

            <template v-slot:[`item.target_countries`]="{ item }">
              <v-tooltip
                bottom
                max-width="200"
                v-for="c in item.target_countries"
                :key="c.name"
              >
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex justify-center">
                      {{ c.name }}
                    </span>
                  </span>
                </template>
                {{ c.cities.join(",") }}
              </v-tooltip>
            </template>
          </DataTable>
        </v-col>
        <progress-bar v-if="isFetchCancels" />
      </v-row>
    </client-only>
  </v-container>
</template>

<script>
import moment from "moment-timezone";
import ipa from "~/configs/pages/ipa";
import Alert from "../../helpers/alert";
import DataTable from "~/components/design/DataTable.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import ProgressBar from "~/components/common/ProgressBar.vue";
import PageActions from "~/components/design/PageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import CustomButton from "~/components/design/components/CustomButton.vue";
import IPAFilter from "~/components/single_sales_management_system/IPAFilter";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import StepperComponent from "../../components/single_sales_management_system/ipa_component/StepperComponent";

export default {
  components: {
    CustomizeColumn,
    IPAFilter,
    CustomPageActions,
    PageActions,
    CustomButton,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    DataTable,
    ProgressBar,
    TableTabs,
    StepperComponent,
  },
  data() {
    return {
      tabKey: "all",
      tableValues: [],
      tabItems: ipa(this).tabItems,
      breadcrumb: ipa(this).breadcrumb,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,
      options: {},
      showRestore: false,
      extraData: {},
      //register section
      statusItems: [],
      downloadRecords: [],
      // profile section
      showProfile: false,

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

      apiCalling: false,
      isFetchCancels: false,
      downloadDialog: false,
      showProgressBar: false,
    };
  },

  async fetch() {
    await this.getRecord();
  },
  async created() {
    await this.fetchHeaders();
  },
  watch: {
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "ipa_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    checkSelectedTab(value) {
      // this.tableKey++;    // Commented due to pagination bug
      this.selectedItems = [];
      let currentTab = "";
      this.tabItems = this.tabItems.map((item) => {
        item.key === value
          ? ((item.isSelected = 1), (currentTab = item.key))
          : (item.isSelected = 0);
        return item;
      });
      this.setStatusItems(currentTab);
    },
    getTabKey() {
      return this.tabItems[this.tabIndex].key;
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
    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      this.selectedItems = [];
      this.checkSelectedTab(this.tabKey);
      const data = this.fetchOptions();
      const response = await this.$axios.get(`single-sales/ipa`, {
        params: data,
      });
      if (response?.status === 200) {
        this.tableValues = response.data?.data;
        this.extraData = response?.data;
      }
      this.apiCalling = false;
    },
    async pushRecord(data) {
      if (this.getTabKey() === "all" || this.getTabKey() === "pending")
        await this.tableValues.unshift(data);
      this.extraData.allTotal += 1;
      this.extraData.pendingTotal += 1;
      this.extraData.total += 1;
    },
    async updateRecord(data) {
      this.tableValues = this.tableValues.map((item) => {
        if (item?.id == data?.id) return data;
        else return item;
      });
    },
    getTotals(tabKey) {
      switch (tabKey) {
        case "all":
          return this.extraData?.allTotal || 0;
        case "pending":
          return this.extraData?.pendingTotal || 0;
        case "in review":
          return this.extraData?.inReviewTotal || 0;
        case "rejected":
          return this.extraData?.rejectedTotal || 0;
        case "in creation":
          return this.extraData?.inCreationTotal || 0;
        case "in testing":
          return this.extraData?.inTestingTotal || 0;
        case "sales moving":
          return this.extraData?.salesMovingTotal || 0;
        case "sales unstable":
          return this.extraData?.salesUnstableTotal || 0;
        case "stopped":
          return this.extraData?.stoppedTotal || 0;
        case "failed":
          return this.extraData?.failedTotal || 0;
        case "cancelled":
          return this.extraData?.cancelledTotal || 0;
      }
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },
    viewSelectedDesignRequest(item) {
      this.showProfile = true;
    },
    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    async getRecord() {
      try {
        const data = this.$fetchOptions(
          this.tabKey,
          this.filterData,
          this.options,
          this.searchContent,
          this.searchId
        );
        this.apiCalling = true;
        const response = await this.$axios.get("single-sales/ipa", {
          params: data,
        });
        if (response.status === 200) {
          this.tableValues = response.data?.data;
          this.extraData = response?.data;
        }
        this.apiCalling = false;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },
    completedStyle(item) {
      return item.status == "completed" ? "completedAnimation" : "";
    },
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
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
          "single-sales/ipa/search",
          options
        ); // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.tableValues = this.tableValues.filter(
            (row) => row.id !== data.id
          );
          this.tableValues.unshift(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },
    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
    },
    openFilterDialog() {
      this.$refs.ipaFilter.changeModel();
    },
    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },
    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.fetchDataAccordingtoStatus();
    },
    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },
    //customize columns: called from child

    openInsertDialog() {
      this.$refs.ipaComponentStepper.showDialog();
    },
    setStatusItems(status) {
      this.showRestore = false;
      switch (status) {
        case "pending":
          this.statusItems = [
            { id: "rejected", name: "rejected" },
            { id: "in creation", name: "in_creation" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;

        case "rejected":
          this.statusItems = [
            { id: "in testing", name: "in_testing" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "in creation":
          this.statusItems = [
            { id: "in testing", name: "in_testing" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;

        case "in testing":
          this.statusItems = [
            { id: "sales moving", name: "sales_moving" },
            { id: "sales unstable", name: "sales_unstable" },
            { id: "stopped", name: "stopped" },
            { id: "failed", name: "failed" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;

        case "sales moving":
          this.statusItems = [
            { id: "rejected", name: "rejected" },
            { id: "in creation", name: "in_creation" },
            { id: "in testing", name: "in_testing" },
            { id: "stopped", name: "stopped" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "sales unstable":
          this.statusItems = [
            { id: "rejected", name: "rejected" },
            { id: "in creation", name: "in_creation" },
            { id: "in testing", name: "in_testing" },
            { id: "stopped", name: "stopped" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "stopped":
          this.statusItems = [
            { id: "rejected", name: "rejected" },
            { id: "in creation", name: "in_creation" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "failed":
          this.statusItems = [
            { id: "in review", name: "in_review" },
            { id: "rejected", name: "rejected" },
            { id: "in creation", name: "in_creation" },
            { id: "in testing", name: "in_testing" },
            { id: "stopped", name: "stopped" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;

        case "cancelled":
          this.statusItems = [
            { id: "in review", name: "in_review" },
            { id: "rejected", name: "rejected" },
            { id: "in creation", name: "in_creation" },
            { id: "in testing", name: "in_testing" },
            { id: "sales moving", name: "sales_moving" },
            { id: "sales unstable", name: "sales_unstable" },
            { id: "stopped", name: "stopped" },
            { id: "failed", name: "failed" },
          ];
          break;
        case "removed":
          this.statusItems = [];
          this.showRestore = true;
        default:
          this.statusItems = [];
          break;
      }
    },
    async onEdit() {
      if (this.selectedItems.length > 1) {
        // Alert.customAlert(this, "Please select one item", "restricted");
        this.$toasterNA("primary", this.$tr("item_max_limit_text"));
      } else {
        this.$refs.ipaComponentStepper.showDialog(this.selectedItems);
      }
    },

    async onAutoEdit() {
      return;
      if (this.selectedItems.length > 0) {
        this.isEdit = true;
        this.isAutoEdit = true;
        this.editKey++;
      }
    },
  },
};
</script>

<style>
</style>

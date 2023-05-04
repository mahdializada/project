<template>
  <v-container fluid class="pa-0">

    <client-only>
      <v-dialog v-model="columnDialog" width="1300" persistent>
        <CustomizeColumn v-if="columnDialog" @saveChanges="
          (data) => {
            all_headers[selectedIndex] = data;
            columnDialog = false;
          }
        " :page_headers.sync="all_headers[selectedIndex]" :page_name="'advertisement_' + currentTab.key"
          @closeColumnDialog="columnDialog = false" :key="selectedIndex" />
      </v-dialog>
      <v-row no-gutters>
        <v-col cols="12">
          <PageHeader Icon="advertisement_management_system" :Title="breadcrumb[1].text" :Breadcrumb="breadcrumb"
            :show_ad_img="true">
          </PageHeader>
        </v-col>
        <v-col cols="12">
          <PageFilters ref="pageFilterRef" :selected_header.sync="all_headers[0].selected_headers"
            :downloadContent="tableRecords" :downloadDialog="downloadDialog"
            :filename="'advertisement-' + currentTab.key" note-title="" @onFilter="openFilterDialog"
            :showBulkUpload="$isInScope('advc')" :showDownload="$isInScope('adve')" @searchById="searchById"
            @onBulkUpload="adBulkUpload = !adBulkUpload" @onDownload="downloadDialog = !downloadDialog"
            @clearAndUnselectId="clearAndUnselectId" @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange" @onColumn="columnDialog = true">

          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions :showView="false" :showEdit="false" :showAutoEdit="false" :showDelete="false"
            :ignoreReason="false" :showSelect="false" :showApply="false" :showAssign="false" :showApprove="false"
            :showUnAssign="false" :selectedItems.sync="currentTab.selectedItems" :tab-key="'not'" :showBlock="false"
            :showRestore="false" :statusItems="[]">

          </CustomPageActions>
        </v-col>

        <v-col cols="12">
          <v-row class="mx-0">
            <v-col cols="12 pa-0">
              <AdvertisementTab :items="tabItems" @onChange="onTabChange" @unselect="onUnselected"
                :totalRecords="totalRecords" />
            </v-col>

          </v-row>

          <DataTable ref="projectTableRef" :headers.sync="all_headers[selectedIndex].selected_headers"
            :items="tableRecords" :ItemsLength="totalRecords" :itemsPerPage="20" height="870px"
            @onTablePaginate="onTableChanges" v-model="currentTab.selectedItems" :loading="apiCalling"
            @click:row="onRowClicked" :useDefaltStatus="false">
            <template v-slot:[`item.code`]="{ item }">
              <span class="mx-1 primary--text font-weight-bold">
                {{ item.code }}
              </span>
            </template>


            <template v-slot:[`item.advertisement_status`]="{ item }">
              <v-switch @click.stop="changeRecordStatus(item)" v-model="item.advertisement_status" inset
                @click.native.stop :color="primaryColor" class="mt-0" hide-details true-value="active"
                false-value="inactive" :title="'active'" :loading="status_loading && item.id == status_id"></v-switch>
            </template>

            <template v-slot:[`item.total_bid`]="{ item }">
              {{ getTotalBid(item) }}
            </template>
            <template v-slot:[`item.payment_method`]="{ item }">
              {{ getPaymentMethod(item) }}
            </template>

          </DataTable>
        </v-col>
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
import { mapActions, mapGetters } from "vuex";
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import Advertisement from "~/configs/pages/advertisement/advertisement";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import AdvertisementTab from "../../components/advertisement/AdvertisementTab.vue";
import GenerateLinkCreation from "../../components/advertisement/GenerateLinkCreation.vue";
import AdvertismentFilter from "../../components/advertisement/AdvertismentFilter.vue";
import FlagIcon from "../../components/common/FlagIcon.vue";
// import AdBulkUpload from "../../components/advertisement/bulk-upload/AdBulkUpload.vue";
import Dialog from "../../components/design/Dialog/Dialog.vue";
import LabelForm from "../../components/advertisement/label/LabelForm.vue";
import RemarkForm from "../../components/advertisement/remarks/RemarkForm.vue";
import AdvRefreshPropmpt from "../../components/advertisement/AdvRefreshPropmpt.vue";
import PopOver from "../../components/design/PopOver.vue";
import FilterDateRange from "../../components/advertisement/FilterDateRange.vue";
import RefreshPercentage from "../../components/advertisement/RefreshPercentage.vue";
import GenerateConnection from "../../components/advertisement/Connections/GenerateConnection.vue";
import AdvertisementGraph from "../../components/advertisement/AdvertisementGraph.vue";
import BudgetForm from "../../components/advertisement/budget/BudgetForm.vue";
import moment from "moment";

import AdReasons from "../../components/advertisement/AdReasons.vue";
import AdHistory from "../../components/advertisement/AdHistory.vue";
import CompaignBidHistory from "../../components/companies/CompaignBidHistory.vue";
import BidHistory from "../../components/advertisement/BidHistory.vue";
import BulkUploadStepper from "../../components/advertisement/bulk-upload2/BulkUploadStepper.vue";
import BalanceModal from "../../components/advertisement/balance/BalanceModal.vue";
import RefreshPopOver from "../../components/design/RefreshPopOver.vue";
export default {
  meta: {
    hasAuth: true,
    scope: "advv",
  },

  components: {
    CustomPageActions,
    CustomButton,
    PageHeader,
    PageFilters,
    DataTable,
    CustomizeColumn,
    AdvertisementTab,
    GenerateLinkCreation,
    FlagIcon,
    AdvertismentFilter,
    Dialog,
    // AdBulkUpload,
    LabelForm,
    RemarkForm,
    AdvRefreshPropmpt,
    PopOver,
    FilterDateRange,
    RefreshPercentage,
    GenerateConnection,
    AdvertisementGraph,
    AdReasons,
    AdHistory,
    BudgetForm,
    CompaignBidHistory,
    BidHistory,
    BulkUploadStepper,
    BalanceModal,
    RefreshPopOver
  },

  data() {
    return {
      sheets: {
        campaign: [],
        ad_set: [],
        ad: [],
        connection_data: [],
      },
      selectedTabData: {},
      adBulkUpload: false,
      copy: false,
      tableRecords: [],
      totalRecords: 0,
      headers: [],
      tabItems: Advertisement(this).upload_tabItem,
      breadcrumb: Advertisement(this).breadcrumb,
      currentTab: Advertisement(this).upload_tabItem[0],
      apiCalling: false,
      axiosSource: null,
      status_loading: false,
      updateStatusItemId: null,

      ColumnAxiosSource: null,

      filterData: {},
      type: 1,
      content: [],
      searchContent: "",
      searchId: "",
      options: {},
      filterByDate: {
        start_date: moment().format("YYYY-MM-DD"),
        end_date: moment().format("YYYY-MM-DD"),
      },
      downloadDialog: false,
      showRefresh: false,
      canRefresh: false,
      refreshTime: null,
      isRefreshing: false,
      refresh_percentage: 0,

      showLabelPopOver: false,
      showRemarkPopOver: false,
      lableRemarkErrMessage: "",
      // for custom columns
      dialogKey: 0,
      columnDialog: false,
      selected_column: [],
      selected_headers: [],
      selectedIndex: 0,
      status_id: 0,

      all_headers: [
        {
          key: "campaign",
          name: "campaign",
          selected_headers: [],
          headers: [],
        },
        {
          key: "ad_set",
          name: "Adset",
          selected_headers: [],
          headers: [],
        },
        {
          key: "ad",
          name: "ad",
          selected_headers: [],
          headers: [],
        },
      ],
      showReasonDialog: false,
      statusChangeItem: [],
      selectedCampaignBudget: [],
      fetchBudget: false,
      balanceData: null,
      showHistory: false,
      showCrmPopOver: false,
    };
  },

  async created() {
    await this.fetchHeaders();
    this.fetchItemsAccordingToTabKey(this.currentTab.key, {
      query: null,
      body: null,
    });
  },

  mounted() {
    this.getRefreshProgress();
  },

  beforeDestroy() {
    this.$echo.leave(`refresh-ads.${this.$auth.user.id}`);
  },

  watch: {
    "currentTab.selectedItems": function (value) {
      const changeItem = (item) => {
        if (item.key == this.currentTab.key) {
          item.selectedItems = value;
        }
      };
      this.tabItems.forEach(changeItem);
    },

  },
  computed: {
    ...mapGetters({
      custom_columns: "customColumns/get_custom_columns",
    }),
    primaryColor() {
      return this.$vuetify.theme.themes.light.primary == "#2E7BE6"
        ? "success"
        : "primary";
    },
  },

  methods: {
    ...mapActions({
      fetchCustomColumns: "customColumns/fetchCustomColumns",
    }),

    checkPlatforms(item) {
      const disabledPlatforms = ["google ads", "facebook"];
      if (item.platform) {
        const name = item.platform.platform_name;
        return !disabledPlatforms.includes(name);
      }
      return false;
    },

    async changeAdvertisementStatus(reasons) {
      try {
        const tab = this.currentTab.key;
        const id = this.statusChangeItem.id;
        this.updateStatusItemId = id;
        const body = {
          section: tab,
          item_id: id,
          status: this.statusChangeItem.status,
          reason_ids: reasons,
        };
        const url = `/advertisement/change-status`;
        const { data } = await this.$axios.put(url, body);
        if (data.status == "success") {
          const currentStatus = this.statusChangeItem.status;
          const newStatus = currentStatus == "ACTIVE" ? "INACTIVE" : "ACTIVE";
          if (this.updateStatusItemId == this.statusChangeItem.id) {
            this.statusChangeItem.status = newStatus;
          }
          const message = this.$tr(
            "record_item_status_changed",
            this.$tr(data.type)
          );
          // this.$toastr.s(message);
          this.$toasterNA("green", this.$tr(message));
        } else {
          // this.$toastr.e(this.$tr(data.status));
          this.$toasterNA("red", this.$tr(data.status));
        }
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
      this.updateStatusItemId = null;
    },

    async submitReasons(reasons) {
      const currentTab = this.currentTab.key;
      const statusTabs = ["campaign", "ad_set", "ad"];
      if (statusTabs.includes(currentTab)) {
        this.changeAdvertisementStatus(reasons);
        this.closeReasonDialog();
        return;
      }
      const data = {
        model: this.currentTab.key,
        id: this.statusChangeItem.id,
        status: this.statusChangeItem.advertisement_status,
        reason_ids: reasons,
      };
      try {
        const response = await this.$axios.post("assign-reason", data);
      } catch (error) {
        this.$toasterNA("red", this.$tr("something_went_wrong"));
        this.closeReasonDialog();
      }
      this.showReasonDialog = false;
      this.status_loading = false;
    },
    closeReasonDialog() {
      this.showReasonDialog = false;
      this.status_loading = false;
      const currentTab = this.currentTab.key;
      const statusTabs = ["campaign", "adset", "ads"];
      if (statusTabs.includes(currentTab)) {
        return;
      }

      const index = this.tableRecords.findIndex(
        (row) => row == this.statusChangeItem
      );
      this.tableRecords[index].advertisement_status =
        this.tableRecords[index].advertisement_status == "active"
          ? "inactive"
          : "active";
    },

    changeRecordStatus(item) {
      this.status_id = item.id;
      console.log("loading index", item.id);
      this.status_loading = true;
      this.statusChangeItem = item;
      this.$refs.reasonRef.fetchAllReasons(
        null,
        this.statusChangeItem.advertisement_status
      );
      this.showReasonDialog = true;
    },

    getRefreshProgress() {
      this.$echo
        .private(`refresh-ads.${this.$auth.user.id}`)
        .listen("SendRefreshAdsEvent", ({ data }) => {
          this.isRefreshing = true;
          const total = parseInt(data.total);
          const current = parseInt(data.item_index) + 1;
          if (total == current) {
            this.fetchItemsAccordingToTabKey(this.currentTab.key, {
              query: null,
              body: null,
            });
            setTimeout(() => {
              this.isRefreshing = false;
              this.refresh_percentage = 0;
              // this.$toastr.s(this.$tr("successfully_refreshed"));
              this.$toasterNA("green", this.$tr("successfully_refreshed"));
            }, 3000);
          }
          const percentage = (100 / total) * current;
          const twoDecimal = Math.round(percentage);
          if (twoDecimal == 100) {
            this.refresh_percentage = 99;
          } else {
            this.refresh_percentage = twoDecimal;
          }
        });
    },

    onDateRangeSubmit(range) {
      const timeRange = {
        start_date: range.start_date,
        end_date: range.end_date,
      };
      this.filterByDate = timeRange;
      const options = this.fetchOptions();
      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: {
          ...filterData,
        },
      });
    },

    async fetchHeaders() {
      try {
        if (this.ColumnAxiosSource)
          this.ColumnAxiosSource.cancel("Request-Cancelled");
        this.ColumnAxiosSource = this.$axios.CancelToken.source();

        if (this.all_headers[this.selectedIndex].headers.length == 0) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "advertisement_" + this.currentTab.key,
            },
            cancelToken: this.ColumnAxiosSource.token,
          });

          this.all_headers[this.selectedIndex].selected_headers =
            response.data.selected_headers;
          this.all_headers[this.selectedIndex].headers = response.data.headers;
          this.all_headers = { ...this.all_headers };
        }
      } catch (error) { }
    },
    async clickchild(event, item) {
      if (event.ctrlKey) {
        window.open(item, "_blank");
      } else {
        try {
          await navigator.clipboard.writeText(item);
          // this.$toastr.s(this.$tr("successfully_copied"));
          this.$toasterNA("green", this.$tr("successfully_copied"));
        } catch (error) {
          // this.$toastr.e(this.$tr("connot_copy"));
          this.$toasterNA("red", this.$tr("connot_copy"));
        }
      }
    },

    setSelectedTabWithData(items) {
      if (items.length) {
        const key = this.tabItems[this.selectedIndex].key;
        this.selectedTabData[key] = items;
        this.selectedTabData.key = key;
      }
    },

    async toggleRefresh() {
      try {
        this.showRefresh = !this.showRefresh;
        if (this.showRefresh) {
          let { data } = await this.$axios.get(`advertisement/last-refresh`);
          if (data.no_record) {
            this.canRefresh = false;
            return;
          }
          let now = moment().format("DD/MM/YYYY HH:mm:ss");
          let ms = moment(now, "DD/MM/YYYY HH:mm:ss").diff(moment(data.date));
          this.refreshTime = data.date;
          if (ms > 40 * 60000) {
            // 40 minsp;
            this.canRefresh = true;
          } else {
            this.canRefresh = false;
          }
        }
      } catch (e) { }
    },
    getTimeAgo(time) {
      return moment(new Date(`${time} UTC`))
        .local(true)
        .fromNow(true);
    },
    getTimeHour(time) {
      return moment(new Date(`${time} UTC`))
        .local(true)
        .format("LT");
    },
    async refreshAds() {
      try {
        this.isRefreshing = true;
        let url = `advertisement/connection/refresh`;
        await this.$axios.get(url);
        this.refresh_percentage = 1;
      } catch (_) {
        this.isRefreshing = false;
        this.$toasterNA("red", this.$tr("something_went_wrong"));
      }
    },

    getDate(item) {
      console.log("date", item);
    },
    async clickchild(event, item) {
      if (event.ctrlKey) {
        window.open(item, "_blank");
      } else {
        try {
          await navigator.clipboard.writeText(item);
          // this.$toastr.s(this.$tr("successfully_copied"));
          this.$toasterNA("green", this.$tr("successfully_copied"));
        } catch (error) {
          // this.$toastr.e(this.$tr("connot_copy"));
          this.$toasterNA("red", this.$tr("connot_copy"));
        }
      }
    },

    clearAndUnselectId(code) {
      this.currentTab.selectedItems = this.currentTab.selectedItems.filter(
        (row) => row.code !== code
      );
    },

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.removeUser(data.id);
          this.insertUser(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    async searchById(id) {
      this.searchId = id;
      // try {
      const options = this.fetchOptions();
      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: filterData,
        searchValue: true,
        search_by_id: true,
      });
      // this.selectData(response);
      // } catch (error) {
      // 	HandleException.handleApiException(this, error);
      // }
    },

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      const options = this.fetchOptions();

      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: filterData,
      });
    },

    onSearchTypeChange() {
      this.currentTab.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },

    fetchOptions() {
      let data = {
        key: this.currentTab.key,
      };
      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;

      return data;
    },
    getCurrentTabIndex() {
      return this.tabItems.findIndex((tab) => tab.key == this.currentTab.key);
    },
    onTableChanges(options) {
      this.options = this._.clone(options);
      options = this.fetchOptions();
      const currentTabIndex = this.getCurrentTabIndex();
      const filterData = this.filterOptions(currentTabIndex);
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: options,
        body: filterData,
      });
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      const currentTabIndex = this.getCurrentTabIndex();
      const tabData = this.filterOptions(currentTabIndex);
      this.filterData = filterData;
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        body: { ...filterData, ...tabData },
      });
    },
    openFilterDialog() {
      this.$refs.advertismentFilterRefs.changeModel();
    },
    onRowClicked(item) {
      let items = this.currentTab.selectedItems;
      let found = items.findIndex((item2) => item.id == item2.id) > -1;
      if (found) {
        items = items.filter((item2) => item2.id != item.id);
      } else {
        items.push(item);
      }
      this.currentTab.selectedItems = items;
      this.setSelectedTabWithData(this.currentTab.selectedItems);
      const changeItem = (item) => {
        if (item.key == this.currentTab.key) {
          item.selectedItems = Array.from(items);
        }
      };
      this.tabItems.forEach(changeItem);
    },
    async onTabChange(tabIndex) {
      this.selectedIndex = tabIndex;
      const tabItem = this.tabItems[tabIndex];
      this.currentTab = tabItem;
      this.fetchHeaders();
      const filterData = this.filterOptions(tabIndex);
      if (this.$refs.advertisementGraph) {
        const items = Object.values(filterData);
        const filteredItems = items.filter((item) => {
          if (Array.isArray(item)) return item.length > 0;
          return false;
        });
        if (filteredItems.length > 0) {
          this.$refs.advertisementGraph.fetchGraphTabs(filterData);
          this.$refs.advertisementGraph.fetchGraphData(filterData);
        }
      }
      this.fetchItemsAccordingToTabKey(tabItem.key, {
        query: this.options,
        body: filterData,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },

    filterOptions(tabIndex) {
      const prevTabItem = this.currentTab;
      let options = { prev: prevTabItem.key };
      const items = this.tabItems;
      // const items = this.tabItems.slice(0, tabIndex);
      const eachItem = (tab) => {
        const ids = tab.selectedItems.map(({ id }) => id);
        options[tab.key] = ids;
      };
      items.forEach(eachItem);
      options = { ...options, ...this.filterByDate };
      return options;
    },

    async fetchItemsAccordingToTabKey(tabkey, options) {
      try {
        if (!options.searchValue) this.apiCalling = true;
        if (this.axiosSource) this.axiosSource.cancel("Request-Cancelled");
        this.axiosSource = this.$axios.CancelToken.source();
        let url = `advertisement/manual/data/${tabkey}`;
        const header = {
          cancelToken: this.axiosSource.token,
          params: { ...options.query, search_by_id: options.search_by_id },
        };

        this.fetchDataCounts({
          ...options.body,
          searchValue: options?.searchValue,
          ...this.filterData,
        });

        const { data } = await this.$axios.post(
          url,
          {
            ...options.body,
            searchValue: options?.searchValue,
            ...this.filterData,
          },
          header
        );

        if (data.result && data.items) {
          this.setSelectedTabWithData(data.items);
          this.tableRecords = data.items;
          this.totalRecords = data.total;
        } else if (data.result && data.item) {
          this.totalRecords = data.total;
          this.tableRecords = this.tableRecords.filter(
            (item) => item.id != data.item.id
          );
          this.tableRecords.unshift(data.item);
          this.currentTab.selectedItems.push(data.item);
        } else if (!data.item) {
          this.$root.$emit("removeSearchId", this.searchId);
        } else if (data.result == false) {
          // this.$toastr.e(this.$tr(data.code));
          this.$toasterNA("red", this.$tr(data.code));
        }
        this.apiCalling = false;
      } catch ({ message }) {
        if (message == "Request-Cancelled") {
          if (!options.searchValue) this.apiCalling = true;
        } else {
          this.apiCalling = false;
        }
      }
    },

    async fetchDataCounts(passedData) {
      const { data } = await this.$axios.post("advertisement/counts", {
        ...passedData,
      });
      if (data.result) {
        let counts = data.counts;
        this.tabItems = this.tabItems.map((item) => {
          if (item.key in counts) {
            item.count = counts[item.key];
          }
          return item;
        });
        // console.log(data.counts);
      }
    },
    onUnselected(tabIndex) {
      this.currentTab.selectedItems = [];
      let prevTabItem = { key: null, selectedItems: [] };
      if (tabIndex > 0) {
        prevTabItem = this.tabItems[tabIndex - 1];
      } else {
        prevTabItem = this.tabItems[tabIndex];
      }
      const clickedTab = this.tabItems[tabIndex];
      if (clickedTab.key == this.currentTab.key) return;
      const currentTabIndex = this.getCurrentTabIndex();
      const body = this.filterOptions(currentTabIndex);
      if (this.$refs.advertisementGraph) {
        this.$refs.advertisementGraph.fetchGraphTabs(body);
        this.$refs.advertisementGraph.fetchGraphData(body);
      }
      body.prev = prevTabItem.key;
      this.fetchItemsAccordingToTabKey(this.currentTab.key, {
        query: this.options,
        body: body,
      });
      this.tableRecords = [];
      this.totalRecords = 0;
    },

    getTotalBid(item) {
      let total = 0;
      let adsets = Array.isArray(item.adsets)
        ? item.adsets
        : item.campaign_adsets;
      adsets.forEach((adset) => {
        total = total + parseFloat(adset.bid);
      });
      return total;
    },
    getPaymentMethod(item) {
      let method = "";
      if (item.connection !== undefined) {
        switch (item.connection.platform.platform_name) {
          case "facebook":
            method = "Credit Card";
            break;
          case "tiktok":
            method = "Debit Card";
            break;
          default:
            method = "unkown";
            break;
        }
      }
      return method;
    },

    toggleBalanceModal() {
      if (
        this.currentTab.selectedItems.length < 1 ||
        this.currentTab.selectedItems.length > 1
      ) {
        this.$toasterNA("red", this.$tr("please_select_one_option"));
        return false;
      }
      this.balanceData = this.currentTab.selectedItems;

      this.$refs.balanceModal.toggleDialog(this.currentTab.selectedItems[0].id);
    },
    localeHumanReadableTime(date) {
      return moment(date)
        .locale(this.$vuetify.lang.current)
        .format("YYYY-MM-DD h:mm: A");
    },
    toggleHistory(item, tab) {
      this.$refs.historyRefs.openDialog({
        campaign_id: item.id,
        item: item,
        model: this.currentTab.key,
        tab: tab,
        dateRange: this.filterByDate,
      });
    },
    refreshCrm() {
      this.showCrmPopOver = true;
    }
  },
};
</script>

<style>
.link:hover {
  cursor: copy;
}

.link .primary:hover {
  background-color: var(--v-primary-lighten1) !important;
}
</style>
<style scoped>
.custom-card-title-2 {
  font-size: 16px;
  font-weight: 400;
  color: #777;
}

.remarks-count {
  padding: 0px 4px;
  position: absolute;
  top: -5px;
  left: 12px;
  border: 2px solid rgb(255, 255, 255) !important;
  color: rgb(255, 255, 255) !important;
  height: 16px;
  line-height: 16px;
  font-size: 10px;
  border-radius: 5px !important;
  min-width: 20px;
}
</style>

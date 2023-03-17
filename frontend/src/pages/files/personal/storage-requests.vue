<template>
  <v-container fluid class="pa-0">
    <PersonalRequestFilter
      ref="personalRequestFilterRef"
      @filterRecords="onFilterRecords"
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
        :page_name="'storage_requests_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />
      <v-dialog
        v-model="profileDialog"
        persistent
        max-width="600"
        class="rounded-0"
      >
        <RequestStorageProfile
          @closeDialog="profileDialog = false"
          :item="profileData"
          @approved="approved"
          @rejected="rejected"
        />
      </v-dialog>

      <v-col cols="12">
        <PageHeader
          :Icon="`storage_requests`"
          Title="storage_requests"
          :Breadcrumb="breadcrumb"
        />
      </v-col>
      <v-col cols="12">
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="tableValues"
          :downloadDialog="downloadDialog"
          :filename="$tr('countries')"
          :show-add-note="true"
          @onFilter="openFilterDialog"
          :showDownload="$isInScope('stre')"
          note-title="add_country_note"
          @onDownload="downloadDialog = !downloadDialog"
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
          @onColumn="columnDialog = true"
        />
      </v-col>
      <v-col cols="12">
        <CustomPageActions
          ref="customPageAction"
          :selectedItems.sync="selectedItems"
          :tab-key.sync="tabKey"
          :showAutoEdit="false"
          :showTracing="false"
          :showEdit="false"
          :showDelete="false"
          :showBlock="false"
          :showSelect="$isInScope('stru')"
          :showApply="$isInScope('stru')"
          :routeName="'storage-requests'"
          :subSystemName="'Settings'"
          :noReasonSubmitOperations="'approved'"
          :statusItems="statusItems"
          @onView="showProfileDialog()"
          @fetchNewData="fetchNewData"
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
          :key="tableKey"
          v-model="selectedItems"
          :headers="all_headers[0].selected_headers"
          :ItemsLength="extraData.total"
          :items="tableValues"
          :loading="apiCalling"
          @click:row="onRowClicked"
          @onTablePaginate="onTableChanges"
        >
          <template v-slot:[`item.type`]="{ item }">
            <span>
              {{ $tr(item.type) }}
            </span>
          </template>
          <template v-slot:[`item.user`]="{ item }">
            {{ item.user | getUserFullName }}
          </template>
          <template v-slot:[`item.code`]="{ item }">
            <span
              @click="() => showProfileDialog(item.id)"
              class="mx-1 primary--text font-weight-bold"
            >
              {{ item.code }}
            </span>
          </template>
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import HandleException from "~/helpers/handle-exception";
import RequestStorage from "../../../configs/pages/request_storage";
import Alert from "~/helpers/alert";
import ProgressBar from "../../../components/common/ProgressBar";
import PageHeader from "../../../components/design/PageHeader";
import PageFilters from "../../../components/design/PageFilters";
import CustomPageActions from "../../../components/design/CustomPageActions.vue";
import DataTable from "../../../components/design/DataTable";
import TableTabs from "~/components/common/TableTabs.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import PersonalRequestFilter from "../../../components/files/personal/PersonalRequestFilter.vue";
import RequestStorageProfile from "../../../components/files/personal/RequestStorageProfile.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "cnv",
  },
  name: "request_storage",
  components: {
    ProgressBar,
    DataTable,
    CustomPageActions,
    PageFilters,
    PageHeader,
    TableTabs,
    CustomizeColumn,
    PersonalRequestFilter,
    RequestStorageProfile,
  },

  watch: {
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
  },

  data() {
    return {
      tabItems: this.$getTabItems(["all", "approved", "rejected", "pending"]),
      apiCalling: false,
      tableValues: [],

      breadcrumb: RequestStorage(this).breadcrumb,

      extraData: {},
      statusItems: [],
      tabKey: "all",
      tabChipKey: 0,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,

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
      searchContent: "",
      searchId: "",
      filterData: {},
      options: {},
      profileDialog: false,
      profileData: {},
    };
  },

  async created() {
    //customize columns
    await this.fetchHeaders();
  },

  async fetch() {
    await this.getRecord();
  },
  async mounted() {
    this.$echo
      .private(`update.request-storage`)
      .listen("Updated", async (e) => {
        if (e.action == "created") {
          if (this.tabKey == "all" || this.tabKey == "pending") {
            this.tableValues.unshift(e.data);
            this.extraData.total++;
            this.extraData.allTotal++;
            this.extraData.pendingTotal++;
          } else {
            this.extraData.total++;
            this.extraData.allTotal++;
            this.extraData.pendingTotal++;
          }
        }
        if (e.action == "destroy") {
          if (this.tabKey == "all" || this.tabKey == "pending") {
            this.tableValues = this.tableValues.filter(
              (row) => row.id != e.data
            );
            this.extraData.total--;
            this.extraData.allTotal--;
            this.extraData.pendingTotal--;
          } else {
            this.extraData.total--;
            this.extraData.allTotal--;
            this.extraData.pendingTotal--;
          }
        }
      });
  },
  beforeDestroy() {
    this.$echo.leave("update.request-storage");
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "storage_requests_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    openFilterDialog() {
      this.$refs.personalRequestFilterRef.changeModel();
    },

    approved() {
      Alert.confirmAlert(
        this,
        () => {
          this.$refs.customPageAction.onSubmit([], false, "approved");
          this.profileDialog = false;
        },
        "",
        "warning",
        "are_you_sure"
      );
    },
    rejected() {
      this.$refs.customPageAction.fetchAllReasons("rejected");
      this.profileDialog = false;
    },
    setStatusItems(status) {
      switch (status) {
        case "pending":
          this.statusItems = [
            { id: "approved", name: "approved" },
            { id: "rejected", name: "rejected" },
          ];
          break;

        default:
          this.statusItems = [];
          break;
      }
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
    // 	***********************			No changes required		************************
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
          this.tableValues = this.tableValues.filter(
            (item) => item.id !== data.id
          );

          this.tableValues.unshift(data);
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

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    // 	***********************			No changes required		************************

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
          "storage-requests/search",
          options
        ); // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
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
        const response = await this.$axios.get("storage-requests", {
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

    async showProfileDialog(itemId = null) {
      if (this.tabKey == "pending") {
        try {
          this.showProgressBar = true;
          const id = itemId == null ? this.selectedItems[0].id : itemId;
          const response = await this.$axios.get(`storage-requests/${id}`);
          this.profileData = response.data;
          this.showProgressBar = false;
          this.profileDialog = true;
        } catch (error) {}
      } else {
        this.$toasterNA("red", this.$tr("cant_do_operation_on_current_tab"));
      }
    },
  },
};
</script>

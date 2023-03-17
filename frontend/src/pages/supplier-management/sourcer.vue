<template>
  <v-container fluid class="pa-0">
    <client-only>
      <SourcerFilter ref="sourcerFilter" @filterRecords="onFilterRecords" />
      <SourcerStepperVue ref="sourcerInsertionRef" @pushRecord="pushRecord" />
      <updateSourcerStepper
        ref="sourcerUpdateRef"
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
          :page_name="'sourcing_request_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

      <AssignStepper
        ref="assignStepper"
        :selected="this.selectedItems"
        @fetchNewData="getRecord()"
        :title="`Assign Sourcer`"
        :url="`single-sales/sourcing/assign-user`"
      />
      <v-row no-gutters>
        <v-col cols="12">
          <PageHeader
            :Icon="breadcrumb[1].icon"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>
        <v-col cols="12">
          <PageFilters
            ref="pageFilterRef"
            :selected_header.sync="all_headers[0].selected_headers"
            :downloadContent="sourcerRecords"
            :downloadDialog="downloadDialog"
            @onFilter="openFilterDialog"
            :filename="$tr('sourcer')"
            @onDownload="
              () => {
                downloadDialog = !downloadDialog;
              }
            "
            @searchById="searchById"
            @clearAndUnselectId="clearAndUnselectId"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            @onColumn="columnDialog = true"
            note-title=""
            :showBulkUpload="false"
            :showDownload="$isInScope('adve')"
          >
            <CustomButton
              v-if="$isInScope('advc')"
              icon="fa-plus"
              @click="openInsertDialog"
              :text="$tr('create_item', $tr('sourcer'))"
              type="primary"
            />
          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            :showEdit="true"
            :showAutoEdit="false"
            @fetchNewData="getRecord()"
            :showDelete="$isInScope('advd')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :showAssign="$isInScope('adva')"
            :showApprove="$isInScope('advj')"
            :showUnAssign="$isInScope('adva')"
            :showRestore="showRestore"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            :showBlock="false"
            @onAutoEdit="onAutoEdit"
            @onEdit="onEdit"
            @onAssign="onAssign"
            @onUnAssign="onUnAssign"
            :statusItems="statusItems"
            :routeName="'single-sales/sourcing/request'"
            :subSystemName="'Sourcing Request'"
            :noReasonSubmitOperations="'in sourcing, found, in hold, pending'"
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
            :items="sourcerRecords"
            :ItemsLength="getTotals(tabKey)"
            v-model="selectedItems"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :key="tableKey"
          >
            <template v-slot:[`item.code`]="{ item }">
              <span class="mx-1 primary--text font-weight-bold">
                {{ item.code }}
              </span>
            </template>
            <template v-slot:[`item.reason_for_search`]="{ item }">
              <v-tooltip bottom max-width="200">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex">
                      {{ item.reason_for_search.substring(0, 30) + " ..." }}
                    </span>
                  </span>
                </template>
                {{ item.reason_for_search }}
              </v-tooltip>
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
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import DesignRequestOperation from "~/components/landing/DesignRequestOperation";
import Sourcer from "~/configs/pages/single_sales/sourcer";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";

import DesignRequestFilter from "~/components/landing/DesignRequestFilter.vue";
import Profile from "~/components/landing/profile.vue";
import LandingNoteView from "~/components/landing/Orders/LandingNoteView.vue";
import RejectionReview from "~/components/landing/Orders/RejectionReview.vue";
import DesignRequestChart from "~/components/landing/DesignRequestChart.vue";
import DesignRequestAutoReview from "~/components/landing/DesignRequestAutoReview.vue";
import HandleException from "~/helpers/handle-exception";
import SourcerFilter from "~/components/single_sales_management_system/SourcerFilter.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import SourcerStepperVue from "../../components/single_sales_management_system/sourcer_insertion/SourcerStepper.vue";
import updateSourcerStepper from "../../components/single_sales_management_system/sourcer_insertion/updateSourcerStepper.vue";
import AssignStepper from "../../components/assign/assigedStepper/AssignStepper.vue";
import Alert from "../../helpers/alert";

export default {
  meta: {
    hasAuth: true,
    scope: "advv",
  },

  components: {
    CustomPageActions,
    CustomButton,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    Dialog,
    DataTable,
    CustomizeColumn,
    DesignRequestOperation,
    DesignRequestFilter,
    Profile,
    LandingNoteView,
    RejectionReview,
    DesignRequestChart,
    DesignRequestAutoReview,
    SourcerFilter,
    TableTabs,
    SourcerStepperVue,
    updateSourcerStepper,
    AssignStepper,
  },

  data() {
    return {
      tabKey: "all",
      showChart: false,
      sourcerRecords: [],
      tabItems: Sourcer(this).tabItems,
      breadcrumb: Sourcer(this).breadcrumb,
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},
      extraData: {},
      showRestore: false,
      downloadDialog: false,
      statusItems: [],
      assignUser: false,
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

      autoEdit: false,
      editKey: 0,
      autoEditData: [],
      selectedItems: [],
      content: [],
      contentData: "",
      profileData: {},

      isEdit: false,
      isAutoEdit: false,
      editData: {},

      searchId: "",
      searchContent: "",
      filterData: {},
    };
  },

  watch: {
    tabKey: function (value) {
      this.checkSelectedTab(value);
    },
  },

  async created() {
    await this.fetchHeaders();
    this.getRecord();
    this.$root.$on("closeEdit", this.toggleEdit);
    //customize columns
  },

  async fetch() {
    await this.getRecord();
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "sourcing_request_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      this.selectedItems = [];
      this.checkSelectedTab(this.tabIndex);
      const data = this.fetchOptions();
      const response = await this.$axios.get(`single-sales/sourcing/request`, {
        params: data,
      });
      if (response?.status === 200) {
        this.productStudyRecords = response.data?.data;
        this.extraData = response?.data;
      }
      this.apiCalling = false;
    },
    getTabKey() {
      /* let tabKey;
      this.tabItems.forEach((a) => {
        if (this.tabKey == a.key)
          tabKey = a.key;
      })*/
      // console.log(tabKey)
      return this.tabKey;
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
    onAssign() {
      this.$refs.assignStepper.showAssignDialog();
    },
    async onUnAssign() {
      if (this.selectedItems.length) {
        let orderIds = this.selectedItems.map((item) => item.id);
        try {
          await this.$axios.post(
            "single-sales/sourcing/assign-user?type=unassign",
            {
              orderIds: orderIds,
            }
          );
          this.getRecord();
        } catch (error) {
          this.$toasterNA("red", this.$tr('something_went_wrong'));
        }
      }
    },
    openInsertDialog() {
      this.$refs.sourcerInsertionRef.showDialog();
    },
    openFilterDialog() {
      this.$refs.sourcerFilter.changeModel();
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
    },
    async pushRecord(data) {
      if (this.tabKey === "all" || this.tabKey === "pendding")
        await this.sourcerRecords.unshift(data);
      this.extraData.allTotal += 1;
      this.extraData.pendingTotal += 1;
      this.extraData.total += 1;
    },
    async updateRecord(data) {
      this.sourcerRecords = this.sourcerRecords.map((item) => {
        if (item?.id == data?.id) return data;
        else return item;
      });
    },

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

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter((row) =>
        !isNaN(code) ? row.code !== "LDR000" + code : row.code !== code
      );
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
          "single-sales/sourcing/search",
          options
        );
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.sourcerRecords = this.sourcerRecords.filter(
            (item) => item.id !== data.id
          );
          this.sourcerRecords?.unshift(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    toggleEdit() {
      this.editKey++;
      this.isEdit = !this.isEdit;
    },

    async onEdit() {
      if (this.selectedItems.length == 1) {
        const map = (item) => {
          return {
            id: item.id,
            approx_quantity: item.approx_quantity,
            approx_quantity: item.approx_quantity,
            importing_country: item.importing_country,
            importing_country_id: item.importing_country_id,
            receiving_country_id: item.receiving_country_id,
            sourcing_type: item.sourcing_type,
            target_cost: item.target_cost,
            reason_for_search: item.reason_for_search,
            required_shipping_method: item.required_shipping_method,
          };
        };
        const parsedItems = this.selectedItems.map(map);
        console.log(parsedItems, "my item");
        this.$refs.sourcerUpdateRef.showDialog(parsedItems);
        this.editMode = true;
      } else {
        Alert.customAlert(this, "Please select one item", "restricted");
      }
    },

    async onAutoEdit() {
      return;
      if (this.selectedItems.length > 0) {
        this.editData = this.selectedItems;
        this.isEdit = true;
        this.isAutoEdit = true;
        this.editKey++;
      }
    },

    // add or remove item from selected items
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    getTotals(tabKey) {
      switch (tabKey) {
        case "pending":
          return this.extraData?.pendingTotal || 0;
        case "in hold":
          return this.extraData?.inHoldTotal || 0;
        case "not found":
          return this.extraData?.notFoundTotal || 0;
        case "found":
          return this.extraData?.foundTotal || 0;
        case "all":
          return this.extraData?.allTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
        case "cancelled":
          return this.extraData?.cancelledTotal || 0;
        // case "rejected":
        //   return this.extraData?.rejectedTotal || 0;
        case "in sourcing":
          return this.extraData?.inSourcingTotal || 0;
        case "assigned":
          return this.extraData?.assignedTotal || 0;
        case "not assigned":
          return this.extraData?.notAssignedTotal || 0;
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
        const response = await this.$axios.get(
          "single-sales/sourcing/request",
          {
            params: data,
          }
        );
        if (response.status === 200) {
          this.sourcerRecords = response.data?.data;
          this.extraData = response?.data;
        }
        this.apiCalling = false;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    setStatusItems(status) {
      switch (status) {
        case "pending":
          this.statusItems = [
            { id: "in sourcing", name: "in_sourcing" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "cancelled":
          this.statusItems = [
            { id: "restore", name: "restore" },
            { id: "delete", name: "delete" },
          ];
          break;
        // case "rejected":
        //   this.statusItems = [
        //     { id: "restore", name: "restore" },
        //     { id: "delete", name: "delete" },
        //   ];
        //   break;
        case "in sourcing":
          this.statusItems = [
            { id: "in hold", name: "in_hold" },
            { id: "found", name: "found" },
            { id: "not found", name: "not_found" },
          ];
          break;
        case "in hold":
          this.statusItems = [
            { id: "in sourcing", name: "in_sourcing" },
            { id: "cancelled", name: "cancelled" },
            { id: "rejected", name: "rejected" },
          ];
          break;
        case "found":
          this.statusItems = [
            { id: "in sourcing", name: "in_sourcing" },
            { id: "cancelled", name: "cancelled" },
            { id: "rejected", name: "rejected" },
          ];
          break;
        case "removed":
          this.statusItems = [{ id: "restore" }];
          this.showRestore = true;
          break;

        default:
          this.statusItems = [];
          break;
      }
    },
  },
};
</script>

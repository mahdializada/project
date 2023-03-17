<template>
  <v-container fluid class="pa-0">
    <client-only>
      <QuantityReservationInsertion
        ref="quantityInsertionRef"
        @pushRecord="pushRecord"
      />
      <StepperComponent
        ref="quantityInsertionRefStepper"
        @pushRecord="pushRecord"
      />
      <updateStepperComponent
        ref="quantityUpdateRefStepper"
        @updateRecord="updateRecord"
      />

      <QuantityReservationFilter
        ref="quantityReservationFilter"
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
          :page_name="'quantity_reservation_request_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

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
            :downloadContent="quantityReservationRecords"
            :downloadDialog="downloadDialog"
            @onFilter="openFilterDialog"
            :filename="'quantity_reservation'"
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
              :text="$tr('create_item', $tr('quantity_servertion'))"
              type="primary"
            />
          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            :showEdit="true"
            :showAutoEdit="false"
            :showDelete="$isInScope('advd')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabItems[tabIndex].key"
            :showBlock="false"
            @fetchNewData="fetchNewData"
            :showRestore="showRestore"
            @onAutoEdit="onAutoEdit"
            @onEdit="onEdit"
            :statusItems="statusItems"
            :routeName="'single-sales/quantity/reservation'"
            :subSystemName="'Quantity Reservation Request'"
            :noReasonSubmitOperations="'order made, order received'"
            :noReasonSubmitTabs="'removed, deleted'"
            
          />
        </v-col>
        <v-col cols="12">
          <v-row class="mx-0">
            <v-col cols="12 pa-0">
              <v-tabs
                v-model="tabIndex"
                height="40"
                background-color="primary"
                active-class="active-background"
                show-arrows
                dark
                center-active
              >
                <client-only>
                  <v-tab v-for="(item, i) in tabItems" :key="i" class="px-3">
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
                    <v-chip
                      :key="tabChipKey"
                      class="ms-1 tab-chip"
                      :color="item.isSelected ? 'primary' : 'white'"
                      :text-color="item.isSelected ? 'white' : 'primary'"
                      small
                      v-text="getTotals(item.key)"
                    />
                  </v-tab>
                </client-only>
              </v-tabs>
            </v-col>
          </v-row>
          <DataTable
            ref="projectTableRef"
            :headers="all_headers[0].selected_headers"
            :items="quantityReservationRecords"
            :ItemsLength="getTotals(tabItems[tabIndex].key)"
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
            <template v-slot:[`item.order_note`]="{ item }">
              <v-tooltip bottom max-width="200">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex">
                      {{ item.products_note?.substring(0, 30) + " ..." }}
                    </span>
                  </span>
                </template>
                {{ item.products_note }}
              </v-tooltip>
            </template>
            <template v-slot:[`item.products`]="{ item }">
              <v-tooltip bottom max-width="200">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex">
                      {{ $tr('products')}}
                    </span>
                  </span>
                </template>
                <span v-for="(product,i) in item.products" :key="i">
                  {{ product.name }} <br>
                </span>
              </v-tooltip>
            </template>
            <template v-slot:[`item.quantity`]="{ item }">
                  <div>
                    {{ item.products[0]?.pivot?.quantity }}
                  </div>
            </template>
            <template v-slot:[`item.arrival_time`]="{ item }">
                  <div>
                    {{ item.products[0]?.info?.arrival_time??'no content' }}
                  </div>
            </template>
            <template v-slot:[`item.purchase_order_number`]="{ item }">
                  <div>
                    {{ item.products[0]?.info?.order_no??'no content' }}
                  </div>
            </template>
            <template v-slot:[`item.receiving_country`]="{ item }">
                  <div>
                    {{ item.products[0]?.receiving_country?.name??'no content' }}
                  </div>
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
import QuantityReservation from "~/configs/pages/single_sales/quantity-reservation";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";

import DesignRequestFilter from "~/components/landing/DesignRequestFilter.vue";
import Profile from "~/components/landing/profile.vue";
import LandingNoteView from "~/components/landing/Orders/LandingNoteView.vue";
import RejectionReview from "~/components/landing/Orders/RejectionReview.vue";
import DesignRequestChart from "~/components/landing/DesignRequestChart.vue";
import DesignRequestAutoReview from "~/components/landing/DesignRequestAutoReview.vue";
import HandleException from "~/helpers/handle-exception";
import QuantityReservationFilter from "../../components/single_sales_management_system/QuantityReservationFilter.vue";
import QuantityReservationInsertion from "../../components/single_sales_management_system/QuantityReservationInsertion.vue";
import StepperComponent from "../../components/single_sales_management_system/quantity_reservation_insertion/StepperComponent";
import updateStepperComponent from "../../components/single_sales_management_system/quantity_reservation_insertion/updateStepperComponent";
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
    QuantityReservationFilter,
    QuantityReservationInsertion,
    StepperComponent,
    updateStepperComponent,
  },

  data() {
    return {
      customKey: 0,
      reviewDialog: false,
      reviewDialogKey: 0,
      showChart: false,
      tabChipKey: 0,
      landing_note: "",
      note_title: "",
      showNoteDialog: false,
      quantityReservationRecords: [],

      tabItems: QuantityReservation(this).tabItems,
      breadcrumb: QuantityReservation(this).breadcrumb,
      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},

      downloadDialog: false,
      statusItems: [],
      extraData: {},

      // profile section
      showProfile: false,
      showRestore: false,

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
      content: [],
      contentData: "",
      filterData: {},
      profileData: {},

      isEdit: false,
      isAutoEdit: false,
      editData: {},
    };
  },

  watch: {
    tabIndex: function (index) {
      this.fetchDataAccordingtoStatus();
    },
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
  },

  async created() {
    await this.fetchHeaders();

    this.fetchDataAccordingtoStatus();
    this.$root.$on("closeEdit", this.toggleEdit);
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "quantity_reservation_request_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    openInsertDialog() {
      // this.$refs.quantityInsertionRef.toggle();
      // this.$refs.quantityInsertionRefStepper.showDialog();
      this.$refs.quantityInsertionRefStepper.showDialog();
    },

    openFilterDialog() {
      this.$refs.quantityReservationFilter.changeModel();
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.fetchDataAccordingtoStatus();
    },

    async pushRecord(data) {
      if (this.getTabKey() === "all" || this.getTabKey() === "pendding")
        await this.quantityReservationRecords.unshift(data);
      this.extraData.allTotal += 1;
      this.extraData.total += 1;
      this.extraData.pendingTotal += 1;
    },
    async updateRecord(data) {
      this.quantityReservationRecords = this.quantityReservationRecords.map(
        (item) => {
          if (item?.id == data?.id) return data;
          else return item;
        }
      );
    },
    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      this.selectedItems = [];
      this.checkSelectedTab(this.tabIndex);
      const data = this.fetchOptions();
      const response = await this.$axios.get(
        `single-sales/quantity/reservation`,
        {
          params: data,
        }
      );
      console.log("response:",response);
      if (response?.status === 200) {
        this.quantityReservationRecords = response.data?.data;
        this.extraData = response?.data;
      }
      this.apiCalling = false;
    },

    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.fetchDataAccordingtoStatus();
      } else this.options = this._.clone(options);
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

    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },

    checkSelectedTab(value) {
      this.selectedItems = [];
      let currentTab = "";
      this.tabItems = this.tabItems.map((item, index) => {
        index === value
          ? ((item.isSelected = 1), (currentTab = item.key))
          : (item.isSelected = 0);
        return item;
      });

      this.setStatusItems(currentTab);
    },

    getTabKey() {
      return this.tabItems[this.tabIndex].key;
    },

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.get(
          "single-sales/quantity/reservation/search",
          {
            params: options,
          }
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
          this.quantityReservationRecords =
            this.quantityReservationRecords.filter(
              (item) => item.id !== data.id
            );
          this.quantityReservationRecords?.unshift(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
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

    fetchNewData() {
      this.selectedItems = [];
      this.fetchDataAccordingtoStatus();
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
            quantity: item.quantity,
            required_shipping_method: item.required_shipping_method,
            receiving_country_id: item.receiving_country_id,
            purchase_order_number: item.purchase_order_number,
            arrival_time: item.arrival_time,
            order_note: item.order_note,
          };
        };
        const parsedItems = this.selectedItems.map(map);
        this.$refs.quantityUpdateRefStepper.showDialog(parsedItems);
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
        case "in process":
          return this.extraData?.inProcessTotal || 0;
        case "not possible":
          return this.extraData?.notPossibleTotal || 0;
        case "order made":
          return this.extraData?.orderMadeTotal || 0;
        case "all":
          return this.extraData?.allTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
        case "cancelled":
          return this.extraData?.cancelledTotal || 0;
        case "rejected":
          return this.extraData?.rejectedTotal || 0;
        case "order received":
          return this.extraData?.orderReceivedTotal || 0;
      }
    },
    setStatusItems(status) {
      this.showRestore = false;
      switch (status) {
        case "pending":
          this.statusItems = [
            { id: "rejected", name: "rejected" },
            { id: "in process", name: "in_process" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;

        case "in process":
          this.statusItems = [
            { id: "not possible", name: "not_possible" },
            { id: "order made", name: "order_made" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;

        case "order made":
          this.statusItems = [
            { id: "order received", name: "order_received" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "not possible":
          this.statusItems = [
            { id: "order received", name: "order_received" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "removed":
          this.statusItems = [];
          this.showRestore = true;
          break;
        default:
          this.statusItems = [];
      }
    },
  },
};
</script>

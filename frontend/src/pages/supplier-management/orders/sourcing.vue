<template>
  <v-container fluid class="pa-0">
    <client-only>
      <SourcerFilter ref="sourcerFilter" @filterRecords="onFilterRecords" />

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
            :filename="'sourcer'"
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
          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            :showEdit="false"
            :showAutoEdit="false"
            :showDelete="$isInScope('advd')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabItems[tabIndex].key"
            :showBlock="false"
            :showRestore="false"
            @onAutoEdit="onAutoEdit"
            @onEdit="onEdit"
            :statusItems="statusItems"
            :routeName="'design-request'"
            :subSystemName="'Design Request'"
            :noReasonSubmitOperations="''"
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
            :items="sourcerRecords"
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
      sourcerRecords: [],

      tabItems: Sourcer(this).tabItemsOrder,
      breadcrumb: Sourcer(this).breadcrumb,
      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},

      downloadDialog: false,
      statusItems: [],

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
      filterData: {},
      profileData: {},

      note_title: "",
      isEdit: false,
      isAutoEdit: false,
      editData: {},
    };
  },

  watch: {
    tabIndex: function () {
      this.fetchDataAccordingtoStatus();
    },
  },

  async created() {
    await this.fetchHeaders();
    this.fetchDataAccordingtoStatus();
    this.$root.$on("closeEdit", this.toggleEdit);
  },

  computed: {},

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

    openFilterDialog() {
      this.$refs.sourcerFilter.changeModel();
    },

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.fetchDataAccordingtoStatus();
    },

    async fetchDataAccordingtoStatus() {
      this.apiCalling = true;
      this.selectedItems = [];
      this.checkSelectedTab(this.tabIndex);
      const data = this.fetchOptions();
      const response = await this.$axios.get(
        `single-sales/sourcer?order=true`,
        {
          params: data,
        }
      );
      if (response?.status === 200) {
        this.sourcerRecords = response.data?.data;
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
      this.selectedItems = this.selectedItems.filter((row) =>
        !isNaN(code) ? row.code !== "LDR000" + code : row.code !== code
      );
    },

    checkSelectedTab(value) {
      this.selectedItems = [];
      let currentTab = "";
      this.tabItems = this.tabItems.map((item, index) => {
        index === value
          ? ((item.isSelected = 1), (currentTab = item))
          : (item.isSelected = 0);
        return item;
      });
    },

    getTabKey() {
      return this.tabItems[this.tabIndex].key;
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
          "single-sales/sourcer/search",
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
      return;
      if (this.selectedItems.length > 0) {
        this.editData = this.selectedItems;
        this.isEdit = true;
        this.editKey++;
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
        case "rejected":
          return this.extraData?.rejectedTotal || 0;
        case "in sourcing":
          return this.extraData?.inSourcingTotal || 0;
        case "assigned":
          return this.extraData?.assignedTotal || 0;
        case "not assigned":
          return this.extraData?.notAssignedTotal || 0;
      }
    },
  },
};
</script>

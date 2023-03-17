<template>
  <v-container fluid class="pa-0">
    <client-only>
      <ProductStudyStepper ref="productStudyStepper" @pushRecord="pushRecord" />
      <ProductStudyUpdateStepper
        ref="productStudyUpdateStepper"
        @updateRecord="updateRecord"
      />
      <ProductStudyFilter
        ref="productStudyFilter"
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
          :page_name="'ProductStudy_all'"
          @closeColumnDialog="columnDialog = false"
        />
      </v-dialog>

      <AssignStepper
        ref="assignStepper"
        :selected="this.selectedItems"
        @fetchNewData="fetchDataAccordingtoStatus()"
        :title="`Assign Product Study`"
        :url="`product-management/pr-product-study/assign-user`"
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
            :downloadContent="productStudyRecords"
            :downloadDialog="downloadDialog"
            @onFilter="openFilterDialog"
            filename="product_study"
            @onDownload="
              () => {
                downloadDialog = !downloadDialog;
              }
            "
            @searchById="searchById"
            @clearAndUnselectId="clearAndUnselectId"
            @searchByContent="searchByContent"
            @onTypeChange="onSearchTypeChange"
            @onColumn="
              dialogKey++;
              columnDialog = true;
            "
            note-title=""
            :showBulkUpload="false"
            :showDownload="$isInScope('adve')"
          >
            <CustomButton
              v-if="$isInScope('advc')"
              icon="fa-plus"
              @click="openInsertDialog"
              :text="$tr('create_item', $tr('product_study'))"
              type="primary"
            />
          </PageFilters>
        </v-col>

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            :showEdit="true"
            :showAutoEdit="false"
            @fetchNewData="fetchDataAccordingtoStatus()"
            :showDelete="$isInScope('advd')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :showAssign="$isInScope('advu')"
            :showUnAssign="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabItems[tabIndex].key"
            :showBlock="false"
            :showRestore="showRestore"
            @onAutoEdit="onAutoEdit"
            @onEdit="onEdit"
            @onAssign="onAssign"
            @onUnAssign="onUnAssign"
            :statusItems="statusItems"
            :routeName="'product-management/pr-product-study'"
            :subSystemName="'ProductStudy'"
            :noReasonSubmitOperations="'active'"
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
            :items="productStudyRecords"
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
            <template v-slot:[`item.study_reason`]="{ item }">
              <v-tooltip bottom max-width="200">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex">
                      {{ item.study_reason.substring(0, 30) + " ..." }}
                    </span>
                  </span>
                </template>
                {{ item.study_reason }}
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
import ProductStudy from "~/configs/pages/single_sales/product-study";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";

import DesignRequestFilter from "~/components/landing/DesignRequestFilter.vue";
import Profile from "~/components/landing/profile.vue";
import LandingNoteView from "~/components/landing/Orders/LandingNoteView.vue";
import RejectionReview from "~/components/landing/Orders/RejectionReview.vue";
import DesignRequestChart from "~/components/landing/DesignRequestChart.vue";
import DesignRequestAutoReview from "~/components/landing/DesignRequestAutoReview.vue";
import HandleException from "~/helpers/handle-exception";
import ProductStudyFilter from "~/components/single_sales_management_system/ProductStudyFilter.vue";
import ProductStudyStepper from "../../components/single_sales_management_system/product_study/ProductStudyStepper.vue";
import ProductStudyUpdateStepper from "../../components/single_sales_management_system/product_study/ProductStudyUpdateStepper.vue";
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
    ProductStudyFilter,
    ProductStudyStepper,
    ProductStudyUpdateStepper,
    AssignStepper,
  },

  data() {
    return {
      customKey: 0,
      reviewDialog: false,
      reviewDialogKey: 0,
      showChart: false,
      tabChipKey: 0,
      landing_note: "",
      showNoteDialog: false,
      productStudyRecords: [],

      tabItems: ProductStudy(this).tabItems,
      breadcrumb: ProductStudy(this).breadcrumb,
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},

      downloadDialog: false,
      showRestore: false,
      statusItems: [],
      extraData: {},

      // profile section
      showProfile: false,
      assignUser: false,

      // for custom columns

      apiCalling: false,

      selected_headers: [],
      columnDialog: false,
      all_headers: [
        {
          key: "all",
          selected_headers: [],
          headers: [],
        },
      ],

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
    this.checkSelectedTab(this.tabIndex);
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "ProductStudy_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    openInsertDialog() {
      this.$refs.productStudyStepper.showDialog();
    },

    openFilterDialog() {
      this.$refs.productStudyFilter.changeModel();
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
      const response = await this.$axios.get(`product-management/pr-product-study`, {
        params: data,
      });
      if (response?.status === 200) {
        this.productStudyRecords = response.data?.data;
        this.extraData = response?.data;
      }
      this.apiCalling = false;
    },
    onAssign() {
      this.$refs.assignStepper.showAssignDialog();
    },
    async onUnAssign() {
      if (this.selectedItems.length) {
        let orderIds = this.selectedItems.map((item) => item.id);
        try {
          await this.$axios.post(
            "product-management/pr-product-study/assign-user?type=unassign",
            {
              orderIds: orderIds,
            }
          );
          this.fetchDataAccordingtoStatus();
        } catch (error) {
          this.$toasterNA("red", this.$tr('something_went_wrong'));
        }
      }
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
          "product-management/pr-product-study/search",
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
          this.productStudyRecords = this.productStudyRecords.filter(
            (item) => item.id !== data.id
          );
          this.productStudyRecords?.unshift(data);
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

    toggleEdit() {
      this.editKey++;
      this.isEdit = !this.isEdit;
    },

    async onEdit() {
      if (this.selectedItems.length == 1) {
        const map = (item) => {
          return {
            id: item.id,
            name: item.name,
            study_language_id: item.study_language_id,
            study_reason: item.study_reason,
          };
        };
        const parsedItems = this.selectedItems.map(map);
        this.$refs.productStudyUpdateStepper.showDialog(parsedItems);
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
        case "active":
          return this.extraData?.activeTotal || 0;
        case "all":
          return this.extraData?.allTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
        case "inactive":
          return this.extraData?.inactiveTotal || 0;
        case "assigned":
          return this.extraData?.assignedTotal || 0;
        case "not assigned":
          return this.extraData?.notAssignedTotal || 0;
      }
    },

    async pushRecord(data) {
      if (this.getTabKey() === "all" || this.getTabKey() === "inactive")
        await this.productStudyRecords.unshift(data);
      this.extraData.allTotal += 1;
      this.extraData.inactiveTotal += 1;
      this.extraData.total += 1;
    },
    async updateRecord(data) {
      this.productStudyRecords = this.productStudyRecords.map((item) => {
        if (item?.id == data?.id) return data;
        else return item;
      });
    },

    setStatusItems(status) {
      this.showRestore = false;
      switch (status) {
        case "inactive":
          this.statusItems = [{ id: "active", name: "active" }];
          break;

        case "active":
          this.statusItems = [{ id: "inactive", name: "inactive" }];
          break;
        case "removed":
          this.statusItems = [];
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

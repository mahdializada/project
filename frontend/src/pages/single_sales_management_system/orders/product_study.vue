<template>
  <v-container fluid class="pa-0">
    <client-only>
      <ProductStudyFilter
        ref="productStudyFilter"
        @filterRecords="onFilterRecords"
        :order="true"
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

      <v-row no-gutters>
        <v-col cols="12">
          <PageHeader
            :Icon="`product_study`"
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
            :noReasonSubmitOperations="'in storyboard, storyboard review, in design, design review, in programming, completed'"
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
            <template v-slot:[`item.language`]="{ item }">
                {{ item.language.name }}
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
import { mapActions, mapGetters, mapState } from "vuex";
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
import ColumnHelper from "~/helpers/column-helper";
import DesignRequestFilter from "~/components/landing/DesignRequestFilter.vue";
import Profile from "~/components/landing/profile.vue";
import LandingNoteView from "~/components/landing/Orders/LandingNoteView.vue";
import RejectionReview from "~/components/landing/Orders/RejectionReview.vue";
import DesignRequestChart from "~/components/landing/DesignRequestChart.vue";
import DesignRequestAutoReview from "~/components/landing/DesignRequestAutoReview.vue";
import HandleException from "~/helpers/handle-exception";
import ProductStudyFilter from "~/components/single_sales_management_system/ProductStudyFilter.vue";
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
      productStudyRecords: [],

      tabItems: ProductStudy(this).tabItemsOrder,
      breadcrumb: ProductStudy(this).breadcrumb,
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
    //customize columns
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
      const response = await this.$axios.get(
        `single-sales/product-study/?order=true`,
        {
          params: data,
        }
      );
      console.log("response:",response);
      if (response?.status === 200) {
        this.productStudyRecords = response.data?.data;
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
        const options = this.fetchOptions();
        const response = await this.$axios.get(
          "single-sales/product-study/search?order=true",
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
        case "active":
          return this.extraData?.activeTotal || 0;
        case "all":
          return this.extraData?.allTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
        case "inactive":
          return this.extraData?.inactiveTotal || 0;
      }
    },
  },
};
</script>

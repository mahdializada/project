<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <SourcerFilter  ref="SourcerFilterRef" @filterRecords="onfilterRecords"/>
    <SourcersStepper
      ref="SourcersStepperRef"
      @pushRecord="pushRecord"
      @updateData="updateData"
    />
    <SourcersProfileView ref="SourcerProfileViewRef" />
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
        :page_name="'sourcers_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <client-only>
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`sourcer`"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>

        <!--start  page filter  -->
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="tableValues"
          :downloadDialog="downloadDialog"
          :filename="$tr('sourcers')"
          :show-add-note="true"
          @onFilter="openFilterDialog"
          :showDownload="$isInScope('adve')"
          note-title="add_project_note"
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
        >
          <CustomButton
            v-if="$isInScope('advc')"
            icon="fa-plus"
            @click="openInsertDialog"
            :text="$tr('create_item', $tr('sourcers'))"
            type="primary"
          />
        </PageFilters>
        <!--end  page filter  -->

        <v-col cols="12">
          <PageActions
            :showView="$isInScope('advv')"
            :showEdit="$isInScope('advu')"
            :showAutoEdit="false"
            :showDelete="$isInScope('advd')"
            :showBlock="false"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            :statusItems="statusItems"
            :showRestore="showRestore"
            @onView="toggleViewProfile"
            @onEdit="toggleEditSourcers"
            :routeName="'sourcers'"
            :subSystemName="'sourcers'"
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
            ref="projectTableRef"
            :headers="all_headers[0].selected_headers"
            :items="tableValues"
            v-model="selectedItems"
            :ItemsLength="extraData.total"
            :loading="apiCalling"
            @onTablePaginate="onTableChanges"
            @click:row="onRowClicked"
            :item-class="completedStyle"
            :key="tableKey"
          >
          <template v-slot:[`item.profile`]="{ item }">
            <v-menu
              offset-y
              open-on-hover
              :close-on-content-click="false"
              open-delay="500"
            >
              <template v-slot:activator="{ on, attrs }">
                <span
                  style="white-space: nowrap"
                  v-bind="attrs"
                  v-on="on"
                  class="mb-1"
                >
                  <v-img
                    class="rounded-circle"
                    width="30"
                    height="30"
                    lazy-src="https://picsum.photos/id/11/10/6"
                    :src="item.attachments[0]?.path"
                  ></v-img>
                </span>
              </template>
              <span>
                <v-img
                  width="400"
                  lazy-src="https://picsum.photos/id/11/10/6"
                  :src="item.attachments[0]?.path"
                ></v-img>
              </span>
            </v-menu>
          </template>
          </DataTable>
        </v-col>
        <progress-bar v-if="isFetchCancels" />
      </v-row>
    </client-only>
  </v-container>
</template>

<script>
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import sourcers from "~/configs/pages/sourcers";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import ProductFilter from "~/components/single_sales_management_system/productFilter.vue";
import SourcersStepper from "~/components/supplier/sourcers/SourcersStepper.vue";
import SourcersProfileView from "../../components/supplier/sourcers/sourcers-profile/SourcersProfileView.vue";
import SourcerFilter from "../../components/supplier/sourcers/sourcerFilter/SourcerFilter.vue";

export default {
  components: {
    PageActions,
    CustomButton,
    PageHeader,
    PageFilters,
    DataTable,
    ProgressBar,
    TableTabs,
    ProductFilter,
    CustomizeColumn,
    SourcersStepper,
    SourcersProfileView,
    SourcerFilter
},
  data() {
    return {
      tabKey: "all",
      showRestore: false,
      tableValues: [],

      tabItems: sourcers(this).tabItems,
      breadcrumb: sourcers(this).breadcrumb,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,
      options: {},
      extraData: {},
      //register section
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
      isFetchCancels: false,
      showProgressBar: false,
      // download dialog
      downloadDialog: false,
      // search
      searchId: "",
      searchContent: "",
      filterData: {},
    };
  },
  async created() {
    this.$root.$on("closeEdit", this.toggleEdit);
    //customize columns
    await this.fetchHeaders();
  },
  watch: {
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
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
              tab_name: "sourcers_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },
    toggleEditSourcers() {
      if (this.selectedItems.length == 1)
        this.$refs.SourcersStepperRef.toggleEdit(this.selectedItems[0]);
    },
    toggleViewProfile() {
      this.$refs.SourcerProfileViewRef.showViewDialog(this.selectedItems[0]);
    },
    openInsertDialog() {
      this.$refs.SourcersStepperRef.showSourcersDialog();
      console.log("hashmat");
    },
    getTotal(tabKey) {
      switch (tabKey) {
        case "active":
          return this.extraData?.activeTotal || 0;
        case "inactive":
          return this.extraData?.inactiveTotal || 0;
        case "removed":
          return this.extraData?.removedTotal || 0;
        default:
          return this.extraData?.allTotal || 0;
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
        const response = await this.$axios.get(`sourcers`,{ 
          params: data,
        });
        if (response.status === 200) {
          this.tableValues = response.data?.data;
          this.extraData = response?.data;
        }
        this.apiCalling = false;
      } catch (error) {
        console.log(error);
        HandleException.handleApiException(this, error);
      }
    },
    async pushRecord(data) {

      if (this.tabKey === "all" || this.tabKey === "inactive") {
        this.tableValues.unshift(data);
        this.extraData.allTotal += 1;
        this.extraData.inactiveTotal += 1;
        this.extraData.total += 1;
      }
    },
    updateData(updateData) {
      console.log(updateData);
      this.tableValues = this.tableValues.map((i) => {
        if (i.id == updateData.id) {
          return updateData;
        }
        return i;
      });
    },
    setStatusItems(status) {
      this.showRestore = false;
      switch (status) {
        case "active":
          this.statusItems = [{ id: "inactive", name: "inactive" }];
          break;
        case "inactive":
          this.statusItems = [{ id: "active", name: "active" }];
          break;
        case "removed":
          this.statusItems = [
            { id: "active", name: "active" },
            { id: "inactive", name: "inactive" },
          ];
          this.showRestore = true;
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
    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
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
    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },
    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.getRecord();
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
          "sourcers/search",
          options
        ); // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    onfilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
    },
    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },
    openFilterDialog() {
      this.$refs.SourcerFilterRef.changeModel();
    },
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },
    completedStyle(item) {
      return item.status == "completed" ? "completedAnimation" : "";
    },
  },
};
</script>

<style></style>

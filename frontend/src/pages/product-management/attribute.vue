<template>
  <v-container fluid class="pa-0">
  <TestStepper ref="actionInsertRef"  @pushRecord="pushRecord" />
  
    <v-dialog v-model="columnDialog" persistent width="1300" class="rounded-0">
    <CustomizeColumn
      @closeDialog="columnDialog = false"
      @saveChanges="saveColumnSetting($event)"
      :tableHeader="$_.cloneDeep(headers)"
      :custom_columns="$_.cloneDeep(selected_column)"
      :categoryHeader="$_.cloneDeep(category)"
      :key="dialogKey"
      page_name="actions_ssp"
    />
  </v-dialog>
    <client-only>
    <ActionFilter ref="actionFilter" @filterRecords="onFilterRecords" />
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
        :selected_header.sync="selected_header"
        :downloadContent="tableValues"
        :downloadDialog="downloadDialog"
        :filename="$tr('action_ssp')"
        :show-add-note="true"
        @onFilter="openFilterDialog"
        :showDownload="$isInScope('adve')"
        note-title="add_action_ssp"
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
          () => {
            dialogKey++;
            columnDialog = true;
          }
        "
      >
        <CustomButton
          @click="toggleRegisterDialog"
          icon="fa-user-plus"
          :text="$tr('create_item', $tr('action'))"
          type="primary"
        />
      </PageFilters>
      <!--end  page filter  -->
      <v-col cols="12">
        <CustomPageActions
          :showView="false"
          :showEdit="false"
          :showAutoEdit="false"
          :showRestore="showRestore"
          @fetchNewData="fetchNewData"
          :showDelete="$isInScope('advd')"
          :showSelect="$isInScope('advu')"
          :showApply="$isInScope('advu')"
          :selectedItems.sync="selectedItems"
          :tab-key.sync="tabKey"
          :showBlock="false"
          :statusItems="statusItems"
          :routeName="'single-sales/action'"
          :subSystemName="'Actions'"
          :noReasonSubmitOperations="'inprocess, archived, done'"
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
          :headers="selected_header"
          :items="tableValues"
          :ItemsLength="extraData.total"
          v-model="selectedItems"
          :loading="apiCalling"
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
          <template v-slot:[`item.goals`]="{ item }">
            <v-tooltip bottom max-width="200">
              <template v-slot:activator="{ on, attrs }">
                <span
                  v-bind="attrs"
                  v-on="on"
                  style="white-space: nowrap"
                  class="primary--text"
                >
                  <span class="mx-auto d-flex  ">
                    {{ goal(item.goals).substring(0, 30) + " ..." }}
                  </span>
                </span>
              </template>
              {{ goal(item.goals) }}
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
import { mapActions, mapGetters, mapState } from "vuex";
import action_spp from "~/configs/pages/test_ssp";
import ColumnHelper from "~/helpers/column-helper";
import PageFilters from "~/components/design/PageFilters.vue";
import CustomButton from "~/components/design/components/CustomButton.vue";
import ActionFilter from "../../components/single_sales_management_system/ActionFilter.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn.vue";

import PageHeader from "~/components/design/PageHeader.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import DataTable from "~/components/design/DataTable.vue";
import TestStepper from "../../components/single_sales_management_system/attribute_insertion/AttributeStepper.vue";


export default {
components: {
 
  PageHeader,
  PageFilters,
  CustomButton,
  ActionFilter,
  CustomPageActions,
  CustomizeColumn,
  TableTabs,
  DataTable,
  TestStepper
},
data() {
  return {
    tabKey: "all",
    tableValues: [],
    headers: action_spp(this).headers,
    category: action_spp().category,
    tabItems: action_spp().tabItems,
    breadcrumb: action_spp(this).breadcrumb,
    selectedItems: [],
    tableKey: 0,
    tabIndex: 0,
    options: {},
    extraData: {},

    //register section

    statusItems: [],

    // profile section
    showProfile: false,
    showRestore: false,

    // for custom columns
    selected_column: [],
    selected_header: [],
    apiCalling: false,
    isFetchCancels: false,

    showProgressBar: false,
    downloadDialog: false,
    columnDialog: false,
    dialogKey: 0,
  };
},

watch: {
  tabKey: function (index) {
    this.checkSelectedTab(index);
  },
},

computed: {
  ...mapGetters({
    custom_columns: "customColumns/get_custom_columns",
  }),
},

async fetch() {
  await this.getRecord();
},

async created() {
  //customize columns
  await this.getCustomColumn({ view_name: "attributes_ssp" });
  if (process.client) {
    const response = await ColumnHelper.getselectedHeader(
      this.headers,
      this.custom_columns
    );
    this.selected_header = response.selected_header;
    this.selected_column = this.selected_header.map((row) => row.id);
    this.category = await ColumnHelper.getCategoryValue(
      this.selected_header,
      this.category
    );
  }
  this.checkSelectedTab(this.tabKey);
},

methods: {
  ...mapActions({
    getCustomColumn: "customColumns/fetchCustomColumns",
  }),
  setStatusItems(status) {
    this.showRestore = false;
    switch (status) {
      case "inprocess":
        this.statusItems = [
          { id: "archived", name: "archived" },
          { id: "cancelled", name: "cancelled" },
        ];
        break;
      case "archived":
        this.statusItems = [
          { id: "done", name: "done" },
          { id: "failed", name: "failed" },
        ];
        break;
      case "failed": 
      case "cancelled":
        this.statusItems = [
          { id: "inprocess", name: "inprocess" },
        ];
        break;
      case "done":
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
  toggleRegisterDialog() {
    this.$refs.actionInsertRef.showDialog();
  },
  saveColumnSetting(data) {
    if (data.close_form) {
      this.columnDialog = false;
    }
    this.selected_header = data.selected_header;
    this.selected_column = data.selected_header.map((row) => row.id);
    this.category = ColumnHelper.getCategoryValue(
      data.selected_header,
      this.category
    );
  },

  openFilterDialog() {
    this.$refs.actionFilter.changeModel();
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
        "single-sales/action/search",
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
      const response = await this.$axios.get("single-sales/action", {
        params: data,
      });
      if (response.status === 200) {
        this.tableValues = response.data?.data;
        this.extraData = response?.data;
        console.log("my data", this.extraData)
        
      }
      this.apiCalling = false;
    } catch (error) {
      HandleException.handleApiException(this, error);
    }
  },
  viewSelectedDesignRequest(item) {
    this.showProfile = true;
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

  completedStyle(item) {
    return 1;
  },

  onTableChanges(options) {
    if (JSON.stringify(options) !== JSON.stringify(this.options)) {
      this.options = this._.clone(options);
      this.getRecord();
    } else this.options = this._.clone(options);
  },
    clearAndUnselectId(code) {
    this.selectedItems = this.selectedItems.filter(
      (row) => row.code !== code
    );
  },
    onSearchTypeChange() {
    this.selectedItems = [];
    this.searchContent = "";
    this.searchId = "";
  },
  onFilterRecords(filterData) {
    this.$root.$emit("resetPageNumber");
    this.filterData = filterData;
    this.getRecord();
  },
  async pushRecord(data) {
    if(this.tabKey == 'inprocess' || this.tabKey == 'all'){
      await this.tableValues.unshift(data);
    }
    this.extraData.allTotal += 1; 
    this.extraData.inprocessTotal += 1;
    this.extraData.total += 1;
  },
  goal(item){
    let data = item.replace('[','').replace(']','').replaceAll('"','');
    data = data.split(',').join(" ");
    return data;
  }
},
};
</script>

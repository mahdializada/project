<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0">
    <IsppFilter ref="IsppFilterRef" @filterRecords="onFilterRecords" />
    <!-- <IsppInsertion ref="isppInsertionModal" @pushRecord="pushRecord" /> -->
    <IsspStepper ref="issppInsertionStepper" @pushRecord="pushRecord" />
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
        :page_name="'ispp_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <client-only>
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`ispp`"
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
          :filename="$tr('ispp')"
          :show-add-note="true"
          @onFilter="openFilterDialog"
          :showDownload="$isInScope('adve')"
          note-title="add_ispp"
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
            :text="$tr('create_item', $tr('ispp'))"
            type="primary"
          />
        </PageFilters>
        <!--end  page filter  -->

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            :showEdit="false"
            :showAutoEdit="false"
            :showDelete="$isInScope('advd')"
            :showRestore="showRestore"
            :showApply="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            @fetchNewData="fetchNewData"
            :showBlock="false"
            :statusItems="statusItems"
            :routeName="'single-sales/ispp'"
            :subSystemName="'ISPP'"
            :noReasonSubmitOperations="'pending,in review,in preparation,in hold,completed'"
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
            ref="isppTableRef"
            :headers="all_headers[0].selected_headers"
            :ItemsLength="extraData.total"
            :items="tableValues"
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
            <template v-slot:[`item.target_sale_country`]="{ item }">
              <span
                style="white-space: nowrap"
                v-for="role in item.target_sale_country"
                :key="role.id"
              >
                {{ `${target_sale_country.name}` }}
              </span>
            </template>

            <template v-slot:[`item.selling_goals`]="{ item }">
              <v-tooltip bottom max-width="200">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex">
                      {{ item.selling_goals?.substring(0, 30) + " ..." }}
                    </span>
                  </span>
                </template>

                {{ item.selling_goals }}
              </v-tooltip>
            </template>

            <template v-slot:[`item.landing_page_note`]="{ item }">
              <v-tooltip bottom max-width="200">
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="mx-auto d-flex">
                      {{ item.landing_page_note?.substring(0, 30) + " ..." }}
                    </span>
                  </span>
                </template>
                {{ item.landing_page_note }}
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
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import ispp from "~/configs/pages/ispp";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";

import TableTabs from "~/components/common/TableTabs.vue";
import IsppFilter from "~/components/single_sales_management_system/IsppFilter.vue";
import IsppInsertion from "../../components/single_sales_management_system/IsppInsertion.vue";
import IsspStepper from "../../components/single_sales_management_system/issp_insertion/IsspStepper";
export default {
  components: {
    IsppInsertion,
    CustomPageActions,
    PageActions,
    CustomButton,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    DataTable,
    ProgressBar,
    TableTabs,
    CustomizeColumn,
    IsppFilter,
    IsspStepper,
  },
  data() {
    return {
      tabKey: "all",
      tableValues: [],

      tabItems: ispp(this).tabItems,
      breadcrumb: ispp(this).breadcrumb,

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
      downloadDialog: false,
      showProgressBar: false,
      showRestore: false,
    };
  },

  watch: {
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
  },

  async created() {
    //customize columns
    await this.fetchHeaders();
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
              tab_name: "ispp_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    openInsertDialog() {
      // this.$refs.isppInsertionModal.toggle();
      this.$refs.issppInsertionStepper.showDialog();
    },
    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
    },

    openFilterDialog() {
      this.$refs.IsppFilterRef.changeModel();
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

    setStatusItems(status) {
      switch (status) {
        case "pending":
          this.statusItems = [
            { id: "in review", name: "in review" },
            { id: "in preparation", name: "in preparation" },
            { id: "in hold", name: "in hold" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "in review":
          this.statusItems = [
            { id: "in preparation", name: "in preparation" },
            { id: "in hold", name: "in hold" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "in preparation":
          this.statusItems = [
            { id: "completed", name: "completed" },
            { id: "failed", name: "failed" },
            { id: "in hold", name: "in hold" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "in hold":
          this.statusItems = [
            { id: "failed", name: "failed" },
            { id: "in preparation", name: "in preparation" },
            { id: "cancelled", name: "cancelled" },
          ];
          break;
        case "cancelled":
          this.statusItems = [{ id: "in review", name: "in review" }];
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

    viewSelectedDesignRequest(item) {
      this.showProfile = true;
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
        const response = await this.$axios.get("single-sales/ispp", {
          params: data,
        });
        console.log("response:",response);
        if (response.status === 200) {
          this.tableValues = response.data?.data;
          this.extraData = response?.data;
        }
        this.apiCalling = false;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
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
    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
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
          "single-sales/ispp/search",
          options
        ); // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.getRecord();
    },

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        console.log(data);
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

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    async pushRecord(data) {
      if (this.tabKey === "all" || this.tabKey === "pendding")
        await this.tableValues.unshift(data);
      this.extraData.allTotal += 1;
      this.extraData.pendingTotal += 1;
      this.extraData.total += 1;
    },
  },
};
</script>

<style>
</style>

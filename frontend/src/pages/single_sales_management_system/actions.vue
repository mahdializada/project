<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0 mb-5">
    <ActionStepper ref="actionInsertRef" @pushRecord="pushRecord" />
    <updateActionStepper ref="updateActionStepper" @pushRecord="pushRecord" />
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
        :page_name="'actions_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>
    <client-only>
      <ActionFilter ref="actionFilter" @filterRecords="onFilterRecords" />
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`actions`"
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
          :filename="$tr('action_ssp')"
          :show-add-note="true"
          @onFilter="openFilterDialog"
          :showDownload="$isInScope('stre')"
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
          @onColumn="columnDialog = true"
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
            :showEdit="true"
            :showAutoEdit="false"
            :showRestore="showRestore"
            @fetchNewData="fetchNewData"
            :showDelete="$isInScope('drd')"
            :showSelect="$isInScope('dru')"
            :showApply="$isInScope('dru')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            :showBlock="false"
            :statusItems="statusItems"
            :routeName="'single-sales/action'"
            :subSystemName="'Actions'"
            :noReasonSubmitOperations="'inprocess, archived, done'"
            @onEdit="toggleEditDialog"
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
                    <span class="mx-auto d-flex">
                      {{ goal(item.goals)?.substring(0, 30) + " ..." }}
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
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import action_spp from "~/configs/pages/action_spp";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import ActionFilter from "../../components/single_sales_management_system/ActionFilter.vue";
import ActionStepper from "../../components/single_sales_management_system/action_insertion/ActionStepper.vue";
import updateActionStepper from "../../components/single_sales_management_system/action_insertion/updateActionStepper";

export default {
  components: {
    CustomPageActions,
    PageActions,
    CustomButton,
    DesignRequestEdit,
    PageHeader,
    PageFilters,
    DataTable,
    ProgressBar,
    TableTabs,
    ActionFilter,
    CustomizeColumn,
    ActionStepper,
    updateActionStepper,
  },
  data() {
    return {
      tabKey: "all",
      tableValues: [],

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
      downloadDialog: false,
    };
  },

  watch: {
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
  },

  async fetch() {
    await this.getRecord();
  },

  async created() {
    //customize columns
    await this.fetchHeaders();

    this.checkSelectedTab(this.tabKey);
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "actions_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    toggleEditDialog() {
      if (this.selectedItems.length == 1) {
        const map = (item) => {
          let classes = item.classes;
          return {
            id: item.id,
            code: item.code,
            goals: JSON.parse(item.goals),
            priority: item.priority,
            action_category: item.action_category,
            dependable_id: item.dependable_id,
            type: item.dependable_type.includes("Ipa") ? "ipa" : "ispp",
            statements:
              Array.isArray(classes) && classes.length > 0
                ? JSON.parse(classes[0].statement)
                : [],
            attachments:
              Array.isArray(classes) && classes.length > 0
                ? classes[0].attachments
                : [],
            value:
              Array.isArray(classes) && classes.length > 0
                ? classes[0].value
                : "",
          };
        };

        const parsedItems = this.selectedItems.map(map);
        this.$refs.updateActionStepper.showDialog(parsedItems[0]);
        this.editMode = true;
      }
    },
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
          this.statusItems = [{ id: "inprocess", name: "inprocess" }];
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
      if (this.tabKey == "inprocess" || this.tabKey == "all") {
        await this.tableValues.unshift(data);
      }
      this.extraData.allTotal += 1;
      this.extraData.inprocessTotal += 1;
      this.extraData.total += 1;
    },
    goal(item) {
      if(item){
      let data = item?.replace("[", "")?.replace("]", "")?.replaceAll('"', "");
      data = data?.split(",")?.join("\n");
      return data;
      }
    },
  },
};
</script>

<style></style>

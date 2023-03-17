<template>
  <v-container fluid class="pa-0">
    <AttribueteStepper ref="attributeInsertRef" @pushRecord="pushRecord" @mapRecord="mapRecord" />
    <updateAttributeStepper
      ref="updateAttributeStepper"
      @updateRecord="updateRecord"
    />
    <AttributeProfileView ref="attributeViewProfileRef" />
    <AttributesFilter
      ref="attributeFilter"
      @filterRecords="onFilterRecords"
    ></AttributesFilter>
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
        :page_name="'attributes_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>
    <!-- create form of attributes -->
    <v-dialog v-model="createFlag" width="1300">
      <Dialog @closeDialog="createFlag = false">
        <Create @pushRecord="pushRecord" />
      </Dialog>
    </v-dialog>
    <client-only>
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`attributes`"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
          >
          </PageHeader>
        </v-col>
        <!-- {{ createFlag }} -->
        <!--start  page filter  -->
        <PageFilters
          ref="pageFilterRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="tableValues"
          :downloadDialog="downloadDialog"
          :filename="$tr('attribute_ssp')"
          :show-add-note="true"
          @onFilter="openFilterDialog"
          :showDownload="$isInScope('adve')"
          note-title="add_attribute_ssp"
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
            :text="$tr('create_item', $tr('atrribute'))"
            type="primary"
          />
        </PageFilters>

        <!--end  page filter  -->

        <v-col cols="12">
          <CustomPageActions
            :showView="$isInScope('advv')"
            @fetchNewData="fetchNewData"
            @onEdit="toggleEditDialog"
            @onView="toggleViewDialog"
            :showEdit="$isInScope('advu')"
            :showAutoEdit="false"
            :showRestore="showRestore"
            :showDelete="$isInScope('advd')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            :showBlock="false"
            :statusItems="statusItems"
            :routeName="'single-sales/attribute-ssp'"
            :subSystemName="'Attributes'"
            :noReasonSubmitOperations="'active'"
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
                class="primary--text font-weight-bold"
              >
                {{ item.code }}
              </span>
            </template>
            <template v-slot:[`item.values`]="{ item }">
              <v-menu
              v-if="item.type == 'file'"
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
                    :src="item.values?.file_type=='image'?item.values?.cloud_path:''"
                  ></v-img>
                </span>
              </template>
              <span>
                <v-img
                  width="400"
                  lazy-src="https://picsum.photos/id/11/10/6"
                  :src="item.values?.file_type=='image'?item.values?.cloud_path:''"
                ></v-img>
              </span>
            </v-menu>
              <v-tooltip bottom max-width="200" v-else>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    <span class="">
                      {{ $tr("values") }}
                    </span>
                  </span>
                </template>
                <span v-for="value in item?.values" :key="value">
                  {{ value }} <br />
                </span>
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
import CustomButton from "~/components/design/components/CustomButton.vue";
import DataTable from "~/components/design/DataTable.vue";
import PageActions from "~/components/design/PageActions.vue";
import CustomPageActions from "~/components/design/CustomPageActions.vue";
import PageFilters from "~/components/design/PageFilters.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import DesignRequestEdit from "~/components/landing/DesignRequestEdit.vue";
import attributes from "~/configs/pages/attributes_ssp.js";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import ColumnHelper from "~/helpers/column-helper";
import TableTabs from "~/components/common/TableTabs.vue";
import AttributesFilter from "../../components/single_sales_management_system/AttributesFilter.vue";
import Dialog from "~/components/design/Dialog/Dialog.vue";
import Alert from "../../helpers/alert";

import Create from "~/components/single_sales_management_system/attribute/Create.vue";
import AttribueteStepper from "../../components/single_sales_management_system/attribute_insertion/AttributeStepper.vue";
import updateAttributeStepper from "../../components/single_sales_management_system/attribute_insertion/updateAttributeStepper.vue";
import AttributeProfileView from "../../components/single_sales_management_system/attribute/AttributeProfileView.vue";

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
    AttributesFilter,
    CustomizeColumn,
    Dialog,
    Create,
    AttribueteStepper,
    updateAttributeStepper,
    AttributeProfileView,
},
  data() {
    return {
      createFlag: false,
      tabKey: "all",
      tableValues: [],

      tabItems: attributes(this).tabItems,
      breadcrumb: attributes(this).breadcrumb,

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
      downloadDialog: false,

      showRestore: false,
    };
  },

  async created() {
    //customize columns
    await this.fetchHeaders();

    this.checkSelectedTab(this.tabKey);
  },
  async fetch() {
    await this.getRecord();
  },

  watch: {
    tabKey: function (index) {
      this.checkSelectedTab(index);
    },
  },

  methods: {
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "attributes_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
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
          this.statusItems = [];
          this.showRestore = true;
          break;
        default:
          this.statusItems = [];
          break;
      }
    },
    toggleRegisterDialog() {
      this.$refs.attributeInsertRef.showDialog();
    },
    toggleViewDialog() {
      console.log(this.selectedItems);
      if (this.selectedItems.length == 1) {
        const item = this.selectedItems[0]
        this.$refs.attributeViewProfileRef.showDialog(item);
      }
    },
    toggleEditDialog() {
      if (this.selectedItems.length == 1) {
        const map = (item) => {
          return {
            id: item.id,
            name: item.name,
            // selectedAttr: JSON.parse(item.values),
          };
        };
        const parsedItems = this.selectedItems.map(map);
        this.$refs.attributeInsertRef.showEditDialog(this.selectedItems[0]);
        this.editMode = true;
      } else {
        Alert.customAlert(this, "Please select one item", "restricted");
      }
    },
    mapRecord(datas){
      this.tableValues = this.tableValues.map((item) => {
        if (item?.id == datas?.id) return datas;
        else return item;
      });
    },
    checkSelectedTab(value) {
      // Commented due to pagination bug
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

    openFilterDialog() {
      this.$refs.attributeFilter.changeModel();
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
          "single-sales/attributes/search",
          options
        ); // Change the route
        this.selectData(response);
        console.log('data:',response);
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
        const response = await this.$axios.get("single-sales/attribute-ssp", {
          params: data,
        });
        console.log("data:",response);
        if (response.status === 200) {
          this.tableValues = response.data?.data;
          this.extraData = response?.data;
        }
        this.apiCalling = false;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    async pushRecord(data) {
      if (data.length > 0) {
        data.forEach((element) => {
          if (this.tabKey === "all" || this.tabKey === "inactive")
            this.tableValues.unshift(element);
          this.extraData.allTotal += 1;
          this.extraData.inactiveTotal += 1;
          this.extraData.total += 1;
        });
      }
    },
    async updateRecord(data) {
      this.tableValues = this.tableValues.map((item) => {
        if (item?.id == data?.id) return data;
        else return item;
      });
    },
    async fetch() {
      await this.getRecord();
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
    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
    },
  },
};
</script>

<style></style>

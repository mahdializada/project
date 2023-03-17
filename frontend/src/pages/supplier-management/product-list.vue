<template>
  <!-- main components in a page -->
  <v-container fluid class="pa-0 mb-5">
    <AddSupplierForm
      ref="supplierFormRefs"
      :selectedItems.sync="selectedItems"
      @fetchData="changeSupplier"
    />
    <ProductProfileInfoStepper ref="productProfileInfoRef" />
    <ProductListStepper
      ref="productListStepperRef"
      @addNewItem="addNewItem"
      @updateItem="updateItem"
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
        :page_name="'product_list_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <client-only>
      <ActionFilter ref="actionFilter" @filterRecords="onFilterRecords" />
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="'products'"
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
          :filename="$tr('product_list')"
          :show-add-note="true"
          @onFilter="openFilterDialog"
          :showDownload="$isInScope('prle')"
          note-title="add_product_list"
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
            @click="toggleOprationDialog(null)"
            icon="mdi-shopping"
            :text="$tr('create_item', $tr('product_list'))"
            type="primary"
          />
          <!-- <CustomButton
          @click="openModel()"
          icon="mdi-shopping"
          :text="$tr('create_item', $tr('test'))"
          type="primary"
          /> -->
        </PageFilters>
        <!--end  page filter  -->
        <v-col cols="12">
          <CustomPageActions
            :showView="false"
            :showEdit="true"
            :showAutoEdit="false"
            :showAddSupplier="true"
            :showRestore="showRestore"
            @fetchNewData="fetchNewData"
            :showDelete="$isInScope('prld')"
            :showSelect="$isInScope('dru')"
            :showApply="$isInScope('dru')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabKey"
            :showBlock="false"
            :statusItems="statusItems"
            :routeName="'product_list'"
            :subSystemName="'Product List'"
            :noReasonSubmitOperations="'with suppliers, archived, done'"
            @onAddSupplier="AddSupplier"
            @onEdit="toggleOprationDialog(selectedItems[0])"
          />
        </v-col>
        <v-col cols="12">
          <TableTabs
            @getSelectedTabRecords="
              (key) => {
                tabKey = key;
                (selectedItems = []), fetchRecord();
              }
            "
            :tabData="tabItems"
            :extraData="extraData"
          />

          <DataTable
            ref="projectTableRef"
            :headers="all_headers[0].selected_headers"
            :items="tableValues"
            :ItemsLength="total"
            v-model="selectedItems"
            :loading="apiCalling"
            @click:row="onRowClicked"
            :item-class="completedStyle"
            @onTablePaginate="onTableChanges"
            :key="tableKey"
          >
            <template v-slot:[`item.product_image`]="{ item }">
              <v-menu
                offset-y
                open-on-hover
                :close-on-content-click="false"
                :open-delay="500"
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
                      :src="item?.attachments[0]?.path"
                    ></v-img>
                  </span>
                </template>
                <span>
                  <v-img
                    width="400"
                    lazy-src="https://picsum.photos/id/11/10/6"
                    :src="item?.attachments[0]?.path"
                  ></v-img>
                </span>
              </v-menu>
            </template>
            <template v-slot:[`item.id`]="{ item }">
              <!-- @click="() => viewSelectedDesignRequest(item)" -->
              <span class="mx-1 primary--text font-weight-bold">
                {{ item.id }}
              </span>
            </template>
            <template v-slot:[`item.suppliers`]="{ item }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text font-weight-bold"
                  >
                    {{ $tr("suppliers") }}
                  </span>
                </template>
                <span v-for="(supplier, i) in item.suppliers" :key="i"
                  >{{ supplier.supplier_name }}
                  <b class="white--text"> {{ supplier.pivot.product_cost }} </b
                  ><br
                /></span>
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
import Products from "~/configs/pages/product_list";
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import TableTabs from "~/components/common/TableTabs.vue";
import HandleException from "~/helpers/handle-exception";
import ActionFilter from "~/components/single_sales_management_system/ActionFilter.vue";
import ProductListStepper from "../../components/supplier/product_list/ProductListStepper.vue";
import AddSupplierForm from "../../components/supplier/product_list/AddSupplierForm.vue";
import ProductProfileInfoStepper from "../../components/advertisement/product-profile-info/ProductProfileInfoStepper.vue";

export default {
  components: {
    CustomPageActions,
    PageActions,
    CustomButton,
    PageHeader,
    PageFilters,
    DataTable,
    ProgressBar,
    TableTabs,
    ActionFilter,
    CustomizeColumn,
    ProductListStepper,
    AddSupplierForm,
    ProductProfileInfoStepper,
  },
  data() {
    return {
      tabKey: "all",
      tableValues: [],
      tabItems: Products(this).tabItems,
      breadcrumb: Products(this).breadcrumb,
      selectedItems: [],
      tableKey: 0,
      tabIndex: 0,
      options: {},
      extraData: {},
      total: 0,

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
  async created() {
    await this.fetchHeaders();
    this.fetchRecord();
  },

  methods: {
    openModel() {
      this.$refs.productProfileInfoRef.openDialog();
    },
    changeSupplier() {
      this.selectedItems = [];
      this.fetchRecord();
    },
    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "product_list_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },
    addNewItem(newItem) {
      if (this.tabKey != "with supplier")
        for (let index = 0; index < newItem.length; index++) {
          this.tableValues?.unshift(newItem[index]);
        }
      this.total += 1;
      this.extraData.allTotal += 1;
      this.extraData.withoutSupplierTotal += 1;
    },
    updateItem(updateItem) {
      try {
        this.tableValues = this.tableValues.map((i) => {
          if (i.id == updateItem.id) {
            return updateItem;
          } else {
            return i;
          }
        });
      } catch(error) {
        console.error('error',error)
      }
    },
    toggleOprationDialog(item) {
      if (item && this.selectedItems.length > 1) {
        this.$toasterNA("orange", this.$tr("select_only_one_item"));
        return;
      }
      this.$refs.productListStepperRef.showDialog(item);
    },

    openFilterDialog() {
      this.$refs.actionFilter.changeModel();
    },

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.fetchRecord();
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
    async fetchRecord() {
      try {
        const data = this.$fetchOptions(
          this.tabKey,
          this.filterData,
          this.options,
          this.searchContent,
          this.searchId
        );
        this.apiCalling = true;
        const response = await this.$axios.get("product_list", {
          params: data,
        });
        if (response.status === 200) {
          this.tableValues = response.data?.data.data;
          this.total = response.data.data.total;
          this.extraData = response?.data?.extraData;
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
      this.fetchRecord();
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
        this.fetchRecord();
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
      this.fetchRecord();
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
      let data = item.replace("[", "").replace("]", "").replaceAll('"', "");
      data = data.split(",").join("\n");
      return data;
    },
    AddSupplier() {
      if (this.tabKey == "with supplier" && this.selectedItems.length > 1) {
        this.$toasterNA("orange", this.$tr("select_only_one_item"));
        return;
      }
      this.$refs.supplierFormRefs.toggleDialog();
    },
  },
};
</script>

<style></style>

<template>
     <v-container fluid class="pa-0">
          <BrandFilter ref="BrandFilterRef" @filterRecords="onFilterRecords" />
          <AccountPixelInsertion ref="pixelInsetionRefs" @pushRecord="pushRecord" />
          <v-dialog v-model="columnDialog" width="1300" persistent>
               <CustomizeColumn @saveChanges="
                 (data) => {
                   all_headers[0] = data;
                   all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
                   columnDialog = false;
                 }
               " :page_headers.sync="all_headers[0]" :page_name="'Brands_all'"
                    @closeColumnDialog="columnDialog = false" />
          </v-dialog>
          <v-dialog v-model="userRegisterDialog" width="1300" persistent v-if="$isInScope('uc')">
               <Dialog @closeDialog="closeRegisterDialog">
                    <CreateBrand :tableKey="tabItems[tabIndex].key" ref="registerUserDialog" />
               </Dialog>
          </v-dialog>
          <client-only>
               <v-row no-gutters>
                    <ProgressBar v-if="showProgressBar" />

                    <v-col cols="12">
                         <PageHeader :Icon="breadcrumb[1].icon" :Title="breadcrumb[1].text" :Breadcrumb="breadcrumb">
                         </PageHeader>
                    </v-col>

                    <!--start  page filter  -->
                    <PageFilters ref="pageFilterRef" :selected_header.sync="all_headers[0].selected_headers"
                         :downloadContent="tableValues" :downloadDialog="downloadDialog" :filename="$tr('brand')"
                         :show-add-note="true" @onFilter="openFilterDialog" :showDownload="$isInScope('stre')"
                         note-title="Add Brand" @onDownload="
                           () => {
                             downloadDialog = !downloadDialog;
                           }
                         " @searchById="searchById" @clearAndUnselectId="clearAndUnselectId"
                         @searchByContent="searchByContent" @onTypeChange="onSearchTypeChange"
                         @onColumn="columnDialog = true">
                         <CustomButton v-if="$isInScope('drc')" icon="fa-plus" @click="openInsertDialog"
                              :text="$tr('create_item', $tr('account_pixel'))" type="primary" />
                    </PageFilters>
                    <!--end  page filter  -->
                    <v-col cols="12">
                         <CustomPageActions :showView="$isInScope('drv')" :showEdit="true" :showAutoEdit="false"
                              :showRestore="tabKey == 'removed'" :showDelete="$isInScope('drd')"
                              :showSelect="$isInScope('dru')" :showApply="$isInScope('dru')"
                              :selectedItems.sync="selectedItems" :tab-key.sync="tabKey" :showBlock="false"
                              :statusItems="statusItems" :routeName="'advertisement/brand'" :subSystemName="'Brands'"
                              :noReasonSubmitOperations="'active'" @fetchNewData="fetchNewData">
                         </CustomPageActions>
                    </v-col>
                    <v-col cols="12">
                         <TableTabs @getSelectedTabRecords="
                           (key) => {
                             tabKey = key;
                             getRecord();
                           }
                         " :tabData="tabItems" :extraData="extraData" />

                         <DataTable ref="isppTableRef" :headers="all_headers[0].selected_headers" :items="tableValues"
                              v-model="selectedItems" :ItemsLength="extraData.total" :loading="apiCalling"
                              @onTablePaginate="onTableChanges" @click:row="onRowClicked" :key="tableKey">
                              <template v-slot:[`item.code`]="{ item }">
                                   <span @click="() => viewSelectedDesignRequest(item)"
                                        class="mx-1 primary--text font-weight-bold">
                                        {{ item.code }}
                                   </span>
                              </template>
                              <template v-slot:[`item.is_approved`]="{ item }">
                                   <span class="mx-1 ps-5">
                                        {{ item.is_approved ? $tr("yes") : $tr("no") }}
                                   </span>
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
import ProgressBar from "~/components/common/ProgressBar.vue";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";

import TableTabs from "~/components/common/TableTabs.vue";
import account_pixel from "~/configs/pages/account_pixel";
import BrandFilter from "~/components/single_sales_management_system/BrandFilter.vue";
import Dialog from "../../components/design/Dialog/Dialog.vue";
import HandleException from "../../helpers/handle-exception";
import AccountPixelInsertion from "../../components/advertisement/account_pixel/AccountPixelInsertion.vue";
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
          BrandFilter,
          CustomizeColumn,
          Dialog,
          AccountPixelInsertion
     },
     data() {
          return {
               tabKey: "all",
               tableValues: [],

               tabItems: account_pixel(this).tabItems,
               breadcrumb: account_pixel(this).breadcrumb,

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

               userRegisterDialog: false,
          };
     },

     async created() {
          await this.fetchHeaders();

          await this.getRecord();
     },

     watch: {
          tabKey: function (index) {
               this.checkSelectedTab(index);
          },
     },
     methods: {
          async submit(formRef, vuelidate) {
               return true;
          },

          async fetchHeaders() {
               try {
                    if (this.all_headers[0].selected_headers != []) {
                         const response = await this.$axios.get("sub-system-header", {
                              params: {
                                   tab_name: "Brands_all",
                              },
                         });
                         this.all_headers[0].selected_headers = response.data.selected_headers;
                         this.all_headers[0].headers = response.data.headers;
                    }
               } catch (error) { }
          },

          fetchNewData() {
               this.selectedItems = [];
               this.getRecord();
          },
          viewSelectedDesignRequest(item) {
               this.showProfile = true;
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
                         "product-management/pr-brand/search",
                         options
                    ); // Change the route
                    this.selectData(response);
               } catch (error) {
                    HandleException.handleApiException(this, error);
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
                    const response = await this.$axios.get("product-management/pr-brand", {
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
          setStatusItems(status) {
               switch (status) {
                    case "active":
                         this.statusItems = [{ id: "inactive", name: "inactive" }];
                         break;
                    case "inactive":
                         this.statusItems = [{ id: "active", name: "active" }];
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

          clearAndUnselectId(code) {
               this.selectedItems = this.selectedItems.filter(
                    (row) => row.code !== code
               );
          },

          searchByContent(searchContent) {
               this.searchContent = searchContent;
               this.fetchDataAccordingtoStatus();
          },

          openFilterDialog() {
               this.$refs.BrandFilterRef.changeModel();
          },

          onFilterRecords(filterData) {
               this.$root.$emit("resetPageNumber");
               this.filterData = filterData;
               this.fetchDataAccordingtoStatus();
          },

          closeRegisterDialog() {
               this.userRegisterDialog = false;
               this.$refs.registerUserDialog.close();
          },

          toggleUserRegisterDialog() {
               this.registerUserKey++;
               this.editableUser = {};
               this.isEditUser = false;
               this.userRegisterDialog = !this.userRegisterDialog;
          },
          onRowClicked(item) {
               let items = new Set(this.selectedItems);
               items.has(item) ? items.delete(item) : items.add(item);
               this.selectedItems = Array.from(items);
          },

          openInsertDialog() {
               this.$refs.pixelInsetionRefs.showDialog();
          },
          async fetchDataAccordingtoStatus() {
               this.apiCalling = true;
               this.selectedItems = [];
               this.checkSelectedTab(this.tabKey);
               const data = this.$fetchOptions(
                    this.tabKey,
                    this.filterData,
                    this.options,
                    this.searchContent,
                    this.searchId
               );
               const response = await this.$axios.get(`product-management/pr-brand`, {
                    params: data,
               });
               if (response?.status === 200) {
                    this.tableValues = response.data?.data;
                    this.extraData = response?.data;
               }
               this.apiCalling = false;
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

          async pushRecord(data) {
               if (this.tabKey === "all" || this.tabKey === "inactive")
                    await this.tableValues.unshift(data);
               this.extraData.allTotal += 1;
               this.extraData.inactiveTotal += 1;
               this.extraData.total += 1;
          },
          completedStyle(item) {
               return item.status == "completed" ? "completedAnimation" : "";
          },
     },
};
</script>

<style>

</style>

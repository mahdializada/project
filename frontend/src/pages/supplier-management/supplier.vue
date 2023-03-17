<template>
  <v-container fluid class="pa-0">
    <!-- column dialog -->
    <v-dialog v-model="columnDialog" persistent width="1300" class="rounded-0">
      <CustomizeColumn
        @closeDialog="columnDialog = false"
        @saveChanges="saveColumnSetting($event)"
        :tableHeader="$_.cloneDeep(headers)"
        :custom_columns="$_.cloneDeep(selected_column)"
        :categoryHeader="$_.cloneDeep(category)"
        :key="dialogKey"
        page_name="suppliers"
      />
    </v-dialog>
    <!-- Add supplier location component  -->
    <AddSupplierLocation ref="locationRegistrationRef" />
    <!-- View of location -->
    <view-supplier-locations ref="ViewSupplierLocationsRef" />
    <supplier-stepper ref="SupplierStepperRef" />
    <supplier-stepper ref="SupplierStepperRef" />
    <!-- Supplier Profile Section -->
    <v-dialog
      v-model="showProfile"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('advv')"
    >
      <SupplierProfile
        :dialog.sync="showProfile"
        :profileData="profileData"
        :key="key"
        @closeProfile="showProfile = false"
        @onEdit="onEdit"
      />
    </v-dialog>
    <!-- Supplier Bulk Upload -->
    <!-- <v-dialog v-model="supplier" width="1300">
      <Dialog persistent max-width="1300" @closeDialog="supplier = false">
        <SupplierBulkUpload :tabKey="getTabKey()" v-if="supplier" />
      </Dialog>
    </v-dialog> -->
    <!--    Supplier Register Dialog-->
    <v-dialog
      v-model="supplierRegisterDialog"
      width="1300"
      persistent
      v-if="$isInScope('advc')"
    >
      <Dialog @closeDialog="close">
        <supplierRegister ref="supplierRegisterDialog" />
      </Dialog>
    </v-dialog>

    <v-dialog
      v-model="SupplierFilterDialog"
      persistent
      max-width="1300"
      width="1300"
    >
      <SupplierFilter
        @close="onFilter"
        @getRecord="getRecord"
        :prevFilterData="filterData"
        :key="dialogKey"
      />
    </v-dialog>
    <v-dialog
      v-model="editDialog"
      persistent
      width="1300"
      v-if="$isInScope('advu')"
    >
      <EditSupplier
        :edit-dialog.sync="editDialog"
        :key="editKey"
        :allCompanies="allCompanies"
        :allCountries="allCountries"
        :allSourcers="allSourcers"
        :editItems="editItems"
        :is-auto-edit.sync="isAutoEdit"
      />
    </v-dialog>
    <!-- :allLabels="allLabels" -->
    <!-- supplier bank account -->
    <v-dialog v-model="bankaccountdialog" width="1300">
      <Dialog persistent max-width="1300" @closeDialog="closebankaccount">
        <AddSupplierBankAccount
          :id="selectedItems[0]?.id"
          :reset="bankaccountdialog"
        />
      </Dialog>
    </v-dialog>
    <!-- view bank dialog -->
    <SupplierViewBankAccount ref="supplierViewBankAccountRef" />
    <v-container fluid class="pa-0">
      <v-row no-gutters>
        <ProgressBar v-if="showProgressBar" />
        <v-col cols="12">
          <PageHeader
            :Icon="`supplier_management_system`"
            :Title="breadcrumb[1].text"
            :Breadcrumb="breadcrumb"
          />
        </v-col>

        <v-col cols="12">
          <PageFilters
            ref="pageFilterRef"
            :selected_header.sync="selected_header"
            :downloadContent="suppliers"
            :downloadDialog="downloadDialog"
            :filename="$tr('suppliers')"
            :content.sync="content"
            :contentT.sync="contentData"
            :selectedOption.sync="type"
            @onDownload="toggleDownload"
            @onFilter="onFilter"
            @search="searchSupplier"
            @searchAgain="searchAgain"
            @onTypeChange="onTypeChange"
            @unSelect="unSelect"
            @onColumn="
              () => {
                dialogKey++
                columnDialog = true
              }
            "
            note-title="add_project_note"
            :show-add-note="$isInScope('advc')"
            :showDownload="$isInScope('advv')"
          >
            <!-- <CustomButton
              icon="fa-plus"
              @click="supplierRegisterDialog = true"
              :text="$tr('add_item', $tr('supplier'))"
              type="primary"
            /> -->
            <CustomButton
              icon="fa-plus"
              @click="openBankAccountDialog()"
              v-show="isAddBankAccount"
              :text="$tr('add_item', $tr('bank_account'))"
              type="primary"
            />
            <CustomButton
              icon="fa-plus"
              @click="SupplierRegistrations"
              :text="$tr('add_item', $tr('supplier'))"
              type="warning"
            />
            <CustomButton
              icon="fa-plus"
              v-show="isAddLocation"
              @click="locationSupplierDialog"
              :text="$tr('add_item', $tr('location'))"
              type="primary"
            />
          </PageFilters>
        </v-col>
        <v-col cols="12">
          <PageActions
            :showView="$isInScope('advv')"
            :showEdit="$isInScope('advu')"
            :showAutoEdit="$isInScope('advu')"
            :showDelete="$isInScope('advd')"
            :showBlock="$isInScope('advu')"
            :showSelect="$isInScope('advu')"
            :showApply="$isInScope('advu')"
            :selectedItems.sync="selectedItems"
            :tab-key.sync="tabItems[tabIndex].key"
            @onView="onView"
            @onAutoEdit="onAutoEdit"
            @onEdit="onEdit"
            :routeName="'suppliers'"
            :subSystemName="'Sourcing Request'"
            @fetchNewData="fetchNewData"
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
                      class="ms-1 tab-chip"
                      :color="item.isSelected ? 'primary' : 'white'"
                      :text-color="item.isSelected ? 'white' : 'primary'"
                      small
                      v-text="getTotals(tabItems[i].key)"
                    />
                  </v-tab>
                </client-only>
              </v-tabs>
            </v-col>
          </v-row>
          <DataTable
            v-model="selectedItems"
            :headers="selected_header"
            :key="tableKey"
            :loading="apiCalling"
            @click:row="onRowClicked"
            ref="supplierTableRef"
            :items="suppliers"
            :ItemsLength="getTotals(getTabKey())"
            @select="onItemsSelect"
            @onTablePaginate="onTableChanges"
          >
            <template v-slot:[`item.code`]="{ item }">
              <span
                @click="() => viewSelectedSupplier(item)"
                class="mx-1 primary--text font-weight-bold"
              >
                {{ item.code }}
              </span>
            </template>
            <template v-slot:[`item.email`]="{ item }">
              <span class="primary--text">
                <a :href="`mailto:${item.email}`">{{ item.email }}</a>
              </span>
            </template>
            <template v-slot:[`item.website`]="{ item }">
              <span class="primary--text">
                <a :href="item.website" target="_blank">{{ item.website }}</a>
              </span>
            </template>
            <template v-slot:[`item.label`]="{ item }">
              <span v-if="item.label">
                {{ item.label.title }}
              </span>
            </template>
            <template v-slot:[`item.supplierLogo`]="{ item }">
              <v-avatar :size="30" color="blue-grey darken-3">
                <span class="white--text text-h6 pa-1">
                  {{
                    item.supplier_name
                      ? item.supplier_name[0].toUpperCase()
                      : ''
                  }}
                </span>
              </v-avatar>
            </template>
            <!-- <template v-slot:[`item.sourcers`]="{ item }">
              <span>
                {{ item.sourcer }}
              </span>
            </template> -->
            <template v-slot:[`item.sourcers`]="{ item }">
              <div v-for="(sourcer, index) in item.sourcers" :key="index">
                {{ sourcer.name }}
              </div>
            </template>
            <template v-slot:[`item.supp_location`]="{ item }">
              <span
                @click="viewLocations(item.id)"
                class="mx-1 font-weight-bold"
              >
                <v-icon class="primary--text">
                  mdi-map-marker-multiple-outline
                </v-icon>
              </span>
            </template>
            <template v-slot:[`item.bank_account`]="{ item }">
              <span
                @click="viewBankAccount(item.id)"
                class="mx-1 font-weight-bold"
              >
                <v-icon class="primary--text">mdi-bank</v-icon>
              </span>
            </template>
          </DataTable>
          <h1></h1>
        </v-col>
      </v-row>
      <br />
      <br />
      <br />
      <br />
      <br />
    </v-container>
  </v-container>
</template>

<script>
import SupplierProfile from '../../components/supplier/SupplierProfile'
import { mapActions, mapGetters } from 'vuex'
import CustomButton from '../../components/design/components/CustomButton.vue'
import DataTable from '../../components/design/DataTable.vue'
import Dialog from '../../components/design/Dialog/Dialog.vue'
import PageActions from '../../components/design/PageActions.vue'
import PageFilters from '../../components/design/PageFilters.vue'
import PageHeader from '../../components/design/PageHeader.vue'
import Suppliers from '../../configs/pages/suppliers'
import HandleException from '~/helpers/handle-exception'
import Alert from '../../helpers/alert'
import ProgressBar from '../../components/common/ProgressBar.vue'
import CustomizeColumn from '../../components/customizeColumn/CustomizeColumn.vue'
import EditSupplier from '~/components/supplier/EditSupplier.vue'
import supplierRegister from '~/components/supplier/supplierRegister.vue'
import SupplierFilter from '~/components/supplier/SupplierFilter.vue'
import ColumnHelper from '~/helpers/column-helper'
// import SupplierBulkUpload from "~/components/supplier/SupplierBulkupload.vue";
import SupplierStepper from '../../components/supplier/supplier-Stepper/SupplierStepper.vue'
import AddSupplierLocation from '../../components/supplier/AddSupplierLocation.vue'
import ViewSupplierLocations from '../../components/supplier/ViewSupplierLocations.vue'
import AddSupplierBankAccount from '../../components/supplier/AddSupplierBankAccount.vue'
import SupplierViewBankAccount from '../../components/supplier/SupplierViewBankAccount.vue'

export default {
  meta: {
    hasAuth: true,
    scope: 'advv',
  },
  async asyncData({ store }) {
    await store.dispatch('customColumns/fetchCustomColumns', {
      view_name: 'suppliers',
    })
    await store.dispatch('suppliers/fetchSuppliers', {
      key: 'all',
      options: {
        ...this?.filterData,
        type: this?.type,
        content: this?.content,
        contentData: this?.contentData,
      },
    })
  },
  components: {
    EditSupplier,
    ProgressBar,
    PageActions,
    CustomButton,
    PageHeader,
    PageFilters,
    Dialog,
    DataTable,
    CustomizeColumn,
    ProgressBar,
    SupplierProfile,
    supplierRegister,
    SupplierFilter,
    // SupplierBulkUpload,
    SupplierStepper,
    AddSupplierLocation,
    ViewSupplierLocations,
    AddSupplierBankAccount,
    SupplierViewBankAccount,
  },

  data() {
    return {
      supplier: false,
      supplierRegisterDialog: false,
      category: Suppliers(this).category,
      headers: Suppliers(this).headers,
      tabItems: Suppliers(this).tabItems,
      breadcrumb: Suppliers(this).breadcrumb,
      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      options: {},
      key: 0,
      isAddLocation: false,

      //register section
      registerDialog: false,

      downloadDialog: false,

      // profile section
      showProfile: false,

      // for custom columns
      dialogKey: 0,
      columnDialog: false,
      selected_column: [],
      selected_header: [],
      showProgressBar: false,

      // Edit and Auto Edit
      editData: {},
      editKey: 0,
      editDialog: false,
      isAutoEdit: false,
      editItems: [],
      allSourcers: [],
      // allLabels: [],
      allCountries: [],
      allCompanies: [],

      autoEdit: false,
      editKey: 0,
      selectedItems: [],
      type: 1,
      content: [],
      contentData: '',
      SupplierFilterDialog: false,
      filterData: {},
      profileData: {},

      // bulkUploadDialog: false,
      selectedsupplier: [],
      bankaccountdialog: false,
      isAddBankAccount: false,
    }
  },

  watch: {
    tabIndex: function (value) {
      let data = {
        ...this.filterData,
        type: this.type,
        content: this.content,
        contentData: this.contentData,
      }
      this.checkSelectedTab(value)
      this.fetchSuppliers({ key: this.getTabKey(), options: data })
    },
    selectedItems: function () {
      if (this.selectedItems.length == 1) {
        this.isAddLocation = true
        this.isAddBankAccount = true
      } else {
        this.isAddLocation = false
        this.isAddBankAccount = false
      }
    },
  },

  computed: {
    ...mapGetters({
      getTranslations: 'translations/getTranslations',
      suppliers: 'suppliers/getSuppliers',
      itemsTotal: 'suppliers/itemsTotal',
      apiCalling: 'suppliers/isApiCalling',
      getTotals: 'suppliers/getTotal',
      custom_columns: 'customColumns/get_custom_columns',
    }),
  },

  async created() {
    //customize columns
    if (process.client) {
      const response = await ColumnHelper.getselectedHeader(
        this.headers,
        this.custom_columns,
      )
      this.selected_header = response.selected_header
      this.selected_column = this.selected_header.map((row) => row.id)
      this.category = await ColumnHelper.getCategoryValue(
        this.selected_header,
        this.category,
      )
    }
    this.$root.$on('getData', (data) => {
      this.filterData = data
    })
  },

  methods: {
    locationSupplierDialog() {
      this.$refs.locationRegistrationRef.openDialog(this.selectedItems[0].id)
    },
    viewLocations(id) {
      this.$refs.ViewSupplierLocationsRef.openDialog(id)
    },
    SupplierRegistrations() {
      this.$refs.SupplierStepperRef.toggle(this.getTabKey())
    },
    // supplier bank account dialog
    openBankAccountDialog() {
      this.bankaccountdialog = true
    },
    closebankaccount() {
      this.bankaccountdialog = false
    },

    // view supplier bank account dialog
    viewBankAccount(id) {
      this.$refs.supplierViewBankAccountRef.togglleBankViewDialog(id)
    },
    SupplierRegistrations() {
      this.$refs.SupplierStepperRef.toggle(this.getTabKey())
    },
    closecoldialog() {
      this.columnDialog = true
    },
    // onView() {
    // 	this.showProfile = true;
    // 	this.profileData = this.selectedItems[0];
    // 	console.log(this.profileData);
    // },
    ...mapActions({
      fetchTranslations: 'translations/fetchTranslations',
      fetchSuppliers: 'suppliers/fetchSuppliers',
      fetchCompanies: 'companies/fetchCompanies',
      changeRecordStatus: 'projects/changeRecordStatus',
      fetchAscCountries: 'countries/fetchAscCountries',
      fetchCountries: 'countries/fetchCountries',
      deleteItem: 'suppliers/deleteItem',
      insertNewItem: 'suppliers/insertNewItem',
    }),
    toggleProgressBar() {
      this.showProgressBar = !this.showProgressBar
    },
    onItemsSelect(items) {
      this.selectedsupplier = items
    },
    onTableChanges(options) {
      let data = {
        ...options,
        ...this.filterData,
        type: this.type,
        content: this.content,
        contentData: this.contentData,
      }

      this.fetchSuppliers({ key: this.getTabKey(), options: data })
    },

    checkSelectedTab(value) {
      this.tableKey++
      this.selectedItems = []
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0)
        return item
      })
    },
    getTabKey() {
      return this.tabItems[this.tabIndex].key
    },

    onView() {
      if (this.selectedItems.length == 1) {
        this.profileData = this.selectedItems[0]
        console.log(this.profileData)
        this.key++
        this.showProfile = true
      } else {
        // this.$toastr.w(this.$tr("view_item_max_limit_text"));
        this.$toasterNA('orange', this.$tr('view_item_max_limit_text'))
      }
    },

    close() {
      var isInserted = this.$refs.supplierRegisterDialog.closeForm()

      if (isInserted) {
        let data = {
          ...this.filterData,
          type: this.type,
          content: this.content,
          contentData: this.contentData,
        }

        this.fetchSuppliers({ key: 'all', options: data })
      }
      this.supplierRegisterDialog = false
    },
    onFilter() {
      this.dialogKey++
      this.SupplierFilterDialog = !this.SupplierFilterDialog
    },

    toggleDownload() {
      this.downloadDialog = !this.downloadDialog
    },

    fetchNewData() {
      this.selectedItems = []
      let data = {
        ...this.filterData,
        type: this.type,
        content: this.content,
        contentData: this.contentData,
      }

      this.fetchSuppliers({ key: this.getTabKey(), options: data })
    },

    async viewSelectedSupplier(item) {
      this.profileData = item
      this.showProfile = true
    },

    goToWebsite(link) {},

    editSelectedSupplier(item) {
      this.showProfile = false
      this.supplierEditKey++
      this.editData = item
      this.supplierEdit = !this.supplierEdit
    },

    // customize columns: start=>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    //customize columns: called from child
    saveColumnSetting(data) {
      if (data.close_form) {
        this.columnDialog = false
      }
      this.selected_header = data.selected_header
      this.selected_column = data.selected_header.map((row) => row.id)
      this.category = ColumnHelper.getCategoryValue(
        data.selected_header,
        this.category,
      )
    },
    // customize columns: end =========================================================================================
    async searchSupplier() {
      if (this.type == 1) {
        try {
          const data = {
            type: this.type,
            content: this.content,
            contentData: this.contentData,
          }
          const response = await this.$axios.post(
            'suppliers/searchSupplier',
            data,
          )
          this.selectData(response)
        } catch (error) {}
      } else if (this.type == 0) {
        this.getRecord(this.filterData)
      }
    },
    onTypeChange() {
      this.selectedItems = []
    },
    selectData(response) {
      if (response.status === 200) {
        const data = response.data
        if (data != null) {
          this.deleteItem(data.id)
          this.insertNewItem(data)
          this.selectedItems?.unshift(data)
          this.$refs.supplierTableRef.selected?.unshift(data)
        }
      } else {
        this.$refs.pageFilterRef.clearInput()
      }
    },
    searchAgain() {
      this.getRecord(this.filterData)
    },
    onRowClicked(item) {
      let items = new Set(this.selectedItems)
      items.has(item) ? items.delete(item) : items.add(item)
      this.selectedItems = Array.from(items)
    },
    async getRecord(filterData) {
      try {
        const data = {
          key: this.getTabKey(),
          options: {
            type: this.type,
            content: this.content,
            contentData: this.contentData,
            supply_type: filterData.supply_type,
            legal_type: filterData.legal_type,
            commercial_type: filterData.commercial_type,
            prepration_period: filterData.prepration_period,
            country_type: filterData.country_type,
            created_date: filterData.created_date,
            updated_date: filterData.updated_date,
            supplier_ids: filterData.supplier_ids,
            include: filterData.include,
            supplier_name: filterData.supplier_name,
            supplier_trading_name: filterData.supplier_trading_name,
          },
        }
        const res = await this.fetchSuppliers(data)
      } catch (error) {
        // this.$toastr.w(error);
        this.$toasterNA('orange', this.$tr(error))
      }
    },
    async onAutoEdit() {
      await this.fetchNeccessaryData()
      this.isAutoEdit = true
      this.editKey++
      this.editDialog = true
      this.editItems = this._.clone(this.selectedItems)
      this.selectedItems = []
    },

    async fetchNeccessaryData() {
      this.showProgressBar = true
      if (
        this.allSourcers.length === 0 ||
        this.allCountries.length === 0 ||
        // this.allLabels.length === 0 ||
        this.allCompanies.length === 0
      ) {
        try {
          const response = await this.$axios.get('suppliers/id')
          if (response?.status === 200 && response?.data?.result) {
            this.allCompanies = response?.data?.allCompanies
            // this.allLabels = response?.data?.allLabels;
            this.allSourcers = response?.data?.allSourcers
            this.allCountries = response?.data?.allCountries
          } else {
            this.$toasterNA('red', this.$tr('something_went_wrong'))
          }
        } catch (error) {
          HandleException.handleApiException(this, error)
        }
      }
      this.showProgressBar = false
    },
    async onEdit() {
      if (this.selectedItems.length == 1)
        this.$refs.SupplierStepperRef.toggleEdit(this.selectedItems[0])
      // await this.fetchNeccessaryData();
      // this.editKey++;
      // this.editDialog = true;
      // this.editItems[0] = this._.clone(this.selectedItems[0]);
      // this.selectedItems = [];
    },

    // toggleBulkUpload() {
    //   this.supplier = !this.supplier
    // },

    bulk_upload() {},
    unSelect(key) {
      this.selectedItems = this.selectedItems.filter(
        (data) => data.code !== key,
      )
    },
    // =>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
  },
}
</script>
<style scoped>
a,
a:hover,
a:focus,
a:active {
  text-decoration: none;
  color: inherit;
}
</style>

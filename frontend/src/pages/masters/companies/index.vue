<template>
	<v-container fluid class="pa-0">
		<v-dialog v-model="columnDialog" width="1300" persistent>
			<CustomizeColumn @saveChanges="
				(data) => {
					all_headers[0] = data;
					all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
					columnDialog = false;
				}
			" :page_headers.sync="all_headers[0]" :page_name="'companies_all'" @closeColumnDialog="columnDialog = false" />
		</v-dialog>
		<!-- Company Bulk Upload -->
		<v-dialog v-model="companyBulkUpload" width="1300" v-if="$isInScope('cmc')">
			<Dialog persistent max-width="1300" @closeDialog="companyBulkUpload = false">
				<CompanyBulkUpload :countries="getCountries" :systems="getSystems" :tabKey="getTabKey()"
					v-if="companyBulkUpload" />
			</Dialog>
		</v-dialog>
		<!--    Register Company Section -->
		<v-dialog v-model="registerDialog" persistent width="1300" class="max-height-register-model" scrollable
			v-if="$isInScope('cmc')">
			<Dialog @closeDialog="closeRegisterDialog">
				<RegisterCompany :countries="getCountries" :systems="getSystems" ref="registerCompany" />
			</Dialog>
		</v-dialog>
		<ProgressBar v-if="showProgressBar" />
		<!-- Company Profile Section -->
		<v-dialog v-model="showProfile" persistent width="1300" scrollable v-if="$isInScope('cmv')">
			<CompanyProfile :key="viewKey" :dialog.sync="showProfile" @onEdit="editSelectedCompany"
				:profileData="profileData" />
		</v-dialog>
		<v-dialog v-model="autoEdit" persistent width="1300" scrollable max-height="800px" v-if="$isInScope('cmu')">
			<CompanyAutoEdit v-if="autoEdit" :autoEditData="autoEditData" :countries="getCountries"
				:systems="getSystems" />
		</v-dialog>

    <v-dialog
      v-model="companyEdit"
      @toggleProgressBar="toggleProgressBar"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('cmu')"
    >
      <CompanyEdit
        v-if="companyEdit"
        :autoEditData="editData"
        :selectedItems="selectedItems"
        :countries="getCountries"
        :systems="getSystems"
      />
    </v-dialog>
    <v-dialog
      v-model="companyFilter"
      persistent
      max-width="1300"
      width="1300"
      v-if="$isInScope('cmv')"
    >
      <CompanyFilter
        @close="companyFilter = false"
        :countries="getCountries"
        :systems="systemsForSelect"
        @getRecord="onFilterRecords"
      />
    </v-dialog>
    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />
      <v-col cols="12">
        <PageHeader
          :Icon="`companies`"
          Title="companies"
          :Breadcrumb="breadcrumb"
        />
      </v-col>

			<v-col cols="12">
				<PageFilters :selected_header.sync="all_headers[0].selected_headers" :downloadContent="companies"
					:downloadDialog="downloadDialog" :filename="'companies'"
					@onDownload="downloadDialog = !downloadDialog" @onFilter="onFilter"
					@onBulkUpload="companyBulkUpload = !companyBulkUpload" @onColumn="columnDialog = true"
					:show-bulk-upload="$isInScope('cmc')" :showDownload="$isInScope('cme')"
					@searchById="searchById" @clearAndUnselectId="clearAndUnselectId"
					@searchByContent="searchByContent" @onTypeChange="onSearchTypeChange">
					<CustomButton v-if="$isInScope('cmc')" icon="fa-plus" @click="registerDialog = true"
						:text="$tr('create_company')" type="primary" />
				</PageFilters>
			</v-col>
			<v-col cols="12">
				<PageActions :AssignInCompany="true" :showView="$isInScope('cmv')" :showEdit="$isInScope('cmu')"
					:showAutoEdit="$isInScope('cmu')" :showDelete="$isInScope('cmd')"
					:showBlock="$isInScope('cmu')" :showSelect="$isInScope('cmu')" :showApply="$isInScope('cmu')"
					:selectedItems.sync="selectedItems" :tab-key.sync="tabKey" @onView="onView"
					@onAutoEdit="onAutoEdit" @onEdit="onEdit" :routeName="'companies'" :subSystemName="'Companies'"
					@fetchNewData="selectItems = []" />
			</v-col>
			<v-col cols="12">
				<TableTabs @getSelectedTabRecords="tabKeyGetter" :tabData="tabItems" :extraData="extraData" />
				<DataTable ref="projectTableRef" :headers="all_headers[0].selected_headers" :items="companies"
					:ItemsLength="extraData.total" v-model="selectedItems" :loading="apiCalling"
					@onTablePaginate="onTableChanges" @click:row="onRowClicked">
					<template v-slot:[`item.code`]="{ item }">
						<span @click="() => viewSelectedCompany(item)"
							class="mx-1 primary--text font-weight-bold">
							{{ item.code }}
						</span>
					</template>
				</DataTable>
			</v-col>
		</v-row>
		<br />
		<br />
		<br />
		<br />
		<br />
	</v-container>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from "vuex";
import CompanyAutoEdit from "../../../components/companies/CompanyAutoEdit.vue";
import CustomButton from "../../../components/design/components/CustomButton.vue";
import DataTable from "../../../components/design/DataTable.vue";
import Dialog from "../../../components/design/Dialog/Dialog.vue";
import PageActions from "../../../components/design/PageActions.vue";
import PageFilters from "../../../components/design/PageFilters.vue";
import PageHeader from "../../../components/design/PageHeader.vue";
import CompanyEdit from "../../../components/companies/CompanyEdit.vue";
import CompanyFilter from "../../../components/companies/CompanyFilter.vue";
import CompanyProfile from "../../../components/masters/CompanyProfile.vue";
import RegisterCompany from "../../../components/companies/RegisterCompany.vue";
import Projects from "../../../configs/pages/projects";
import ProgressBar from "../../../components/common/ProgressBar.vue";
import CustomizeColumn from "../../../components/customizeColumn/CustomizeColumn2.vue";
import CompanyBulkUpload from "../../../components/companies/CompanyBulkUpload";
import TableTabs from "~/components/common/TableTabs.vue";
import HandleException from "../../../helpers/handle-exception";
import ChangeActiveStatus from "../../../components/companies/ChangeActiveStatus.vue";
export default {
	
	meta: {
		hasAuth: true,
		scope: "cmv",
	},

	components: {
    CompanyFilter,
    ProgressBar,
    PageActions,
    CustomButton,
    CompanyProfile,
    CompanyEdit,
    PageHeader,
    PageFilters,
    RegisterCompany,
    Dialog,
    DataTable,
    CompanyAutoEdit,
    CustomizeColumn,
    CompanyBulkUpload,
    TableTabs,
    ChangeActiveStatus
},

  data() {
    return {
      tabItems: Projects(this).tabItems,
      breadcrumb: Projects(this).breadcrumb,
      selectedItems: [],
      showProgress: false,
      tabIndex: 0,
      systemsForSelect: [],
      options: {},
      companyBulkUpload: false,
      //register section
      registerDialog: false,
      tabKey: "all",
      extraData: [],
      downloadDialog: false,

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

      viewKey: 0,

      showProgressBar: false,
      autoEdit: false,
      editKey: 0,
      autoEditData: [],
      selectedItems: [],
      searchId: "",
      searchContent: "",

      companyFilter: false,
      filterData: {},
      profileData: {},

      companyEdit: false,
      companyEditKey: 0,
      editData: {},
      bulkUploadDialog: false,
      tabChipKey: 0,
    };
  },

  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },
  async created() {
    await this.fetchHeaders();
    this.$root.$on("closeAutoEdit", this.toggleAutoEdit);
    this.$root.$on("closeEdit", this.toggleEdit);
    this.fetchAscCountries();
    await this.getRecord();
  },

  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      companies: "companies/getCompanies",
      itemsTotal: "companies/itemsTotal",
      apiCalling: "companies/isApiCalling",
      getTotals: "companies/getTotal",
      custom_columns: "customColumns/get_custom_columns",
      getCountries: "countries/getAscCountries",
      getSystems: "systems/items",
    }),
  },

  // async fetch() {
  // 	await this.getRecord();
  // },

  async mounted() {
    this.$echo.private(`update.company`).listen("Updated", async (e) => {
      if (e.action == "created") {
        let res = await this.fetchCompany(e.data);
        this.set_new_company({ new_company: res, tabKey: this.getTabKey() });
      } else if (e.action == "updated") {
        let res = await this.fetchCompany(e.data);
        this.update_company(res);
      } else if (e.action == "status-changed") {
        let res = await this.fetchCompany(e.data.item);
        this.change_record_status({
          data: e.data,
          item: res,
          tabKey: this.getTabKey(),
        });
        await this.fetchNewRealtime();
        this.tabChipKey++;
      } else if (e.action == "deleted") {
        this.delete_company({ tabKey: this.getTabKey(), item: e.data });
      }
    });
  },
  beforeDestroy() {
    this.$echo.leave("update.company");
  },
  methods: {
    ...mapMutations({
      set_new_company: "companies/SET_NEW_COMPANY",
      update_company: "companies/UPDATE_COMPANY",
      change_record_status: "companies/CHANGE_RECORD_STATUS",
      delete_company: "companies/DELETE_COMPANY",
      fetch_new_real_time: "companies/FETCH_NEW_REAL_TIME",
    }),
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
      fetchCompanies: "companies/fetchCompanies",
      changeRecordStatus: "projects/changeRecordStatus",
      fetchAscCountries: "countries/fetchAscCountries",
      deleteItem: "companies/deleteItem",
      insertNewItem: "companies/insertNewItem",
    }),

    tabKeyGetter(key) {
      this.tabKey = key;
      this.selectedItems = [];
      this.getRecord();
    },

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "companies_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    async fetchCompany(id) {
      try {
        const response = await this.$axios.get(`companies/${id}`, {
          params: {},
        });
        if (response?.status === 200) {
          return response?.data?.data;
        }
      } catch (err) {}
    },
    async fetchNewRealtime() {
      const data = {
        key: this.getTabKey(),
        options: {
          itemsPerPage: 1,
          page: this.companies.length + 1,
        },
      };
      const response = await this.$axios.get(`companies?key=${data.key}`, {
        params: data.options,
      });
      this.fetch_new_real_time(response);
      this.selectedItems = [];
    },

    toggleProgressBar() {
      this.showProgressBar = !this.showProgressBar;
    },

    closeRegisterDialog() {
      this.registerDialog = false;
      this.$refs.registerCompany.closeRegisterDialog();
    },
    checkSelectedTab(value) {
      this.selectedItems = [];
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    getTabKey() {
      return this.tabItems[this.tabIndex].key;
    },

    onView() {
      if (this.selectedItems.length == 1) {
        this.profileData = this.selectedItems[0];
        this.showProfile = true;
      } else {
        //this.$toastr.w(this.$tr("view_item_max_limit_text"));
        this.$toasterNA("orange", this.$tr("view_item_max_limit_text"));
      }
    },

    viewSelectedCompany(data) {
      data.status_transformations = this.$sortStatusTransformationData(data);
      this.profileData = data;
      this.showProfile = !this.showProfile;
      this.viewKey++;
    },

    editSelectedCompany(item) {
      this.showProfile = false;
      this.companyEditKey++;
      this.editData = [item];
      this.companyEdit = !this.companyEdit;
    },

    //customize columns: called from child

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    onAutoEdit() {
      this.editKey++;
      this.autoEditData = this.selectedItems;
      this.autoEdit = !this.autoEdit;
    },
    toggleAutoEdit() {
      // this.editKey++;
      this.autoEdit = !this.autoEdit;
      this.selectedItems = [];
    },
    onEdit() {
      this.companyEditKey++;
      this.editData = this.selectedItems;
      this.companyEdit = !this.companyEdit;
    },
    toggleEdit() {
      this.companyEditKey++;
      this.companyEdit = !this.companyEdit;
      this.selectedItems = [];
    },

    // new search and filter functions
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.getRecord();
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

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;

      this.getRecord();
    },

    onFilter() {
      this.getSystemsForSelect();
      this.companyFilter = true;
    },

    async getSystemsForSelect() {
      try {
        const response = await this.$axios.get("systems-for-select");
        this.systemsForSelect = response?.data?.data;
      } catch (error) {
        return null;
      }
    },

    fetchOptions() {
      let data = {
        key: this.tabKey,
      };

      if (!this.$isObjectEmpty(this.filterData))
        Object.assign(data, this.filterData);

      if (!this.$isObjectEmpty(this.options)) Object.assign(data, this.options);

      if (this.searchContent != "") data.searchContent = this.searchContent;

      if (this.searchId != "") data.code = this.searchId;
      return data;
    },

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post(
          "companies/searchCompany",
          options
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
          this.deleteItem(data.id);
          this.insertNewItem(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchCompanies(data);
        this.extraData = data2.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
  },
};
</script>

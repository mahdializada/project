<template>
  <v-container fluid class="pa-0">
    <v-dialog v-model="columnDialog" width="1300" persistent>
      <CustomizeColumn @saveChanges="
        (data) => {
          all_headers[0] = data;
          all_headers = JSON.parse(JSON.stringify({ ...all_headers }));
          columnDialog = false;
        }
      " :page_headers.sync="all_headers[0]" :page_name="'departments_all'"
        @closeColumnDialog="columnDialog = false" />
    </v-dialog>
    <v-dialog v-model="departmentFilter" persistent max-width="1300" width="1300" v-if="$isInScope('dpv')">
      <DepartmentFilter @close="departmentFilter = false" @getRecord="onFilterRecords" />
    </v-dialog>

    <v-dialog v-model="departmentRegisterDialog" width="1300" v-if="$isInScope('dpc')">
      <Dialog @closeDialog="departmentRegisterDialog = false">
        <Create @onToggleShowProgress="toggleShowProgress" @onCloseDialog="toggleDepartmentRegisterDialog"
          v-if="departmentRegisterDialog" :tabKey="getTabKey()" :options="options" />
      </Dialog>
    </v-dialog>
    <!-- Department Bulk Upload -->
    <v-dialog v-model="departmentBulkUpload" width="1300" v-if="$isInScope('dpc')">
      <Dialog persistent max-width="1300" @closeDialog="departmentBulkUpload = false">
        <DepartmentBulkUpload v-if="departmentBulkUpload" :tabKey="getTabKey()" />
      </Dialog>
    </v-dialog>

    <v-dialog v-model="departmentEdit" persistent width="1300" scrollable max-height="800px" :key="departmentEditKey"
      v-if="$isInScope('dpu')">
      <DepartmentEdit v-if="departmentEdit" :autoEditData.sync="editData" @onCloseDialog="departmentEdit = false"
        :tabKey="getTabKey()" :options="options" />
    </v-dialog>
    <v-dialog v-model="autoEdit" persistent width="1300" scrollable max-height="800px" v-if="$isInScope('dpu')">
      <DepartmentEdit v-if="autoEdit" :autoEditData.sync="editData" @onCloseDialog="autoEdit = false"
        :tabKey="getTabKey()" :options="options" isAutoEdit />
    </v-dialog>
    <v-dialog persistent v-model="showProfile" width="1300" class="rounded-0 custom-v-dialog" v-if="$isInScope('dpv')">
      <DepartmentProfile :key="viewKey" style="overflow-y: hidden" :profileData="profileData" :dialog.sync="showProfile"
        @onEdit="editSelectedDepartment" @toggleShowProgress="toggleShowProgress" />
    </v-dialog>
    <!--    Display Register Dialog -->

    <v-row no-gutters>
      <ProgressBar v-if="showProgress" />
      <v-col cols="12">
        <PageHeader :Icon="`departments`" Title="departments" :Breadcrumb="breadcrumb" />
      </v-col>
      <v-col cols="12">
        <PageFilters :selected_header.sync="all_headers[0].selected_headers" :downloadContent="getDepartments"
          :downloadDialog="downloadDialog" :filename="$tr('departments')" @onDownload="toggleDownload"
          @onBulkUpload="onDepartmentBulkUpload" ref="pageFilterRef" :show-bulk-upload="$isInScope('dpc')"
          :showDownload="$isInScope('dpe')" note-title="add_department_note" @onColumn="columnDialog = true"
          @onFilter="departmentFilter = true" @searchById="searchById" @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent" @onTypeChange="onSearchTypeChange">
          <CustomButton @click="toggleDepartmentRegisterDialog" icon="fa-user-plus"
            :text="$tr('create_item', $tr('department'))" type="primary" v-if="$isInScope('dpc')" />
        </PageFilters>
      </v-col>
      <!-- Department Profile -->

      <v-col cols="12">
        <!-- :selectedItems="selectedItems" -->
        <PageActions :routeName="'departments'" :subSystemName="'Departments'" @onView="onView"
          @onAutoEdit="toggleAutoEdit" @onEdit="toggleEdit" :selectedItems.sync="selectedItems" :tab-key.sync="tabKey"
          :showView="$isInScope('dpv')" :showEdit="$isInScope('dpu')" :showAutoEdit="$isInScope('dpu')"
          :showDelete="$isInScope('dpd')" :showBlock="$isInScope('dpu')" :showSelect="$isInScope('dpu')"
          :showApply="$isInScope('dpu')" @fetchNewData="selectedItems = []" />
      </v-col>
      <v-col cols="12">
        <!-- <v-row class="mx-0">
					<v-col cols="12" class="pa-0">
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
				</v-row> -->

        <TableTabs @getSelectedTabRecords="tabKeyGetter" :tabData="tabItems" :extraData="extraData" />

        <DataTable v-model="selectedItems" :key="tableKey" :headers="all_headers[0].selected_headers"
          :loading="apiCalling" @onTablePaginate="onTableChanges" ref="departmentTableRef" :items="getDepartments"
          :ItemsLength="extraData.total" @click:row="onRowClicked" :defaultCountry="false">
          <template v-slot:[`item.created_by`]="{ item }">
            {{
            item.created_by
            ? item.created_by.firstname + " " + item.created_by.lastname
            : ""
            }}
          </template>
          <template v-slot:[`item.updated_by`]="{ item }">
            {{
            item.updated_by
            ? item.updated_by.firstname + " " + item.updated_by.lastname
            : ""
            }}
          </template>
          <template v-slot:[`item.industry`]="{ item }">
            {{ item.industry ? item.industry.name : "" }}
          </template>
          <template v-slot:[`item.code`]="{ item }">
            <span @click="() => viewSelectedDepartment(item)" class="mx-1 primary--text font-weight-bold">
              {{ item.code }}
            </span>
          </template>
          <template v-slot:[`item.business_location`]="{ item }">
            <span>{{ item.business_location.name }}</span>
          </template>
          <template v-slot:[`item.note`]="{ item }">
            <v-tooltip bottom>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on" style="white-space: nowrap">
                  {{ getSubString(item.note) }}
                </span>
              </template>
              <span>{{ item.note }}</span>
            </v-tooltip>
          </template>
          <template v-slot:[`item.industries`]="{ item }">
            <div>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span v-bind="attrs" v-on="on" style="white-space: nowrap" class="primary--text">
                    {{ $tr("industries") }}
                  </span>
                </template>
                <div v-if="2 > 1">
             
                
                  <div v-if="item.industries.length > 0">
                    <p v-for="industry in item.industries" :key="industry.index" class="pb-0 mb-0">
                      {{ industry.name }}
                    </p>
                  </div>
                  <div v-else>
                    <p class="pb-0 mb-0">
                      {{ $tr("no_item", $tr("industries")) }}
                    </p>
                  </div>
                </div>
              </v-tooltip>
            </div>
          </template>
          <template v-slot:[`item.countries`]="{ item }">
            <div>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span v-bind="attrs" v-on="on" style="white-space: nowrap" class="primary--text">
                    {{ $tr("countries") }}
                  </span>
                </template>
                <div v-if="item.companies.map((c) => c.countries)[0]">
                  <div v-if="item.companies.map((c) => c.countries)[0].length > 0">
                    <p v-for="country in item.companies.map(
                      (c) => c.countries
                    )[0]" :key="country.index" class="pb-0 mb-0">
                      {{ country.name }}
                    </p>
                  </div>
                  <div v-else>
                    <p class="pb-0 mb-0">{{ $tr("no_country") }}</p>
                  </div>
                </div>
              </v-tooltip>
            </div>
          </template>
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from "vuex";
import Departments from "../../../configs/pages/departments";
import ProgressBar from "../../../components/common/ProgressBar";
import PageHeader from "../../../components/design/PageHeader";
import PageFilters from "../../../components/design/PageFilters";
import PageActions from "../../../components/design/PageActions";
import DataTable from "../../../components/design/DataTable";
import CustomButton from "../../../components/design/components/CustomButton";
import Create from "../../../components/masters/department/Create";
import DepartmentFilter from "../../../components/masters/department/DepartmentFilter";
import Dialog from "../../../components/design/Dialog/Dialog.vue";
import CustomizeColumn from "../../../components/customizeColumn/CustomizeColumn2.vue";
import DepartmentEdit from "../../../components/masters/department/DepartmentEdit";
import DepartmentProfile from "../../../components/masters/department/DepartmentProfile.vue";
import DepartmentBulkUpload from "../../../components/masters/department/DepartmentBulkUpload";
import TableTabs from "~/components/common/TableTabs.vue";
import HandleException from "~/helpers/handle-exception";
export default {
  meta: {
    hasAuth: true,
    scope: "dpv",
  },

  async created() {
    await this.fetchHeaders();
    await this.getRecord();
  },
  async mounted() {
    this.$echo.private(`update.department`).listen("Updated", async (e) => {
      if (e.action == "created") {
        let res = await this.fetchNewDataRealTime(e.data);
        this.set_new_department({
          new_department: res,
          tabKey: this.getTabKey(),
        });
      } else if (e.action == "updated") {
        let res = await this.fetchNewDataRealTime(e.data);
        this.update_department(res);
      } else if (e.action == "status-changed") {
        let res = await this.fetchNewDataRealTime(e.data.item);
        this.change_record_status({
          data: e.data,
          item: res,
          tabKey: this.getTabKey(),
        });
        await this.fetchNewRealtime();
        this.tabChipKey++;
      } else if (e.action == "deleted") {
        this.delete_department({ tabKey: this.getTabKey(), id: e.data });
      }
    });
  },
  beforeDestroy() {
    this.$echo.leave("update.department");
  },

  name: "departments",
  components: {
    DepartmentProfile,
    ProgressBar,
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    DepartmentFilter,
    Create,
    Dialog,
    CustomizeColumn,
    DepartmentEdit,
    DepartmentBulkUpload,
    TableTabs,
  },
  data() {
    return {
      breadcrumb: Departments(this).breadcrumb,
      tabItems: Departments(this).tabItems,
      selectedItems: [],
      showProgress: false,
      tableKey: 0,
      tabIndex: 0,
      tabKey: "all",
      extraData: [],
      departmentRegisterDialog: false,
      departmentFilter: false,
      // Page actions
      downloadDialog: false,

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

      showProgressBar: false,

      autoEdit: false,
      editKey: 0,
      autoEditData: [],
      departmentEdit: false,
      departmentEditKey: 0,
      editData: {},
      profileData: null,
      showProfile: false,
      viewKey: 0,
      departmentBulkUpload: false,
      tabChipKey: 0,
      // new search varibles

      // Filter
      options: {},
      filterData: {},
      searchId: "",
      searchContent: "",
    };
  },
  computed: {
    ...mapGetters({
      getDepartments: "departments/getDepartments",
      itemsTotal: "departments/itemsTotal",
      apiCalling: "departments/isApiCalling",
      getTotals: "departments/getTotal",
    }),
  },
  methods: {
    ...mapActions({
      fetchDepartments: "departments/fetchDepartments",
      removeDepartment: "departments/deleteItem",
      insertDepartemnt: "departments/insertNewItem",
    }),

    ...mapMutations({
      set_new_department: "departments/SET_NEW_DEPARTMENT",
      update_department: "departments/UPDATE_DEPARTMENT",
      change_record_status: "departments/CHANGE_RECORD_STATUS",
      delete_department: "departments/DELETE_DEPARTMENT",
      fetch_new_real_time: "departments/FETCH_NEW_REAL_TIME",
    }),

    tabKeyGetter(key) {
      this.tabKey = key;
      this.getRecord();
    },

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "departments_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) { }
    },

    async fetchNewDataRealTime(id) {
      try {
        const response = await this.$axios.get(`departments/${id}`, {
          params: {},
        });
        return response?.data?.data;
      } catch (err) {
        return err;
      }
    },
    async fetchNewRealtime() {
      const data = {
        key: this.getTabKey(),
        options: {
          itemsPerPage: 1,
          page: this.getDepartments.length + 1,
        },
      };
      const response = await this.$axios.get(`departments?key=${data.key}`, {
        params: data.options,
      });
      this.fetch_new_real_time(response);
      this.selectedItems = [];
    },

    onDepartmentBulkUpload() {
      this.departmentBulkUpload = !this.departmentBulkUpload;
    },

    getSubString(value, length = 20) {
      let subText = value?.substring(0, length);
      if (value?.length > length) {
        subText += "...";
      }
      return subText;
    },

    viewSelectedDepartment(data) {
      data.status_transformations = this.$sortStatusTransformationData(data);
      this.profileData = data;
      this.showProfile = !this.showProfile;
      this.viewKey++;
    },

    // 	***********************			No changes required		************************
    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
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

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.removeDepartment(data.id);
          this.insertDepartemnt(data);
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

    onFilterRecords(filterData) {
      this.$root.$emit("resetPageNumber");
      this.filterData = filterData;
      this.getRecord();
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

    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },
    // 	***********************			No changes required		************************

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        this.$store.commit("departments/TOGGLE_API_CALLING", true);
        const response = await this.$axios.post(
          "departments/search-department",
          options
        );
        this.$store.commit("departments/TOGGLE_API_CALLING", false);
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchDepartments(data);
        this.extraData = data2.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    // fired when on view button clicked
    onView() {
      this.viewSelectedDepartment(this.selectedItems[0]);
    },

    checkSelectedTab(value) {
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    // register department dialog toggle
    toggleDepartmentRegisterDialog() {
      this.departmentRegisterDialog = !this.departmentRegisterDialog;
    },

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    toggleDownload() {
      this.downloadDialog = !this.downloadDialog;
    },

    toggleShowProgress() {
      this.showProgress = !this.showProgress;
    },
    editSelectedDepartment(item) {
      this.showProfile = false;
      this.departmentEditKey++;
      this.editData = [item];
      this.departmentEdit = !this.departmentEdit;
    },

    toggleEdit() {
      this.editData = this.selectedItems;
      this.departmentEditKey++;
      this.departmentEdit = !this.departmentEdit;
      this.selectedItems = [];
    },

    toggleAutoEdit() {
      this.editData = this.selectedItems;
      this.editKey++;
      this.autoEdit = !this.autoEdit;
      this.selectedItems = [];
    },
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },
};
</script>

<style scoped>
.custom-v-dialog {
  overflow-y: visible;
}
</style>

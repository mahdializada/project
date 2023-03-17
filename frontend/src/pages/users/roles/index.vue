<template>
  <v-container fluid class="pa-0">
    <ProgressBar v-if="showProgressBar" />

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
        :page_name="'roles_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <v-dialog v-model="roleFilter" persistent max-width="1300" width="1300">
      <RolesFilter
        @close="roleFilter = false"
        :departments="getDepartments"
        @filterRecords="onFilterRecords"
      />
    </v-dialog>
    <v-dialog v-model="showProfile" persistent width="1300" scrollable>
      <RoleProfile
        @onEdit="onEdit"
        :profileData="profileData"
        :key="profileKey"
        :dialog.sync="showProfile"
        v-if="$isInScope('rv')"
      />
    </v-dialog>
    <v-dialog
      v-model="autoEditDialog"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('ru')"
    >
      <TeamAutoEdit
        role
        :autoEditData="getRoleWithMetaData"
        @close="autoEditDialog = false"
        @toggleProgressBar="toggleProgressBar"
        v-if="autoEditDialog"
      />
    </v-dialog>

    <!-- Team / Role Bulk Upload -->
    <v-dialog v-model="teamRoleBulkUpload" width="1300" v-if="$isInScope('rc')">
      <Dialog
        persistent
        max-width="1300"
        @closeDialog="teamRoleBulkUpload = false"
      >
        <TeamRoleBulkUpload
          :tabKey="getTabKey()"
          :team_or_role="'role'"
          v-if="teamRoleBulkUpload"
        />
      </Dialog>
    </v-dialog>
    <v-dialog
      v-model="editDialog"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('ru')"
    >
      <TeamEdit
        role
        ref="edit_dialog"
        @toggleProgressBar="toggleProgressBar"
        :editData="getRoleWithMetaData[0]"
        @close="editDialog = false"
        v-if="editDialog"
      />
    </v-dialog>
    <v-dialog
      v-model="registerDialog"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('rc')"
    >
      <Dialog @closeDialog="toggleTeamRoleRegistration">
        <TeamRoleRegistration
          ref="team_role_registration"
          role
          @close="toggleTeamRoleRegistration"
          @toggleProgressBar="toggleProgressBar"
          :tabKey="tabItems[tabIndex].key"
        />
      </Dialog>
    </v-dialog>
    <v-row no-gutters>
      <v-col cols="12">
        <PageHeader
          :Icon="`role_list`"
          Title="role_list"
          :Breadcrumb="breadcrumb"
        />
      </v-col>
      <v-col cols="12">
        <PageFilters
          ref="pageTableRef"
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="roles"
          :downloadDialog="downloadDialog"
          :filename="$tr('role_list')"
          @onDownload="downloadDialog = !downloadDialog"
          @onFilter="roleFilter = true"
          @searchById="searchById"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
          @onColumn="columnDialog = true"
          :show-bulk-upload="$isInScope('rc')"
          :showDownload="$isInScope('re')"
          @onBulkUpload="teamRoleBulkUpload = !teamRoleBulkUpload"
        >
          <CustomButton
            @click="registerDialog = true"
            icon="fa-plus"
            text="create_role"
            type="primary"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :showView="$isInScope('rv')"
          :showEdit="$isInScope('ru')"
          :showAutoEdit="$isInScope('ru')"
          :showBlock="$isInScope('ru')"
          :showDelete="$isInScope('rd')"
          :showSelect="$isInScope('ru')"
          :showApply="$isInScope('ru')"
          :tabKey="tabKey"
          :selectedItems.sync="selectedItems"
          @onAutoEdit="onAutoEdit"
          @onView="viewSelectedRole"
          @onEdit="onEdit"
          :routeName="'roles'"
          :subSystemName="'Roles'"
          @fetchNewData="selectedItems = []"
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
          ref="tableRef"
          v-model="selectedItems"
          :headers="all_headers[0].selected_headers"
          :items="roles"
          :loading="apiCalling"
          @viewSelectedItem="viewSelectedRole"
          @click:row="onRowClicked"
          :ItemsLength="getTotal(getTabKey())"
          @onTablePaginate="onTableChanges"
        >
        </DataTable>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from "vuex";
import ProgressBar from "~/components/common/ProgressBar";
import PageHeader from "~/components/design/PageHeader";
import PageFilters from "~/components/design/PageFilters";
import PageActions from "~/components/design/PageActions";
import DataTable from "~/components/design/DataTable";
import Roles from "../../../configs/pages/roles";
import CustomButton from "~/components/design/components/CustomButton";
import TeamRoleRegistration from "~/components/users/team/TeamRoleRegistration.vue";
import RoleProfile from "~/components/users/roles/RoleProfile.vue";
import Dialog from "~/components/design/Dialog/Dialog";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import TeamAutoEdit from "~/components/users/team/TeamAutoEdit.vue";
import TeamEdit from "~/components/users/team/TeamEdit.vue";
import HandleException from "~/helpers/handle-exception";
import Alert from "~/helpers/alert";
import RolesFilter from "~/components/users/roles/RolesFilter.vue";
import TeamRoleBulkUpload from "~/components/users/TeamRoleBulkUpload";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "rv",
  },
  name: "Role",
  components: {
    RoleProfile,
    Dialog,
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    ProgressBar,
    TeamRoleRegistration,
    CustomizeColumn,
    TeamEdit,
    RolesFilter,
    TeamAutoEdit,
    TeamRoleBulkUpload,
    TableTabs,
  },

  data() {
    return {
      tabItems: Roles(this).tabItems,

      breadcrumb: Roles(this).breadcrumb,
      registerDialog: false,
      tabIndex: 0,

      // Profile
      showProfile: false,
      profileKey: 0,
      profileData: {},

      roleData: {},
      downloadDialog: false,

      selectedItems: [],

      // auto edit
      editTeamItems: [],
      isAutoEdit: false,
      editDialog: false,
      autoEditDialog: false,
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

      rolesEdit: false,
      rolesEditKey: 0,
      editData: {},
      roleFilter: false,
      searchContent: "",
      searchId: "",
      filterData: {},
      options: {},
      teamRoleBulkUpload: false,
      tabChipKey: 0,

      tabKey: "all",
      extraData: [],
    };
  },
  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      apiCalling: "roles/isApiCalling",
      roles: "roles/getItems",
      getTotal: "roles/getTotal",
      getDepartments: "departments/getDepartments",
      getRoleWithMetaData: "filterData/getrolesWithMetaData",
    }),
  },
  watch: {
    tabIndex: function (value) {
      this.selectedItems = [];
      this.checkSelectedTab(value);
      this.getRecord();
    },
  },

  async created() {
    await this.fetchHeaders();

    this.getRecord();
    //customize columns

    if (process.client) {
      this.last_header_update = this._.clone(this.selected_header);
    }
    await this.fetchDepartments({
      key: "all",
    });
  },
  async mounted() {
    this.$echo.private(`update.role`).listen("Updated", async (e) => {
      if (e.action == "created") {
        let role = await this.getRole(e.data.role_id);
        this.set_new_role({ new_role: role, tabKey: this.getTabKey() });
      } else if (e.action == "updated") {
        let role = await this.getRole(e.data.role_id);
        this.update_role(role);
      } else if (e.action == "status-changed") {
        let role = await this.getRole(e.data.item);
        this.change_record_status({
          data: e.data,
          item: role,
          tabKey: this.tabKey,
        });
        await this.fetchNewRealtime();
        this.tabChipKey++;
      } else if (e.action == "deleted") {
        this.delete_ROLE({ tabKey: this.getTabKey(), item: e.data });
      }
    });
  },
  beforeDestroy() {
    this.$echo.leave("update.role");
  },
  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
      fetchRoleMetaData: "filterData/fetchRoleMetaDataForEdit",
      fetchcountries: "countries/fetchCountriesWithCompanies",

      removeRole: "roles/removeItem",
      insertRole: "roles/insertNewItem",
      fetchItems: "roles/fetchItems",
      fetchDepartments: "departments/fetchDepartments",
    }),
    ...mapMutations({
      set_new_role: "roles/SET_NEW_ROLE",
      update_role: "roles/UPDATE_ROLE",
      change_record_status: "roles/CHANGE_RECORD_STATUS",
      delete_ROLE: "roles/DELETE_ROLE",
      fetch_new_real_time: "roles/FETCH_NEW_REAL_TIME",
    }),

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "roles_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    async getRole(id) {
      try {
        const response = await this.$axios.get(`roles/${id}`);
        if (response?.status === 200) {
          return response?.data?.data;
        }
      } catch (error) {
        return error;
      }
    },
    async fetchNewRealtime() {
      const data = {
        key: this.tabKey,
        options: {
          itemsPerPage: 1,
          page: this.roles.length + 1,
        },
      };
      const response = await this.$axios.get(`roles?key=${data.key}`, {
        params: data.options,
      });
      this.fetch_new_real_time(response);
      this.selectedItems = [];
    },

    viewSelectedRole(item) {
      const data = item || this.selectedItems[0];
      data.status_transformations = this.$sortStatusTransformationData(data);
      this.profileData = data;
      this.showProfile = true;
    },

    onTableChanges(options) {
      if (JSON.stringify(options) !== JSON.stringify(this.options)) {
        this.options = this._.clone(options);
        this.getRecord();
      } else this.options = this._.clone(options);
    },

    checkSelectedTab(value) {
      this.selectedItems = [];
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },
    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post("roles/searchRole", options); // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },
    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchItems(data); // Change request url
        this.extraData = data2.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    // customize columns: end=>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
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

    // get tab key
    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
    },

    async onEdit() {
      if (this.selectedItems.length > 1) {
        // this.$toastr.w(this.$tr("edit_item_max_limit_text"));
				this.$toasterNA("orange",this.$tr('edit_item_max_limit_text'));

      } else {
        this.toggleProgressBar();
        await this.fetchRoleMetaData([this.selectedItems[0].id]);
        await this.fetchcountries();
        this.editDialog = !this.editDialog;
        this.toggleProgressBar();
      }
    },

    async onAutoEdit() {
      this.toggleProgressBar();
      await this.fetchRoleMetaData(this.selectedItems.map((item) => item.id));
      await this.fetchcountries();
      this.autoEditDialog = !this.autoEditDialog;
      this.toggleProgressBar();
    },

    toggleProgressBar() {
      this.showProgressBar = !this.showProgressBar;
    },
    toggleTeamRoleRegistration() {
      this.registerDialog = !this.registerDialog;
      this.$refs.team_role_registration.resetAll();
    },
    // =>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    onSearchTypeChange() {
      this.selectedItems = [];
      this.searchContent = "";
      this.searchId = "";
    },
    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.removeRole(data.id);
          this.insertRole(data);
          this.selectedItems?.unshift(data);
        }
      } else {
        this.$root.$emit("removeSearchId", this.searchId);
      }
    },

    searchByContent(searchContent) {
      this.searchContent = searchContent;
      this.getRecord();
    },
    clearAndUnselectId(code) {
      this.selectedItems = this.selectedItems.filter(
        (row) => row.code !== code
      );
    },
  },
};
</script>

<style scoped></style>

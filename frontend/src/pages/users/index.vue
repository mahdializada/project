f<template>
  <v-container fluid class="pa-0">
    <ProgressBar v-if="showProgressBar" />
    <!--    Register User Dialog-->
    <v-dialog
      v-model="userRegisterDialog"
      width="1300"
      persistent
      v-if="$isInScope('uc')"
    >
      <Dialog @closeDialog="closeRegisterDialog">
        <RegisterUser
          :tableKey="tabItems[tabIndex].key"
          ref="registerUserDialog"
        />
      </Dialog>
    </v-dialog>

    <!--    Register User Dialog-->
    <v-dialog v-model="permissionDialog" width="1300" persistent>
      <Dialog @closeDialog="togglePermissionsDialog">
        <StaticPermission
          :key="permissionKey"
          :scheduleTypeProps="permissionUser.schedule_type"
          :selectedSubSystemsProps="permissionUser.selectedSubSystems"
          :selectedActionsProps="permissionUser.selectedSystems"
          :selectedCompanySystemsProps="permissionUser.companiesSystems"
          :allCompaniesProps="permissionUser.selectedCompanies"
          :selectedTeamsProps="permissionUser.selectedTeams"
          :selectedRolesProps="permissionUser.selectedRoles"
          :selectedCompaniesProps="permissionUser.selectedCompanies"
          :allTeamsProps="permissionUser.companyDepartmentsTeams"
          :timeRangeProps="permissionUser.time_range"
          :dateRangeProps="permissionUser.date_range"
          :actualTimeRangeProps="permissionUser.actualTimeRange"
          :actualDateRangeProps="permissionUser.actualDateRange"
          :isSubmitting="isUpdatingPermissions"
          showButtons
          showTabs
          @submit="updateUserPermisions"
          @onClose="togglePermissionsDialog"
        />
      </Dialog>
    </v-dialog>

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
        :page_name="'users_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <v-dialog
      v-model="filterDialog"
      persistent
      max-width="1300"
      width="1300"
      v-if="$isInScope('uv')"
    >
      <UserFilter @close="filterDialog = false" @getRecord="onFilterRecords" />
    </v-dialog>
    <v-dialog
      v-model="showProfile"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('uv')"
    >
      <UserProfile
        @editPermissions="onEdit"
        :user="userData"
        :dialog.sync="showProfile"
        :key="viewKey"
      />
    </v-dialog>
    <!-- User Bulk Upload -->
    <v-dialog v-model="userBulkUpload" width="1300" v-if="$isInScope('uc')">
      <Dialog persistent max-width="1300" @closeDialog="userBulkUpload = false">
        <UserBulkUpload v-if="userBulkUpload" :tabKey="getTabKey()" />
      </Dialog>
    </v-dialog>
    <!--  User Edit Section    -->

    <v-dialog
      v-model="editDialog"
      persistent
      width="1300"
      v-if="$isInScope('uu')"
    >
      <UserEdit
        :tabKey="tabItems[tabIndex].key"
        :edit-dialog.sync="editDialog"
        :key="editUserKey"
        :editItems="editUserItems"
        :is-auto-edit.sync="isAutoEdit"
      />
    </v-dialog>

    <v-row no-gutters>
      <v-col cols="12">
        <PageHeader
          :Icon="`user_list`"
          Title="user_list"
          :Breadcrumb="breadcrumb"
        >
          <template slot="button">
            <CustomButton
              v-if="$isInScope(['uv', 'rv'], true)"
              :icon="
                this.onlineVisible
                  ? 'mdi-chevron-triple-up'
                  : 'mdi-chevron-triple-down'
              "
              class="toggle-online-user"
              text=""
              @click="() => toggleVisibleOnlineUsers()"
            />
          </template>
          <template slot="content" v-if="$isInScope(['uv', 'rv'], true)">
            <UserAnalytics2 :visible.sync="onlineVisible" ref="userAnalytic" />
          </template>
        </PageHeader>
      </v-col>

      <v-col cols="12">
        <PageFilters
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="getItems"
          :downloadDialog="downloadDialog"
          :filename="$tr('users')"
          :show-bulk-upload="$isInScope('uc')"
          :showDownload="$isInScope('ue')"
          @onDownload="downloadDialog = !downloadDialog"
          @onBulkUpload="userBulkUpload = !userBulkUpload"
          @onColumn="columnDialog = true"
          @searchById="searchById"
          @onFilter="filterDialog = true"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
          <CustomButton
            v-if="$isInScope('uc')"
            @click="toggleUserRegisterDialog"
            icon="fa-user-plus"
            :text="$tr('create_item', $tr('user'))"
            type="primary"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :showTracing="$isInScope('uu')"
          :showView="$isInScope('uv')"
          :showEdit="$isInScope('uu')"
          :showAutoEdit="$isInScope('uu')"
          :showDelete="$isInScope('ud')"
          :showBlock="$isInScope('uu')"
          :showSelect="$isInScope('uu')"
          :showApply="$isInScope('uu')"
          :selectedItems="selectedItems"
          :tab-key.sync="tabKey"
          @onView="onView"
          @onAutoEdit="onAutoEdit"
          @onEdit="onEdit"
          :routeName="'users'"
          :subSystemName="'Users'"
          @fetchNewData="selectedItems = []"
          @askToAddPermissions="askToAddPermissions"
        />
      </v-col>
      <v-col cols="12">
        <TableTabs
          @getSelectedTabRecords="
            (key) => {
              tabKey = key;
              selectedItems = []
              getRecord();
            }
          "
          :tabData="tabItems"
          :extraData="extraData"
        />

        <DataTable
          ref="tableRef"
          :headers="all_headers[0].selected_headers"
          :items="getItems"
          :ItemsLength="getTotals(tabItems[tabIndex].key)"
          v-model="selectedItems"
          :loading="apiCalling"
          @onTablePaginate="onTableChanges"
          @click:row="onRowClicked"
        >
          <template v-slot:[`item.status`]="{ item }">
            <span v-if="item.status === 'inactive'">{{ $tr("deactive") }}</span>
            <span v-else>{{ $tr(item.status) }}</span>
          </template>

          <template v-slot:[`item.tracing_status`]="{ item }">
            <span>
              {{ item.tracing_status ? $tr("yes") : $tr("no") }}
            </span>
          </template>

          <template v-slot:[`item.teams`]="{ item }">
            <span
              v-for="(team, index) in item.teams"
              :key="team.id"
              v-if="index < 3"
            >
              {{ `${team.name} ${index === 0 ? "" : index < 3 ? "," : ""}` }}
            </span>
            <span v-else-if="index === 2"> ... </span>
          </template>

          <template v-slot:[`item.projects`]="{ item }">
            <span
              v-for="(project, index) in item.projects"
              :key="project.id"
              v-if="index < 3"
            >
              {{ `${project.name} ${index === 0 ? "" : index < 3 ? "," : ""}` }}
            </span>
            <span v-else-if="index === 2"> ... </span>
          </template>

          <template v-slot:[`item.roles`]="{ item }">
            <span
              style="white-space: nowrap"
              v-for="(role, index) in item.roles"
              :key="role.id"
              v-if="index < 3"
            >
              {{ `${role.name} ${index === 0 ? "" : index < 3 ? "," : ""}` }}
            </span>
            <span v-else-if="index === 2"> ... </span>
          </template>

          <template v-slot:[`item.code`]="{ item }">
            <span
              @click="() => viewSelectedUser(item.id)"
              class="mx-1 primary--text font-weight-bold"
            >
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
import PageHeader from "../../components/design/PageHeader";
import PageFilters from "../../components/design/PageFilters";
import PageActions from "../../components/design/PageActions";
import UserAnalytics2 from "../../components/users/UserAnalytics";
import DataTable from "../../components/design/DataTable";
import HandleException from "~/helpers/handle-exception";
import Alert from "../../helpers/alert";
import Helper from "../../helpers/helpers";
import ProgressBar from "../../components/common/ProgressBar";
import UserFilter from "../../components/users/UserFilter.vue";
import CustomButton from "../../components/design/components/CustomButton";
import Dialog from "../../components/design/Dialog/Dialog.vue";
import UserProfile from "../../components/users/UserProfile";
import StaticPermission from "../../components/users/StaticPermission";
import RegisterUser from "../../components/users/RegisterUser.vue";
import UserEdit from "../../components/users/UserEdit.vue";
import CustomizeColumn from "../../components/customizeColumn/CustomizeColumn2.vue";
import DownloadPopUp from "../../components/common/DownloadPopUp.vue";
import Users from "../../configs/pages/users";
import UserBulkUpload from "../../components/users/UserBulkUpload";
import ColumnHelper from "~/helpers/column-helper";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "uv",
  },
  components: {
    UserProfile,
    RegisterUser,
    CustomButton,
    ProgressBar,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    UserAnalytics2,
    UserFilter,
    Dialog,
    CustomizeColumn,
    DownloadPopUp,
    UserEdit,
    StaticPermission,
    UserBulkUpload,
    TableTabs,
  },
  async created() {
    await this.fetchHeaders();
  },
  async mounted() {
    this.$echo.private(`update.user`).listen("Updated", async (e) => {
      if (e.action == "created") {
        let res = await this.fetchUser(e.data);
        this.set_new_user({ new_user: res, tabKey: this.getTabKey() });
      } else if (e.action == "updated") {
        let res = await this.fetchUser(e.data);
        this.update_user(res);
      } else if (e.action == "status-changed") {
        let res = await this.fetchUser(e.data.item);
        this.change_record_status({
          data: e.data,
          item: res,
          tabKey: this.getTabKey(),
        });
        await this.fetchNewRealtime();
        this.tabChipKey++;
      } else if (e.action == "deleted") {
        this.delete_user({ tabKey: this.getTabKey(), item: e.data });
      }
    });
  },
  beforeDestroy() {
    this.$echo.leave("update.user");
  },
  data() {
    return {
      category: Users(this).category,
      headers: Users(this).headers,
      tabItems: Users(this).tabItems,
      breadcrumb: Users(this).breadcrumb,
      selectedItems: [],
      tabIndex: 0,

      // register user properties
      isEditUser: false,
      registerUserKey: 0,
      editableUser: {},
      userRegisterDialog: false,
      showProfile: false,
      userData: {},
      viewKey: 0,

      // user permission section
      permissionDialog: false,
      permissionKey: 0,
      isUpdatingPermissions: false,
      permissionUser: {},

      //bulk updload
      bulkUploadDialog: false,
      userBulkUpload: false,

      // User Edit Section
      isAutoEdit: false,
      editDialog: false,
      editUserKey: 0,
      editUserItems: [],

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

      // Filter
      filterDialog: false,
      filterData: {},
      options: {},
      searchId: "",
      searchContent: "",

      onlineVisible: false,
      editKey: 0,
      autoEdit: false,
      userAutoEditData: [],

      userEditKey: 0,
      userEdit: false,
      editData: {},
      downloadDialog: false,
      tabChipKey: 0,

      tabKey: "all",
      extraData: [],
    };
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },
  computed: {
    ...mapGetters({
      apiCalling: "users/isApiCalling",
      getTotals: "users/getTotal",
      getItems: "users/getItems",
      getTranslations: "translations/getTranslations",
    }),
  },

  async fetch() {
    await this.getRecord();
  },

  methods: {
    // vuex state methods
    ...mapActions({
      fetchUserPagination: "users/paginateItems",
      fetchItems: "users/fetchItems",
      fetchItemsNew: "users/fetchItemsNew",
      insertUser: "users/insertNewItem",
      removeUser: "users/deleteItem",
      fetchTranslations: "translations/fetchTranslations",
    }),
    ...mapMutations({
      set_new_user: "users/SET_NEW_USER",
      update_user: "users/UPDATE_USER",
      change_record_status: "users/CHANGE_RECORD_STATUS",
      delete_user: "users/DELETE_USER",
      fetch_new_real_time: "users/FETCH_NEW_REAL_TIME",
    }),

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "users_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    async fetchUser(id) {
      try {
        const response = await this.$axios.get(`users/${id}`, {
          params: {},
        });
        if (response?.status === 200) {
          return response?.data?.data;
        }
      } catch (error) {
        return error;
      }
    },

    checkSelectedTab(value) {
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    // add or remove item from selected items
    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    // fired when on view button clicked
    onView() {
      if (this.selectedItems?.length > 1) {
        // this.$toastr.i(this.$tr("select_only_one_item"));
        this.$toasterNA("primary", this.$tr("view_item_max_limit_text"));
        
        return;
      }
      const userId = this.selectedItems[0]?.id;
      this.viewSelectedUser(userId);
    },

    // fired on auto edit button clicked to edit multiple users
    async onAutoEdit() {
      if (this.getTabKey() === "tracing") {
        Alert.customAlert(
          this,
          "cant_change_record_status_in_tracing_tab",
          "restricted"
        );
        return;
      }
      if (this.selectedItems.length > 0) {
        this.editUserItems = [];
        const userIds = this.selectedItems.map((user) => user.id);
        const fetchedUser = await this.fetchUserDetails(userIds);
        if (fetchedUser) {
          this.editUserItems = fetchedUser;
          this.isAutoEdit = true;
          this.editDialog = !this.editDialog;
          this.editUserKey++;
          this.selectedItems = [];
        }
      }
    },

    async editSelectedUser(user) {
      this.editUserItems = [];
      const fetchedUser = await this.fetchUserDetails(user.id);
      if (fetchedUser) {
        this.editUserItems[0] = fetchedUser[0];
        this.editDialog = !this.editDialog;
        this.editUserKey++;
        this.selectedItems.splice(0, 1);
      }
    },

    // fired on edit button clicked to edit user
    async onEdit() {
      if (this.getTabKey() === "tracing") {
        Alert.customAlert(
          this,
          "cant_change_record_status_in_tracing_tab",
          "restricted"
        );
        return;
      }
      if (this.selectedItems.length > 0) {
        this.editUserItems = [];
        const user = this.selectedItems[0];

        const fetchedUser = await this.fetchUserDetails(user.id);
        if (fetchedUser) {
          this.editUserItems[0] = fetchedUser[0];
          this.editDialog = !this.editDialog;
          this.editUserKey++;
          this.selectedItems.splice(0, 1);
        }
      }
    },

    // fetch use details
    async fetchUserDetails(userIds) {
      try {
        this.showProgressBar = true;
        const response = await this.$axios.get(`users/details/${userIds}`);
        const users = response?.data?.data;
        const filteredUsers = [];
        for (const user of users) {
          const departmentsSet = [];
          const countriesSet = [];
          const selectedSystemsSet = new Set();
          const selectedSubSystemsSet = new Set();
          const companiesSet = [];
          const rolesSet = [];
          const teamsSet = [];
          const companiesSystems = [];
          const companyDepartmentTeams = [];

          // parse teams and teams departments
          for (const userTeam of user.teams) {
            if (!this.isExitsIn(userTeam.id, teamsSet)) teamsSet.push(userTeam);
            for (const department of userTeam.departments) {
              if (!this.isExitsIn(department.id, departmentsSet))
                departmentsSet.push(department);
            }
          }

          // parse user roles
          for (const userRole of user.roles) {
            if (!this.isExitsIn(userRole.id, rolesSet)) rolesSet.push(userRole);
          }

          for (const userCompany of user.companies) {
            // parse company department temas
            for (const department of userCompany.departments) {
              // parse company department temas
              for (const team of department.teams) {
                if (!this.isExitsIn(team.id, companyDepartmentTeams))
                  companyDepartmentTeams.push(team);
              }
            }

            // parse company systems
            for (const system of userCompany.systems) {
              if (!this.isExitsIn(system.id, companiesSystems))
                companiesSystems.push(system);
            }

            // parse company countries
            for (const country of userCompany.countries) {
              if (!this.isExitsIn(country.id, countriesSet))
                countriesSet.push(country);
            }

            if (!this.isExitsIn(userCompany.id, companiesSet))
              companiesSet.push({
                id: userCompany.id,
                logo: userCompany.logo,
                name: userCompany.name,
              });

            // parse company systems
            for (const permission of user.permissions) {
              selectedSubSystemsSet.add(
                `${permission.sub_system.system_id}|${permission?.sub_system_id}`
              );
              const parsedPermission = `${permission.sub_system.system_id}|${permission?.sub_system_id}|${permission?.action_id}`;
              selectedSystemsSet.add(parsedPermission);
            }
          }

          user.selectedDepartments = departmentsSet;
          user.companyDepartmentsTeams = companyDepartmentTeams;
          user.selectedCountries = countriesSet;
          user.companiesSystems = companiesSystems;
          user.selectedSystems = Array.from(selectedSystemsSet);
          user.selectedSubSystems = Array.from(selectedSubSystemsSet);
          user.selectedCompanies = companiesSet;
          user.selectedRoles = rolesSet;
          user.selectedTeams = teamsSet;
          user.permission_type = "manually";
          user.isPermissionsSkipped = true;
          let timeRange = JSON.parse(user.time_range);
          user.actualTimeRange = timeRange;
          const to = this.$tr("to");
          if (timeRange) {
            const startTime = Helper.convertTime(this, timeRange.startRange);
            const endTime = Helper.convertTime(this, timeRange.endRange);
            timeRange = `${startTime} ${to} ${endTime}`;
          }

          let dateRange = JSON.parse(user.date_range);
          user.actualDateRange = dateRange;
          if (dateRange) {
            const startDate = Helper.convertTime(this, dateRange.startRange);
            const endDate = Helper.convertTime(this, dateRange.endRange);
            dateRange = `${startDate} ${to} ${endDate}`;
          }

          user.date_range = dateRange;
          user.time_range = timeRange;

          user.generate_password = true;
          user.email_password = true;
          user.change_password = user.change_password !== 0;
          filteredUsers.push(user);
        }

        this.showProgressBar = false;
        return filteredUsers;
      } catch (e) {
        this.$toasterNA("red", this.$tr('something_went_wrong'));
        this.showProgressBar = false;
      }
    },

    isExitsIn(id, items) {
      for (const item of items) {
        if (item.id === id) {
          return true;
        }
      }
      return false;
    },

    // register user dialog toggle
    toggleUserRegisterDialog() {
      this.registerUserKey++;
      this.editableUser = {};
      this.isEditUser = false;
      this.userRegisterDialog = !this.userRegisterDialog;
    },

    toggleAutoEdit() {
      this.editKey++;
      this.autoEdit = !this.autoEdit;
    },

    toggleEdit() {
      this.userEditKey++;
      this.userEdit = !this.userEdit;
    },

    // shows user details
    async viewSelectedUser(userId) {
      try {
        this.showProgressBar = true;
        const response = await this.$axios.get(`/users/${userId}`);

        this.userData = response?.data?.data;
        this.userData.status_transformations =
          this.$sortStatusTransformationData(this.userData);
        this.showProfile = true;
        this.viewKey++;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
      this.showProgressBar = false;
    },

    // ask to add permissions
    askToAddPermissions(userId) {
      Alert.confirmAlert(
        this,
        () => this.editPermissions(userId),
        "",
        "info",
        "add_new_permissions"
      );
    },

    // update user permissions
    async updateUserPermisions(selectedPermisions) {
      try {
        this.isUpdatingPermissions = true;
        const user = this.permissionUser;
        const url = `users?update-permissions=true&user-id=${user.id}`;
        const systems = selectedPermisions.permissions;
        const { permissions, ...data } = selectedPermisions;
        data.permissions = systems;
        const permissionData = Helper.getFormData(data);
        const response = await this.$axios.post(url, permissionData);
        this.isUpdatingPermissions = false;
        if (response.status === 200 && response.data?.result) {
          this.togglePermissionsDialog();
          Alert.successAlert(this, "successfully_updated");
        } else {
          // this.$toastr.w(this.$tr("something_went_wrong"));
				this.$toasterNA("orange",this.$tr('something_went_wrong'));

        }
      } catch (error) {
        this.isUpdatingPermissions = false;
        HandleException.handleApiException(this, error);
      }
    },

    // open permission dialog to edit user permissions
    async editPermissions(userId) {
      const fetchedUser = await this.fetchUserDetails(userId);
      if (fetchedUser) {
        this.permissionUser = fetchedUser[0];
        this.permissionKey++;
        this.togglePermissionsDialog();
      }
    },

    // toggle permission dialog
    togglePermissionsDialog() {
      if (this.permissionDialog) {
        this.permissionUser = {};
      }
      this.permissionDialog = !this.permissionDialog;
    },

    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
    },
    fetchNewData() {
      this.selectedItems = [];
      this.getRecord();
    },

    getSubString(value, length = 20) {
      let subText = value?.substring(0, length);
      if (value?.length > length) {
        subText += "...";
      }
      return subText;
    },
    toggleVisibleOnlineUsers() {
      this.onlineVisible = !this.onlineVisible;
      this.$refs.userAnalytic.userAnalyticsToggleVisible();
    },

    // close register dialog
    closeRegisterDialog() {
      this.userRegisterDialog = false;
      this.$refs.registerUserDialog.close();
    },

    async fetchNewRealtime() {
      const data = {
        key: this.getTabKey(),
        options: {
          itemsPerPage: 1,
          page: this.getItems.length + 1,
        },
      };
      const response = await this.$axios.get(`users?key=${data.key}`, {
        params: data.options,
      });
      this.fetch_new_real_time(response);
      this.selectedItems = [];
    },
    async fetchSession(id) {
      try {
        const response = await this.$axios.get("user/sessions", {
          params: { user_id: id },
        });
        if (response.status == 200) {
        }
      } catch (error) {}
    },

    // 	***********************		start No changes required		************************
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

    selectData(response) {
      if (response.status === 200) {
        const data = response.data;
        if (data != null) {
          this.removeUser(data.id);
          this.insertUser(data);
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

    async searchById(id) {
      this.searchId = id;
      try {
        const options = this.fetchOptions();
        const response = await this.$axios.post("users/searchUser", options); // Change the route
        this.selectData(response);
        console.log(response);
      } 
      catch (error) {
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

    // 	***********************		End NO changes required		************************
  },
};
</script>
<style>
.hide-scroll {
  overflow-y: unset !important;
  height: 90% !important;
}

.active-background {
  background-color: white;
  color: #3b3939 !important;
  font-weight: bolder;
}

.toggle-online-user {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: 20px;
}

@media only screen and (min-width: 800px) {
  .toggle-online-user {
    right: unset;
    left: 50%;
    transform: translate(-50%, -50%);
  }
}
</style>

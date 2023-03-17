<template>
  <v-container fluid class="pa-0">
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
        :page_name="'teams_all'"
        @closeColumnDialog="columnDialog = false"
      />
    </v-dialog>

    <v-dialog
      v-model="profileDialog"
      persistent
      width="1300"
      class="custom-v-dialog"
      v-if="$isInScope('tv')"
    >
      <TeamProfile
        @onEdit="editSelectedTeam"
        :profileData="teamData"
        :key="profileKey"
        :dialog.sync="profileDialog"
      />
    </v-dialog>
    <v-dialog
      v-model="autoEditDialog"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('tu')"
    >
      <TeamAutoEdit
        v-if="autoEditDialog"
        :autoEditData="getTeamWithMetaData"
        @close="autoEditDialog = false"
        @toggleProgressBar="toggleProgressBar"
      />
    </v-dialog>

    <!-- Team / Role Bulk Upload -->
    <v-dialog v-model="teamRoleBulkUpload" width="1300" v-if="$isInScope('tc')">
      <Dialog
        persistent
        max-width="1300"
        @closeDialog="teamRoleBulkUpload = false"
      >
        <TeamRoleBulkUpload
          :tabKey="getTabKey()"
          :team_or_role="'team'"
          v-if="teamRoleBulkUpload"
        />
      </Dialog>
    </v-dialog>

    <v-dialog
      v-model="editDialog"
      persistent
      width="1300"
      scrollable
      max-height="800px"
      v-if="$isInScope('tu')"
    >
      <TeamEdit
        ref="edit_dialog"
        @toggleProgressBar="toggleProgressBar"
        :editData="getTeamWithMetaData[0]"
        @close="editDialog = false"
        v-if="editDialog"
      />
    </v-dialog>
    <v-dialog
      v-model="TeamFilter"
      persistent
      max-width="1300"
      width="1300"
      v-if="$isInScope('tv')"
    >
      <TeamFilter
        @close="TeamFilter = false"
        :departments="getDepartments"
        @getRecord="onFilterRecords"
      />
    </v-dialog>
    <v-dialog
      v-model="registerDialog"
      persistent
      width="1300"
      scrollable
      v-if="$isInScope('tc')"
    >
      <Dialog @closeDialog="toggleTeamRoleRegistration">
        <TeamRoleRegistration
          ref="team_role_registration"
          @close="toggleTeamRoleRegistration"
          @toggleProgressBar="toggleProgressBar"
          :tabKey="tabItems[tabIndex].key"
        />
      </Dialog>
    </v-dialog>
    <v-row no-gutters>
      <ProgressBar v-if="showProgressBar" />

      <v-col cols="12">
        <PageHeader
          :Icon="`team_list`"
          Title="team_list"
          :Breadcrumb="breadcrumb"
        />
      </v-col>
      <v-col cols="12">
        <PageFilters
          :selected_header.sync="all_headers[0].selected_headers"
          :downloadContent="getTeams"
          @onDownload="downloadDialog = !downloadDialog"
          :downloadDialog="downloadDialog"
          :filename="$tr('team_list')"
          :show-add-note="$isInScope('tc')"
          :show-bulk-upload="$isInScope('tc')"
          :showDownload="$isInScope('te')"
          @onBulkUpload="teamRoleBulkUpload = !teamRoleBulkUpload"
          @onColumn="columnDialog = true"
          @searchById="searchById"
          @onFilter="TeamFilter = true"
          @clearAndUnselectId="clearAndUnselectId"
          @searchByContent="searchByContent"
          @onTypeChange="onSearchTypeChange"
        >
          <CustomButton
            @click="registerDialog = true"
            icon="fa-plus"
            text="create_team"
            type="primary"
            v-if="$isInScope('tc')"
          />
        </PageFilters>
      </v-col>
      <v-col cols="12">
        <PageActions
          :showView="$isInScope('tv')"
          :showEdit="$isInScope('tu')"
          :showAutoEdit="$isInScope('tu')"
          :showBlock="$isInScope('tu')"
          :showDelete="$isInScope('td')"
          :showSelect="$isInScope('tu')"
          :showApply="$isInScope('tu')"
          :selectedItems="selectedItems"
          :tab-key.sync="tabKey"
          @onView="viewSelectedTeam"
          @onEdit="onEdit"
          @onAutoEdit="onAutoEdit"
          :routeName="'teams'"
          :subSystemName="'Teams'"
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
          v-model="selectedItems"
          ref="tableRef"
          :headers="all_headers[0].selected_headers"
          :items="getTeams"
          @viewSelectedItem="viewSelectedTeam"
          :loading="apiCalling"
          @click:row="onRowClicked"
          :ItemsLength="getTotal(tabItems[tabIndex].key)"
          @onTablePaginate="onTableChanges"
        >
          <!----------    DATATABLE Countries SECTION        ---------->
          <template v-slot:[`item.department_company_country`]="{ item }">
            <div>
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <span
                    v-bind="attrs"
                    v-on="on"
                    style="white-space: nowrap"
                    class="primary--text"
                  >
                    {{ $tr("countries") }}
                  </span>
                </template>
                <div v-if="item.department_company_country">
                  <div v-if="item.department_company_country.length > 0">
                    <p
                      v-for="country in getTeamsCountries(
                        item.department_company_country
                      )"
                      :key="country.index"
                      class="pb-0 mb-0"
                    >
                      {{ country }}
                    </p>
                  </div>
                  <div v-else>
                    <p class="pb-0 mb-0">{{ $tr("noCountry") }}</p>
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
import ProgressBar from "~/components/common/ProgressBar.vue";
import PageHeader from "~/components/design/PageHeader.vue";
import PageFilters from "~/components/design/PageFilters";
import PageActions from "~/components/design/PageActions";
import DataTable from "~/components/design/DataTable";
import Teams from "~/configs/pages/teams";
import CustomButton from "~/components/design/components/CustomButton";
import TeamRoleRegistration from "~/components/users/team/TeamRoleRegistration.vue";
import TeamProfile from "~/components/users/team/TeamProfile.vue";
import Dialog from "~/components/design/Dialog/Dialog";
import CustomizeColumn from "~/components/customizeColumn/CustomizeColumn2.vue";
import TeamAutoEdit from "~/components/users/team/TeamAutoEdit.vue";
import TeamFilter from "~/components/users/team/TeamFilter.vue";
import TeamEdit from "~/components/users/team/TeamEdit.vue";
import TeamRoleBulkUpload from "~/components/users/TeamRoleBulkUpload";
import TableTabs from "~/components/common/TableTabs.vue";

export default {
  meta: {
    hasAuth: true,
    scope: "tv",
  },
  name: "Team",
  components: {
    TeamProfile,
    Dialog,
    CustomButton,
    DataTable,
    PageActions,
    PageFilters,
    PageHeader,
    ProgressBar,
    TeamRoleRegistration,
    CustomizeColumn,
    TeamAutoEdit,
    TeamFilter,
    TeamEdit,
    TeamRoleBulkUpload,
    TableTabs,
  },

  data() {
    return {
      tabItems: Teams(this).tabItems,

      breadcrumb: Teams(this).breadcrumb,
      registerDialog: false,
      profileDialog: false,

      // Profile
      profileKey: 0,
      teamData: {},

      selectedItems: [],
      tabIndex: 0,
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
      searchContent: "",

      downloadDialog: false,

      // auto edit
      editTeamItems: [],
      isAutoEdit: false,
      editDialog: false,
      autoEditDialog: false,

      TeamFilter: false,
      filterData: {},

      autoEdit: false,
      editKey: 0,
      autoEditData: [],
      teamEdit: false,
      teamEditKey: 0,
      teamRoleBulkUpload: false,
      tabChipKey: 0,

      searchId: "",

      tabKey: "all",
      extraData: [],
    };
  },

  computed: {
    ...mapGetters({
      getTranslations: "translations/getTranslations",
      apiCalling: "teams/isApiCalling",
      getTeams: "teams/getTeams",
      getTotal: "teams/getTotal",
      getDepartments: "departments/getDepartments",
      getTeamWithMetaData: "filterData/getteamsWithMetaData",
    }),
  },
  async created() {
    //customize columns
    await this.fetchHeaders();

    if (process.client) {
      this.fetchDepartments({
        key: "all",
        searchData: null,
      });
    }
  },
  async fetch() {
    await this.getRecord();
  },
  async mounted() {
    this.$echo.private(`update.team`).listen("Updated", async (e) => {
      if (e.action == "created") {
        let team = await this.getTeam(e.data.team_id);
        this.set_new_team({ new_team: team, tabKey: this.getTabKey() });
      } else if (e.action == "updated") {
        let team = await this.getTeam(e.data.team_id);
        this.update_team(team);
      } else if (e.action == "status-changed") {
        let team = await this.getTeam(e.data.item);
        this.change_record_status({
          data: e.data,
          item: team,
          tabKey: this.getTabKey(),
        });
        await this.fetchNewRealtime();
        this.tabChipKey++;
      } else if (e.action == "deleted") {
        this.delete_team({ tabKey: this.getTabKey(), item: e.data });
      }
    });
  },
  beforeDestroy() {
    this.$echo.leave("update.team");
  },
  watch: {
    tabIndex: function (index) {
      this.selectedItems = [];
      this.checkSelectedTab(index);
      this.getRecord();
    },
  },

  methods: {
    ...mapActions({
      fetchTranslations: "translations/fetchTranslations",
      fetchTeamMetaData: "filterData/fetchTeamMetaDataForEdit",
      fetchTeams: "teams/fetchTeams",
      removeTeam: "teams/removeItem",
      insertTeam: "teams/insertNewItem",
      fetchcountries: "countries/fetchCountriesWithCompanies",
      fetchDepartments: "departments/fetchDepartments",
    }),
    ...mapMutations({
      set_new_team: "teams/SET_NEW_TEAM",
      update_team: "teams/UPDATE_TEAM",
      change_record_status: "teams/CHANGE_RECORD_STATUS",
      delete_team: "teams/DELETE_TEAM",
      fetch_new_real_time: "teams/FETCH_NEW_REAL_TIME",
    }),

    async fetchHeaders() {
      try {
        if (this.all_headers[0].selected_headers != []) {
          const response = await this.$axios.get("sub-system-header", {
            params: {
              tab_name: "teams_all",
            },
          });
          this.all_headers[0].selected_headers = response.data.selected_headers;
          this.all_headers[0].headers = response.data.headers;
        }
      } catch (error) {}
    },

    async getTeam(id) {
      try {
        const response = await this.$axios.get(`teams/${id}`);
        if (response?.status === 200) {
          return response?.data?.data;
        }
      } catch (error) {
        return error;
      }
    },

    async fetchNewRealtime() {
      const data = {
        key: this.getTabKey(),
        options: {
          itemsPerPage: 1,
          page: this.getTeams.length + 1,
        },
      };
      const response = await this.$axios.get(`teams?key=${data.key}`, {
        params: data.options,
      });
      this.fetch_new_real_time(response);
      this.selectedItems = [];
    },

    viewSelectedTeam(item) {
      this.profileKey++;
      const data = item || this.selectedItems[0];
      data.status_transformations = this.$sortStatusTransformationData(data);
      this.teamData = data;
      this.profileDialog = true;
    },

    onRowClicked(item) {
      let items = new Set(this.selectedItems);
      items.has(item) ? items.delete(item) : items.add(item);
      this.selectedItems = Array.from(items);
    },

    checkSelectedTab(value) {
      this.selectedItems = [];
      this.tabItems = this.tabItems.map((item, index) => {
        index === value ? (item.isSelected = 1) : (item.isSelected = 0);
        return item;
      });
    },

    getTabKey() {
      return this.tabItems[this.tabIndex]?.key;
    },

    async editSelectedTeam(item) {
      this.toggleProgressBar();
      await this.fetchTeamMetaData([item.id]);
      this.editDialog = !this.editDialog;
      this.toggleProgressBar();
      this.profileDialog = false;
    },

    async onEdit() {
      if (this.selectedItems.length > 1) {
        // this.$toastr.w(this.$tr("edit_item_max_limit_text"));
        this.$toasterNA("orange", this.$tr("edit_item_max_limit_text"));
      } else {
        this.toggleProgressBar();
        await this.fetchTeamMetaData([this.selectedItems[0].id]);
        await this.fetchcountries();
        this.editDialog = !this.editDialog;
        this.toggleProgressBar();
      }
    },
    async onAutoEdit() {
      this.toggleProgressBar();
      await this.fetchTeamMetaData(this.selectedItems.map((item) => item.id));
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

    getTeamsCountries(data) {
      const countrySet = new Set();
      const companySet = new Set();
      const departmentSet = new Set();
      data.forEach((dep) => {
        departmentSet.add(dep.name);
        dep.companies.forEach((com) => {
          companySet.add(com.name);
          com.countries.forEach((count) => {
            countrySet.add(count.name);
          });
        });
      });
      return Array.from(countrySet);
    },

    //customize columns: called from child

    // =>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

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
          this.removeTeam(data.id);
          this.insertTeam(data);
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
        const response = await this.$axios.post("teams/searchTeam", options); // Change the route
        this.selectData(response);
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    async getRecord() {
      try {
        const data = this.fetchOptions();
        const data2 = await this.fetchTeams(data); // Change request url
        this.extraData = data2.data;
      } catch (error) {
        HandleException.handleApiException(this, error);
      }
    },

    // 	***********************		End NO changes required		************************
  },
};
</script>

<style scoped>
.custom-v-dialog {
  overflow-y: visible;
}
</style>

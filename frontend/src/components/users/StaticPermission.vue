<template>
  <div :class="`pa-${padding} pb-4`">
    <div
      class="
        d-flex
        flex-wrap
        justify-center justify-md-space-between
        mx-auto
        mb-2
      "
      style="max-width: 540px"
      v-if="showTabs"
    >
      <FormBtn
        notBold
        notPrimary
        :active="permissionType === 'manually'"
        title="personal"
        class="px-5 ma-1"
        @click="onTabChanges('manually')"
      />
      <FormBtn
        notBold
        notPrimary
        :active="permissionType === 'by_team'"
        title="team"
        class="px-5 ma-1"
        @click="onTabChanges('by_team')"
      />
      <FormBtn
        notBold
        notPrimary
        :active="permissionType === 'by_role'"
        title="role"
        class="px-5 ma-1"
        @click="onTabChanges('by_role')"
      />
    </div>
    <v-tabs-items v-model="permissionType">
      <v-tab-item
        value="manually"
        :style="` ${padding === 0 ? '' : ' height: 470px; overflow: auto;'} `"
      >
        <div class="permissions">
          <div class="perm-header pa-2">
            <p class="perm-title mb-0">
              {{ $tr("manual_permission") }}
            </p>
          </div>
          <v-row class="ma-0">
            <v-col cols="12" md="4" class="pa-0 border-right">
              <div class="sys-titles px-1 my-2">
                <div
                  v-for="(system, systemIndex) in selectedCompanySystems"
                  :key="system.id"
                  @click="changeSystem(systemIndex + 1, system)"
                  :class="`mb-2 py-1 sys-title color-primary d-flex align-center ${systemIndex} ${
                    tab == 'tab-' + (systemIndex + 1)
                      ? 'current primary--text'
                      : ''
                  }`"
                >
                  <div class="icon-container me-1">
                    <img :src="system.logo" :alt="system.name" class="w-full" />
                  </div>
                  <span class="font-weight-black">{{
                    $tr(system.phrase)
                  }}</span>
                  <CheckIcon
                    height="22"
                    class="primary--text"
                    v-if="hasSelected(system.id)"
                  />
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="8" class="pa-1 py-3">
              <div class="sys-perm elevation-1">
                <div class="sys-perm-title">
                  <div class="sys-title color-primary d-flex align-center">
                    <div class="icon-container me-1">
                      <img
                        :src="
                          currentSelectedSystem
                            ? currentSelectedSystem.logo
                            : ''
                        "
                        alt=""
                        class="w-full"
                      />
                    </div>
                    <span class="font-weight-bold">
                      {{
                        currentSelectedSystem
                          ? $tr(currentSelectedSystem.phrase)
                          : ""
                      }}
                    </span>
                  </div>
                </div>
                <v-row class="ma-0">
                  <v-col cols="12" md="6" class="border-right py-3 px-2">
                    <v-tabs-items
                      v-model="tab"
                      v-for="(system, i) in selectedCompanySystems"
                      :key="i"
                    >
                      <v-tab-item
                        :value="'tab-' + (i + 1)"
                        v-if="tab == 'tab-' + (i + 1)"
                      >
                        <v-checkbox
                          v-model="sub_systemsAllArr"
                          :input-value="sub_systemsAllArr"
                          :label="$tr('all')"
                          :value="system.id"
                          multiple
                          hide-details
                          :key="`all-${system.id}`"
                          class="perm-check mt-0 font-weight-bold"
                          @click="systemAllClicked(system.id)"
                        >
                        </v-checkbox>
                        <template v-for="(sub_system, i) in system.sub_systems">
                          <div :key="i">
                            <v-checkbox
                              v-model="sub_systemsArr"
                              multiple
                              :input-value="sub_systemsArr"
                              :label="$tr(sub_system.phrase)"
                              :value="`${system.id}|${sub_system.id}`"
                              hide-details
                              class="perm-check mt-0 font-weight-bold"
                              @click="
                                subSystemClicked(
                                  `${system.id}|${sub_system.id}`
                                )
                              "
                            >
                            </v-checkbox>
                            <div
                              class="perm-check-level-2 ps-4"
                              v-if="
                                isInArr(
                                  `${system.id}|${sub_system.id}`,
                                  sub_systemsArr
                                )
                              "
                            >
                              <v-checkbox
                                v-for="(action, i) in sub_system.actions"
                                :key="i"
                                v-model="actionsArr"
                                :input-value="actionsArr"
                                :label="$tr(action.phrase)"
                                :value="`${system.id}|${sub_system.id}|${action.id}`"
                                hide-details
                                multiple
                                @click="getPermissions"
                                style="font-weight: 600 !important"
                                class="perm-check mt-0"
                              ></v-checkbox>
                            </div>
                          </div>
                        </template>
                      </v-tab-item>
                    </v-tabs-items>
                  </v-col>
                  <v-col cols="12" md="6" class="py-3 px-2">
                    <div class="choose-project">
                      <div class="time">
                        <CustomInput
                          :items="allCompaniesProps"
                          :label="$tr('label_required', $tr('company'))"
                          :placeholder="$tr('choose_item', $tr('company'))"
                          v-model="companyId"
                          item-value="id"
                          item-text="name"
                          has-logo
                          type="autocomplete"
                          class="me-md-2 mb-md-1"
                          ref="autoCompleteRef"
                          @change="onCompanyChanged"
                          :error-messages="companyErrorMessage"
                        />
                        <div class="selected d-flex flex-wrap">
                          <SelectedItem
                            v-for="selectedCompany in selectedCompanies"
                            :key="selectedCompany.id"
                            @close="
                              () => removeSelectedCompany(selectedCompany.id)
                            "
                            :title="selectedCompany.name"
                            :logo-url="selectedCompany.logo"
                          />
                        </div>

                        <h3 class="time-title">
                          {{ $tr("time_schedule") }}
                        </h3>
                        <v-radio-group
                          v-model="actualPermission.schedule_type"
                          class="mt-0"
                        >
                          <v-radio
                            :label="$tr('unlimited')"
                            :value="'unlimited'"
                            class="font-weight-bold"
                          />
                          <v-radio
                            :label="$tr('limited')"
                            :value="'limited'"
                            class="font-weight-bold"
                          />
                          <div
                            class="px-3"
                            v-if="actualPermission.schedule_type === 'limited'"
                          >
                            <p class="mb-1 custom-input-title">
                              {{ $tr("date_range") }}
                            </p>
                            <RangePicker
                              :placeholder="dateRange"
                              @getRangeData="onDateRangePickerSelected"
                              :error-messages="dateErrMsg"
                            />
                            <p class="mb-1 custom-input-title">
                              {{ $tr("time_range") }}
                            </p>
                            <RangePicker
                              :placeholder="timeRange"
                              type="time"
                              @getRangeData="onTimeRangePickerSelected"
                              :error-messages="timeErrMsg"
                            />
                          </div>
                        </v-radio-group>
                      </div>
                    </div>
                  </v-col>
                </v-row>
              </div>
            </v-col>
          </v-row>
        </div>
      </v-tab-item>
      <v-tab-item value="by_team" style="height: 470px; overflow: auto">
        <div class="team mt-4">
          <div class="d-flex">
            <div class="w-full">
              <CustomInput
                :items="teams"
                v-model="selectedTeamId"
                item-value="id"
                item-text="name"
                has-logo
                :label="$tr('label_required', $tr('team'))"
                :placeholder="$tr('choose_item', $tr('team'))"
                type="autocomplete"
                class="me-md-4 mb-md-2 mb-0"
                :error-messages="teamErrorMessage"
              />
            </div>
            <div class="w-100">
              <FormBtn
                @click="onTeamSelected"
                title="add_plus"
                class="mt-md-4 mb-2"
              />
            </div>
          </div>
          <div class="selected d-flex flex-wrap">
            <SelectedItem
              v-for="teams in selectedTeams"
              :key="teams.id"
              @close="() => removeSelectedTeam(teams.id)"
              :logo-url="teams.logo"
              :title="teams.name"
            />
          </div>
        </div>
      </v-tab-item>
      <v-tab-item value="by_role" style="height: 470px; overflow: auto">
        <div class="role mt-4">
          <div class="d-flex">
            <div class="w-full">
              <CustomInput
                :items="roles"
                v-model="selectedRoleId"
                item-value="id"
                item-text="name"
                has-logo
                :label="$tr('label_required', $tr('role'))"
                :placeholder="$tr('choose_item', $tr('role'))"
                type="autocomplete"
                class="me-md-4 mb-md-2 mb-0"
                :error-messages="roleErrorMessage"
              />
            </div>
            <div class="w-100">
              <FormBtn
                @click="onRoleSelected"
                title="add_plus"
                class="mt-md-4 mb-2"
              />
            </div>
          </div>

          <div class="selected d-flex flex-wrap">
            <SelectedItem
              v-for="role in selectedRoles"
              :key="role.id"
              @close="() => removeSelectedRole(role.id)"
              :title="role.name"
              logo-url=""
            />
          </div>
        </div>
      </v-tab-item>
    </v-tabs-items>

    <v-row v-if="showButtons" class="mt-3 mb-4">
      <v-spacer />

      <v-btn
        color="primary"
        class="stepper-btn px-3"
        style="min-width: 160px"
        v-if="loading || isSubmitting"
        :loading="loading || isSubmitting"
      >
        <template v-slot:loader>
          <span>
            <span>
              {{
                isSubmitting
                  ? $tr("submitting") + "..."
                  : $tr("validating") + "..."
              }}</span
            >
            <v-progress-circular
              class="ma-0"
              indeterminate
              color="white"
              size="25"
              :width="2"
            />
          </span>
        </template>
      </v-btn>

      <v-btn
        v-else
        color="primary"
        class="stepper-btn"
        @click="submit"
        :type="'button'"
      >
        {{ $tr("submit") }}
        <v-icon right small> fa-check </v-icon>
      </v-btn>
      <v-btn
        text
        color="primary"
        @click="cancelDialog"
        class="stepper-btn px-2 mx-2"
      >
        {{ $tr("cancel") }}
      </v-btn>
    </v-row>
  </div>
</template>

<script>
import FormBtn from "../design/components/FormBtn";
import RangePicker from "./RangePicker";

import CheckIcon from "../design/components/CheckIcon.vue";
import CustomInput from "../design/components/CustomInput";
import SelectedItem from "../design/components/SelectedItem";
import Alert from "../../helpers/alert";

export default {
  name: "StaticPermission",
  components: {
    SelectedItem,
    CustomInput,
    FormBtn,
    CheckIcon,
    RangePicker,
  },

  props: {
    selectedCompanySystemsProps: {
      type: Array,
      default: () => [],
    },
    allCompaniesProps: {
      type: Array,
      default: () => [],
    },
    selectedCompaniesProps: {
      type: Array,
      default: () => [],
    },
    selectedActionsProps: {
      type: Array,
      default: () => [],
    },

    selectedSubSystemsProps: {
      type: Array,
      default: () => [],
    },

    selectedTeamsProps: {
      type: Array,
      default: () => [],
    },

    selectedRolesProps: {
      type: Array,
      default: () => [],
    },

    allTeamsProps: {
      type: Array,
      default: () => [],
    },

    scheduleTypeProps: {
      type: String,
      default: "unlimited",
    },

    dateRangeProps: {
      type: String,
    },

    actualDateRangeProps: {
      type: Object,
      default: () => {},
    },

    timeRangeProps: {
      type: String,
    },

    actualTimeRangeProps: {
      type: Object,
      default: () => {},
    },
    dialog: {
      Type: Boolean,
      default: false,
    },

    showButtons: {
      Type: Boolean,
      default: false,
    },
    padding: {
      type: Number,
      default: 10,
    },
    isSubmitting: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },

    showButtons: {
      type: Boolean,
      default: false,
    },
    leaveDateRange: {
      type: Boolean,
      default: false,
    },

    showTabs: {
      type: Boolean,
      default: false,
    },
  },

  watch: {
    dateRangeProps: function (value) {
      this.dateRange = value;
    },

    timeRangeProps: function (value) {
      this.timeRange = value;
    },
    actualDateRangeProps: function (value) {
      this.actualPermission.dateRange = value;
    },
    actualTimeRangeProps: function (value) {
      this.actualPermission.timeRange = value;
    },
  },

  data() {
    return {
      dateRange: this.dateRangeProps,
      timeRange: this.timeRangeProps,
      // extra data
      selectedCompanies: this.selectedCompaniesProps,
      companyErrorMessage: "",
      companyId: "",

      // manually permission Section
      permissionType: "manually",
      selectedCompanySystems: this.selectedCompanySystemsProps,

      // permissions data
      sub_systemsArr: this.selectedSubSystemsProps,
      actionsArr: this.selectedActionsProps,
      sub_systemsAllArr: [],
      actualPermission: {
        permissions: [],
        schedule_type: this.scheduleTypeProps,
        timeRange: this.actualTimeRangeProps,
        dateRange: this.actualDateRangeProps,
      },

      // system permission Section
      tab: "tab-1",
      currentSelectedSystem:
        this.selectedCompanySystemsProps.length > 0
          ? this.selectedCompanySystemsProps[0]
          : {},

      // role section
      teams: this.allTeamsProps,
      selectedTeamId: "",
      teamErrorMessage: "",
      selectedTeams: this.selectedTeamsProps,

      roles: [],
      selectedRoleId: "",
      roleErrorMessage: "",
      selectedRoles: this.selectedRolesProps,

      timeErrMsg: "",
      dateErrMsg: "",
    };
  },

  created() {
    this.fetchAllRoles();
  },

  methods: {
    /** Time Picker Section */

    // fired when tab changed
    async onTabChanges(tabValue) {
      if (this.permissionType == "manually") {
        if (
          this.actualPermission.permissions &&
          this.actualPermission.permissions.length < 1
        ) {
          this.permissionType = tabValue;
          return;
        }
      } else if (this.permissionType === "by_role") {
        if (this.selectedRoles.length < 1) {
          this.permissionType = tabValue;
          return;
        }
      } else if (this.permissionType === "by_team") {
        if (this.selectedTeams.length < 1) {
          this.permissionType = tabValue;
          return;
        }
      }
      await Alert.confirmAlert(
        this,
        this.clearPermissions,
        "",
        "info",
        "clear_permissions"
      );
      this.permissionType = tabValue;
    },

    // set data
    setExtraData(teams, selectedCompanies) {
      this.teams = teams;
      this.selectedCompanies = selectedCompanies;
    },

    validateDateTime() {
      if (
        (this.actualPermission.dateRange == undefined ||
          this.actualPermission.dateRange == "") &&
        this.actualPermission.schedule_type == "limited"
      ) {
        if (this.leaveDateRange) {
          this.dateErrMsg = "";
          this.actualPermission.dateRange = null;
        } else {
          this.dateErrMsg = this.$tr(
            "item_is_required",
            this.$tr("date_range")
          );
        }
      }

      if (
        (this.actualPermission.timeRange == undefined ||
          this.actualPermission.timeRange == "") &&
        this.actualPermission.schedule_type == "limited"
      ) {
        this.timeErrMsg = this.$tr("item_is_required", this.$tr("time_range"));
      }
      if (this.actualPermission.schedule_type == "limited") {
        return this.timeErrMsg == "" && this.dateErrMsg == "";
      }
      return true;
    },

    // clear all permissions
    clearPermissions() {
      if (this.permissionType === "manually") {
        this.sub_systemsArr = this.selectedSubSystemsProps;
        this.actionsArr = this.selectedActionsProps;
        this.sub_systemsAllArr = [];
        this.actualPermission = {
          schedule_type: "unlimited",
          timeRange: {},
          dateRange: {},
        };
      } else if (this.permissionType === "by_role") {
        this.selectedRoles = [];
      } else if (this.permissionType === "by_team") {
        this.selectedTeams = [];
      }
    },

    // clear all permissions
    clearAllPermissions() {
      this.sub_systemsArr = [];
      this.actionsArr = [];
      this.sub_systemsAllArr = [];
      this.actualPermission = {
        schedule_type: "unlimited",
        timeRange: {},
        dateRange: {},
      };
      this.selectedRoles = [];
      this.selectedTeams = [];
    },

    // fetch companies systems
    async fetchCompaniesSystems(companyIds) {
      try {
        const url = `systems?company-ids=${companyIds}`;
        const response = await this.$axios.get(url);
        this.selectedCompanySystems = response?.data;
        if (this.selectedCompanySystems.length > 0) {
          this.currentSelectedSystem = this.selectedCompanySystems[0];
        } else {
          this.sub_systemsArr = [];
          this.actionsArr = [];
        }
      } catch (e) {}
    },

    onDateRangePickerSelected(data) {
      this.actualPermission.dateRange = data;
      this.dateErrMsg = "";
    },

    onTimeRangePickerSelected(data) {
      this.actualPermission.timeRange = data;
      this.timeErrMsg = "";
    },

    /** Roles Section */
    getData() {
      this.getPermissions();
      const data = this.actualPermission;
      data.permission_type = this.permissionType;
      data.teams = this.selectedTeams.length
        ? this.selectedTeams.map((team) => team?.id)
        : [];
      data.roles = this.selectedRoles.length
        ? this.selectedRoles.map((role) => role?.id)
        : [];
      data.companies = this.selectedCompanies.map((company) => company?.id);
      if (data.schedule_type !== "limited") {
        this.actualPermission.timeRange = "";
        this.actualPermission.dateRange = "";
        data.timeRange = "";
        data.dateRange = "";
      }

      return data;
    },

    cancelDialog() {
      this.$emit("onClose");
      this.selectedTeams = [];
      this.selectedRoles = [];
      this.selectedCompanies = [];
      this.actualPermission = {
        permissions: [],
        schedule_type: "unlimited",
        timeRange: {},
        dateRange: {},
      };
    },

    submit() {
      if (this.checkRoleSection()) {
        const currentData = this.getData();
        this.$emit("submit", currentData);
      }
    },

    // change systems
    changeSystem(num, system) {
      this.tab = "tab-" + num;
      this.currentSelectedSystem = system;
    },

    hasSelected(system) {
      let res = this.sub_systemsArr.find((item) => {
        return item.split("|")[0] === system;
      });
      return res !== undefined;
    },

    // check role section validations
    checkRoleSection() {
      let currentData = this.getData();

      let isPermissionValid = false;
      if (currentData.teams.length > 0) {
        isPermissionValid = true;
      }
      if (currentData.roles.length > 0) {
        isPermissionValid = true;
      }
      if (currentData?.permissions) {
        if (currentData.permissions.length > 0) {
          isPermissionValid = true;
        }
      }

      const isCompanyInvalid = currentData.companies.length < 1;

      let isDateRangeInvalid = false;
      let isTimeRangeInvalid = false;

      if (currentData.schedule_type === "limited") {
        if (this.$_.isEmpty(currentData.dateRange)) {
          isDateRangeInvalid = true;
        }
        if (this.$_.isEmpty(currentData.timeRange)) {
          isTimeRangeInvalid = true;
        }
      }

      isPermissionValid = !isPermissionValid;

      let roleSectionHasError =
        isCompanyInvalid ||
        isPermissionValid ||
        isDateRangeInvalid ||
        isTimeRangeInvalid;

      if (roleSectionHasError) {
        // this.$toastr.w(this.$tr("select_permissions_or_skip"));
					this.$toasterNA("orange",this.$tr("select_permissions_or_skip"));

        return false;
      }
      return true;
    },

    // add selected team
    onTeamSelected() {
      if (this.selectedTeamId) {
        this.teamErrorMessage = "";
        const selectedTeam = this.teams.find(
          (item) => item.id === this.selectedTeamId
        );
        const isExists = this.selectedTeams?.find(
          (item) => item.id === selectedTeam.id
        );
        if (isExists) {
          this.teamErrorMessage = this.$tr(
            "item_already_exists",
            this.$tr("team")
          );
          return;
        }
        this.selectedTeamId = "";
        this.selectedTeams.unshift(selectedTeam);
      } else {
        this.teamErrorMessage = this.$tr("item_is_required", this.$tr("team"));
      }
    },

    // remove selected team
    removeSelectedTeam(selectedTeamId) {
      const filteredTeams = this.selectedTeams.filter(
        (team) => team.id !== selectedTeamId
      );
      this.selectedTeams = filteredTeams;
      if (filteredTeams.length < 1) {
        this.teamErrorMessage = this.$tr("item_is_required", this.$tr("team"));
      }
    },

    // add selected role
    onRoleSelected() {
      if (this.selectedRoleId) {
        this.roleErrorMessage = "";
        const selectedRole = this.roles.find(
          (item) => item.id === this.selectedRoleId
        );
        const isExists = this.selectedRoles?.find(
          (item) => item.id === selectedRole.id
        );
        if (isExists) {
          return;
        }
        this.selectedRoles.unshift(selectedRole);
        this.selectedRoleId = "";
      } else {
        this.roleErrorMessage = this.$tr(
          "item_already_exists",
          this.$tr("role")
        );
      }
    },

    // remove selected role
    removeSelectedRole(selectedRoleId) {
      const filteredRoles = this.selectedRoles?.filter(
        (role) => role?.id !== selectedRoleId
      );
      this.selectedRoles = filteredRoles;
      if (filteredRoles?.length < 1) {
        this.roleErrorMessage = this.$tr("item_is_required", this.$tr("role"));
      }
    },

    // fetch all teams
    async fetchAllTeams(selectedDepartments) {
      try {
        const departmentsIds = selectedDepartments?.map(({ id }) => id);
        const { data } = await this.$axios.get(`common/team/${departmentsIds}`);
        this.teams = data;
      } catch (_) {}
    },

    // fetch all roles
    async fetchAllRoles() {
      try {
        if (this.roles.length > 0) {
          return;
        }
        const { data } = await this.$axios.get("common/roles");
        this.roles = data.roles;
      } catch (_) {}
    },

    isInArr(str, arr) {
      let res = arr.find((item) => {
        return item == str;
      });
      return res !== undefined;
    },

    removeSystemsStuff(system) {
      this.sub_systemsArr = this.sub_systemsArr.filter((sub_system) => {
        return !(sub_system.split("|")[0] == system);
      });
      this.actionsArr = this.actionsArr.filter((action) => {
        return !(action.split("|")[0] == system);
      });
    },

    systemAllClicked(system) {
      if (this.isInArr(system, this.sub_systemsAllArr)) {
        this.removeSystemsStuff(system);
        this.selectedCompanySystems.forEach((sys) => {
          if (sys.id == system) {
            sys.sub_systems.forEach((sub_sys) => {
              this.sub_systemsArr.push(`${sys.id}|${sub_sys.id}`);
              // sub_sys.actions.forEach((act) => {
              //   this.actionsArr.push(`${sys.id}|${sub_sys.id}|${act.id}`);
              // });
            });
          }
        });
      } else {
        this.removeSystemsStuff(system);
      }
      this.getPermissions();
    },

    // assign permissions
    subSystemClicked(sub_system) {
      if (!this.isInArr(sub_system, this.sub_systemsArr)) {
        this.actionsArr = this.actionsArr.filter((action) => {
          return !(
            action.split("|")[0] == sub_system.split("|")[0] &&
            action.split("|")[1] == sub_system.split("|")[1]
          );
        });
      }
      this.getPermissions();
    },

    // assign permissions
    getPermissions() {
      let arr = [];
      this.actionsArr.forEach((action) => {
        action = action.split("|");
        arr.push({
          system_id: action[0],
          sub_system_id: action[1],
          action_id: action[2],
        });
      });

      this.actualPermission.permissions = arr;
    },

    // add new company
    async onCompanyChanged(companyId) {
      const CompanyRequiredText = this.$tr(
        "item_is_required",
        this.$tr("company")
      );

      if (companyId) {
        const company = this.allCompaniesProps.find(
          (company) => company.id === companyId
        );
        if (company) {
          if (
            this.selectedCompanies?.some(
              (companyItem) => companyItem?.id === company.id
            )
          ) {
            return;
          }
          this.companyId = "";
          this.$refs.autoCompleteRef.lazySearch = "";
          this.selectedCompanies.unshift(company);
          const companyIds = this.selectedCompanies.map(
            (company) => company.id
          );
          this.fetchCompaniesSystems(companyIds);
        }

        this.selectedCompanies.length < 1
          ? (this.companyErrorMessage = CompanyRequiredText)
          : (this.companyErrorMessage = "");
      } else if (this.selectedCompanies?.length < 1) {
        this.companyErrorMessage = CompanyRequiredText;
      }
    },

    // remove selected company
    async removeSelectedCompany(companyId) {
      this.selectedCompanies = this.selectedCompanies.filter(
        (company) => company.id !== companyId
      );

      const companyIds = this.selectedCompanies.map((company) => company.id);
      this.fetchCompaniesSystems(companyIds);

      const requiredText = this.$tr("item_is_required", this.$tr("company"));
      if (this.selectedCompanies.length < 1) {
        this.selectedCompanySystems = [];
        this.currentSelectedSystem = {};
        this.companyErrorMessage = requiredText;
      } else {
        this.companyErrorMessage = "";
      }
    },
  },
};
</script>
<style scoped>
.permissions,
.selected-projects {
  border: 1px solid var(--input-placeholder-color);
}

.perm-header {
  background: var(--stepper-header-bg);
}

.perm-title {
  font-size: 16px;
  color: var(--input-title-color);
  letter-spacing: 1px;
}

.border-right {
  border-right: 1px solid var(--input-placeholder-color);
}

@media only screen and (max-width: 960px) {
  .border-right {
    border-right: 0 !important;
  }
}

.v-application--is-rtl .border-right {
  border-left: 1px solid var(--input-placeholder-color);
}

.sys-title {
  font-weight: 500;
  cursor: pointer;
}

.sys-title .icon-container {
  height: 20px;
  width: 20px;
  overflow: hidden;
  background: var(--input-title-color) !important;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 4px;
  border-radius: 3px;
}

.sys-title.current .icon-container {
  background: var(--v-primary-base) !important;
}

.sys-title .v-icon {
  font-size: 12px;
  vertical-align: middle;
  color: var(--color-white);
}

.v-application--is-rtl .sys-title .v-icon {
  margin-left: 4px;
}

.sys-perm {
  border: 1px solid var(--input-placeholder-color);
  border-radius: 4px;
}

.sys-perm-title {
  padding: 4px 8px;
  border-bottom: 1px solid var(--input-placeholder-color);
}

.time-title {
  font-size: 16px;
  font-weight: 500;
}

.perm-check .v-label {
  font-weight: 500;
}

.perm-check-level-2 .perm-check .v-label {
  font-size: 12px;
  font-weight: 400;
}

.perm-check-level-2 .perm-check .v-icon {
  font-size: 16px !important;
}

.perm-check-level-2
  .perm-check
  .v-input--selection-controls__input:hover
  .v-input--selection-controls__ripple:before {
  transform: scale(0.8) !important;
}
</style>

<template>
  <div>
    <div
      class="d-flex flex-wrap justify-center justify-md-space-between mx-auto"
      style="max-width: 540px"
    >
      <FormBtn
        notBold
        notPrimary
        :active="user.permission_type.$model === 'manually'"
        title="personal"
        class="px-5 ma-1"
        @click="user.permission_type.$model = 'manually'"
      />
      <FormBtn
        notBold
        notPrimary
        :active="user.permission_type.$model === 'by_team'"
        title="team"
        class="px-5 ma-1"
        @click="user.permission_type.$model = 'by_team'"
      />
      <FormBtn
        notBold
        notPrimary
        :active="user.permission_type.$model === 'by_role'"
        title="role"
        class="px-5 ma-1"
        @click="user.permission_type.$model = 'by_role'"
      />
    </div>
    <v-tabs-items v-model="user.permission_type.$model">
      <v-tab-item value="manually">
        <ManualPermission
          ref="manualPermissions"
          class="mt-4"
          :selectedCompanies="selectedCompanies"
          @getPermissions="getSelectedPermissions"
          @getTimeDate="getSelectedSchedule"
        />
      </v-tab-item>
      <v-tab-item value="by_team">
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
      <v-tab-item value="by_role">
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
  </div>
</template>

<script>
import FormBtn from "../design/components/FormBtn";
import ManualPermission from "../design/ManualPermission";
import CustomInput from "../design/components/CustomInput";
import SelectedItem from "../design/components/SelectedItem";

export default {
  name: "UserPermission",
  components: { SelectedItem, CustomInput, ManualPermission, FormBtn },
  props: {
    user: {
      required: true,
      type: Object,
    },
    rules: {
      required: true,
      type: Object,
    },
    isEdit: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      selectedCompanies: [],

      // role section
      teams: [],
      selectedTeamId: "",
      teamErrorMessage: "",
      selectedTeams: [],

      roles: [],
      selectedRoleId: "",
      roleErrorMessage: "",
      selectedRoles: [],
    };
  },

  methods: {
    setPermissions(selectedActions, selectedSubSystems) {
      this.$refs.manualPermissions.actionsArr = selectedActions;
      this.$refs.manualPermissions.sub_systemsArr = selectedSubSystems;
      const scheduleType = this.user.$model.schedule_type;
      const endDate = this.user.$model.limit_date;
      const endTime = this.user.$model.limit_time;
      this.$refs.manualPermissions.radio =
        scheduleType !== "unlimited" ? "limited" : "unlimited";
      this.$refs.manualPermissions.restrictDateTime = endDate ? "date" : "time";
      if (scheduleType !== "unlimited") {
        this.$refs.manualPermissions.scheduleErrorMessage = "";
      }
      this.$refs.manualPermissions.end_date = endDate;
      this.$refs.manualPermissions.end_time = endTime;
    },

    /** Roles Section */

    // check role section validations
    checkRoleSection() {
      this.user.teams.$model = this.selectedTeams.map((team) => team?.id);
      this.user.roles.$model = this.selectedRoles.map((role) => role?.id);
      this.user.isPermissionsSkipped.$model = this.isEdit;

      let isSystemsInvalid = this.user.systems.$invalid;
      let isRoleInvalid = this.user.roles.$invalid;
      let isTeamInvalid = this.user.teams.$invalid;
      const isEndDateInvalid = this.user.limit_date.$invalid;
      const isEndTimeInvalid = this.user.limit_time.$invalid;

      const roleSectionHasError =
        isSystemsInvalid ||
        isRoleInvalid ||
        isTeamInvalid ||
        isEndDateInvalid ||
        isEndTimeInvalid;

      if (roleSectionHasError) {
        // this.$toastr.w(this.$tr("please_select_permission"));
					this.$toasterNA("orange",this.$tr("please_select_permission"));

        return false;
      }
      return true;
    },

    // add selected team
    onTeamSelected() {
      if (this.selectedTeamId) {
        this.teamErrorMessage = "";
        const selectedTeam = this.teams.find(
          (item) => item?.id === this.selectedTeamId
        );
        const isExists = this.selectedTeams?.find(
          (item) => item?.id === selectedTeam?.id
        );
        if (isExists) {
          this.teamErrorMessage = this.$tr("exists", [this.$tr("team")]);
          return;
        }
        this.selectedTeams.unshift(selectedTeam);
      } else {
        this.teamErrorMessage = this.$tr("required", [this.$tr("team")]);
      }
    },

    // remove selected team
    removeSelectedTeam(selectedTeamId) {
      const filteredTeams = this.selectedTeams?.filter(
        (team) => team?.id !== selectedTeamId
      );
      this.selectedTeams = filteredTeams;
      if (filteredTeams?.length < 1) {
        this.teamErrorMessage = this.$tr("required", [this.$tr("team")]);
      }
    },

    // add selected role
    onRoleSelected() {
      if (this.selectedRoleId) {
        this.roleErrorMessage = "";
        const selectedRole = this.roles.find(
          (item) => item?.id === this.selectedRoleId
        );
        const isExists = this.selectedRoles?.find(
          (item) => item?.id === selectedRole?.id
        );
        if (isExists) {
          return;
        }
        this.selectedRoles.unshift(selectedRole);
      } else {
        this.roleErrorMessage = this.$tr("exists", [this.$tr("role")]);
      }
    },

    // remove selected role
    removeSelectedRole(selectedRoleId) {
      const filteredRoles = this.selectedRoles?.filter(
        (role) => role?.id !== selectedRoleId
      );
      this.selectedRoles = filteredRoles;
      if (filteredRoles?.length < 1) {
        this.roleErrorMessage = this.$tr("exists", [this.$tr("role")]);
      }
    },

    // return selected permissions
    getSelectedPermissions(permissionsList) {
      this.user.systems.$model = permissionsList;
    },

    // return selected schedule time
    getSelectedSchedule(schedule) {
      this.user.schedule_type.$model = schedule?.schedule_type;
      if (schedule.schedule_type === "date") {
        this.user.limit_date.$model = schedule.end_date;
        this.user.limit_time.$model = "";
      } else if (schedule.schedule_type === "time") {
        this.user.limit_time.$model = schedule.end_time;
        this.user.limit_date.$model = "";
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
  },
};
</script>

<style scoped></style>

<template>
  <v-form ref="teamRoleForm" lazy-validation @submit.prevent="onSubmit">
    <CustomStepper
      :headers="steppers"
      :canNext="false"
      @submit="onSubmit"
      @validate="() => {}"
      @close="
        () => {

          $emit('close');
        }
      "
      @changeToValidate="() => {}"
      ref="asignStepper"
      :loading="isLoading"
      :isSubmitting="isSubmitting"
      :showStartOver="false"
      :showClose="true"
    >
      <template v-slot:step1>
        <div class="teams">
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                :items="teams"
                item-value="id"
                item-text="name"
                v-model="selectedTeamId"
                :label="$tr('label_required', $tr('teams'))"
                :placeholder="$tr('choose_item', $tr('teams'))"
                ref="teamInput"
                type="autocomplete"
                class="me-md-4 mb-md-2 mb-0"
                :error-messages="teamErrorMessage"
                @blur="
                  isValidForm('teams', 'selectedTeams', 'teamErrorMessage')
                "
                :loading="teamLoading"
                :team="true"
              />
            </div>
            <div class="w-100">
              <FormBtn
                @click="addSelectedTeam"
                :title="'add_plus'"
                :class="`mt-md-4 mb-2`"
              />
            </div>
          </div>
          <div class="selected d-flex flex-wrap">
            <SelectedItem
              v-for="selectedTeam in selectedTeams"
              :key="selectedTeam.id"
              @close="() => removeSelectedTeam(selectedTeam.id)"
              :title="`${selectedTeam.name}`"
              logoName=""
            />
          </div>
        </div>
        <div class="users">
          <div class="d-flex flex-column flex-md-row">
            <div class="w-full">
              <CustomInput
                :items="arrangeUsers(users)"
                :user_with_or_no_permissions="true"
                :item-text="
                  (item) =>
                    item.firstname +
                    ' ' +
                    item.lastname +
                    ' ( ' +
                    item.code +
                    ' )'
                "
                item-value="id"
                v-model="selectedUserId"
                :label="$tr('label_required', $tr('users'))"
                :placeholder="$tr('choose_item', $tr('users'))"
                ref="userInput"
                type="autocomplete"
                class="me-md-4 mb-md-2 mb-0"
                :error-messages="userErrorMessage"
                @blur="
                  isValidForm('users', 'selectedUsers', 'userErrorMessage')
                "
                :loading="userLoading"
                :item-disabled="(item) => false"
              />
              <!-- :item-disabled="(item)=>checkPermission(item)" -->
            </div>
            <div class="w-100">
              <FormBtn
                @click="addSelectedUser"
                :title="'add_plus'"
                :class="`mt-md-4 mb-2`"
              />
            </div>
          </div>
          <div class="selected d-flex flex-wrap">
            <SelectedItem
              v-for="selectedUser in selectedUsers"
              :key="selectedUser.id"
              @close="() => removeSelectedUser(selectedUser.id)"
              :title="`${selectedUser.firstname} ${selectedUser.lastname} (${selectedUser.code})`"
              :logoUrl="`${selectedUser.image}`"
              logoName=""
            />
          </div>
        </div>
      </template>
      <template v-slot:step2>
        <div v-if="users.length == 0">
          <div class="d-flex flex-column align-center">
            <lottie-player
              src="/assets/success.json"
              background="transparent"
              speed="1"
              style="width: 250px; height: 250px"
              loop
              autoplay
            >
            </lottie-player>
            <h2 class="mb-1" style="color: #2cda94">
              {{ $tr("all_users_are_assigned_to_selected_design_request") }}
            </h2>
          </div>
        </div>
        <DoneMessage
          v-else
          :title="$tr('item_all_set', 'user')"
          :subTitle="$tr('you_can_access_your_item', $tr('user'))"
        />
      </template>
    </CustomStepper>
  </v-form>
</template>
<script>
import CustomStepper from "~/components/design/FormStepper/CustomStepper";
import DoneMessage from "~/components/design/components/DoneMessage.vue";
import CustomInput from "../design/components/CustomInput";
import SelectedItem from "../design/components/SelectedItem";
import FormBtn from "../design/components/FormBtn";

export default {
  components: {
    CustomStepper,
    DoneMessage,
    CustomInput,
    FormBtn,
    SelectedItem,
  },
  props: {
    selected: {
      type: Array,
      require: true,
    },
    tabKey: {
      type: String,
      require: true,
    },
  },
  data() {
    return {
      teamLoading: false,
      users: [],
      teams: [],
      teamErrorMessage: "",
      selectedUserId: "",
      selectedTeamId: "",
      userErrorMessage: "",
      selectedUsers: [],
      selectedTeams: [],
      isLoading: false,
      isSubmitting: false,
      userLoading: false,
      usersLoaded: false,
      steppers: [
        {
          icon: "fa-info",
          title: "assign",
          slotName: "step1",
        },
        {
          icon: "fa-thumbs-up",
          title: "done",
          slotName: "step2",
        },
      ],
    };
  },
  async created() {
    this.fetchTeams();
    this.users = await this.fetchTeamUsers([]);
  },
  methods: {
    checkPermission(item) {
      const allowedPermissions = new Set(
        item.permissions.map((p) => p.action.name)
      );
      return !Array.from(allowedPermissions).includes(
        this.prepareTabKey(this.tabKey)
      );
    },
    arrangeUsers(users) {
      let new_users = [];
      users.forEach((user) => {
        if (user.teams.length) {
          if (
            !new_users.map((u) => u.header).includes(this.$tr("team_member"))
          ) {
            new_users.push({ header: this.$tr("team_member") }, { ...user });
          } else {
            new_users.push(user);
          }
        } else {
          if (
            !new_users
              .map((u) => u.header)
              .includes(this.$tr("out_of_team_member"))
          ) {
            new_users.push(
              { divider: true },
              { header: this.$tr("out_of_team_member") },
              { ...user }
            );
          } else {
            new_users.push(user);
          }
        }
      });
      return new_users;
    },
    async addSelectedTeam() {
      const requiredText = this.$tr("item_is_required", this.$tr("teams"));
      if (this.selectedTeamId) {
        if (this.selectedTeams.map((t) => t.id).includes(this.selectedTeamId)) {
          return;
        }
        this.selectedTeams?.unshift(
          this.teams?.find((t) => t.id == this.selectedTeamId)
        );
        this.selectedTeamId = "";
        this.selectedTeams?.length < 1
          ? (this.teamErrorMessage = requiredText)
          : (this.teamErrorMessage = "");
        this.users = await this.fetchTeamUsers(this.selectedTeams);
      } else {
        this.teamErrorMessage = requiredText;
      }
    },
    async fetchTeamUsers(selected_teams) {
      try {
        this.userLoading = true;
        const response = await this.$axios.get("users", {
          params: {
            not_assigned_users: true,
            tabKey: this.prepareTabKey(this.tabKey),
            team_ids: selected_teams.map((t) => t.id),
          },
        });
        this.userLoading = false;
        return this.prepareUsersAccordingToPermission(response?.data?.users);
      } catch (err) {
        this.userLoading = false;
      }
    },
    prepareUsersAccordingToPermission(users) {
      let new_users = [];
      users.forEach((user) => {
        new_users.push({
          id: user.id,
          image: user.image,
          firstname: user.firstname,
          lastname: user.lastname,
          code: user.code,
          teams: user.teams,
          disabled: false,
        });
      });
      return new_users;
    },
    prepareTabKey(key) {
      switch (key) {
        case "in storyboard":
          return "In Story Board";
        case "storyboard review":
          return "In Story Board Review";
        case "design review":
          return "In Design Review";
        case "in design":
          return "In Design";
        case "in programming":
          return "In Programming";
      }
    },
    addSelectedUser() {
      const requiredText = this.$tr("item_is_required", this.$tr("users"));
      if (this.selectedUserId) {
        const user = this.users?.find(
          (user) => user?.id === this.selectedUserId
        );
        if (!user) {
          return;
        }
        if (user.disabled) {
          this.userErrorMessage = this.$tr("this_user_has_no_permission");
          return;
        }
        if (this.selectedUsers?.some((userItem) => userItem?.id === user?.id)) {
          return;
        }
        this.selectedUsers?.unshift(user);
        this.selectedUserId = "";
        this.selectedUsers?.length < 1
          ? (this.userErrorMessage = requiredText)
          : (this.userErrorMessage = "");
      } else {
        this.userErrorMessage = requiredText;
      }
    },
    removeSelectedTeam(teamId) {
      try {
        this.selectedTeams = this.selectedTeams?.filter(
          (user) => user?.id !== teamId
        );
        this.selectedUsers = this.selectedUsers.filter((u) => {
          if (!u.teams.map((u) => u.id).includes(teamId)) {
            return u;
          }
        });
        this.users = this.users.filter((u) => {
          if (!u.teams.map((u) => u.id).includes(teamId)) {
            return u;
          }
        });
        const requiredText = this.$tr("item_is_required", this.$tr("teams"));
        this.selectedTeams?.length < 1
          ? (this.teamErrorMessage = requiredText)
          : (this.teamErrorMessage = "");
      } catch (error) {}
    },

    removeSelectedUser(userId) {
      try {
        this.selectedUsers = this.selectedUsers?.filter(
          (user) => user?.id !== userId
        );
        const requiredText = this.$tr("item_is_required", this.$tr("users"));
        this.selectedUsers?.length < 1
          ? (this.userErrorMessage = requiredText)
          : (this.userErrorMessage = "");
      } catch (error) {}
    },

    async fetchTeams() {
      if (this.teams.length) {
        return;
      }
      try {
        this.teamLoading = true;
        const url = "teams?key=teams-only";
        const response = await this.$axios.get(url);
        this.teams = response?.data?.data;
        this.teamLoading = false;
      } catch (error) {
        this.teamLoading = false;
      }
      this.usersLoaded = true;
    },

    isValidForm(item, selectedItem, inputErrMessage) {
      const inputItemRequiredText = this.$tr(
        "item_is_required",
        this.$tr(item)
      );
      this[selectedItem]?.length < 1
        ? (this[inputErrMessage] = inputItemRequiredText)
        : (this[inputErrMessage] = "");
      return inputErrMessage == "";
    },

    resetForm() {
      this.$refs.teamRoleForm.resetValidation();
      this.userErrorMessage = "";
      this.selectedUsers = [];
      this.selectedUserId = "";
    },

    filterUsers(user_ids) {
      this.users = this.users.filter((user) => {
        if (!this.isInArray(user_ids, user.id)) {
          return user;
        }
      });
    },

    isInArray(arr, id) {
      let res = arr.find((item) => item === id);
      return res !== undefined;
    },

    async onSubmit() {
      if (this.selectedUsers.length) {
        this.isSubmitting = true;
        let user_ids = this.selectedUsers.map((user) => user.id);
        try {
          const response = await this.$axios.post(
            "design-request/assign-user",
            {
              user_ids: user_ids,
              design_request_ids: this.selected.map((u) => u.id),
            }
          );
          if (response?.status === 201) {
            this.filterUsers(user_ids);
            this.$refs.asignStepper.forceNext();
            this.resetForm();
            // this.$emit("fetchNewData");
            this.isSubmitting = false;
            return true;
          } else {
            this.isSubmitting = false;
            return false;
          }
        } catch (error) {
          this.isSubmitting = false;
          if (error.response?.status === 404) {
            // this.$toastr.e(this.$tr("design_request_order_not_found"));
				    this.$toasterNA("red", this.$tr("design_request_order_not_found"));
          } else {
				    this.$toasterNA("red", this.$tr("something_went_wrong"));
            // this.$toasterNA("red", this.$tr('something_went_wrong'));
          }
        }
        this.isSubmitting = false;
      } else {
        if (this.selectedTeams.length == 0 && this.selectedUsers.length == 0) {
          this.teamErrorMessage = this.$tr(
            "item_is_required",
            this.$tr("teams")
          );
          this.userErrorMessage = this.$tr(
            "item_is_required",
            this.$tr("users")
          );
        } else if (this.selectedTeams.length == 0) {
          this.teamErrorMessage = this.$tr(
            "item_is_required",
            this.$tr("teams")
          );
        } else if (this.selectedUsers.length == 0) {
          this.userErrorMessage = this.$tr(
            "item_is_required",
            this.$tr("users")
          );
        }
      }
    },
  },
};
</script>
